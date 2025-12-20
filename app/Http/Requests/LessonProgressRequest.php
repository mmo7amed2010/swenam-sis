<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Lesson Progress Request
 *
 * Validates data for marking a lesson as complete.
 * Used in the student lesson progress tracking system.
 */
class LessonProgressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller to check program enrollment
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * This is currently a simple request with no body parameters,
     * as the lesson ID is passed via route parameter. However,
     * this FormRequest is created for consistency and future extensibility.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // No additional validation needed currently
            // Lesson ID validation is handled by route model binding
        ];
    }
}
