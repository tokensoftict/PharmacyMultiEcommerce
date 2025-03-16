<html data-bs-theme="{{ app(\App\Classes\Settings::class)->uiSettings('theme') }}" data-navigation-type="{{ app(\App\Classes\Settings::class)->uiSettings('navigation_type') }}" data-navbar-horizontal-shape="{{ app(\App\Classes\Settings::class)->uiSettings('horizontal_shape') }}" lang="{{ app(\App\Classes\Settings::class)->uiSettings('lang') }}" dir="{{ app(\App\Classes\Settings::class)->uiSettings('dir') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ app(\App\Classes\Settings::class)->uiSettings('title') ?? config('app.name') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <!--
    <link rel="manifest" href="{{ asset('images/manifest.json') }}">
    -->
    <meta name="msapplication-TileImage" content="{{ asset('images/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('js/simplebar.min.js') }}"></script>
    <script src="{{ asset('js/config.js') }}"></script>

    <link href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css" rel="stylesheet" type="text/css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('css/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- <link href="{{ asset('css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl"> -->
    <link href="{{ asset('css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
@stack('css')

@livewireStyles
    <script src="/rappasoft/laravel-livewire-tables/core.min.js"  ></script>
    <script src="/rappasoft/laravel-livewire-tables/thirdparty.min.js"  ></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>
