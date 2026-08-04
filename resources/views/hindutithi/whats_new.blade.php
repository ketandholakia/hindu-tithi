@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Release</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">What's new in v2.0.0</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">Highlights of the v2.0 release: Kundli and Astrology support, new REST endpoints, expanded documentation, and accuracy improvements.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">New</strong>
                        <ul class="mt-3 space-y-2">
                            <li>• Complete Kundli engine (birth chart generation)</li>
                            <li>• Planetary position APIs</li>
                            <li>• Chart and Kundli JSON endpoints</li>
                            <li>• Improved Panchang accuracy and DST handling</li>
                        </ul>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Upgrade</strong>
                        <p class="mt-3">Run <code>composer update vittix/panchang</code> to upgrade. See the changelog for detailed migration notes.</p>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang/releases/tag/v2.0.0" target="_blank" class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-900">View GitHub release</a>
                </div>
            </div>
        </section>
    </div>
@endsection
