{{--
/**
 * Timeline Item Component
 *
 * Timeline/activity feed item with icon, content, and timestamp.
 *
 * @param string|null $icon - Icon name
 * @param string $iconColor - Icon color (default: primary)
 * @param string|null $title - Event title
 * @param string|null $description - Event description
 * @param string|null $time - Timestamp text
 *
 * @slot default - Custom content
 *
 * @example
 * <x-lists.timeline
 *     icon="ki-check"
 *     title="Course Completed"
 *     description="CS101 - Introduction to Programming"
 *     time="2 hours ago"
 * />
 */
--}}

@props([
    'icon' => null,
    'iconColor' => 'primary',
    'title' => null,
    'description' => null,
    'time' => null,
])

<div class="d-flex align-items-center mb-5">
    @if ($icon)
        <div class="symbol symbol-40px me-5">
            <span class="symbol-label bg-light-{{ $iconColor }}">
                <i class="ki-duotone ki-{{ $icon }} fs-2 text-{{ $iconColor }}"></i>
            </span>
        </div>
    @endif

    <div class="d-flex flex-column flex-grow-1">
        @if (isset($slot) && trim($slot) !== '')
            {{ $slot }}
        @else
            @if ($title)
                <span class="text-gray-800 fw-bold fs-6">{{ $title }}</span>
            @endif
            @if ($description)
                <span class="text-muted fw-semibold fs-7">{{ $description }}</span>
            @endif
        @endif
    </div>

    @if ($time)
        <span class="text-muted fs-7">{{ $time }}</span>
    @endif
</div>

