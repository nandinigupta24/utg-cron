<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-js ">
    <head>  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="description" content="Report System">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') | UTGAPI</title>

        <!-- Styles -->

        <link rel="stylesheet" href="{{ asset('assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.addons.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/iconfonts/font-awesome/css/font-awesome.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
        <link rel="stylesheet" href="{{ asset('assets/css/select2.css')}}">
        
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css"/>-->
        @yield('css')

    </head>
    <body>
        <div class="container-scroller">
            @include('elements.header')
            <div class="container-fluid page-body-wrapper">
                @include('elements.sidebar')
                <div class="main-panel">
                    @yield('content')
                    @include('elements.footer')
                </div>

            </div>
        </div>
        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
        <script src="{{ asset('assets/vendors/js/vendor.bundle.addons.js')}}"></script>
        <!-- endinject -->
        <!-- inject:js -->
        <script src="{{ asset('assets/js/off-canvas.js')}}"></script>
        <script src="{{ asset('assets/js/misc.js')}}"></script>
        <script src="{{ asset('assets/js/dashboard.js')}}"></script>
        
        
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
        $( function() {
          var dateFormat = "yy-mm-dd",
            from = $( "#start" )
              .datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'yy-mm-dd'
              })
              .on( "change", function() {
                to.datepicker( "option", "minDate", getDate( this ) );
              }),
            to = $( "#end" ).datepicker({
              defaultDate: "+1w",
              changeMonth: true,
              numberOfMonths: 1,
              dateFormat: 'yy-mm-dd'
            })
            .on( "change", function() {
              from.datepicker( "option", "maxDate", getDate( this ) );
            });

          function getDate( element ) {
            var date;
            try {
              date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
              date = null;
            }

            return date;
          }
        } );
  </script>
  <script src="{{ asset('assets/js/select2.js')}}"></script>
  <script>
      $(function () {
            $('.select2').select2();
      })
      </script>
  @yield('script')
<!--<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js"></script>-->
<!--<script>
    $(document).ready(function() {
    $('#example').DataTable({"processing": true,
        "serverSide": true,
        "ajax": "scripts/server_processing.php"});
} );
    </script>-->
    </body>
</html>
