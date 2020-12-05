<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-js ">
    <head>  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Report System">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Styles -->

        <link rel="stylesheet" href="{{ asset('assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.addons.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
        @yield('css')

    </head>
    <body class="theme-blush">

        @yield('content')
        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
        <script src="{{ asset('assets/vendors/js/vendor.bundle.addons.js')}}"></script>
        <!-- endinject -->
        <!-- inject:js -->
        <script src="{{ asset('assets/js/off-canvas.js')}}"></script>
        <script src="{{ asset('assets/js/misc.js')}}"></script>
    </body>
</html>
