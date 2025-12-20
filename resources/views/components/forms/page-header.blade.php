{{--
/**
 * Page Header Component
 *
 * Visual hero-style header for form pages (create/edit) with icon, title, and actions.
 * Provides consistent styling across all admin form pages.
 *
 * @param string $title - Page title
 * @param string|null $subtitle - Optional subtitle/description
 * @param string|null $icon - Icon name for getIcon helper
 * @param string $color - Theme color: primary|success|warning|danger|info|dark (default: primary)
 * @param string|null $backRoute - Route name for back button
 * @param array|null $backParams - Route parameters for back button
 * @param bool $showBack - Show back button (default: true if backRoute provided)
 *
 * @slot actions - Additional action buttons in header
 * @slot badges - Status badges to display
 *
 * @example Basic Usage
 * <x-forms.page-header
 *     title="Create Program"
 *     subtitle="Add a new academic program to your catalog"
 *     icon="abstract-26"
 *     back-route="admin.programs.index"
 * />
 *
 * @example With Actions and Badges
 * <x-forms.page-header title="Edit Program" icon="abstract-26" color="warning">
 *     <x-slot:badges>
 *         <span class="badge badge-light-success">Active</span>
 *     </x-slot:badges>
 *     <x-slot:actions>
 *         <a href="#" class="btn btn-sm btn-light-danger">Delete</a>
 *     </x-slot:actions>
 * </x-forms.page-header>
 */
--}}

@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'color' => 'primary',
    'backRoute' => null,
    'backParams' => [],
    'showBack' => null,
])

@php
    $colorMap = [
        'primary' => ['bg' => 'bg-light-primary', 'text' => 'text-primary'],
        'success' => ['bg' => 'bg-light-success', 'text' => 'text-success'],
        'warning' => ['bg' => 'bg-light-warning', 'text' => 'text-warning'],
        'danger' => ['bg' => 'bg-light-danger', 'text' => 'text-danger'],
        'info' => ['bg' => 'bg-light-info', 'text' => 'text-info'],
        'dark' => ['bg' => 'bg-light-dark', 'text' => 'text-dark'],
    ];

    $colors = $colorMap[$color] ?? $colorMap['primary'];
    $shouldShowBack = $showBack ?? ($backRoute !== null);
@endphp

<div {{ $attributes->merge(['class' => 'card mb-6 border-0 shadow-sm overflow-hidden']) }}>
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-lg-row">
            {{-- Left: Icon & Info --}}
            <div class="d-flex align-items-center p-6 p-lg-8 flex-grow-1">
                {{-- Back Button --}}
                @if ($shouldShowBack && $backRoute)
                    <a href="{{ route($backRoute, $backParams) }}"
                       class="btn btn-icon btn-sm btn-light-{{ $color }} me-4"
                       data-bs-toggle="tooltip"
                       title="{{ __('Back') }}">
                        {!! getIcon('arrow-left', 'fs-4') !!}
                    </a>
                @endif

                {{-- Icon --}}
                @if ($icon)
                    <div class="symbol symbol-55px symbol-circle me-4">
                        <span class="symbol-label {{ $colors['bg'] }}">
                            {!! getIcon($icon, 'fs-2x ' . $colors['text']) !!}
                        </span>
                    </div>
                @endif

                {{-- Title & Subtitle --}}
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <h1 class="fs-2 fw-bold text-gray-900 mb-0">{{ $title }}</h1>
                        @if (isset($badges))
                            <div class="d-flex align-items-center gap-2">
                                {{ $badges }}
                            </div>
                        @endif
                    </div>
                    @if ($subtitle)
                        <span class="text-gray-600 fs-6">{{ $subtitle }}</span>
                    @endif
                </div>
            </div>

            {{-- Right: Actions --}}
            @if (isset($actions))
                <div class="d-flex align-items-center justify-content-end p-4 p-lg-6 border-start border-gray-200 bg-gray-50">
                    <div class="d-flex flex-wrap gap-2">
                        {{ $actions }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
