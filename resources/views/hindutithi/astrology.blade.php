@extends('layouts.app')

@section('title', 'Astrology & Kundli — Birth Charts, Dasha, Varga, Shadbala | Hindutithi')
@section('meta_description', 'Explore Vedic astrology demos: Kundli birth charts, Vimshottari Dasha timelines, D9 divisional charts, planetary strength (Shadbala) and classical yoga detection.')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Astrology</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">Astrology & Kundli</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">Vittix Vedic Panchang now includes a full astrology demo surface: birth chart generation, Vimshottari Dasha, divisional Varga charts, planetary strength, and yoga detection.</p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Kundli Demo</strong>
                        <p class="mt-2">Enter birth data and view a live birth chart, house placements, planetary positions and ascendant information.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-5 text-sm text-slate-300">
                        <strong class="text-white">Dasha, Varga, Shadbala</strong>
                        <p class="mt-2">Explore Vimshottari Dasha periods, divisional charts like D9, planetary strength, and classical yogas.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('hindutithi.kundli') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">Open Kundli Demo</a>
                    <a href="{{ route('api.docs') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-white">API docs</a>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-white">Try these astrology demos</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5 text-sm text-slate-300">
                    <p class="font-semibold text-white">Kundli / Birth chart</p>
                    <p class="mt-2">Interactive birth chart generator with houses, ascendant, planetary placements, and divisional output support.</p>
                    <p class="mt-4"><a href="{{ route('hindutithi.kundli') }}" class="text-amber-200 hover:text-white">Open the Kundli demo</a></p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5 text-sm text-slate-300">
                    <p class="font-semibold text-white">Vimshottari Dasha</p>
                    <p class="mt-2">View major and sub-period timelines derived from the same birth data.</p>
                    <p class="mt-4"><a href="{{ route('hindutithi.vimshottari') }}" class="text-amber-200 hover:text-white">Open Dasha timeline</a></p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5 text-sm text-slate-300">
                    <p class="font-semibold text-white">Varga charts</p>
                    <p class="mt-2">Explore divisional charts like D9 with the same input set.</p>
                    <p class="mt-4"><a href="{{ route('hindutithi.varga', ['varga' => 'D9']) }}" class="text-amber-200 hover:text-white">Open Varga chart</a></p>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5 text-sm text-slate-300">
                    <p class="font-semibold text-white">Shadbala & Yogas</p>
                    <p class="mt-2">Review planetary strength and detected classical yogas from the same birth input.</p>
                    <p class="mt-4"><a href="{{ route('hindutithi.shadbala') }}" class="text-amber-200 hover:text-white">Open Shadbala</a> · <a href="{{ route('hindutithi.yogas') }}" class="text-amber-200 hover:text-white">Open Yogas</a></p>
                </div>
            </div>
        </section>
    </div>
@endsection
