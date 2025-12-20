<?php

namespace App\Http\Requests;

use App\Rules\ValidationRulesets;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        $courseCodeRules = ValidationRulesets::courseCode($courseId);
        // Make course_code optional on update (but allow editing)
        if ($courseId) {
            array_unshift($courseCodeRules, 'sometimes');
        }

        return [
            'course_code' => $courseCodeRules,
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'credits' => 'required|numeric',
            'instructor_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('user_type', 'instructor'),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_code.regex' => 'Course code must be 6-10 uppercase letters and numbers (e.g., CS101, MATH201).',
            'course_code.unique' => 'This course code already exists.',
            'program_id.required' => 'Please select a program for this course.',
            'program_id.exists' => 'The selected program does not exist.',
            'instructor_id.exists' => 'The selected instructor does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Uppercase course code
        if ($this->has('course_code')) {
            $this->merge([
                'course_code' => strtoupper($this->course_code),
            ]);
        }
    }
}
