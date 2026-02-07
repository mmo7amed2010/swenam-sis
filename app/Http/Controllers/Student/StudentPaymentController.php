<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentReceiptRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentPaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Upload payment receipt.
     */
    public function uploadReceipt(UploadPaymentReceiptRequest $request)
    {
        $user = $request->user();
        $application = $user->student?->studentApplication;

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        try {
            $this->paymentService->uploadPaymentReceipt($application, $request->file('payment_receipt'));

            return back()->with('success', 'Payment receipt uploaded successfully. It will be reviewed by our team.');
        } catch (\Exception $e) {
            Log::error('Failed to upload payment receipt', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to upload payment receipt: '.$e->getMessage());
        }
    }
}
