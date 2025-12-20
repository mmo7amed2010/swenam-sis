<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRequirements implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Minimum 8 characters
        if (strlen($value) < 8) {
            $fail('Password must be at least 8 characters long.');

            return;
        }

        // Must contain at least one number
        if (! preg_match('/[0-9]/', $value)) {
            $fail('Password must contain at least one number.');

            return;
        }

        // Must contain uppercase letter
        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Password must contain at least one uppercase letter.');

            return;
        }

        // Must contain lowercase letter
        if (! preg_match('/[a-z]/', $value)) {
            $fail('Password must contain at least one lowercase letter.');

            return;
        }

        // Must contain at least one special character
        if (! preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $value)) {
            $fail('Password must contain at least one special character.');

            return;
        }

        // Check for common weak passwords
        $commonPasswords = [
            'password', 'Password1', 'Password1!', '12345678', 'Aa123456',
            'Admin123', 'Admin123!', 'Welcome1', 'Welcome1!', 'Qwerty123!',
        ];

        if (in_array($value, $commonPasswords)) {
            $fail('This password is too common. Please choose a stronger password.');

            return;
        }
    }
}
