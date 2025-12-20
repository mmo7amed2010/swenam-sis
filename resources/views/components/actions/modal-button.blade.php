@props([
    'target' => '#',
    'permission' => null,
    'icon' => null,
    'danger' => false,
])

<div class="menu-item px-3">
    <button
        type="button"
        data-bs-toggle="modal"
        data-bs-target="{{ $target }}"
        {{ $attributes->merge(['class' => 'menu-link px-3 w-100 text-start border-0 bg-transparent' . ($danger ? ' text-danger' : '')]) }}
    >
        @if($icon)
            {!! getIcon($icon, 'fs-6 me-2') !!}
        @endif
        {{ $slot }}
    </button>
</div>
