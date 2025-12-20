<?php

namespace App\Rules;

use Illuminate\Validation\Rule;

class ValidationRulesets
{
    /**
     * Validation rules for course code.
     *
     * @param  int|null  $ignoreId  Course ID to ignore for uniqueness check
     * @return array<int, mixed>
     */
    public static function courseCode(?int $ignoreId = null): array
    {
        return [
            'required',
            'string',
            'max:20',
            'regex:/^[A-Z0-9]{4,12}$/',
            Rule::unique('courses', 'course_code')->ignore($ignoreId),
        ];
    }

    /**
     * Validation rules for user email.
     *
     * @param  int|null  $ignoreId  User ID to ignore for uniqueness check
     * @return array<int, mixed>
     */
    public static function userEmail(?int $ignoreId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($ignoreId),
        ];
    }

    /**
     * Validation rules for phone number.
     *
     * @return array<int, mixed>
     */
    public static function phoneNumber(): array
    {
        return [
            'required',
            'string',
            'regex:/^[0-9]{10,15}$/',
        ];
    }

    /**
     * Validation rules for student number.
     *
     * @param  int|null  $ignoreId  Student ID to ignore for uniqueness check
     * @return array<int, mixed>
     */
    public static function studentNumber(?int $ignoreId = null): array
    {
        return [
            'required',
            'string',
            'size:10',
            'regex:/^STU[0-9]{7}$/',
            Rule::unique('students', 'student_number')->ignore($ignoreId),
        ];
    }

    /**
     * Validation rules for program code.
     *
     * @param  int|null  $ignoreId  Program ID to ignore for uniqueness check
     * @return array<int, mixed>
     */
    public static function programCode(?int $ignoreId = null): array
    {
        return [
            'required',
            'string',
            'max:10',
            'regex:/^[A-Z]{2,4}$/',
            Rule::unique('programs', 'code')->ignore($ignoreId),
        ];
    }
}
