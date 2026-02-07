<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadSignedContractRequest;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentContractController extends Controller
{
    public function __construct(
        private ContractService $contractService
    ) {}

    /**
     * Download the contract PDF.
     */
    public function downloadContract(Request $request)
    {
        $user = $request->user();
        $application = $user->student?->studentApplication;

        if (! $application) {
            abort(404, 'Application not found.');
        }

        $contract = $application->latestContract;

        if (! $contract || ! $contract->generated_pdf_path || ! Storage::exists($contract->generated_pdf_path)) {
            abort(404, 'Contract not found.');
        }

        Log::info('Student downloaded contract', [
            'reference_number' => $application->reference_number,
            'user_id' => $user->id,
        ]);

        return Storage::download($contract->generated_pdf_path, 'contract.pdf');
    }

    /**
     * Upload signed contract.
     */
    public function uploadSignedContract(UploadSignedContractRequest $request)
    {
        $user = $request->user();
        $application = $user->student?->studentApplication;

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        try {
            $this->contractService->uploadSignedContract($application, $request->file('signed_contract'));

            return back()->with('success', 'Signed contract uploaded successfully. It will be reviewed by our team.');
        } catch (\Exception $e) {
            Log::error('Failed to upload signed contract', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to upload signed contract: '.$e->getMessage());
        }
    }
}
