@extends('layouts.app')

@section('title', 'Upcoming Hindu Festivals — Next 120 Days | Hindutithi')
@section('meta_description', 'Upcoming Hindu festivals for the next 120 days — Diwali, Holi, Ekadashi, Purnima and more — computed from your location.')

@section('content')
@php
    // ── Emoji map for well-known festivals ──────────────────────────────────
    $emojiMap = [
        'diwali'          => '🪔', 'deepawali'       => '🪔',
        'holi'            => '🎨', 'dhuleti'         => '🎨',
        'navratri'        => '🙏', 'dussehra'        => '⚔️',
        'vijayadashami'   => '⚔️',
        'ganesh'          => '🐘', 'ganesha'         => '🐘',
        'janmashtami'     => '🦚', 'krishna'         => '🦚',
        'ram navami'      => '🏹', 'ram'             => '🏹',
        'shivaratri'      => '🔱', 'shivratri'       => '🔱',
        'raksha'          => '🪡', 'rakshabandhan'   => '🪡',
        'ekadashi'        => '🌑', 'purnima'         => '🌕',
        'amavasya'        => '🌑', 'chaturthi'       => '🌙',
        'sankranti'       => '🌞', 'makar'           => '🌞',
        'onam'            => '🌸', 'pongal'          => '🍚',
        'ugadi'           => '🌿', 'gudi'            => '🎋',
        'baisakhi'        => '🌾', 'vaisakhi'        => '🌾',
        'durga'           => '🌺', 'kali'            => '🌺',
        'saraswati'       => '📚', 'laxmi'           => '💰',
        'hanuman'         => '🚩', 'navaratri'       => '🙏',
        'shashti'         => '🌙', 'saptami'         => '🌙',
        'ashtami'         => '🌙', 'navami'          => '🌙',
        'dwadashi'        => '🌙', 'trayodashi'      => '🌙',
        'chaturdashi'     => '🌙', 'pradosh'         => '🔱',
        'govardhan'       => '⛰️', 'bhai dooj'       => '❤️',
        'chhath'          => '☀️', 'mahalaya'        => '🙏',
        'pitru'           => '🙏', 'shraddh'         => '🙏',
        'mahashivratri'   => '🔱',
    ];

    $getFestivalEmoji = function(array $names) use ($emojiMap): string {
        $joined = strtolower(implode(' ', $names));
        foreach ($emojiMap as $keyword => $emoji) {
            if (str_contains($joined, $keyword)) return $emoji;
        }
        return '🪷';
    };

    // Days until helper
    $today    = new \DateTimeImmutable('today', new \DateTimeZone($input['tz'] ?? 'Asia/Kolkata'));
    $thisYear = (int) $today->format('Y');

    // Group list by month
    $byMonth = [];
    foreach ($list as $item) {
        $monthKey = $item['date']->format('Y-m');
        $byMonth[$monthKey][] = $item;
    }

    // Next upcoming (first item)
    $nextFestival = $list[0] ?? null;
@endphp

{{-- ── Hero strip ─────────────────────────────────────────────────────── --}}
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-amber-500 mb-1">Hindu Calendar</p>
            <h1 class="text-3xl font-bold text-white tracking-tight">Upcoming Festivals</h1>
            <p class="mt-2 text-sm text-slate-400">
                Next <span class="text-slate-200 font-medium">120 days</span> ·
                <span class="text-slate-200 font-medium">{{ count($list) }}</span> festival{{ count($list) !== 1 ? 's' : '' }} found ·
                <span class="text-slate-400">{{ $today->format('d M Y') }}</span>
            </p>
        </div>
        {{-- Location pill --}}
        <div class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs text-slate-400 self-start sm:self-auto">
            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ number_format((float)$input['lat'], 4) }}, {{ number_format((float)$input['lon'], 4) }} · {{ $input['tz'] }}
        </div>
    </div>
</div>

@if(empty($list))
    {{-- Empty state --}}
    <div class="flex flex-col items-center justify-center rounded-2xl border border-white/10 bg-white/5 py-20 text-center">
        <p class="text-5xl mb-4">🗓️</p>
        <p class="text-lg font-semibold text-white mb-2">No festivals in the next 120 days</p>
        <p class="text-sm text-slate-400">Try changing your location or date in the settings.</p>
    </div>
@else

    {{-- ── Next up spotlight ───────────────────────────────────────────── --}}
    @if($nextFestival)
    @php
        $daysUntil = (int) $today->diff($nextFestival['date'])->days;
        $spotEmoji = $getFestivalEmoji($nextFestival['names']);
    @endphp
    <div class="relative mb-6 overflow-hidden rounded-2xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-transparent p-6">
        <div class="absolute -right-8 -top-8 text-[120px] opacity-10 select-none pointer-events-none">{{ $spotEmoji }}</div>
        <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 border border-amber-500/20 text-4xl">
                {{ $spotEmoji }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-400 mb-1">
                    {{ $daysUntil === 0 ? '🎉 Today!' : ($daysUntil === 1 ? 'Tomorrow' : "In {$daysUntil} days") }}
                </p>
                <h2 class="text-xl font-bold text-white">{{ implode(' · ', $nextFestival['names']) }}</h2>
                <p class="mt-1 text-sm text-slate-400">
                    {{ $nextFestival['date']->format('l, d F Y') }}
                </p>
            </div>
            <div class="shrink-0 text-right">
                <p class="text-3xl font-black text-amber-400 tabular-nums">{{ $nextFestival['date']->format('d') }}</p>
                <p class="text-xs font-semibold text-amber-500/70 uppercase tracking-wide">{{ $nextFestival['date']->format('M') }}</p>
                <p class="text-xs text-slate-500">{{ $nextFestival['date']->format('Y') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Month-grouped festival list ────────────────────────────────── --}}
    <div class="space-y-6">
        @foreach($byMonth as $monthKey => $monthItems)
            @php
                $monthDt   = \DateTimeImmutable::createFromFormat('Y-m', $monthKey, new \DateTimeZone($input['tz'] ?? 'Asia/Kolkata'));
                $monthName = $monthDt ? $monthDt->format('F Y') : $monthKey;
                $isCurrentMonth = ($monthKey === $today->format('Y-m'));
            @endphp

            <div>
                {{-- Month header --}}
                <div class="flex items-center gap-3 mb-3">
                    <h3 class="text-xs font-bold uppercase tracking-widest {{ $isCurrentMonth ? 'text-amber-400' : 'text-slate-500' }}">
                        {{ $monthName }}{{ $isCurrentMonth ? ' · Now' : '' }}
                    </h3>
                    <div class="flex-1 h-px bg-white/5"></div>
                    <span class="text-xs text-slate-600">{{ count($monthItems) }} festival{{ count($monthItems) !== 1 ? 's' : '' }}</span>
                </div>

                {{-- Festival cards --}}
                <div class="space-y-2">
                    @foreach($monthItems as $item)
                        @php
                            $daysUntil = (int) $today->diff($item['date'])->days;
                            $isToday   = $item['date']->format('Y-m-d') === $today->format('Y-m-d');
                            $isPast    = $item['date'] < $today;
                            $emoji     = $getFestivalEmoji($item['names']);
                        @endphp

                        <div class="group flex items-start gap-4 rounded-xl border px-4 py-3.5 transition
                                    {{ $isToday
                                        ? 'border-amber-500/40 bg-amber-500/8 hover:bg-amber-500/12'
                                        : ($isPast
                                            ? 'border-white/5 bg-white/[0.015] opacity-50'
                                            : 'border-white/8 bg-white/[0.025] hover:bg-white/5 hover:border-white/15') }}">

                            {{-- Day number block --}}
                            <div class="shrink-0 w-12 text-center">
                                <p class="text-xl font-black leading-none {{ $isToday ? 'text-amber-400' : ($isPast ? 'text-slate-600' : 'text-slate-200') }}">
                                    {{ $item['date']->format('d') }}
                                </p>
                                <p class="text-xs font-medium uppercase {{ $isToday ? 'text-amber-500/70' : 'text-slate-600' }}">
                                    {{ $item['date']->format('M') }}
                                </p>
                            </div>

                            {{-- Divider --}}
                            <div class="shrink-0 w-px self-stretch {{ $isToday ? 'bg-amber-500/30' : 'bg-white/8' }}"></div>

                            {{-- Emoji --}}
                            <div class="shrink-0 text-2xl leading-none mt-0.5">{{ $emoji }}</div>

                            {{-- Festival name(s) --}}
                            <div class="flex-1 min-w-0">
                                @foreach($item['names'] as $name)
                                    <p class="text-sm font-semibold {{ $isToday ? 'text-amber-200' : ($isPast ? 'text-slate-500' : 'text-slate-100') }} leading-snug">
                                        {{ $name }}
                                    </p>
                                @endforeach
                                <p class="mt-0.5 text-xs text-slate-600">
                                    {{ $item['date']->format('l') }}
                                </p>
                            </div>

                            {{-- Days badge --}}
                            <div class="shrink-0 text-right">
                                @if($isToday)
                                    <span class="inline-flex items-center rounded-full bg-amber-500/20 border border-amber-500/30 px-2.5 py-1 text-xs font-bold text-amber-300">
                                        Today
                                    </span>
                                @elseif(!$isPast)
                                    <span class="text-xs text-slate-500 tabular-nums">
                                        {{ $daysUntil }}d
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Footer summary ─────────────────────────────────────────────── --}}
    <div class="mt-8 rounded-xl border border-white/5 bg-white/[0.02] px-5 py-4">
        <div class="flex flex-wrap gap-6 text-xs text-slate-500">
            <span>📅 Showing next 120 days from <strong class="text-slate-400">{{ $today->format('d M Y') }}</strong></span>
            <span>📍 Lat {{ number_format((float)$input['lat'], 2) }}° Lon {{ number_format((float)$input['lon'], 2) }}°</span>
            <span>🕐 Timezone: {{ $input['tz'] }}</span>
            <span>🎉 {{ count($list) }} festivals total</span>
        </div>
    </div>

@endif
@endsection
