@extends('layouts.app')

@section('title', 'Hindu Calendar Summary — Lunar Month, Moon Phases | Hindutithi')
@section('meta_description', 'Hindu calendar summary with lunar month, moon phases and Vikram Samvat years for the selected date and timezone.')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold uppercase tracking-widest text-emerald-400 mb-1">Hindu Calendar</div>
        <h1 class="text-2xl font-bold text-white">Calendar Summary</h1>
        <p class="mt-1 text-sm text-slate-400">Lunar month, moon phases, and Samvat years</p>
    </div>

    @include('hindutithi.partials.birth_form')

    <div class="grid gap-4 sm:grid-cols-2">
        {{-- Lunar Month --}}
        <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">
            <div class="border-b border-white/10 px-5 py-4">
                <h2 class="text-sm font-semibold text-white">Lunar Month</h2>
            </div>
            <div class="divide-y divide-white/5">
                @foreach([
                    ['Amanta Month',    $calendar['amantaMonth'],    'text-emerald-400'],
                    ['Purnimanta Month',$calendar['purnimantaMonth'],'text-emerald-300'],
                    ['Adhika Masa',     $calendar['isAdhikaMasa'] ? 'Yes ✓' : 'No', $calendar['isAdhikaMasa'] ? 'text-amber-400' : 'text-slate-400'],
                    ['Vikram Samvat',   $calendar['vikramSamvat'],   'text-violet-400'],
                    ['Shaka Samvat',    $calendar['shakaSamvat'],    'text-sky-400'],
                ] as [$label, $value, $color])
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <span class="text-sm text-slate-400">{{ $label }}</span>
                        <span class="text-sm font-semibold {{ $color }}">{{ $value ?? '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Moon Phases --}}
        <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">
            <div class="border-b border-white/10 px-5 py-4">
                <h2 class="text-sm font-semibold text-white">Moon Phases</h2>
            </div>
            <div class="divide-y divide-white/5">
                @foreach([
                    ['🌑', 'Previous New Moon', $calendar['previousNewMoon']],
                    ['🌑', 'Next New Moon',     $calendar['nextNewMoon']],
                    ['🌕', 'Next Full Moon',    $calendar['nextFullMoon']],
                ] as [$icon, $label, $value])
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <span>{{ $icon }}</span>
                            <span class="text-sm text-slate-400">{{ $label }}</span>
                        </div>
                        <span class="text-sm font-medium text-white">
                            {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('d M Y H:i') : '—' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
