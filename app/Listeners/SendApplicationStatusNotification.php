<?php

namespace App\Listeners;

use App\Notifications\ApplicationStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendApplicationStatusNotification implements ShouldQueue
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
        // Get the application and user from the event
        $application = $event->application;
        $status = $event->status;
        $message = $event->message ?? null;

        // Find the user by email (applications are created before user accounts)
        $user = \App\Models\User::where('email', $application->email)->first();

        if ($user) {
            // Send notification to the user
            $user->notify(new ApplicationStatusNotification($application, $status, $message));
        }
    }
}
