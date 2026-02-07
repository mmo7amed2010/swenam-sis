<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovePaymentRequest;
use App\Http\Requests\RejectPaymentRequest;
use App\Models\StudentApplication;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Download payment receipt.
     */
    public function downloadReceipt(StudentApplication $application)
    {
        if (! $application->payment_receipt_path || ! Storage::exists($application->payment_receipt_path)) {
            abort(404, 'Payment receipt not found.');
        }

        Log::info('Payment receipt downloaded', [
            'application_id' => $application->id,
            'downloaded_by' => auth()->id(),
        ]);

        return Storage::download($application->payment_receipt_path, 'payment-receipt.'.pathinfo($application->payment_receipt_path, PATHINFO_EXTENSION));
    }

    /**
     * Approve payment.
     */
    public function approve(ApprovePaymentRequest $request, StudentApplication $application)
    {
        try {
            $this->paymentService->approvePayment($application, $request->validated());

            return back()->with('success', 'Payment approved. Student account creation is in progress.');
        } catch (\Exception $e) {
            Log::error('Failed to approve payment', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve payment: '.$e->getMessage());
        }
    }

    /**
     * Reject payment.
     */
    public function reject(RejectPaymentRequest $request, StudentApplication $application)
    {
        try {
            $this->paymentService->rejectPayment($application, $request->validated());

            return back()->with('success', 'Payment rejected. Student has been notified to re-upload.');
        } catch (\Exception $e) {
            Log::error('Failed to reject payment', [
                'reference_number' => $application->reference_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject payment: '.$e->getMessage());
        }
    }
}
