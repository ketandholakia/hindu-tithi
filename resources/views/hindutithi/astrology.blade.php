@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Astrology</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">Astrology & Kundli</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">Vittix Vedic Panchang v2 adds a full astrology module: birth charts (Kundli), planet positions, houses, ascendant (Lagna), Rashi and Nakshatra, and more.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Kundli</strong>
                        <p class="mt-2">Generate birth charts from date, time and location with timezone-aware calculations.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Planets & Houses</strong>
                        <p class="mt-2">Compute planetary longitudes, ascendant, house positions and basic yogas.</p>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('hindutithi.kundli') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">Open Kundli Demo</a>
                    <a href="{{ route('api.docs') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-white">API docs</a>
                </div>
            </div>
        </section>
    </div>
@endsection
