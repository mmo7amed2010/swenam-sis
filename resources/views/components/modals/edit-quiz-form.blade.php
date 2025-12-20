{{--
 * Shared Edit Quiz/Exam Modal Component
 *
 * AJAX-based modal for editing quizzes or exams.
 * Used by both admin and instructor views with context-specific routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
 * @param \App\Models\CourseModule $module (optional)
--}}

@props(['context', 'program', 'course', 'quiz', 'module' => null])

@php
    $isAdmin = $context === 'admin';
    $isExam = $quiz->assessment_type === 'exam';
    $isModuleExam = $quiz->scope === 'module' || $isExam;
    $moduleForRefresh = $module ?? $quiz->module;

    $action = $isAdmin
        ? route('admin.programs.courses.quizzes.update', [$program, $course, $quiz])
        : route('instructor.courses.quizzes.update', [$program, $course, $quiz]);
    $refreshUrl = $moduleForRefresh
        ? ($isAdmin
            ? route('admin.programs.courses.modules.content', [$program, $course, $moduleForRefresh])
            : route('instructor.courses.modules.content', [$program, $course, $moduleForRefresh]))
        : null;
@endphp

<x-modals.ajax-form
    id="kt_modal_edit_quiz_{{ $quiz->id }}"
    title="{{ __('Edit :type', ['type' => $isExam ? __('Exam') : __('Quiz')]) }}"
    :action="$action"
    method="PUT"
    size="lg"
    :targetContainer="$moduleForRefresh ? '#module-content-' . $moduleForRefresh->id : null"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Quiz updated successfully') }}"
    submitLabel="{{ __('Update') }}"
>
    <input type="hidden" name="scope" value="{{ $quiz->scope }}">
    @if($quiz->module_id)
    <input type="hidden" name="module_id" value="{{ $quiz->module_id }}">
    @endif

    @if($isExam)
    <div class="alert alert-info border-2 mb-5">
        <div class="d-flex align-items-center">
            {!! getIcon('award', 'fs-2 text-info me-3') !!}
            <div>
                <h6 class="mb-1">{{ __('Editing Module Exam') }}</h6>
                <p class="mb-0 small">{{ __('This exam unlocks the next module when passed.') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Assessment Type Selection (only for non-module quizzes) --}}
    @if(!$isModuleExam)
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Assessment Type') }}</label>
        <div class="d-flex gap-4">
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input assessment-type-radio" type="radio"
                       name="assessment_type" value="quiz"
                       {{ !$isExam ? 'checked' : '' }}
                       data-quiz-id="{{ $quiz->id }}" />
                <span class="form-check-label fw-semibold">
                    {!! getIcon('question-2', 'fs-4 me-2 text-info') !!}
                    {{ __('Quiz') }}
                </span>
            </label>
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input assessment-type-radio" type="radio"
                       name="assessment_type" value="exam"
                       {{ $isExam ? 'checked' : '' }}
                       data-quiz-id="{{ $quiz->id }}" />
                <span class="form-check-label fw-semibold">
                    {!! getIcon('award', 'fs-4 me-2 text-danger') !!}
                    {{ __('Exam') }}
                </span>
            </label>
        </div>
        <div class="text-muted fs-7 mt-2">
            <span class="quiz-hint-edit-{{ $quiz->id }} {{ $isExam ? 'd-none' : '' }}">{{ __('Quizzes are lesson-level assessments.') }}</span>
            <span class="exam-hint-edit-{{ $quiz->id }} {{ !$isExam ? 'd-none' : '' }}">{{ __('Exams are high-stakes module-level assessments.') }}</span>
        </div>
    </div>
    @else
    <input type="hidden" name="assessment_type" value="{{ $quiz->assessment_type }}">
    @endif

    {{-- Title --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control"
               placeholder="{{ __('E.g., Chapter 1 Quiz or Module Final Exam') }}"
               value="{{ old('title', $quiz->title) }}" required />
    </div>

    {{-- Description --}}
    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" class="form-control" rows="3"
                  placeholder="{{ __('Optional instructions or description...') }}">{{ old('description', $quiz->description) }}</textarea>
    </div>

    <div class="row">
        {{-- Passing Score --}}
        <div class="col-md-6 mb-10 fv-row">
            <label class="required form-label">{{ __('Passing Score (%)') }}</label>
            <input type="number" name="passing_score" class="form-control"
                   min="0" max="100" value="{{ old('passing_score', $quiz->passing_score) }}" required />
        </div>
    </div>

    {{-- Show Correct Answers --}}
    <div class="mb-10 fv-row">
        <div class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox"
                   name="show_correct_answers" value="1" id="show_correct_answers_edit_{{ $quiz->id }}"
                   {{ old('show_correct_answers', $quiz->show_correct_answers) === 'after_each_attempt' ? 'checked' : '' }} />
            <label class="form-check-label" for="show_correct_answers_edit_{{ $quiz->id }}">
                {{ __('Show Correct Answers After Submission') }}
            </label>
        </div>
        <div class="text-muted fs-7 mt-1">{{ __('When enabled, students will see correct answers after submitting') }}</div>
    </div>

    {{-- Options Row --}}
    <div class="row">
        <div class="col-md-6 mb-10 fv-row">
            <div class="form-check form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox"
                       name="shuffle_questions" value="1" id="shuffle_questions_edit_{{ $quiz->id }}"
                       {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }} />
                <label class="form-check-label" for="shuffle_questions_edit_{{ $quiz->id }}">
                    {{ __('Shuffle Questions') }}
                </label>
            </div>
        </div>
        <div class="col-md-6 mb-10 fv-row">
            <div class="form-check form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox"
                       name="shuffle_answers" value="1" id="shuffle_answers_edit_{{ $quiz->id }}"
                       {{ old('shuffle_answers', $quiz->shuffle_answers) ? 'checked' : '' }} />
                <label class="form-check-label" for="shuffle_answers_edit_{{ $quiz->id }}">
                    {{ __('Shuffle Answer Options') }}
                </label>
            </div>
        </div>
    </div>

    {{-- Published Toggle --}}
    <div class="fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox"
                   name="published" value="1" id="published_edit_{{ $quiz->id }}"
                   {{ old('published', $quiz->published ?? $quiz->is_published) ? 'checked' : '' }} />
            <label class="form-check-label" for="published_edit_{{ $quiz->id }}">
                {{ __('Published') }}
            </label>
        </div>
        <div class="text-muted fs-7 mt-1">{{ __('Make available to students') }}</div>
    </div>
</x-modals.ajax-form>

@push('scripts')
<script>
(function() {
    const quizId = '{{ $quiz->id }}';

    // Toggle hints when assessment type changes
    document.querySelectorAll(`input[name="assessment_type"][data-quiz-id="${quizId}"]`).forEach(radio => {
        radio.addEventListener('change', function() {
            const isExam = this.value === 'exam';

            // Toggle hints
            const quizHint = document.querySelector(`.quiz-hint-edit-${quizId}`);
            const examHint = document.querySelector(`.exam-hint-edit-${quizId}`);
            if (quizHint && examHint) {
                quizHint.classList.toggle('d-none', isExam);
                examHint.classList.toggle('d-none', !isExam);
            }
        });
    });
})();
</script>
@endpush
