<?php

namespace App\Jobs;

use App\Mail\StudentLmsAccessActivatedMail;
use App\Models\Student;
use App\Models\StudentApplication;
use App\Models\User;
use App\Services\LmsApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Creates LMS account after admin approval.
 * This job assumes the SIS User/Student records already exist (created by CreateImmediateStudentAccountJob).
 * It only handles LMS integration and activating course access.
 */
class CreateLmsStudentAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * Create a new job instance.
     */
    public function __construct(
        public StudentApplication $application
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the already-created user
        $user = User::find($this->application->created_user_id);

        if (! $user) {
            Log::error('Cannot create LMS account - no user found for application', [
                'application_id' => $this->application->id,
                'created_user_id' => $this->application->created_user_id,
            ]);

            return;
        }

        $student = $user->student;

        if (! $student) {
            Log::error('Cannot create LMS account - no student record found', [
                'user_id' => $user->id,
                'application_id' => $this->application->id,
            ]);

            return;
        }

        // Create LMS account
        $this->createLmsAccount($user, $student);

        // Send LMS access activated email
        Mail::to($user->email)->queue(
            new StudentLmsAccessActivatedMail($user, $student, $this->application)
        );

        Log::info('LMS account created for approved student', [
            'user_id' => $user->id,
            'student_number' => $student->student_number,
            'lms_user_id' => $user->fresh()->lms_user_id,
            'application_id' => $this->application->id,
        ]);
    }

    /**
     * Create corresponding account in LMS for course access via API.
     */
    private function createLmsAccount(User $user, Student $student): void
    {
        try {
            $lmsApiService = app(LmsApiService::class);

            $result = $lmsApiService->createStudent([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $student->phone,
                'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                'program_id' => $this->application->program_id,
                'intake_id' => $this->application->intake_id,
                'application_reference' => $this->application->reference_number,
                'sis_application_id' => $this->application->id,
            ]);

            if ($result['success']) {
                // Store the LMS user ID for SSO
                $user->update([
                    'lms_user_id' => $result['user_id'],
                ]);

                Log::info('LMS account created for student', [
                    'sis_user_id' => $user->id,
                    'lms_user_id' => $result['user_id'],
                    'student_number' => $result['student_number'] ?? null,
                ]);
            } else {
                Log::error('Failed to create LMS account', [
                    'sis_user_id' => $user->id,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            // Log but don't fail - student can still use SIS
            Log::error('LMS API error during account creation', [
                'sis_user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
