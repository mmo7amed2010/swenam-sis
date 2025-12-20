{{--
    Quick Actions Component - Metronic Design System Aligned

    A card with quick navigation buttons for the student dashboard.

    @props
    - program: ?Program - Whether user has a program
    - actions: array - Optional custom actions array

    @example
    <x-student.quick-actions :program="$program" />
--}}

@props([
    'program' => null,
    'actions' => null,
])

@php
    $defaultActions = [
        [
            'url' => route('student.program.index'),
            'label' => __('My Program'),
            'icon' => 'book-open',
            'color' => 'primary',
        ],
        [
            'url' => route('student.grades.index'),
            'label' => __('View Grades'),
            'icon' => 'chart-line-up',
            'color' => 'warning',
        ],
    ];

    $displayActions = $actions ?? $defaultActions;
@endphp

<div class="card card-flush h-xl-100">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-900">{{ __('Quick Actions') }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Navigate quickly') }}</span>
        </h3>
    </div>

    <div class="card-body pt-5">
        <nav class="d-flex flex-column gap-3" aria-label="{{ __('Quick navigation') }}">
            @foreach($displayActions as $action)
                <a href="{{ $action['url'] }}"
                   class="btn btn-flex btn-light-{{ $action['color'] }} btn-active-{{ $action['color'] }}">
                    {!! getIcon($action['icon'], 'fs-3 me-2') !!}
                    {{ $action['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</div>
