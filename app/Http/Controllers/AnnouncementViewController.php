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
                            // Students in a program see announcements for that program
                            $q->where('program_id', $user->program_id);
                        }
                    });
            })
            ->latest();
        
        $systemAnnouncements = $systemAnnouncementsQuery->paginate(10);

        return view('pages.announcements.index', compact('systemAnnouncements'));
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement): View
    {
        // Check if announcement is accessible
        abort_unless($announcement->is_published, 404);

        return view('pages.announcements.show', compact('announcement'));
    }
}
