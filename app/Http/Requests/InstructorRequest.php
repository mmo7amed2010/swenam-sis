<?php

namespace App\Http\Requests;

use App\Rules\ValidationRulesets;
use Illuminate\Foundation\Http\FormRequest;

class InstructorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by controller/middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $instructor = $this->route('instructor');
        $isUpdate = $instructor !== null;

        // Get the instructor ID for unique email check (Instructor extends User, so same table)
        $instructorId = null;
        if ($isUpdate) {
            $instructorId = is_object($instructor) ? $instructor->id : $instructor;
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ValidationRulesets::userEmail($instructorId),
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
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
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
