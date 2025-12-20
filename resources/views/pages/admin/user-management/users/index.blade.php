<x-default-layout>

    @section('title')
        {{ __('Admins') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('user-management.users.index') }}
    @endsection

    <!--begin::Stats Row-->
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="security-user"
            :label="__('Total Admins')"
            :value="$totalAdmins"
            color="primary"
            data-admin-total-count
        />
        <x-stat-card
            icon="verify"
            :label="__('Active (30 days)')"
            :value="$activeAdmins"
            color="success"
            data-admin-active-count
        />
        <x-stat-card
            icon="calendar-add"
            :label="__('New This Month')"
            :value="$newThisMonth"
            color="info"
            data-admin-new-month-count
        />
    </div>
    <!--end::Stats Row-->

    <x-tables.card-wrapper
        :title="__('Admin Management')"
        icon="security-user"
        :subtitle="__('Manage administrator accounts')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search admins...') }}"
                search-name="search"
                :show-refresh="true">

                <x-slot:actions>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#adminCreateModal">
                        {!! getIcon('plus', 'fs-6 me-1') !!}
                        {{ __('Add Admin') }}
                    </button>
                </x-slot:actions>
            </x-tables.toolbar>
        </x-slot:toolbar>

        <!--begin::Table Container-->
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="users-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('user-management.users.index') }}"
                       data-page-length="15"
                       data-text-showing="{{ __('Showing') }}"
                       data-text-to="{{ __('to') }}"
                       data-text-of="{{ __('of') }}"
                       data-text-entries="{{ __('entries') }}"
                       data-text-filtered-from="{{ __('filtered from') }}"
                       data-text-total="{{ __('total') }}"
                       data-text-no-records="{{ __('No admins found') }}"
                       data-text-edit="{{ __('Edit') }}"
                       data-text-delete="{{ __('Delete') }}">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-250px">{{ __('Admin') }}</th>
                            <th class="min-w-200px">{{ __('Email') }}</th>
                            <th class="text-center min-w-150px">{{ __('Last Login') }}</th>
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

    @include('pages.admin.user-management.users.partials.admin-modals')

    @push('scripts')
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/users-table.js') }}"></script>
    @endpush

</x-default-layout>
