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
}
