<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\MsfaaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentMsfaaController extends Controller
{
    public function __construct(
        private MsfaaService $msfaaService
    ) {}

    /**
     * Confirm MSFAA (student clicks Yes).
     */
    public function confirm(Request $request): RedirectResponse
    {
        $user = $request->user();
        $application = $user->student?->studentApplication;

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        try {
            $this->msfaaService->confirmMsfaa($application);

            return back()->with('success', 'MSFAA confirmed successfully. It will be reviewed by our team.');
        } catch (\Exception $e) {
            Log::error('Failed to confirm MSFAA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to confirm MSFAA. Please try again.');
        }
    }
}
