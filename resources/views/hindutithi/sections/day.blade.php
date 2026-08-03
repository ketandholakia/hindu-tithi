@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Page header --}}
    <div>
        <div class="text-xs font-semibold uppercase tracking-widest text-amber-400 mb-1">Daily View</div>
        <h1 class="text-2xl font-bold text-white">Daily Panchang</h1>
        <p class="mt-1 text-sm text-slate-400">
            {{ \Illuminate\Support\Carbon::parse($day['date'])->format('l, F j, Y') }}
            <span class="ml-2 text-slate-600">·</span>
            <span class="ml-2 text-slate-500">{{ $day['timezone'] }}</span>
        </p>
    </div>

    @include('hindutithi.partials.birth_form')

    {{-- Solar Events --}}
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach([
            ['☀', 'Sunrise',    $day['solarEvents']['sunrise'],   'text-amber-400',  'bg-amber-400/10',  'border-amber-400/20'],
            ['🌤', 'Solar Noon', $day['solarEvents']['solarNoon'], 'text-yellow-400', 'bg-yellow-400/10', 'border-yellow-400/20'],
            ['🌇', 'Sunset',    $day['solarEvents']['sunset'],    'text-orange-400', 'bg-orange-400/10', 'border-orange-400/20'],
        ] as [$icon, $label, $value, $textColor, $bgColor, $borderColor])
            <div class="flex items-center gap-4 rounded-2xl border {{ $borderColor }} {{ $bgColor }} px-5 py-4">
                <span class="text-2xl">{{ $icon }}</span>
                <div>
                    <div class="text-xs font-medium text-slate-400">{{ $label }}</div>
                    <div class="text-lg font-semibold {{ $textColor }}">
                        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('H:i:s') : '—' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Panchanga elements grid --}}
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">
        <div class="border-b border-white/10 px-5 py-4">
            <h2 class="text-sm font-semibold text-white">Panchanga Elements at Sunrise</h2>
        </div>
        <div class="grid divide-y divide-white/5 sm:grid-cols-2 sm:divide-y-0 sm:divide-x">
            @foreach([
                ['Vara (Day)',         $day['vara'],                'text-violet-400'],
                ['Tithi',             $day['tithiAtSunrise'],       'text-amber-400'],
                ['Nakshatra',         $day['nakshatraAtSunrise'],   'text-sky-400'],
                ['Pada',              $day['padaAtSunrise'],        'text-emerald-400'],
                ['Yoga',              $day['yogaAtSunrise'],        'text-pink-400'],
                ['Karana',            $day['karanaAtSunrise'],      'text-orange-400'],
            ] as [$label, $value, $valueColor])
                <div class="flex items-center justify-between px-5 py-3.5 sm:col-span-1">
                    <span class="text-sm text-slate-400">{{ $label }}</span>
                    <span class="text-sm font-semibold {{ $valueColor }}">{{ $value ?? '—' }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- End times --}}
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">
        <div class="border-b border-white/10 px-5 py-4">
            <h2 class="text-sm font-semibold text-white">Element End Times</h2>
        </div>
        <div class="divide-y divide-white/5">
            @foreach([
                ['Tithi ends',          $day['tithiEndsAt']],
                ['Nakshatra ends',      $day['nakshatraEndsAt']],
                ['Yoga ends',           $day['yogaEndsAt']],
                ['Karana ends',         $day['karanaEndsAt']],
                ['Second Tithi',        $day['tithiSecond']],
                ['Second Tithi ends',   $day['tithiSecondEndsAt']],
            ] as [$label, $value])
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-slate-400">{{ $label }}</span>
                    <span class="text-sm font-medium text-white">
                        @if($value)
                            @if(str_contains($value, 'T') || str_contains($value, ':'))
                                {{ \Illuminate\Support\Carbon::parse($value)->format('Y-m-d H:i') }}
                            @else
                                {{ $value }}
                            @endif
                        @else
                            <span class="text-slate-600">—</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Calendar --}}
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">
        <div class="border-b border-white/10 px-5 py-4">
            <h2 class="text-sm font-semibold text-white">Lunar Calendar</h2>
        </div>
        <div class="divide-y divide-white/5">
            @foreach([
                ['Lunar Month',    $day['lunarMonth']],
                ['Paksha',         $day['paksha']],
                ['Adhika Masa',    $day['isAdhikaMasa'] ? 'Yes' : 'No'],
                ['Lunar Location', $day['location']['latitude'] . ', ' . $day['location']['longitude']],
            ] as [$label, $value])
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-slate-400">{{ $label }}</span>
                    <span class="text-sm font-medium text-white">{{ $value ?? '—' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
