{{--
 * Questions List Component
 *
 * Displays the list of questions with reordering capability.
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
    $questions = $quiz->questions;
    $questionsCount = $questions->count();

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $reorderRoute = route("{$routePrefix}.quizzes.questions.reorder", [$program, $course, $quiz]);
@endphp

<div class="card card-flush" id="questionsCard">
    {{-- Questions Header --}}
    <div class="card-header py-5 border-bottom">
        <div class="card-title d-flex align-items-center gap-3">
            <h3 class="fs-4 fw-bold m-0">
                {!! getIcon('questionnaire-tablet', 'fs-3 me-2 text-primary') !!}
                {{ __('Questions') }}
            </h3>
            <span class="badge badge-light-primary fs-7">{{ $questionsCount }}</span>
        </div>
        <div class="card-toolbar d-flex align-items-center gap-3">
            {{-- Total Points Display --}}
            <div class="d-flex align-items-center gap-2 px-4 py-2 bg-light-warning rounded">
                {!! getIcon('star', 'fs-5 text-warning') !!}
                <span class="fw-bold text-gray-800">{{ $quiz->total_points }}</span>
                <span class="text-gray-600 fs-7">{{ __('pts total') }}</span>
            </div>

            {{-- Reorder Toggle --}}
            @if($questionsCount > 1)
            <button type="button" class="btn btn-sm btn-light-info btn-reorder-toggle"
                    data-reorder-list="#questionsList"
                    title="{{ __('Drag to reorder questions') }}">
                {!! getIcon('menu', 'fs-5 me-1') !!}
                {{ __('Reorder') }}
            </button>
            @endif

            {{-- Add Question --}}
            <button type="button"
                    class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#kt_modal_add_question_{{ $quiz->id }}">
                {!! getIcon('plus', 'fs-5 me-1') !!}
                {{ __('Add Question') }}
            </button>
        </div>
    </div>

    {{-- Questions List --}}
    <div class="card-body p-0">
        <div id="questionsList"
             class="questions-list {{ $questionsCount === 0 ? 'questions-list--empty' : '' }}"
             data-reorder-url="{{ $reorderRoute }}">
            @if($questionsCount > 0)
                @foreach($questions as $question)
                    <x-quizzes.question-card
                        :context="$context"
                        :program="$program"
                        :course="$course"
                        :quiz="$quiz"
                        :question="$question"
                        :index="$loop->index"
                    />
                @endforeach
            @else
                {{-- Empty State --}}
                <div class="text-center py-15">
                    <div class="mb-5">
                        <div class="symbol symbol-80px mb-3">
                            <span class="symbol-label bg-light-primary">
                                {!! getIcon('questionnaire-tablet', 'fs-2x text-primary') !!}
                            </span>
                        </div>
                    </div>
                    <h4 class="fw-bold text-gray-800 mb-2">{{ __('No Questions Yet') }}</h4>
                    <p class="text-gray-600 fs-6 mb-7 mw-400px mx-auto">
                        {{ __('Add questions to this :type. Students will see them when they take the assessment.', ['type' => $quiz->isExam() ? __('exam') : __('quiz')]) }}
                    </p>
                    <button type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_question_{{ $quiz->id }}">
                        {!! getIcon('plus', 'fs-4 me-2') !!}
                        {{ __('Add First Question') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Questions Footer Summary --}}
    @if($questionsCount > 0)
    <div class="card-footer py-4 bg-gray-50">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex flex-wrap gap-4 text-gray-600 fs-7">
                <span>
                    <strong data-quiz-questions-count>{{ $questionsCount }}</strong> {{ __('questions') }}
                </span>
                <span class="border-start ps-4">
                    <strong data-quiz-total-points>{{ $quiz->total_points }}</strong> {{ __('total points') }}
                </span>
                @if($quiz->passing_score)
                <span class="border-start ps-4" data-quiz-passing-score="{{ $quiz->passing_score }}">
                    <strong data-quiz-passing-points>{{ round($quiz->total_points * $quiz->passing_score / 100) }}</strong> {{ __('pts to pass') }}
                    <span class="text-muted">({{ $quiz->passing_score }}%)</span>
                </span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <button type="button"
                        class="btn btn-sm btn-light-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#kt_modal_add_question_{{ $quiz->id }}">
                    {!! getIcon('plus', 'fs-5 me-1') !!}
                    {{ __('Add Question') }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
