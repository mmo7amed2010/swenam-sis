<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationSettingController extends Controller
{
    /**
     * Show the notification settings form.
     */
    public function edit(): View
    {
        $settings = NotificationSetting::getOrCreateForUser(auth()->id());

        return view('pages\notifications.settings', compact('settings'));
    }

    /**
     * Update the notification settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $settings = NotificationSetting::getOrCreateForUser(auth()->id());

        $settings->update([
            'course_announcements_email' => $request->boolean('course_announcements_email'),
            'system_notifications_email' => $request->boolean('system_notifications_email'),
            'assignment_reminders_email' => $request->boolean('assignment_reminders_email'),
            'grade_notifications_email' => $request->boolean('grade_notifications_email'),
            'application_updates_email' => $request->boolean('application_updates_email'),
            'quiz_notifications_email' => $request->boolean('quiz_notifications_email'),
        ]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
