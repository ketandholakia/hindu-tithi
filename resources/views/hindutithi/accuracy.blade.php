@extends('layouts.app')

@section('title', 'Panchang Accuracy — Astronomical Precision | Hindutithi')
@section('meta_description', 'How Vittix Vedic Panchang achieves accurate tithi, nakshatra, yoga and muhurta calculations using timezone-aware astronomy, Lahiri ayanamsa and DST handling.')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 shadow-2xl shadow-slate-950/20 px-6 py-8 sm:px-10 lg:px-12 lg:py-12">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Accuracy</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white sm:text-5xl">Astronomical precision for Panchang calculations.</h1>
                <p class="mt-4 text-base leading-7 text-slate-300 sm:text-lg">
                    Vittix Vedic Panchang uses timezone-aware astronomy, consistent ayanamsa, and proven sunrise and moon algorithms to deliver reliable Hindu calendar output for modern PHP applications.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('hindutithi.home') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">Back to demo</a>
                    <a href="{{ route('api.docs') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">API docs</a>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Astronomical basis</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Ephemeris and calculation methods</h2>
                <p class="mt-4 text-sm leading-6 text-slate-300">
                    The package builds daily Panchang results from astronomical positions rather than fixed lookup tables. This ensures that tithi, nakshatra, yoga, and calendar output remain accurate across timezones and historical dates.
                </p>
                <ul class="mt-5 space-y-3 text-sm text-slate-300">
                    <li>• Ephemeris-derived solar and lunar longitudes</li>
                    <li>• Sunrise and sunset computed per timezone</li>
                    <li>• Moonrise and moonset from lunar position algorithms</li>
                </ul>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Ayanamsa</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Using a consistent sidereal reference</h2>
                <p class="mt-4 text-sm leading-6 text-slate-300">
                    The library supports the Lahiri ayanamsa system and other widely accepted sidereal references for Vedic calculations. This makes the output reliable for festival rules and muhurta windows.
                </p>
                <ul class="mt-5 space-y-3 text-sm text-slate-300">
                    <li>• Lahiri Ayanamsa support</li>
                    <li>• Accurate sidereal longitudes for tithi and nakshatra</li>
                    <li>• Consistent results for traditional calendar events</li>
                </ul>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Timing and validation</p>
            <h2 class="text-2xl font-semibold text-white">Timezone, DST, and historical accuracy</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                    <strong class="text-white">Timezone support</strong>
                    <p class="mt-2">All calculations respect local timezone offsets and daylight saving time rules for location-aware Panchang results.</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                    <strong class="text-white">Historical dates</strong>
                    <p class="mt-2">The engine can compute calendar data for past years, making it suitable for archives, festival lookup, and retrospective queries.</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                    <strong class="text-white">Moon calculations</strong>
                    <p class="mt-2">Moonrise, moonset, and lunar phase calculations are derived from astronomical positions to align with traditional Panchang rules.</p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                    <strong class="text-white">Validation</strong>
                    <p class="mt-2">The package is designed for measurable output and can be validated against established Panchang sources and festival rules.</p>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Supported range</p>
            <h2 class="text-2xl font-semibold text-white">Broad date and time coverage</h2>
            <p class="mt-4 text-sm leading-6 text-slate-300">
                Vittix Vedic Panchang is built to support modern application requirements across current and historical periods, with timezone-correct Panchang outputs and festival calculations that adapt to local rules.
            </p>
        </section>
    </div>
@endsection
