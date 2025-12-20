<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'min:2',
                'max:4',
                'regex:/^[A-Z]{2,4}$/',
                Rule::unique('departments', 'code')->ignore($departmentId)->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.regex' => 'The code must be 2-4 uppercase letters only (e.g., CS, MATH, ENG).',
            'code.unique' => 'This department code is already in use.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }

        // Set default value for active if not provided
        if (! $this->has('active')) {
            $this->merge(['active' => true]);
        }
    }
}
