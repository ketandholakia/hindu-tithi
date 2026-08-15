<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Hindutithi'))</title>
        <meta name="description" content="@yield('meta_description', 'Vittix Vedic Panchang provides accurate Panchang and Vedic astrology (Kundli) calculations for PHP and Laravel with timezone-aware REST APIs and developer tools.')">
        @hasSection('robots')
            <meta name="robots" content="@yield('robots')">
        @endif

        <link rel="canonical" href="{{ url()->current() }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Hindutithi">
        <meta property="og:title" content="@yield('title', config('app.name', 'Hindutithi'))">
        <meta property="og:description" content="@yield('meta_description', 'Vittix Vedic Panchang provides accurate Panchang and Vedic astrology (Kundli) calculations for PHP and Laravel with timezone-aware REST APIs and developer tools.')">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="@yield('title', config('app.name', 'Hindutithi'))">
        <meta name="twitter:description" content="@yield('meta_description', 'Vittix Vedic Panchang provides accurate Panchang and Vedic astrology (Kundli) calculations for PHP and Laravel with timezone-aware REST APIs and developer tools.')">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebSite",
            "name": "Hindutithi",
            "url": "{{ url('/') }}",
            "description": "Vittix Vedic Panchang provides accurate Panchang and Vedic astrology (Kundli) calculations for PHP and Laravel with timezone-aware REST APIs and developer tools."
        }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased min-h-screen">

        {{-- Astronomical gradient backdrop --}}
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none bg-[var(--color-bg-base)]">
            <div class="absolute -top-40 -right-40 h-[800px] w-[800px] rounded-full bg-[var(--color-brand-saffron)]/5 blur-[120px]"></div>
            <div class="absolute top-1/3 -left-40 h-[600px] w-[600px] rounded-full bg-[var(--color-brand-warm)]/5 blur-[100px]"></div>
            <div class="absolute bottom-0 right-1/3 h-[500px] w-[500px] rounded-full bg-[var(--color-brand-gold)]/5 blur-[120px]"></div>
        </div>

        <div class="flex min-h-screen flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)]/60 backdrop-blur">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8 text-[var(--color-text-primary)]">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                @yield('content')
            </main>

            <footer class="border-t border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] py-12 mt-auto">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div class="space-y-2 text-sm text-[var(--color-text-muted)]">
                            <div class="flex items-center gap-2">
                                <span class="text-[var(--color-brand-saffron)] text-lg leading-none">☀</span>
                                <span class="font-semibold text-[var(--color-text-primary)] tracking-wide">Vittix Panchang</span>
                            </div>
                            <div class="mt-2">Professional Vedic Panchang engine for PHP, Laravel, and REST APIs.</div>
                            <div class="mt-4 text-xs">© {{ date('Y') }} Vittix. MIT Licensed.</div>
                        </div>
                        <div class="flex flex-wrap justify-center md:justify-end gap-x-8 gap-y-4 text-sm font-medium">
                            <a href="{{ route('hindutithi.day') }}" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Product</a>
                            <a href="{{ route('api.docs') }}" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Documentation</a>
                            <a href="{{ route('hindutithi.accuracy') }}" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Accuracy</a>
                            <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">GitHub</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
