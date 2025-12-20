@extends('layout.master')

@section('content')

    <!--begin::Header-->
    <x-guest-header />
    <!--end::Header-->

    <!--begin::Hero Section-->
    <section class="bgi-size-cover bgi-position-center position-relative d-flex align-items-center" style="background-image: linear-gradient(rgba(18, 41, 76, 0.7), rgba(18, 41, 76, 0.7)), url('{{ asset("assets/media/u-scaled.jpg") }}'); min-height: 60vh;">
        <div class="overlay overlay-primary opacity-70 position-absolute top-0 start-0 w-100 h-100"></div>
        <div class="container position-relative z-index-1">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="text-white fs-2qx fw-bolder mb-6">Welcome to Swenam College</h1>
                    <p class="text-white fs-4 mb-8 opacity-95">Transform your future with quality education. Start your journey with us today.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mb-8">
                        <a href="{{ route('application.step1') }}" class="btn btn-primary btn-lg fw-semibold px-10">
                            Apply Now
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-semibold px-8">
                            Login
                        </a>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('application.status') }}" class="text-white text-decoration-none">
                            <i class="ki-outline ki-search fs-5 me-2"></i>Already applied? Check your application status
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end::Hero Section-->

    <!--begin::Call to Action-->
    <section class="py-20 bg-light" id="about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8 mx-auto text-center">
                    <h2 class="text-gray-900 fw-bolder mb-6 fs-1">
                        {{ __('Ready to Get Started?') }}
                    </h2>
                    <p class="text-gray-600 fw-semibold fs-3 mb-10">
                        {{ __('Join our vibrant community of learners and start your educational journey today. The application process is simple and straightforward.') }}
                    </p>
                    <div class="d-flex gap-4 justify-content-center flex-wrap">
                        <a href="{{ route('application.step1') }}" class="btn btn-primary btn-lg fw-semibold px-10 py-4">
                            {{ __('Start Your Application') }}
                        </a>
                        <a href="{{ route('application.status') }}" class="btn btn-light btn-lg fw-semibold px-10 py-4">
                            {{ __('Check Application Status') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end::Call to Action-->

    <!--begin::Footer-->
    <x-guest-footer />
    <!--end::Footer-->

@endsection
