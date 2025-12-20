<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Admin with permissions
        if (($user->isAdmin() )) {
            return true;
        }

        // Instructor assigned to the course
        if ($user->isInstructor()) {
            $course = $this->route('course');
            if ($course && $course->hasInstructor($user)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $assignmentId = $this->route('assignment')?->id;
        $hasPublishedGrades = $assignmentId ? \App\Models\Assignment::find($assignmentId)
            ->submissions()
            ->whereHas('grades', function ($q) {
                $q->where('is_published', true);
            })
            ->exists() : false;

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_points' => 'required|integer|min:1|max:1000',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'submission_type' => 'required|in:file_upload,text_entry,url_submission,multiple',
            'is_published' => 'nullable|boolean',
            'module_id' => 'nullable|exists:course_modules,id',
        ];

        // Story 3.2 AC #4: Cannot change Total Points if grades already published
        if ($hasPublishedGrades && $this->isMethod('PUT') && $assignmentId) {
            $assignment = \App\Models\Assignment::find($assignmentId);
            $currentPoints = $assignment->max_points ?? $assignment->total_points ?? 100;
            $rules['total_points'] = [
                'required',
                'integer',
                'min:1',
                'max:1000',
                Rule::in([$currentPoints]),
            ];
        }

        // Simplified file upload rules - just size limit
        if ($this->input('submission_type') === 'file_upload' || $this->input('submission_type') === 'multiple') {
            $rules['max_file_size_mb'] = 'nullable|integer|min:1|max:50';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Assignment title is required.',
            'total_points.required' => 'Total points is required.',
            'total_points.min' => 'Total points must be at least 1.',
            'total_points.max' => 'Total points cannot exceed 1000.',
            'total_points.in' => 'Cannot change total points when grades have been published.',
            'passing_score.integer' => 'Passing score must be a whole number.',
            'passing_score.min' => 'Passing score cannot be less than 0%.',
            'passing_score.max' => 'Passing score cannot exceed 100%.',
            'submission_type.required' => 'Submission type is required.',
            'max_file_size_mb.max' => 'Maximum file size cannot exceed 50MB.',
            'module_id.exists' => 'Selected module does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Map total_points to max_points for database
        if ($this->has('total_points')) {
            $this->merge([
                'max_points' => $this->total_points,
            ]);
        }

        // Set default values
        if (! $this->has('is_published')) {
            $this->merge(['is_published' => false]);
        }

        // Default passing score to 60% if not provided
        if (! $this->has('passing_score') || $this->input('passing_score') === null) {
            $this->merge(['passing_score' => 60]);
        }

        // Default file size to 10MB for file uploads
        if (! $this->has('max_file_size_mb') && ($this->input('submission_type') === 'file_upload' || $this->input('submission_type') === 'multiple')) {
            $this->merge(['max_file_size_mb' => 10]);
        }
    }
}
