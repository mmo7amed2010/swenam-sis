<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePostedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Grade $grade
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

        if ($notifiable->wantsNotificationEmail('grade_notifications')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $gradeable = $this->grade->gradeable;
        $itemName = $gradeable->title ?? $gradeable->name ?? 'Item';
        $itemType = class_basename($this->grade->gradeable_type);

        return (new MailMessage)
            ->subject('Grade Posted: ' . $itemName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new grade has been posted for:')
            ->line('**' . $itemType . ':** ' . $itemName)
            ->line('**Course:** ' . ($gradeable->course->name ?? 'N/A'))
            ->line('**Score:** ' . $this->grade->score . '/' . $this->grade->max_score)
            ->action('View Gradebook', $this->getActionUrl())
            ->line('Keep up the great work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $gradeable = $this->grade->gradeable;
        $itemName = $gradeable->title ?? $gradeable->name ?? 'Item';
        $itemType = class_basename($this->grade->gradeable_type);

        return [
            'grade_id' => $this->grade->id,
            'gradeable_type' => $this->grade->gradeable_type,
            'gradeable_id' => $this->grade->gradeable_id,
            'item_name' => $itemName,
            'item_type' => $itemType,
            'score' => $this->grade->score,
            'max_score' => $this->grade->max_score,
            'course_id' => $gradeable->course_id ?? null,
            'course_name' => $gradeable->course->name ?? 'N/A',
            'title' => 'Grade Posted: ' . $itemName,
            'message' => 'You received ' . $this->grade->score . '/' . $this->grade->max_score . ' on ' . $itemName,
            'action_url' => $this->getActionUrl(),
            'icon' => 'chart-line-up',
            'priority' => 'medium',
        ];
    }

    /**
     * Get action URL based on gradeable type.
     */
    private function getActionUrl(): string
    {
        $gradeable = $this->grade->gradeable;

        if (!$gradeable || !isset($gradeable->course_id)) {
            return route('dashboard');
        }

        $programId = $gradeable->course->program_id ?? null;
        $courseId = $gradeable->course_id;

        if (!$programId) {
            return route('dashboard');
        }

        // Return to course grades page
        return route('student.courses.show', [$programId, $courseId]) . '#grades';
    }
}
