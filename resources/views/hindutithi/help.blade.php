@extends('layouts.app')

@section('title', 'Help — How to Use the Panchang Demo | Hindutithi')
@section('meta_description', 'Learn how to explore Panchang calculations for any date, time, timezone and location using the Hindutithi demo and its REST API.')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Help</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">How to use the Panchang demo</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">Explore Panchang calculations for a chosen date, time, timezone and location.</p>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <h2 class="text-2xl font-semibold text-white">How to use the form</h2>
            <div class="mt-5 space-y-3 text-sm leading-6 text-slate-300">
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><strong class="text-white">Date</strong> sets the civil date used for calculations.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><strong class="text-white">Time</strong> is used by moment-based views like <code>/moment</code> and <code>/janmarashi</code>.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><strong class="text-white">Timezone</strong> controls how sunrise, moon events, and calendar dates are interpreted.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><strong class="text-white">Latitude</strong>, <strong class="text-white">longitude</strong>, and <strong class="text-white">elevation</strong> affect solar and lunar results.</div>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <h2 class="text-2xl font-semibold text-white">Main pages</h2>
            <div class="mt-5 grid gap-3 text-sm leading-6 text-slate-300 sm:grid-cols-2">
                <a href="{{ route('hindutithi.day') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-4 transition hover:border-slate-600"><strong class="text-white">/day</strong> — sunrise-based daily Panchang.</a>
                <a href="{{ route('hindutithi.moment') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-4 transition hover:border-slate-600"><strong class="text-white">/moment</strong> — Panchang for one exact instant.</a>
                <a href="{{ route('hindutithi.calendar') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-4 transition hover:border-slate-600"><strong class="text-white">/calendar</strong> — Hindu calendar summary.</a>
                <a href="{{ route('hindutithi.muhurta') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-4 transition hover:border-slate-600"><strong class="text-white">/muhurta</strong> — daytime muhurtas derived from sunrise and sunset.</a>
                <a href="{{ route('hindutithi.electional') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-4 transition hover:border-slate-600"><strong class="text-white">/electional</strong> — electional astrology checks available in the package.</a>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <h2 class="text-2xl font-semibold text-white">API testing</h2>
            <div class="mt-5 space-y-3 text-sm leading-6 text-slate-300">
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><a href="{{ route('api.docs') }}" class="font-semibold text-amber-200 hover:text-white">Open API Docs</a> to browse the test endpoints.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">If you are signed in, open <a href="{{ route('api.keys.index') }}" class="font-semibold text-amber-200 hover:text-white">API Keys</a> to generate a personal key.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><code>/api</code> lists the available API routes.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4"><code>/api/examples</code> returns sample URLs for the current form values.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">The API expects the <code>X-API-KEY</code> header when protection is enabled.</div>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-slate-950/70 p-8 shadow-lg shadow-slate-950/20">
            <h2 class="text-2xl font-semibold text-white">Tips</h2>
            <div class="mt-5 space-y-3 text-sm leading-6 text-slate-300">
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">If a page shows unavailable data, check whether the package supports that feature in your installed version.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">Use the same date and timezone across pages when comparing outputs.</div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">For debugging, the API endpoints are easier to inspect than the HTML views.</div>
            </div>
        </section>
    </div>
@endsection
