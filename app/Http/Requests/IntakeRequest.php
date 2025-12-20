<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class IntakeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt for AJAX requests.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->ajax() || $this->wantsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $intakeId = $this->route('intake')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('intakes', 'slug')->ignore($intakeId),
            ],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('intake name'),
            'slug' => __('slug'),
            'is_active' => __('active status'),
            'description' => __('description'),
            'sort_order' => __('sort order'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
