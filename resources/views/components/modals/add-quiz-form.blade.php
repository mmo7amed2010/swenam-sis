{{--
 * Shared Add Quiz/Exam Modal Component
 *
 * AJAX-based modal for creating new quizzes or exams with assessment type selection.
 * Used by both admin and instructor views with context-specific routing.
 *
 * Exams in a module are displayed sequentially - first exam is always visible,
 * subsequent exams become visible only after failing the previous one.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
--}}

@props(['context', 'program', 'course', 'module'])

@php
    $isAdmin = $context === 'admin';
    $action = $isAdmin
        ? route('admin.programs.courses.quizzes.store', [$program, $course])
        : route('instructor.courses.quizzes.store', [$program, $course]);
    $refreshUrl = $isAdmin
        ? route('admin.programs.courses.modules.content', [$program, $course, $module])
        : route('instructor.courses.modules.content', [$program, $course, $module]);
@endphp

<x-modals.ajax-form
    id="kt_modal_add_quiz_{{ $module->id }}"
    title="{{ __('Add Quiz/Exam') }} - {{ $module->title }}"
    :action="$action"
    method="POST"
    size="lg"
    targetContainer="#module-content-{{ $module->id }}"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Quiz created successfully') }}"
    submitLabel="{{ __('Create') }}"
>
    <input type="hidden" name="module_id" value="{{ $module->id }}">
    <input type="hidden" name="scope" value="module">

    {{-- Assessment Type Selection --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Assessment Type') }}</label>
        <div class="d-flex gap-4">
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input assessment-type-radio" type="radio"
                       name="assessment_type" value="quiz" checked
                       data-module-id="{{ $module->id }}" />
                <span class="form-check-label fw-semibold">
                    {!! getIcon('question-2', 'fs-4 me-2 text-info') !!}
                    {{ __('Quiz') }}
                </span>
            </label>
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input assessment-type-radio" type="radio"
                       name="assessment_type" value="exam"
                       data-module-id="{{ $module->id }}" />
                <span class="form-check-label fw-semibold">
                    {!! getIcon('award', 'fs-4 me-2 text-danger') !!}
                    {{ __('Module Exam') }}
                </span>
            </label>
        </div>
        <div class="text-muted fs-7 mt-2">
            <span class="quiz-hint-{{ $module->id }}">{{ __('Quizzes are lesson-level assessments.') }}</span>
            <span class="exam-hint-{{ $module->id }} d-none">{{ __('Exams are high-stakes module-level assessments. Multiple exams in a module are shown sequentially - students see the next exam only after failing the previous one.') }}</span>
        </div>
    </div>

    {{-- Title --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control"
               placeholder="{{ __('E.g., Chapter 1 Quiz or Module Final Exam') }}" required />
    </div>

    {{-- Description --}}
    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" class="form-control" rows="3"
                  placeholder="{{ __('Optional instructions or description...') }}"></textarea>
    </div>

    <div class="row">
        {{-- Passing Score --}}
        <div class="col-md-6 mb-10 fv-row">
            <label class="required form-label">{{ __('Passing Score (%)') }}</label>
            <input type="number" name="passing_score" class="form-control"
                   min="0" max="100" value="70" required />
        </div>
    </div>

    {{-- Show Correct Answers --}}
    <div class="mb-10 fv-row">
        <div class="form-check form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox"
                   name="show_correct_answers" value="1" id="show_correct_answers_{{ $module->id }}" checked />
            <label class="form-check-label" for="show_correct_answers_{{ $module->id }}">
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
                       name="shuffle_questions" value="1" id="shuffle_questions_{{ $module->id }}" />
                <label class="form-check-label" for="shuffle_questions_{{ $module->id }}">
                    {{ __('Shuffle Questions') }}
                </label>
            </div>
        </div>
        <div class="col-md-6 mb-10 fv-row">
            <div class="form-check form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox"
                       name="shuffle_answers" value="1" id="shuffle_answers_{{ $module->id }}" />
                <label class="form-check-label" for="shuffle_answers_{{ $module->id }}">
                    {{ __('Shuffle Answer Options') }}
                </label>
            </div>
        </div>
    </div>

    {{-- Published Toggle --}}
    <div class="fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox"
                   name="published" value="1" id="published_{{ $module->id }}" />
            <label class="form-check-label" for="published_{{ $module->id }}">
                {{ __('Publish immediately') }}
            </label>
        </div>
        <div class="text-muted fs-7 mt-1">{{ __('If unchecked, quiz will be saved as draft.') }}</div>
    </div>
</x-modals.ajax-form>

@push('scripts')
<script>
(function() {
    const moduleId = '{{ $module->id }}';

    // Toggle hints when assessment type changes
    document.querySelectorAll(`input[name="assessment_type"][data-module-id="${moduleId}"]`).forEach(radio => {
        radio.addEventListener('change', function() {
            const isExam = this.value === 'exam';

            // Toggle hints
            const quizHint = document.querySelector(`.quiz-hint-${moduleId}`);
            const examHint = document.querySelector(`.exam-hint-${moduleId}`);
            if (quizHint && examHint) {
                quizHint.classList.toggle('d-none', isExam);
                examHint.classList.toggle('d-none', !isExam);
            }
        });
    });
})();
</script>
@endpush
