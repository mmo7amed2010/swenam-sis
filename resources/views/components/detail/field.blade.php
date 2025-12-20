{{--
/**
 * Detail Field Component
 *
 * Displays a field with optional icon, label, and value.
 * Used for showing key-value pairs in detail views.
 *
 * @param string|null $icon - Keenicons icon name
 * @param string $label - The field label
 * @param string|null $value - The field value
 * @param string|null $href - Optional link URL
 * @param bool $showIcon - Whether to show icon (default: true)
 *
 * @slot default - Custom value content (overrides value prop)
 *
 * @example Basic usage
 * <x-detail.field icon="user" label="Full Name" value="John Doe" />
 *
 * @example With link
 * <x-detail.field icon="sms" label="Email" value="john@example.com" href="mailto:john@example.com" />
 *
 * @example With custom content
 * <x-detail.field icon="geolocation" label="Address">
 *     123 Main St<br>New York, NY 10001
 * </x-detail.field>
 */
--}}

@props([
    'icon' => null,
    'label',
    'value' => null,
    'href' => null,
    'showIcon' => true,
    'color' => 'primary',
])

<div class="d-flex align-items-start">
    @if($showIcon && $icon)
        <div class="symbol symbol-40px me-3">
            <span class="symbol-label bg-light-{{ $color }}">
                {!! getIcon($icon, 'fs-5 text-' . $color) !!}
            </span>
        </div>
    @endif
    <div class="flex-grow-1">
        <div class="text-gray-500 fs-8 text-uppercase fw-semibold mb-1">{{ $label }}</div>
        @if($slot->isNotEmpty())
            <div class="text-gray-800 fw-semibold fs-6">{{ $slot }}</div>
        @elseif($href)
            <a href="{{ $href }}" class="text-gray-800 fw-semibold text-hover-primary fs-6">{{ $value }}</a>
        @else
            <div class="text-gray-800 fw-semibold fs-6">{{ $value ?? '-' }}</div>
        @endif
    </div>
</div>
