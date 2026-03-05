<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentApplication;
use App\Models\User;
use App\Repositories\ApplicationRepository;
use App\Services\ApplicationReviewService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $programs = $lmsApiService->getPrograms();

        return view('pages.admin.applications.show', compact('application', 'intakes', 'programs'));
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
     * Update the program assigned to an application.
     * Syncs the change to LMS (if applicable) — fails if LMS sync fails.
     */
    public function updateProgram(Request $request, StudentApplication $application)
    {
        $validated = $request->validate([
            'program_id' => 'required|integer',
        ]);

        if ((int) $application->program_id === (int) $validated['program_id']) {
            return back()->with('info', 'Program is already set to the selected value.');
        }

        $lmsApiService = app(\App\Services\LmsApiService::class);
        $programs = collect($lmsApiService->getPrograms());
        $newProgram = $programs->firstWhere('id', (int) $validated['program_id']);

        if (! $newProgram) {
            return back()->with('error', 'Could not find the selected program in LMS.');
        }

        $oldProgramName = $application->program_name ?? 'N/A';

        DB::beginTransaction();

        try {
            $application->update([
                'program_id' => $validated['program_id'],
            ]);

            // Update SIS user record if exists
            if ($application->created_user_id) {
                $sisUser = User::find($application->created_user_id);
                if ($sisUser) {
                    $sisUser->update(['program_id' => $validated['program_id']]);
                }

                // Sync to LMS if student has an LMS account
                if ($sisUser && $sisUser->lms_user_id) {
                    $result = $lmsApiService->updateStudent($sisUser->lms_user_id, [
                        'program_id' => (int) $validated['program_id'],
                    ]);

                    if (! $result['success']) {
                        throw new \Exception('Failed to update program in LMS: ' . ($result['error'] ?? 'Unknown error'));
                    }
                }
            }

            \App\Models\ApplicationAuditLog::create([
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'action' => 'program_changed',
                'old_status' => $application->status,
                'new_status' => $application->status,
                'reason' => "Program changed from \"{$oldProgramName}\" to \"{$newProgram['name']}\"",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return back()->with('success', 'Program has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update application program', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update program: ' . $e->getMessage());
        }
    }

    /**
     * Update the agency referral information on an application.
     */
    public function updateAgency(Request $request, StudentApplication $application)
    {
        $validated = $request->validate([
            'has_referral' => 'required|boolean',
            'referral_agency_name' => 'required_if:has_referral,1|nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $oldReferral = $application->has_referral ? ($application->referral_agency_name ?? 'Yes') : 'None';

            $application->update([
                'has_referral' => $validated['has_referral'],
                'referral_agency_name' => $validated['has_referral'] ? $validated['referral_agency_name'] : null,
            ]);

            $newReferral = $validated['has_referral'] ? ($validated['referral_agency_name'] ?? 'Yes') : 'None';

            \App\Models\ApplicationAuditLog::create([
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'action' => 'agency_referral_changed',
                'old_status' => $application->status,
                'new_status' => $application->status,
                'reason' => "Agency referral changed from \"{$oldReferral}\" to \"{$newReferral}\"",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return back()->with('success', 'Agency referral has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update agency referral', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update agency referral: ' . $e->getMessage());
        }
    }

    /**
     * Update the funding type on an application.
     */
    public function updateFundingType(Request $request, StudentApplication $application)
    {
        $validated = $request->validate([
            'funding_type' => 'required|in:self_funded,government_funded',
        ]);

        if ($application->funding_type === $validated['funding_type']) {
            return back()->with('info', 'Funding type is already set to the selected value.');
        }

        DB::beginTransaction();

        try {
            $oldFunding = $application->funding_type ?? 'N/A';

            $application->update([
                'funding_type' => $validated['funding_type'],
            ]);

            \App\Models\ApplicationAuditLog::create([
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'action' => 'funding_type_changed',
                'old_status' => $application->status,
                'new_status' => $application->status,
                'reason' => "Funding type changed from \"{$oldFunding}\" to \"{$validated['funding_type']}\"",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return back()->with('success', 'Funding type has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update funding type', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update funding type: ' . $e->getMessage());
        }
    }

    /**
     * Update the education details on an application.
     */
    public function updateEducation(Request $request, StudentApplication $application)
    {
        $validated = $request->validate([
            'education_country' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $oldCountry = $application->education_country ?? 'N/A';
            $oldInstitution = $application->institution_name ?? 'N/A';

            $application->update([
                'education_country' => $validated['education_country'],
                'institution_name' => $validated['institution_name'],
            ]);

            \App\Models\ApplicationAuditLog::create([
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'action' => 'education_changed',
                'old_status' => $application->status,
                'new_status' => $application->status,
                'reason' => "Education changed from \"{$oldInstitution} ({$oldCountry})\" to \"{$validated['institution_name']} ({$validated['education_country']})\"",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return back()->with('success', 'Education details have been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update education details', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update education details: ' . $e->getMessage());
        }
    }

    /**
     * Update the work experience on an application.
     */
    public function updateWork(Request $request, StudentApplication $application)
    {
        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'position_level' => 'required|in:Senior Managerial Position,Managerial Position,Entry Level Position',
            'organization_name' => 'required|string|max:255',
            'work_start_date' => 'required|date',
            'work_end_date' => 'nullable|date|after_or_equal:work_start_date',
            'years_of_experience' => 'required|integer|min:0|max:50',
        ]);

        DB::beginTransaction();

        try {
            $oldOrg = $application->organization_name ?? 'N/A';
            $oldTitle = $application->position_title ?? 'N/A';

            $application->update($validated);

            \App\Models\ApplicationAuditLog::create([
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'action' => 'work_changed',
                'old_status' => $application->status,
                'new_status' => $application->status,
                'reason' => "Work experience updated: \"{$oldTitle} at {$oldOrg}\" to \"{$validated['position_title']} at {$validated['organization_name']}\"",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return back()->with('success', 'Work experience has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update work details', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update work experience: ' . $e->getMessage());
        }
    }

    /**
     * Update the supporting documents on an application.
     */
    public function updateDocuments(Request $request, StudentApplication $application)
    {
        $validated = $request->validate([
            'government_id' => 'sometimes|nullable|file|max:10240',
            'degree_certificate' => 'sometimes|nullable|file|max:10240',
            'transcripts' => 'sometimes|nullable|file|max:10240',
            'cv' => 'sometimes|nullable|file|max:10240',
            'english_test' => 'sometimes|nullable|file|max:10240',
        ]);

        $documentService = app(\App\Services\ApplicationDocumentService::class);
        $documentTypes = ['government_id', 'degree_certificate', 'transcripts', 'cv', 'english_test'];
        $uploaded = [];

        DB::beginTransaction();

        try {
            foreach ($documentTypes as $docType) {
                if ($request->hasFile($docType)) {
                    $path = $documentService->upload(
                        $request->file($docType),
                        $application->reference_number,
                        $docType
                    );
                    $application->update(["{$docType}_path" => $path]);
                    $uploaded[] = $docType;
                }
            }

            if (! empty($uploaded)) {
                \App\Models\ApplicationAuditLog::create([
                    'application_id' => $application->id,
                    'user_id' => auth()->id(),
                    'action' => 'documents_updated',
                    'old_status' => $application->status,
                    'new_status' => $application->status,
                    'reason' => 'Documents updated: ' . implode(', ', array_map(fn ($d) => str_replace('_', ' ', $d), $uploaded)),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            DB::commit();

            if (empty($uploaded)) {
                return back()->with('info', 'No documents were selected for upload.');
            }

            return back()->with('success', count($uploaded) . ' document(s) updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update documents', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to update documents: ' . $e->getMessage());
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
