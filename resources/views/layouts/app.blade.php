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
    <body class="sidebar-mini">
        <div class="wrapper">
            @include('layouts.navbar')
            @include('layouts.sidebar')

            {{ $slot }}

            <footer class="main-footer">
                <strong>Copyright © 2026 <a href="https://rmediasolusindo.com">Rahman Media Solusindo</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                  <b>Version</b> 1.0.0 (beta)</b>
                </div>
            </footer>
        </div>
        @include('layouts.script')
        <script>
            $(document).ready(function(){
                // Force remove any overlay or backdrop
                $('body').removeClass('hold-transition');
                $('.modal-backdrop, .overlay, .preloader').remove();

                $(".logout").click(function(){
                    $.post('{{url('logout')}}',{ '_token': '{{csrf_token()}}' }, function (data){
                        window.location = "{{url('dashboard')}}"
                    });
                });
            });
        </script>
    </body>
</html>

