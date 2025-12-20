{{--
/**
 * Status Badge Component
 *
 * Contextual status badges with icons, pulses, and semantic color mapping.
 * Automatically maps common status strings to appropriate colors.
 *
 * @param string $status - Status value (auto-mapped to colors)
 * @param string|null $label - Custom label (default: ucfirst of status)
 * @param string|null $color - Override color: primary|success|warning|danger|info|secondary|dark
 * @param string|null $icon - Custom icon name
 * @param bool $showIcon - Show status icon (default: true)
 * @param bool $pulse - Show pulse animation for active states (default: false)
 * @param string $size - Badge size: sm|md|lg (default: md)
 * @param string $variant - Style: solid|light|outline (default: light)
 *
 * @example Basic Usage
 * <x-tables.status-badge status="active" />
 * <x-tables.status-badge status="draft" />
 * <x-tables.status-badge status="pending" />
 *
 * @example With Pulse Animation
 * <x-tables.status-badge status="active" :pulse="true" />
 *
 * @example Custom Override
 * <x-tables.status-badge
 *     status="custom"
 *     label="In Review"
 *     color="info"
 *     icon="eye"
 * />
 *
 * @example Solid Variant
 * <x-tables.status-badge status="published" variant="solid" />
 */
--}}

@props([
    'status',
    'label' => null,
    'color' => null,
    'icon' => null,
    'showIcon' => true,
    'pulse' => false,
    'size' => 'md',
    'variant' => 'light',
])

@php
    // Status to color mapping
    $statusColors = [
        // Success states
        'active' => 'success',
        'published' => 'success',
        'approved' => 'success',
        'completed' => 'success',
        'verified' => 'success',
        'enabled' => 'success',
        'online' => 'success',
        'passed' => 'success',

        // Warning states
        'draft' => 'warning',
        'pending' => 'warning',
        'review' => 'warning',
        'processing' => 'warning',
        'in_progress' => 'warning',
        'waiting' => 'warning',

        // Danger states
        'inactive' => 'danger',
        'rejected' => 'danger',
        'failed' => 'danger',
        'expired' => 'danger',
        'cancelled' => 'danger',
        'deleted' => 'danger',
        'blocked' => 'danger',
        'overdue' => 'danger',

        // Info states
        'scheduled' => 'info',
        'upcoming' => 'info',
        'new' => 'info',

        // Secondary states
        'archived' => 'secondary',
        'disabled' => 'secondary',
        'paused' => 'secondary',
        'suspended' => 'secondary',
    ];

    // Status to icon mapping
    $statusIcons = [
        'active' => 'check-circle',
        'published' => 'verify',
        'approved' => 'shield-tick',
        'completed' => 'check-square',
        'verified' => 'badge-check',
        'enabled' => 'toggle-on-circle',
        'online' => 'wifi',
        'passed' => 'medal-star',

        'draft' => 'pencil',
        'pending' => 'time',
        'review' => 'eye',
        'processing' => 'loading',
        'in_progress' => 'arrows-circle',
        'waiting' => 'hourglass',

        'inactive' => 'minus-circle',
        'rejected' => 'cross-circle',
        'failed' => 'cross-square',
        'expired' => 'calendar-remove',
        'cancelled' => 'trash',
        'deleted' => 'trash-square',
        'blocked' => 'lock',
        'overdue' => 'notification-bing',

        'scheduled' => 'calendar',
        'upcoming' => 'calendar-tick',
        'new' => 'add-item',

        'archived' => 'archive',
        'disabled' => 'slash',
        'paused' => 'pause',
        'suspended' => 'information',
    ];

    // Normalize status for lookup
    $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));

    // Determine color
    $badgeColor = $color ?? ($statusColors[$normalizedStatus] ?? 'secondary');

    // Determine icon
    $badgeIcon = $icon ?? ($statusIcons[$normalizedStatus] ?? null);

    // Determine label
    $badgeLabel = $label ?? ucfirst(str_replace(['_', '-'], ' ', $status));

    // Size classes
    $sizes = [
        'sm' => ['badge' => 'fs-8 py-1 px-2', 'icon' => 'fs-9'],
        'md' => ['badge' => 'fs-7 py-2 px-3', 'icon' => 'fs-8'],
        'lg' => ['badge' => 'fs-6 py-2 px-4', 'icon' => 'fs-7'],
    ];
    $sizeConfig = $sizes[$size] ?? $sizes['md'];

    // Variant classes
    $variantClasses = [
        'light' => "badge-light-{$badgeColor}",
        'solid' => "badge-{$badgeColor}",
        'outline' => "badge-outline badge-{$badgeColor}",
    ];
    $variantClass = $variantClasses[$variant] ?? $variantClasses['light'];
@endphp

<span class="badge {{ $variantClass }} {{ $sizeConfig['badge'] }} d-inline-flex align-items-center gap-1">
    {{-- Pulse Animation --}}
    @if ($pulse && in_array($badgeColor, ['success', 'primary']))
        <span class="bullet bullet-dot bg-{{ $badgeColor }} me-1 animation-blink"></span>
    @endif

    {{-- Icon --}}
    @if ($showIcon && $badgeIcon)
        <i class="ki-outline ki-{{ $badgeIcon }} {{ $sizeConfig['icon'] }}"></i>
    @endif

    {{-- Label --}}
    {{ $badgeLabel }}
</span>

{{-- Pulse Animation Style --}}
@if ($pulse)
    @once
        @push('styles')
            <style>
                @keyframes statusBlink {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.4; }
                }
                .animation-blink {
                    animation: statusBlink 1.5s ease-in-out infinite;
                }
            </style>
        @endpush
    @endonce
@endif
