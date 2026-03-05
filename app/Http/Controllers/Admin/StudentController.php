<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\Student;
use App\Jobs\SyncStudentStatusToLmsJob;
use App\Services\LmsAdmissionTypeResolver;
use App\Services\LmsApiService;
use App\Services\StudentService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    use HandlesDataTableRequests;

    /**
     * Searchable columns for DataTables.
     */
    protected array $searchableColumns = ['first_name', 'last_name', 'email', 'student_number'];

    /**
     * Filters configuration for DataTables.
     */
    protected array $filterMethods = [
        'application_status' => 'applyApplicationStatusFilter',
        'program_id' => 'applyProgramFilter',
        'suspension_status' => 'applySuspensionStatusFilter',
        'lms_status' => 'applyLmsStatusFilter',
    ];

    /**
     * Orderable columns configuration.
     */
    protected array $orderableColumns = [
        0 => 'first_name',
        1 => 'email',
        3 => 'created_at',
    ];

    public function __construct(
        private StudentService $studentService,
        private LmsApiService $lmsApiService
    ) {}

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        // Handle DataTables AJAX request
        if ($this->isDataTableRequest($request)) {
            $query = Student::query()->with(['studentApplication', 'user.program']);

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: fn ($student) => [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                    'student_number' => $student->student_number,
                    'profile_photo_url' => $student->user?->profile_photo_url,
                    'program_id' => $student->user?->program_id,
                    'program_name' => $student->user?->program?->name,
                    'bypass_application' => $student->user?->bypass_application ?? false,
                    'has_lms_account' => (bool) $student->user?->lms_user_id,
                    'application_reference' => $student->studentApplication?->reference_number,
                    'application_url' => $student->studentApplication
                        ? route('admin.applications.show', $student->studentApplication)
                        : null,
                    'created_at' => $student->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $student->created_at->format('d M Y, h:i a'),
                    'show_url' => route('admin.students.show', $student),
                    'edit_url' => route('admin.students.edit', $student),
                    'delete_url' => route('admin.students.destroy', $student),
                    'can_delete' => $this->studentService->canDelete($student),
                    'is_suspended' => $student->user?->is_suspended ?? false,
                    'lms_status' => $student->user ? LmsAdmissionTypeResolver::status($student->user) : [
                        'active' => false,
                        'reason' => 'No user account',
                        'admission_type' => null,
                    ],
                    'suspend_url' => route('admin.students.suspend', $student),
                    'unsuspend_url' => route('admin.students.unsuspend', $student),
                ],
                searchableColumns: $this->searchableColumns,
                filters: array_map(fn ($method) => [$this, $method], $this->filterMethods),
                orderableColumns: $this->orderableColumns
            );
        }

        // Regular page load - return view with counts and programs
        $stats = $this->studentService->getStatistics();
        $programs = $this->lmsApiService->getPrograms();
        $intakes = $this->lmsApiService->getIntakes();

        return view('pages.admin.students.index', [
            'totalStudents' => $stats['total'],
            'withApplications' => $stats['with_applications'],
            'withoutApplications' => $stats['without_applications'],
            'newThisMonth' => $stats['new_this_month'],
            'activeLms' => $stats['active_lms'],
            'programs' => $programs,
            'intakes' => $intakes,
        ]);
    }

    /**
     * Apply application status filter.
     */
    protected function applyApplicationStatusFilter($query, $value)
    {
        if ($value === 'with') {
            return $query->whereHas('studentApplication');
        } elseif ($value === 'without') {
            return $query->whereDoesntHave('studentApplication');
        }

        return $query;
    }

    /**
     * Apply program filter.
     */
    protected function applyProgramFilter($query, $value)
    {
        return $query->whereHas('user', function ($q) use ($value) {
            $q->where('program_id', $value);
        });
    }

    /**
     * Apply suspension status filter.
     */
    protected function applySuspensionStatusFilter($query, $value)
    {
        if ($value === 'suspended') {
            return $query->whereHas('user', function ($q) {
                $q->where('is_suspended', true);
            });
        } elseif ($value === 'active') {
            return $query->whereHas('user', function ($q) {
                $q->where('is_suspended', false);
            });
        }

        return $query;
    }

    /**
     * Apply LMS status filter.
     */
    protected function applyLmsStatusFilter($query, $value)
    {
        if ($value === 'active_lms') {
            return $query->whereHas('user', function ($q) {
                $q->whereNotNull('lms_user_id')
                    ->where('is_suspended', false)
                    ->where(function ($q2) {
                        $q2->whereHas('student.studentApplication', function ($q3) {
                            $q3->where('status', 'approved');
                        })->orWhere('bypass_application', true);
                    });
            });
        } elseif ($value === 'inactive_lms') {
            return $query->where(function ($q) {
                $q->whereDoesntHave('user', function ($q2) {
                    $q2->whereNotNull('lms_user_id');
                })->orWhereHas('user', function ($q2) {
                    $q2->where('is_suspended', true);
                })->orWhereHas('user', function ($q2) {
                    $q2->whereNotNull('lms_user_id')
                        ->where('is_suspended', false)
                        ->where('bypass_application', false)
                        ->whereDoesntHave('student.studentApplication', function ($q3) {
                            $q3->where('status', 'approved');
                        });
                });
            });
        } elseif ($value === 'no_lms') {
            return $query->whereHas('user', function ($q) {
                $q->whereNull('lms_user_id');
            });
        }

        return $query;
    }

    /**
     * Export students to Excel or CSV.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $extension = $format === 'csv' ? 'csv' : 'xlsx';
        $filename = 'students-' . now()->format('Y-m-d') . '.' . $extension;

        return Excel::download(
            new StudentsExport(
                $request->input('application_status'),
                $request->input('program_id'),
                $request->input('suspension_status')
            ),
            $filename
        );
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(StudentRequest $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $student = $this->studentService->createStudent($request->validated());
                $counts = $this->studentService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Student created successfully!'),
                    'student' => $student,
                    'total_students' => $counts['total'],
                    'with_applications' => $counts['with_applications'],
                    'without_applications' => $counts['without_applications'],
                    'new_this_month' => $counts['new_this_month'],
                ], 201);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to create student: ').$e->getMessage(),
                ], 422);
            }
        }

        $this->studentService->createStudent($request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully!');
    }

    /**
     * Display the specified student.
     * Returns JSON for AJAX requests.
     */
    public function show(Student $student)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $student->id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                    'student_number' => $student->student_number,
                    'program_id' => $student->user?->program_id,
                ],
            ]);
        }

        $student->load('studentApplication');

        return view('pages.admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     * Returns JSON for AJAX modal editing.
     */
    public function edit(Student $student)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $student->id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                    'student_number' => $student->student_number,
                    'program_id' => $student->user?->program_id,
                    'bypass_application' => $student->user?->bypass_application ?? false,
                ],
            ]);
        }

        // Redirect to index - no separate edit page
        return redirect()->route('admin.students.index');
    }

    /**
     * Update the specified student in storage.
     */
    public function update(StudentRequest $request, Student $student)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $student = $this->studentService->updateStudent($student, $request->validated());
                $counts = $this->studentService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Student updated successfully!'),
                    'student' => $student,
                    'total_students' => $counts['total'],
                    'with_applications' => $counts['with_applications'],
                    'without_applications' => $counts['without_applications'],
                    'new_this_month' => $counts['new_this_month'],
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to update student: ').$e->getMessage(),
                ], 422);
            }
        }

        $this->studentService->updateStudent($student, $request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Request $request, Student $student)
    {
        // Check if student can be deleted
        if (! $this->studentService->canDelete($student)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot delete student with an approved application.'),
                ], 422);
            }

            return redirect()->route('admin.students.index')
                ->with('error', 'Cannot delete student with an approved application.');
        }

        $this->studentService->deleteStudent($student);
        $counts = $this->studentService->getCounts();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Student deleted successfully!'),
                'total_students' => $counts['total'],
                'with_applications' => $counts['with_applications'],
                'without_applications' => $counts['without_applications'],
                'new_this_month' => $counts['new_this_month'],
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully!');
    }

    /**
     * Suspend a student's account.
     */
    public function suspend(Request $request, Student $student)
    {
        $user = $student->user;

        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Student has no associated user account.'),
                ], 422);
            }

            return redirect()->route('admin.students.index')
                ->with('error', 'Student has no associated user account.');
        }

        $user->update(['is_suspended' => true]);

        // Sync suspension to LMS
        if ($user->lms_user_id) {
            SyncStudentStatusToLmsJob::dispatch($user, 'suspend');
        }

        \Illuminate\Support\Facades\Log::info('Student suspended', [
            'student_id' => $student->id,
            'user_id' => $user->id,
            'suspended_by' => auth()->id(),
        ]);

        $counts = $this->studentService->getCounts();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Student suspended successfully!'),
                'total_students' => $counts['total'],
                'with_applications' => $counts['with_applications'],
                'without_applications' => $counts['without_applications'],
                'new_this_month' => $counts['new_this_month'],
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student suspended successfully!');
    }

    /**
     * Unsuspend (reactivate) a student's account.
     */
    public function unsuspend(Request $request, Student $student)
    {
        $user = $student->user;

        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Student has no associated user account.'),
                ], 422);
            }

            return redirect()->route('admin.students.index')
                ->with('error', 'Student has no associated user account.');
        }

        $user->update(['is_suspended' => false]);

        // Sync unsuspension to LMS
        if ($user->lms_user_id) {
            SyncStudentStatusToLmsJob::dispatch($user, 'unsuspend');
        }

        \Illuminate\Support\Facades\Log::info('Student unsuspended', [
            'student_id' => $student->id,
            'user_id' => $user->id,
            'unsuspended_by' => auth()->id(),
        ]);

        $counts = $this->studentService->getCounts();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Student reactivated successfully!'),
                'total_students' => $counts['total'],
                'with_applications' => $counts['with_applications'],
                'without_applications' => $counts['without_applications'],
                'new_this_month' => $counts['new_this_month'],
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student reactivated successfully!');
    }
}
