<?php

namespace App\Jobs;

use App\Mail\DuplicateEmailNotificationMail;
use App\Mail\StudentWelcomeMail;
use App\Models\AccountCreationLog;
use App\Models\Student;
use App\Models\StudentApplication;
use App\Models\User;
use App\Services\LmsApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateStudentAccountJob implements ShouldQueue
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
        DB::beginTransaction();

        try {
            // Check for duplicate email
            if (User::where('email', $this->application->email)->exists()) {
                $this->handleDuplicateEmail();
                DB::commit();

                return;
            }

            // Generate temporary password
            $tempPassword = $this->generateTempPassword();

            // Create user account
            $user = User::create([
                'name' => $this->application->first_name.' '.$this->application->last_name,
                'first_name' => $this->application->first_name,
                'last_name' => $this->application->last_name,
                'email' => $this->application->email,
                'password' => Hash::make($tempPassword),
                'user_type' => 'student',
                'program_id' => $this->application->program_id, // Direct link to program
                'email_verified_at' => now(),
                'password_change_required' => true, // Use existing field name
            ]);

            // Generate student number
            $studentNumber = Student::generateStudentNumber();

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $studentNumber,
                'first_name' => $this->application->first_name,
                'last_name' => $this->application->last_name,
                'email' => $this->application->email,
                'phone' => $this->application->phone,
                'date_of_birth' => $this->application->date_of_birth,
                'address' => $this->application->address,
                'enrollment_status' => 'active',
            ]);

            // Note: Program is already linked via User.program_id (set during user creation above)
            // The StudentProgram pivot table is deprecated - students are now directly assigned to programs

            // Update application with created_user_id
            $this->application->update([
                'created_user_id' => $user->id,
            ]);

            // Log success
            AccountCreationLog::create([
                'user_id' => $user->id,
                'student_id' => $student->id,
                'application_id' => $this->application->id,
                'created_by' => $this->application->reviewed_by ?? auth()->id(),
                'student_number' => $studentNumber,
                'status' => 'success',
            ]);

            DB::commit();

            // Create corresponding account in LMS for course access
            $this->createLmsAccount($user, $student, $tempPassword);

            // Send welcome email with credentials (queued)
            Mail::to($user->email)->queue(new StudentWelcomeMail($user, $student, $tempPassword, $this->application));

            Log::info('Student account created successfully', [
                'user_id' => $user->id,
                'student_number' => $studentNumber,
                'application_id' => $this->application->id,
                'lms_user_id' => $user->lms_user_id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log failure
            AccountCreationLog::create([
                'application_id' => $this->application->id,
                'created_by' => $this->application->reviewed_by ?? auth()->id(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Student account creation failed', [
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Generate a secure temporary password (12 chars with uppercase, lowercase, numbers, symbols).
     */
    private function generateTempPassword(): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghjkmnpqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%^&*';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Add 4 more random chars
        $allChars = $uppercase.$lowercase.$numbers.$symbols;
        for ($i = 0; $i < 4; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Create corresponding account in LMS for course access via API.
     */
    private function createLmsAccount(User $user, Student $student, string $tempPassword): void
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
                'temp_password' => $tempPassword,
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
            // Log but don't fail - SIS account was already created successfully
            Log::error('LMS API error during account creation', [
                'sis_user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle duplicate email scenario.
     */
    private function handleDuplicateEmail(): void
    {
        $existingUser = User::where('email', $this->application->email)->first();

        // Notify admin (queued)
        $adminEmail = config('mail.admin_email', config('mail.from.address'));
        if ($adminEmail) {
            Mail::to($adminEmail)->queue(new DuplicateEmailNotificationMail($this->application, $existingUser));
        }

        // Update application with note
        $this->application->update([
            'status' => 'pending_review',
            'admin_notes' => ($this->application->admin_notes ?? '')."\n\nDuplicate email detected. Existing user ID: {$existingUser->id}",
        ]);

        Log::warning('Duplicate email detected during account creation', [
            'application_id' => $this->application->id,
            'email' => $this->application->email,
            'existing_user_id' => $existingUser->id,
        ]);

        // Log failure
        AccountCreationLog::create([
            'application_id' => $this->application->id,
            'created_by' => $this->application->reviewed_by ?? auth()->id(),
            'status' => 'failed',
            'error_message' => "Duplicate email: User ID {$existingUser->id} already exists with this email.",
        ]);

        // Don't retry - this is expected behavior
        $this->delete();
    }
}
