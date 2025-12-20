{{--
/**
 * Generic Card Component
 *
 * Flexible card container with optional header, body, and footer slots.
 * Base component for creating consistent card layouts throughout the application.
 *
 * @param string|null $title - Card title in header (alternative to headerSlot)
 * @param string $flush - Add 'flush' class for borderless card (default: 'flush')
 * @param string $padding - Card padding class: py-4|py-6|py-8 (default: 'py-4')
 * @param string $cardClass - Additional CSS classes for the card
 * @param string $headerClass - Additional CSS classes for the header
 * @param string $bodyClass - Additional CSS classes for the body
 * @param string $footerClass - Additional CSS classes for the footer
 *
 * @slot header - Custom header content (overrides $title)
 * @slot default - Main card body content
 * @slot footer - Card footer content
 * @slot actions - Quick actions in header (displayed on right side)
 *
 * @example Simple Card with Title
 * <x-cards.card title="User Details">
 *     <p>Card content goes here</p>
 * </x-cards.card>
 *
 * @example Card with Custom Header and Footer
 * <x-cards.card>
 *     <x-slot:header>
 *         <div class="d-flex justify-content-between">
 *             <h3>Custom Header</h3>
 *             <button>Action</button>
 *         </div>
 *     </x-slot:header>
 *
 *     <p>Main content</p>
 *
 *     <x-slot:footer>
 *         <button>Save</button>
 *     </x-slot:footer>
 * </x-cards.card>
 *
 * @example Card with Title and Actions
 * <x-cards.card title="Recent Activity">
 *     <x-slot:actions>
 *         <button class="btn btn-sm btn-light">View All</button>
 *     </x-slot:actions>
 *
 *     <p>Activity list...</p>
 * </x-cards.card>
 */
--}}

@props([
    'title' => null,
    'flush' => 'flush',
    'padding' => 'py-4',
    'cardClass' => '',
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

<div class="card card-{{ $flush }} {{ $padding }} {{ $cardClass }}">
    {{-- Header --}}
    @if($title || isset($header) || isset($actions))
    <div class="card-header {{ $headerClass }}">
        @if(isset($header))
            {{ $header }}
        @else
            <div class="card-title {{ isset($actions) ? 'd-flex justify-content-between align-items-center w-100' : '' }}">
                @if($title)
                <h3 class="fw-bold text-gray-900 m-0">{{ $title }}</h3>
                @endif

                @if(isset($actions))
                <div class="card-toolbar">
                    {{ $actions }}
                </div>
                @endif
            </div>
        @endif
    </div>
    @endif

    {{-- Body --}}
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    @if(isset($footer))
    <div class="card-footer {{ $footerClass }}">
        {{ $footer }}
    </div>
    @endif
</div>
