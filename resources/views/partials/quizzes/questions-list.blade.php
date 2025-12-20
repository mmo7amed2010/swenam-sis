{{--
 * Shared Questions List Partial for AJAX Refresh
 *
 * This partial wraps the shared component for use in AJAX responses.
 * Both admin and instructor controllers use this same partial with different context values.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
--}}

<x-quizzes.questions-list
    :context="$context"
    :program="$program"
    :course="$course"
    :quiz="$quiz"
/>

{{-- Edit Question Modals (needed for AJAX refresh to work properly) --}}
@foreach($quiz->questions as $question)
    <x-quizzes.edit-question-modal
        :context="$context"
        :program="$program"
        :course="$course"
        :quiz="$quiz"
        :question="$question"
    />
@endforeach
