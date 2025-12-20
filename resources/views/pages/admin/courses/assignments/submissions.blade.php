<x-default-layout>

    @section('title')
        {{ __('Submissions') }} - {{ $assignment->title }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $assignment->title, 'url' => route('admin.programs.courses.assignments.show', [$program, $course, $assignment])],
            ['title' => __('Submissions')]
        ]" />
    @endsection

    <!--begin::Toolbar-->
    <x-assignments.submissions-toolbar
        :assignment="$assignment"
        :program="$program"
        :course="$course"
        filter=""
        context="admin"
    />
    <!--end::Toolbar-->

    <!--begin::Submission Stats-->
    <x-assignments.submissions-stats
        :submittedCount="$submittedCount"
        :totalStudents="$totalStudents"
    />
    <!--end::Submission Stats-->

    <!--begin::DataTable Card-->
    <x-tables.card-wrapper
        :title="__('Student Submissions')"
        icon="document"
        :subtitle="__('View and manage all submissions')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search by student name or email...') }}"
                search-name="search"
                :show-refresh="true">

                <x-slot:filters>
                    <select name="filter" class="form-select form-select-sm w-150px">
                        <option value="">{{ __('All Submissions') }}</option>
                        <option value="ungraded">{{ __('Ungraded') }}</option>
                        <option value="graded">{{ __('Graded') }}</option>
                    </select>
                </x-slot:filters>
            </x-tables.toolbar>
        </x-slot:toolbar>

        <!--begin::Table Container-->
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="submissions-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('admin.programs.courses.assignments.submissions', [$program, $course, $assignment]) }}"
                       data-page-length="15"
                       data-text-showing="{{ __('Showing') }}"
                       data-text-to="{{ __('to') }}"
                       data-text-of="{{ __('of') }}"
                       data-text-entries="{{ __('entries') }}"
                       data-text-no-records="{{ __('No submissions found') }}"
                       data-text-grade="{{ __('Grade') }}"
                       data-text-view-edit="{{ __('View/Edit Grade') }}"
                       data-text-not-graded="{{ __('Not Graded') }}">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-200px">{{ __('Student') }}</th>
                            <th class="min-w-150px">{{ __('Submission Date') }}</th>
                            <th class="text-center min-w-100px">{{ __('Status') }}</th>
                            <th class="text-center min-w-120px">{{ __('Grade') }}</th>
                            <th class="text-end pe-4 min-w-100px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-700">
                        {{-- Data loaded via AJAX --}}
                    </tbody>
                </table>
            </div>

            <x-tables.datatable-footer />
        </div>
        <!--end::Table Container-->

    </x-tables.card-wrapper>
    <!--end::DataTable Card-->

    @push('scripts')
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/submissions-table.js') }}"></script>
    @endpush

</x-default-layout>
