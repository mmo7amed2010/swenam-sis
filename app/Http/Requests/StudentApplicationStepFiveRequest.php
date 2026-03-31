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
        $isGovernmentFunded = session('application.funding_type') === 'government_funded';

        $rules = [
            'government_id' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'degree_certificate' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'transcripts' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'cv' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],
            'english_test' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx'],

            // Declarations & Acknowledgments
            'ack_information_declaration' => ['required', 'accepted'],
            'ack_contact_responsibility' => ['required', 'accepted'],
            'ack_non_attendance' => ['required', 'accepted'],
        ];

        if ($isGovernmentFunded) {
            $rules['ack_gov_funding_loan'] = ['required', 'accepted'];
            $rules['ack_gov_academic_engagement'] = ['required', 'accepted'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'government_id.required' => 'Please upload your government-issued photo ID.',
            'government_id.max' => 'Government ID file size must not exceed 10MB.',
            'government_id.mimes' => 'Government ID must be a PDF, JPG, or PNG file.',

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

            'ack_information_declaration.required' => 'You must acknowledge the applicant information declaration.',
            'ack_information_declaration.accepted' => 'You must acknowledge the applicant information declaration.',
            'ack_gov_funding_loan.required' => 'You must acknowledge the government funding loan terms.',
            'ack_gov_funding_loan.accepted' => 'You must acknowledge the government funding loan terms.',
            'ack_gov_academic_engagement.required' => 'You must acknowledge the academic engagement requirement.',
            'ack_gov_academic_engagement.accepted' => 'You must acknowledge the academic engagement requirement.',
            'ack_contact_responsibility.required' => 'You must acknowledge the contact information responsibility.',
            'ack_contact_responsibility.accepted' => 'You must acknowledge the contact information responsibility.',
            'ack_non_attendance.required' => 'You must acknowledge the non-attendance/withdrawal policy.',
            'ack_non_attendance.accepted' => 'You must acknowledge the non-attendance/withdrawal policy.',
        ];
    }
}
