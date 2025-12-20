{{--
    Program Hero Component
    
    A header showing program information and progress
    with a gradient background.
    
    @props
    - program: Program model
    - progressPercentage: int Program progress percentage
    - completedCourses: int Number of completed courses
    - totalCourses: int Total number of courses
    - gpa: ?float GPA value
    - enrollmentDate: ?Carbon\Carbon Enrollment date
    - credits: array Credits progress information
    - overview: array Program overview with detailed statistics
    
    @example
    <x-student.program-header
        :program="$program"
        :progressPercentage="$overview['statistics']['progress_percentage']"
        :completedCourses="$overview['statistics']['completed_courses']"
        :totalCourses="$overview['statistics']['total_courses']"
        :gpa="$overview['gpa']['value']"
        :enrollmentDate="$enrollmentDate"
        :credits="$credits"
        :overview="$overview"
    />
--}}

@props([
    'program',
    'progressPercentage' => 0,
    'completedCourses' => 0,
    'totalCourses' => 0,
    'gpa' => null,
    'enrollmentDate' => null,
    'credits' => ['earned' => 0, 'required' => 0, 'percentage' => 0],
    'overview' => ['statistics' => ['in_progress_courses' => 0, 'not_started_courses' => 0]],
])

<div class="card card-flush mb-5 overflow-hidden program-hero-card">
    <div class="card-body p-0">
        {{-- Hero Section with Gradient Background --}}
        <div class="program-hero-bg py-6 px-6 px-lg-8">
            <div class="d-flex flex-column flex-lg-row gap-5">
                {{-- Left: Program Identity --}}
                <div class="flex-grow-1">
                    {{-- Program Code & Status --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge bg-white bg-opacity-20 text-white fs-7 fw-semibold px-3 py-2">
                            {{ $program->code ?? 'PROG' }}
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white fs-7 fw-semibold px-3 py-2">
                            {{ __('Active') }}
                        </span>
                    </div>

                    {{-- Program Title --}}
                    <h1 class="text-white fw-bolder fs-2x mb-4">{{ $program->name }}</h1>

                    {{-- Enrollment Date --}}
                    <div class="d-flex flex-wrap gap-4 fs-6 text-white text-opacity-85 mb-3">
                        <span class="d-flex align-items-center gap-2">
                            {!! getIcon('calendar', 'fs-5') !!}
                            {{ __('Started') }}: {{ $enrollmentDate ? $enrollmentDate->format('M Y') : __('N/A') }}
                        </span>
                    </div>

                    {{-- Progress Overview --}}
                    <div class="d-flex align-items-center gap-4">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fs-6 fw-semibold">{{ __('Program Progress') }}</span>
                                <span class="text-white fs-5 fw-bolder">{{ round($progressPercentage) }}%</span>
                            </div>
                            <div class="progress h-10px bg-white bg-opacity-20 rounded">
                                <div class="progress-bar bg-white rounded"
                                     role="progressbar"
                                     style="width: {{ $progressPercentage }}%"
                                     aria-valuenow="{{ $progressPercentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     aria-label="{{ __('Program progress') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Key Metrics --}}
                <div class="flex-shrink-0 d-flex flex-column gap-3">
                    {{-- GPA Badge --}}
                    @if($gpa !== null && $gpa > 0)
                        <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center">
                            <div class="text-white text-opacity-75 fs-7 mb-1">{{ __('GPA') }}</div>
                            <div class="text-white fs-2 fw-bolder">{{ number_format($gpa, 2) }}</div>
                        </div>
                    @endif

                    {{-- Credits Progress --}}
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center">
                        <div class="text-white text-opacity-75 fs-7 mb-1">{{ __('Credits') }}</div>
                        <div class="text-white fs-2 fw-bolder">{{ $credits['earned'] ?? 0 }}/{{ $credits['required'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Stats Bar --}}
        <div class="d-flex flex-wrap bg-white">
            {{-- Courses Progress --}}
            <div class="flex-equal py-4 px-5 border-end text-center">
                <span class="fs-3 fw-bold text-gray-800 d-block">
                    {{ $completedCourses }}/{{ $totalCourses }}
                </span>
                <span class="text-muted fs-7">{{ __('Courses') }}</span>
                <div class="mt-2">
                    <span class="badge badge-light-success fs-8 me-1">{{ $overview['statistics']['completed_courses'] ?? 0 }} {{ __('Completed') }}</span>
                    <span class="badge badge-light-primary fs-8 me-1">{{ $overview['statistics']['in_progress_courses'] ?? 0 }} {{ __('In Progress') }}</span>
                    <span class="badge badge-light-secondary fs-8">{{ $overview['statistics']['not_started_courses'] ?? 0 }} {{ __('Not Started') }}</span>
                </div>
            </div>

            {{-- Credits Progress --}}
            <div class="flex-equal py-4 px-5 border-end text-center">
                <span class="fs-3 fw-bold text-gray-800 d-block">
                    {{ $credits['earned'] ?? 0 }}/{{ $credits['required'] ?? 0 }}
                </span>
                <span class="text-muted fs-7">{{ __('Credits') }}</span>
                <div class="mt-2">
                    <div class="progress h-6px bg-light-primary rounded" style="width: 120px; margin: 0 auto;">
                        <div class="progress-bar bg-primary rounded"
                             role="progressbar"
                             style="width: {{ $credits['percentage'] ?? 0 }}%"
                             aria-valuenow="{{ $credits['percentage'] ?? 0 }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>

            {{-- GPA --}}
            @if($gpa !== null && $gpa > 0)
                <div class="flex-equal py-4 px-5 text-center">
                    <span class="fs-3 fw-bold text-gray-800 d-block">
                        {{ number_format($gpa, 2) }}
                    </span>
                    <span class="text-muted fs-7">{{ __('GPA') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

@once
@push('styles')
<style>
.program-hero-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.program-hero-bg {
    background: linear-gradient(135deg, #12294C 0%, #1e3a6b 50%, #0f1f3d 100%);
    position: relative;
}

.program-hero-bg::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 50%;
    background: radial-gradient(ellipse at 80% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
    pointer-events: none;
}

/* Mobile adjustments */
@media (max-width: 991.98px) {
    .program-hero-bg .flex-shrink-0 {
        width: 100%;
        display: flex;
        flex-direction: row;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .program-hero-bg .flex-shrink-0 > div {
        flex: 1;
    }
}
</style>
@endpush
@endonce
