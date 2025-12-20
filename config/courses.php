<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Course Departments
    |--------------------------------------------------------------------------
    |
    | Available departments for course categorization.
    | Can be managed in the admin panel or database later.
    |
    */
    'departments' => [
        'Computer Science',
        'Mathematics',
        'Physics',
        'Chemistry',
        'Biology',
        'Engineering',
        'Business Administration',
        'Economics',
        'Psychology',
        'Sociology',
        'History',
        'Literature',
        'Philosophy',
        'Art & Design',
        'Music',
    ],

    /*
    |--------------------------------------------------------------------------
    | Course Programs
    |--------------------------------------------------------------------------
    |
    | Course programs for grouping and filtering.
    |
    */
    'programs' => [
        'Undergraduate',
        'Graduate',
        'Certificate',
        'Diploma',
        'Professional Development',
        'Online Program',
        'Hybrid Program',
        'Executive Education',
    ],

    /*
    |--------------------------------------------------------------------------
    | Difficulty Levels
    |--------------------------------------------------------------------------
    |
    | Course difficulty levels.
    |
    */
    'difficulty_levels' => [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
    ],

    /*
    |--------------------------------------------------------------------------
    | Course Code Pattern
    |--------------------------------------------------------------------------
    |
    | Regex pattern for validating course codes (e.g., CS101, MATH205).
    |
    */
    'course_code_pattern' => '/^[A-Za-z]{2,4}\d{3,4}$/',

    /*
    |--------------------------------------------------------------------------
    | Maximum Values
    |--------------------------------------------------------------------------
    */
    'max_name_length' => 200,
    'max_description_length' => 1000,
    'max_enrollment' => 500,
];
