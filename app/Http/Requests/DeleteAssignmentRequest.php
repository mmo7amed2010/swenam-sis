<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Delete Assignment Request
 *
 * Validates data required to delete an assignment.
 * Requires typed confirmation if submissions exist.
 */
class DeleteAssignmentRequest extends FormRequest
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
     * Story 3.2 AC #7: Require typed confirmation if submissions exist
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $assignment = $this->route('assignment');
        $submissionCount = $assignment->submissions()->count();

        $rules = [];

        // Require typed confirmation if submissions exist
        if ($submissionCount > 0) {
            $rules['confirmation'] = [
                'required',
                'in:DELETE ASSIGNMENT',
            ];
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirmation.required' => 'You must type "DELETE ASSIGNMENT" to confirm deletion.',
            'confirmation.in' => 'Confirmation text must be exactly "DELETE ASSIGNMENT".',
        ];
    }
}
