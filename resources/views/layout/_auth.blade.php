@extends('layout.master')

@section('content')


    <!--begin::Header-->
    <x-guest-header :showLogin="false" />
    <!--end::Header-->

    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root bg-white" id="kt_app_root">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1 bg-white">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Page-->
                        {{ $slot }}
                        <!--end::Page-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
            </div>
            <!--end::Body-->

            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2 position-relative" style="background-image: linear-gradient(rgba(18, 41, 76, 0.7), rgba(18, 41, 76, 0.7)), url('{{ asset("assets/media/u-scaled.jpg") }}');">
                <div class="overlay overlay-primary opacity-70"></div>
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100 position-relative z-index-1">
                    <!--begin::Logo-->
                    <a href="{{ route('home') }}" class="mb-12">
                        <img alt="{{ __('Logo') }}" src="{{ image('logos/swenamcollege_logo.png') }}" class="h-60px h-lg-75px"/>
                    </a>
                    <!--end::Logo-->

                    <!--begin::Title-->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        {{ __('Welcome to Swenam College') }}
                    </h1>
                    <!--end::Title-->

                    <!--begin::Text-->
                    <div class="d-none d-lg-block text-white fs-base text-center">
                        {{ __('Sign in to access your student portal and continue your educational journey with us.') }}
                    </div>
                    <!--end::Text-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::App-->

@endsection
