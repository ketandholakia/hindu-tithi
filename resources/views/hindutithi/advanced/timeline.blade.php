@extends('layouts.app')
@section('title', 'Panchang Timeline | Hindutithi')
@section('meta_description', 'Explore tithi, nakshatra, yoga, and karana transition times across a date range.')
@section('content')
<div class="space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Advanced Panchang</div><h1 class="text-2xl font-bold text-white">Panchang Timeline</h1><p class="mt-1 text-sm text-slate-400">See when each Panchang limb begins and ends. Choose a range of up to seven days.</p></header>
    @include('hindutithi.partials.birth_form')
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-white/10 bg-slate-900/70 p-4">
        <label class="space-y-1 text-xs text-slate-400">From<input name="date" type="date" value="{{ $input['date'] }}" class="block rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
        <label class="space-y-1 text-xs text-slate-400">Until<input name="end_date" type="date" value="{{ $input['end_date'] }}" class="block rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
        <input type="hidden" name="tz" value="{{ $input['tz'] }}"><input type="hidden" name="lat" value="{{ $input['lat'] }}"><input type="hidden" name="lon" value="{{ $input['lon'] }}"><input type="hidden" name="elev" value="{{ $input['elev'] }}"><input type="hidden" name="lang" value="{{ $input['lang'] }}">
        <button class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-slate-900">Show timeline</button>
    </form>
    @if(isset($result['error']))<p role="alert" class="rounded-xl border border-red-400/20 bg-red-400/5 p-4 text-sm text-red-200">{{ $result['error'] }}</p>@endif
    @if($windows)
        <div class="overflow-x-auto rounded-2xl border border-white/10"><table class="w-full text-left text-sm"><thead class="bg-slate-900 text-xs uppercase tracking-wide text-slate-400"><tr><th class="px-4 py-3">Limb</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Starts</th><th class="px-4 py-3">Ends</th></tr></thead><tbody class="divide-y divide-white/5 bg-slate-900/50">@foreach($windows as $window)<tr><td class="px-4 py-3 text-amber-300">{{ $window['field'] }}</td><td class="px-4 py-3 text-white">{{ $window['name'] }}</td><td class="px-4 py-3 text-slate-300">{{ \Illuminate\Support\Carbon::parse($window['start'])->format('D, d M H:i') }}</td><td class="px-4 py-3 text-slate-300">{{ \Illuminate\Support\Carbon::parse($window['end'])->format('D, d M H:i') }}</td></tr>@endforeach</tbody></table></div>
    @elseif(!isset($result['error']))<p class="rounded-xl border border-white/10 p-5 text-sm text-slate-400">No transitions were returned for this range.</p>@endif
</div>
@endsection
