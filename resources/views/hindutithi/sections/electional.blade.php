@extends('layouts.app')

@section('title', 'Electional Astrology Checks | Hindutithi')
@section('meta_description', 'Evaluate auspicious yogas and traditional electional considerations for a date, time, and place.')

@section('content')
    <div class="space-y-6">
        <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Auspicious timing</div><h1 class="text-2xl font-bold text-white">Electional Checks</h1><p class="mt-1 text-sm text-slate-400">Traditional Panchang checks for {{ $result['input']['weekday'] }}, {{ $result['input']['date'] }} at {{ $result['input']['time'] }}.</p></header>
        @include('hindutithi.partials.birth_form')
        <div class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-2xl border {{ $result['summary']['auspicious'] ? 'border-emerald-400/30 bg-emerald-400/5' : 'border-white/10 bg-slate-900/60' }} p-5"><h2 class="font-semibold text-white">Auspicious combinations</h2><p class="mt-1 text-sm {{ $result['summary']['auspicious'] ? 'text-emerald-200' : 'text-slate-400' }}">{{ $result['summary']['auspicious'] ? 'At least one checked Siddhi Yoga is present.' : 'No checked Siddhi Yoga was found for this moment.' }}</p></article>
            <article class="rounded-2xl border {{ $result['summary']['inauspicious'] ? 'border-amber-400/30 bg-amber-400/5' : 'border-white/10 bg-slate-900/60' }} p-5"><h2 class="font-semibold text-white">Caution checks</h2><p class="mt-1 text-sm {{ $result['summary']['inauspicious'] ? 'text-amber-200' : 'text-slate-400' }}">{{ $result['summary']['inauspicious'] ? 'Bhadra or Panchaka Dosha is present.' : 'No Bhadra or Panchaka Dosha was detected.' }}</p></article>
        </div>
        <section class="rounded-2xl border border-white/10 bg-slate-900/60 p-5"><h2 class="font-semibold text-white">Panchang at sunrise</h2><dl class="mt-4 grid gap-3 sm:grid-cols-3">@foreach($result['panchangLimbs'] as $name => $value)<div><dt class="text-xs uppercase tracking-wide text-slate-500">{{ ucfirst($name) }}</dt><dd class="mt-1 text-sm text-slate-200">{{ $value }}</dd></div>@endforeach</dl></section>
        <section class="overflow-hidden rounded-2xl border border-white/10"><h2 class="bg-slate-900 px-5 py-4 font-semibold text-white">Checks</h2><dl class="divide-y divide-white/5 bg-slate-900/50">@foreach($result['electional'] as $name => $value)<div class="flex flex-wrap justify-between gap-2 px-5 py-3"><dt class="text-sm text-slate-300">{{ \Illuminate\Support\Str::headline($name) }}</dt><dd class="text-sm font-medium {{ is_bool($value) ? ($value ? 'text-amber-200' : 'text-slate-500') : 'text-slate-200' }}">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : (is_scalar($value) ? $value : json_encode($value)) }}</dd></div>@endforeach</dl></section>
    </div>
@endsection
