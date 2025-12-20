<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Reject Application Request
 *
 * Validates data required to reject a student application.
 * Ensures a meaningful rejection reason is provided.
 */
class RejectApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rejection_reason' => [
                'required',
                'string',
                'min:20',
                'max:1000',
            ],
            'admin_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'A rejection reason is required.',
            'rejection_reason.min' => 'Rejection reason must be at least 20 characters to provide meaningful feedback.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 1000 characters.',
            'admin_notes.max' => 'Admin notes cannot exceed 1000 characters.',
        ];
    }
}
