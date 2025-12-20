{{--
 * Module Modals Component
 *
 * Returns the add modals (lesson, quiz, assignment) for a specific module.
 * Used for dynamically injecting modals after module creation.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
--}}

@props(['context', 'program', 'course', 'module'])

@php
    $isAdmin = $context === 'admin';
@endphp

{{-- Add modals for the module --}}
<x-modals.add-lesson-form :context="$context" :program="$program" :course="$course" :module="$module" />
<x-modals.add-quiz-form :context="$context" :program="$program" :course="$course" :module="$module" />
<x-modals.add-assignment-form :context="$context" :program="$program" :course="$course" :module="$module" />
