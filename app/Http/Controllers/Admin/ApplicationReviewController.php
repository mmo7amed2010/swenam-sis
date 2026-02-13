<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentApplication;
use App\Models\User;
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
                ->with(['reviewer', 'agent']);

            // Pre-fetch programs ONCE per request (fresh API call, no caching)
            // This eliminates N+1 API calls when accessing program_name
            $lmsApiService = app(\App\Services\LmsApiService::class);
            $programs = collect($lmsApiService->getPrograms());
            $programsMap = $programs->keyBy('id'); // O(1) lookup

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: function ($application) use ($programsMap) {
                    // Use pre-fetched data instead of triggering accessor
                    $program = $programsMap->get($application->program_id);
                    
                    return [
                        'id' => $application->id,
                        'reference_number' => $application->reference_number,
                        'full_name' => $application->full_name,
                        'first_name' => $application->first_name,
                        'last_name' => $application->last_name,
                        'email' => $application->email,
                        'phone' => $application->phone,
                        'program_name' => $program['name'] ?? 'N/A', // Direct lookup, no API call
                        'program_id' => $application->program_id,
                        'status' => $application->status,
                        'noa_status' => $application->noa_status,
                        'msfaa_status' => $application->msfaa_status,
                        'created_at' => $application->created_at->format('M d, Y H:i'),
                        'created_at_human' => $application->created_at->diffForHumans(),
                        'funding_type' => $application->funding_type,
                        'reviewer_name' => $application->reviewer?->name,
                        'agent_name' => $application->agent ? ($application->agent->name ?? ($application->agent->first_name . ' ' . $application->agent->last_name)) : null,
                        'show_url' => route('admin.applications.show', $application),
                    ];
                },
                searchableColumns: ['reference_number', 'first_name', 'last_name', 'email', 'phone'],
                filters: [
                    'status' => fn ($q, $val) => $val !== 'all' ? $q->where('status', $val) : $q,
                    'noa_status' => fn ($q, $val) => $val !== 'all' ? $q->where('noa_status', $val) : $q,
                    'msfaa_status' => fn ($q, $val) => $val !== 'all' ? $q->where('msfaa_status', $val) : $q,
                    'program' => fn ($q, $val) => $val !== 'all' ? $q->where('program_id', $val) : $q,
                    'from' => fn ($q, $val) => $q->whereDate('created_at', '>=', $val),
                    'to' => fn ($q, $val) => $q->whereDate('created_at', '<=', $val),
                    'agent_id' => fn ($q, $val) => $q->where('agent_id', (int) $val),
                ],
                orderableColumns: [
                    0 => 'reference_number',
                    1 => 'first_name',
                    2 => 'email',
                    5 => 'created_at',
                    6 => 'status',
                ]
            );
        }

        // Regular page load - return view with stats
        $stats = $this->applicationRepository->getStats();

        $agentFilter = null;
        if ($request->filled('agent_id')) {
            $agentFilter = User::where('id', $request->input('agent_id'))
                ->where('user_type', 'agent')
                ->first(['id', 'name']);
        }

        return view('pages.admin.applications.index', compact('stats', 'agentFilter'));
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
        $noaStatus = $request->input('noa_status');
        $msfaaStatus = $request->input('msfaa_status');
        $agentId = $request->input('agent_id');

        return $this->applicationService->exportApplications($status, $from, $to, $format, $noaStatus, $msfaaStatus, $agentId);
    }

    /**
     * Display the specified application
     */
    public function show(StudentApplication $application)
    {
        $application->load('reviewer', 'createdUser', 'agent', 'latestContract', 'latestContract.template');

        $lmsApiService = app(\App\Services\LmsApiService::class);
        $intakes = $lmsApiService->getIntakes();

        return view('pages.admin.applications.show', compact('application', 'intakes'));
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
     * Update the intake assigned to an application
     */
    public function updateIntake(\App\Http\Requests\UpdateApplicationIntakeRequest $request, StudentApplication $application)
    {
        try {
            $lmsApiService = app(\App\Services\LmsApiService::class);
            $intake = $lmsApiService->getIntake((int) $request->input('intake_id'));

            if (! $intake) {
                return back()->with('error', 'Could not fetch intake details from LMS. Please try again.');
            }

            $this->applicationService->updateApplicationIntake(
                $application,
                (int) $request->input('intake_id'),
                $intake['name'] ?? 'Unknown Intake'
            );

            return back()->with('success', 'Intake has been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update application intake', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update intake: '.$e->getMessage());
        }
    }

    /**
     * Download uploaded document
     */
    public function downloadDocument(StudentApplication $application, string $documentType)
    {
        $validDocumentTypes = ['government_id', 'degree_certificate', 'transcripts', 'cv', 'english_test'];

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
