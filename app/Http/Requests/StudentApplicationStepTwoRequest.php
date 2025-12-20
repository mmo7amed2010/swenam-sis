<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentApplicationStepTwoRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'email_confirmation' => ['required', 'same:email'],
            'phone' => ['required', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date', 'before:'.now()->subYears(16)->format('Y-m-d')],
            'country_of_citizenship' => ['required', 'string', 'max:100'],
            'residency_status' => ['required', 'string', 'max:100'],
            'primary_language' => ['required', 'string', 'max:50'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state_province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email_confirmation.same' => 'Email addresses do not match.',
            'date_of_birth.before' => 'You must be at least 16 years old to apply.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Combine date of birth components if they exist separately
        if ($this->has('dob_day') && $this->has('dob_month') && $this->has('dob_year')) {
            $this->merge([
                'date_of_birth' => sprintf('%04d-%02d-%02d', $this->dob_year, $this->dob_month, $this->dob_day),
            ]);
        }
    }
}
