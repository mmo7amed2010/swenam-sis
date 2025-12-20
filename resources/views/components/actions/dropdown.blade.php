@props([
    'buttonText' => null,
    'buttonClass' => 'btn-sm btn-light',
    'buttonIcon' => 'dots-vertical',
    'menuClass' => '',
    'placement' => 'bottom-end',
])

<div {{ $attributes->merge(['class' => 'd-inline-block']) }}>
    <button
        class="btn {{ $buttonClass }}"
        data-kt-menu-trigger="click"
        data-kt-menu-placement="{{ $placement }}"
    >
        @if($buttonText)
            {{ $buttonText }}
        @endif
        {!! getIcon($buttonIcon, 'fs-2') !!}
    </button>

    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4 {{ $menuClass }}" data-kt-menu="true">
        {{ $slot }}
    </div>
</div>
