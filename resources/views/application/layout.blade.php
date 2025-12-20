<!DOCTYPE html>
<html lang="en">
<head>
    <base href=""/>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="shortcut icon" href="{{ asset(config('settings.KT_THEME_ASSETS.favicon')) }}" />

    {{-- Fonts --}}
    {!! includeFonts() !!}

    {{-- Global Stylesheets Bundle (mandatory for all pages) --}}
    @foreach(config('settings.KT_THEME_ASSETS.global.css', []) as $path)
        <link rel="stylesheet" href="{{ asset($path) }}">
    @endforeach

    {{-- Vendors Stylesheets --}}
    @foreach(config('settings.KT_THEME_ASSETS.vendors.css', []) as $path)
        <link rel="stylesheet" href="{{ asset($path) }}">
    @endforeach

    <title>@yield('title', 'Apply Now') - {{ config('app.name') }}</title>

    {{-- Alpine.js cloak style --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>

<body id="kt_app_body" class="app-default">
    {{-- Header --}}
    <x-guest-header :showLogin="false" />

    {{-- Hero Section --}}
    <section class="bgi-size-cover bgi-position-center position-relative py-15" style="background-image: linear-gradient(rgba(18, 41, 76, 0.7), rgba(18, 41, 76, 0.7)), url('{{ asset("assets/media/u-scaled.jpg") }}');">
        <div class="overlay overlay-primary opacity-70"></div>
        <div class="container position-relative z-index-1">
            <div class="d-flex align-items-center gap-2 text-white fs-6 mb-8">
                <a href="{{ route('home') }}" class="text-white text-decoration-none text-hover-primary">
                    <i class="ki-outline ki-home fs-5"></i>
                </a>
                <span class="opacity-75">/</span>
                <span class="opacity-75">Apply Now</span>
            </div>
            <h1 class="text-white fs-2qx fw-bolder text-center mb-2">Apply Now</h1>
            <p class="text-white fs-5 text-center opacity-90">Complete your application in 5 simple steps</p>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="d-flex flex-column flex-root app-root">
        <div class="app-page flex-column flex-column-fluid">
            <div class="app-wrapper flex-column flex-row-fluid">
                <div class="app-main flex-column flex-row-fluid">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div class="app-content flex-column-fluid">
                            <div class="app-container container-xxl">
                                {{-- Main Card --}}
                                <div class="card shadow-lg border-0 p-10 mb-10" style="margin-top: -2rem; position: relative; z-index: 1;">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Global Javascript Bundle --}}
    @foreach(config('settings.KT_THEME_ASSETS.global.js', []) as $path)
        <script src="{{ asset($path) }}"></script>
    @endforeach

    {{-- Vendors Javascript --}}
    @foreach(config('settings.KT_THEME_ASSETS.vendors.js', []) as $path)
        <script src="{{ asset($path) }}"></script>
    @endforeach

    {{-- Footer --}}
    <x-guest-footer />

    @stack('scripts')
</body>
</html>
