<?php

namespace App\Services;

use App\Mail\MsfaaConfirmedMail;
use App\Mail\MsfaaRejectedMail;
use App\Mail\MsfaaRequestedMail;
use App\Models\ApplicationAuditLog;
use App\Models\StudentApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MsfaaService
{
    /**
     * Request MSFAA from a student.
     */
    public function requestMsfaa(StudentApplication $application): StudentApplication
    {
        if (! $application->canRequestMsfaa()) {
            throw new \Exception('MSFAA can only be requested for applications with an approved NOA and no existing MSFAA request.');
        }

        $application->update([
            'msfaa_status' => 'requested',
            'msfaa_requested_at' => now(),
            'msfaa_requested_by' => auth()->id(),
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'msfaa_requested');

        Log::info('MSFAA requested from student', [
            'reference_number' => $application->reference_number,
            'requested_by' => auth()->id(),
        ]);

        // Send email to student (email failures won't break the request)
        try {
            Mail::to($application->email)->queue(new MsfaaRequestedMail($application));
        } catch (\Exception $e) {
            Log::warning('Failed to send MSFAA requested notification email', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);
        }

        return $application->fresh();
    }

    /**
     * Confirm MSFAA (student clicks Yes).
     */
    public function confirmMsfaa(StudentApplication $application): StudentApplication
    {
        if (! $application->canConfirmMsfaa()) {
            throw new \Exception('MSFAA can only be confirmed when it has been requested or rejected.');
        }

        $application->update([
            'msfaa_status' => 'confirmed',
            'msfaa_confirmed_at' => now(),
            'msfaa_rejection_reason' => null,
            'msfaa_admin_notes' => null,
            'msfaa_rejected_by' => null,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'msfaa_confirmed');

        Log::info('MSFAA confirmed by student', [
            'reference_number' => $application->reference_number,
            'confirmed_by' => auth()->id(),
        ]);

        // Notify admin (email failures won't break the confirmation)
        try {
            $adminEmail = config('mail.admin_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new MsfaaConfirmedMail($application));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send MSFAA confirmed notification email', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);
        }

        return $application->fresh();
    }

    /**
     * Approve MSFAA.
     */
    public function approveMsfaa(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canReviewMsfaa()) {
            throw new \Exception('MSFAA can only be approved when it has been confirmed by the student.');
        }

        $application->update([
            'msfaa_status' => 'approved',
            'msfaa_approved_at' => now(),
            'msfaa_approved_by' => auth()->id(),
            'msfaa_admin_notes' => $data['admin_notes'] ?? null,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'msfaa_approved');

        Log::info('MSFAA approved', [
            'reference_number' => $application->reference_number,
            'approved_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Reject MSFAA (student must re-confirm).
     */
    public function rejectMsfaa(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canReviewMsfaa()) {
            throw new \Exception('MSFAA can only be rejected when it has been confirmed by the student.');
        }

        $application->update([
            'msfaa_status' => 'rejected',
            'msfaa_rejection_reason' => $data['rejection_reason'] ?? null,
            'msfaa_admin_notes' => $data['admin_notes'] ?? null,
            'msfaa_rejected_by' => auth()->id(),
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'msfaa_rejected', $data['rejection_reason'] ?? null);

        Log::info('MSFAA rejected, student must re-confirm', [
            'reference_number' => $application->reference_number,
            'rejected_by' => auth()->id(),
        ]);

        // Send rejection email to student (email failures won't break the rejection)
        try {
            Mail::to($application->email)->queue(new MsfaaRejectedMail($application));
        } catch (\Exception $e) {
            Log::warning('Failed to send MSFAA rejected notification email', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);
        }

        return $application->fresh();
    }
}
