{{--
/**
 * Alert Banner Component
 *
 * Styled alert banner for form pages with icon, title, and message.
 * Useful for warnings, information, and important notices.
 *
 * @param string $type - Alert type: warning|danger|info|success|primary (default: warning)
 * @param string|null $title - Optional bold title text
 * @param string|null $message - Alert message (can also use default slot)
 * @param string|null $icon - Custom icon (default: auto-selected based on type)
 * @param bool $dismissible - Allow dismissing the alert (default: false)
 *
 * @slot default - Alert message content (alternative to message prop)
 *
 * @example Basic Warning
 * <x-forms.alert-banner
 *     type="warning"
 *     title="Warning: Active Courses Exist"
 *     message="This program has 5 active courses. Changes may affect course visibility."
 * />
 *
 * @example With Slot Content
 * <x-forms.alert-banner type="info" title="Note">
 *     You can add <strong>HTML content</strong> in the slot.
 * </x-forms.alert-banner>
 *
 * @example Dismissible
 * <x-forms.alert-banner
 *     type="success"
 *     message="Your changes have been saved successfully."
 *     dismissible
 * />
 */
--}}

@props([
    'type' => 'warning',
    'title' => null,
    'message' => null,
    'icon' => null,
    'dismissible' => false,
])

@php
    $typeConfig = [
        'warning' => ['bg' => 'alert-warning', 'text' => 'text-warning', 'icon' => 'information-5'],
        'danger' => ['bg' => 'alert-danger', 'text' => 'text-danger', 'icon' => 'cross-circle'],
        'info' => ['bg' => 'alert-info', 'text' => 'text-info', 'icon' => 'information-2'],
        'success' => ['bg' => 'alert-success', 'text' => 'text-success', 'icon' => 'check-circle'],
        'primary' => ['bg' => 'alert-primary', 'text' => 'text-primary', 'icon' => 'information'],
    ];

    $config = $typeConfig[$type] ?? $typeConfig['warning'];
    $iconName = $icon ?? $config['icon'];
@endphp

<div {{ $attributes->merge(['class' => "alert {$config['bg']} d-flex align-items-center p-5 mb-6"]) }}
     @if($dismissible) role="alert" @endif>
    {{-- Icon --}}
    {!! getIcon($iconName, 'fs-2hx ' . $config['text'] . ' me-4') !!}

    {{-- Content --}}
    <div class="d-flex flex-column flex-grow-1">
        @if ($title)
            <h4 class="mb-1 {{ $config['text'] }}">{{ $title }}</h4>
        @endif
        <span>
            @if ($message)
                {{ $message }}
            @else
                {{ $slot }}
            @endif
        </span>
    </div>

    {{-- Dismiss Button --}}
    @if ($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    @endif
</div>
