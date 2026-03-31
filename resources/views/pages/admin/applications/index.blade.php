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
            icon="send"
            :label="__('Contract Sent')"
            :value="$stats['contract_sent']"
            color="primary"
        />
        <x-stat-card
            icon="document"
            :label="__('Contract Uploaded')"
            :value="$stats['contract_uploaded']"
            color="info"
        />
        <x-stat-card
            icon="verify"
            :label="__('Contract Approved')"
            :value="$stats['contract_approved']"
            color="success"
        />
        <x-stat-card
            icon="wallet"
            :label="__('Payment Pending')"
            :value="$stats['payment_pending']"
            color="warning"
        />
        <x-stat-card
            icon="wallet"
            :label="__('Payment Uploaded')"
            :value="$stats['payment_uploaded']"
            color="info"
        />
        <x-stat-card
            icon="wallet"
            :label="__('Payment Approved')"
            :value="$stats['payment_approved']"
            color="success"
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

        @if($agentFilter)
            <div class="alert alert-info d-flex align-items-center py-3 px-5 mb-0 border-0 rounded-0" id="agent-filter-banner">
                <i class="ki-outline ki-information-3 fs-2 text-info me-3"></i>
                <div class="d-flex flex-grow-1 align-items-center">
                    <span class="fw-semibold">
                        {{ __('Showing applications submitted by agent:') }}
                        <strong>{{ $agentFilter->name }}</strong>
                    </span>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-light-info ms-3">
                    <i class="ki-outline ki-cross fs-5 me-1"></i>{{ __('Clear Filter') }}
                </a>
            </div>
            <div class="separator border-gray-200"></div>
        @endif

        <input type="hidden" name="agent_id" value="{{ request('agent_id', '') }}">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search applications...') }}"
                search-name="search"
                :show-refresh="true">
                <x-slot:actions>
                    <x-tables.export-dropdown
                        :export-url="route('admin.applications.export')"
                        :extra-fields="['agent_id']"
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
                            <option value="contract_sent">{{ __('Contract Sent') }}</option>
                            <option value="contract_uploaded">{{ __('Contract Uploaded') }}</option>
                            <option value="contract_approved">{{ __('Contract Approved') }}</option>
                            <option value="payment_pending">{{ __('Payment Pending') }}</option>
                            <option value="payment_uploaded">{{ __('Payment Uploaded') }}</option>
                            <option value="payment_approved">{{ __('Payment Approved') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('NOA Status') }}</label>
                        <select name="noa_status" class="form-select form-select-sm">
                            <option value="all">{{ __('All NOA Statuses') }}</option>
                            <option value="requested">{{ __('Requested') }}</option>
                            <option value="uploaded">{{ __('Uploaded') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('MSFAA Status') }}</label>
                        <select name="msfaa_status" class="form-select form-select-sm">
                            <option value="all">{{ __('All MSFAA Statuses') }}</option>
                            <option value="requested">{{ __('Requested') }}</option>
                            <option value="confirmed">{{ __('Confirmed') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Program') }}</label>
                        <select name="program" class="form-select form-select-sm">
                            <option value="all">{{ __('All Programs') }}</option>
                            @foreach($programs as $program)
                                <option value="{{ $program['id'] }}">{{ $program['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Intake') }}</label>
                        <select name="intake" class="form-select form-select-sm">
                            <option value="all">{{ __('All Intakes') }}</option>
                            @foreach($intakes as $intake)
                                <option value="{{ $intake['id'] }}">{{ $intake['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Agency') }}</label>
                        <select name="agency" class="form-select form-select-sm">
                            <option value="all">{{ __('All Agencies') }}</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency }}">{{ $agency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Date From') }}</label>
                        <input type="date" name="from" class="form-control form-control-sm"
                               placeholder="{{ __('From Date') }}">
                    </div>
                    <div class="col-md-3 col-lg">
                        <label class="form-label fs-7 fw-semibold text-gray-600">{{ __('Date To') }}</label>
                        <input type="date" name="to" class="form-control form-control-sm"
                               placeholder="{{ __('To Date') }}">
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
                       data-ajax-url="{{ route('admin.applications.index') }}"
                       data-page-length="25">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-150px">{{ __('Reference') }}</th>
                            <th class="min-w-200px">{{ __('Applicant Name') }}</th>
                            <th class="min-w-200px">{{ __('Email') }}</th>
                            <th class="min-w-150px">{{ __('Agent') }}</th>
                            <th class="min-w-150px">{{ __('Program') }}</th>
                            <th class="text-center min-w-120px">{{ __('Submitted') }}</th>
                            <th class="text-center min-w-120px">{{ __('Status') }}</th>
                            <th class="text-center min-w-120px">{{ __('NOA Status') }}</th>
                            <th class="text-center min-w-120px">{{ __('MSFAA Status') }}</th>
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const badge = document.getElementById('active-filter-count');
                const resetBtn = document.getElementById('reset-filters');
                const filtersPanel = document.getElementById('applications-filters');
                if (!filtersPanel) return;

                const selects = filtersPanel.querySelectorAll('select');
                const dateInputs = filtersPanel.querySelectorAll('input[type="date"]');

                function updateBadge() {
                    let count = 0;
                    selects.forEach(function(s) { if (s.value !== 'all') count++; });
                    dateInputs.forEach(function(d) { if (d.value) count++; });

                    if (count > 0) {
                        badge.textContent = count;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                }

                selects.forEach(function(s) { s.addEventListener('change', updateBadge); });
                dateInputs.forEach(function(d) { d.addEventListener('change', updateBadge); });

                resetBtn.addEventListener('click', function() {
                    selects.forEach(function(s) {
                        s.value = 'all';
                        s.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    dateInputs.forEach(function(d) {
                        d.value = '';
                        d.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    // Clear agent filter
                    const agentInput = document.querySelector('input[name="agent_id"]');
                    if (agentInput && agentInput.value) {
                        agentInput.value = '';
                        agentInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    const banner = document.getElementById('agent-filter-banner');
                    if (banner) banner.remove();
                });
            });
        </script>
    @endpush

</x-default-layout>
