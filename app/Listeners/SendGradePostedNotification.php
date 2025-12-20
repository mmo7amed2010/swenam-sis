<?php

namespace App\Listeners;

use App\Notifications\GradePostedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendGradePostedNotification implements ShouldQueue
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
        // Get the grade from the event
        $grade = $event->grade;

        // Get the student who received the grade
        $student = $grade->student;

        if ($student) {
            // Send notification to the student
            $student->notify(new GradePostedNotification($grade));
        }
    }
}
