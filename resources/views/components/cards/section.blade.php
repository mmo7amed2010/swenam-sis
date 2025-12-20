{{--
/**
 * Card Section Component
 *
 * Standard content section card with title, toolbar, body, and footer slots.
 * Replaces repeated card markup patterns across views.
 *
 * @param string|null $title - Card title in header
 * @param string|null $subtitle - Optional subtitle below title
 * @param bool $flush - Add card-flush class for borderless card (default: false)
 * @param bool $bordered - Add card-bordered class (default: false)
 * @param bool $shadow - Show shadow (default: true)
 *
 * @slot toolbar - Toolbar actions displayed on right side of header
 * @slot default - Main card body content
 * @slot footer - Card footer content
 *
 * @example Basic Card with Title
 * <x-cards.section title="Section Title">
 *     <p>Card content goes here</p>
 * </x-cards.section>
 *
 * @example Card with Toolbar and Footer
 * <x-cards.section title="Section Title">
 *     <x-slot:toolbar>
 *         <button class="btn btn-sm btn-primary">Action</button>
 *     </x-slot:toolbar>
 *
 *     <p>Main content</p>
 *
 *     <x-slot:footer>
 *         <button class="btn btn-primary">Save</button>
 *     </x-slot:footer>
 * </x-cards.section>
 */
--}}

@props([
    'title' => null,
    'subtitle' => null,
    'flush' => false,
    'bordered' => false,
    'shadow' => true,
])

@php
    $cardClasses = [
        'card',
        $flush ? 'card-flush' : '',
        $bordered ? 'card-bordered' : '',
        ! $shadow ? 'shadow-none' : '',
    ];
@endphp

<div {{ $attributes->merge(['class' => implode(' ', array_filter($cardClasses))]) }}>
    @if ($title || isset($toolbar))
        <div class="card-header{{ isset($toolbar) ? '' : ' border-0' }}">
            @if ($title)
                <div class="card-title{{ isset($subtitle) ? ' flex-column align-items-start' : '' }}">
                    <h3 class="card-label fw-bold text-gray-900{{ isset($subtitle) ? ' fs-3 mb-1' : '' }}">
                        {{ $title }}
                    </h3>
                    @if (isset($subtitle))
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ $subtitle }}</span>
                    @endif
                </div>
            @endif

            @if (isset($toolbar))
                <div class="card-toolbar">
                    {{ $toolbar }}
                </div>
            @endif
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>

