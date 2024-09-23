<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @include('layouts.css')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
            </div>
            @include('layouts.navbar')
            @include('layouts.sidebar')

                {{ $slot }}

            <!-- Page Content -->
            {{-- <main>
                {{ $slot }}
            </main> --}}
            <footer class="main-footer">
                <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                  <b>Version</b> 3.2.0
                </div>
            </footer>
        </div>
        @include('layouts.script')
        <script>
            $(function(){
                $(".logout").click(function(){
                    $.post('{{url('logout')}}',{ '_token': '{{csrf_token()}}' }, function (data){
                        window.location = "{{url('dashboard')}}"
                    });
                });
            });

        </script>
    </body>
</html>
