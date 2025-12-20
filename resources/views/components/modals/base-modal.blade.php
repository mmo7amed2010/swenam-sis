{{--
/**
 * Base Modal Component
 *
 * Standard Bootstrap 5 modal with header, body, and footer sections.
 * Provides consistent modal structure across the application.
 *
 * @param string $id - Unique modal ID (required for trigger buttons)
 * @param string $title - Modal title in header
 * @param string|null $size - Modal size: sm|lg|xl (default: normal)
 * @param bool $centered - Center modal vertically (default: true)
 * @param bool $scrollable - Enable scrollable modal body (default: false)
 * @param bool $static - Disable backdrop click to close (default: false)
 * @param string $headerClass - Additional CSS classes for header
 * @param string $bodyClass - Additional CSS classes for body
 * @param string $footerClass - Additional CSS classes for footer
 *
 * @slot header - Custom header content (overrides $title)
 * @slot default - Modal body content
 * @slot footer - Modal footer content (buttons, etc.)
 *
 * @example Simple Modal
 * <x-modals.base-modal id="exampleModal" title="Confirm Action">
 *     <p>Are you sure you want to proceed?</p>
 *
 *     <x-slot:footer>
 *         <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
 *         <button type="button" class="btn btn-primary">Confirm</button>
 *     </x-slot:footer>
 * </x-modals.base-modal>
 *
 * @example Large Scrollable Modal
 * <x-modals.base-modal id="detailsModal" title="Details" size="lg" :scrollable="true">
 *     <p>Long content that may require scrolling...</p>
 * </x-modals.base-modal>
 *
 * @example Modal with Custom Header
 * <x-modals.base-modal id="customModal">
 *     <x-slot:header>
 *         <div class="d-flex align-items-center">
 *             <i class="bi bi-info-circle me-2"></i>
 *             <h5 class="modal-title">Custom Header</h5>
 *         </div>
 *         <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
 *     </x-slot:header>
 *
 *     <p>Modal content</p>
 * </x-modals.base-modal>
 *
 * @trigger To open modal, use:
 * <button data-bs-toggle="modal" data-bs-target="#exampleModal">Open Modal</button>
 */
--}}

@props([
    'id',
    'title' => null,
    'size' => null,
    'centered' => true,
    'scrollable' => false,
    'static' => false,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

@php
    $dialogClass = 'modal-dialog';
    if ($centered) $dialogClass .= ' modal-dialog-centered';
    if ($scrollable) $dialogClass .= ' modal-dialog-scrollable';
    if ($size) $dialogClass .= ' modal-' . $size;

    $staticAttr = $static ? 'data-bs-backdrop="static" data-bs-keyboard="false"' : '';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true" {!! $staticAttr !!}>
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            {{-- Header --}}
            @if($title || isset($header))
            <div class="modal-header {{ $headerClass }}">
                @if(isset($header))
                    {{ $header }}
                @else
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
            </div>
            @endif

            {{-- Body --}}
            <div class="modal-body {{ $bodyClass }}">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if(isset($footer))
            <div class="modal-footer {{ $footerClass }}">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>
