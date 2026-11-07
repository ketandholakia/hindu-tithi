@extends('layouts.app')

@section('title', 'Panchang Reminder Preferences | Hindutithi')
@section('meta_description', 'Choose which tithi and festival reminders you receive by email and when they arrive.')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <header><div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Personal settings</div><h1 class="text-2xl font-bold text-white">Panchang reminders</h1><p class="mt-1 text-sm text-slate-400">Get one email on days that match your selected tithi or festival reminders.</p></header>
    @if(session('status'))<div role="status" class="rounded-xl border border-emerald-400/20 bg-emerald-400/5 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>@endif
    @if($errors->any())<div role="alert" class="rounded-xl border border-red-400/20 bg-red-400/5 px-4 py-3 text-sm text-red-200">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('reminders.update') }}" class="space-y-5 rounded-2xl border border-white/10 bg-slate-900/70 p-5">
        @csrf @method('PUT')
        <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-800/50 p-4"><input type="checkbox" name="enabled" value="1" @checked(old('enabled', $settings?->enabled ?? false)) class="mt-1 rounded border-white/20 bg-slate-800 text-amber-400 focus:ring-amber-400"><span><span class="block font-medium text-white">Enable email reminders</span><span class="mt-1 block text-xs text-slate-400">You can turn these off any time.</span></span></label>
        <fieldset class="space-y-3"><legend class="mb-2 text-sm font-semibold text-white">Send reminders for</legend>
            @foreach(['ekadashi' => 'Ekadashi', 'purnima' => 'Purnima', 'amavasya' => 'Amavasya', 'festivals' => 'Festivals'] as $key => $label)
                <label class="flex items-center gap-3 text-sm text-slate-300"><input type="checkbox" name="notify_{{ $key }}" value="1" @checked(old('notify_'.$key, $settings?->{'notify_'.$key} ?? true)) class="rounded border-white/20 bg-slate-800 text-amber-400 focus:ring-amber-400">{{ $label }}</label>
            @endforeach
        </fieldset>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="space-y-1 text-xs text-slate-400">Delivery time<input required name="send_time" type="time" value="{{ old('send_time', $settings?->send_time ?? '08:00') }}" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Timezone<input required name="timezone" value="{{ old('timezone', $settings?->timezone ?? 'Asia/Kolkata') }}" placeholder="Area/City" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Latitude<input required name="latitude" type="number" step="any" min="-90" max="90" value="{{ old('latitude', $settings?->latitude ?? 28.6139) }}" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400">Longitude<input required name="longitude" type="number" step="any" min="-180" max="180" value="{{ old('longitude', $settings?->longitude ?? 77.2090) }}" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
            <label class="space-y-1 text-xs text-slate-400 sm:col-span-2">Elevation (meters)<input name="elevation_meters" type="number" step="any" value="{{ old('elevation_meters', $settings?->elevation_meters ?? 0) }}" class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white"></label>
        </div>
        <p class="text-xs text-slate-500">Scheduled email delivery requires the site mail settings and Laravel scheduler to be configured.</p>
        <button type="submit" class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-slate-900 hover:bg-amber-400">Save preferences</button>
    </form>
</div>
@endsection
