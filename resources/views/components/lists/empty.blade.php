{{--
/**
 * Empty List Component
 *
 * Displays a message when a list is empty.
 *
 * @param string $message - Empty state message
 * @param string|null $icon - Icon name
 * @param string|null $actionLabel - Action button label
 * @param string|null $actionRoute - Action button route
 *
 * @example
 * <x-lists.empty
 *     message="No courses found"
 *     icon="ki-book"
 *     action-label="Create Course"
 *     action-route="admin.courses.create"
 * />
 */
--}}

@props([
    'message' => 'No items found',
    'title' => null,
    'icon' => null,
    'iconSize' => '2x',
    'actionLabel' => null,
    'actionRoute' => null,
])

<div class="text-center py-10">
    @if ($icon)
        <div class="symbol symbol-75px mb-5">
            <span class="symbol-label bg-light-primary">
                <i class="ki-duotone ki-{{ $icon }} fs-{{ $iconSize }} text-primary"></i>
            </span>
        </div>
    @endif

    @if ($title)
        <h3 class="fw-bold text-gray-900 mb-3">{{ $title }}</h3>
    @endif

    @if (isset($slot) && trim($slot) !== '')
        {{ $slot }}
    @else
        <p class="text-gray-500 fs-6 mb-5">{{ $message }}</p>

        @if ($actionLabel && $actionRoute)
            <a href="{{ route($actionRoute) }}" class="btn btn-primary">
                {{ $actionLabel }}
            </a>
        @endif
    @endif
</div>

