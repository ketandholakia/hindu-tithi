@extends('layouts.app')

@section('title', "What's New in v2.4.0 — Release Notes | Hindutithi")
@section('meta_description', "Vittix Vedic Panchang v2.4.0: Kundli engine, planetary position APIs, Vimshottari Dasha, Shadbala, yogas and improved festival coverage.")

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Release</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">What's new in v2.4.0</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">This release expands Vittix Vedic Panchang into a full astrology playground with Kundli generation, chart endpoints, and new demo pages.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Highlights</strong>
                        <ul class="mt-3 space-y-2">
                            <li>• Complete Kundli engine: birth chart, houses, ascendant</li>
                            <li>• Planetary position APIs and chart data endpoints</li>
                            <li>• Kundli and divisional chart demo pages</li>
                            <li>• Vimshottari Dasha, Shadbala, and yogas output</li>
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Improvements</strong>
                        <ul class="mt-3 space-y-2">
                            <li>• Better Panchang accuracy and timezone/DST handling</li>
                            <li>• Expanded API docs and quick-start examples</li>
                            <li>• More accessible astrology demo UX</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 rounded-3xl border border-slate-800 bg-slate-950 p-6 text-sm text-slate-300">
                    <p>For the full release note text, see <code>RELEASE_NOTES_V2.md</code> in the repository or open the GitHub release.</p>
                    <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/releases/tag/v2.4.0" target="_blank" class="mt-4 inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-900">View GitHub release</a>
                </div>
            </div>
        </section>
    </div>
@endsection
