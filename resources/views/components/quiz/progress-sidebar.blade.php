{{--
/**
 * Quiz Progress Sidebar Component
 *
 * Displays quiz progress and question navigation
 * Following Metronic design system patterns
 *
 * @param int $currentQuestion Current question index
 * @param int $totalQuestions Total number of questions
 * @param int $answeredCount Number of answered questions
 * @param Collection $questions Question collection
 * @param array $studentAnswers Student's answers
 */
--}}

@props([
    'currentQuestion' => 0,
    'totalQuestions' => 0,
    'answeredCount' => 0,
    'questions' => collect(),
    'studentAnswers' => [],
])

<div {{ $attributes->merge(['class' => 'card card-flush position-sticky', 'style' => 'top: 20px;']) }}>
    {{-- Progress Header --}}
    <div class="card-header pt-7">
        <div class="card-title flex-column w-100">
            <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                <h3 class="fw-bold text-gray-900 m-0">{{ __('Progress') }}</h3>
                <span class="badge badge-light-primary fs-7 fw-bold" id="progressPercentage">
                    {{ $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0 }}%
                </span>
            </div>
            <span class="text-gray-500 fw-semibold fs-7">
                <span id="progressCount">{{ $answeredCount }}</span> {{ __('of') }} {{ $totalQuestions }} {{ __('completed') }}
            </span>
        </div>
    </div>

    <div class="card-body pt-5">
        {{-- Progress Bar --}}
        <div class="mb-7">
            <div class="h-8px bg-light rounded overflow-hidden">
                <div
                    class="bg-primary h-100 rounded"
                    role="progressbar"
                    id="progressBar"
                    style="width: {{ $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0 }}%;"
                    aria-valuenow="{{ $answeredCount }}"
                    aria-valuemin="0"
                    aria-valuemax="{{ $totalQuestions }}"
                    aria-label="{{ __('Quiz progress') }}">
                </div>
            </div>
        </div>

        {{-- Section Divider --}}
        <div class="separator separator-dashed my-6"></div>

        {{-- Questions Navigation --}}
        <div class="d-flex align-items-center justify-content-between mb-5">
            <h4 class="fw-bold text-gray-900 m-0">{{ __('Questions') }}</h4>
            <span class="badge badge-light fs-8">{{ $totalQuestions }} {{ __('total') }}</span>
        </div>

        {{-- Visual Question Grid --}}
        <div class="question-grid" role="navigation" aria-label="{{ __('Question navigation') }}">
            @foreach($questions as $idx => $question)
            @php
                $isAnswered = isset($studentAnswers[$question->id]) &&
                              !empty($studentAnswers[$question->id]);
                $isCurrent = $idx === $currentQuestion;
            @endphp

            <button
                type="button"
                class="question-item {{ $isCurrent ? 'current' : '' }} {{ $isAnswered ? 'answered' : 'unanswered' }}"
                data-question-index="{{ $idx }}"
                data-question-id="{{ $question->id }}"
                onclick="QuizApp.navigateToQuestion({{ $idx }})"
                aria-label="{{ __('Question :num', ['num' => $idx + 1]) }} - {{ $isAnswered ? __('Answered') : __('Not answered') }}"
                aria-current="{{ $isCurrent ? 'true' : 'false' }}"
                title="{{ __('Question :num', ['num' => $idx + 1]) }} ({{ $question->points }} {{ __('pts') }})">

                {{-- Question Number Circle --}}
                <div class="question-circle">
                    <span class="question-number">{{ $idx + 1 }}</span>
                    <i class="ki-duotone ki-check check-icon">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>

                {{-- Status Indicator Dot --}}
                <span class="status-dot"></span>
            </button>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="mt-6 pt-5 border-top border-gray-200">
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-primary me-3"></span>
                    <span class="fs-7 text-gray-700">{{ __('Current Question') }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-success me-3"></span>
                    <span class="fs-7 text-gray-700">{{ __('Answered') }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend-dot bg-gray-300 me-3"></span>
                    <span class="fs-7 text-gray-700">{{ __('Not Answered') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

