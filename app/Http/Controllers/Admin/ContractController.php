<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveContractRequest;
use App\Http\Requests\IssueContractRequest;
use App\Http\Requests\PreviewContractRequest;
use App\Http\Requests\RejectContractRequest;
use App\Models\ContractTemplate;
use App\Models\StudentApplication;
use App\Models\StudentContract;
use App\Services\ContractService;
use App\Services\ContractTemplateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct(
        private ContractService $contractService,
        private ContractTemplateService $templateService
    ) {}

    /**
     * Show the contract creation form (select template, fill admin fields).
     */
    public function create(StudentApplication $application)
    {
        if (! $application->canSendContract()) {
            return back()->with('error', 'Contract can only be sent for initially approved applications.');
        }

        $templates = $this->templateService->getTemplatesForProgram($application->program_id);

        return view('pages.admin.contracts.create', compact('application', 'templates'));
    }

    /**
     * Get template placeholders via AJAX.
     */
    public function getTemplatePlaceholders(ContractTemplate $template)
    {
        return response()->json([
            'admin_placeholders' => $template->getAdminPlaceholders(),
            'sis_placeholders' => array_keys(ContractTemplate::getSisPlaceholders()),
        ]);
    }

    /**
     * Preview the rendered contract HTML via AJAX.
     */
    public function preview(PreviewContractRequest $request, StudentApplication $application)
    {
        $template = ContractTemplate::findOrFail($request->contract_template_id);
        $sisValues = $this->contractService->resolveSisPlaceholders($application);
        $adminValues = $request->input('admin_fields', []);

        $renderedBody = $this->contractService->renderContractBody($template->body, $sisValues, $adminValues);

        return response()->json(['html' => $renderedBody]);
    }

    /**
     * Issue the contract.
     */
    public function store(IssueContractRequest $request, StudentApplication $application)
    {
        try {
            $template = ContractTemplate::findOrFail($request->contract_template_id);
            $adminFields = $request->input('admin_fields', []);

            $this->contractService->issueContract($application, $template, $adminFields);

            return redirect()
                ->route('admin.applications.show', $application)
                ->with('success', 'Contract has been issued and sent to the student.');
        } catch (\Exception $e) {
            Log::error('Failed to issue contract', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to issue contract: '.$e->getMessage());
        }
    }

    /**
     * Download the generated contract PDF.
     */
    public function downloadGenerated(StudentContract $contract)
    {
        if (! $contract->generated_pdf_path || ! Storage::exists($contract->generated_pdf_path)) {
            abort(404, 'Generated contract not found.');
        }

        Log::info('Generated contract downloaded', [
            'contract_id' => $contract->id,
            'downloaded_by' => auth()->id(),
        ]);

        return Storage::download($contract->generated_pdf_path, 'contract.pdf');
    }

    /**
     * Download the signed contract PDF.
     */
    public function downloadSigned(StudentContract $contract)
    {
        if (! $contract->signed_pdf_path || ! Storage::exists($contract->signed_pdf_path)) {
            abort(404, 'Signed contract not found.');
        }

        Log::info('Signed contract downloaded', [
            'contract_id' => $contract->id,
            'downloaded_by' => auth()->id(),
        ]);

        return Storage::download($contract->signed_pdf_path, 'signed-contract.pdf');
    }

    /**
     * Approve the contract.
     */
    public function approveContract(ApproveContractRequest $request, StudentApplication $application)
    {
        try {
            $this->contractService->approveContract($application, $request->validated());

            $message = $application->isGovernmentFunded()
                ? 'Contract approved. Student account creation is in progress.'
                : 'Contract approved. Awaiting payment receipt from student.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Failed to approve contract', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve contract: '.$e->getMessage());
        }
    }

    /**
     * Reject the contract.
     */
    public function rejectContract(RejectContractRequest $request, StudentApplication $application)
    {
        try {
            $this->contractService->rejectContract($application, $request->validated());

            return back()->with('success', 'Contract rejected. Student has been notified to re-upload.');
        } catch (\Exception $e) {
            Log::error('Failed to reject contract', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject contract: '.$e->getMessage());
        }
    }
}
