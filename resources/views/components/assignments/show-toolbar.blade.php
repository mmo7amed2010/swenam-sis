{{--
 * Assignment Show - Toolbar Component
 *
 * Displays toolbar with title, status badge, view submissions, edit/delete buttons.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['assignment', 'program', 'course', 'context'])

@php
    $isAdmin = $context === 'admin';

    $submissionsRoute = $isAdmin
        ? route('admin.programs.courses.assignments.submissions', [$program, $course, $assignment])
        : route('instructor.courses.assignments.submissions', [$program, $course, $assignment]);
@endphp

<!--begin::Toolbar-->
<div class="d-flex flex-wrap flex-stack pb-7">
    <div class="d-flex flex-wrap align-items-center my-1">
        <h3 class="fw-bold me-5 my-1">{{ $assignment->title }}</h3>
        @if($assignment->is_published)
        <span class="badge badge-light-success fs-7 fw-bold my-1">{{ __('Published') }}</span>
        @else
        <span class="badge badge-light-secondary fs-7 fw-bold my-1">{{ __('Draft') }}</span>
        @endif
    </div>
    <div class="d-flex my-1">
        <a href="{{ $submissionsRoute }}" class="btn btn-sm btn-info me-3">
            {!! getIcon('file-down', 'fs-2') !!}
            {{ __('View Submissions') }}
            @if($assignment->submissions->count() > 0)
                <span class="badge badge-light-primary ms-2">{{ $assignment->submissions->count() }}</span>
            @endif
        </a>
        <button type="button" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_assignment_{{ $assignment->id }}">
            {!! getIcon('pencil', 'fs-6 me-1') !!}
            {{ __('Edit Assignment') }}
        </button>
        <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_assignment">
            {!! getIcon('trash', 'fs-2') !!}
            {{ __('Delete') }}
        </button>
    </div>
</div>
<!--end::Toolbar-->
