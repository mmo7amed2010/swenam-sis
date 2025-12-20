{{--
/**
 * Enhanced Empty State Component
 *
 * Display a visually appealing empty state with animations and multiple style variants.
 * Features gradient backgrounds, animated icons, and contextual theming.
 *
 * @param string $icon - Icon name from getIcon() helper (default: 'file-text')
 * @param string $title - Empty state title/heading
 * @param string|null $message - Description/explanation text
 * @param string|null $actionText - CTA button text
 * @param string|null $actionUrl - CTA button URL or route
 * @param string|null $actionModal - Modal ID to trigger instead of URL
 * @param string|null $actionPermission - Permission gate to check before showing action
 * @param string|null $secondaryActionText - Secondary button text
 * @param string|null $secondaryActionUrl - Secondary button URL
 * @param string $variant - Style variant: default|minimal|illustrated|gradient (default: default)
 * @param string $color - Theme color: primary|success|warning|info|secondary (default: primary)
 * @param string $size - Size: sm|md|lg (default: md)
 * @param bool $animated - Enable subtle animation on icon (default: true)
 *
 * @slot default - Custom content instead of message text
 * @slot action - Custom action buttons
 * @slot illustration - Custom illustration/image slot
 *
 * @example Basic Empty State
 * <x-tables.empty-state
 *     icon="book"
 *     title="No courses yet"
 *     message="Get started by creating your first course."
 *     actionText="Create Course"
 *     actionUrl="{{ route('courses.create') }}"
 * />
 *
 * @example Gradient Variant
 * <x-tables.empty-state
 *     icon="search"
 *     title="No results found"
 *     message="Try adjusting your search or filter criteria."
 *     variant="gradient"
 *     color="info"
 * />
 *
 * @example With Custom Actions
 * <x-tables.empty-state icon="users" title="No team members">
 *     <x-slot:action>
 *         <a href="#" class="btn btn-primary me-2">Invite Member</a>
 *         <a href="#" class="btn btn-light">Import CSV</a>
 *     </x-slot:action>
 * </x-tables.empty-state>
 */
--}}

@props([
    'icon' => 'file-text',
    'title',
    'message' => null,
    'actionText' => null,
    'actionUrl' => null,
    'actionModal' => null,
    'actionPermission' => null,
    'secondaryActionText' => null,
    'secondaryActionUrl' => null,
    'variant' => 'default',
    'color' => 'primary',
    'size' => 'md',
    'animated' => true,
])

@php
    // Size configurations
    $sizes = [
        'sm' => ['padding' => '40px 20px', 'icon' => 'fs-3x', 'iconBox' => '70px', 'title' => 'fs-5', 'text' => 'fs-7'],
        'md' => ['padding' => '60px 20px', 'icon' => 'fs-4x', 'iconBox' => '100px', 'title' => 'fs-3', 'text' => 'fs-6'],
        'lg' => ['padding' => '80px 30px', 'icon' => 'fs-5x', 'iconBox' => '130px', 'title' => 'fs-2', 'text' => 'fs-5'],
    ];
    $sizeConfig = $sizes[$size] ?? $sizes['md'];

    // Variant styles
    $variants = [
        'default' => [
            'bg' => '#f9fafb',
            'iconBg' => 'white',
            'iconShadow' => '0 8px 24px rgba(0, 0, 0, 0.08)',
        ],
        'minimal' => [
            'bg' => 'transparent',
            'iconBg' => "var(--bs-{$color}-light, #f1f5f9)",
            'iconShadow' => 'none',
        ],
        'illustrated' => [
            'bg' => 'linear-gradient(180deg, #f8fafc 0%, #ffffff 100%)',
            'iconBg' => 'white',
            'iconShadow' => '0 12px 32px rgba(0, 0, 0, 0.1)',
        ],
        'gradient' => [
            'bg' => "linear-gradient(135deg, var(--bs-{$color}-light, #eff6ff) 0%, #ffffff 100%)",
            'iconBg' => 'white',
            'iconShadow' => "0 8px 24px var(--bs-{$color}-light, rgba(59, 130, 246, 0.15))",
        ],
    ];
    $variantConfig = $variants[$variant] ?? $variants['default'];

    // Animation class
    $animationClass = $animated ? 'empty-state-animated' : '';
@endphp

<div class="empty-state {{ $animationClass }}"
     style="background: {{ $variantConfig['bg'] }}; padding: {{ $sizeConfig['padding'] }}; border-radius: 16px; text-align: center;">

    {{-- Custom Illustration Slot --}}
    @if (isset($illustration))
        <div class="empty-state-illustration mb-6">
            {{ $illustration }}
        </div>
    @else
        {{-- Icon Container --}}
        <div class="empty-state-icon d-inline-flex align-items-center justify-content-center mb-5"
             style="width: {{ $sizeConfig['iconBox'] }}; height: {{ $sizeConfig['iconBox'] }}; background: {{ $variantConfig['iconBg'] }}; border-radius: 50%; box-shadow: {{ $variantConfig['iconShadow'] }};">
            {!! getIcon($icon, $sizeConfig['icon'] . ' text-' . $color) !!}
        </div>
    @endif

    {{-- Title --}}
    <h3 class="text-gray-800 fw-bold mb-3 {{ $sizeConfig['title'] }}">{{ $title }}</h3>

    {{-- Message / Custom Content --}}
    @if (isset($slot) && ! empty(trim($slot)))
        <div class="text-gray-500 {{ $sizeConfig['text'] }} mb-6 mx-auto" style="max-width: 400px;">
            {{ $slot }}
        </div>
    @elseif ($message)
        <p class="text-gray-500 {{ $sizeConfig['text'] }} mb-6 mx-auto" style="max-width: 400px;">
            {{ $message }}
        </p>
    @endif

    {{-- Actions --}}
    @if (isset($action))
        <div class="empty-state-actions d-flex flex-wrap justify-content-center gap-3">
            {{ $action }}
        </div>
    @elseif ($actionText)
        @php
            $showAction = true;
            if ($actionPermission) {
                $showAction = auth()->check() && auth()->user()->can($actionPermission);
            }
        @endphp

        @if ($showAction)
            <div class="empty-state-actions d-flex flex-wrap justify-content-center gap-3">
                @if ($actionModal)
                    <button class="btn btn-{{ $color }}" data-bs-toggle="modal" data-bs-target="#{{ $actionModal }}">
                        {!! getIcon('plus', 'fs-4 me-1') !!}
                        {{ $actionText }}
                    </button>
                @elseif ($actionUrl)
                    <a href="{{ $actionUrl }}" class="btn btn-{{ $color }}">
                        {!! getIcon('plus', 'fs-4 me-1') !!}
                        {{ $actionText }}
                    </a>
                @endif

                @if ($secondaryActionText && $secondaryActionUrl)
                    <a href="{{ $secondaryActionUrl }}" class="btn btn-light-{{ $color }}">
                        {{ $secondaryActionText }}
                    </a>
                @endif
            </div>
        @endif
    @endif
</div>

{{-- Animation Styles --}}
@if ($animated)
    @once
        @push('styles')
            <style>
                .empty-state-animated .empty-state-icon {
                    animation: emptyStateFloat 3s ease-in-out infinite;
                }

                @keyframes emptyStateFloat {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-8px); }
                }

                .empty-state-animated:hover .empty-state-icon {
                    animation-play-state: paused;
                    transform: scale(1.05);
                    transition: transform 0.3s ease;
                }
            </style>
        @endpush
    @endonce
@endif
