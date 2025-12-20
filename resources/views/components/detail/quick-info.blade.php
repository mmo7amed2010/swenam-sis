{{--
/**
 * Quick Info Component
 *
 * Displays a list of key-value pairs with optional badges.
 * Used in sidebars for summary information.
 *
 * @param string|null $title - Card title
 * @param string|null $icon - Title icon
 * @param array $items - Array of items with 'label', 'value', and optional 'badge' (color)
 *
 * @example
 * <x-detail.quick-info
 *     title="Quick Info"
 *     icon="information-2"
 *     :items="[
 *         ['label' => 'Days Since', 'value' => '5 days', 'badge' => 'primary'],
 *         ['label' => 'Documents', 'value' => '3 / 4', 'badge' => 'info'],
 *         ['label' => 'Experience', 'value' => 'Yes', 'badge' => 'success'],
 *     ]"
 * />
 */
--}}

@props([
    'title' => 'Quick Info',
    'icon' => 'information-2',
    'items' => [],
])

<div class="card border-0 shadow-sm">
    <div class="card-header border-0 bg-light-info py-5">
        <h3 class="card-title fw-bold text-gray-800">
            {!! getIcon($icon, 'fs-4 me-2 text-info') !!}
            {{ $title }}
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="d-flex flex-column">
            @foreach($items as $index => $item)
                <div class="d-flex align-items-center justify-content-between px-6 py-4 {{ $index < count($items) - 1 ? 'border-bottom' : '' }}">
                    <span class="text-gray-600 fs-7">{{ $item['label'] }}</span>
                    @if(isset($item['badge']))
                        <span class="badge badge-light-{{ $item['badge'] }} fs-7 fw-bold">{{ $item['value'] }}</span>
                    @else
                        <span class="text-gray-800 fw-semibold fs-7">{{ $item['value'] }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
