<x-default-layout>

    @section('title')
        {{ __('Students') }} - {{ $course->course_code }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => __('Students')]
        ]" />
    @endsection

    <x-courses.students-index context="admin" :program="$program" :course="$course" :stats="$stats" />

</x-default-layout>
