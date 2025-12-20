<x-default-layout>
    @section('title')
        {{ __('Application Review Dashboard') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.applications.index') }}
    @endsection

    {{-- Stat Cards --}}
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="document"
            :label="__('Total Applications')"
            :value="$stats['total']"
            color="primary"
        />
        <x-stat-card
            icon="time"
            :label="__('Pending Review')"
            :value="$stats['pending']"
            color="warning"
        />
        <x-stat-card
            icon="shield-tick"
            :label="__('Initial Approved')"
            :value="$stats['initial_approved']"
            color="info"
        />
        <x-stat-card
            icon="check-circle"
            :label="__('Approved')"
            :value="$stats['approved']"
            color="success"
        />
        <x-stat-card
            icon="cross-circle"
            :label="__('Rejected')"
            :value="$stats['rejected']"
            color="danger"
        />
    </div>

    {{-- Applications Table --}}
    <x-tables.card-wrapper
        :title="__('Student Applications')"
        icon="document"
        :subtitle="__('Review and process student applications')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search applications...') }}"
                search-name="search"
                :show-refresh="true">
                
                <x-slot:filters>
                    {{-- Status Filter --}}
                    <select name="status" class="form-select form-select-sm w-150px">
                        <option value="all">{{ __('All Statuses') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="initial_approved">{{ __('Initial Approved') }}</option>
                        <option value="approved">{{ __('Approved') }}</option>
                        <option value="rejected">{{ __('Rejected') }}</option>
                    </select>

                    {{-- Date From --}}
                    <input type="date" name="from" class="form-control form-control-sm w-150px" 
                           placeholder="{{ __('From Date') }}">

                    {{-- Date To --}}
                    <input type="date" name="to" class="form-control form-control-sm w-150px" 
                           placeholder="{{ __('To Date') }}">
                </x-slot:filters>
            </x-tables.toolbar>
        </x-slot:toolbar>

        {{-- DataTable Container --}}
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="applications-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('admin.applications.index') }}"
                       data-page-length="25">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-150px">{{ __('Reference') }}</th>
                            <th class="min-w-200px">{{ __('Applicant Name') }}</th>
                            <th class="min-w-200px">{{ __('Email') }}</th>
                            <th class="min-w-150px">{{ __('Program') }}</th>
                            <th class="text-center min-w-120px">{{ __('Submitted') }}</th>
                            <th class="text-center min-w-120px">{{ __('Status') }}</th>
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

    </x-tables.card-wrapper>

    @push('scripts')
        <script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/applications-table.js') }}"></script>
    @endpush

</x-default-layout>
