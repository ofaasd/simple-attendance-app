<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite('resources/css/app.css')
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>
                    @endauth
                </div>
            @endif

            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="flex justify-center">
                    <img src="{{ asset('/img/logo_kampi.png') }}" alt="Logo Kampi" class="h-32 w-auto">
                </div>

                <div class="mt-16 text-center">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Selamat Datang di Sistem Informasi Monitoring dan Evaluasi KAMPI</h1>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Silakan login untuk melanjutkan.</p>
                    <a href="{{ route('login') }}" class="mt-6 inline-block px-6 py-3 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition-all">Login</a>
                </div>
                
                <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
                    <div class="text-center text-sm sm:text-left">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
