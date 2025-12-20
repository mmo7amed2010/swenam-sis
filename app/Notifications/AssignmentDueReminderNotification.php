<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentDueReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Assignment $assignment
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->wantsNotificationEmail('assignment_reminders')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $hoursRemaining = now()->diffInHours($this->assignment->due_date);

        return (new MailMessage)
            ->subject('Reminder: ' . $this->assignment->title . ' Due Soon')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a friendly reminder that the following assignment is due soon:')
            ->line('**Assignment:** ' . $this->assignment->title)
            ->line('**Course:** ' . $this->assignment->course->name)
            ->line('**Due Date:** ' . $this->assignment->due_date->format('F d, Y \a\t h:i A'))
            ->line('**Time Remaining:** ' . $hoursRemaining . ' hours')
            ->action('View Assignment', $this->getActionUrl())
            ->line('Don\'t forget to submit your work before the deadline!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_id' => $this->assignment->course_id,
            'course_name' => $this->assignment->course->name,
            'due_date' => $this->assignment->due_date->toISOString(),
            'due_date_formatted' => $this->assignment->due_date->format('M d, Y h:i A'),
            'title' => 'Assignment Due Soon: ' . $this->assignment->title,
            'message' => $this->assignment->title . ' is due on ' . $this->assignment->due_date->format('M d, Y'),
            'action_url' => $this->getActionUrl(),
            'icon' => 'time',
            'priority' => 'medium',
        ];
    }

    /**
     * Get action URL for the assignment.
     */
    private function getActionUrl(): string
    {
        $programId = $this->assignment->course->program_id ?? null;
        $courseId = $this->assignment->course_id;

        if (!$programId) {
            return route('dashboard');
        }

        return route('student.courses.assignments.show', [$programId, $courseId, $this->assignment->id]);
    }
}
