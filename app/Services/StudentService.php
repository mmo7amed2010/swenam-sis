<?php

namespace App\Services;

use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Jobs\SyncStudentStatusToLmsJob;
use App\Services\LmsAdmissionTypeResolver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Student Service
 *
 * Handles all business logic for students including CRUD, validation,
 * and pagination with filters.
 */
class StudentService
{
    /**
     * Create a new student with user account.
     *
     * @param  array  $data  Student data
     * @return Student Newly created student
     *
     * @throws \Exception
     */
    public function createStudent(array $data): Student
    {
        DB::beginTransaction();
        try {
            // Create user account with program assignment
            $user = User::create([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => 'student',
                'program_id' => $data['program_id'],
                'intake_id' => $data['intake_id'] ?? null,
                'bypass_application' => $data['bypass_application'] ?? false,
            ]);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'student_number' => Student::generateStudentNumber(),
                'enrollment_status' => 'active',
            ]);

            DB::commit();

            // If bypass is enabled, create LMS account immediately
            if ($data['bypass_application'] ?? false) {
                $this->createLmsAccountForBypassStudent($user, $student, $data['program_id'], $data['intake_id'] ?? null);
            }

            // Clear cached counts
            $this->clearCountCache();

            Log::info('Student created', [
                'student_id' => $student->id,
                'user_id' => $user->id,
                'created_by' => auth()->id(),
                'email' => $student->email,
            ]);

            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing student.
     *
     * @param  Student  $student  Student to update
     * @param  array  $data  Update data
     * @return Student Updated student
     */
    public function updateStudent(Student $student, array $data): Student
    {
        $oldProgramId = $student->user?->program_id;
        $newProgramId = $data['program_id'] ?? null;

        // Track bypass status
        $isNowBypassed = $data['bypass_application'] ?? false;

        // Update password only if provided
        $userPassword = null;
        if (! empty($data['password'])) {
            $userPassword = Hash::make($data['password']);
        }

        // Update student record
        $student->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
        ]);

        // Update user record (name, first_name, last_name, email, password, program_id, intake_id, bypass_application)
        if ($student->user) {
            $userData = [
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'program_id' => $newProgramId,
                'intake_id' => $data['intake_id'] ?? null,
                'bypass_application' => $isNowBypassed,
            ];

            if ($userPassword) {
                $userData['password'] = $userPassword;
            }

            $student->user->update($userData);

            // If bypass is enabled and no LMS account exists, create one
            // This handles both newly enabled bypass AND retry if previous creation failed
            // Still creates account if suspended (so lms_user_id is set) but with admission_type = null
            if ($isNowBypassed && ! $student->user->lms_user_id && ! empty($data['intake_id'])) {
                $this->createLmsAccountForBypassStudent($student->user, $student, $newProgramId, $data['intake_id']);
            }

            // If bypass is re-enabled and LMS account already exists, sync to LMS
            if ($isNowBypassed && $student->user->lms_user_id) {
                // Skip LMS restore if student is suspended — suspension takes precedence
                if (! $student->user->isSuspended()) {
                    SyncStudentStatusToLmsJob::dispatch($student->user, 'bypass_enabled');
                }
            }

            // If bypass is removed and student has an LMS account, sync to LMS
            if (! $isNowBypassed && $student->user->lms_user_id) {
                SyncStudentStatusToLmsJob::dispatch($student->user, 'bypass_removed');
            }
        }

        // Log program change if applicable
        if ($oldProgramId !== $newProgramId) {
            Log::warning('Student program changed', [
                'student_id' => $student->id,
                'user_id' => $student->user_id,
                'old_program_id' => $oldProgramId,
                'new_program_id' => $newProgramId,
                'changed_by' => auth()->id(),
            ]);

            // Clear cache since program assignment changed
            $this->clearCountCache();
        }

        Log::info('Student updated', [
            'student_id' => $student->id,
            'updated_by' => auth()->id(),
            'changes' => $student->getChanges(),
        ]);

        return $student->fresh();
    }

    /**
     * Delete a student.
     *
     * @param  Student  $student  Student to delete
     * @return bool Success status
     *
     * @throws \Exception If LMS deletion fails (when student has LMS account)
     */
    public function deleteStudent(Student $student): bool
    {
        $user = $student->user;
        $lmsUserId = $user?->lms_user_id;

        // If student has an LMS account, delete it first
        if ($lmsUserId) {
            $lmsApiService = app(LmsApiService::class);
            $result = $lmsApiService->deleteStudent($lmsUserId);

            if (! $result['success']) {
                Log::error('Failed to delete student in LMS, preventing SIS deletion', [
                    'student_id' => $student->id,
                    'user_id' => $student->user_id,
                    'lms_user_id' => $lmsUserId,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                throw new \Exception('Failed to delete student in LMS: '.($result['error'] ?? 'Unknown error'));
            }

            Log::info('Student deleted in LMS, proceeding with SIS deletion', [
                'student_id' => $student->id,
                'lms_user_id' => $lmsUserId,
            ]);
        }

        // Clear cached counts
        $this->clearCountCache();

        Log::warning('Student deleted', [
            'student_id' => $student->id,
            'user_id' => $student->user_id,
            'lms_user_id' => $lmsUserId,
            'deleted_by' => auth()->id(),
            'email' => $student->email,
        ]);

        return $student->delete();
    }

    /**
     * Check if a student can be deleted.
     * Students with approved applications cannot be deleted.
     * Note: Students with LMS accounts can now be deleted (deletion will be synced to LMS).
     *
     * @param  Student  $student  Student to check
     * @return bool True if can be deleted
     */
    public function canDelete(Student $student): bool
    {
        $user = $student->user;

        if (! $user) {
            return true; // Student has no user, can be deleted
        }

        // Check if student has an approved application
        $application = $student->studentApplication;
        if ($application && $application->status === 'approved') {
            return false;
        }

        // Note: Students with LMS accounts can now be deleted
        // The deletion will be synced to LMS in deleteStudent() method
        return true;
    }

    /**
     * Get paginated students with optional filters.
     *
     * @param  Request  $request  HTTP request with filters
     * @param  int  $perPage  Items per page
     */
    public function getPaginatedStudents(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Student::query()->with(['studentApplication', 'user']);

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        // Apply application status filter
        if ($status = $request->input('application_status')) {
            if ($status === 'with') {
                $query->whereHas('studentApplication');
            } elseif ($status === 'without') {
                $query->whereDoesntHave('studentApplication');
            }
        }

        // Apply program filter
        if ($programId = $request->input('program_id')) {
            $query->whereHas('user', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }

        // Apply sorting
        $sortBy = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        $allowedSorts = ['id', 'first_name', 'last_name', 'email', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get per-page from request with validation
        $requestedPerPage = (int) $request->input('per_page', $perPage);
        $validPerPage = in_array($requestedPerPage, [10, 15, 25, 50, 100]) ? $requestedPerPage : $perPage;

        return $query->paginate($validPerPage)->withQueryString();
    }

    /**
     * Get total students count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getTotalCount(int $cacheDuration = 300): int
    {
        return Student::count();
    }

    /**
     * Get students with applications count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getWithApplicationsCount(int $cacheDuration = 300): int
    {
        return Student::whereHas('studentApplication')->count();
    }

    /**
     * Get students without applications count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getWithoutApplicationsCount(int $cacheDuration = 300): int
    {
        return Student::whereDoesntHave('studentApplication')->count();
    }

    /**
     * Get new students this month count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getNewThisMonthCount(int $cacheDuration = 300): int
    {
        return Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Clear cached student counts.
     *
     * Call this after creating/updating/deleting students.
     */
    public function clearCountCache(): void {}

    /**
     * Get all active programs for selection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivePrograms()
    {
        return Program::where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Get student statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->getTotalCount(),
            'with_applications' => $this->getWithApplicationsCount(),
            'without_applications' => $this->getWithoutApplicationsCount(),
            'new_this_month' => $this->getNewThisMonthCount(),
        ];
    }

    /**
     * Get student counts for AJAX response.
     */
    public function getCounts(): array
    {
        return [
            'total' => $this->getTotalCount(0),
            'with_applications' => $this->getWithApplicationsCount(0),
            'without_applications' => $this->getWithoutApplicationsCount(0),
            'new_this_month' => $this->getNewThisMonthCount(0),
        ];
    }

    /**
     * Create LMS account for a bypass student.
     * This is used when bypass_application is enabled and the student needs LMS access.
     *
     * @param  User  $user  The user to create LMS account for
     * @param  Student  $student  The student record
     * @param  int  $programId  The program ID
     * @param  int|null  $intakeId  The intake ID
     */
    private function createLmsAccountForBypassStudent(User $user, Student $student, int $programId, ?int $intakeId = null): void
    {
        try {
            $lmsApiService = app(LmsApiService::class);

            $result = $lmsApiService->createStudent([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $student->phone,
                'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                'program_id' => $programId,
                'intake_id' => $intakeId,
                'admission_type' => LmsAdmissionTypeResolver::resolve($user),
                'is_suspended' => $user->is_suspended,
            ]);

            if ($result['success'] && $result['user_id']) {
                $user->update(['lms_user_id' => $result['user_id']]);

                Log::info('LMS account created for bypass student', [
                    'sis_user_id' => $user->id,
                    'lms_user_id' => $result['user_id'],
                    'student_number' => $student->student_number,
                ]);
            } else {
                Log::error('Failed to create LMS account for bypass student', [
                    'sis_user_id' => $user->id,
                    'error' => $result['error'] ?? 'Unknown error',
                    'lms_response_user_id' => $result['user_id'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('LMS API error during bypass student account creation', [
                'sis_user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
