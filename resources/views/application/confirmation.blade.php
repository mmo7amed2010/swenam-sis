@extends('application.layout')

@section('page-title', 'Application Submitted')

@section('content')
    <div class="text-center py-15">
        {{-- Success Icon --}}
        <div class="mb-10">
            <i class="ki-outline ki-check-circle fs-5tx text-success"></i>
        </div>

        {{-- Success Message --}}
        <h1 class="fw-bold text-gray-900 mb-5">{{ __('Application Submitted Successfully!') }}</h1>

        {{-- Reference Number --}}
        <div class="mb-8">
            <p class="fs-5 text-muted mb-2">{{ __('Your Application Reference Number:') }}</p>
            <div class="fs-2 fw-bold text-primary" style="letter-spacing: 2px;">
                {{ $application->reference_number }}
            </div>
        </div>

        {{-- Password Email Notice --}}
        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-10 mx-auto justify-content-center" style="max-width: 500px;">
            <i class="ki-outline ki-sms fs-2tx text-info me-4"></i>
            <div class="text-start">
                <div class="fs-5 text-gray-800 fw-semibold">
                    {{ __('Your login credentials have been sent to your email address.') }}
                </div>
                <div class="fs-6 text-muted mt-1">
                    {{ $application->email }}
                </div>
            </div>
        </div>

        {{-- Login Button --}}
        <div class="mt-10">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                <i class="ki-outline ki-entrance-right fs-3 me-2"></i>
                {{ __('Login to Your Account') }}
            </a>
        </div>
    </div>
@endsection
