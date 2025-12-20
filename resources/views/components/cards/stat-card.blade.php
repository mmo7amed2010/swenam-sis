{{--
/**
 * Stat Card Component
 *
 * Displays a metric with icon, value, and optional trend indicator.
 * Commonly used in dashboards to show key performance indicators.
 *
 * @param string $title - Card title/label (e.g., "Total Students")
 * @param string|int $value - Metric value to display (e.g., "1,234" or 1234)
 * @param string $icon - Icon name from getIcon() helper (e.g., "profile-user", "check-circle")
 * @param string $color - Bootstrap color theme: primary|success|danger|warning|info|dark (default: primary)
 * @param string|null $subtitle - Optional subtitle text below value
 * @param string|null $trend - Optional trend text (e.g., "+15% from last month")
 * @param string $iconBg - Icon background color variant: light-{color} (default: auto from $color)
 * @param string $iconSize - Icon size: fs-2x|fs-3x|fs-4x (default: fs-2x)
 * @param string $cardClass - Additional CSS classes for the card
 * @param bool $hoverable - Add hover effect (default: true)
 *
 * @example Basic Usage
 * <x-cards.stat-card
 *     title="Total Students"
 *     value="1,234"
 *     icon="profile-user"
 *     color="success"
 * />
 *
 * @example With Trend
 * <x-cards.stat-card
 *     title="Active Courses"
 *     value="42"
 *     icon="book"
 *     color="primary"
 *     trend="+5 this week"
 * />
 *
 * @example With Subtitle
 * <x-cards.stat-card
 *     title="Revenue"
 *     value="$12,450"
 *     icon="dollar"
 *     color="success"
 *     subtitle="This month"
 * />
 */
--}}

@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
    'subtitle' => null,
    'trend' => null,
    'iconBg' => null,
    'iconSize' => 'fs-2x',
    'cardClass' => '',
    'hoverable' => true,
])

@php
    $iconBg = $iconBg ?? 'light-' . $color;
    $hoverClass = $hoverable ? 'hoverable' : '';
@endphp

<div class="card bg-body {{ $hoverClass }} card-xl-stretch {{ $cardClass }}">
    <div class="card-body">
        <div class="d-flex align-items-center">
            {{-- Icon --}}
            <div class="symbol symbol-50px me-3">
                <span class="symbol-label bg-{{ $iconBg }}">
                    {!! getIcon($icon, $iconSize . ' text-' . $color) !!}
                </span>
            </div>

            {{-- Content --}}
            <div class="d-flex flex-column flex-grow-1">
                {{-- Title --}}
                <span class="text-gray-500 fs-7 fw-semibold">{{ $title }}</span>

                {{-- Value --}}
                <span class="text-gray-800 fs-2 fw-bold">{{ $value }}</span>

                {{-- Subtitle --}}
                @if($subtitle)
                <span class="text-gray-500 fs-8">{{ $subtitle }}</span>
                @endif
            </div>
        </div>

        {{-- Trend --}}
        @if($trend)
        <div class="separator my-3"></div>
        <div class="d-flex align-items-center">
            <span class="text-muted fs-7">{{ $trend }}</span>
        </div>
        @endif
    </div>
</div>
