<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-grayTheme-light text-grayTheme-dark">
        <div class="app-shell" x-data="sidebarLayout()">
            @include('layouts.navigation')
            @include('layouts.sidebar')

            <!-- Page Heading -->
            <main
                class="transition-all duration-200"
                :class="desktopCollapsed ? 'sm:ml-16' : 'sm:ml-64'"
            >
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
