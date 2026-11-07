@extends('layouts.app')
@section('title', 'Astronomy Report | Hindutithi')
@section('meta_description', 'View sunrise, sunset, moonrise, moonset, sidereal coordinates, and solar month for a date and location.')
@section('content')
<div class="space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Advanced Panchang</div><h1 class="text-2xl font-bold text-white">Astronomy Report</h1><p class="mt-1 text-sm text-slate-400">Solar and lunar events with sidereal values for the selected day and place.</p></header>
    @include('hindutithi.partials.birth_form')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([['Sunrise', $result['solar']['sunrise'] ?? null], ['Solar noon', $result['solar']['solarNoon'] ?? null], ['Sunset', $result['solar']['sunset'] ?? null], ['Moonrise', $result['lunar']['moonrise'] ?? null], ['Lunar transit', $result['lunar']['transit'] ?? null], ['Moonset', $result['lunar']['moonset'] ?? null]] as [$label, $value])
            <article class="rounded-xl border border-white/10 bg-slate-900/60 p-4"><p class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</p><p class="mt-2 text-lg font-semibold text-white">{{ $value ? \Illuminate\Support\Carbon::parse($value)->format('H:i:s') : 'Unavailable' }}</p>@if($value)<p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($value)->format('d M Y') }} · {{ $input['tz'] }}</p>@endif</article>
        @endforeach
    </div>
    <section class="grid gap-4 sm:grid-cols-2"><article class="rounded-xl border border-white/10 bg-slate-900/60 p-4"><h2 class="font-medium text-white">Solar data</h2><dl class="mt-3 space-y-2 text-sm"><div class="flex justify-between"><dt class="text-slate-400">Day length</dt><dd class="text-slate-200">{{ isset($result['solar']['dayLengthSeconds']) ? gmdate('H:i:s', $result['solar']['dayLengthSeconds']) : '—' }}</dd></div><div class="flex justify-between"><dt class="text-slate-400">Sun sidereal longitude</dt><dd class="text-slate-200">{{ $result['solar']['siderealLongitude'] ?? '—' }}°</dd></div><div class="flex justify-between"><dt class="text-slate-400">Solar month</dt><dd class="text-slate-200">{{ $result['solarMonth']['name'] ?? '—' }}</dd></div></dl></article><article class="rounded-xl border border-white/10 bg-slate-900/60 p-4"><h2 class="font-medium text-white">Lunar and sidereal data</h2><dl class="mt-3 space-y-2 text-sm"><div class="flex justify-between"><dt class="text-slate-400">Moon sidereal longitude</dt><dd class="text-slate-200">{{ $result['lunar']['siderealLongitude'] ?? '—' }}°</dd></div><div class="flex justify-between"><dt class="text-slate-400">Ayanamsha</dt><dd class="text-slate-200">{{ $result['ayanamsha'] ?? '—' }}°</dd></div><div class="flex justify-between"><dt class="text-slate-400">Local sidereal time</dt><dd class="text-slate-200">{{ $result['siderealTime'] ?? '—' }} h</dd></div></dl></article></section>
</div>
@endsection
