{{--
 * Quiz Header Component
 *
 * Displays quiz title, type badge, status, and key statistics.
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
    $questionsCount = $quiz->questions->count();
    $questionTypes = $quiz->questions->groupBy('question_type')->map->count();

    $typeGradients = [
        'quiz' => ['bg' => '#eff6ff', 'icon' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)', 'iconName' => 'question-2', 'color' => 'info'],
        'exam' => ['bg' => '#fef2f2', 'icon' => 'linear-gradient(135deg, #ef4444, #dc2626)', 'iconName' => 'award', 'color' => 'danger'],
    ];
    $typeInfo = $typeGradients[$quiz->assessment_type] ?? $typeGradients['quiz'];

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $backRoute = $isAdmin
        ? route('admin.programs.courses.show', [$program, $course])
        : route('instructor.courses.show', [$program, $course]);
    $deleteRoute = route("{$routePrefix}.quizzes.destroy", [$program, $course, $quiz]);
@endphp

<div class="card mb-6 border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-lg-row">
            {{-- Left: Quiz Icon & Info --}}
            <div class="d-flex align-items-center p-8 flex-grow-1" style="background: linear-gradient(135deg, {{ $typeInfo['bg'] }} 0%, #ffffff 100%);">
                {{-- Quiz Type Icon --}}
                <div class="symbol symbol-70px symbol-circle me-5 flex-shrink-0" style="background: {{ $typeInfo['icon'] }};">
                    <span class="symbol-label bg-transparent">
                        {!! getIcon($typeInfo['iconName'], 'fs-2x text-white') !!}
                    </span>
                </div>

                {{-- Quiz Title & Meta --}}
                <div class="d-flex flex-column flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center flex-wrap mb-2 gap-2">
                        <h1 class="fs-2 fw-bold text-gray-900 mb-0 text-truncate">{{ $quiz->title }}</h1>
                        <span class="badge badge-light-{{ $typeInfo['color'] }} fs-7 fw-semibold">
                            {{ $isExam ? __('Exam') : __('Quiz') }}
                        </span>
                        @if($quiz->published)
                            <span class="badge badge-light-success fs-8">{{ __('Published') }}</span>
                        @else
                            <span class="badge badge-light-warning fs-8">{{ __('Draft') }}</span>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap text-gray-500 fs-7 gap-3" data-quiz-meta>
                        <span title="{{ __('Questions') }}">
                            {!! getIcon('questionnaire-tablet', 'fs-7 me-1') !!}
                            <span data-quiz-questions-count>{{ $questionsCount }}</span> {{ __('questions') }}
                        </span>
                        <span title="{{ __('Total Points') }}">
                            {!! getIcon('star', 'fs-7 me-1') !!}
                            <span data-quiz-total-points>{{ $quiz->total_points }}</span> {{ __('points') }}
                        </span>
                        @if($quiz->hasTimeLimit())
                        <span title="{{ __('Time Limit') }}">
                            {!! getIcon('timer', 'fs-7 me-1') !!}
                            {{ $quiz->time_limit }} {{ __('min') }}
                        </span>
                        @endif
                        <span title="{{ __('Passing Score') }}">
                            {!! getIcon('verify', 'fs-7 me-1') !!}
                            {{ $quiz->passing_score }}% {{ __('to pass') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="d-flex align-items-center justify-content-end p-6 border-start border-gray-200 bg-gray-50">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button"
                            class="btn btn-sm btn-flex btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_question_{{ $quiz->id }}">
                        {!! getIcon('plus', 'fs-6 me-1') !!}
                        {{ __('Add Question') }}
                    </button>
                    <x-actions.dropdown buttonText="{{ __('Actions') }}" buttonClass="btn-sm btn-light" buttonIcon="down">
                        {{-- Edit Settings --}}
                        <x-actions.modal-button
                            target="#kt_modal_edit_quiz_{{ $quiz->id }}"
                            icon="setting-2"
                        >
                            {{ __('Edit Settings') }}
                        </x-actions.modal-button>

                        {{-- Back to Course --}}
                        <x-actions.link
                            href="{{ $backRoute }}"
                            icon="arrow-left"
                        >
                            {{ __('Back to Course') }}
                        </x-actions.link>

                        <x-actions.separator />

                        {{-- Danger Zone --}}
                        <x-actions.form-button
                            action="{{ $deleteRoute }}"
                            method="DELETE"
                            confirm="{{ __('Delete this :type permanently?', ['type' => $isExam ? __('exam') : __('quiz')]) }}"
                            icon="trash"
                            :danger="true"
                        >
                            {{ __('Delete :type', ['type' => $isExam ? __('Exam') : __('Quiz')]) }}
                        </x-actions.form-button>
                    </x-actions.dropdown>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Stats Bar --}}
<div class="d-flex flex-wrap gap-4 mb-6">
    {{-- Questions by Type --}}
    @if($questionsCount > 0)
    <div class="d-flex align-items-center gap-2 px-4 py-3 bg-light rounded-3">
        <span class="text-gray-600 fs-7 fw-semibold">{{ __('Question Types:') }}</span>
        @foreach($questionTypes as $type => $count)
            @php
                $typeLabel = match($type) {
                    'mcq' => __('Multiple Choice'),
                    'true_false' => __('True/False'),
                    'short_answer' => __('Short Answer'),
                    'essay' => __('Essay'),
                    default => ucfirst(str_replace('_', ' ', $type))
                };
                $typeColor = match($type) {
                    'mcq' => 'primary',
                    'true_false' => 'info',
                    'short_answer' => 'success',
                    'essay' => 'warning',
                    default => 'secondary'
                };
            @endphp
            <span class="badge badge-light-{{ $typeColor }}">
                {{ $typeLabel }}: {{ $count }}
            </span>
        @endforeach
    </div>
    @endif

    {{-- Due Date Warning --}}
    @if($quiz->due_date)
        @php
            $isOverdue = $quiz->isOverdue();
            $isDueSoon = !$isOverdue && $quiz->due_date->diffInDays(now()) <= 3;
        @endphp
        <div class="d-flex align-items-center gap-2 px-4 py-3 rounded-3 {{ $isOverdue ? 'bg-light-danger' : ($isDueSoon ? 'bg-light-warning' : 'bg-light') }}">
            {!! getIcon('calendar', 'fs-5 ' . ($isOverdue ? 'text-danger' : ($isDueSoon ? 'text-warning' : 'text-gray-600'))) !!}
            <span class="fs-7 fw-semibold {{ $isOverdue ? 'text-danger' : ($isDueSoon ? 'text-warning' : 'text-gray-600') }}">
                {{ __('Due') }}: {{ $quiz->due_date->format('M d, Y H:i') }}
                @if($isOverdue)
                    <span class="badge badge-danger ms-2">{{ __('Overdue') }}</span>
                @elseif($isDueSoon)
                    <span class="badge badge-warning ms-2">{{ __('Due Soon') }}</span>
                @endif
            </span>
        </div>
    @endif
</div>
