@extends('layouts.app')
@section('title', 'Sankranti Calendar | Hindutithi')
@section('meta_description', 'Find exact local times when the Sun enters each sidereal zodiac sign.')
@section('content')
<div class="space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Advanced Panchang</div><h1 class="text-2xl font-bold text-white">Sankranti Calendar</h1><p class="mt-1 text-sm text-slate-400">Exact solar sign-entry moments for {{ $year }} in your selected timezone.</p></header>
    @include('hindutithi.partials.birth_form')
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-white/10 bg-slate-900/70 p-4"><label class="space-y-1 text-xs text-slate-400">Year<input name="year" type="number" min="1800" max="2200" value="{{ $year }}" class="block rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label><input type="hidden" name="tz" value="{{ $input['tz'] }}"><input type="hidden" name="lang" value="{{ $input['lang'] }}"><button class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-slate-900">Show year</button></form>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@forelse($result['results'] ?? [] as $entry)<article class="rounded-xl border border-white/10 bg-slate-900/60 p-4"><h2 class="font-semibold text-white">{{ $entry['rashi']['name'] }}</h2><time class="mt-2 block text-sm text-amber-200">{{ \Illuminate\Support\Carbon::parse($entry['local'])->format('l, d M Y · H:i:s T') }}</time><p class="mt-1 text-xs text-slate-500">{{ $entry['local'] }}</p></article>@empty<p class="text-sm text-slate-400">No Sankranti times were returned for this year.</p>@endforelse</div>
</div>
@endsection
