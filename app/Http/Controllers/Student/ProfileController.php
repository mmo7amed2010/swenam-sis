<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Services\LmsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected LmsApiService $lmsApiService;

    public function __construct(LmsApiService $lmsApiService)
    {
        $this->lmsApiService = $lmsApiService;
    }

    /**
     * Display the student's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $application = $student?->studentApplication;

        $profileData = $this->buildProfileData($user, $student, $application);

        return view('pages.student.profile.show', [
            'user' => $user,
            'student' => $student,
            'application' => $application,
            'profile' => $profileData,
            'hasApplication' => ! empty($application),
        ]);
    }

    /**
     * Update the student's profile.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $student = $user->student;
        $validated = $request->validated();

        // Update user name
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
        ]);

        if ($student) {
            $student->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'] ?? $student->phone,
                'address' => [
                    'line1' => $validated['address_line1'] ?? null,
                    'line2' => $validated['address_line2'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state_province' => $validated['state_province'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'country' => $validated['country'] ?? null,
                ],
                'emergency_first_name' => $validated['emergency_first_name'] ?? null,
                'emergency_last_name' => $validated['emergency_last_name'] ?? null,
                'emergency_phone' => $validated['emergency_phone'] ?? null,
                'emergency_address' => [
                    'line1' => $validated['emergency_address_line1'] ?? null,
                    'line2' => $validated['emergency_address_line2'] ?? null,
                    'city' => $validated['emergency_city'] ?? null,
                    'state_province' => $validated['emergency_state_province'] ?? null,
                    'postal_code' => $validated['emergency_postal_code'] ?? null,
                    'country' => $validated['emergency_country'] ?? null,
                ],
            ]);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $photoPath = $request->file('profile_photo')->store('profiles', 'public');
            $user->update(['profile_photo_path' => $photoPath]);
        }

        // Handle photo removal
        if (! empty($validated['profile_photo_remove']) && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        // Sync name with LMS if user has an LMS account
        $lmsSyncError = null;
        if ($user->lms_user_id) {
            $lmsResult = $this->lmsApiService->updateStudent($user->lms_user_id, [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ]);

            if (! $lmsResult['success']) {
                $lmsSyncError = $lmsResult['error'] ?? 'Failed to sync with LMS';
                Log::warning('Profile updated locally but LMS sync failed', [
                    'user_id' => $user->id,
                    'lms_user_id' => $user->lms_user_id,
                    'error' => $lmsSyncError,
                ]);
            }
        }

        Log::info('Student profile updated', [
            'user_id' => $user->id,
            'student_id' => $student?->id,
            'lms_synced' => $user->lms_user_id && ! $lmsSyncError,
        ]);

        $message = __('Profile updated successfully.');
        if ($lmsSyncError) {
            $message .= ' '.__('However, there was an issue syncing with the learning system. Please try again later.');
        }

        return redirect()->route('student.profile.show')
            ->with($lmsSyncError ? 'warning' : 'success', $message);
    }

    /**
     * Build profile data from available sources.
     * Priority: User/Student data > Application data (fallback)
     */
    private function buildProfileData($user, $student, $application): array
    {
        $profile = [
            // Name: Use user data first, fallback to application if empty
            'first_name' => $user->first_name ?: ($application?->first_name ?? ''),
            'last_name' => $user->last_name ?: ($application?->last_name ?? ''),
            'email' => $user->email,
            'phone' => $student?->phone,
            'address' => $student?->address ?? [],
            'emergency_first_name' => $student?->emergency_first_name,
            'emergency_last_name' => $student?->emergency_last_name,
            'emergency_phone' => $student?->emergency_phone,
            'emergency_address' => $student?->emergency_address ?? [],
        ];

        // If application exists, use as fallback for empty fields
        if ($application) {
            // Pre-populate address from application if student address is empty
            if (empty($profile['address']) || empty(array_filter($profile['address'] ?? []))) {
                $profile['address'] = [
                    'line1' => $application->address_line1,
                    'line2' => $application->address_line2,
                    'city' => $application->city,
                    'state_province' => $application->state_province,
                    'postal_code' => $application->postal_code,
                    'country' => $application->country,
                ];
            }

            // Use application phone if student phone is empty
            if (empty($profile['phone'])) {
                $profile['phone'] = $application->phone;
            }
        }

        return $profile;
    }
}
