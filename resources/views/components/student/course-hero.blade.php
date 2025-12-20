{{--
    Course Hero Component
    A prominent header section with course metadata and integrated progress display.

    @param \App\Models\Course $course - The course model
    @param array $progress - Progress data with 'percentage', 'completed_items', 'total_items'
    @param string $courseStatus - 'not_started', 'in_progress', or 'completed'
    @param string $instructorName - Name of the lead instructor
    @param array $contentTotals - Array with 'lessons', 'quizzes', 'assignments' counts
--}}

@props([
    'course',
    'progress',
    'courseStatus',
    'instructorName',
    'contentTotals',
])

@php
    // Status badge configuration
    $statusConfig = match($courseStatus) {
        'completed' => ['class' => 'bg-success', 'text' => __('Completed'), 'icon' => 'check-circle'],
        'in_progress' => ['class' => 'bg-warning text-dark', 'text' => __('In Progress'), 'icon' => 'loading'],
        default => ['class' => 'bg-secondary', 'text' => __('Not Started'), 'icon' => 'timer'],
    };

    // Estimate total duration from lessons (if available)
    $totalDuration = $course->modules->flatMap(fn($m) => $m->lessons)->sum('estimated_duration') ?? 0;
    $durationDisplay = $totalDuration > 0 ? ($totalDuration >= 60 ? floor($totalDuration / 60) . 'h ' . ($totalDuration % 60) . 'm' : $totalDuration . 'm') : null;

    // Progress color based on percentage
    $progressColor = match(true) {
        $progress['percentage'] >= 100 => 'success',
        $progress['percentage'] >= 75 => 'primary',
        $progress['percentage'] >= 50 => 'info',
        $progress['percentage'] >= 25 => 'warning',
        default => 'secondary',
    };
@endphp

<div class="card card-flush mb-6 overflow-hidden course-hero-card">
    <div class="card-body p-0">
        {{-- Hero Section --}}
        <div class="course-hero-bg py-6 px-6 px-lg-8">
            <div class="d-flex flex-column flex-lg-row gap-5">
                {{-- Left: Course Info --}}
                <div class="flex-grow-1">
                    {{-- Course Code & Status --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge bg-white bg-opacity-20 text-white fs-7 fw-semibold px-3 py-2">
                            {{ $course->course_code }}
                        </span>
                        <span class="badge {{ $statusConfig['class'] }} fs-8 px-3 py-2">
                            {!! getIcon($statusConfig['icon'], 'fs-8 me-1') !!}
                            {{ $statusConfig['text'] }}
                        </span>
                    </div>

                    {{-- Course Title --}}
                    <h1 class="text-white fw-bolder fs-2x mb-4">{{ $course->name }}</h1>

                    {{-- Metadata Row --}}
                    <div class="d-flex flex-wrap gap-4 fs-6 text-white text-opacity-85">
                        {{-- Instructor --}}
                        <span class="d-flex align-items-center gap-2">
                            {!! getIcon('profile-user', 'fs-5') !!}
                            {{ $instructorName }}
                        </span>

                        {{-- Credits --}}
                        @if($course->credits)
                            <span class="d-flex align-items-center gap-2">
                                {!! getIcon('award', 'fs-5') !!}
                                {{ number_format($course->credits, 1) }} {{ __('Credits') }}
                            </span>
                        @endif

                        {{-- Estimated Duration --}}
                        @if($durationDisplay)
                            <span class="d-flex align-items-center gap-2">
                                {!! getIcon('timer', 'fs-5') !!}
                                {{ $durationDisplay }}
                            </span>
                        @endif

                        {{-- Semester --}}
                        @if($course->semester)
                            <span class="d-flex align-items-center gap-2">
                                {!! getIcon('calendar', 'fs-5') !!}
                                {{ $course->semester }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Right: Progress Card --}}
                <div class="flex-shrink-0 d-flex align-items-center">
                    <div class="progress-card bg-white bg-opacity-10 rounded-3 p-4 text-center" style="min-width: 160px;">
                        {{-- Progress Percentage --}}
                        <div class="mb-2">
                            <span class="fs-3x fw-bolder text-white">{{ round($progress['percentage']) }}</span>
                            <span class="fs-4 fw-bold text-white text-opacity-75">%</span>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="progress h-8px bg-white bg-opacity-20 rounded mb-3">
                            <div class="progress-bar bg-{{ $progressColor }} rounded"
                                 role="progressbar"
                                 style="width: {{ $progress['percentage'] }}%"
                                 aria-valuenow="{{ $progress['percentage'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>

                        {{-- Items Count --}}
                        <div class="text-white text-opacity-85 fs-7">
                            <span class="fw-semibold">{{ $progress['completed_items'] }}</span>
                            <span class="text-opacity-65">/</span>
                            <span>{{ $progress['total_items'] }}</span>
                            <span class="text-opacity-65 ms-1">{{ __('items') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats Bar --}}
        <div class="d-flex flex-wrap bg-white">
            {{-- Modules --}}
            <div class="flex-equal py-4 px-5 border-end text-center">
                <span class="fs-3 fw-bold text-gray-800 d-block">
                    {{ $course->modules->count() }}
                </span>
                <span class="text-muted fs-7">{{ __('Modules') }}</span>
            </div>

            {{-- Lessons --}}
            <div class="flex-equal py-4 px-5 border-end text-center">
                <span class="fs-3 fw-bold text-gray-800 d-block">
                    {{ $contentTotals['lessons'] }}
                </span>
                <span class="text-muted fs-7">{{ __('Lessons') }}</span>
            </div>

            {{-- Quizzes --}}
            <div class="flex-equal py-4 px-5 border-end text-center d-none d-sm-block">
                <span class="fs-3 fw-bold text-gray-800 d-block">
                    {{ $contentTotals['quizzes'] }}
                </span>
                <span class="text-muted fs-7">{{ __('Quizzes') }}</span>
            </div>

            {{-- Assignments --}}
            <div class="flex-equal py-4 px-5 text-center d-none d-sm-block">
                <span class="fs-3 fw-bold text-gray-800 d-block">
                    {{ $contentTotals['assignments'] }}
                </span>
                <span class="text-muted fs-7">{{ __('Assignments') }}</span>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<style>
.course-hero-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.course-hero-bg {
    background: linear-gradient(135deg, #12294C 0%, #1e3a6b 50%, #0f1f3d 100%);
    position: relative;
}

.course-hero-bg::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 50%;
    background: radial-gradient(ellipse at 80% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
    pointer-events: none;
}

.progress-card {
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.progress-card .progress {
    overflow: hidden;
}

.progress-card .progress-bar {
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Mobile adjustments */
@media (max-width: 991.98px) {
    .progress-card {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.25rem !important;
    }

    .progress-card > div:first-child {
        margin-bottom: 0 !important;
    }

    .progress-card .progress {
        flex: 1;
        margin: 0 1rem 0 0 !important;
    }

    .progress-card > div:last-child {
        white-space: nowrap;
    }
}
</style>
@endpush
@endonce
