<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Notifications\AnnouncementDatabaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class SendNotificationChunkJob implements ShouldQueue
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
        
        Log::info('Processing in-app notification chunk', [
            'announcement_id' => $this->announcement->id,
            'chunk_id' => $chunkId,
            'user_count' => $this->users->count(),
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($this->users as $user) {
            try {
                // Rate limit: 100 notifications per second (database is faster than email)
                RateLimiter::attempt(
                    'send-database-notifications',
                    100,
                    function () use ($user) {
                        Notification::send($user, new AnnouncementDatabaseNotification($this->announcement));
                    },
                    1
                );
                
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to send in-app notification to user', [
                    'announcement_id' => $this->announcement->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Small delay
            usleep(10000); // 10ms delay = 100 notifications/second max
        }

        Log::info('In-app notification chunk processed', [
            'announcement_id' => $this->announcement->id,
            'chunk_id' => $chunkId,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('In-app notification chunk job failed', [
            'announcement_id' => $this->announcement->id,
            'user_count' => $this->users->count(),
            'error' => $exception->getMessage(),
        ]);
    }
}
