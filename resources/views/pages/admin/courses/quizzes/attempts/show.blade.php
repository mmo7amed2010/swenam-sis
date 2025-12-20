<x-default-layout>

    @section('title')
        {{ __('Attempt Details') }} - {{ $attempt->student->name ?? __('Unknown') }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $quiz->title, 'url' => route('admin.programs.courses.quizzes.show', [$program, $course, $quiz])],
            ['title' => __('Attempts'), 'url' => route('admin.programs.courses.quizzes.attempts', [$program, $course, $quiz])],
            ['title' => $attempt->student->name ?? __('Attempt Details')]
        ]" />
    @endsection

    <x-quizzes.attempt-detail
        context="admin"
        :program="$program"
        :course="$course"
        :quiz="$quiz"
        :attempt="$attempt"
        :questions="$questions"
        :studentAnswers="$studentAnswers"
    />

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/custom/admin/courses/quiz-results-metronic.css') }}">
    @endpush

</x-default-layout>
