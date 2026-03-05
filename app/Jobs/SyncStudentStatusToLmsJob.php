<?php

namespace App\Jobs;

use App\Models\LmsSyncFailure;
use App\Models\User;
use App\Services\LmsAdmissionTypeResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncStudentStatusToLmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public function __construct(
        public User $user,
        public string $action
    ) {}

    public function handle(): void
    {
        // Re-read latest state to handle rapid toggling
        $this->user->refresh();
        if (! $this->user->lms_user_id) {
            Log::warning('SyncStudentStatusToLmsJob: user has no LMS account', [
                'user_id' => $this->user->id,
                'action' => $this->action,
            ]);
            return;
        }

        $admissionType = LmsAdmissionTypeResolver::resolve($this->user);

        $payload = [
            'admission_type' => $admissionType,
            'is_suspended' => $this->user->is_suspended,
        ];

        // Call LMS API directly (bypass LmsApiService's exception swallowing so retries work)
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-Key' => config('lms.api_key'),
        ])->put(config('lms.api_url') . '/api/v1/students/' . $this->user->lms_user_id, $payload);

        if ($response->failed()) {
            $error = $response->json('error', 'HTTP ' . $response->status());

            Log::error('SyncStudentStatusToLmsJob: LMS API call failed', [
                'user_id' => $this->user->id,
                'lms_user_id' => $this->user->lms_user_id,
                'action' => $this->action,
                'payload' => $payload,
                'error' => $error,
                'attempt' => $this->attempts(),
            ]);

            throw new \RuntimeException('LMS sync failed: ' . $error);
        }

        Log::info('SyncStudentStatusToLmsJob: successfully synced to LMS', [
            'user_id' => $this->user->id,
            'lms_user_id' => $this->user->lms_user_id,
            'action' => $this->action,
            'payload' => $payload,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncStudentStatusToLmsJob: all retries exhausted', [
            'user_id' => $this->user->id,
            'lms_user_id' => $this->user->lms_user_id,
            'action' => $this->action,
            'error' => $exception->getMessage(),
        ]);

        try {
            LmsSyncFailure::create([
                'user_id' => $this->user->id,
                'lms_user_id' => $this->user->lms_user_id,
                'action' => $this->action,
                'payload' => [
                    'admission_type' => LmsAdmissionTypeResolver::resolve($this->user),
                    'is_suspended' => $this->user->is_suspended,
                ],
                'error' => $exception->getMessage(),
                'attempts' => $this->attempts() ?? $this->tries,
            ]);
        } catch (\Throwable $e) {
            Log::critical('SyncStudentStatusToLmsJob: failed to record sync failure', [
                'user_id' => $this->user->id,
                'original_error' => $exception->getMessage(),
                'recording_error' => $e->getMessage(),
            ]);
        }
    }
}
