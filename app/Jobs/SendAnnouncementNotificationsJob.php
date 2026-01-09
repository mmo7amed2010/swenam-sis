<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementEmailNotification;
use App\Notifications\AnnouncementNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendAnnouncementNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $announcementId;
    protected $targetAudience;
    protected $programId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $announcementId, string $targetAudience, ?int $programId = null)
    {
        $this->announcementId = $announcementId;
        $this->targetAudience = $targetAudience;
        $this->programId = $programId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $announcement = Announcement::find($this->announcementId);

        if (!$announcement) {
            Log::warning('Announcement not found', ['id' => $this->announcementId]);
            return;
        }

        Log::info('Processing announcement notifications', [
            'announcement_id' => $announcement->id,
            'target_audience' => $this->targetAudience,
            'program_id' => $this->programId,
            'send_email' => $announcement->send_email,
        ]);

        // Get target users based on audience
        $users = $this->getTargetUsers($announcement);

        Log::info('Target users found', [
            'announcement_id' => $announcement->id,
            'user_count' => $users->count(),
        ]);

        if ($users->isEmpty()) {
            Log::warning('No users found for announcement', [
                'announcement_id' => $announcement->id,
                'target_audience' => $this->targetAudience,
            ]);
            return;
        }

        // Send notifications in chunks to avoid memory issues
        $users->chunk(100)->each(function ($chunk) use ($announcement) {
            // Always send database notification
            Notification::send($chunk, new AnnouncementNotification($announcement));

            // Send email notification if enabled
            if ($announcement->send_email) {
                Notification::send($chunk, new AnnouncementEmailNotification($announcement));
            }
        });

        Log::info('Announcement notifications sent successfully', [
            'announcement_id' => $announcement->id,
            'user_count' => $users->count(),
            'email_sent' => $announcement->send_email,
        ]);
    }

    /**
     * Get target users based on announcement audience.
     */
    protected function getTargetUsers(Announcement $announcement)
    {
        $query = User::query();

        switch ($this->targetAudience) {
            case 'students':
                $query->where('user_type', 'student');
                break;
            case 'admins':
                $query->where('user_type', 'admin');
                break;
            case 'program':
                if ($this->programId) {
                    // Target students in specific program
                    $query->where('user_type', 'student')
                        ->where('program_id', $this->programId);
                }
                break;
            case 'all':
            default:
                // No filter - all users
                break;
        }

        return $query->get();
    }
}
