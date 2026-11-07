@extends('layouts.app')
@section('title', 'Moon Sign Calculator | Hindutithi')
@section('meta_description', 'Calculate the Moon’s sidereal sign and degree at a selected date and time.')
@section('content')
<div class="space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Advanced Astrology</div><h1 class="text-2xl font-bold text-white">Moon Sign at a Moment</h1><p class="mt-1 text-sm text-slate-400">Calculate the geocentric sidereal Moon sign for your selected time.</p></header>
    @include('hindutithi.partials.birth_form')
    <section class="rounded-2xl border border-amber-400/20 bg-amber-400/5 p-6"><p class="text-xs uppercase tracking-widest text-amber-300">Moon sign</p><h2 class="mt-2 text-3xl font-bold text-white">{{ $result['rashi']['name'] ?? '—' }}</h2><p class="mt-3 text-sm text-slate-300">{{ $result['degreeInSign'] ?? '—' }}° into the sign</p><p class="mt-1 text-xs text-slate-500">Sidereal longitude: {{ $result['moonSiderealLongitude'] ?? '—' }}° · {{ $result['instant'] ?? '' }}</p></section>
</div>
@endsection
