<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Announcement $announcement
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

        // Check if email should be sent based on announcement type and user preferences
        if ($this->announcement->send_email) {
            $preferenceType = $this->announcement->type === 'course'
                ? 'course_announcements'
                : 'system_notifications';

            if ($notifiable->wantsNotificationEmail($preferenceType)) {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Hello ' . $notifiable->name . ',');

        if ($this->announcement->type === 'course') {
            $message->line('A new announcement has been posted in ' . $this->announcement->course->name . ':');
        } else {
            $message->line('A new system announcement:');
        }

        $message->line('## ' . $this->announcement->title)
            ->line($this->getTruncatedContent())
            ->action('View Announcement', $this->getActionUrl());

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'content' => $this->getTruncatedContent(),
            'full_content' => $this->announcement->content,
            'type' => $this->announcement->type,
            'priority' => $this->announcement->priority,
            'course_id' => $this->announcement->course_id,
            'course_name' => $this->announcement->course->name ?? null,
            'creator_name' => $this->announcement->creator->name ?? 'System',
            'action_url' => $this->getActionUrl(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Get truncated content for preview.
     */
    private function getTruncatedContent(): string
    {
        $content = strip_tags($this->announcement->content);
        return strlen($content) > 200
            ? substr($content, 0, 200) . '...'
            : $content;
    }

    /**
     * Get email subject.
     */
    private function getSubject(): string
    {
        if ($this->announcement->type === 'course') {
            $courseCode = $this->announcement->course->course_code ?? '';
            return '[' . $courseCode . '] ' . $this->announcement->title;
        }

        return '[' . config('app.name') . '] ' . $this->announcement->title;
    }

    /**
     * Get action URL.
     */
    private function getActionUrl(): string
    {
        // Always link to the announcement show page
        return route('announcements.show', $this->announcement->id);
    }

    /**
     * Get icon based on priority.
     */
    private function getIcon(): string
    {
        return match ($this->announcement->priority) {
            'high' => 'notification-bing',
            'medium' => 'notification',
            'low' => 'information',
            default => 'notification',
        };
    }
}
