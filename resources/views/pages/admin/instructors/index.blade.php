<x-default-layout>

    @section('title')
        {{ __('Instructors') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.instructors.index') }}
    @endsection

    <!--begin::Stats Row-->
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="teacher"
            :label="__('Total Instructors')"
            :value="$totalInstructors"
            color="primary"
            data-instructor-total-count
        />
        <x-stat-card
            icon="bookmark"
            :label="__('Active Instructors')"
            :value="$activeInstructors"
            color="success"
            data-instructor-active-count
        />
        <x-stat-card
            icon="calendar-add"
            :label="__('New This Month')"
            :value="$newThisMonth"
            color="info"
            data-instructor-new-month-count
        />
    </div>
    <!--end::Stats Row-->

    <x-tables.card-wrapper
        :title="__('Instructor Management')"
        icon="teacher"
        :subtitle="__('Manage instructor accounts and course assignments')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search instructors...') }}"
                search-name="search"
                :show-refresh="true">

                <x-slot:filters>
                    <select name="status" class="form-select form-select-sm w-150px">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active">{{ __('With Courses') }}</option>
                        <option value="inactive">{{ __('No Courses') }}</option>
                    </select>
                </x-slot:filters>

                <x-slot:actions>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#instructorCreateModal">
                        {!! getIcon('plus', 'fs-6 me-1') !!}
                        {{ __('Add Instructor') }}
                    </button>
                </x-slot:actions>
            </x-tables.toolbar>
        </x-slot:toolbar>

        <!--begin::Table Container-->
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="instructors-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('admin.instructors.index') }}"
                       data-page-length="15"
                       data-text-showing="{{ __('Showing') }}"
                       data-text-to="{{ __('to') }}"
                       data-text-of="{{ __('of') }}"
                       data-text-entries="{{ __('entries') }}"
                       data-text-filtered-from="{{ __('filtered from') }}"
                       data-text-total="{{ __('total') }}"
                       data-text-no-records="{{ __('No instructors found') }}"
                       data-text-view="{{ __('View Details') }}"
                       data-text-edit="{{ __('Edit') }}"
                       data-text-delete="{{ __('Delete') }}">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-250px">{{ __('Instructor') }}</th>
                            <th class="min-w-200px">{{ __('Email / Courses') }}</th>
                            <th class="text-center min-w-150px">{{ __('Created Date') }}</th>
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

    @include('pages.admin.instructors.partials.instructor-modals')

    @push('scripts')
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/instructors-table.js') }}"></script>
    @endpush

</x-default-layout>
