<x-default-layout>

    @section('title')
        {{ __('Agent Details') }} - {{ $agent->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.agents.show', $agent) }}
    @endsection

    @php
        $isActive = $agent->last_login_at && $agent->last_login_at->greaterThan(now()->subDays(30));

        $statusConfig = [
            'pending' => ['badge' => 'warning', 'icon' => 'time', 'label' => 'Pending Review'],
            'initial_approved' => ['badge' => 'info', 'icon' => 'shield-tick', 'label' => 'In Progress'],
            'contract_sent' => ['badge' => 'primary', 'icon' => 'send', 'label' => 'In Progress'],
            'contract_uploaded' => ['badge' => 'info', 'icon' => 'document', 'label' => 'In Progress'],
            'contract_approved' => ['badge' => 'success', 'icon' => 'verify', 'label' => 'In Progress'],
            'payment_pending' => ['badge' => 'warning', 'icon' => 'wallet', 'label' => 'In Progress'],
            'payment_uploaded' => ['badge' => 'info', 'icon' => 'wallet', 'label' => 'In Progress'],
            'payment_approved' => ['badge' => 'success', 'icon' => 'wallet', 'label' => 'In Progress'],
            'approved' => ['badge' => 'success', 'icon' => 'check-circle', 'label' => 'Approved'],
            'rejected' => ['badge' => 'danger', 'icon' => 'cross-circle', 'label' => 'Rejected'],
        ];

        $headerPills = [
            'Member Since' => $agent->created_at->format('M d, Y'),
            'Last Login' => $agent->last_login_at?->diffForHumans() ?? 'Never',
            'Total Applications' => $statusCounts['total'],
        ];
    @endphp

    {{-- Profile Header --}}
    <x-profile.header
        :name="$agent->name"
        :initials="strtoupper(collect(explode(' ', $agent->name))->map(fn($w) => substr($w, 0, 1))->take(2)->join(''))"
        :email="$agent->email"
        :status="$isActive ? 'active' : 'inactive'"
        :statusLabel="$isActive ? __('Active') : __('Inactive')"
        :pills="$headerPills"
    />

    <div class="row g-6">
        {{-- Main Content --}}
        <div class="col-xl-8">
            {{-- Agent Information --}}
            <x-cards.section :title="__('Agent Information')" class="mb-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-detail.field icon="user" :label="__('Full Name')" :value="$agent->name" color="primary" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="sms" :label="__('Email Address')" :value="$agent->email" :href="'mailto:' . $agent->email" color="primary" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="calendar" :label="__('Member Since')" :value="$agent->created_at->format('F d, Y')" color="info" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="time" :label="__('Last Login')" :value="$agent->last_login_at?->format('F d, Y \a\t h:i A') ?? __('Never')" color="info" />
                    </div>
                </div>
            </x-cards.section>

            {{-- Submitted Applications --}}
            <x-cards.section :title="__('Submitted Applications')">
                <x-slot:toolbar>
                    @if($agentApplications->isNotEmpty())
                        <a href="{{ route('admin.applications.index', ['agent_id' => $agent->id]) }}" class="btn btn-sm btn-light-primary">
                            {!! getIcon('filter', 'fs-5 me-1') !!}
                            {{ __('View in Applications') }}
                        </a>
                    @endif
                </x-slot:toolbar>

                @if($agentApplications->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4 mb-0">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 min-w-125px">{{ __('Reference') }}</th>
                                    <th class="min-w-150px">{{ __('Student Name') }}</th>
                                    <th class="min-w-150px">{{ __('Program') }}</th>
                                    <th class="text-center min-w-100px">{{ __('Status') }}</th>
                                    <th class="min-w-125px">{{ __('Submitted') }}</th>
                                    <th class="text-end pe-4 min-w-80px">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-700">
                                @foreach($agentApplications as $application)
                                    @php
                                        $appConfig = $statusConfig[$application->status] ?? $statusConfig['pending'];
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <a href="{{ route('admin.applications.show', $application) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                {{ $application->reference_number }}
                                            </a>
                                        </td>
                                        <td>{{ $application->full_name }}</td>
                                        <td>{{ $application->resolved_program_name }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light-{{ $appConfig['badge'] }}">
                                                {{ $appConfig['label'] }}
                                            </span>
                                        </td>
                                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                {!! getIcon('eye', 'fs-5') !!}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-tables.empty-state
                        icon="document"
                        :title="__('No Applications')"
                        :message="__('This agent has not submitted any applications yet')"
                        size="sm"
                    />
                @endif
            </x-cards.section>
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-4">
            <x-detail.quick-info
                :title="__('Application Statistics')"
                icon="chart-simple"
                :items="[
                    ['label' => __('Total'), 'value' => $statusCounts['total'], 'badge' => 'primary'],
                    ['label' => __('Pending'), 'value' => $statusCounts['pending'], 'badge' => 'warning'],
                    ['label' => __('In Progress'), 'value' => $statusCounts['initial_approved'], 'badge' => 'info'],
                    ['label' => __('Approved'), 'value' => $statusCounts['approved'], 'badge' => 'success'],
                    ['label' => __('Rejected'), 'value' => $statusCounts['rejected'], 'badge' => 'danger'],
                ]"
                class="mb-5"
            />

            <a href="{{ route('admin.agents.index') }}" class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2">
                {!! getIcon('left', 'fs-4') !!}
                {{ __('Back to Agents') }}
            </a>
        </div>
    </div>

</x-default-layout>
