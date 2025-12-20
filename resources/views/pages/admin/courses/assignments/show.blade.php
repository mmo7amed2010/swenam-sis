<x-default-layout>

    @section('title')
        {{ $assignment->title }} - {{ $course->course_code }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $assignment->title]
        ]" />
    @endsection

    <!--begin::Toolbar-->
    <x-assignments.show-toolbar
        :assignment="$assignment"
        :program="$program"
        :course="$course"
        context="admin"
    />
    <!--end::Toolbar-->

    <div class="row g-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-xl-8">
            <!--begin::Assignment Details-->
            <x-assignments.show-details :assignment="$assignment" />
            <!--end::Assignment Details-->

            <!--begin::Submissions-->
            <x-assignments.show-submissions-table
                :submissions="$assignment->submissions"
                :program="$program"
                :course="$course"
                :assignment="$assignment"
                context="admin"
            />
            <!--end::Submissions-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-xl-4">
            <!--begin::Course Info-->
            <x-assignments.show-course-info
                :program="$program"
                :course="$course"
                context="admin"
            />
            <!--end::Course Info-->
        </div>
        <!--end::Col-->
    </div>

    <!--begin::Delete Assignment Modal-->
    <x-assignments.delete-modal
        :assignment="$assignment"
        :program="$program"
        :course="$course"
        context="admin"
    />
    <!--end::Delete Assignment Modal-->

    <!--begin::Edit Assignment Modal-->
    <x-assignments.edit-modal
        :assignment="$assignment"
        :program="$program"
        :course="$course"
        context="admin"
    />
    <!--end::Edit Assignment Modal-->

</x-default-layout>
