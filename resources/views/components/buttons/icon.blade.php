{{--
/**
 * Icon Button Component
 *
 * Square icon-only button with consistent sizing and optional tooltip.
 * Supports all standard button features without text label.
 *
 * @param string $icon - Icon name from getIcon() helper (required)
 * @param string $color - Button color: primary|success|danger|warning|info|light|dark (default: light)
 * @param string $size - Button size: sm|md|lg (default: sm)
 * @param string $variant - Button variant: solid|light|outline (default: light)
 * @param string $type - Button type: button|submit|reset (default: button)
 * @param string|null $tooltip - Tooltip text (optional)
 * @param string|null $href - If provided, renders as <a> tag
 * @param string|null $confirm - Confirmation message for onclick
 * @param bool $disabled - Disable button (default: false)
 * @param string $buttonClass - Additional CSS classes
 *
 * @example Delete Icon Button
 * <x-buttons.icon
 *     icon="trash"
 *     color="danger"
 *     tooltip="Delete item"
 *     confirm="Are you sure?"
 * />
 *
 * @example Edit Link Button
 * <x-buttons.icon
 *     icon="pencil"
 *     color="primary"
 *     href="{{ route('courses.edit', $course) }}"
 *     tooltip="Edit course"
 * />
 *
 * @example Add Button
 * <x-buttons.icon
 *     icon="plus"
 *     color="success"
 *     size="lg"
 *     tooltip="Add new item"
 * />
 */
--}}

@props([
    'icon',
    'color' => 'light',
    'size' => 'sm',
    'variant' => 'light',
    'type' => 'button',
    'tooltip' => null,
    'href' => null,
    'confirm' => null,
    'disabled' => false,
    'buttonClass' => '',
])

@php
    // Build button class
    $colorClass = $variant === 'solid' ? 'btn-' . $color : 'btn-' . $variant . '-' . $color;
    $sizeClass = $size === 'md' ? '' : 'btn-' . $size;
    $baseClass = 'btn btn-icon ' . $colorClass . ' ' . $sizeClass . ' ' . $buttonClass;

    // Build attributes
    $attrs = [];

    if ($tooltip) {
        $attrs['data-bs-toggle'] = 'tooltip';
        $attrs['data-bs-placement'] = 'top';
        $attrs['title'] = $tooltip;
    }

    if ($confirm) {
        $attrs['onclick'] = "return confirm('" . addslashes($confirm) . "')";
    }

    if ($disabled) {
        $attrs['disabled'] = true;
    }

    $tag = $href ? 'a' : 'button';
    if ($href) {
        $attrs['href'] = $href;
    } else {
        $attrs['type'] = $type;
    }

    // Build attribute string
    $attrString = '';
    foreach ($attrs as $key => $val) {
        if (is_bool($val)) {
            $attrString .= $val ? " $key" : '';
        } else {
            $attrString .= " $key=\"$val\"";
        }
    }
@endphp

<{{ $tag }} class="{{ $baseClass }}" {!! $attrString !!} {{ $attributes }}>
    {!! getIcon($icon, 'fs-2') !!}
</{{ $tag }}>

@if($tooltip)
@once
@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endonce
@endif
