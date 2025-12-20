<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementViewController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Get system-wide announcements with proper filtering
        $systemAnnouncementsQuery = Announcement::systemWide()
            ->published()
            ->where(function ($query) use ($user) {
                // All users announcements
                $query->where('target_audience', 'all')
                    // Or announcements for specific user type
                    ->orWhere('target_audience', $user->user_type . 's')
                    // Or program-specific announcements
                    ->orWhere(function ($q) use ($user) {
                        $q->where('target_audience', 'program');
                        
                        if ($user->user_type === 'student' && $user->program_id) {
                            // Students in a program see all announcements for that program
                            // (both program-wide and course-specific within that program)
                            $q->where('program_id', $user->program_id);
                        }
                    });
            })
            ->latest();
        
        $systemAnnouncements = $systemAnnouncementsQuery->paginate(10);

        // Get course announcements if user is a student
        $courseAnnouncements = collect();
        if ($user->user_type === 'student' && $user->program_id) {
            $courseIds = $user->programCourses()->pluck('courses.id');
            $courseAnnouncements = Announcement::whereIn('course_id', $courseIds)
                ->published()
                ->with('course')
                ->latest()
                ->paginate(10);
        }

        return view('pages.announcements.index', compact('systemAnnouncements', 'courseAnnouncements'));
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement): View
    {
        // Check if announcement is accessible
        abort_unless($announcement->is_published, 404);

        // For course announcements, verify user has access
        if ($announcement->type === 'course' && $announcement->course_id) {
            $user = auth()->user();
            $hasAccess = $user->programCourses()->where('courses.id', $announcement->course_id)->exists()
                || $user->instructorCourses()->where('courses.id', $announcement->course_id)->exists()
                || $user->hasRole('admin');

            abort_unless($hasAccess, 403);
        }

        return view('pages.announcements.show', compact('announcement'));
    }
}
