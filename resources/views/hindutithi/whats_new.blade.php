@extends('layouts.app')

@section('title', "What's New in v2.6.0 — Release Notes | Hindutithi")
@section('meta_description', "Vittix Vedic Panchang v2.6.0 adds localized Panchang API responses in ten languages, bounded LRU caching, accuracy fixes and improved lunar event calculations.")

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Release</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">What's new in v2.6.0</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">The latest engine release brings localized Panchang responses, more reliable astronomical calculations, and bounded caching for long-running use.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">New in 2.6.0</strong>
                        <ul class="mt-3 space-y-2">
                            <li>• Localized Panchang names and daily responses in ten bundled languages</li>
                            <li>• Custom translator support through PanchangService</li>
                            <li>• Shared bounded LRU caching for astronomy and service results</li>
                            <li>• Existing Kundli, divisional charts, Dasha, Shadbala and yogas remain available</li>
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Recent engine improvements</strong>
                        <ul class="mt-3 space-y-2">
                            <li>• More accurate planetary positions and transition times</li>
                            <li>• Improved moonrise and moonset handling, including high latitudes</li>
                            <li>• Bounded memory use for repeated and batch calculations</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 rounded-3xl border border-slate-800 bg-slate-950 p-6 text-sm text-slate-300">
                    <p>See the full package release notes and migration details on GitHub.</p>
                    <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/releases/tag/v2.6.0" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-900">View GitHub release</a>
                </div>
            </div>
        </section>
    </div>
@endsection
