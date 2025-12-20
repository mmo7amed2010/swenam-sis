<?php

namespace App\Http\Requests;

use App\Rules\ValidationRulesets;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisteredUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ValidationRulesets::userEmail(),
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
