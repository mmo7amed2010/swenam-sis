<?php

namespace App\Services;

use App\Models\User;

class LmsAdmissionTypeResolver
{
    /**
     * Determine the LMS admission_type for a SIS user.
     *
     * Truth table:
     *   is_suspended = true                               → null
     *   is_suspended = false AND application = 'approved'  → 'approved'
     *   is_suspended = false AND bypass_application = true → 'bypass'
     *   is_suspended = false AND neither                   → null
     */
    public static function resolve(User $user): ?string
    {
        if ($user->isSuspended()) {
            return null;
        }

        $student = $user->student;
        if ($student) {
            $application = $student->studentApplication;
            if ($application && $application->status === 'approved') {
                return 'approved';
            }
        }

        if ($user->bypass_application) {
            return 'bypass';
        }

        return null;
    }

    /**
     * Get the LMS status and reason for a SIS user.
     *
     * @return array{active: bool, reason: string, admission_type: ?string}
     */
    public static function status(User $user): array
    {
        if (! $user->lms_user_id) {
            return [
                'active' => false,
                'reason' => 'No LMS account',
                'admission_type' => null,
            ];
        }

        if ($user->isSuspended()) {
            return [
                'active' => false,
                'reason' => 'Suspended',
                'admission_type' => null,
            ];
        }

        $student = $user->student;
        if ($student) {
            $application = $student->studentApplication;
            if ($application) {
                if ($application->status === 'approved') {
                    return [
                        'active' => true,
                        'reason' => 'Approved application',
                        'admission_type' => 'approved',
                    ];
                }

                return [
                    'active' => false,
                    'reason' => 'Application ' . $application->status,
                    'admission_type' => null,
                ];
            }
        }

        if ($user->bypass_application) {
            return [
                'active' => true,
                'reason' => 'Bypass',
                'admission_type' => 'bypass',
            ];
        }

        return [
            'active' => false,
            'reason' => 'No approved application or bypass',
            'admission_type' => null,
        ];
    }
}
