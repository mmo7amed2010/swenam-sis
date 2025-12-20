{{--
/**
 * Table Card Wrapper Component
 *
 * Enhanced card container for tables with toolbar, title, and loading state.
 * Features refined styling with shadows, borders, and smooth transitions.
 *
 * @param string|null $title - Optional card title
 * @param string|null $subtitle - Optional subtitle below title
 * @param string|null $icon - Optional icon for title (from getIcon helper)
 * @param bool $flush - Remove card borders for seamless integration (default: false)
 * @param bool $loading - Show loading overlay (default: false)
 * @param string $variant - Card style variant: default|elevated|bordered|minimal (default: default)
 *
 * @slot toolbar - Search, filters, and actions toolbar
 * @slot headerActions - Quick actions in header (badges, buttons)
 * @slot default - Table content
 * @slot footer - Optional footer content (pagination, summary)
 *
 * @example Basic Usage
 * <x-tables.card-wrapper>
 *     <x-slot:toolbar>
 *         <x-tables.toolbar search-placeholder="Search courses..." />
 *     </x-slot:toolbar>
 *     <table class="table">...</table>
 * </x-tables.card-wrapper>
 *
 * @example With Title and Actions
 * <x-tables.card-wrapper title="All Courses" icon="book" subtitle="Manage your course catalog">
 *     <x-slot:headerActions>
 *         <span class="badge badge-light-success">12 Active</span>
 *     </x-slot:headerActions>
 *     <x-slot:toolbar>...</x-slot:toolbar>
 *     <table class="table">...</table>
 *     <x-slot:footer>
 *         {{ $courses->links() }}
 *     </x-slot:footer>
 * </x-tables.card-wrapper>
 */
--}}

@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'flush' => false,
    'loading' => false,
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'shadow-sm border-0',
        'elevated' => 'shadow border-0',
        'bordered' => 'shadow-none border border-gray-200',
        'minimal' => 'shadow-none border-0 bg-transparent',
    ];

    $cardClass = $variantClasses[$variant] ?? $variantClasses['default'];
    $flushClass = $flush ? 'card-flush' : '';
@endphp

<div {{ $attributes->merge(['class' => "card {$cardClass} {$flushClass}"]) }}>
    {{-- Header with Title --}}
    @if ($title || isset($toolbar) || isset($headerActions))
        <div class="card-header border-0 pt-6 pb-4">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 w-100">
                {{-- Left: Title Section --}}
                @if ($title)
                    <div class="d-flex align-items-center">
                        @if ($icon)
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-primary">
                                    {!! getIcon($icon, 'fs-3 text-primary') !!}
                                </span>
                            </div>
                        @endif
                        <div class="d-flex flex-column">
                            <h3 class="card-title fw-bold text-gray-800 mb-0 fs-4">{{ $title }}</h3>
                            @if ($subtitle)
                                <span class="text-gray-500 fs-7 mt-1">{{ $subtitle }}</span>
                            @endif
                        </div>
                        @if (isset($headerActions))
                            <div class="d-flex align-items-center gap-2 ms-4">
                                {{ $headerActions }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Right: Toolbar --}}
                @if (isset($toolbar))
                    <div class="d-flex align-items-center gap-3">
                        {{ $toolbar }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Body --}}
    <div class="card-body py-4 position-relative" style="overflow: visible;">
        {{-- Loading Overlay --}}
        @if ($loading)
            <div class="table-loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                 style="background: rgba(255,255,255,0.8); z-index: 10; backdrop-filter: blur(2px);">
                <div class="d-flex flex-column align-items-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-gray-600 fs-7 fw-semibold">Loading data...</span>
                </div>
            </div>
        @endif

        {{ $slot }}
    </div>

    {{-- Footer / Pagination --}}
    @if (isset($footer) || isset($pagination))
        <div class="card-footer border-0 pt-0 pb-5">
            @if (isset($pagination))
                {{ $pagination }}
            @endif
            @if (isset($footer))
                {{ $footer }}
            @endif
        </div>
    @endif
</div>
