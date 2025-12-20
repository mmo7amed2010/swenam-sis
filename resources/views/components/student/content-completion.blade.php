{{--
 * Content Completion Component
 *
 * A reusable completion section for lessons, quizzes, and assignments
 * with animated checkmark celebration on completion.
 *
 * @param string $type - 'lesson', 'quiz', or 'assignment'
 * @param int $itemId - The content item ID
 * @param bool $isCompleted - Whether the item is already completed
 * @param int $courseProgress - Current course progress percentage
 * @param string|null $score - Score display for quiz/assignment (e.g., "85.5 / 100")
 * @param string|null $percentage - Percentage for quiz/assignment (e.g., "85.5%")
--}}

@props([
    'type' => 'lesson',
    'itemId',
    'isCompleted' => false,
    'courseProgress' => 0,
    'score' => null,
    'percentage' => null,
])

@php
    $typeConfig = match($type) {
        'quiz' => [
            'icon' => 'question-2',
            'color' => 'info',
            'title_pending' => __('Ready to test your knowledge?'),
            'title_complete' => __('Quiz Complete!'),
            'description_pending' => __('Take this quiz to check your understanding and track your progress.'),
            'description_complete' => __('Great job! You\'ve completed this quiz.'),
            'button_text' => __('Start Quiz'),
        ],
        'assignment' => [
            'icon' => 'notepad-edit',
            'color' => 'success',
            'title_pending' => __('Ready to submit?'),
            'title_complete' => __('Assignment Complete!'),
            'description_pending' => __('Complete and submit your assignment to earn credit.'),
            'description_complete' => __('Great job! You\'ve completed this assignment.'),
            'button_text' => __('Start Submission'),
        ],
        default => [
            'icon' => 'check-square',
            'color' => 'primary',
            'title_pending' => __('Ready to continue?'),
            'title_complete' => __('Lesson Complete!'),
            'description_pending' => __('Mark this lesson as complete to track your progress and unlock what\'s next.'),
            'description_complete' => __('Great job! You\'ve completed this lesson. Keep up the momentum!'),
            'button_text' => __('Mark as Complete'),
        ],
    };
@endphp

<div class="completion-section mt-8" id="completion-section-{{ $type }}-{{ $itemId }}">
    @if($isCompleted)
        {{-- Already Completed State --}}
        <div class="card card-flush overflow-hidden completion-success-card completion-static">
            <div class="card-body py-10 px-10 text-center completion-success-gradient">
                {{-- Animated Checkmark --}}
                <div class="completion-checkmark-wrapper mb-5">
                    <svg class="completion-checkmark" viewBox="0 0 100 100">
                        <circle class="checkmark-circle" cx="50" cy="50" r="45"
                                fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="4"/>
                        <circle class="checkmark-circle-fill" cx="50" cy="50" r="45"
                                fill="none" stroke="#fff" stroke-width="4"/>
                        <path class="checkmark-check" d="M30 50 L45 65 L70 35"
                              fill="none" stroke="#fff" stroke-width="5"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h3 class="text-white fw-bolder fs-1 mb-2">{{ $typeConfig['title_complete'] }}</h3>

                @if($score)
                    <div class="score-display text-white mb-2">{{ $score }}</div>
                    @if($percentage)
                        <span class="badge bg-white bg-opacity-20 text-white fs-6 mb-4">{{ $percentage }}</span>
                    @endif
                @else
                    <p class="text-white text-opacity-85 fs-5 mb-6">
                        {{ $typeConfig['description_complete'] }}
                    </p>
                @endif

                @if($courseProgress > 0)
                    <div class="d-inline-flex align-items-center completion-progress-pill rounded-pill px-5 py-3">
                        <span class="text-white fs-6">{{ __('Course Progress:') }}</span>
                        <span class="text-white fw-bold fs-4 ms-2" data-progress-text="course">{{ $courseProgress }}%</span>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Pending Completion State --}}
        <div class="card card-flush border border-dashed border-gray-300 completion-pending" id="completion-pending-{{ $type }}-{{ $itemId }}">
            <div class="card-body py-8 px-10">
                <div class="d-flex flex-column align-items-center text-center">
                    {{-- Icon Symbol --}}
                    <div class="symbol symbol-70px symbol-circle mb-5">
                        <span class="symbol-label">
                            {!! getIcon($typeConfig['icon'], 'fs-2x text-' . $typeConfig['color']) !!}
                        </span>
                    </div>

                    {{-- Title & Description --}}
                    <h4 class="fw-bold text-gray-900 mb-2">{{ $typeConfig['title_pending'] }}</h4>
                    <p class="text-gray-600 fs-6 mb-6 mw-400px">
                        {{ $typeConfig['description_pending'] }}
                    </p>

                    {{-- Action Button (only for lessons - quizzes/assignments have their own flow) --}}
                    @if($type === 'lesson')
                        <button class="btn btn-primary btn-lg px-10"
                                id="mark-complete-btn"
                                data-completed="false"
                                data-lesson-id="{{ $itemId }}">
                            {!! getIcon('check', 'fs-3 me-2') !!}
                            {{ $typeConfig['button_text'] }}
                        </button>
                    @endif

                    {{-- Helper Text --}}
                    <div class="text-muted fs-7 mt-5">
                        {!! getIcon('information-2', 'fs-7 me-1') !!}
                        {{ __('You can revisit this content anytime') }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom/admin/courses/content-viewer.css') }}">
@endpush
