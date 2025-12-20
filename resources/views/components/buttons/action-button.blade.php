{{--
/**
 * Action Button Component
 *
 * Reusable button with consistent styling, icons, and loading states.
 * Supports various colors, sizes, and interaction patterns.
 *
 * @param string $text - Button text/label
 * @param string|null $icon - Icon name from getIcon() helper (optional)
 * @param string $color - Button color: primary|success|danger|warning|info|light|dark (default: primary)
 * @param string $size - Button size: sm|md|lg (default: sm)
 * @param string $variant - Button variant: solid|light|outline (default: solid)
 * @param string $type - Button type: button|submit|reset (default: button)
 * @param string|null $href - If provided, renders as <a> tag instead of <button>
 * @param string|null $modal - Modal target ID for data-bs-toggle="modal"
 * @param bool $loading - Show loading indicator (default: false)
 * @param bool $disabled - Disable button (default: false)
 * @param string|null $confirm - Confirmation message for onclick (optional)
 * @param string $buttonClass - Additional CSS classes
 *
 * @example Primary Button
 * <x-buttons.action-button
 *     text="Create Course"
 *     icon="plus"
 *     color="primary"
 * />
 *
 * @example Link Button
 * <x-buttons.action-button
 *     text="View Details"
 *     icon="eye"
 *     color="light"
 *     href="{{ route('courses.show', $course) }}"
 * />
 *
 * @example Modal Trigger
 * <x-buttons.action-button
 *     text="Add Student"
 *     icon="plus"
 *     modal="enrollStudentModal"
 * />
 *
 * @example Delete with Confirmation
 * <x-buttons.action-button
 *     text="Delete"
 *     icon="trash"
 *     color="danger"
 *     variant="light"
 *     confirm="Are you sure you want to delete this?"
 * />
 *
 * @example Submit Button with Loading
 * <x-buttons.action-button
 *     text="Save Changes"
 *     type="submit"
 *     color="primary"
 *     :loading="true"
 * />
 */
--}}

@props([
    'text',
    'icon' => null,
    'color' => 'primary',
    'size' => 'sm',
    'variant' => 'solid',
    'type' => 'button',
    'href' => null,
    'modal' => null,
    'loading' => false,
    'disabled' => false,
    'confirm' => null,
    'buttonClass' => '',
])

@php
    // Build button class
    $colorClass = $variant === 'solid' ? 'btn-' . $color : 'btn-' . $variant . '-' . $color;
    $sizeClass = $size === 'md' ? '' : 'btn-' . $size;
    $baseClass = 'btn ' . $colorClass . ' ' . $sizeClass . ' ' . $buttonClass;

    // Build attributes
    $attrs = [];
    if ($modal) {
        $attrs['data-bs-toggle'] = 'modal';
        $attrs['data-bs-target'] = '#' . $modal;
    }
    if ($confirm) {
        $attrs['onclick'] = "return confirm('" . addslashes($confirm) . "')";
    }
    if ($disabled || $loading) {
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
    @if($loading)
        <span class="indicator-progress">
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    @else
        @if($icon)
            {!! getIcon($icon, 'fs-2') !!}
        @endif
        {{ __($text) }}
    @endif
</{{ $tag }}>
