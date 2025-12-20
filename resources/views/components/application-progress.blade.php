@props(['currentStep' => 1])

@php
    $steps = [
        1 => ['title' => 'Program Information', 'icon' => 'ki-outline ki-book'],
        2 => ['title' => 'Personal Information', 'icon' => 'ki-outline ki-profile-user'],
        3 => ['title' => 'Education History', 'icon' => 'ki-outline ki-graduation'],
        4 => ['title' => 'Work History', 'icon' => 'ki-outline ki-briefcase'],
        5 => ['title' => 'Supporting Documents', 'icon' => 'ki-outline ki-document']
    ];
@endphp

<div class="border-bottom border-gray-300 mb-10 pb-5">
    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold flex-nowrap gap-4">
        @foreach($steps as $step => $stepData)
            @php
                $isActive = $step == $currentStep;
                $isCompleted = $step < $currentStep;
            @endphp
            <li class="nav-item">
                <span class="nav-link d-flex align-items-center gap-2 text-gray-500 text-active-primary cursor-default {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'text-success' : '' }}">
                    @if($isCompleted)
                        <i class="ki-solid ki-check-circle fs-2 text-success"></i>
                    @else
                        <span class="badge badge-circle badge-{{ $isActive ? 'primary' : 'light' }} d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                            {{ $step }}
                        </span>
                    @endif
                    <span class="d-none d-md-inline">{{ $stepData['title'] }}</span>
                    <span class="d-md-none">{{ $step }}</span>
                </span>
            </li>
        @endforeach
    </ul>
</div>
