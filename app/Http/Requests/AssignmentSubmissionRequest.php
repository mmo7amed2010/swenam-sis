<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');

        return $assignment && $assignment->acceptsSubmissions();
    }

    /**
     * Get the validation rules that apply to the request (Story 3.4 AC #2).
     */
    public function rules(): array
    {
        $assignment = $this->route('assignment');
        $rules = [];

        // Validate based on submission_type (Story 3.4 AC #2)
        if ($assignment) {
            $submissionType = $assignment->submission_type;

            if ($submissionType === 'file_upload' || $submissionType === 'multiple') {
                $maxSizeKB = ($assignment->max_file_size_mb ?? 10) * 1024; // Convert MB to KB
                $rules['file'] = [
                    'required_without_all:text_content,external_url',
                    'file',
                    'max:'.$maxSizeKB,
                ];

                // Validate file types using mimes rule
                if ($assignment->allowed_file_types && count($assignment->allowed_file_types) > 0) {
                    $mimes = implode(',', $assignment->allowed_file_types);
                    $rules['file'][] = 'mimes:'.$mimes;
                }
            }

            if ($submissionType === 'text_entry' || $submissionType === 'multiple') {
                $rules['text_content'] = [
                    'required_without_all:file,external_url',
                    'string',
                    'min:10',
                ];
            }

            if ($submissionType === 'url_submission' || $submissionType === 'multiple') {
                $rules['external_url'] = [
                    'required_without_all:file,text_content',
                    'url',
                    'max:500',
                ];
            }

        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $assignment = $this->route('assignment');
        $allowedTypes = $assignment && $assignment->allowed_file_types
            ? implode(', ', array_map('strtoupper', $assignment->allowed_file_types))
            : 'allowed file types';

        return [
            'file.required_without_all' => 'Please upload a file, enter text, or provide a URL.',
            'file.max' => 'The file size must not exceed :max KB.',
            'file.mimes' => "The file must be one of the following types: {$allowedTypes}.",
            'text_content.required_without_all' => 'Please provide text content, upload a file, or provide a URL.',
            'text_content.min' => 'Text content must be at least :min characters.',
            'external_url.required_without_all' => 'Please provide a URL, upload a file, or enter text content.',
            'external_url.url' => 'Please provide a valid URL.',
            'late_acknowledgment.required' => 'You must acknowledge that this submission is late.',
            'late_acknowledgment.accepted' => 'You must acknowledge that this submission is late.',
        ];
    }
}
