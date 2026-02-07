<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadSignedContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signed_contract' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'signed_contract.required' => 'Please upload the signed contract.',
            'signed_contract.mimes' => 'The signed contract must be a PDF file.',
            'signed_contract.max' => 'The signed contract must not exceed 10MB.',
        ];
    }
}
