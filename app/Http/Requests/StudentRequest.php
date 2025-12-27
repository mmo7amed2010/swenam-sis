<?php

namespace App\Http\Requests;

use App\Rules\ExistsInLms;
use App\Rules\ValidationRulesets;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
        $student = $this->route('student');
        $isUpdate = $student !== null;

        // For email unique check, we need the User ID (not Student ID)
        $userId = null;
        if ($isUpdate) {
            $studentModel = is_object($student) ? $student : \App\Models\Student::find($student);
            $userId = $studentModel?->user_id;
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ValidationRulesets::userEmail($userId),
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'program_id' => ['required', 'integer', new ExistsInLms('programs')],
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
            'program_id.required' => 'Please select a program for the student.',
            'program_id.exists' => 'The selected program is invalid.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'date_of_birth.date' => 'Please provide a valid date of birth.',
        ];
    }
}
