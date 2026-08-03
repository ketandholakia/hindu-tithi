@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold uppercase tracking-widest text-sky-400 mb-1">Moment-Based</div>
        <h1 class="text-2xl font-bold text-white">Panchang at a Moment</h1>
        <p class="mt-1 text-sm text-slate-400">Exact planetary positions for the chosen date &amp; time</p>
    </div>

    @include('hindutithi.partials.birth_form')

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Moment data --}}
        <div class="rounded-2xl border border-sky-500/20 bg-sky-500/5 overflow-hidden">
            <div class="border-b border-white/10 px-5 py-4 flex items-center gap-2">
                <span class="text-sky-400">⏱</span>
                <h2 class="text-sm font-semibold text-white">At the Moment</h2>
            </div>
            <div class="p-4">
                <pre class="overflow-x-auto rounded-lg bg-slate-950/60 p-4 text-xs text-slate-300 leading-relaxed">{{ var_export($moment, true) }}</pre>
            </div>
        </div>

        {{-- Sunrise-anchored day --}}
        <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 overflow-hidden">
            <div class="border-b border-white/10 px-5 py-4 flex items-center gap-2">
                <span class="text-amber-400">☀</span>
                <h2 class="text-sm font-semibold text-white">Sunrise-Anchored Day</h2>
            </div>
            <div class="p-4">
                <pre class="overflow-x-auto rounded-lg bg-slate-950/60 p-4 text-xs text-slate-300 leading-relaxed">{{ var_export($day, true) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection
