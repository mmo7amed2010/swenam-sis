{{--
/**
 * Info Card Component
 *
 * Displays a centered card with icon, label, and value.
 * Used for highlighting key information in a grid layout.
 *
 * @param string $icon - Keenicons icon name
 * @param string $label - The field label
 * @param string|null $value - The field value
 *
 * @slot default - Custom value content (overrides value prop)
 *
 * @example
 * <div class="row g-4">
 *     <div class="col-md-4">
 *         <x-detail.info-card icon="abstract-26" label="Program" value="Computer Science" />
 *     </div>
 *     <div class="col-md-4">
 *         <x-detail.info-card icon="calendar" label="Start Date" value="Jan 2024" />
 *     </div>
 * </div>
 */
--}}

@props([
    'icon',
    'label',
    'value' => null,
    'color' => 'primary',
])

<div class="border border-dashed border-gray-300 rounded p-4 text-center h-100">
    <div class="symbol symbol-45px mb-3">
        <span class="symbol-label bg-light-{{ $color }}">
            {!! getIcon($icon, 'fs-2 text-' . $color) !!}
        </span>
    </div>
    <div class="fs-7 text-gray-500 mb-1">{{ $label }}</div>
    <div class="fs-6 fw-bold text-gray-800">
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            {{ $value ?? '-' }}
        @endif
    </div>
</div>
