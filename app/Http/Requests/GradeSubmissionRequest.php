<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request (Story 3.7).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request (Story 3.7 AC #2, #7).
     */
    public function rules(): array
    {
        $submission = $this->route('submission');
        $assignment = $submission ? $submission->assignment : null;
        $maxPoints = $assignment ? ($assignment->total_points ?? $assignment->max_points ?? 100) : 100;

        return [
            'points_awarded' => [
                'required',
                'numeric',
                'min:0',
                'max:'.$maxPoints,
                'regex:/^\d+(\.\d{1,2})?$/', // Allow up to 2 decimal places (Story 3.7 AC #7)
            ],
            'feedback' => 'nullable|string',
            'late_penalty_override' => 'nullable|numeric|min:0|max:100',
            'rubric_scores' => 'nullable|array',
            'action' => 'required|in:draft,publish', // Story 3.7 AC #4, #5
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'points_awarded.required' => 'Points awarded is required.',
            'points_awarded.numeric' => 'Points awarded must be a number.',
            'points_awarded.min' => 'Points awarded cannot be negative.',
            'points_awarded.max' => 'Points awarded cannot exceed the maximum points.',
            'points_awarded.regex' => 'Points awarded can have up to 2 decimal places.',
            'late_penalty_override.numeric' => 'Late penalty override must be a number.',
            'late_penalty_override.min' => 'Late penalty override cannot be negative.',
            'late_penalty_override.max' => 'Late penalty override cannot exceed 100%.',
            'action.required' => 'You must choose to save as draft or publish.',
            'action.in' => 'Invalid action. Must be either draft or publish.',
        ];
    }
}
