<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request (Story 4.2 - MCQ and True/False only).
     */
    public function rules(): array
    {
        $rules = [
            'question_type' => 'required|in:mcq,true_false',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
        ];

        // MCQ specific rules
        if ($this->input('question_type') === 'mcq') {
            $rules['answers'] = 'required|array|min:2|max:6';
            $rules['answers.*.text'] = 'required|string|max:500';
            $rules['answers.*.is_correct'] = 'boolean';

            // At least one correct answer required
            $rules['has_correct_answer'] = 'required|accepted';
        }

        // True/False specific rules
        if ($this->input('question_type') === 'true_false') {
            $rules['correct_answer'] = 'required|boolean';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question_type.required' => 'Question type is required.',
            'question_type.in' => 'Question type must be Multiple Choice or True/False.',
            'question_text.required' => 'Question text is required.',
            'points.required' => 'Points is required.',
            'points.min' => 'Points must be at least 1.',
            'points.max' => 'Points cannot exceed 100.',
            'answers.required' => 'At least 2 answer options are required for multiple choice questions.',
            'answers.min' => 'At least 2 answer options are required.',
            'answers.max' => 'Maximum 6 answer options allowed.',
            'answers.*.text.required' => 'Answer option text is required.',
            'has_correct_answer.accepted' => 'At least one answer must be marked as correct.',
            'correct_answer.required' => 'Correct answer is required for True/False questions.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // For MCQ, normalize is_correct values and check if at least one answer is correct
        if ($this->input('question_type') === 'mcq' && $this->has('answers')) {
            $answers = $this->input('answers', []);

            // Normalize is_correct to boolean (handles "0", "1", 0, 1, true, false)
            $normalizedAnswers = array_map(function ($answer) {
                $answer['is_correct'] = filter_var($answer['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN);

                return $answer;
            }, $answers);

            $this->merge(['answers' => $normalizedAnswers]);

            // Check if at least one answer is correct
            $hasCorrect = collect($normalizedAnswers)
                ->contains(fn ($answer) => ($answer['is_correct'] ?? false) === true);

            $this->merge(['has_correct_answer' => $hasCorrect]);
        }
    }
}
