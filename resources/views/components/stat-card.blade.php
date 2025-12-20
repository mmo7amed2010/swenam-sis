{{--
/**
 * Stat Card Component
 *
 * Displays a statistic card with icon, label, and value.
 *
 * @param string $icon - Icon name (e.g., 'book-open')
 * @param string $label - Label text to display
 * @param string|int $value - Stat value to display
 * @param string $color - Color theme (primary|success|warning|danger|info|secondary) default: 'primary'
 * @param string $colClass - Column classes (default: 'col-sm-6 col-xl-3')
 * @param string $url - Optional URL to make the card clickable
 * @param string $tooltip - Optional tooltip text
 *
 * @example Basic Usage
 * <x-stat-card
 *     icon="book-open"
 *     label="Total Courses"
 *     :value="25"
 *     color="primary"
 * />
 *
 * @example With Custom Column Size
 * <x-stat-card
 *     icon="people"
 *     label="Total Students"
 *     :value="150"
 *     color="info"
 *     col-class="col-md-4"
 * />
 *
 * @example Clickable Card
 * <x-stat-card
 *     icon="check-circle"
 *     label="Completed"
 *     :value="42"
 *     color="success"
 *     url="{{ route('admin.courses.index') }}"
 * />
 */
--}}

@props([
    'icon',
    'label',
    'value',
    'color' => 'primary',
    'colClass' => 'col-sm-6 col-xl-3',
    'url' => null,
    'tooltip' => null,
    'valueId' => null,
])

<div class="{{ $colClass }}">
    <div class="card card-flush h-100 border-0 shadow-sm {{ $url ? 'cursor-pointer card-hover' : '' }}"
         @if($url) onclick="window.location='{{ $url }}'" @endif
         @if($tooltip) data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip }}" @endif>
        <div class="card-body d-flex align-items-center">
            <div class="symbol symbol-50px me-4">
                <span class="symbol-label bg-light-{{ $color }}">
                    {!! getIcon($icon, 'fs-2x text-' . $color) !!}
                </span>
            </div>
            <div class="d-flex flex-column">
                <span class="text-gray-500 fs-7 fw-semibold">{{ $label }}</span>
                <span class="fs-2 fw-bold text-gray-800" {{ $attributes->merge(['id' => $valueId]) }}>{{ $value }}</span>
            </div>
        </div>
    </div>
</div>


