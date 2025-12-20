<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentApplicationStepThreeRequest extends FormRequest
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
            'highest_education_level' => ['required', 'string', 'max:255'],
            'education_field' => ['required', 'string', 'max:255'],
            'institution_name' => ['required', 'string', 'max:255'],
            'education_completed' => ['required', 'in:yes,no,still_studying'],
            'education_country' => ['required', 'string', 'max:100'],
            'has_disciplinary_action' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'highest_education_level.required' => 'Please select your highest level of education.',
            'education_field.required' => 'Please specify your field of education.',
            'institution_name.required' => 'Please provide the name of your institution.',
            'education_completed.required' => 'Please indicate if you completed this education.',
            'education_country.required' => 'Please select the country where you obtained your education.',
            'has_disciplinary_action.required' => 'Please answer the disciplinary action question.',
        ];
    }
}
