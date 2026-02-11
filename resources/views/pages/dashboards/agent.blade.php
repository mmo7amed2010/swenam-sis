<x-default-layout>

    @section('title')
        {{ __('Agent Dashboard') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    {{-- Welcome Header --}}
    <div class="card mb-6">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-5 py-8 px-10">
            <div>
                <h1 class="fw-bold text-gray-900 fs-2 mb-2">
                    {{ __('Welcome back, :name', ['name' => $user->first_name ?? $user->name]) }}
                </h1>
                <p class="text-gray-600 fs-6 mb-0">{{ __('Manage and track student applications from your agent dashboard.') }}</p>
            </div>
            <a href="{{ route('agent.applications.create') }}" class="btn btn-primary btn-lg">
                <i class="ki-outline ki-plus fs-3 me-2"></i>
                {{ __('Create New Application') }}
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="document"
            :label="__('Total Applications')"
            :value="number_format($stats['total'] ?? 0)"
            color="primary"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="time"
            :label="__('Pending')"
            :value="number_format($stats['pending'] ?? 0)"
            color="warning"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="check-circle"
            :label="__('Approved')"
            :value="number_format($stats['approved'] ?? 0)"
            color="success"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="cross-circle"
            :label="__('Rejected')"
            :value="number_format($stats['rejected'] ?? 0)"
            color="danger"
            col-class="col-md-6 col-xl-3"
        />
    </div>

    {{-- Recent Applications --}}
    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-900">{{ __('Recent Applications') }}</span>
                <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Last 10 submitted applications') }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('agent.applications.index') }}" class="btn btn-sm btn-light-primary">
                    {{ __('View All') }}
                    <i class="ki-outline ki-arrow-right fs-4 ms-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @if($recentApplications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4 min-w-150px">{{ __('Reference #') }}</th>
                                <th class="min-w-200px">{{ __('Student Name') }}</th>
                                <th class="min-w-150px">{{ __('Program') }}</th>
                                <th class="text-center min-w-120px">{{ __('Status') }}</th>
                                <th class="text-center min-w-120px">{{ __('Submitted') }}</th>
                                <th class="text-end pe-4 min-w-100px">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-700">
                            @foreach($recentApplications as $application)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-primary">{{ $application->reference_number }}</span>
                                    </td>
                                    <td>{{ $application->full_name }}</td>
                                    <td>{{ $application->program_name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusBadges = [
                                                'pending' => 'warning',
                                                'initial_approved' => 'info',
                                                'contract_sent' => 'primary',
                                                'contract_uploaded' => 'info',
                                                'contract_approved' => 'success',
                                                'payment_pending' => 'warning',
                                                'payment_uploaded' => 'info',
                                                'payment_approved' => 'success',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                            ];
                                            $badge = $statusBadges[$application->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-light-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span>
                                    </td>
                                    <td class="text-center">{{ $application->created_at->format('M d, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('agent.applications.show', $application) }}" class="btn btn-sm btn-light-primary">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <i class="ki-outline ki-document fs-3tx text-gray-300 mb-5"></i>
                    <p class="text-gray-500 fs-5">{{ __('No applications submitted yet.') }}</p>
                    <a href="{{ route('agent.applications.create') }}" class="btn btn-primary mt-3">
                        <i class="ki-outline ki-plus fs-3 me-2"></i>
                        {{ __('Create Your First Application') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

</x-default-layout>
