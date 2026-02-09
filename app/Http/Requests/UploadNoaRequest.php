<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadNoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->student?->studentApplication?->canUploadNoa() ?? false;
    }

    public function rules(): array
    {
        return [
            'noa_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'noa_document.required' => 'Please upload a NOA document.',
            'noa_document.mimes' => 'The NOA document must be a PDF, JPG, or PNG file.',
            'noa_document.max' => 'The NOA document must not exceed 10MB.',
        ];
    }
}
