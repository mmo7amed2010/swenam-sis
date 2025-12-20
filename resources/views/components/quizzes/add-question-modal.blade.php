{{--
 * Add Question Modal Component
 *
 * AJAX-based modal for creating new quiz questions.
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
    $modalId = 'kt_modal_add_question_' . $quiz->id;

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $storeRoute = route("{$routePrefix}.quizzes.questions.store", [$program, $course, $quiz]);
    $questionsListRoute = route("{$routePrefix}.quizzes.questions-list", [$program, $course, $quiz]);
@endphp

<x-modals.ajax-form
    id="{{ $modalId }}"
    title="{{ __('Add Question') }} - {{ $quiz->title }}"
    :action="$storeRoute"
    method="POST"
    size="lg"
    targetContainer="#questionsCard"
    :refreshUrl="$questionsListRoute"
    successMessage="{{ __('Question added successfully') }}"
    submitLabel="{{ __('Add Question') }}"
    :resetOnSuccess="true"
>
    {{-- Question Type Selection --}}
    <div class="mb-8">
        <label class="required form-label fw-semibold">{{ __('Question Type') }}</label>
        <div class="d-flex gap-4">
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input question-type-radio"
                       type="radio"
                       name="question_type"
                       value="mcq"
                       checked
                       data-modal-id="{{ $modalId }}" />
                <span class="form-check-label fw-semibold">
                    {!! getIcon('check-circle', 'fs-4 me-2 text-primary') !!}
                    {{ __('Multiple Choice') }}
                </span>
            </label>
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input question-type-radio"
                       type="radio"
                       name="question_type"
                       value="true_false"
                       data-modal-id="{{ $modalId }}" />
                <span class="form-check-label fw-semibold">
                    {!! getIcon('toggle-on-circle', 'fs-4 me-2 text-info') !!}
                    {{ __('True/False') }}
                </span>
            </label>
        </div>
    </div>

    {{-- Question Text --}}
    <div class="mb-8 fv-row">
        <label class="required form-label">{{ __('Question Text') }}</label>
        <textarea name="question_text"
                  class="form-control"
                  rows="4"
                  placeholder="{{ __('Enter your question here...') }}"
                  required></textarea>
        <div class="text-muted fs-7 mt-1">{{ __('Write a clear and specific question') }}</div>
    </div>

    {{-- Points --}}
    <div class="mb-8 fv-row">
        <label class="required form-label">{{ __('Points') }}</label>
        <div class="input-group" style="max-width: 150px;">
            <input type="number"
                   name="points"
                   class="form-control"
                   min="1"
                   max="100"
                   value="1"
                   required />
            <span class="input-group-text">{{ __('pts') }}</span>
        </div>
        <div class="text-muted fs-7 mt-1">{{ __('Points for correct answer (1-100)') }}</div>
    </div>

    {{-- MCQ Answers Section --}}
    <div id="mcq-section-{{ $modalId }}" class="mcq-section">
        <div class="mb-4">
            <label class="form-label fw-semibold">{{ __('Answer Options') }}</label>
            <div class="text-muted fs-7 mb-3">{{ __('Add 2-6 answer options. Mark at least one as correct.') }}</div>
        </div>

        <div class="answers-container d-flex flex-column gap-3"
             id="answers-container-{{ $modalId }}"
             data-modal-id="{{ $modalId }}"
             data-min-answers="2"
             data-max-answers="6"
             data-placeholder="{{ __('Enter answer option') }}"
             data-correct-label="{{ __('Correct') }}"
             data-remove-label="{{ __('Remove') }}">
            {{-- Answer Option 1 --}}
            <div class="answer-row d-flex gap-3 align-items-center" data-index="0" data-answer-row="true">
                <div class="d-flex align-items-center justify-content-center w-30px h-30px rounded-circle bg-light-primary flex-shrink-0">
                    <span class="fw-bold fs-7 text-primary answer-letter">A</span>
                </div>
                <div class="flex-grow-1">
                    <input type="text"
                           class="form-control form-control-solid"
                           name="answers[0][text]"
                           placeholder="{{ __('Enter answer option') }}"
                           required />
                </div>
                <div class="form-check form-check-custom form-check-success form-check-solid">
                    <input type="hidden" name="answers[0][is_correct]" value="0">
                    <input class="form-check-input correct-checkbox"
                           type="checkbox"
                           name="answers[0][is_correct]"
                           value="1"
                           id="correct-{{ $modalId }}-0">
                    <label class="form-check-label text-success fs-8" for="correct-{{ $modalId }}-0">
                        {{ __('Correct') }}
                    </label>
                </div>
                <button type="button"
                        class="btn btn-sm btn-icon btn-light-danger btn-remove-answer d-none"
                        title="{{ __('Remove') }}">
                    {!! getIcon('trash', 'fs-5') !!}
                </button>
            </div>

            {{-- Answer Option 2 --}}
            <div class="answer-row d-flex gap-3 align-items-center" data-index="1" data-answer-row="true">
                <div class="d-flex align-items-center justify-content-center w-30px h-30px rounded-circle bg-light-primary flex-shrink-0">
                    <span class="fw-bold fs-7 text-primary answer-letter">B</span>
                </div>
                <div class="flex-grow-1">
                    <input type="text"
                           class="form-control form-control-solid"
                           name="answers[1][text]"
                           placeholder="{{ __('Enter answer option') }}"
                           required />
                </div>
                <div class="form-check form-check-custom form-check-success form-check-solid">
                    <input type="hidden" name="answers[1][is_correct]" value="0">
                    <input class="form-check-input correct-checkbox"
                           type="checkbox"
                           name="answers[1][is_correct]"
                           value="1"
                           id="correct-{{ $modalId }}-1">
                    <label class="form-check-label text-success fs-8" for="correct-{{ $modalId }}-1">
                        {{ __('Correct') }}
                    </label>
                </div>
                <button type="button"
                        class="btn btn-sm btn-icon btn-light-danger btn-remove-answer d-none"
                        title="{{ __('Remove') }}">
                    {!! getIcon('trash', 'fs-5') !!}
                </button>
            </div>
        </div>

        <button type="button"
                class="btn btn-sm btn-light-primary mt-4 btn-add-answer"
                data-container="#answers-container-{{ $modalId }}"
                data-modal-id="{{ $modalId }}"
                data-max-answers="6">
            {!! getIcon('plus', 'fs-5 me-1') !!}
            {{ __('Add Answer Option') }}
        </button>

        {{-- Hidden field to validate at least one correct answer --}}
        <input type="hidden" name="has_correct_answer" id="has_correct_answer_{{ $modalId }}" value="0">
    </div>

    {{-- True/False Section --}}
    <div id="true-false-section-{{ $modalId }}" class="true-false-section d-none">
        <div class="mb-4">
            <label class="required form-label fw-semibold">{{ __('Correct Answer') }}</label>
        </div>

        <div class="d-flex gap-4">
            <label class="form-check form-check-custom form-check-solid form-check-lg bg-light rounded px-5 py-4 w-50 cursor-pointer true-false-option">
                <input class="form-check-input"
                       type="radio"
                       name="correct_answer"
                       value="1"
                       id="true-answer-{{ $modalId }}">
                <span class="form-check-label d-flex align-items-center gap-2">
                    {!! getIcon('check-circle', 'fs-3 text-success') !!}
                    <span class="fw-semibold fs-5">{{ __('True') }}</span>
                </span>
            </label>

            <label class="form-check form-check-custom form-check-solid form-check-lg bg-light rounded px-5 py-4 w-50 cursor-pointer true-false-option">
                <input class="form-check-input"
                       type="radio"
                       name="correct_answer"
                       value="0"
                       id="false-answer-{{ $modalId }}">
                <span class="form-check-label d-flex align-items-center gap-2">
                    {!! getIcon('cross-circle', 'fs-3 text-danger') !!}
                    <span class="fw-semibold fs-5">{{ __('False') }}</span>
                </span>
            </label>
        </div>
    </div>

    {{-- Validation Note --}}
    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mt-6">
        <div class="d-flex flex-stack flex-grow-1">
            {!! getIcon('information', 'fs-2 text-warning me-3') !!}
            <div class="fs-7 text-gray-700">
                <span class="mcq-note">{{ __('For multiple choice questions, mark at least one answer as correct.') }}</span>
                <span class="tf-note d-none">{{ __('Select whether the statement is True or False.') }}</span>
            </div>
        </div>
    </div>
</x-modals.ajax-form>
