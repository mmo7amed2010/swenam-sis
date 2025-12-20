{{--
 * Student Course Header Component
 *
 * A minimal header for the course view with progress indicator,
 * back navigation, and quick actions.
 *
 * @param \App\Models\Course $course - The course being viewed
 * @param array $progress - Course progress data
 * @param \App\Models\ModuleLesson|null $currentLesson - Currently active lesson (optional)
--}}

@props([
    'course',
    'progress',
    'currentLesson' => null,
])

@php
    // Get instructor info (simplified: one instructor per course)
    $instructorAssignment = $course->instructors
        ->whereNull('removed_at')
        ->first();
    $instructorName = $instructorAssignment?->instructor?->name ?? __('No instructor');
@endphp

<header class="course-header d-lg-none bg-white border-bottom sticky-top">
    <div class="d-flex align-items-center justify-content-between px-4 py-3">
        {{-- Left: Menu Toggle & Title --}}
        <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
            <button class="btn btn-icon btn-sm btn-light-primary"
                    type="button"
                    onclick="toggleCourseSidebar()"
                    aria-label="{{ __('Toggle navigation') }}">
                {!! getIcon('menu', 'fs-3') !!}
            </button>
            <div class="min-w-0">
                <h1 class="fs-6 fw-bold text-gray-900 mb-0 text-truncate">
                    @if($currentLesson)
                        {{ $currentLesson->title }}
                    @else
                        {{ $course->name }}
                    @endif
                </h1>
                @if($currentLesson)
                    <span class="text-muted fs-8">{{ $course->name }}</span>
                @endif
            </div>
        </div>

        {{-- Right: Progress Indicator --}}
        <div class="d-flex align-items-center gap-2">
            <div class="progress-ring" title="{{ $progress['percentage'] }}% {{ __('complete') }}">
                <svg width="36" height="36" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e4e6ef" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.5" fill="none" stroke="url(#progress-gradient)"
                            stroke-width="3" stroke-linecap="round"
                            stroke-dasharray="{{ $progress['percentage'] * 0.97 }} 97"
                            transform="rotate(-90 18 18)"/>
                    <defs>
                        <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#3b82f6"/>
                            <stop offset="100%" stop-color="#10b981"/>
                        </linearGradient>
                    </defs>
                </svg>
                <span class="progress-ring-text">{{ $progress['percentage'] }}%</span>
            </div>
        </div>
    </div>

    {{-- Progress Bar (visible when scrolled) --}}
    <div class="progress h-4px w-100" style="margin-top: -1px;">
        <div class="progress-bar"
             data-progress-type="course"
             role="progressbar"
             style="width: {{ $progress['percentage'] }}%; background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%);"
             aria-valuenow="{{ $progress['percentage'] }}"
             aria-valuemin="0"
             aria-valuemax="100">
        </div>
    </div>
</header>

@push('styles')
<style>
.course-header {
    z-index: 99;
}

.progress-ring {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-ring-text {
    position: absolute;
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--bs-gray-700);
}
</style>
@endpush
