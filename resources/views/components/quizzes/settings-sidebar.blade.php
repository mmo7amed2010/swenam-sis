{{--
 * Quiz Settings Sidebar Component
 *
 * Displays quiz configuration and settings in a sidebar card.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
--}}

@props(['context', 'program', 'course', 'quiz'])

@php
    $isAdmin = $context === 'admin';
    $isExam = $quiz->isExam();

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $attemptsRoute = route("{$routePrefix}.quizzes.attempts", [$program, $course, $quiz]);
    $moduleRoute = $isAdmin
        ? route('admin.programs.courses.show', [$program, $course]) . '#module-' . ($quiz->module?->id ?? '')
        : route('instructor.courses.show', [$program, $course]) . '#module-' . ($quiz->module?->id ?? '');
@endphp

{{-- View Attempts Button --}}
<div class="card card-flush mb-5">
    <div class="card-body py-5">
        <a href="{{ $attemptsRoute }}" class="btn btn-flex btn-light-primary w-100">
            {!! getIcon('people', 'fs-4 me-2') !!}
            {{ __('View Student Attempts') }}
        </a>
    </div>
</div>

{{-- Quiz Description --}}
@if($quiz->description)
<div class="card card-flush mb-5">
    <div class="card-header py-4">
        <h4 class="card-title fs-6 fw-bold m-0">
            {!! getIcon('document', 'fs-5 me-2 text-gray-600') !!}
            {{ __('Description') }}
        </h4>
    </div>
    <div class="card-body pt-0">
        <div class="text-gray-700 fs-7">{!! $quiz->description !!}</div>
    </div>
</div>
@endif

{{-- Settings Card --}}
<div class="card card-flush mb-5">
    <div class="card-header py-4">
        <h4 class="card-title fs-6 fw-bold m-0">
            {!! getIcon('setting-2', 'fs-5 me-2 text-gray-600') !!}
            {{ __('Settings') }}
        </h4>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-light-primary"
                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_quiz_{{ $quiz->id }}"
                    title="{{ __('Edit Settings') }}">
                {!! getIcon('pencil', 'fs-6') !!}
            </button>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="d-flex flex-column gap-4">
            {{-- Assessment Type --}}
            <div class="d-flex align-items-center">
                <div class="w-125px text-gray-500 fs-7">{{ __('Type') }}</div>
                <div class="d-flex align-items-center gap-2">
                    @if($isExam)
                        {!! getIcon('award', 'fs-5 text-danger') !!}
                        <span class="fw-semibold text-danger">{{ __('Exam') }}</span>
                    @else
                        {!! getIcon('question-2', 'fs-5 text-info') !!}
                        <span class="fw-semibold text-info">{{ __('Quiz') }}</span>
                    @endif
                </div>
            </div>

            <div class="separator"></div>

            {{-- Total Points --}}
            <div class="d-flex align-items-center">
                <div class="w-125px text-gray-500 fs-7">{{ __('Total Points') }}</div>
                <div class="d-flex align-items-center gap-2">
                    {!! getIcon('star', 'fs-5 text-warning') !!}
                    <span class="fw-bold fs-5">{{ $quiz->total_points }}</span>
                    <span class="text-muted fs-8">{{ __('points') }}</span>
                </div>
            </div>

            <div class="separator"></div>

            {{-- Passing Score --}}
            <div class="d-flex align-items-center">
                <div class="w-125px text-gray-500 fs-7">{{ __('Pass Score') }}</div>
                <div class="d-flex align-items-center gap-2">
                    {!! getIcon('verify', 'fs-5 text-success') !!}
                    <span class="fw-bold fs-5">{{ $quiz->passing_score }}%</span>
                    <span class="text-muted fs-8">({{ round($quiz->total_points * $quiz->passing_score / 100) }} {{ __('pts') }})</span>
                </div>
            </div>

            <div class="separator"></div>

            {{-- Show Correct Answers --}}
            <div class="d-flex align-items-center">
                <div class="w-125px text-gray-500 fs-7">{{ __('Show Answers') }}</div>
                <div class="d-flex align-items-center gap-2">
                    @if($quiz->show_correct_answers === 'after_each_attempt')
                        {!! getIcon('eye', 'fs-5 text-success') !!}
                        <span class="fw-semibold text-success">{{ __('Enabled') }}</span>
                        <span class="text-muted fs-8">({{ __('after submission') }})</span>
                    @else
                        {!! getIcon('eye-slash', 'fs-5 text-danger') !!}
                        <span class="fw-semibold text-danger">{{ __('Disabled') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Options Card --}}
<div class="card card-flush mb-5">
    <div class="card-header py-4">
        <h4 class="card-title fs-6 fw-bold m-0">
            {!! getIcon('category', 'fs-5 me-2 text-gray-600') !!}
            {{ __('Options') }}
        </h4>
    </div>
    <div class="card-body pt-0">
        <div class="d-flex flex-column gap-3">
            {{-- Shuffle Questions --}}
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-gray-700 fs-7">{{ __('Shuffle Questions') }}</span>
                @if($quiz->shuffle_questions)
                    <span class="badge badge-light-success">
                        {!! getIcon('check', 'fs-7 me-1') !!}{{ __('Yes') }}
                    </span>
                @else
                    <span class="badge badge-light-secondary">{{ __('No') }}</span>
                @endif
            </div>

            {{-- Shuffle Answers --}}
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-gray-700 fs-7">{{ __('Shuffle Answers') }}</span>
                @if($quiz->shuffle_answers)
                    <span class="badge badge-light-success">
                        {!! getIcon('check', 'fs-7 me-1') !!}{{ __('Yes') }}
                    </span>
                @else
                    <span class="badge badge-light-secondary">{{ __('No') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Module Info (if applicable) --}}
@if($quiz->module)
<div class="card card-flush">
    <div class="card-header py-4">
        <h4 class="card-title fs-6 fw-bold m-0">
            {!! getIcon('book-square', 'fs-5 me-2 text-gray-600') !!}
            {{ __('Module') }}
        </h4>
    </div>
    <div class="card-body pt-0">
        <a href="{{ $moduleRoute }}"
           class="d-flex align-items-center text-hover-primary">
            <div class="symbol symbol-35px symbol-circle bg-light-primary me-3">
                <span class="symbol-label text-primary fs-7 fw-bold">{{ $quiz->module->order_index }}</span>
            </div>
            <div>
                <div class="fw-semibold text-gray-800">{{ $quiz->module->title }}</div>
                <div class="text-muted fs-8">{{ __('Click to view module') }}</div>
            </div>
        </a>
    </div>
</div>
@endif
