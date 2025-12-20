{{--
/**
 * Info Pill Component
 *
 * Displays a compact pill-shaped badge with label and value.
 * Used in profile headers and summary sections.
 *
 * @param string $label - The field label
 * @param string $value - The field value
 *
 * @example
 * <x-detail.info-pill label="Reference" value="APP-2024-001" />
 * <x-detail.info-pill label="Status" value="Active" />
 */
--}}

@props([
    'label',
    'value',
    'color' => null,
])

<div class="d-flex align-items-center {{ $color ? 'bg-light-' . $color : 'bg-gray-100' }} rounded-pill px-4 py-2">
    @if($color)
        <span class="bullet bullet-dot bg-{{ $color }} me-2"></span>
    @endif
    <span class="text-gray-500 fs-7 me-2">{{ $label }}:</span>
    <span class="fw-semibold text-gray-700 fs-7">{{ $value }}</span>
</div>
