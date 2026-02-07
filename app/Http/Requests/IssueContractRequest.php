<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_template_id' => ['required', 'exists:contract_templates,id'],
            'admin_fields' => ['nullable', 'array'],
            'admin_fields.*' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'contract_template_id.required' => 'Please select a contract template.',
            'contract_template_id.exists' => 'The selected template is invalid.',
        ];
    }
}
