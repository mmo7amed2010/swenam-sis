<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request (Story 4.1).
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            // HIDDEN_FEATURE: due_date - currently hidden from UI, forced to null
            'due_date' => 'nullable|date|after:now',
            // HIDDEN_FEATURE: time_limit - currently hidden from UI, forced to null
            'time_limit' => 'nullable|integer|min:1|max:480', // Max 8 hours
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            // HIDDEN_FEATURE: Simplified to enabled/disabled toggle (shows after submission when enabled)
            'show_correct_answers' => 'required|in:never,after_each_attempt',
            'passing_score' => 'required|integer|min:0|max:100',
            'published' => 'boolean',
            'assessment_type' => 'required|in:quiz,exam',
            'scope' => 'required|in:lesson,module',
            'module_id' => 'nullable|required_if:scope,module|exists:course_modules,id',
            // HIDDEN_FEATURE: max_attempts - currently hidden from UI, forced to 1
            'max_attempts' => 'nullable|integer|min:-1|max:10',
        ];

        // HIDDEN_FEATURE: Exam-specific rules temporarily disabled (single attempt for all)
        // If it's an exam, enforce specific rules
        // if ($this->input('assessment_type') === 'exam') {
        //     $rules['max_attempts'] = 'required|integer|min:2|max:2'; // Exams always have exactly 2 attempts
        //     $rules['scope'] = 'required|in:module'; // Exams are always module-level
        //     $rules['module_id'] = 'required|exists:course_modules,id'; // Exams must be associated with a module
        // } else {
        //     // Quiz rules (can have unlimited attempts and be lesson-level)
        //     $rules['max_attempts'] = 'required|integer|min:-1|max:10'; // -1 for unlimited
        // }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Quiz/Exam title is required.',
            'due_date.after' => 'Due date must be in the future.',
            'time_limit.max' => 'Time limit cannot exceed 480 minutes (8 hours).',
            'max_attempts.required' => 'Maximum attempts is required.',
            'max_attempts.min' => 'Maximum attempts must be -1 (unlimited) or between 1 and 10.',
            'max_attempts.max' => 'Maximum attempts cannot exceed 10.',
            'show_correct_answers.required' => 'Show correct answers setting is required.',
            'passing_score.required' => 'Passing score is required.',
            'passing_score.min' => 'Passing score must be between 0 and 100.',
            'passing_score.max' => 'Passing score cannot exceed 100.',
            'assessment_type.required' => 'Assessment type is required.',
            'assessment_type.in' => 'Assessment type must be either quiz or exam.',
            'scope.required' => 'Scope is required.',
            'scope.in' => 'Scope must be either lesson or module.',
            'module_id.required_if' => 'Module is required when scope is module-level.',
            'module_id.exists' => 'Selected module does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // =====================================================================
        // HIDDEN_FEATURE: Force values for hidden fields
        // These features are hidden from UI but backend supports them.
        // To re-enable: remove these forced merges and uncomment the UI fields.
        // =====================================================================
        $this->merge([
            'time_limit' => null,      // HIDDEN_FEATURE: Force unlimited time
            'max_attempts' => 1,       // HIDDEN_FEATURE: Force single attempt
            'due_date' => null,        // HIDDEN_FEATURE: Force no due date
        ]);

        // Set default values
        if (! $this->has('shuffle_questions')) {
            $this->merge(['shuffle_questions' => false]);
        }

        if (! $this->has('shuffle_answers')) {
            $this->merge(['shuffle_answers' => false]);
        }

        if (! $this->has('published')) {
            $this->merge(['published' => false]);
        }

        // Set default assessment type to quiz
        if (! $this->has('assessment_type')) {
            $this->merge(['assessment_type' => 'quiz']);
        }

        // Set default scope to lesson for quizzes
        if (! $this->has('scope')) {
            $this->merge(['scope' => 'lesson']);
        }

        // HIDDEN_FEATURE: Convert show_correct_answers toggle to appropriate value
        // When enabled (truthy), show after submission; when disabled, never show
        $showCorrectAnswers = $this->input('show_correct_answers');
        if ($showCorrectAnswers === '1' || $showCorrectAnswers === true || $showCorrectAnswers === 'on') {
            $this->merge(['show_correct_answers' => 'after_each_attempt']);
        } elseif ($showCorrectAnswers === '0' || $showCorrectAnswers === false || ! $showCorrectAnswers) {
            $this->merge(['show_correct_answers' => 'never']);
        }
        // If already 'never' or 'after_each_attempt', keep as is (for backwards compatibility)

        // HIDDEN_FEATURE: max_attempts logic disabled (forced to 1 above)
        // Convert max_attempts to -1 if "unlimited" is selected
        // if ($this->input('max_attempts') === 'unlimited' || $this->input('max_attempts') === '-1') {
        //     $this->merge(['max_attempts' => -1]);
        // }

        // HIDDEN_FEATURE: Exam-specific attempts logic disabled (forced to 1 above)
        // Enforce exam-specific rules
        // if ($this->input('assessment_type') === 'exam') {
        //     // Exams always have exactly 2 attempts
        //     $this->merge(['max_attempts' => 2]);
        //     // Exams are always module-level
        //     $this->merge(['scope' => 'module']);
        // }
    }
}
