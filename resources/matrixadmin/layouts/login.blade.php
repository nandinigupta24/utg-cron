<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-js ">
    <head>  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Report System">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_new/images/favicon.png')}}">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <link rel="stylesheet" href="{{customAsset('css/style.min.css')}}">
        @yield('css')
    </head>
    <body class="theme-blush">

        @yield('content')
        <script src="{{ customAsset('libs/jquery/dist/jquery.min.js')}}"></script>
        <script src="{{ customAsset('libs/popper.js/dist/umd/popper.min.js')}}"></script>
        <script src="{{ customAsset('libs/bootstrap/dist/js/bootstrap.min.js')}}"></script>
        
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>

    $('[data-toggle="tooltip"]').tooltip();
    $(".preloader").fadeOut();
    // ============================================================== 
    // Login and Recover Password 
    // ============================================================== 
    $('#to-recover').on("click", function() {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });
    $('#to-login').click(function(){
        
        $("#recoverform").hide();
        $("#loginform").fadeIn();
    });
    </script>
    </body>
</html>
