<?php

namespace App\Http\Requests;

use App\Rules\ExistsInLms;
use Illuminate\Foundation\Http\FormRequest;

class StudentApplicationStepOneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Guest-accessible, no authorization needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Programs and intakes are validated against LMS API (master system).
     */
    public function rules(): array
    {
        return [
            'program_id' => ['required', 'integer', new ExistsInLms('programs')],
            'intake_id' => ['required', 'integer', new ExistsInLms('intakes')],
            'has_referral' => ['required', 'boolean'],
            'referral_agency_name' => ['required_if:has_referral,1,true', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'program_id.required' => 'Please select a program.',
            'program_id.exists' => 'The selected program is invalid.',
            'intake_id.required' => 'Please select your preferred intake.',
            'intake_id.exists' => 'The selected intake is no longer available.',
            'has_referral.required' => 'Please indicate whether you were referred by an agency.',
            'referral_agency_name.required_if' => 'Please provide the name of the referring agency.',
            'referral_agency_name.max' => 'Agency name cannot exceed 255 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert has_referral to boolean
        $this->merge([
            'has_referral' => filter_var($this->has_referral, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }
}
