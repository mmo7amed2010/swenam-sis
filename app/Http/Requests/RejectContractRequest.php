<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Please provide a reason for rejecting the contract.',
            'rejection_reason.min' => 'The rejection reason must be at least 10 characters.',
        ];
    }
}
