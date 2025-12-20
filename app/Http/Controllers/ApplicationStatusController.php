<?php

namespace App\Http\Controllers;

use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicationStatusController extends Controller
{
    /**
     * Show the status check form.
     */
    public function index()
    {
        return view('application.status-check');
    }

    /**
     * Check application status based on email and application number.
     */
    public function check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reference_number' => 'required|string',
        ]);

        // Query by both email AND reference number for security
        $application = StudentApplication::where('email', $request->email)
            ->where('reference_number', $request->reference_number)
            ->with('program')
            ->first();

        if (! $application) {
            // Don't reveal which field is wrong (security)
            return back()->withErrors([
                'application' => 'Application not found. Please check your email and reference number.',
            ])->withInput($request->only('email'));
        }

        // Log status check for analytics
        Log::info('Application status checked', [
            'application_id' => $application->id,
            'reference_number' => $application->reference_number,
            'status' => $application->status,
            'ip' => $request->ip(),
        ]);

        return view('application.status', [
            'application' => $application,
        ]);
    }
}
