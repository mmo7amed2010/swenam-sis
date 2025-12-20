<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Course $course
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

        if ($notifiable->wantsNotificationEmail('system_notifications')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You\'ve Been Enrolled in ' . $this->course->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been enrolled in the following course:')
            ->line('**Course:** ' . $this->course->name)
            ->line('**Code:** ' . $this->course->course_code)
            ->line('**Program:** ' . ($this->course->program->name ?? 'N/A'))
            ->action('Access Course', route('student.courses.show', [$this->course->program_id, $this->course->id]))
            ->line('You can now access course materials, assignments, and quizzes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'course_code' => $this->course->course_code,
            'program_id' => $this->course->program_id,
            'program_name' => $this->course->program->name ?? 'N/A',
            'title' => 'Enrolled in ' . $this->course->name,
            'message' => 'You have been enrolled in ' . $this->course->name . '. You can now access the course content.',
            'action_url' => route('student.courses.show', [$this->course->program_id, $this->course->id]),
            'icon' => 'book',
            'priority' => 'high',
        ];
    }
}
