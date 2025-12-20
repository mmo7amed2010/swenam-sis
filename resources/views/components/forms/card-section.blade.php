{{--
/**
 * Card Section Component
 *
 * Standard card wrapper with header and body for form sections.
 * Enhanced with icon, subtitle, and collapsible support.
 *
 * @param string $title - Header title text
 * @param string|null $subtitle - Optional subtitle below title
 * @param string|null $icon - Icon name for getIcon helper
 * @param string $color - Icon/accent color: primary|success|warning|danger|info|dark (default: primary)
 * @param string|null $class - Additional CSS classes for the card
 * @param bool $collapsible - Make section collapsible (default: false)
 * @param bool $collapsed - Start collapsed (default: false, only if collapsible)
 * @param string $variant - Card variant: default|flush|bordered (default: default)
 *
 * @slot headerActions - Optional action buttons in header
 * @slot default - Card body content
 *
 * @example Basic Usage
 * <x-forms.card-section title="General Information">
 *     <!-- Card body content (fields, inputs, etc.) -->
 * </x-forms.card-section>
 *
 * @example With Icon and Subtitle
 * <x-forms.card-section
 *     title="Program Details"
 *     subtitle="Basic information about the academic program"
 *     icon="abstract-26"
 *     color="primary"
 * >
 *     <!-- Card body content -->
 * </x-forms.card-section>
 *
 * @example Collapsible Section
 * <x-forms.card-section
 *     title="Advanced Settings"
 *     icon="setting-2"
 *     collapsible
 *     :collapsed="true"
 * >
 *     <!-- Hidden by default -->
 * </x-forms.card-section>
 *
 * @example With Header Actions
 * <x-forms.card-section title="Modules">
 *     <x-slot:headerActions>
 *         <button type="button" class="btn btn-sm btn-light-primary">Add Module</button>
 *     </x-slot:headerActions>
 *     <!-- Card body content -->
 * </x-forms.card-section>
 */
--}}

@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'color' => 'primary',
    'class' => '',
    'collapsible' => false,
    'collapsed' => false,
    'variant' => 'default',
])

@php
    $uniqueId = 'card_section_' . uniqid();

    $variantClasses = [
        'default' => 'card-flush',
        'flush' => 'card-flush border-0',
        'bordered' => 'border border-gray-200',
    ];

    $cardClass = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<div {{ $attributes->merge(['class' => "card {$cardClass} py-4 {$class}"]) }}>
    {{-- Card Header --}}
    <div class="card-header{{ $collapsible ? ' cursor-pointer' : '' }}"
         @if($collapsible)
             data-bs-toggle="collapse"
             data-bs-target="#{{ $uniqueId }}"
             aria-expanded="{{ $collapsed ? 'false' : 'true' }}"
         @endif
    >
        <div class="card-title d-flex align-items-center">
            @if ($icon)
                <div class="symbol symbol-40px me-3">
                    <span class="symbol-label bg-light-{{ $color }}">
                        {!! getIcon($icon, 'fs-3 text-' . $color) !!}
                    </span>
                </div>
            @endif

            <div class="d-flex flex-column">
                <h2 class="mb-0">{{ __($title) }}</h2>
                @if ($subtitle)
                    <span class="text-gray-500 fs-7 mt-1">{{ __($subtitle) }}</span>
                @endif
            </div>
        </div>

        <div class="card-toolbar d-flex align-items-center gap-2">
            @if (isset($headerActions))
                {{ $headerActions }}
            @endif

            @if ($collapsible)
                <div class="btn btn-sm btn-icon btn-light btn-active-light-primary rotate {{ $collapsed ? 'collapsed' : '' }}"
                     data-bs-toggle="collapse"
                     data-bs-target="#{{ $uniqueId }}">
                    {!! getIcon('arrow-down', 'fs-4 rotate-180') !!}
                </div>
            @endif
        </div>
    </div>

    {{-- Card Body --}}
    @if ($collapsible)
        <div id="{{ $uniqueId }}" class="collapse{{ $collapsed ? '' : ' show' }}">
            <div class="card-body pt-0">
                {{ $slot }}
            </div>
        </div>
    @else
        <div class="card-body pt-0">
            {{ $slot }}
        </div>
    @endif
</div>
