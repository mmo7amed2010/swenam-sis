{{--
 * Content Stats Header Component
 *
 * Displays summary statistics for course modules, lessons, quizzes, and assignments.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \Illuminate\Support\Collection $modules
 * @param array $contentTotals - Array with keys: lessons, quizzes, assignments
--}}

@props(['context', 'program', 'course', 'modules', 'contentTotals'])

@php
    $isAdmin = $context === 'admin';
    $totalLessons = $contentTotals['lessons'] ?? 0;
    $totalQuizzes = $contentTotals['quizzes'] ?? 0;
    $totalAssignments = $contentTotals['assignments'] ?? 0;
@endphp

<!--begin::Summary Stats Header-->
<div class="d-flex flex-wrap align-items-center gap-4 mb-5 p-4 bg-light rounded-3">
    <div class="d-flex align-items-center">
        <div class="symbol symbol-40px me-3">
            <span class="symbol-label bg-primary">
                {!! getIcon('book-square', 'fs-4 text-white') !!}
            </span>
        </div>
        <div>
            <div class="fs-2 fw-bold text-gray-900">
                <span id="totalModulesCount">{{ $modules->count() }}</span>
            </div>
            <div class="text-gray-500 fs-8">{{ __('Modules') }}</div>
        </div>
    </div>
    <div class="border-start ps-4">
        <div class="fs-4 fw-bold text-primary">
            <span id="totalLessonsCount">{{ $totalLessons }}</span>
        </div>
        <div class="text-gray-500 fs-8">{{ __('Lessons') }}</div>
    </div>
    <div class="border-start ps-4">
        <div class="fs-4 fw-bold text-info">
            <span id="totalQuizzesCount">{{ $totalQuizzes }}</span>
        </div>
        <div class="text-gray-500 fs-8">{{ __('Quizzes') }}</div>
    </div>
    <div class="border-start ps-4">
        <div class="fs-4 fw-bold text-success">
            <span id="totalAssignmentsCount">{{ $totalAssignments }}</span>
        </div>
        <div class="text-gray-500 fs-8">{{ __('Assignments') }}</div>
    </div>
    <div class="ms-auto">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_module">
            {!! getIcon('plus', 'fs-6 me-1') !!}{{ __('Add Module') }}
        </button>
    </div>
</div>
<!--end::Summary Stats Header-->
