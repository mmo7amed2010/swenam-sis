<?php

namespace App\Services;

use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => 'student',
                'program_id' => $data['program_id'],
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

        // Update user record (name, email, password, program_id)
        if ($student->user) {
            $userData = [
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
                'program_id' => $newProgramId,
            ];

            if ($userPassword) {
                $userData['password'] = $userPassword;
            }

            $student->user->update($userData);
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
     */
    public function deleteStudent(Student $student): bool
    {
        // Clear cached counts
        $this->clearCountCache();

        Log::warning('Student deleted', [
            'student_id' => $student->id,
            'user_id' => $student->user_id,
            'deleted_by' => auth()->id(),
            'email' => $student->email,
        ]);

        return $student->delete();
    }

    /**
     * Check if a student can be deleted.
     * Students with approved applications or LMS accounts cannot be deleted.
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

        // Check if student has an LMS account (meaning they're active in LMS)
        if ($user->lms_user_id) {
            return false;
        }

        // Check if student has an approved application
        $application = $student->studentApplication;
        if ($application && $application->status === 'approved') {
            return false;
        }

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
        return Cache::remember('students.total.count', $cacheDuration, function () {
            return Student::count();
        });
    }

    /**
     * Get students with applications count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getWithApplicationsCount(int $cacheDuration = 300): int
    {
        return Cache::remember('students.with_applications.count', $cacheDuration, function () {
            return Student::whereHas('studentApplication')->count();
        });
    }

    /**
     * Get students without applications count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getWithoutApplicationsCount(int $cacheDuration = 300): int
    {
        return Cache::remember('students.without_applications.count', $cacheDuration, function () {
            return Student::whereDoesntHave('studentApplication')->count();
        });
    }

    /**
     * Get new students this month count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getNewThisMonthCount(int $cacheDuration = 300): int
    {
        return Cache::remember('students.new_this_month.count', $cacheDuration, function () {
            return Student::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        });
    }

    /**
     * Clear cached student counts.
     *
     * Call this after creating/updating/deleting students.
     */
    public function clearCountCache(): void
    {
        Cache::forget('students.total.count');
        Cache::forget('students.with_applications.count');
        Cache::forget('students.without_applications.count');
        Cache::forget('students.new_this_month.count');
    }

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
}
