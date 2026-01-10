<?php

namespace App\Rules;

use App\Services\LmsApiService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that an email does not exist in LMS (or belongs to the ignored user on update).
 *
 * This rule checks if an email already exists in the LMS system.
 * - On create: Fails if email exists for any user in LMS
 * - On update: Fails if email exists for a different user (allows same email for same user)
 */
class EmailNotInLms implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  int|null  $ignoreLmsUserId  LMS user ID to ignore (for update operations)
     */
    public function __construct(
        protected ?int $ignoreLmsUserId = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lmsApiService = app(LmsApiService::class);
        $result = $lmsApiService->checkEmailExists($value);

        // If email doesn't exist in LMS, validation passes
        if (! $result['exists']) {
            return;
        }

        // If email exists and we're on update, check if it belongs to the same user
        if ($this->ignoreLmsUserId !== null) {
            $foundUserId = $result['user_id'];

            // Allow if it's the same user (allows same email on update)
            if ($foundUserId == $this->ignoreLmsUserId) {
                return;
            }

            // Fail if email belongs to a different user
            $fail('This email is already registered in the LMS system by another user.');
            return;
        }

        // On create, fail if email exists for any user
        $fail('This email is already registered in the LMS system.');
    }
}

