<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveNoaRequest;
use App\Http\Requests\RejectNoaRequest;
use App\Models\StudentApplication;
use App\Services\NoaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoaController extends Controller
{
    public function __construct(
        private NoaService $noaService
    ) {}

    /**
     * Request NOA from a student.
     */
    public function request(StudentApplication $application): RedirectResponse
    {
        try {
            $this->noaService->requestNoa($application);

            return back()->with('success', 'NOA request sent to the student successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to request NOA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to request NOA. Please try again.');
        }
    }

    /**
     * Download NOA document.
     */
    public function download(StudentApplication $application): StreamedResponse
    {
        if (! $application->noa_document_path || ! Storage::exists($application->noa_document_path)) {
            abort(404, 'NOA document not found.');
        }

        Log::info('NOA document downloaded', [
            'application_id' => $application->id,
            'downloaded_by' => auth()->id(),
        ]);

        return Storage::download($application->noa_document_path, 'noa-document.'.pathinfo($application->noa_document_path, PATHINFO_EXTENSION));
    }

    /**
     * Approve NOA document.
     */
    public function approve(ApproveNoaRequest $request, StudentApplication $application): RedirectResponse
    {
        try {
            $this->noaService->approveNoa($application, $request->validated());

            return back()->with('success', 'NOA document approved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to approve NOA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve NOA. Please try again.');
        }
    }

    /**
     * Reject NOA document.
     */
    public function reject(RejectNoaRequest $request, StudentApplication $application): RedirectResponse
    {
        try {
            $this->noaService->rejectNoa($application, $request->validated());

            return back()->with('success', 'NOA document rejected. Student has been notified to re-upload.');
        } catch (\Exception $e) {
            Log::error('Failed to reject NOA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject NOA. Please try again.');
        }
    }
}
