<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\Student;
use App\Services\StudentService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;

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
    protected array $filters = [
        'application_status' => 'applyApplicationStatusFilter',
        'program_id' => 'applyProgramFilter',
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
        private StudentService $studentService
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
                ],
                searchableColumns: $this->searchableColumns,
                filters: $this->filters,
                orderableColumns: $this->orderableColumns
            );
        }

        // Regular page load - return view with counts and programs
        $stats = $this->studentService->getStatistics();
        $programs = $this->studentService->getActivePrograms();

        return view('pages.admin.students.index', [
            'totalStudents' => $stats['total'],
            'withApplications' => $stats['with_applications'],
            'withoutApplications' => $stats['without_applications'],
            'newThisMonth' => $stats['new_this_month'],
            'programs' => $programs,
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
                    'message' => __('Cannot delete student with an approved application or active LMS account.'),
                ], 422);
            }

            return redirect()->route('admin.students.index')
                ->with('error', 'Cannot delete student with an approved application or active LMS account.');
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
}
