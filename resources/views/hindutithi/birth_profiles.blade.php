@extends('layouts.app')

@section('title', 'Saved Birth Profiles | Hindutithi')
@section('meta_description', 'Manage private birth profiles and open Kundli charts from your saved details.')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Personal astrology</div><h1 class="text-2xl font-bold text-white">Birth profiles</h1><p class="mt-1 text-sm text-slate-400">Keep up to 20 private birth records with precise time, timezone, and place details.</p></header>
    @if(session('status'))<div role="status" class="rounded-xl border border-emerald-400/20 bg-emerald-400/5 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>@endif
    @if($errors->any())<div role="alert" class="rounded-xl border border-red-400/20 bg-red-400/5 px-4 py-3 text-sm text-red-200">{{ $errors->first() }}</div>@endif
    <section class="space-y-5 rounded-2xl border border-white/10 bg-slate-900/70 p-5">
        <h2 class="font-semibold text-white">Add a profile</h2>
        @include('hindutithi.partials.place_search')
        <form method="POST" action="{{ route('birth-profiles.store') }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <label class="space-y-1 text-xs text-slate-400 sm:col-span-2">Profile name<input required name="name" maxlength="120" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white" placeholder="e.g. My chart"></label>
            <label class="space-y-1 text-xs text-slate-400">Birth date<input required name="birth_date" type="date" max="{{ now()->toDateString() }}" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Birth time<input required name="birth_time" type="time" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Timezone<input required name="timezone" value="Asia/Kolkata" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white" placeholder="Area/City"></label>
            <label class="space-y-1 text-xs text-slate-400">Elevation (meters)<input name="elevation_meters" type="number" step="any" value="0" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Latitude<input required name="latitude" type="number" min="-90" max="90" step="any" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Longitude<input required name="longitude" type="number" min="-180" max="180" step="any" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <div class="sm:col-span-2"><button type="submit" class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-slate-900 hover:bg-amber-400">Save birth profile</button></div>
        </form>
    </section>
    <section class="space-y-3">
        <h2 class="font-semibold text-white">Saved profiles <span class="text-sm font-normal text-slate-500">{{ $profiles->count() }}/20</span></h2>
        @forelse($profiles as $profile)
            <article class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-white/10 bg-slate-900/60 p-4">
                <div><h3 class="font-medium text-white">{{ $profile->name }}</h3><p class="mt-1 text-xs text-slate-400">{{ $profile->birth_date->format('d M Y') }} · {{ substr($profile->birth_time, 0, 5) }} {{ $profile->timezone }} · {{ number_format($profile->latitude, 4) }}, {{ number_format($profile->longitude, 4) }}</p></div>
                <div class="flex gap-2"><a class="rounded-full border border-amber-400/30 px-3 py-1.5 text-xs text-amber-200 hover:bg-amber-400/10" href="{{ route('hindutithi.kundli', ['date' => $profile->birth_date->format('Y-m-d'), 'time' => substr($profile->birth_time, 0, 5), 'tz' => $profile->timezone, 'lat' => $profile->latitude, 'lon' => $profile->longitude, 'elev' => $profile->elevation_meters]) }}">Open Kundli</a>
                    <form method="POST" action="{{ route('birth-profiles.destroy', $profile) }}" onsubmit="return confirm('Remove this birth profile?')">@csrf @method('DELETE')<button class="rounded-full border border-red-400/20 px-3 py-1.5 text-xs text-red-200 hover:bg-red-400/10">Remove</button></form></div>
            </article>
        @empty<p class="rounded-xl border border-dashed border-white/10 p-5 text-sm text-slate-400">You have no saved birth profiles yet.</p>@endforelse
    </section>
</div>
@endsection
