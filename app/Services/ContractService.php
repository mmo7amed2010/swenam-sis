<?php

namespace App\Services;

use App\Mail\ContractApprovedMail;
use App\Mail\ContractIssuedMail;
use App\Mail\PaymentPendingMail;
use App\Models\ApplicationAuditLog;
use App\Models\ContractTemplate;
use App\Models\StudentApplication;
use App\Models\StudentContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ContractService
{
    public function __construct(
        private ApplicationReviewService $reviewService
    ) {}

    /**
     * Resolve SIS placeholder values from application data.
     */
    public function resolveSisPlaceholders(StudentApplication $application): array
    {
        $addressParts = array_filter([
            $application->address_line1,
            $application->address_line2,
            $application->city,
            $application->state_province,
            $application->postal_code,
            $application->country,
        ]);

        return [
            '{{student_name}}' => $application->full_name,
            '{{student_id}}' => $application->reference_number,
            '{{email}}' => $application->email,
            '{{phone}}' => $application->phone,
            '{{program_name}}' => $application->program_name ?? 'N/A',
            '{{intake_name}}' => $application->intake_name ?? 'N/A',
            '{{reference_number}}' => $application->reference_number,
            '{{date_of_birth}}' => $application->date_of_birth ? $application->date_of_birth->format('F d, Y') : 'N/A',
            '{{funding_type}}' => $application->isSelfFunded() ? 'Self-Funded' : 'Government-Funded',
            '{{nationality}}' => $application->country_of_citizenship ?? 'N/A',
            '{{address}}' => !empty($addressParts) ? implode(', ', $addressParts) : 'N/A',
            '{{contract_date}}' => now()->format('F d, Y'),
            '{{highest_education}}' => $application->highest_education_level ?? 'N/A',
            '{{institution_name}}' => $application->institution_name ?? 'N/A',
        ];
    }

    /**
     * Render the contract body by replacing all placeholders.
     */
    public function renderContractBody(string $templateBody, array $sisValues, array $adminValues): string
    {
        // Format admin values to match {{key}} format
        $formattedAdminValues = [];
        foreach ($adminValues as $key => $value) {
            $formattedAdminValues['{{'.$key.'}}'] = $value;
        }

        $allValues = array_merge($sisValues, $formattedAdminValues);

        return str_replace(array_keys($allValues), array_values($allValues), $templateBody);
    }

    /**
     * Generate a contract PDF.
     */
    public function generateContractPdf(StudentApplication $application, string $renderedHtml): string
    {
        $pdf = Pdf::loadView('pdf.contract', [
            'application' => $application,
            'renderedBody' => $renderedHtml,
            'generatedAt' => now(),
        ]);

        $directory = "applications/{$application->reference_number}";
        $filename = "contract-".now()->format('YmdHis').'.pdf';
        $path = "{$directory}/{$filename}";

        Storage::makeDirectory($directory);
        Storage::put($path, $pdf->output());

        return $path;
    }

    /**
     * Issue a contract for an application.
     */
    public function issueContract(StudentApplication $application, ContractTemplate $template, array $adminFieldValues): StudentContract
    {
        if (! $application->canSendContract()) {
            throw new \Exception('Contract can only be sent for initially approved applications.');
        }

        $oldStatus = $application->status;

        // Resolve placeholders and render
        $sisValues = $this->resolveSisPlaceholders($application);
        $renderedBody = $this->renderContractBody($template->body, $sisValues, $adminFieldValues);

        // Generate PDF
        $pdfPath = $this->generateContractPdf($application, $renderedBody);

        // Create contract record
        $contract = StudentContract::create([
            'application_id' => $application->id,
            'contract_template_id' => $template->id,
            'admin_field_values' => $adminFieldValues,
            'rendered_body' => $renderedBody,
            'generated_pdf_path' => $pdfPath,
            'issued_by' => auth()->id(),
            'issued_at' => now(),
        ]);

        // Update application status
        $application->update([
            'status' => 'contract_sent',
            'contract_sent_at' => now(),
            'contract_sent_by' => auth()->id(),
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'contract_sent', null, $oldStatus);

        // Send email with PDF attachment
        Mail::to($application->email)->queue(new ContractIssuedMail($application, $pdfPath));

        Log::info('Contract issued for application', [
            'reference_number' => $application->reference_number,
            'template_id' => $template->id,
            'issued_by' => auth()->id(),
        ]);

        return $contract;
    }

    /**
     * Upload a signed contract.
     */
    public function uploadSignedContract(StudentApplication $application, UploadedFile $file): StudentContract
    {
        if (! $application->canUploadSignedContract()) {
            throw new \Exception('Signed contract can only be uploaded when contract has been sent.');
        }

        $oldStatus = $application->status;

        // Store the signed file
        $directory = "applications/{$application->reference_number}";
        $filename = "signed-contract-".now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename);

        // Update the latest contract
        $contract = $application->latestContract;
        $contract->update([
            'signed_pdf_path' => $path,
            'signed_at' => now(),
        ]);

        // Update application status
        $application->update([
            'status' => 'contract_uploaded',
            'contract_uploaded_at' => now(),
            'signed_contract_path' => $path,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'contract_uploaded', null, $oldStatus);

        Log::info('Signed contract uploaded', [
            'reference_number' => $application->reference_number,
            'uploaded_by' => auth()->id(),
        ]);

        return $contract;
    }

    /**
     * Approve a contract.
     * For government-funded: auto-approves the application.
     * For self-funded: transitions to payment_pending.
     */
    public function approveContract(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canApproveContract()) {
            throw new \Exception('Contract can only be approved when signed contract has been uploaded.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'contract_approved',
            'contract_approved_at' => now(),
            'contract_approved_by' => auth()->id(),
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'contract_approved', null, $oldStatus);

        // Send contract approved email
        Mail::to($application->email)->queue(new ContractApprovedMail($application));

        if ($application->isGovernmentFunded()) {
            // Auto-approve application for government-funded students
            $this->reviewService->approveApplication($application, $data);
        } else {
            // Transition to payment_pending for self-funded
            $application->update(['status' => 'payment_pending']);
            ApplicationAuditLog::logDecision($application, 'payment_pending', null, 'contract_approved');

            Mail::to($application->email)->queue(new PaymentPendingMail($application));
        }

        Log::info('Contract approved', [
            'reference_number' => $application->reference_number,
            'funding_type' => $application->funding_type,
            'approved_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Reject a contract (reset to contract_sent so student can re-upload).
     */
    public function rejectContract(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canApproveContract()) {
            throw new \Exception('Contract can only be rejected when signed contract has been uploaded.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'contract_sent',
            'contract_uploaded_at' => null,
            'signed_contract_path' => null,
            'contract_rejection_reason' => $data['rejection_reason'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
        ]);

        // Clear signed PDF from latest contract
        $contract = $application->latestContract;
        if ($contract) {
            $contract->update([
                'signed_pdf_path' => null,
                'signed_at' => null,
            ]);
        }

        // Log audit
        ApplicationAuditLog::logDecision($application, 'contract_rejected', $data['rejection_reason'] ?? null, $oldStatus);

        Log::info('Contract rejected, student must re-upload', [
            'reference_number' => $application->reference_number,
            'rejected_by' => auth()->id(),
        ]);

        return $application->fresh();
    }
}
