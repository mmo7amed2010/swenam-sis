<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];

        // Require payment amount for self-funded students
        if ($this->route('application')?->isSelfFunded()) {
            $rules['payment_amount'] = ['required', 'numeric', 'min:0.01', 'max:999999.99'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'payment_amount.required' => 'Please enter the payment amount for this self-funded student.',
            'payment_amount.min' => 'The payment amount must be greater than zero.',
        ];
    }
}
