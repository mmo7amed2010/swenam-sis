<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentApplication;
use App\Repositories\ApplicationRepository;
use App\Services\ApplicationReviewService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApplicationReviewController extends Controller
{
    use HandlesDataTableRequests;

    public function __construct(
        private ApplicationReviewService $applicationService,
        private ApplicationRepository $applicationRepository
    ) {}

    /**
     * Display the application review dashboard with DataTables support.
     */
    public function index(Request $request)
    {
        // Handle DataTables AJAX request
        if ($this->isDataTableRequest($request)) {
            $query = StudentApplication::query()
                ->with(['reviewer']);

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: fn ($application) => [
                    'id' => $application->id,
                    'reference_number' => $application->reference_number,
                    'full_name' => $application->full_name,
                    'first_name' => $application->first_name,
                    'last_name' => $application->last_name,
                    'email' => $application->email,
                    'phone' => $application->phone,
                    'program_name' => $application->program_name ?? 'N/A',
                    'program_id' => $application->program_id,
                    'status' => $application->status,
                    'created_at' => $application->created_at->format('M d, Y H:i'),
                    'created_at_human' => $application->created_at->diffForHumans(),
                    'reviewer_name' => $application->reviewer?->name,
                    'show_url' => route('admin.applications.show', $application),
                ],
                searchableColumns: ['reference_number', 'first_name', 'last_name', 'email', 'phone'],
                filters: [
                    'status' => fn ($q, $val) => $val !== 'all' ? $q->where('status', $val) : $q,
                    'program' => fn ($q, $val) => $val !== 'all' ? $q->where('program_id', $val) : $q,
                    'from' => fn ($q, $val) => $q->whereDate('created_at', '>=', $val),
                    'to' => fn ($q, $val) => $q->whereDate('created_at', '<=', $val),
                ],
                orderableColumns: [
                    0 => 'reference_number',
                    1 => 'first_name',
                    2 => 'email',
                    4 => 'created_at',
                    5 => 'status',
                ]
            );
        }

        // Regular page load - return view with stats
        $stats = $this->applicationRepository->getStats();

        return view('pages.admin.applications.index', compact('stats'));
    }

    /**
     * Export applications to Excel or CSV
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $status = $request->input('status', 'all');
        $from = $request->input('from');
        $to = $request->input('to');

        return $this->applicationService->exportApplications($status, $from, $to, $format);
    }

    /**
     * Display the specified application
     */
    public function show(StudentApplication $application)
    {
        $application->load('reviewer', 'createdUser');

        return view('pages.admin.applications.show', compact('application'));
    }

    /**
     * Initially approve an application (first step of two-step approval)
     */
    public function initialApprove(\App\Http\Requests\InitialApproveApplicationRequest $request, StudentApplication $application)
    {
        try {
            $this->applicationService->initialApproveApplication($application, $request->only('admin_notes'));

            return back()->with('success', 'Application initially approved. You may now contact the student to discuss enrollment details.');
        } catch (\Exception $e) {
            Log::error('Failed to initially approve application', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
                'reviewed_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to initially approve application: '.$e->getMessage());
        }
    }

    /**
     * Final approve an application and create student account
     */
    public function approve(\App\Http\Requests\ApproveApplicationRequest $request, StudentApplication $application)
    {
        try {
            $this->applicationService->approveApplication($application, $request->only('admin_notes'));

            return back()->with('success', 'Application finally approved. Student account creation is in progress and credentials will be emailed shortly.');
        } catch (\Exception $e) {
            Log::error('Failed to approve application', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
                'reviewed_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to approve application: '.$e->getMessage());
        }
    }

    /**
     * Reject an application
     */
    public function reject(\App\Http\Requests\RejectApplicationRequest $request, StudentApplication $application)
    {

        try {
            $this->applicationService->rejectApplication($application, $request->only('rejection_reason', 'admin_notes'));

            return back()->with('success', 'Application has been rejected and the applicant has been notified.');
        } catch (\Exception $e) {
            Log::error('Failed to reject application', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
                'reviewed_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to reject application: '.$e->getMessage());
        }
    }

    /**
     * Download uploaded document
     */
    public function downloadDocument(StudentApplication $application, string $documentType)
    {
        $validDocumentTypes = ['degree_certificate', 'transcripts', 'cv', 'english_test'];

        if (! in_array($documentType, $validDocumentTypes)) {
            abort(404, 'Invalid document type');
        }

        $pathField = "{$documentType}_path";
        $path = $application->$pathField;

        if (! $path || ! Storage::exists($path)) {
            abort(404, 'Document not found');
        }

        // Check if preview is requested
        if (request()->has('preview')) {
            return Storage::response($path);
        }

        // Log document download for audit trail
        Log::info('Application document downloaded', [
            'application_id' => $application->id,
            'reference_number' => $application->reference_number,
            'document_type' => $documentType,
            'downloaded_by' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);

        return Storage::download($path);
    }
}
