<footer style="background-color: var(--bs-app-sidebar-base-bg-color);" class="py-10 mt-auto">
    <div class="container">
        <div class="row g-5 g-lg-10">
            <!--begin::Logo and Description-->
            <div class="col-lg-6">
                <a href="{{ route('home') }}" class="mb-6 d-inline-block">
                    <img src="{{ image('logos/swenamcollege_logo.png') }}" alt="Swenam College Logo" class="h-40px" />
                </a>
                <div class="text-white opacity-75 fs-6">
                    {{ __('Transform your future with quality education. Start your journey with us today.') }}
                </div>
            </div>
            <!--end::Logo and Description-->

            <!--begin::Quick Links-->
            <div class="col-lg-6">
                <h4 class="text-white fw-bold mb-6">{{ __('Quick Links') }}</h4>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <a href="{{ route('home') }}" class="text-white opacity-75 text-hover-primary text-decoration-none fs-6">
                            {{ __('Home') }}
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('application.step1') }}" class="text-white opacity-75 text-hover-primary text-decoration-none fs-6">
                            {{ __('Apply Now') }}
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('application.status') }}" class="text-white opacity-75 text-hover-primary text-decoration-none fs-6">
                            {{ __('Check Application Status') }}
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('login') }}" class="text-white opacity-75 text-hover-primary text-decoration-none fs-6">
                            {{ __('Login') }}
                        </a>
                    </li>
                </ul>
            </div>
            <!--end::Quick Links-->
        </div>

        <!--begin::Copyright-->
        <div class="separator separator-border border-white opacity-25 my-10"></div>
        <div class="text-center">
            <div class="text-white opacity-75 fs-6">
                {{ date('Y') }} &copy; {{ __('Swenam College. All rights reserved.') }}
            </div>
        </div>
        <!--end::Copyright-->
    </div>
</footer>

