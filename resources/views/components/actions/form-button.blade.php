@props([
    'action' => '#',
    'method' => 'POST',
    'permission' => null,
    'confirm' => null,
    'icon' => null,
    'danger' => false,
])

<div class="menu-item px-3">
    <form action="{{ $action }}" method="POST">
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif
        <button
            type="submit"
            {{ $attributes->merge(['class' => 'menu-link px-3 w-100 text-start border-0 bg-transparent' . ($danger ? ' text-danger' : '')]) }}
            @if($confirm) data-confirm="{{ $confirm }}" @endif
        >
            @if($icon)
                {!! getIcon($icon, 'fs-6 me-2') !!}
            @endif
            {{ $slot }}
        </button>
    </form>
</div>
