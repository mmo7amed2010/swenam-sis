<x-default-layout>

    @section('title')
        {{ __('Students') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.students.index') }}
    @endsection

    <!--begin::Stats Row-->
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="profile-user"
            :label="__('Total Students')"
            :value="$totalStudents"
            color="primary"
            data-student-total-count
        />
        <x-stat-card
            icon="document"
            :label="__('With Application')"
            :value="$withApplications"
            color="success"
            data-student-with-app-count
        />
        <x-stat-card
            icon="questionnaire-tablet"
            :label="__('Without Application')"
            :value="$withoutApplications"
            color="warning"
            data-student-without-app-count
        />
        <x-stat-card
            icon="calendar-add"
            :label="__('New This Month')"
            :value="$newThisMonth"
            color="info"
            data-student-new-month-count
        />
    </div>
    <!--end::Stats Row-->

    <x-tables.card-wrapper
        :title="__('Student Management')"
        icon="profile-user"
        :subtitle="__('Manage student accounts and program assignments')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search students...') }}"
                search-name="search"
                :show-refresh="true">

                <x-slot:filters>
                    <select name="application_status" class="form-select form-select-sm w-175px">
                        <option value="">{{ __('All Students') }}</option>
                        <option value="with">{{ __('With Application') }}</option>
                        <option value="without">{{ __('Without Application') }}</option>
                    </select>
                    <select name="program_id" class="form-select form-select-sm w-175px">
                        <option value="">{{ __('All Programs') }}</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                        @endforeach
                    </select>
                </x-slot:filters>

                <x-slot:actions>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#studentCreateModal">
                        {!! getIcon('plus', 'fs-6 me-1') !!}
                        {{ __('Add Student') }}
                    </button>
                </x-slot:actions>
            </x-tables.toolbar>
        </x-slot:toolbar>

        <!--begin::Table Container-->
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="students-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('admin.students.index') }}"
                       data-page-length="15"
                       data-text-showing="{{ __('Showing') }}"
                       data-text-to="{{ __('to') }}"
                       data-text-of="{{ __('of') }}"
                       data-text-entries="{{ __('entries') }}"
                       data-text-filtered-from="{{ __('filtered from') }}"
                       data-text-total="{{ __('total') }}"
                       data-text-no-records="{{ __('No students found') }}"
                       data-text-view="{{ __('View Details') }}"
                       data-text-edit="{{ __('Edit') }}"
                       data-text-delete="{{ __('Delete') }}">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-200px">{{ __('Student') }}</th>
                            <th class="min-w-200px">{{ __('Email / Program') }}</th>
                            <th class="text-center min-w-150px">{{ __('Application') }}</th>
                            <th class="text-center min-w-120px">{{ __('Created Date') }}</th>
                            <th class="text-end pe-4 min-w-150px">{{ __('Actions') }}</th>
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

    @include('pages.admin.students.partials.student-modals')

    @push('scripts')
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/students-table.js') }}"></script>
    @endpush

</x-default-layout>
