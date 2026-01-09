<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
            ->paginate(6);

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
            'target_audience' => 'required|in:all,students,admins,program',
            'program_id' => 'nullable|required_if:target_audience,program|exists:programs,id',
        ]);

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => 'system',
            'priority' => $validated['priority'],
            'target_audience' => $validated['target_audience'],
            'program_id' => $validated['program_id'] ?? null,
            'send_email' => $request->boolean('send_email'),
            'is_published' => $request->boolean('is_published', true),
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
            \App\Jobs\SendAnnouncementNotificationsJob::dispatch(
                $announcement->id,
                $validated['target_audience'],
                $validated['program_id'] ?? null
            );
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
            'target_audience' => 'required|in:all,students,admins,program',
            'program_id' => 'nullable|required_if:target_audience,program|exists:programs,id',
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'send_email' => $request->boolean('send_email'),
            'is_published' => $request->boolean('is_published', true),
            'target_audience' => $validated['target_audience'],
            'program_id' => $validated['program_id'] ?? null,
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
}
