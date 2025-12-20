@props([
    'href' => '#',
    'permission' => null,
    'icon' => null,
    'danger' => false,
])

<div class="menu-item px-3">
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'menu-link px-3' . ($danger ? ' text-danger' : '')]) }}>
        @if($icon)
            {!! getIcon($icon, 'fs-6 me-2') !!}
        @endif
        {{ $slot }}
    </a>
</div>
