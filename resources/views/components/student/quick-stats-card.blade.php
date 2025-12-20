{{--
    Quick Stats Card Component - Metronic Design System Aligned

    A central stats card showing overall progress with completion rate,
    course counts, and GPA/credits display.

    @props
    - completionRate: int - Overall completion percentage
    - coursesEnrolled: int - Active courses count
    - coursesCompleted: int - Completed courses count
    - assignmentsPending: int - Pending assignments count
    - gpa: float - Current GPA
    - creditsEarned: float - Credits earned

    @example
    <x-student.quick-stats-card
        :completionRate="$progress_stats['completion_rate']"
        :coursesEnrolled="$progress_stats['courses_enrolled']"
        :coursesCompleted="$progress_stats['courses_completed']"
        :assignmentsPending="$progress_stats['assignments_pending']"
        :gpa="$progress_stats['gpa']"
        :creditsEarned="$progress_stats['credits_earned']"
    />
--}}

@props([
    'completionRate' => 0,
    'coursesEnrolled' => 0,
    'coursesCompleted' => 0,
    'assignmentsPending' => 0,
    'gpa' => 0,
    'creditsEarned' => 0,
])

<div class="card card-flush h-xl-100 bg-light-primary">
    <div class="card-body d-flex flex-column justify-content-center text-center py-9">
        {{-- Header --}}
        <div class="mb-7">
            <span class="text-gray-700 fs-6 fw-semibold">{{ __('Overall Progress') }}</span>
        </div>

        {{-- Main Progress Display --}}
        <div class="mb-7">
            <span class="display-1 fw-bold text-primary">{{ $completionRate }}%</span>
        </div>

        {{-- Progress Bar --}}
        <div class="mb-5">
            <div class="progress h-10px w-100 mb-3 bg-light-success">
                <div class="progress-bar bg-primary"
                     role="progressbar"
                     style="width: {{ $completionRate }}%"
                     aria-valuenow="{{ $completionRate }}"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     aria-label="{{ __('Overall progress') }}">
                </div>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="row g-3" role="list" aria-label="{{ __('Course statistics') }}">
            {{-- Active Courses --}}
            <div class="col-4" role="listitem">
                <div class="p-3 rounded bg-light-primary">
                    <span class="fs-2x fw-bold d-block text-primary">{{ $coursesEnrolled }}</span>
                    <span class="text-gray-700 fs-8">{{ __('Active') }}</span>
                </div>
            </div>

            {{-- Completed Courses --}}
            <div class="col-4" role="listitem">
                <div class="p-3 rounded bg-light-success">
                    <span class="fs-2x fw-bold d-block text-success">{{ $coursesCompleted }}</span>
                    <span class="text-gray-700 fs-8">{{ __('Done') }}</span>
                </div>
            </div>

            {{-- Pending Assignments --}}
            <div class="col-4" role="listitem">
                <div class="p-3 rounded bg-light-danger">
                    <span class="fs-2x fw-bold d-block text-danger">{{ $assignmentsPending }}</span>
                    <span class="text-gray-700 fs-8">{{ __('Pending') }}</span>
                </div>
            </div>
        </div>

        {{-- GPA Display --}}
        @if($gpa > 0)
            <div class="separator separator-dashed my-5"></div>
            <div class="d-flex justify-content-center gap-8">
                <div>
                    <span class="fs-2 fw-bolder text-gray-800">{{ number_format($gpa, 2) }}</span>
                    <span class="text-muted fs-7 d-block">{{ __('GPA') }}</span>
                </div>
                <div>
                    <span class="fs-2 fw-bolder text-gray-800">{{ number_format($creditsEarned, 1) }}</span>
                    <span class="text-muted fs-7 d-block">{{ __('Credits') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
