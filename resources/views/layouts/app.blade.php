<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Vittix Vedic Panchang — Professional Panchang & Astrology Engine for PHP</title>
        <meta name="description" content="Vittix Vedic Panchang provides accurate Panchang and Vedic astrology (Kundli) calculations for PHP and Laravel with timezone-aware REST APIs and developer tools.">

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

            <footer class="border-t border-white/5 py-8 text-slate-400">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="space-y-2 text-sm text-slate-300">
                            <div>© 2026 Vittix</div>
                            <div>Professional Vedic Panchang engine for PHP, Laravel, and REST APIs.</div>
                        </div>
                        <div class="flex flex-wrap justify-center gap-4 text-sm">
                                <a href="{{ route('api.docs') }}" class="text-slate-300 hover:text-white">Documentation</a>
                                <a href="{{ route('hindutithi.accuracy') }}" class="text-slate-300 hover:text-white">Accuracy</a>
                                <a href="{{ route('hindutithi.kundli') }}" class="text-slate-300 hover:text-white">Kundli</a>
                                <a href="{{ route('hindutithi.whats_new') }}" class="text-slate-300 hover:text-white">What's New</a>
                                <a href="{{ route('hindutithi.home') }}" class="text-slate-300 hover:text-white">Demo</a>
                                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="text-slate-300 hover:text-white">GitHub</a>
                                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/issues" target="_blank" class="text-slate-300 hover:text-white">Issues</a>
                                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/blob/main/LICENSE" target="_blank" class="text-slate-300 hover:text-white">License</a>
                                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/blob/main/CHANGELOG.md" target="_blank" class="text-slate-300 hover:text-white">Changelog</a>
                            </div>
                    </div>
                    <div class="mt-6 text-center text-xs text-slate-500 md:text-left">
                        Powered by <span class="text-amber-500/70 font-medium">vittix/panchang</span> · Hindutithi Demo
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
