{{--
/**
 * List Item Component
 *
 * Standardized list item with avatar/icon, title, subtitle, and optional badge.
 *
 * @param string|null $icon - Icon name (e.g., ki-book)
 * @param string $iconColor - Icon color theme (default: primary)
 * @param string|null $title - Item title
 * @param string|null $subtitle - Item subtitle
 * @param string|null $badge - Badge text
 * @param string $badgeColor - Badge color (default: success)
 * @param string|null $href - Link URL (makes item clickable)
 *
 * @slot default - Custom content (overrides title/subtitle)
 * @slot icon - Custom icon content
 * @slot actions - Action buttons/links on right side
 *
 * @example
 * <x-lists.item
 *     icon="ki-book"
 *     icon-color="primary"
 *     title="Course Name"
 *     subtitle="CS101"
 *     badge="Active"
 *     :href="route('courses.show', $course)"
 * />
 */
--}}

@props([
    'icon' => null,
    'iconColor' => 'primary',
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'badgeColor' => 'success',
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
    $classes = 'd-flex align-items-center mb-5';
    if ($href) {
        $classes .= ' text-gray-800 text-hover-primary';
    }
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    class="{{ $classes }}" {{ $attributes }}>
    @if ($icon || isset($icon))
        <div class="symbol symbol-40px me-5">
            <span class="symbol-label bg-light-{{ $iconColor }}">
                @if (isset($icon))
                    {{ $icon }}
                @else
                    <i class="ki-duotone ki-{{ $icon }} fs-2 text-{{ $iconColor }}"></i>
                @endif
            </span>
        </div>
    @endif

    <div class="d-flex flex-column flex-grow-1">
        @if (isset($slot) && trim($slot) !== '')
            {{ $slot }}
        @else
            @if ($title)
                <span class="text-gray-800 text-hover-primary fw-bold fs-6">
                    {{ $title }}
                </span>
            @endif
            @if ($subtitle)
                <span class="text-muted fw-semibold fs-7">
                    {{ $subtitle }}
                </span>
            @endif
        @endif
    </div>

    @if ($badge)
        <span class="badge badge-light-{{ $badgeColor }}">{{ $badge }}</span>
    @endif

    @if (isset($actions))
        <div class="ms-2">
            {{ $actions }}
        </div>
    @endif
</{{ $tag }}>

