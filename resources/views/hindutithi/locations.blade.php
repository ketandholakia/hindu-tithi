@extends('layouts.app')

@section('title', 'Saved Locations | Hindutithi')
@section('meta_description', 'Search for places and save locations for quick Panchang calculations.')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Personal settings</div><h1 class="text-2xl font-bold text-white">Saved locations</h1><p class="mt-1 text-sm text-slate-400">Save up to 20 places for quick reuse in Panchang and astrology calculations.</p></header>
    @if(session('status'))<div role="status" class="rounded-xl border border-emerald-400/20 bg-emerald-400/5 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>@endif
    @if($errors->any())<div role="alert" class="rounded-xl border border-red-400/20 bg-red-400/5 px-4 py-3 text-sm text-red-200">{{ $errors->first() }}</div>@endif
    <section class="space-y-5 rounded-2xl border border-white/10 bg-slate-900/70 p-5">
        <h2 class="font-semibold text-white">Find and save a place</h2>
        @include('hindutithi.partials.place_search')
        <form method="POST" action="{{ route('locations.store') }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <label class="space-y-1 text-xs text-slate-400 sm:col-span-2">Place name<input required name="name" maxlength="120" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white" placeholder="Choose a search result or enter a name"></label>
            <label class="space-y-1 text-xs text-slate-400">Latitude<input required name="latitude" type="number" min="-90" max="90" step="any" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Longitude<input required name="longitude" type="number" min="-180" max="180" step="any" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Timezone<input required name="timezone" value="Asia/Kolkata" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white" placeholder="Area/City"></label>
            <label class="space-y-1 text-xs text-slate-400">Elevation (meters)<input name="elevation_meters" type="number" step="any" value="0" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <div class="sm:col-span-2"><button type="submit" class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-slate-900 hover:bg-amber-400">Save location</button></div>
        </form>
    </section>
    <section class="space-y-3">
        <h2 class="font-semibold text-white">Your locations <span class="text-sm font-normal text-slate-500">{{ $locations->count() }}/20</span></h2>
        @forelse($locations as $location)
            <article class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-white/10 bg-slate-900/60 p-4">
                <div><h3 class="font-medium text-white">{{ $location->name }}</h3><p class="mt-1 text-xs text-slate-400">{{ number_format($location->latitude, 4) }}, {{ number_format($location->longitude, 4) }} · {{ $location->timezone }}</p></div>
                <div class="flex gap-2"><a class="rounded-full border border-white/10 px-3 py-1.5 text-xs text-slate-300 hover:border-amber-400/40 hover:text-amber-200" href="{{ route('hindutithi.day', ['lat' => $location->latitude, 'lon' => $location->longitude, 'elev' => $location->elevation_meters, 'tz' => $location->timezone]) }}">Use location</a>
                    <form method="POST" action="{{ route('locations.destroy', $location) }}" onsubmit="return confirm('Remove this saved location?')">@csrf @method('DELETE')<button class="rounded-full border border-red-400/20 px-3 py-1.5 text-xs text-red-200 hover:bg-red-400/10">Remove</button></form></div>
            </article>
        @empty<p class="rounded-xl border border-dashed border-white/10 p-5 text-sm text-slate-400">You have no saved locations yet.</p>@endforelse
    </section>
</div>
@endsection
