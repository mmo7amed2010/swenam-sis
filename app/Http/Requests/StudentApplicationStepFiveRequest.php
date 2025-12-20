<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentApplicationStepFiveRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'degree_certificate' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'transcripts' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'cv' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'english_test' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'degree_certificate.required' => 'Please upload your degree certificate or latest educational credentials.',
            'degree_certificate.max' => 'Degree certificate file size must not exceed 10MB.',
            'degree_certificate.mimes' => 'Degree certificate must be a PDF, JPG, PNG, or DOCX file.',

            'transcripts.required' => 'Please upload your transcripts.',
            'transcripts.max' => 'Transcripts file size must not exceed 10MB.',
            'transcripts.mimes' => 'Transcripts must be a PDF, JPG, PNG, or DOCX file.',

            'cv.required' => 'Please upload your CV/Resume.',
            'cv.max' => 'CV file size must not exceed 10MB.',
            'cv.mimes' => 'CV must be a PDF, JPG, PNG, or DOCX file.',

            'english_test.max' => 'English test file size must not exceed 10MB.',
            'english_test.mimes' => 'English test results must be a PDF, JPG, PNG, or DOCX file.',
        ];
    }
}
