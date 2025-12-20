<x-default-layout>

    @section('title')
        {{ __('Quiz Attempts') }} - {{ $quiz->title }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $quiz->title, 'url' => route('admin.programs.courses.quizzes.show', [$program, $course, $quiz])],
            ['title' => __('Attempts')]
        ]" />
    @endsection

    <x-quizzes.attempts-list
        context="admin"
        :program="$program"
        :course="$course"
        :quiz="$quiz"
        :stats="$stats"
    />

</x-default-layout>
