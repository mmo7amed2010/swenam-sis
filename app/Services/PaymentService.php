<?php

namespace App\Services;

use App\Mail\PaymentApprovedMail;
use App\Models\ApplicationAuditLog;
use App\Models\StudentApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function __construct(
        private ApplicationReviewService $reviewService
    ) {}

    /**
     * Upload a payment receipt.
     */
    public function uploadPaymentReceipt(StudentApplication $application, UploadedFile $file): StudentApplication
    {
        if (! $application->canUploadPayment()) {
            throw new \Exception('Payment receipt can only be uploaded when payment is pending.');
        }

        $oldStatus = $application->status;

        // Store the file
        $directory = "applications/{$application->reference_number}";
        $filename = "payment-receipt-".now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename);

        $application->update([
            'status' => 'payment_uploaded',
            'payment_uploaded_at' => now(),
            'payment_receipt_path' => $path,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'payment_uploaded', null, $oldStatus);

        Log::info('Payment receipt uploaded', [
            'reference_number' => $application->reference_number,
            'uploaded_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Approve payment and finalize enrollment.
     */
    public function approvePayment(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canApprovePayment()) {
            throw new \Exception('Payment can only be approved when a receipt has been uploaded.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'payment_approved',
            'payment_approved_at' => now(),
            'payment_approved_by' => auth()->id(),
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'payment_approved', null, $oldStatus);

        // Send email
        Mail::to($application->email)->queue(new PaymentApprovedMail($application));

        // Auto-approve application (creates LMS account)
        $this->reviewService->approveApplication($application, $data);

        Log::info('Payment approved, application fully approved', [
            'reference_number' => $application->reference_number,
            'approved_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Reject payment (reset to payment_pending so student can re-upload).
     */
    public function rejectPayment(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canApprovePayment()) {
            throw new \Exception('Payment can only be rejected when a receipt has been uploaded.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'payment_pending',
            'payment_uploaded_at' => null,
            'payment_receipt_path' => null,
            'payment_rejection_reason' => $data['rejection_reason'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'payment_rejected', $data['rejection_reason'] ?? null, $oldStatus);

        Log::info('Payment rejected, student must re-upload', [
            'reference_number' => $application->reference_number,
            'rejected_by' => auth()->id(),
        ]);

        return $application->fresh();
    }
}
