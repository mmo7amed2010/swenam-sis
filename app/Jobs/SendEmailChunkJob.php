<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Notifications\AnnouncementEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class SendEmailChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s

    protected Announcement $announcement;
    protected Collection $users;

    public function __construct(Announcement $announcement, Collection $users)
    {
        $this->announcement = $announcement;
        $this->users = $users;
    }

    public function handle(): void
    {
        $chunkId = substr(md5($this->users->pluck('id')->implode(',')), 0, 8);
        
        Log::info('Processing email chunk', [
            'announcement_id' => $this->announcement->id,
            'chunk_id' => $chunkId,
            'user_count' => $this->users->count(),
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($this->users as $user) {
            try {
                // Check if user wants email notifications
                $preferenceType = $this->announcement->type === 'course'
                    ? 'course_announcements'
                    : 'system_notifications';

                if ($user->wantsNotificationEmail($preferenceType)) {
                    // Rate limit: 50 emails per second
                    RateLimiter::attempt(
                        'send-emails',
                        50,
                        function () use ($user) {
                            Notification::send($user, new AnnouncementEmailNotification($this->announcement));
                        },
                        1
                    );
                    
                    $sent++;
                } else {
                    Log::debug('User opted out of email notifications', [
                        'user_id' => $user->id,
                        'announcement_id' => $this->announcement->id,
                    ]);
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to send email to user', [
                    'announcement_id' => $this->announcement->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Small delay to prevent overwhelming the mail server
            usleep(20000); // 20ms delay = 50 emails/second max
        }

        Log::info('Email chunk processed', [
            'announcement_id' => $this->announcement->id,
            'chunk_id' => $chunkId,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Email chunk job failed', [
            'announcement_id' => $this->announcement->id,
            'user_count' => $this->users->count(),
            'error' => $exception->getMessage(),
        ]);
    }
}
