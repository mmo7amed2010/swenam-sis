<?php

namespace App\Services;

use App\Mail\NoaRejectedMail;
use App\Mail\NoaRequestedMail;
use App\Mail\NoaUploadedMail;
use App\Models\ApplicationAuditLog;
use App\Models\StudentApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class NoaService
{
    /**
     * Request NOA from a student.
     */
    public function requestNoa(StudentApplication $application): StudentApplication
    {
        if (! $application->canRequestNoa()) {
            throw new \Exception('NOA can only be requested for approved applications without an existing NOA request.');
        }

        $application->update([
            'noa_status' => 'requested',
            'noa_requested_at' => now(),
            'noa_requested_by' => auth()->id(),
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'noa_requested');

        // Send email to student
        Mail::to($application->email)->queue(new NoaRequestedMail($application));

        Log::info('NOA requested from student', [
            'reference_number' => $application->reference_number,
            'requested_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Upload a NOA document.
     */
    public function uploadNoa(StudentApplication $application, UploadedFile $file): StudentApplication
    {
        if (! $application->canUploadNoa()) {
            throw new \Exception('NOA can only be uploaded when it has been requested or rejected.');
        }

        $application = DB::transaction(function () use ($application, $file) {
            // Store the file
            $directory = "applications/{$application->reference_number}";
            $filename = 'noa-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename);

            if (! $path) {
                throw new \RuntimeException('Failed to store NOA document.');
            }

            $application->update([
                'noa_status' => 'uploaded',
                'noa_uploaded_at' => now(),
                'noa_document_path' => $path,
                'noa_rejection_reason' => null,
            ]);

            // Log audit
            ApplicationAuditLog::logDecision($application, 'noa_uploaded');

            Log::info('NOA document uploaded', [
                'reference_number' => $application->reference_number,
                'uploaded_by' => auth()->id(),
            ]);

            return $application->fresh();
        });

        // Notify admin (outside transaction — email failures won't roll back the upload)
        try {
            $adminEmail = config('mail.admin_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new NoaUploadedMail($application));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send NOA uploaded notification email', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);
        }

        return $application;
    }

    /**
     * Skip NOA upload (student decision).
     */
    public function skipNoa(StudentApplication $application): StudentApplication
    {
        if (! $application->canUploadNoa()) {
            throw new \Exception('NOA can only be skipped when it has been requested or rejected.');
        }

        $application->update([
            'noa_skipped' => true,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'noa_skipped');

        Log::info('Student skipped NOA upload', [
            'reference_number' => $application->reference_number,
            'skipped_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Approve NOA document.
     */
    public function approveNoa(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canReviewNoa()) {
            throw new \Exception('NOA can only be approved when a document has been uploaded.');
        }

        $application->update([
            'noa_status' => 'approved',
            'noa_approved_at' => now(),
            'noa_approved_by' => auth()->id(),
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'noa_approved');

        Log::info('NOA approved', [
            'reference_number' => $application->reference_number,
            'approved_by' => auth()->id(),
        ]);

        return $application->fresh();
    }

    /**
     * Reject NOA document (student can re-upload).
     */
    public function rejectNoa(StudentApplication $application, array $data): StudentApplication
    {
        if (! $application->canReviewNoa()) {
            throw new \Exception('NOA can only be rejected when a document has been uploaded.');
        }

        // Delete the orphaned file from disk
        if ($application->noa_document_path) {
            Storage::delete($application->noa_document_path);
        }

        $application->update([
            'noa_status' => 'rejected',
            'noa_document_path' => null,
            'noa_rejection_reason' => $data['rejection_reason'] ?? null,
        ]);

        // Log audit
        ApplicationAuditLog::logDecision($application, 'noa_rejected', $data['rejection_reason'] ?? null);

        // Send rejection email to student
        Mail::to($application->email)->queue(new NoaRejectedMail($application));

        Log::info('NOA rejected, student must re-upload', [
            'reference_number' => $application->reference_number,
            'rejected_by' => auth()->id(),
        ]);

        return $application->fresh();
    }
}
