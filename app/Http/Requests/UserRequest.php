<?php

namespace App\Http\Requests;

use App\Rules\ValidationRulesets;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $isUpdate = $this->route('user') !== null;

        return true;
    }

    public function rules(): array
    {
        $userModel = $this->route('user');
        $isUpdate = $userModel !== null;

        // Get the user ID for unique email check
        $userId = null;
        if ($isUpdate) {
            $userId = is_object($userModel) ? $userModel->id : $userModel;
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ValidationRulesets::userEmail($userId),
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_remove' => ['nullable'],

        ];

        // Password rules differ for create vs update
        if ($isUpdate) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['nullable', 'string'];
        } else {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            $rules['password_confirmation'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.max' => 'Avatar must not exceed 2MB.',
        ];
    }
}
