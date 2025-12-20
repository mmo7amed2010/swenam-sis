<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" {!! printHtmlAttributes('html') !!}>
<!--begin::Head-->

<head>
    <base href="" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="" />
    <link rel="canonical" href="{{ url()->current() }}" />

    {!! includeFavicon() !!}

    <!--begin::Fonts-->
    {!! includeFonts() !!}
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    @foreach (getGlobalAssets('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Vendor Stylesheets(used by this page)-->
    @foreach (getVendors('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Vendor Stylesheets-->

    <!--begin::Custom Stylesheets(optional)-->
    @foreach (getCustomCss() as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Custom Stylesheets-->

    @stack('styles')

    <!-- JavaScript translations -->
    <script src="{{ asset('messages.js') }}"></script>
</head>
<!--end::Head-->

<!--begin::Body-->

<body {!! printHtmlClasses('body') !!} {!! printHtmlAttributes('body') !!}>

    @include('partials/theme-mode/_init')

    @yield('content')

    <!--begin::Javascript-->
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    @foreach (getGlobalAssets() as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Global Javascript Bundle-->

    <!--begin::Vendors Javascript(used by this page)-->
    @foreach (getVendors('js') as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Vendors Javascript-->

    <!--begin::Custom Javascript(optional)-->
    @foreach (getCustomJs() as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Custom Javascript-->
    @stack('scripts')
    <!--end::Javascript-->

    <script>
        // Global app event helpers to replace Livewire notifications
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
<!--end::Body-->

</html>
