{{-- Agent application layout: renders the application form inside the dashboard chrome --}}
<?php app(config('settings.KT_THEME_BOOTSTRAP.default'))->init(); ?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" {!! printHtmlAttributes('html') !!}>

<head>
    <base href="" />
    <title>@yield('title', 'Apply Now') - {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="canonical" href="{{ url()->current() }}" />

    {!! includeFavicon() !!}

    <!--begin::Fonts-->
    {!! includeFonts() !!}
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle-->
    @foreach (getGlobalAssets('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Vendor Stylesheets-->
    @foreach (getVendors('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Vendor Stylesheets-->

    <!--begin::Custom Stylesheets-->
    @foreach (getCustomCss() as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Custom Stylesheets-->

    {{-- Alpine.js cloak style --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')

    <script src="{{ asset('messages.js') }}"></script>
</head>

<body {!! printHtmlClasses('body') !!} {!! printHtmlAttributes('body') !!}>

    @include('partials/theme-mode/_init')

    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            @include(config('settings.KT_THEME_LAYOUT_DIR') . '/partials/sidebar-layout/_header')
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                @include(config('settings.KT_THEME_LAYOUT_DIR') . '/partials/sidebar-layout/_sidebar')
                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">
                        @include(config('settings.KT_THEME_LAYOUT_DIR') . '/partials/sidebar-layout/_toolbar')
                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <!--begin::Content container-->
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                @yield('content')
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                    @include(config('settings.KT_THEME_LAYOUT_DIR') . '/partials/sidebar-layout/_footer')
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    @include('partials/_scrolltop')

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!--begin::Global Javascript Bundle-->
    @foreach (getGlobalAssets() as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Global Javascript Bundle-->

    <!--begin::Vendors Javascript-->
    @foreach (getVendors('js') as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Vendors Javascript-->

    <!--begin::Custom Javascript-->
    @foreach (getCustomJs() as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Custom Javascript-->

    @stack('scripts')

    <script>
        (function() {
            function notify(type, message) {
                if (window.toastr) {
                    if (type === 'success') toastr.success(message);
                    else if (type === 'error') toastr.error(message);
                    else toastr.info(message);
                }
            }

            document.addEventListener('app:success', function(e){ notify('success', e.detail?.message || 'Success'); });
            document.addEventListener('app:error', function(e){ notify('error', e.detail?.message || 'Error'); });
            document.addEventListener('app:swal', function(e){
                const detail = e.detail || {};
                Swal.fire({
                    text: detail.message || '',
                    icon: detail.icon || 'success',
                    buttonsStyling: false,
                    confirmButtonText: detail.confirmButtonText || (window.Lang ? Lang.get('Ok, got it!') : 'Ok, got it!'),
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            });
        })();
    </script>

    <script src="{{ asset('messages.js') }}"></script>
</body>

</html>
