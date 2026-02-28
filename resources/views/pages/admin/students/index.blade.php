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
                <x-slot:actions>
                    <x-tables.export-dropdown
                        :export-url="route('admin.students.export')"
                        filterId="students-filters"
                    />
                    <button type="button"
                            class="btn btn-sm btn-light-primary d-flex align-items-center gap-2"
                            data-bs-toggle="collapse"
                            data-bs-target="#students-filters"
                            aria-expanded="false">
                        <i class="ki-outline ki-filter fs-4"></i>
                        {{ __('Filters') }}
                        <span class="badge badge-circle badge-primary d-none"
                              id="active-filter-count">0</span>
                    </button>
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

        {{-- Collapsible Filter Panel --}}
        <div class="collapse" id="students-filters">
            <div class="separator border-gray-200"></div>
            <div class="py-5 px-3">
                <div class="row g-4">
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Application') }}</label>
                        <select name="application_status" class="form-select form-select-sm">
                            <option value="">{{ __('All Students') }}</option>
                            <option value="with">{{ __('With Application') }}</option>
                            <option value="without">{{ __('Without Application') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Program') }}</label>
                        <select name="program_id" class="form-select form-select-sm">
                            <option value="">{{ __('All Programs') }}</option>
                            @foreach($programs as $program)
                                <option value="{{ $program['id'] }}">{{ $program['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Account Status') }}</label>
                        <select name="suspension_status" class="form-select form-select-sm">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="suspended">{{ __('Suspended') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-auto d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-light-danger" id="reset-filters">
                            <i class="ki-outline ki-arrows-circle fs-4 me-1"></i>
                            {{ __('Reset') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="separator border-gray-200"></div>
        </div>

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
                       data-text-delete="{{ __('Delete') }}"
                       data-text-suspend="{{ __('Suspend') }}"
                       data-text-unsuspend="{{ __('Reactivate') }}"
                       data-text-suspended="{{ __('Suspended') }}"
                       data-text-active="{{ __('Active') }}"
                       data-text-confirm-suspend="{{ __('Are you sure you want to suspend this student? They will not be able to login.') }}"
                       data-text-confirm-unsuspend="{{ __('Are you sure you want to reactivate this student?') }}">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-200px">{{ __('Student') }}</th>
                            <th class="min-w-200px">{{ __('Email / Program') }}</th>
                            <th class="text-center min-w-100px">{{ __('Status') }}</th>
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
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}?v={{ filemtime(public_path('assets/js/custom/admin/courses/main.js')) }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}?v={{ filemtime(public_path('assets/js/custom/admin/tables/column-renderers.js')) }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}?v={{ filemtime(public_path('assets/js/custom/admin/tables/admin-datatable.js')) }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/students-table.js') }}?v={{ filemtime(public_path('assets/js/custom/admin/tables/students-table.js')) }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const badge = document.getElementById('active-filter-count');
                const resetBtn = document.getElementById('reset-filters');
                const filtersPanel = document.getElementById('students-filters');
                if (!filtersPanel) return;

                const selects = filtersPanel.querySelectorAll('select');

                function updateBadge() {
                    let count = 0;
                    selects.forEach(function(s) { if (s.value && s.value !== '') count++; });

                    if (count > 0) {
                        badge.textContent = count;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                }

                selects.forEach(function(s) { s.addEventListener('change', updateBadge); });

                resetBtn.addEventListener('click', function() {
                    selects.forEach(function(s) {
                        s.value = '';
                        s.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                });
            });
        </script>
    @endpush

</x-default-layout>
