{{--
 * Question Card Component
 *
 * Displays a single question with its answers and actions.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
 * @param \App\Models\QuizQuestion $question
 * @param int $index - Zero-based index
--}}

@props(['context', 'program', 'course', 'quiz', 'question', 'index'])

@php
    $isAdmin = $context === 'admin';
    $questionNumber = $question->order_number ?? ($index + 1);
    $questionTypes = [
        'mcq' => ['label' => __('Multiple Choice'), 'icon' => 'check-circle', 'color' => 'primary'],
        'true_false' => ['label' => __('True/False'), 'icon' => 'toggle-on-circle', 'color' => 'info'],
        'short_answer' => ['label' => __('Short Answer'), 'icon' => 'text', 'color' => 'success'],
        'essay' => ['label' => __('Essay'), 'icon' => 'document', 'color' => 'warning'],
        'matching' => ['label' => __('Matching'), 'icon' => 'arrows-loop', 'color' => 'secondary'],
        'fill_blank' => ['label' => __('Fill in Blank'), 'icon' => 'text-align-left', 'color' => 'dark'],
    ];
    $typeInfo = $questionTypes[$question->question_type] ?? ['label' => ucfirst(str_replace('_', ' ', $question->question_type)), 'icon' => 'question', 'color' => 'secondary'];

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $deleteRoute = route("{$routePrefix}.quizzes.questions.destroy", [$program, $course, $quiz, $question]);
@endphp

<div class="question-card border-bottom" data-question-id="{{ $question->id }}">
    <div class="d-flex p-5 hover-bg-light-primary transition-all">
        {{-- Drag Handle (shown in reorder mode) --}}
        <div class="drag-handle d-none me-3 cursor-grab align-self-start pt-1">
            {!! getIcon('dots-vertical', 'fs-4 text-gray-400') !!}
        </div>

        {{-- Question Number Badge --}}
        <div class="flex-shrink-0 me-4">
            <div class="d-flex align-items-center justify-content-center w-45px h-45px rounded-circle bg-light-{{ $typeInfo['color'] }}">
                <span class="fw-bold fs-5 text-{{ $typeInfo['color'] }}">{{ $questionNumber }}</span>
            </div>
        </div>

        {{-- Question Content --}}
        <div class="flex-grow-1 min-w-0">
            {{-- Question Header --}}
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="badge badge-light-{{ $typeInfo['color'] }} fs-8">
                    {!! getIcon($typeInfo['icon'], 'fs-8 me-1') !!}
                    {{ $typeInfo['label'] }}
                </span>
                <span class="badge badge-light-warning fs-8">
                    {!! getIcon('star', 'fs-8 me-1') !!}
                    {{ $question->points }} {{ __('pts') }}
                </span>
            </div>

            {{-- Question Text --}}
            <div class="question-text text-gray-900 fs-6 mb-4">
                {!! $question->question_text !!}
            </div>

            {{-- Answers Section --}}
            @if($question->question_type === 'mcq')
                <div class="answers-list d-flex flex-column gap-2">
                    @foreach($question->answers_json ?? [] as $answerIndex => $answer)
                        @php
                            $isCorrect = $answer['is_correct'] ?? false;
                            $letterCode = chr(65 + $answerIndex);
                        @endphp
                        <div class="answer-item d-flex align-items-start gap-2 p-3 rounded {{ $isCorrect ? 'bg-light-success border border-success border-dashed' : 'bg-light' }}">
                            <span class="badge {{ $isCorrect ? 'badge-success' : 'badge-light' }} fw-bold fs-8 mt-1">
                                {{ $letterCode }}
                            </span>
                            <span class="text-gray-800 {{ $isCorrect ? 'fw-semibold' : '' }}">
                                {{ $answer['text'] ?? '' }}
                            </span>
                            @if($isCorrect)
                                <span class="badge badge-success fs-9 ms-auto">
                                    {!! getIcon('check', 'fs-9 me-1') !!}
                                    {{ __('Correct') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif($question->question_type === 'true_false')
                @php
                    $correctAnswer = $question->answers_json['correct'] ?? false;
                @endphp
                <div class="d-flex gap-3">
                    <div class="d-flex align-items-center gap-2 px-4 py-2 rounded {{ $correctAnswer ? 'bg-light-success border border-success border-dashed' : 'bg-light' }}">
                        {!! getIcon('check-circle', 'fs-5 ' . ($correctAnswer ? 'text-success' : 'text-gray-400')) !!}
                        <span class="{{ $correctAnswer ? 'fw-semibold text-success' : 'text-gray-600' }}">{{ __('True') }}</span>
                        @if($correctAnswer)
                            <span class="badge badge-success fs-9">{{ __('Correct') }}</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2 px-4 py-2 rounded {{ !$correctAnswer ? 'bg-light-success border border-success border-dashed' : 'bg-light' }}">
                        {!! getIcon('cross-circle', 'fs-5 ' . (!$correctAnswer ? 'text-success' : 'text-gray-400')) !!}
                        <span class="{{ !$correctAnswer ? 'fw-semibold text-success' : 'text-gray-600' }}">{{ __('False') }}</span>
                        @if(!$correctAnswer)
                            <span class="badge badge-success fs-9">{{ __('Correct') }}</span>
                        @endif
                    </div>
                </div>
            @elseif($question->question_type === 'short_answer')
                <div class="bg-light rounded p-3">
                    <div class="text-gray-500 fs-8 mb-1">{{ __('Expected Answer:') }}</div>
                    <div class="text-gray-800 fw-semibold">
                        {{ $question->answers_json['expected'] ?? __('Manual grading required') }}
                    </div>
                </div>
            @elseif($question->question_type === 'essay')
                <div class="bg-light rounded p-3">
                    <div class="d-flex align-items-center gap-2 text-gray-600 fs-7">
                        {!! getIcon('document', 'fs-5 text-warning') !!}
                        {{ __('Essay response - Manual grading required') }}
                    </div>
                    @if(isset($question->settings_json['word_limit']))
                        <div class="text-muted fs-8 mt-1">
                            {{ __('Word limit: :limit', ['limit' => $question->settings_json['word_limit']]) }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="question-actions d-flex align-items-start gap-2 ms-4 flex-shrink-0">
            <button type="button"
               class="btn btn-sm btn-icon btn-light-primary"
               data-bs-toggle="modal"
               data-bs-target="#kt_modal_edit_question_{{ $question->id }}"
               title="{{ __('Edit Question') }}">
                {!! getIcon('pencil', 'fs-5') !!}
            </button>
            <form action="{{ $deleteRoute }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('{{ __('Delete this question?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="{{ __('Delete Question') }}">
                    {!! getIcon('trash', 'fs-5') !!}
                </button>
            </form>
        </div>
    </div>
</div>
