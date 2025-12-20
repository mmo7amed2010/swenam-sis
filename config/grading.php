<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grading Scale Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the grading scale thresholds used throughout the LMS.
    | Grades are calculated based on percentage scores, and letter grades are
    | assigned according to these thresholds.
    |
    | Format: 'Letter Grade' => minimum_percentage_required
    |
    */

    'scale' => [
        'A' => env('GRADE_A_THRESHOLD', 93),
        'A-' => env('GRADE_A_MINUS_THRESHOLD', 90),
        'B+' => env('GRADE_B_PLUS_THRESHOLD', 87),
        'B' => env('GRADE_B_THRESHOLD', 83),
        'B-' => env('GRADE_B_MINUS_THRESHOLD', 80),
        'C+' => env('GRADE_C_PLUS_THRESHOLD', 77),
        'C' => env('GRADE_C_THRESHOLD', 73),
        'C-' => env('GRADE_C_MINUS_THRESHOLD', 70),
        'D+' => env('GRADE_D_PLUS_THRESHOLD', 67),
        'D' => env('GRADE_D_THRESHOLD', 63),
        'D-' => env('GRADE_D_MINUS_THRESHOLD', 60),
        'F' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Grade Point Scale (4.0 system)
    |--------------------------------------------------------------------------
    |
    | Maps letter grades to grade points for GPA calculation.
    |
    */

    'grade_points' => [
        'A' => 4.0,
        'A-' => 3.7,
        'B+' => 3.3,
        'B' => 3.0,
        'B-' => 2.7,
        'C+' => 2.3,
        'C' => 2.0,
        'C-' => 1.7,
        'D+' => 1.3,
        'D' => 1.0,
        'D-' => 0.7,
        'F' => 0.0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Passing Grade Threshold
    |--------------------------------------------------------------------------
    |
    | Minimum letter grade required to pass a course.
    |
    */

    'passing_grade' => env('PASSING_GRADE', 'D-'),
];
