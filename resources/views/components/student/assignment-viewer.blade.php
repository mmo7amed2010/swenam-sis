{{--
 * Student Assignment Viewer Component
 *
 * Unified content display for assignments following Metronic design.
 *
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param string $status - not_submitted, submitted, graded, overdue
 * @param \App\Models\Submission|null $latestSubmission
 * @param \App\Models\AssignmentGrade|null $publishedGrade
 * @param \App\Models\CourseModule|null $module
 * @param array|null $previousItem - ['url' => string] for previous content navigation
 * @param array|null $nextItem - ['url' => string] for next content navigation
--}}

@props([
    'course',
    'assignment',
    'status' => 'not_submitted',
    'latestSubmission' => null,
    'publishedGrade' => null,
    'module' => null,
    'previousItem' => null,
    'nextItem' => null,
])

@php
    // Simplified status configuration for self-paced courses (no overdue status)
    $statusConfig = match($status) {
        'graded' => ['color' => 'success', 'icon' => 'check-circle', 'label' => __('Graded')],
        'submitted' => ['color' => 'primary', 'icon' => 'send', 'label' => __('Submitted')],
        default => ['color' => 'warning', 'icon' => 'notepad', 'label' => __('Not Submitted')],
    };

    // Submission type labels
    $submissionTypeLabels = [
        'file_upload' => __('File Upload'),
        'text_entry' => __('Text Entry'),
        'url_submission' => __('URL Submission'),
        'multiple' => __('Multiple Types'),
    ];

@endphp

<article class="assignment-viewer card card-flush shadow-sm">
    {{-- Back to Course Link --}}
    <x-student.content-viewer-back-link :courseUrl="route('student.courses.show', $course->id)" />

    {{-- Header Zone --}}
    <div class="card-header border-0 pt-6 pb-0">
        {{-- Badge Row --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 w-100 mb-4">
            <div class="d-flex flex-wrap gap-2">
                @if($module)
                    <span class="badge badge-light-primary fs-7 py-2 px-3">
                        {!! getIcon('folder', 'fs-7 me-1') !!}
                        {{ $module->title }}
                    </span>
                @endif
                <span class="badge badge-light-{{ $statusConfig['color'] }} fs-7 py-2 px-3">
                    {!! getIcon($statusConfig['icon'], 'fs-7 me-1') !!}
                    {{ $statusConfig['label'] }}
                </span>
            </div>
            <button type="button" class="btn btn-sm btn-light-primary" onclick="toggleCourseSidebar()">
                {!! getIcon('burger-menu-2', 'fs-5') !!}
                <span class="d-none d-sm-inline ms-2">{{ __('Contents') }}</span>
            </button>
        </div>

        {{-- Title --}}
        <h1 class="fw-bold text-gray-900 mb-4 fs-2">{{ $assignment->title }}</h1>

        {{-- Metadata Bar --}}
        <div class="d-flex flex-wrap align-items-center gap-4 p-4 bg-gray-100 rounded mb-4">
            {{-- Assignment Type --}}
            <div class="d-flex align-items-center gap-2">
                <span class="symbol symbol-35px">
                    <span class="symbol-label bg-light-success">
                        {!! getIcon('notepad', 'fs-5 text-success') !!}
                    </span>
                </span>
                <span class="fw-semibold text-gray-800">{{ __('Assignment') }}</span>
            </div>

            {{-- Points --}}
            <div class="d-flex align-items-center gap-2 text-gray-600">
                {!! getIcon('star', 'fs-5 text-warning') !!}
                <span>{{ $assignment->total_points ?? $assignment->max_points ?? 0 }} {{ __('points') }}</span>
            </div>

            {{-- Submission Type --}}
            @if($assignment->submission_type)
                <div class="d-flex align-items-center gap-2 text-gray-600 ms-auto">
                    {!! getIcon('file', 'fs-5') !!}
                    <span>{{ $submissionTypeLabels[$assignment->submission_type] ?? $assignment->submission_type }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Description Zone --}}
    @if($assignment->description)
        <div class="card-body pt-0 pb-4">
            <div class="bg-light-primary rounded p-4">
                <p class="text-gray-700 mb-0">{!! nl2br(e($assignment->description)) !!}</p>
            </div>
        </div>
    @endif

    {{-- Content Zone --}}
    <div class="card-body pt-0">
        {{-- Grade Card (for graded assignments) --}}
        @if($publishedGrade)
            <div class="card mb-6 bg-light-success border-0">
                <div class="card-body text-center py-6">
                    <div class="fs-3x fw-bold text-success mb-2">
                        {{ number_format($publishedGrade->points_awarded, 1) }} / {{ number_format($publishedGrade->max_points, 1) }}
                    </div>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge badge-success fs-6">{{ number_format($publishedGrade->percentage, 1) }}%</span>
                        @if($publishedGrade->letter_grade)
                            <span class="badge badge-primary fs-6">{{ $publishedGrade->letter_grade }}</span>
                        @endif
                    </div>
                    @if($publishedGrade->feedback)
                        <div class="text-start bg-white rounded p-3 mt-4">
                            <strong class="text-gray-800">{{ __('Feedback') }}:</strong>
                            <p class="text-gray-600 mb-0 mt-1">{{ $publishedGrade->feedback }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Status/Action Cards --}}
        <div class="completion-section" id="completion-section-assignment-{{ $assignment->id }}">
            @if($status === 'submitted')
                {{-- Awaiting Grading Card --}}
                <div class="card bg-light-info border border-info border-dashed status-pending-card">
                    <div class="card-body text-center py-8">
                        <div class="symbol symbol-70px symbol-circle mb-5">
                            <span class="symbol-label bg-info">
                                {!! getIcon('time', 'fs-2x text-white') !!}
                            </span>
                        </div>
                        <h4 class="fw-bold text-gray-900 mb-2">{{ __('Awaiting Grading') }}</h4>
                        <p class="text-gray-600 mb-0">{{ __('Your submission is being reviewed by your instructor.') }}</p>
                        @if($latestSubmission && $latestSubmission->submitted_at)
                            <div class="text-muted fs-7 mt-3">
                                {{ __('Submitted') }}: {{ $latestSubmission->submitted_at->format('M d, Y g:i A') }}
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($status === 'graded')
                {{-- Graded Card --}}
                <div class="card border border-gray-300 border-dashed">
                    <div class="card-body text-center py-8">
                        <div class="symbol symbol-70px symbol-circle mb-5">
                            <span class="symbol-label bg-light-success">
                                {!! getIcon('check-circle', 'fs-2x text-success') !!}
                            </span>
                        </div>
                        <h4 class="fw-bold text-gray-900 mb-2">{{ __('Assignment Graded') }}</h4>
                        <p class="text-gray-600 mb-6">{{ __('Your work has been reviewed and graded.') }}</p>
                        <a href="{{ route('student.courses.assignments.show', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" class="btn btn-primary btn-lg">
                            {!! getIcon('eye', 'fs-4 me-2 text-white') !!}
                            {{ __('View Feedback') }}
                        </a>
                    </div>
                </div>

            @else
                {{-- Not Submitted Card --}}
                <div class="card border border-gray-300 border-dashed status-action-card">
                    <div class="card-body text-center py-8">
                        <div class="symbol symbol-70px symbol-circle mb-5">
                            <span class="symbol-label bg-light-success">
                                {!! getIcon('notepad-edit', 'fs-2x text-success') !!}
                            </span>
                        </div>
                        <h4 class="fw-bold text-gray-900 mb-2">{{ __('Ready to submit?') }}</h4>
                        <p class="text-gray-600 mb-6">{{ __('Complete and submit your assignment before the deadline.') }}</p>
                        <a href="{{ route('student.assignments.submit', $assignment->id) }}" class="btn btn-primary btn-lg">
                            {!! getIcon('arrow-right', 'fs-4 me-2 text-white') !!}
                            {{ __('Start Submission') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Footer Zone --}}
    <x-student.content-viewer-footer
        :previousUrl="$previousItem['url'] ?? null"
        :nextUrl="$nextItem['url'] ?? null"
    />
</article>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom/admin/courses/content-viewer.css') }}">
@endpush
