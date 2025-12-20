{{--
 * Assignment Show - Course Info Component
 *
 * Displays course information sidebar.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['program', 'course', 'context'])

@php
    $isAdmin = $context === 'admin';

    $courseRoute = $isAdmin
        ? route('admin.programs.courses.show', [$program, $course])
        : route('instructor.courses.show', [$program, $course]);
@endphp

<x-cards.section :title="__('Course Information')">
    <div class="d-flex flex-column gap-5">
        <div>
            <div class="text-gray-600 mb-2">{{ __('Course Code') }}</div>
            <div class="text-gray-900 fw-bold">{{ $course->course_code }}</div>
        </div>
        <div class="separator"></div>
        <div>
            <div class="text-gray-600 mb-2">{{ __('Course Name') }}</div>
            <div class="text-gray-900 fw-bold">{{ $course->name }}</div>
        </div>
        <div class="separator"></div>
        <a href="{{ $courseRoute }}" class="btn btn-light-primary w-100">
            {{ __('View Course') }}
        </a>
    </div>
</x-cards.section>
