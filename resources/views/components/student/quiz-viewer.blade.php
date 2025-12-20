{{--
 * Student Quiz Viewer Component
 *
 * Unified content display for quizzes/exams following Metronic design.
 *
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
 * @param string $status - not_started, in_progress, completed, passed, failed
 * @param \App\Models\QuizAttempt|null $latestAttempt
 * @param array $canStart - ['can_start' => bool, 'reason' => string|null, 'resume_url' => string|null]
 * @param \App\Models\CourseModule|null $module
 * @param array|null $previousItem - ['url' => string] for previous content navigation
 * @param array|null $nextItem - ['url' => string] for next content navigation
--}}

@props([
    'course',
    'quiz',
    'status' => 'not_started',
    'latestAttempt' => null,
    'canStart' => ['can_start' => true, 'reason' => null, 'resume_url' => null],
    'module' => null,
    'previousItem' => null,
    'nextItem' => null,
])

@php
    // Status configuration
    $statusConfig = match($status) {
        'passed' => ['color' => 'success', 'icon' => 'check-circle', 'label' => __('Passed')],
        'failed' => ['color' => 'danger', 'icon' => 'cross-circle', 'label' => __('Failed')],
        'completed' => ['color' => 'primary', 'icon' => 'check', 'label' => __('Completed')],
        'in_progress' => ['color' => 'warning', 'icon' => 'time', 'label' => __('In Progress')],
        default => ['color' => 'secondary', 'icon' => 'question-2', 'label' => __('Not Started')],
    };

    // Quiz type configuration
    $isExam = $quiz->isExam();
    $quizTypeConfig = $isExam
        ? ['icon' => 'award', 'color' => 'danger', 'label' => __('Exam')]
        : ['icon' => 'question-2', 'color' => 'info', 'label' => __('Quiz')];

    // Calculate score and pass status for completed attempts
    $hasResult = $latestAttempt && in_array($status, ['completed', 'passed', 'failed']);
    $percentage = 0;
    $isPassed = false;

    if ($hasResult) {
        $percentage = $quiz->total_points > 0 ? ($latestAttempt->score / $quiz->total_points) * 100 : 0;
        $isPassed = $percentage >= $quiz->passing_score;
    }

@endphp

<article class="quiz-viewer card card-flush shadow-sm">
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
        <h1 class="fw-bold text-gray-900 mb-4 fs-2">{{ $quiz->title }}</h1>

        {{-- Metadata Bar --}}
        <div class="d-flex flex-wrap align-items-center gap-4 p-4 bg-gray-100 rounded mb-4">
            {{-- Quiz/Exam Type --}}
            <div class="d-flex align-items-center gap-2">
                <span class="symbol symbol-35px">
                    <span class="symbol-label bg-light-{{ $quizTypeConfig['color'] }}">
                        {!! getIcon($quizTypeConfig['icon'], 'fs-5 text-' . $quizTypeConfig['color']) !!}
                    </span>
                </span>
                <span class="fw-semibold text-gray-800">{{ $quizTypeConfig['label'] }}</span>
            </div>

            {{-- Points --}}
            <div class="d-flex align-items-center gap-2 text-gray-600">
                {!! getIcon('star', 'fs-5 text-warning') !!}
                <span>{{ $quiz->total_points }} {{ __('points') }}</span>
            </div>

            {{-- Questions Count --}}
            <div class="d-flex align-items-center gap-2 text-gray-600">
                {!! getIcon('list', 'fs-5') !!}
                <span>{{ $quiz->questions->count() }} {{ __('questions') }}</span>
            </div>

            {{-- Passing Score --}}
            <div class="d-flex align-items-center gap-2 text-gray-600">
                {!! getIcon('verify', 'fs-5 text-success') !!}
                <span>{{ __('Pass') }}: {{ $quiz->passing_score }}%</span>
            </div>

            {{-- Time Limit --}}
            @if($quiz->time_limit)
                <div class="d-flex align-items-center gap-2 text-gray-600 ms-auto">
                    {!! getIcon('timer', 'fs-5 text-danger') !!}
                    <span>{{ $quiz->time_limit }} {{ __('min') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Description Zone --}}
    @if($quiz->description)
        <div class="card-body pt-0 pb-4">
            <div class="bg-light-info rounded p-4">
                <div class="text-gray-700">{!! $quiz->description !!}</div>
            </div>
        </div>
    @endif

    {{-- Content Zone --}}
    <div class="card-body pt-0">
        {{-- Result Card (for completed/passed/failed) --}}
        @if($hasResult)
            <div class="card mb-6 {{ $isPassed ? 'bg-light-success' : 'bg-light-danger' }} border-0">
                <div class="card-body text-center py-6">
                    <div class="fs-3x fw-bold {{ $isPassed ? 'text-success' : 'text-danger' }} mb-2">
                        {{ number_format($latestAttempt->score, 1) }} / {{ $quiz->total_points }}
                    </div>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge badge-{{ $isPassed ? 'success' : 'danger' }} fs-6">
                            {{ number_format($percentage, 1) }}%
                        </span>
                        <span class="badge badge-{{ $isPassed ? 'light-success' : 'light-danger' }} fs-6">
                            {{ $isPassed ? __('Passed') : __('Failed') }}
                        </span>
                    </div>
                    @if($latestAttempt->submitted_at)
                        <div class="text-gray-600 fs-7">
                            {{ __('Completed') }}: {{ $latestAttempt->submitted_at->format('M d, Y g:i A') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Status/Action Cards --}}
        <div class="completion-section" id="completion-section-quiz-{{ $quiz->id }}">
            @if($status === 'in_progress')
                {{-- In Progress Card --}}
                <div class="card bg-light-warning border border-warning border-dashed">
                    <div class="card-body text-center py-8">
                        <div class="symbol symbol-70px symbol-circle mb-5">
                            <span class="symbol-label bg-warning">
                                {!! getIcon('time', 'fs-2x text-white') !!}
                            </span>
                        </div>
                        <h4 class="fw-bold text-gray-900 mb-2">{{ __('Quiz In Progress') }}</h4>
                        <p class="text-gray-600 mb-6">{{ __('You have an unfinished attempt. Continue where you left off.') }}</p>
                        @if($latestAttempt)
                            <a href="{{ route('student.quizzes.take', [$course->id, $quiz->id, $latestAttempt->id]) }}" class="btn btn-warning btn-lg">
                                {!! getIcon('arrow-right', 'fs-4 me-2 text-white') !!}
                                {{ __('Continue Quiz') }}
                            </a>
                        @endif
                    </div>
                </div>

            @elseif($status === 'not_started')
                {{-- Not Started Card --}}
                <div class="card border border-gray-300 border-dashed status-action-card">
                    <div class="card-body text-center py-8">
                        <div class="symbol symbol-70px symbol-circle mb-5">
                            <span class="symbol-label bg-light-info">
                                {!! getIcon('question-2', 'fs-2x text-info') !!}
                            </span>
                        </div>
                        <h4 class="fw-bold text-gray-900 mb-2">{{ __('Ready to test your knowledge?') }}</h4>
                        <p class="text-gray-600 mb-6">{{ __('Take this quiz to check your understanding of the material.') }}</p>
                        @if($canStart['can_start'] ?? true)
                            <form action="{{ route('student.quizzes.start', [$course->id, $quiz->id]) }}" method="POST" class="d-inline" id="start-quiz-form-{{ $quiz->id }}">
                                @csrf
                                <button type="button" class="btn btn-primary btn-lg" id="start-quiz-btn-{{ $quiz->id }}">
                                    {!! getIcon('arrow-right', 'fs-4 me-2 text-white') !!}
                                    {{ __('Start Quiz') }}
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning d-inline-flex align-items-center gap-2 mb-0">
                                {!! getIcon('information-5', 'fs-4') !!}
                                <span>{{ $canStart['reason'] ?? __('You cannot start this quiz at this time.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif(in_array($status, ['passed', 'completed', 'failed']))
                {{-- Completed/Results Card --}}
                <div class="card border border-gray-300 border-dashed">
                    <div class="card-body text-center py-8">
                        <div class="symbol symbol-70px symbol-circle mb-5">
                            <span class="symbol-label bg-light-{{ $isPassed ? 'success' : 'primary' }}">
                                {!! getIcon($isPassed ? 'check-circle' : 'document', 'fs-2x text-' . ($isPassed ? 'success' : 'gray-700')) !!}
                            </span>
                        </div>
                        <h4 class="fw-bold text-gray-900 mb-2">
                            {{ $isPassed ? __('Great job!') : __('Quiz Completed') }}
                        </h4>
                        <p class="text-gray-600 mb-6">
                            {{ $isPassed
                                ? __('You have successfully passed this quiz.')
                                : __('Review your answers and see where you can improve.')
                            }}
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('student.quizzes.results', [$course->id, $quiz->id, $latestAttempt?->id]) }}" class="btn btn-primary btn-lg">
                                {!! getIcon('eye', 'fs-4 me-2 text-white') !!}
                                {{ __('View Results') }}
                            </a>
                            @if(!$isPassed && ($quiz->isUnlimitedAttempts() || ($latestAttempt && $latestAttempt->attempt_number < $quiz->max_attempts)))
                                <form action="{{ route('student.quizzes.start', [$course->id, $quiz->id]) }}" method="POST" class="d-inline" id="retry-quiz-form-{{ $quiz->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-light-primary btn-lg" id="retry-quiz-btn-{{ $quiz->id }}">
                                        {!! getIcon('arrows-circle', 'fs-4 me-2') !!}
                                        {{ __('Retry Quiz') }}
                                    </button>
                                </form>
                            @endif
                        </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Start Quiz confirmation
    const startBtn = document.getElementById('start-quiz-btn-{{ $quiz->id }}');
    if (startBtn) {
        startBtn.addEventListener('click', function() {
            Swal.fire({
                title: '{{ __("Start Quiz?") }}',
                text: '{{ __("Are you sure you want to start this quiz? Your attempt will be recorded.") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, Start Quiz") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('start-quiz-form-{{ $quiz->id }}').submit();
                }
            });
        });
    }

    // Retry Quiz confirmation
    const retryBtn = document.getElementById('retry-quiz-btn-{{ $quiz->id }}');
    if (retryBtn) {
        retryBtn.addEventListener('click', function() {
            Swal.fire({
                title: '{{ __("Retry Quiz?") }}',
                text: '{{ __("Are you sure you want to retry this quiz? A new attempt will begin.") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Yes, Retry Quiz") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('retry-quiz-form-{{ $quiz->id }}').submit();
                }
            });
        });
    }
});
</script>
@endpush
