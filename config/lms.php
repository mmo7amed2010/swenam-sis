<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LMS API Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for communicating with the Learning Management System API.
    | SIS uses these settings to sync data and enable SSO for students.
    |
    */

    'api_url' => env('LMS_API_URL', 'http://lms.test'),
    'api_key' => env('LMS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Session Timeout Configuration
    |--------------------------------------------------------------------------
    |
    | Role-based session timeout values in minutes. These values determine
    | how long a user's session remains active before automatic logout.
    |
    */

    'session' => [
        'student_timeout' => env('LMS_STUDENT_SESSION_TIMEOUT', 30),
        'instructor_timeout' => env('LMS_INSTRUCTOR_SESSION_TIMEOUT', 120),
        'admin_timeout' => env('LMS_ADMIN_SESSION_TIMEOUT', 240),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings including login attempt limits and lockout durations.
    | Admin accounts have stricter limits for enhanced security.
    |
    */

    'security' => [
        'admin_max_attempts' => env('LMS_ADMIN_MAX_ATTEMPTS', 3),
        'default_max_attempts' => env('LMS_DEFAULT_MAX_ATTEMPTS', 5),
        'admin_lockout_minutes' => env('LMS_ADMIN_LOCKOUT_MINUTES', 30),
        'default_lockout_minutes' => env('LMS_DEFAULT_LOCKOUT_MINUTES', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Grading System Configuration
    |--------------------------------------------------------------------------
    |
    | Grading scale, grade point mappings, and letter grade thresholds.
    | Uses standard 4.0 GPA scale with percentage-based mapping.
    |
    */

    'grading' => [
        'scale' => '4.0',
        'grade_points' => [
            93 => 4.0,
            90 => 3.7,
            87 => 3.3,
            83 => 3.0,
            80 => 2.7,
            77 => 2.3,
            73 => 2.0,
            70 => 1.7,
            67 => 1.3,
            63 => 1.0,
            60 => 0.7,
            0 => 0.0,
        ],
        'letter_grades' => [
            93 => 'A',
            90 => 'A-',
            87 => 'B+',
            83 => 'B',
            80 => 'B-',
            77 => 'C+',
            73 => 'C',
            70 => 'C-',
            67 => 'D+',
            63 => 'D',
            60 => 'D-',
            0 => 'F',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache TTL (time-to-live) values in seconds for various data types.
    |
    */

    'cache' => [
        'progress_ttl' => env('LMS_PROGRESS_CACHE_TTL', 900), // 15 minutes
    ],

];
