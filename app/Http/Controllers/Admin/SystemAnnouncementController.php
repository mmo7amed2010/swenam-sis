<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Notification;

class SystemAnnouncementController extends Controller
{
    /**
     * Display a listing of system announcements.
     */
    public function index(): View
    {
        $announcements = Announcement::systemWide()
            ->with('creator')
            ->latest()
            ->paginate(5);

        return view('pages.admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new system announcement.
     */
    public function create(): View
    {
        return view('pages.admin.announcements.create');
    }

    /**
     * Store a newly created system announcement.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'send_email' => 'boolean',
            'target_audience' => 'required|in:all,students,instructors,admins,program',
            'program_id' => 'nullable|required_if:target_audience,program|exists:programs,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'course_id' => $validated['course_id'] ?? null,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => 'system',
            'priority' => $validated['priority'],
            'target_audience' => $validated['target_audience'],
            'program_id' => $validated['program_id'] ?? null,
            'send_email' => $request->boolean('send_email'),
            'is_published' => $request->boolean('is_published', true),
        ]);



        // Debug logging
        \Log::info('Announcement created', [
            'id' => $announcement->id,
            'send_email' => $announcement->send_email,
            'is_published' => $announcement->is_published,
            'target_audience' => $validated['target_audience'],
        ]);

        // Send notifications if announcement is published
        if ($announcement->is_published) {
            \Log::info('Dispatching notification job', [
                'announcement_id' => $announcement->id,
                'program_id' => $validated['program_id'] ?? 'NULL',
                'target_audience' => $validated['target_audience'],
                'send_email' => $announcement->send_email
            ]);
            
            // Dispatch background job for chunked processing
            // The job will send in-app notifications always, and emails only if send_email is true
            \App\Jobs\SendAnnouncementNotificationsJob::dispatch(
                $announcement,
                $validated['target_audience'],
                $validated['program_id'] ?? null
            );
        } else {
            \Log::info('Skipping notifications for announcement: ' . $announcement->id, [
                'reason' => 'Not published',
                'target_audience' => $validated['target_audience'],
                'is_published' => $announcement->is_published,
            ]);
        }

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'System announcement created successfully.',
                'announcement' => $announcement
            ]);
        }

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'System announcement created successfully.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement): View
    {
        abort_unless($announcement->type === 'system', 404);

        return view('pages.admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     * Returns JSON for AJAX/modal requests.
     */
    public function edit(Request $request, Announcement $announcement)
    {
        abort_unless($announcement->type === 'system', 404);

        return response()->json([
            'id' => $announcement->id,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'priority' => $announcement->priority,
            'target_audience' => $announcement->target_audience,
            'program_id' => $announcement->program_id,
            'course_id' => $announcement->course_id,
            'is_published' => $announcement->is_published,
            'send_email' => $announcement->send_email,
        ]);
    }

    /**
     * Update the specified announcement.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse|JsonResponse
    {
        abort_unless($announcement->type === 'system', 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'send_email' => 'boolean',
            'target_audience' => 'required|in:all,students,instructors,admins,program',
            'program_id' => 'nullable|required_if:target_audience,program|exists:programs,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'send_email' => $request->boolean('send_email'),
            'is_published' => $request->boolean('is_published', true),
            'target_audience' => $validated['target_audience'],
            'program_id' => $validated['program_id'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
        ]);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'System announcement updated successfully.',
                'announcement' => $announcement
            ]);
        }

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'System announcement updated successfully.');
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        abort_unless($announcement->type === 'system', 404);

        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'System announcement deleted successfully.');
    }

    /**
     * Send system announcement notifications to target audience.
     */
    private function sendSystemAnnouncementNotifications(
        Announcement $announcement,
        string $targetAudience,
        ?int $programId = null
    ): void {
        $query = User::query();

        switch ($targetAudience) {
            case 'students':
                $query->where('user_type', 'student');
                break;
            case 'instructors':
                $query->where('user_type', 'instructor');
                break;
            case 'admins':
                $query->where('user_type', 'admin');
                break;
            case 'program':
                if ($programId) {
                    // If course_id is set, filter by course enrollment
                    if ($announcement->course_id) {
                        $query->where('user_type', 'student')
                            ->whereHas('enrollments', function ($q) use ($announcement) {
                                $q->where('course_id', $announcement->course_id)
                                  ->where('status', 'active');
                            });
                    } else {
                        // Otherwise, filter by program
                        $query->where('program_id', $programId);
                    }
                }
                break;
            case 'all':
            default:
                // No filter - all users
                break;
        }

        $users = $query->get();

        \Log::info('Sending announcement notifications', [
            'announcement_id' => $announcement->id,
            'target_audience' => $targetAudience,
            'user_count' => $users->count(),
        ]);

        // Send notification to each user
        Notification::send($users, new AnnouncementNotification($announcement));

        \Log::info('Notifications sent successfully', [
            'announcement_id' => $announcement->id,
            'user_count' => $users->count(),
        ]);
    }

    /**
     * Get courses by program for AJAX requests.
     */
    public function getCoursesByProgram($programId)
    {
        $courses = \App\Models\Course::where('program_id', $programId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($courses);
    }
}
