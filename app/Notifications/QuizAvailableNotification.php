<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Quiz $quiz,
        public string $action = 'published' // 'published' or 'reminder'
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

        if ($notifiable->wantsNotificationEmail('quiz_notifications')) {
            $channels[] = 'mail';
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

        if ($this->action === 'published') {
            $message->line('A new quiz is now available:');
        } else {
            $message->line('This is a reminder about an upcoming quiz:');
        }

        $message->line('**Quiz:** ' . $this->quiz->title)
            ->line('**Course:** ' . $this->quiz->course->name);

        if ($this->quiz->available_from) {
            $message->line('**Available From:** ' . $this->quiz->available_from->format('F d, Y \a\t h:i A'));
        }

        if ($this->quiz->available_until) {
            $message->line('**Available Until:** ' . $this->quiz->available_until->format('F d, Y \a\t h:i A'));
        }

        if ($this->quiz->time_limit) {
            $message->line('**Time Limit:** ' . $this->quiz->time_limit . ' minutes');
        }

        $message->action('Take Quiz', $this->getActionUrl())
            ->line('Good luck!');

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
            'quiz_id' => $this->quiz->id,
            'quiz_title' => $this->quiz->title,
            'course_id' => $this->quiz->course_id,
            'course_name' => $this->quiz->course->name,
            'available_from' => $this->quiz->available_from?->toISOString(),
            'available_until' => $this->quiz->available_until?->toISOString(),
            'time_limit' => $this->quiz->time_limit,
            'action' => $this->action,
            'title' => $this->action === 'published' ? 'New Quiz Available: ' . $this->quiz->title : 'Quiz Reminder: ' . $this->quiz->title,
            'message' => $this->getMessage(),
            'action_url' => $this->getActionUrl(),
            'icon' => 'question-2',
            'priority' => 'medium',
        ];
    }

    /**
     * Get notification message.
     */
    private function getMessage(): string
    {
        if ($this->action === 'published') {
            return 'A new quiz "' . $this->quiz->title . '" is now available in ' . $this->quiz->course->name;
        }

        if ($this->quiz->available_until) {
            return 'Quiz "' . $this->quiz->title . '" is available until ' . $this->quiz->available_until->format('M d, Y');
        }

        return 'Quiz "' . $this->quiz->title . '" is available';
    }

    /**
     * Get subject for email.
     */
    private function getSubject(): string
    {
        if ($this->action === 'published') {
            return 'New Quiz Available: ' . $this->quiz->title;
        }

        return 'Quiz Reminder: ' . $this->quiz->title;
    }

    /**
     * Get action URL for the quiz.
     */
    private function getActionUrl(): string
    {
        $courseId = $this->quiz->course->id ?? $this->quiz->course_id;

        return route('student.courses.quizzes.view', ['courseId' => $courseId, 'quizId' => $this->quiz->id]);
    }
}
