<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class CurrentPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! auth()->check()) {
            $fail('You must be authenticated to change your password.');

            return;
        }

        if (! Hash::check($value, auth()->user()->password)) {
            $fail('The current password is incorrect.');
        }
    }
}
