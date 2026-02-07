<?php

namespace App\Http\Requests;

use App\Rules\ExistsInLms;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContractTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'integer', new ExistsInLms('programs')],
            'body' => ['required', 'string', 'min:100'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a template name.',
            'program_id.required' => 'Please select a program.',
            'body.required' => 'Please provide the contract body.',
            'body.min' => 'The contract body must be at least 100 characters.',
        ];
    }
}
