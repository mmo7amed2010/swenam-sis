<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox value: checked="1" or missing (unchecked)
        $this->merge([
            'is_active' => $this->has('is_active') ? (bool) $this->input('is_active') : false,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $programId = $this->route('program') ? $this->route('program')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($programId) {
                    $query = \App\Models\Program::whereRaw('LOWER(name) = ?', [strtolower($value)])
                        ->whereNull('deleted_at');

                    if ($programId) {
                        $query->where('id', '!=', $programId);
                    }

                    if ($query->exists()) {
                        $fail('A program with this name already exists.');
                    }
                },
            ],
            'is_active' => 'required|boolean',
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
            'name.required' => 'Program name is required.',
            'name.unique' => 'A program with this name already exists.',
            'name.max' => 'Program name cannot exceed 100 characters.',
        ];
    }
}
