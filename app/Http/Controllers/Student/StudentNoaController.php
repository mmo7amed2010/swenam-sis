<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadNoaRequest;
use App\Services\NoaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentNoaController extends Controller
{
    public function __construct(
        private NoaService $noaService
    ) {}

    /**
     * Upload NOA document.
     */
    public function upload(UploadNoaRequest $request): RedirectResponse
    {
        $user = $request->user();
        $application = $user->student?->studentApplication;

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        try {
            $this->noaService->uploadNoa($application, $request->file('noa_document'));

            return back()->with('success', 'NOA document uploaded successfully. It will be reviewed by our team.');
        } catch (\Exception $e) {
            Log::error('Failed to upload NOA document', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to upload NOA document. Please try again.');
        }
    }

    /**
     * Skip NOA upload.
     */
    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();
        $application = $user->student?->studentApplication;

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        try {
            $this->noaService->skipNoa($application);

            return back()->with('success', 'NOA upload skipped. You can upload it later if needed.');
        } catch (\Exception $e) {
            Log::error('Failed to skip NOA', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to skip NOA. Please try again.');
        }
    }
}
