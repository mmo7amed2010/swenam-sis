<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveMsfaaRequest;
use App\Http\Requests\RejectMsfaaRequest;
use App\Models\StudentApplication;
use App\Services\MsfaaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class MsfaaController extends Controller
{
    public function __construct(
        private MsfaaService $msfaaService
    ) {}

    /**
     * Request MSFAA from a student.
     */
    public function request(StudentApplication $application): RedirectResponse
    {
        try {
            $this->msfaaService->requestMsfaa($application);

            return back()->with('success', 'MSFAA request sent to the student successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to request MSFAA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to request MSFAA. Please try again.');
        }
    }

    /**
     * Approve MSFAA.
     */
    public function approve(ApproveMsfaaRequest $request, StudentApplication $application): RedirectResponse
    {
        try {
            $this->msfaaService->approveMsfaa($application, $request->validated());

            return back()->with('success', 'MSFAA approved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to approve MSFAA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve MSFAA. Please try again.');
        }
    }

    /**
     * Reject MSFAA.
     */
    public function reject(RejectMsfaaRequest $request, StudentApplication $application): RedirectResponse
    {
        try {
            $this->msfaaService->rejectMsfaa($application, $request->validated());

            return back()->with('success', 'MSFAA rejected. Student has been notified to re-confirm.');
        } catch (\Exception $e) {
            Log::error('Failed to reject MSFAA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject MSFAA. Please try again.');
        }
    }
}
