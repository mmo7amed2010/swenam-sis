<x-default-layout>

    @section('title')
        {{ __('Agents') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.agents.index') }}
    @endsection

    <!--begin::Stats Row-->
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="people"
            :label="__('Total Agents')"
            :value="$totalAgents"
            color="primary"
            data-agent-total-count
        />
        <x-stat-card
            icon="verify"
            :label="__('Active (30 days)')"
            :value="$activeAgents"
            color="success"
            data-agent-active-count
        />
        <x-stat-card
            icon="calendar-add"
            :label="__('New This Month')"
            :value="$newThisMonth"
            color="info"
            data-agent-new-month-count
        />
    </div>
    <!--end::Stats Row-->

    <x-tables.card-wrapper
        :title="__('Agent Management')"
        icon="people"
        :subtitle="__('Manage agent accounts')"
        variant="default">

        <x-slot:toolbar>
            <x-tables.toolbar
                search-placeholder="{{ __('Search agents...') }}"
                search-name="search"
                :show-refresh="true">

                <x-slot:actions>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#agentCreateModal">
                        {!! getIcon('plus', 'fs-6 me-1') !!}
                        {{ __('Add Agent') }}
                    </button>
                </x-slot:actions>
            </x-tables.toolbar>
        </x-slot:toolbar>

        <!--begin::Table Container-->
        <div id="table-container">
            <div class="table-responsive" style="overflow: visible;">
                <table id="agents-table"
                       class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4"
                       data-ajax-url="{{ route('admin.agents.index') }}"
                       data-page-length="15"
                       data-text-showing="{{ __('Showing') }}"
                       data-text-to="{{ __('to') }}"
                       data-text-of="{{ __('of') }}"
                       data-text-entries="{{ __('entries') }}"
                       data-text-filtered-from="{{ __('filtered from') }}"
                       data-text-total="{{ __('total') }}"
                       data-text-no-records="{{ __('No agents found') }}"
                       data-text-edit="{{ __('Edit') }}"
                       data-text-delete="{{ __('Delete') }}">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-250px">{{ __('Agent') }}</th>
                            <th class="min-w-200px">{{ __('Email') }}</th>
                            <th class="text-center min-w-100px">{{ __('Applications') }}</th>
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

    @include('pages.admin.agents.partials.agent-modals')

    @push('scripts')
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/tables/agents-table.js') }}"></script>
    @endpush

</x-default-layout>
