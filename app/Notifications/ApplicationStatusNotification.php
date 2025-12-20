<?php

namespace App\Notifications;

use App\Models\StudentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public StudentApplication $application,
        public string $status,
        public ?string $message = null
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

        // Check if user wants email for application updates
        if ($notifiable->wantsNotificationEmail('application_updates')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->getMainMessage());

        if ($this->message) {
            $mailMessage->line($this->message);
        }

        $mailMessage->action('View Application', route('application.status.check', ['reference' => $this->application->reference_number]));

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'reference_number' => $this->application->reference_number,
            'status' => $this->status,
            'program' => $this->application->program->name ?? 'N/A',
            'message' => $this->message,
            'title' => $this->getTitle(),
            'action_url' => route('application.status.check', ['reference' => $this->application->reference_number]),
            'icon' => $this->getIcon(),
            'priority' => $this->getPriority(),
        ];
    }

    /**
     * Get notification title.
     */
    private function getTitle(): string
    {
        return match ($this->status) {
            'approved' => 'Application Approved!',
            'rejected' => 'Application Status Update',
            'pending' => 'Application Received',
            'more_info_needed' => 'Action Required on Application',
            default => 'Application Status Update',
        };
    }

    /**
     * Get email subject.
     */
    private function getSubject(): string
    {
        return match ($this->status) {
            'approved' => 'Congratulations! Your Application Has Been Approved',
            'rejected' => 'Application Status Update - ' . $this->application->reference_number,
            'pending' => 'Application Received - ' . $this->application->reference_number,
            'more_info_needed' => 'Action Required - ' . $this->application->reference_number,
            default => 'Application Status Update',
        };
    }

    /**
     * Get main message for email.
     */
    private function getMainMessage(): string
    {
        return match ($this->status) {
            'approved' => 'We are delighted to inform you that your application to ' . config('app.name') . ' has been approved! Welcome to the ' . ($this->application->program->name ?? 'program') . '.',
            'rejected' => 'Thank you for your interest in ' . config('app.name') . '. After careful review, we regret to inform you that we are unable to offer you admission at this time.',
            'pending' => 'We have received your application and it is currently under review. We will notify you once a decision has been made.',
            'more_info_needed' => 'We need additional information to process your application. Please review the details and provide the requested information.',
            default => 'Your application status has been updated.',
        };
    }

    /**
     * Get icon for notification.
     */
    private function getIcon(): string
    {
        return match ($this->status) {
            'approved' => 'check-circle',
            'rejected' => 'cross-circle',
            'pending' => 'time',
            'more_info_needed' => 'information',
            default => 'notification',
        };
    }

    /**
     * Get priority level.
     */
    private function getPriority(): string
    {
        return match ($this->status) {
            'approved', 'more_info_needed' => 'high',
            'rejected' => 'medium',
            default => 'low',
        };
    }
}
