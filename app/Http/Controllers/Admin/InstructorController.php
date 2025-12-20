<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorRequest;
use App\Models\Instructor;
use App\Services\InstructorManagementService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    use HandlesDataTableRequests;

    /**
     * Searchable columns for DataTables.
     */
    protected array $searchableColumns = ['first_name', 'last_name', 'email'];

    /**
     * Filters configuration for DataTables.
     */
    protected array $filters = [
        'status' => 'applyStatusFilter',
    ];

    /**
     * Orderable columns configuration.
     */
    protected array $orderableColumns = [
        0 => 'first_name',
        1 => 'email',
        2 => 'created_at',
    ];

    public function __construct(
        private InstructorManagementService $instructorService
    ) {}

    /**
     * Display a listing of instructors.
     */
    public function index(Request $request)
    {
        // Handle DataTables AJAX request
        if ($this->isDataTableRequest($request)) {
            $query = Instructor::query();

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: fn ($instructor) => [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'first_name' => $instructor->first_name,
                    'last_name' => $instructor->last_name,
                    'email' => $instructor->email,
                    'profile_photo_url' => $instructor->profile_photo_url,
                    'courses_count' => $instructor->courseInstructors()->whereNull('removed_at')->count(),
                    'created_at' => $instructor->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $instructor->created_at->format('d M Y, h:i a'),
                    'show_url' => route('admin.instructors.show', $instructor),
                    'can_delete' => $this->instructorService->canDelete($instructor),
                ],
                searchableColumns: $this->searchableColumns,
                filters: $this->filters,
                orderableColumns: $this->orderableColumns
            );
        }

        // Regular page load - return view with stats
        $stats = $this->instructorService->getStatistics();

        return view('pages.admin.instructors.index', [
            'totalInstructors' => $stats['total'],
            'activeInstructors' => $stats['active'],
            'newThisMonth' => $stats['new_this_month'],
        ]);
    }

    /**
     * Apply status filter.
     */
    protected function applyStatusFilter($query, $value)
    {
        if ($value === 'active') {
            return $query->whereHas('courseInstructors', fn ($q) => $q->whereNull('removed_at'));
        } elseif ($value === 'inactive') {
            return $query->whereDoesntHave('courseInstructors', fn ($q) => $q->whereNull('removed_at'));
        }

        return $query;
    }

    /**
     * Store a newly created instructor in storage.
     */
    public function store(InstructorRequest $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $data = $request->validated();

                // Handle file upload
                if ($request->hasFile('avatar')) {
                    $data['avatar'] = $request->file('avatar');
                }

                $instructor = $this->instructorService->createInstructor($data);
                $counts = $this->instructorService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Instructor created successfully!'),
                    'instructor' => $instructor,
                    'total_instructors' => $counts['total'],
                    'active_instructors' => $counts['active'],
                    'new_this_month' => $counts['new_this_month'],
                ], 201);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to create instructor: ').$e->getMessage(),
                ], 422);
            }
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->instructorService->createInstructor($data);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor created successfully.');
    }

    /**
     * Display the specified instructor.
     */
    public function show(Instructor $instructor)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $instructor->id,
                    'first_name' => $instructor->first_name,
                    'last_name' => $instructor->last_name,
                    'email' => $instructor->email,
                    'profile_photo_url' => $instructor->profile_photo_url,
                ],
            ]);
        }

        $instructor->load('courseInstructors.course');

        return view('pages.admin.instructors.show', compact('instructor'));
    }

    /**
     * Show the form for editing the specified instructor.
     */
    public function edit(Instructor $instructor)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $instructor->id,
                    'first_name' => $instructor->first_name,
                    'last_name' => $instructor->last_name,
                    'email' => $instructor->email,
                    'profile_photo_url' => $instructor->profile_photo_url,
                ],
            ]);
        }

        return redirect()->route('admin.instructors.index');
    }

    /**
     * Update the specified instructor in storage.
     */
    public function update(InstructorRequest $request, Instructor $instructor)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $data = $request->validated();

                // Handle file upload
                if ($request->hasFile('avatar')) {
                    $data['avatar'] = $request->file('avatar');
                }

                $instructor = $this->instructorService->updateInstructor($instructor, $data);
                $counts = $this->instructorService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Instructor updated successfully!'),
                    'instructor' => $instructor,
                    'total_instructors' => $counts['total'],
                    'active_instructors' => $counts['active'],
                    'new_this_month' => $counts['new_this_month'],
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to update instructor: ').$e->getMessage(),
                ], 422);
            }
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->instructorService->updateInstructor($instructor, $data);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor updated successfully.');
    }

    /**
     * Remove the specified instructor from storage.
     */
    public function destroy(Request $request, Instructor $instructor)
    {
        // Check if instructor can be deleted
        if (! $this->instructorService->canDelete($instructor)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot delete instructor with active course assignments.'),
                ], 422);
            }

            return redirect()->route('admin.instructors.index')
                ->with('error', 'Cannot delete instructor with active course assignments.');
        }

        $this->instructorService->deleteInstructor($instructor);
        $counts = $this->instructorService->getCounts();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Instructor deleted successfully!'),
                'total_instructors' => $counts['total'],
                'active_instructors' => $counts['active'],
                'new_this_month' => $counts['new_this_month'],
            ]);
        }

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor deleted successfully.');
    }

    /**
     * Show the form for creating a new instructor (redirects to index with modal).
     */
    public function create()
    {
        return redirect()->route('admin.instructors.index');
    }
}
