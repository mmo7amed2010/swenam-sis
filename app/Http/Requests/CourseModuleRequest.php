<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Course Module Request
 *
 * Validates data for creating and updating course modules.
 * Handles both create and update scenarios.
 */
class CourseModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Admins with 'manage courses' permission are always authorized
        if ($user->isAdmin()) {
            return true;
        }

        // Instructors can manage modules for courses they are assigned to
        if ($user->isInstructor()) {
            $course = $this->route('course');
            if ($course) {
                return $course->instructors()
                    ->where('user_id', $user->id)
                    ->whereNull('removed_at')
                    ->exists();
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'nullable',
                'in:draft,published',
            ],
            'release_date' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'date',
            ],
            'requires_exam_pass' => [
                'nullable',
                'boolean',
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
            'title.required' => 'Module title is required.',
            'title.max' => 'Module title cannot exceed 255 characters.',
            'status.in' => 'Status must be either "draft" or "published".',
            'release_date.date' => 'Release date must be a valid date.',
        ];
    }
}
