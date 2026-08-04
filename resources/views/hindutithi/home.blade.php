@extends('layouts.app')

@section('content')
    <div class="space-y-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-full bg-amber-500/10 border border-amber-500/20 px-4 py-2 inline-flex items-center gap-3 text-sm text-amber-200">
                <span class="font-semibold">🚀 Version 2.0 released</span>
                <span class="text-slate-300">Kundli & Astrology support —</span>
                <a href="/whats-new" class="underline text-amber-100">Read release notes</a>
            </div>
        </div>
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 shadow-2xl shadow-slate-950/20">
            <div class="grid gap-8 px-6 py-8 md:px-10 lg:grid-cols-[minmax(0,1.7fr)_minmax(320px,1fr)] lg:px-12 lg:py-12">
                <div class="space-y-6">
                    <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">
                        HinduTithi demo
                    </span>
                    <div class="space-y-4">
                        <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Professional Vedic Panchang & Astrology Engine for PHP, Laravel & REST APIs
                        </h1>
                        <p class="max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">
                            Generate Panchang, Kundli, planetary positions, muhurta, festivals and Hindu calendar data with one modern open-source package. HinduTithi is the official demo and playground for the `vittix/panchang` package.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="#installation" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                            Get Started
                        </a>
                        <a href="{{ route('api.docs') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Documentation
                        </a>
                        <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            ⭐ Star on GitHub
                        </a>
                    </div>

                    <div class="mt-3 text-sm text-slate-300">
                        <a href="{{ route('hindutithi.day') }}" class="font-semibold text-slate-100 hover:text-white">Try the live demo</a>
                    </div>

                    <div class="flex flex-wrap gap-3 text-sm text-slate-300">
                        <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/issues" target="_blank" class="hover:text-white">Report Issue</a>
                        <span class="text-slate-500">•</span>
                        <a href="{{ route('api.docs') }}" class="hover:text-white">API Reference</a>
                        <span class="text-slate-500">•</span>
                        <a href="{{ route('hindutithi.help') }}" class="hover:text-white">Help</a>
                    </div>
                </div>

                <div id="installation" class="rounded-3xl border border-white/10 bg-white/10 p-6 backdrop-blur">
                    <div class="mb-5 text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Installation</div>
                    <div class="rounded-3xl bg-slate-950/80 p-5">
                        <pre class="whitespace-pre-wrap text-sm text-slate-100">composer require vittix/panchang

use Vittix\Panchang\Panchang;

$panchang = Panchang::today('Mumbai');

echo $panchang->tithi->name;
</pre>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-300">
                        Install the package and run a complete end-to-end example in one place.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">
                            <div class="font-semibold text-white">Current release</div>
                                <div class="mt-2">v2.0.0</div>
                        </div>
                        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">
                            <div class="font-semibold text-white">Compatible</div>
                            <div class="mt-2">PHP 8.3+, Laravel 13</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[1fr_max-content]">
            <div class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">What is Vittix Panchang?</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white">A precision Panchang calculation engine built for developers.</h2>
                    <p class="mt-4 text-base leading-7 text-slate-300">
                        The package delivers daily tithi, nakshatra, yoga, karana, vara, sunrise, sunset, moonrise, moonset, festival calendars, and muhurta windows with accurate timezone support.
                        It is optimized for PHP applications, Laravel integrations, and API-driven interfaces.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Accuracy</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Timezone, DST, historical dates, and ephemeris-based calculations for reliable output.</p>
                        </div>
                        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Developer experience</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Simple PHP methods and REST endpoints make adoption fast and predictable.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Quick start</p>
                <div class="mt-4 rounded-3xl bg-slate-950/80 p-5">
                    <pre class="whitespace-pre-wrap text-sm text-slate-100">&lt;?php
$panchang = Panchang::today('Mumbai');

echo $panchang->tithi->name;
echo $panchang->nakshatra->name;
echo $panchang->sunrise;
</pre>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[1fr_max-content]">
            <div class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Trust</p>
                <h3 class="mt-3 text-2xl font-semibold text-white">Open source readiness</h3>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">✓ MIT Licensed</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">✓ Composer install</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">✓ REST API ready</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">✓ Timezone aware</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">✓ Tested on Laravel 13</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">✓ Open source demo</div>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-center text-sm text-slate-200">
                        <div class="text-2xl font-semibold text-white">20+</div>
                        <div class="mt-1 uppercase tracking-[0.2em] text-slate-400">Calculations</div>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-center text-sm text-slate-200">
                        <div class="text-2xl font-semibold text-white">6</div>
                        <div class="mt-1 uppercase tracking-[0.2em] text-slate-400">API endpoints</div>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-center text-sm text-slate-200">
                        <div class="text-2xl font-semibold text-white">MIT</div>
                        <div class="mt-1 uppercase tracking-[0.2em] text-slate-400">License</div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Features</p>
                <h3 class="mt-3 text-2xl font-semibold text-white">Supported calculations</h3>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach([
                        'Tithi', 'Nakshatra', 'Yoga', 'Karana', 'Vara', 'Sunrise', 'Sunset', 'Moonrise', 'Moonset',
                        'Rahu Kalam', 'Gulika', 'Yamaganda', 'Abhijit Muhurta', 'Brahma Muhurta', 'Festivals',
                        'Ekadashi', 'Chaturthi', 'Amavasya', 'Purnima', 'Sankranti',
                    ] as $feature)
                        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-sm text-slate-200">
                            ✔ {{ $feature }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">API</p>
                <h3 class="mt-3 text-2xl font-semibold text-white">REST endpoints</h3>
                    <div class="mt-6 space-y-3 text-sm text-slate-300">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/day</code> — daily Panchang based on sunrise</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/moment</code> — moment-based Panchang for exact instants</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/calendar</code> — Hindu calendar and festival data</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/muhurta</code> — muhurta and auspicious timing windows</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/electional</code> — electional timing data</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/examples</code> — example JSON responses</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/kundli</code> — generate Kundli / birth chart from birth details</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/planets</code> — planetary positions for a given instant/location</div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">GET <code>/api/chart</code> — chart rendered data / JSON for visualisation</div>
                </div>
            </div>
        </section>

        <section class="space-y-6 rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Roadmap</p>
            <h3 class="text-2xl font-semibold text-white">What comes next</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">Add multilingual support and expand festival coverage.</div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">Create dedicated accuracy, documentation, changelog, and API reference pages.</div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">Expand ecosystem support for PHP libraries, Laravel packages, REST APIs, JS SDKs, and mobile clients.</div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">Focus on developer adoption with clear examples, installation first, and GitHub visibility.</div>
            </div>
        </section>
    </div>
@endsection
