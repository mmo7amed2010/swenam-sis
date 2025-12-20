<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAnnouncementNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected Announcement $announcement;
    protected string $targetAudience;
    protected ?int $programId;

    public function __construct(Announcement $announcement, string $targetAudience, ?int $programId = null)
    {
        $this->announcement = $announcement;
        $this->targetAudience = $targetAudience;
        $this->programId = $programId;
    }

    public function handle(): void
    {
        Log::info('Starting announcement notification job', [
            'announcement_id' => $this->announcement->id,
            'target_audience' => $this->targetAudience,
            'program_id' => $this->programId,
            'has_course_id' => !is_null($this->announcement->course_id),
            'send_email' => $this->announcement->send_email,
        ]);

        // Get users based on target audience
        $query = User::query();

        switch ($this->targetAudience) {
            case 'all':
                // All users - no filter needed
                break;
                
            case 'students':
                $query->where('user_type', 'student');
                break;
                
            case 'instructors':
                $query->where('user_type', 'instructor');
                break;
                
            case 'admins':
                $query->where('user_type', 'admin');
                break;
                
            case 'program':
                if ($this->programId) {
                    // All students in the program have access to all courses in that program
                    // So we just filter by program_id
                    $query->where('user_type', 'student')
                        ->where('program_id', $this->programId);
                } else {
                    // No program specified, no users
                    $query->whereRaw('1 = 0');
                }
                break;
                
            default:
                // Unknown audience type, no users for safety
                $query->whereRaw('1 = 0');
                break;
        }

        $totalUsers = $query->count();
        
        Log::info('Total users to notify', [
            'announcement_id' => $this->announcement->id,
            'total_users' => $totalUsers,
        ]);

        // Process in chunks of 500 users
        $chunkSize = 500;
        $totalChunks = ceil($totalUsers / $chunkSize);
        
        $query->chunk($chunkSize, function ($users) {
            // ALWAYS dispatch in-app notification job
            SendNotificationChunkJob::dispatch($this->announcement, $users);
            
            // ONLY dispatch email job if send_email is true
            if ($this->announcement->send_email) {
                SendEmailChunkJob::dispatch($this->announcement, $users);
            }
        });

        Log::info('Announcement notification chunks dispatched', [
            'announcement_id' => $this->announcement->id,
            'total_users' => $totalUsers,
            'chunks' => $totalChunks,
            'send_email' => $this->announcement->send_email,
            'notification_jobs_dispatched' => $totalChunks,
            'email_jobs_dispatched' => $this->announcement->send_email ? $totalChunks : 0,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Announcement notification job failed', [
            'announcement_id' => $this->announcement->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
