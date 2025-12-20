<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Quiz Answer Request
 *
 * Validates data for saving a student's answer to a quiz question.
 * Used during active quiz attempts.
 */
class QuizAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller to check attempt ownership
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question_id' => [
                'required',
                'exists:quiz_questions,id',
            ],
            'answer' => [
                'required',
                // Answer can be string, number, or array depending on question type
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_id.required' => 'Question ID is required.',
            'question_id.exists' => 'Invalid question ID.',
            'answer.required' => 'An answer is required.',
        ];
    }
}
