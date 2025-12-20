<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentApplicationStepFourRequest extends FormRequest
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
        $rules = [
            'has_work_experience' => ['required', 'boolean'],
        ];

        // Only validate work history fields if user has work experience
        if ($this->input('has_work_experience')) {
            $rules['position_level'] = ['required', 'string', 'max:100'];
            $rules['position_title'] = ['required', 'string', 'max:255'];
            $rules['organization_name'] = ['required', 'string', 'max:255'];
            $rules['work_start_date'] = ['required', 'date'];
            $rules['work_end_date'] = ['nullable', 'date', 'after:work_start_date'];
            $rules['years_of_experience'] = ['required', 'string', 'max:50'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'has_work_experience.required' => 'Please indicate if you have work experience.',
            'position_level.required' => 'Please select your position level.',
            'position_title.required' => 'Please provide your position title.',
            'organization_name.required' => 'Please provide the organization name.',
            'work_start_date.required' => 'Please provide the start date.',
            'work_end_date.after' => 'End date must be after start date.',
            'years_of_experience.required' => 'Please select your years of experience.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Combine work start date components if they exist separately
        if ($this->has('work_start_day') && $this->has('work_start_month') && $this->has('work_start_year')) {
            $this->merge([
                'work_start_date' => sprintf('%04d-%02d-%02d', $this->work_start_year, $this->work_start_month, $this->work_start_day),
            ]);
        }

        // Combine work end date components if they exist separately
        if ($this->has('work_end_day') && $this->has('work_end_month') && $this->has('work_end_year')) {
            $this->merge([
                'work_end_date' => sprintf('%04d-%02d-%02d', $this->work_end_year, $this->work_end_month, $this->work_end_day),
            ]);
        }
    }
}
