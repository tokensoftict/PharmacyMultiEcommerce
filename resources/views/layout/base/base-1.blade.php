<html
        data-bs-theme="{{ app(\App\Classes\Settings::class)->uiSettings('theme') }}"
        data-navigation-type="horizontal"
        data-navbar-horizontal-shape="{{ app(\App\Classes\Settings::class)->uiSettings('horizontal_shape') }}"
        lang="{{ app(\App\Classes\Settings::class)->uiSettings('lang') }}" dir="{{ app(\App\Classes\Settings::class)->uiSettings('dir') }}"
        class="chrome osx fontawesome-i2svg-active fontawesome-i2svg-complete"
>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ app(\App\Classes\Settings::class)->get("name", "PS GENERAL DRUGS CENTRE PHARMACY.") ?? config('app.name') }}</title>


    <script src="{{ asset('js/config.js') }}"></script>

    <link href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css" rel="stylesheet" type="text/css" />

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('css/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
@stack('css')

@livewireStyles
    <script src="{{ asset('rappasoft/laravel-livewire-tables/core.min.js?v=1.33') }}"  ></script>
    <script src="{{ asset('rappasoft/laravel-livewire-tables/thirdparty.min.js?v=1.33') }}"  ></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>
