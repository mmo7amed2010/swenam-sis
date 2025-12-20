{{--
    Stat Item Component

    A reusable stat display with icon, number, and label.
    Used in course cards, dashboards, and other places showing counts.

    @props
    - value: The number/value to display
    - label: The text label below the value
    - icon: Icon name (keenicons)
    - color: Color scheme (primary, success, info, warning, danger, secondary)
    - activeColor: Optional different color when value > 0 (useful for alerts)
    - size: Size variant (sm, md, lg) - default: md

    @example
    <x-stat-item :value="5" label="Modules" icon="book" color="info" />
    <x-stat-item :value="$pending" label="To Grade" icon="notepad-edit" color="gray" activeColor="warning" />
--}}

@props([
    'value' => 0,
    'label' => '',
    'icon' => 'abstract-26',
    'color' => 'primary',
    'activeColor' => null,
    'size' => 'md',
])

@php
    // Determine the actual color to use
    $displayColor = ($activeColor && $value > 0) ? $activeColor : $color;

    // Color mappings
    $colorClasses = match($displayColor) {
        'primary' => ['bg' => 'bg-light-primary', 'text' => 'text-primary'],
        'success' => ['bg' => 'bg-light-success', 'text' => 'text-success'],
        'info' => ['bg' => 'bg-light-info', 'text' => 'text-info'],
        'warning' => ['bg' => 'bg-light-warning', 'text' => 'text-warning'],
        'danger' => ['bg' => 'bg-light-danger', 'text' => 'text-danger'],
        'secondary' => ['bg' => 'bg-light', 'text' => 'text-gray-600'],
        'gray' => ['bg' => 'bg-light', 'text' => 'text-gray-500'],
        default => ['bg' => 'bg-light-primary', 'text' => 'text-primary'],
    };

    // Size mappings
    $sizeClasses = match($size) {
        'sm' => ['symbol' => 'symbol-30px', 'icon' => 'fs-6', 'value' => 'fs-7', 'label' => 'fs-8', 'gap' => 'me-2'],
        'lg' => ['symbol' => 'symbol-45px', 'icon' => 'fs-3', 'value' => 'fs-4', 'label' => 'fs-6', 'gap' => 'me-4'],
        default => ['symbol' => 'symbol-35px', 'icon' => 'fs-5', 'value' => 'fs-6', 'label' => 'fs-7', 'gap' => 'me-3'],
    };
@endphp

<div {{ $attributes->merge(['class' => 'd-flex align-items-center']) }}>
    <div class="symbol {{ $sizeClasses['symbol'] }} {{ $sizeClasses['gap'] }}">
        <span class="symbol-label {{ $colorClasses['bg'] }} {{ $colorClasses['text'] }} fw-bold {{ $sizeClasses['icon'] }}">
            {!! getIcon($icon, $sizeClasses['icon']) !!}
        </span>
    </div>
    <div>
        <span class="text-gray-900 fw-bold {{ $sizeClasses['value'] }}">{{ $value }}</span>
        <span class="text-gray-600 {{ $sizeClasses['label'] }} d-block">{{ $label }}</span>
    </div>
</div>
