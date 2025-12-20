{{--
/**
 * Profile Header Component
 *
 * Displays a profile header with avatar, name, contact info, and status badge.
 * Used for applicant, student, and user profile pages.
 *
 * @param string $name - Full name
 * @param string $initials - Avatar initials (e.g., "JD")
 * @param string|null $email - Email address
 * @param string|null $phone - Phone number
 * @param string $status - Status key (pending, approved, rejected, active, etc.)
 * @param string $statusLabel - Display label for status
 * @param array $pills - Array of [label => value] pairs for info pills
 *
 * @slot actions - Optional action buttons on the right
 *
 * @example
 * <x-profile.header
 *     name="John Doe"
 *     initials="JD"
 *     email="john@example.com"
 *     phone="+1 234 567 890"
 *     status="pending"
 *     statusLabel="Pending Review"
 *     :pills="['Reference' => 'APP-001', 'Program' => 'Computer Science']"
 * />
 */
--}}

@props([
    'name',
    'initials',
    'email' => null,
    'phone' => null,
    'status' => 'pending',
    'statusLabel' => null,
    'pills' => [],
])

@php
    $statusConfig = [
        'pending' => ['badge' => 'warning', 'icon' => 'time'],
        'approved' => ['badge' => 'success', 'icon' => 'check-circle'],
        'rejected' => ['badge' => 'danger', 'icon' => 'cross-circle'],
        'active' => ['badge' => 'success', 'icon' => 'check-circle'],
        'inactive' => ['badge' => 'secondary', 'icon' => 'minus-circle'],
    ];
    $config = $statusConfig[$status] ?? $statusConfig['pending'];
    $displayLabel = $statusLabel ?? ucfirst($status);
@endphp

<div class="card border-0 shadow-sm mb-6 overflow-hidden">
    <div class="card-body p-0">
        {{-- Top Banner --}}
        <div class="position-relative" style="height: 100px;"></div>

        {{-- Profile Section --}}
        <div class="px-8 pb-6" style="margin-top: -40px;">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end gap-4">
                {{-- Avatar --}}
                <div class="symbol symbol-80px symbol-circle border border-4 border-white shadow-sm">
                    <span class="symbol-label fs-2 fw-bold bg-light-primary text-primary">
                        {{ $initials }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-grow-1">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h1 class="fs-2 fw-bolder text-gray-900 mb-1">{{ $name }}</h1>
                            <div class="d-flex flex-wrap align-items-center gap-3 text-gray-500 fs-7">
                                @if($email)
                                    <span class="d-flex align-items-center">
                                        {!! getIcon('sms', 'fs-6 me-1') !!}
                                        {{ $email }}
                                    </span>
                                @endif
                                @if($phone)
                                    <span class="d-flex align-items-center">
                                        {!! getIcon('phone', 'fs-6 me-1') !!}
                                        {{ $phone }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge badge-lg badge-{{ $config['badge'] }} d-flex align-items-center gap-2 px-4 py-3">
                                {!! getIcon($config['icon'], 'fs-5') !!}
                                {{ $displayLabel }}
                            </span>
                            @if(isset($actions))
                                {{ $actions }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Info Pills --}}
            @if(count($pills) > 0)
                <div class="d-flex flex-wrap gap-3 mt-5 pt-5 border-top">
                    @foreach($pills as $label => $value)
                        <x-detail.info-pill :label="$label" :value="$value" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
