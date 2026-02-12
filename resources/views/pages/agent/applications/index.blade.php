<x-default-layout>
    @section('title')
        {{ __('My Applications') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
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
            :label="__('Pending')"
            :value="$stats['pending']"
            color="warning"
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
        :subtitle="__('Applications submitted on behalf of students')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search applications...') }}"
                search-name="search"
                :show-refresh="true">
                <x-slot:actions>
                    <a href="{{ route('agent.applications.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                        <i class="ki-outline ki-plus fs-4"></i>
                        {{ __('New Application') }}
                    </a>
                    <x-tables.export-dropdown
                        :export-url="route('agent.applications.export')"
                    />
                    <button type="button"
                            class="btn btn-sm btn-light-primary d-flex align-items-center gap-2"
                            data-bs-toggle="collapse"
                            data-bs-target="#applications-filters"
                            aria-expanded="false">
                        <i class="ki-outline ki-filter fs-4"></i>
                        {{ __('Filters') }}
                        <span class="badge badge-circle badge-primary d-none"
                              id="active-filter-count">0</span>
                    </button>
                </x-slot:actions>
            </x-tables.toolbar>
        </x-slot:toolbar>

        {{-- Collapsible Filter Panel --}}
        <div class="collapse" id="applications-filters">
            <div class="separator border-gray-200"></div>
            <div class="py-5 px-3">
                <div class="row g-4">
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Status') }}</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all">{{ __('All Statuses') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="initial_approved">{{ __('Initial Approved') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Date From') }}</label>
                        <input type="date" name="from" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Date To') }}</label>
                        <input type="date" name="to" class="form-control form-control-sm">
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

        {{-- DataTable Container --}}
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="applications-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('agent.applications.index') }}"
                       data-page-length="25">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-150px">{{ __('Reference') }}</th>
                            <th class="min-w-200px">{{ __('Student Name') }}</th>
                            <th class="min-w-200px">{{ __('Email') }}</th>
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('applications-table');
                if (!table || typeof AdminDataTable === 'undefined') return;

                const dt = new AdminDataTable('applications-table', {
                    columns: [
                        {
                            data: 'reference_number',
                            render: function(data, type, row) {
                                return '<a href="' + row.show_url + '" class="fw-bold text-primary">' + data + '</a>';
                            }
                        },
                        { data: 'full_name' },
                        { data: 'email' },
                        {
                            data: 'created_at',
                            className: 'text-center',
                            render: function(data, type, row) {
                                return '<span title="' + data + '">' + row.created_at_human + '</span>';
                            }
                        },
                        {
                            data: 'status',
                            className: 'text-center',
                            render: function(data) {
                                var badges = {
                                    'pending': 'warning', 'initial_approved': 'info',
                                    'approved': 'success', 'rejected': 'danger',
                                    'contract_sent': 'primary', 'contract_uploaded': 'info',
                                    'contract_approved': 'success', 'payment_pending': 'warning',
                                    'payment_uploaded': 'info', 'payment_approved': 'success'
                                };
                                var badge = badges[data] || 'secondary';
                                var label = data.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                                return '<span class="badge badge-light-' + badge + '">' + label + '</span>';
                            }
                        },
                        {
                            data: null,
                            className: 'text-end pe-4',
                            orderable: false,
                            render: function(data, type, row) {
                                return '<a href="' + row.show_url + '" class="btn btn-sm btn-light-primary">View</a>';
                            }
                        }
                    ]
                });

                // Filter handling
                var badge = document.getElementById('active-filter-count');
                var resetBtn = document.getElementById('reset-filters');
                var filtersPanel = document.getElementById('applications-filters');
                if (!filtersPanel) return;

                var selects = filtersPanel.querySelectorAll('select');
                var dateInputs = filtersPanel.querySelectorAll('input[type="date"]');

                function updateBadge() {
                    var count = 0;
                    selects.forEach(function(s) { if (s.value !== 'all') count++; });
                    dateInputs.forEach(function(d) { if (d.value) count++; });
                    if (count > 0) { badge.textContent = count; badge.classList.remove('d-none'); }
                    else { badge.classList.add('d-none'); }
                }

                selects.forEach(function(s) { s.addEventListener('change', updateBadge); });
                dateInputs.forEach(function(d) { d.addEventListener('change', updateBadge); });

                resetBtn.addEventListener('click', function() {
                    selects.forEach(function(s) { s.value = 'all'; s.dispatchEvent(new Event('change', { bubbles: true })); });
                    dateInputs.forEach(function(d) { d.value = ''; d.dispatchEvent(new Event('change', { bubbles: true })); });
                });
            });
        </script>
    @endpush

</x-default-layout>
