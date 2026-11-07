@extends('layouts.app')

@section('title', 'Choghadiya Timings | Hindutithi')
@section('meta_description', 'View the day and night Choghadiya windows for a date and location, calculated from local sunrise and sunset.')

@section('content')
<div class="space-y-6">
    <header>
        <div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Daily Muhurta</div>
        <h1 class="text-2xl font-bold text-white">Choghadiya Timings</h1>
        <p class="mt-1 text-sm text-slate-400">{{ $date->format('l, F j, Y') }} · {{ $input['tz'] }} · {{ number_format((float) $input['lat'], 4) }}, {{ number_format((float) $input['lon'], 4) }}</p>
    </header>

    @include('hindutithi.partials.birth_form')

    @if(count($dayWindows) && count($nightWindows))
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach([['Day', 'Sunrise to sunset', $dayWindows, 'amber'], ['Night', 'Sunset to next sunrise', $nightWindows, 'indigo']] as [$period, $description, $windows, $tone])
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900/70">
                    <div class="border-b border-white/10 px-5 py-4">
                        <h2 class="font-semibold text-white">{{ $period }} Choghadiya</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ $description }}</p>
                    </div>
                    <ol class="divide-y divide-white/5">
                        @foreach($windows as $index => $window)
                            <li class="flex items-center justify-between gap-4 px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $tone }}-400/10 text-xs font-semibold text-{{ $tone }}-300">{{ $index + 1 }}</span>
                                    <span class="font-medium text-white">{{ $window->name }}</span>
                                </div>
                                <time class="text-sm tabular-nums text-slate-300">{{ $window->start->format('H:i') }} – {{ $window->end->format('H:i') }}</time>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endforeach
        </div>
        <p class="text-xs text-slate-500">Timings are calculated from the selected location’s local sunrise and sunset.</p>
    @else
        <div class="rounded-2xl border border-amber-400/20 bg-amber-400/5 p-5 text-sm text-amber-100">Sunrise or sunset could not be determined for this date and location. Try another date or location.</div>
    @endif
</div>
@endsection
