{{--
 * Assignment Submissions - Toolbar Component
 *
 * Displays toolbar with title, back button, export CSV, and download ZIP.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $filter
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['assignment', 'program', 'course', 'filter', 'context'])

@php
    $isAdmin = $context === 'admin';

    $backRoute = $isAdmin
        ? route('admin.programs.courses.assignments.show', [$program, $course, $assignment])
        : route('instructor.courses.assignments.show', [$program, $course, $assignment]);

    $exportRoute = $isAdmin
        ? route('admin.programs.courses.assignments.submissions.export', [$program, $course, $assignment, 'filter' => $filter])
        : route('instructor.courses.assignments.export', [$program, $course, $assignment, 'filter' => $filter]);

    $downloadZipRoute = $isAdmin
        ? route('admin.programs.courses.assignments.submissions.download-zip', [$program, $course, $assignment])
        : route('instructor.courses.assignments.download-zip', [$program, $course, $assignment]);
@endphp

<!--begin::Toolbar-->
<div class="d-flex flex-wrap flex-stack pb-7">
    <div class="d-flex flex-wrap align-items-center my-1">
        <h3 class="fw-bold me-5 my-1">{{ __('Submissions') }}</h3>
        <span class="text-muted fs-6">{{ $assignment->title }}</span>
    </div>
    <div class="d-flex align-items-center my-1">
        <a href="{{ $backRoute }}" class="btn btn-sm btn-light me-3">
            <i class="ki-duotone ki-arrow-left fs-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ __('Back to Assignment') }}
        </a>

    </div>
</div>
<!--end::Toolbar-->
