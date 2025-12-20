<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementEmailNotification extends Notification implements ShouldQueue
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
        // This notification ONLY sends emails
        return ['mail'];
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
        return route('announcements.show', $this->announcement->id, true);
    }
}
