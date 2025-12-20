{{--
 * Assignment Grade - Navigation Component
 *
 * Displays navigation buttons for grading workflow (previous/next/back).
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string|null $previousSubmissionId
 * @param string|null $nextSubmissionId
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['previousSubmissionId', 'nextSubmissionId', 'program', 'course', 'assignment', 'context'])

@php
    $isAdmin = $context === 'admin';

    $previousRoute = $previousSubmissionId
        ? ($isAdmin
            ? route('admin.programs.courses.assignments.grade', [$program, $course, $assignment, $previousSubmissionId])
            : route('instructor.courses.assignments.grade', [$program, $course, $assignment, $previousSubmissionId]))
        : null;

    $nextRoute = $nextSubmissionId
        ? ($isAdmin
            ? route('admin.programs.courses.assignments.grade', [$program, $course, $assignment, $nextSubmissionId])
            : route('instructor.courses.assignments.grade', [$program, $course, $assignment, $nextSubmissionId]))
        : null;

    $backRoute = $isAdmin
        ? route('admin.programs.courses.assignments.submissions', [$program, $course, $assignment])
        : route('instructor.courses.assignments.submissions', [$program, $course, $assignment]);
@endphp

<div class="d-flex align-items-center my-1">
    <!--begin::Navigation-->
    @if($previousSubmissionId)
    <a href="{{ $previousRoute }}" class="btn btn-sm btn-light me-3">
        <i class="ki-duotone ki-arrow-left fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('Previous') }}
    </a>
    @endif
    @if($nextSubmissionId)
    <a href="{{ $nextRoute }}" class="btn btn-sm btn-light me-3">
        {{ __('Next') }}
        <i class="ki-duotone ki-arrow-right fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </a>
    @endif
    <a href="{{ $backRoute }}" class="btn btn-sm btn-light">
        <i class="ki-duotone ki-arrow-left fs-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('Back to Submissions') }}
    </a>
    <!--end::Navigation-->
</div>
