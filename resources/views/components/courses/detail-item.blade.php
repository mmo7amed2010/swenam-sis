{{--
 * Course Detail Item Component
 *
 * Displays a single course detail with icon, label, and value.
 *
 * @param string $icon - Keenicons icon name
 * @param string $color - Bootstrap color variant (primary, success, warning, info, danger, secondary)
 * @param string $label - The field label
 * @param string $value - The field value
 * @param string|null $href - Optional link URL
 * @param string|null $valueColor - Optional color for value text
--}}

@props([
    'icon',
    'color' => 'primary',
    'label',
    'value',
    'href' => null,
    'valueColor' => null
])

<div class="col">
    <div class="h-100 p-4 border border-dashed rounded-3 bg-light">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-{{ $color }}">
                    {!! getIcon($icon, 'fs-4 text-' . $color) !!}
                </span>
            </div>
            <div class="d-flex flex-column">
                <span class="text-gray-500 fs-8 text-uppercase">{{ $label }}</span>
                @if($href)
                    <a href="{{ $href }}" class="fw-bold text-{{ $valueColor ?? $color }} text-hover-{{ $valueColor ?? $color }}">
                        {{ $value }}
                    </a>
                @else
                    <span class="fw-bold text-{{ $valueColor ?? 'gray-900' }}">{{ $value }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
