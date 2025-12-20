<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display the My Application page.
     * Shows either:
     * - Pending approval message (if application not approved)
     * - Link to LMS courses (if approved and LMS account exists)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $application = $student?->studentApplication;

        // Check if application is approved and LMS account exists
        $isApproved = $application?->isApproved() && $user->hasLmsAccount();

        return view('pages.student.program.index', [
            'user' => $user,
            'student' => $student,
            'application' => $application,
            'isApproved' => $isApproved,
        ]);
    }
}
