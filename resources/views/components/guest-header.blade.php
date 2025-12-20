@props(['showLogin' => true])

<header style="background-color: var(--bs-app-sidebar-base-bg-color);" class="py-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            {{-- Logo Section --}}
            <a href="{{ route('home') }}" class="text-decoration-none">
                <img src="{{ image('logos/swenamcollege_logo.png') }}" alt="Swenam College Logo" class="h-50px" />
            </a>

            {{-- Action Buttons --}}
            <div class="d-flex align-items-center gap-3">
                @if($showLogin)
                    <a href="{{ route('login') }}" class="btn btn-light btn-sm fw-semibold px-5">
                        LOGIN
                    </a>
                @endif
                <a href="{{ route('application.step1') }}" class="btn btn-primary btn-sm fw-semibold px-5">
                    APPLY NOW
                </a>
            </div>
        </div>
    </div>
</header>

