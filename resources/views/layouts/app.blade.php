<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hindutithi') }} — Panchang Demo</title>
        <meta name="description" content="Explore Panchang calculations, Hindu calendar data, and API testing powered by the vittix/panchang package.">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-slate-950 text-slate-100 min-h-screen" style="font-family: 'Inter', sans-serif;">

        {{-- Subtle star/gradient backdrop --}}
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 h-[600px] w-[600px] rounded-full bg-amber-600/5 blur-3xl"></div>
            <div class="absolute top-1/2 -left-40 h-[500px] w-[500px] rounded-full bg-sky-600/5 blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 h-[400px] w-[400px] rounded-full bg-violet-600/5 blur-3xl"></div>
        </div>

        <div class="flex min-h-screen flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-white/10 bg-slate-900/60 backdrop-blur">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                @yield('content')
            </main>

            <footer class="border-t border-white/5 py-6 text-center text-xs text-slate-600">
                Powered by <span class="text-amber-500/70 font-medium">vittix/panchang</span> · Hindutithi Demo
            </footer>
        </div>
    </body>
</html>
