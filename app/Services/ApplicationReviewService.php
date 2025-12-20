<?php

namespace App\Services;

use App\Exports\ApplicationsExport;
use App\Jobs\CreateLmsStudentAccountJob;
use App\Mail\ApplicationRejectedMail;
use App\Models\ApplicationAuditLog;
use App\Models\StudentApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ApplicationReviewService
{
    /**
     * Initially approve an application (first step of two-step approval).
     * Does NOT create student account - allows admin to discuss pricing etc.
     *
     * @param  StudentApplication  $application  Application to initially approve
     * @param  array  $data  Approval data (admin_notes)
     * @return StudentApplication Updated application
     *
     * @throws \Exception If initial approval fails
     */
    public function initialApproveApplication(StudentApplication $application, array $data): StudentApplication
    {
        if ($application->isRejected()) {
            throw new \Exception('Cannot approve a rejected application. Student must reapply.');
        }

        if (! $application->isPending()) {
            throw new \Exception('Only pending applications can be initially approved.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'initial_approved',
            'initial_approved_at' => now(),
            'initial_approved_by' => auth()->id(),
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        // Log decision in audit log
        ApplicationAuditLog::logDecision($application, 'initial_approved', null, $oldStatus);

        // NO email notification for initial approval (admin contacts student directly)

        Log::info('Student application initially approved', [
            'reference_number' => $application->reference_number,
            'reviewed_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Final approve an application and create student account.
     * Only applications with 'initial_approved' status can be finally approved.
     *
     * @param  StudentApplication  $application  Application to approve
     * @param  array  $data  Approval data (admin_notes)
     * @return StudentApplication Updated application
     *
     * @throws \Exception If approval fails
     */
    public function approveApplication(StudentApplication $application, array $data): StudentApplication
    {
        // Prevent re-approval of rejected applications
        if ($application->isRejected()) {
            throw new \Exception('Cannot approve a rejected application. Student must reapply.');
        }

        if (! $application->isInitialApproved()) {
            throw new \Exception('Only initially approved applications can be finally approved.');
        }

        // Verify student account already exists (created on submission)
        if (! $application->created_user_id) {
            throw new \Exception('Student account not found. The account should have been created on application submission.');
        }

        $oldStatus = $application->status;

        // Update application status
        $application->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
        ]);

        // Log decision in audit log
        ApplicationAuditLog::logDecision($application, 'approved', null, $oldStatus);

        // Dispatch job to create LMS account (SIS account already exists)
        CreateLmsStudentAccountJob::dispatch($application);

        Log::info('Student application approved - LMS account creation job dispatched', [
            'reference_number' => $application->reference_number,
            'reviewed_by' => auth()->id(),
            'user_id' => $application->created_user_id,
        ]);

        return $application->fresh();
    }

    /**
     * Reject an application.
     *
     * @param  StudentApplication  $application  Application to reject
     * @param  array  $data  Rejection data (rejection_reason, admin_notes)
     * @return StudentApplication Updated application
     *
     * @throws \Exception If rejection fails
     */
    public function rejectApplication(StudentApplication $application, array $data): StudentApplication
    {
        // Prevent re-rejection of already rejected applications
        if ($application->isRejected()) {
            throw new \Exception('This application has already been rejected.');
        }

        if (! $application->canBeRejected()) {
            throw new \Exception('Only pending or initially approved applications can be rejected.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => $data['rejection_reason'],
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        // Log decision in audit log
        ApplicationAuditLog::logDecision($application, 'rejected', $data['rejection_reason'], $oldStatus);

        Log::info('Student application rejected', [
            'reference_number' => $application->reference_number,
            'reviewed_by' => auth()->id(),
            'reason' => $data['rejection_reason'],
        ]);

        // Send rejection email (queued)
        Mail::to($application->email)->queue(new ApplicationRejectedMail($application));

        return $application->fresh();
    }

    /**
     * Export applications to Excel or CSV.
     *
     * @param  string  $status  Status filter (all, pending, approved, rejected)
     * @param  string|null  $from  Start date
     * @param  string|null  $to  End date
     * @param  string  $format  Export format (xlsx, csv)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Export file
     */
    public function exportApplications(string $status, ?string $from, ?string $to, string $format = 'xlsx')
    {
        $filename = "applications-{$status}-".now()->format('Y-m-d').".{$format}";

        return Excel::download(
            new ApplicationsExport($status, $from, $to),
            $filename,
            $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
