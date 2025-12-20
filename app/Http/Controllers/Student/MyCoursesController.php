<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\LmsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyCoursesController extends Controller
{
    protected LmsApiService $lmsApiService;

    public function __construct(LmsApiService $lmsApiService)
    {
        $this->lmsApiService = $lmsApiService;
    }

    /**
     * Display the My Courses page with link to LMS.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Check if user has LMS account linked
        $hasLmsAccount = ! empty($user->lms_user_id);

        return view('pages.student.my-courses', [
            'user' => $user,
            'hasLmsAccount' => $hasLmsAccount,
            'lmsUrl' => config('lms.api_url'),
        ]);
    }

    /**
     * Generate SSO token and redirect to LMS.
     */
    public function redirectToLms(Request $request)
    {
        $user = $request->user();

        // Check if user has LMS account
        if (empty($user->lms_user_id)) {
            Log::warning('Student without LMS account tried to access courses', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('student.my-courses')
                ->with('error', 'Your LMS account is not yet set up. Please contact administration.');
        }

        // Generate SSO token
        $tokenData = $this->lmsApiService->generateSsoToken($user, '/dashboard');

        if (! $tokenData) {
            Log::error('Failed to generate SSO token for student', [
                'user_id' => $user->id,
                'lms_user_id' => $user->lms_user_id,
            ]);

            return redirect()->route('student.my-courses')
                ->with('error', 'Unable to connect to Learning Management System. Please try again later.');
        }

        // Build redirect URL
        $redirectUrl = $this->lmsApiService->getSsoLoginUrl(
            $tokenData['access_token'],
            $tokenData['redirect_to']
        );

        Log::info('Student redirecting to LMS via SSO', [
            'user_id' => $user->id,
            'lms_user_id' => $user->lms_user_id,
        ]);

        return redirect()->away($redirectUrl);
    }
}
