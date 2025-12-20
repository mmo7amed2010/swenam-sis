<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AnnouncementDatabaseNotification extends Notification implements ShouldQueue
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
        // This notification ONLY sends database notifications
        return ['database'];
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
     * Get action URL.
     */
    private function getActionUrl(): string
    {
        return route('announcements.show', $this->announcement->id, true);
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
