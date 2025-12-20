{{--
 * Assignment Submissions - Filters Component
 *
 * Displays filter buttons for submissions list.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string $filter
 * @param string $sort
 * @param string $dir
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['filter', 'sort', 'dir', 'program', 'course', 'assignment', 'context'])

@php
    $isAdmin = $context === 'admin';

    $baseRoute = $isAdmin
        ? 'admin.programs.courses.assignments.submissions'
        : 'instructor.courses.assignments.submissions';

    $routeParams = [$program, $course, $assignment];
@endphp

<!--begin::Filters-->
<x-cards.section class="mb-5 mb-xl-10">
    <div class="d-flex flex-wrap gap-3 align-items-center">
        <span class="fw-bold text-gray-700">{{ __('Filter:') }}</span>
        <a href="{{ route($baseRoute, array_merge($routeParams, ['filter' => 'all', 'sort' => $sort, 'dir' => $dir])) }}"
           class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-light' }}">
            {{ __('All') }}
        </a>
        <a href="{{ route($baseRoute, array_merge($routeParams, ['filter' => 'ungraded', 'sort' => $sort, 'dir' => $dir])) }}"
           class="btn btn-sm {{ $filter === 'ungraded' ? 'btn-primary' : 'btn-light' }}">
            {{ __('Ungraded') }}
        </a>
        <a href="{{ route($baseRoute, array_merge($routeParams, ['filter' => 'graded', 'sort' => $sort, 'dir' => $dir])) }}"
           class="btn btn-sm {{ $filter === 'graded' ? 'btn-primary' : 'btn-light' }}">
            {{ __('Graded') }}
        </a>
    </div>
</x-cards.section>
<!--end::Filters-->
