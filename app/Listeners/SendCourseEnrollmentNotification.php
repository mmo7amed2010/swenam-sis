<?php

namespace App\Listeners;

use App\Notifications\CourseAssignmentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCourseEnrollmentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Get the user and course from the event
        $user = $event->user;
        $course = $event->course;

        // Send notification to the user
        $user->notify(new CourseAssignmentNotification($course));
    }
}
