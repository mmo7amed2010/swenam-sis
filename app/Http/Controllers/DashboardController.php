<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Route to appropriate dashboard based on user type
        $user = $request->user();

        return match ($user->user_type) {
            'student' => $this->studentDashboard($request),
            'instructor' => $this->instructorDashboard($request),
            'admin' => $this->adminDashboard($request),
            default => abort(403, 'Invalid user type'),
        };
    }

    /**
     * Student dashboard - SIS focused (application tracking, student info)
     * Course access is provided via "My Program" link to LMS
     */
    protected function studentDashboard(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $application = $student?->studentApplication;

        // Check if application is pending (not yet approved)
        $isPendingApproval = $application && ! $application->isApproved();

        // Check if student has LMS account (courses are accessed via LMS, not SIS)
        $hasLmsAccess = $user->hasLmsAccount();

        return view('pages/dashboards.student', [
            'user' => $user,
            'student' => $student,
            'program' => $user->program,
            'application' => $application,
            'isPendingApproval' => $isPendingApproval,
            'hasLmsAccess' => $hasLmsAccess,
        ]);
    }


    /**
     * Instructor dashboard - SIS focused
     * Course management is handled via LMS
     */
    protected function instructorDashboard(Request $request)
    {
        $user = $request->user();

        return view('pages/dashboards.instructor', [
            'user' => $user,
        ]);
    }

    /**
     * Admin dashboard - system management focused
     */
    protected function adminDashboard(Request $request)
    {

        return view('pages/dashboards.admin', []);
    }
}
