@extends('layouts.app')

@section('title', 'Kundli Chart — Birth Chart with Dasha, Varga, Shadbala | Hindutithi')
@section('meta_description', 'Interactive Kundli birth chart with house placements, planetary positions, ascendant, Vimshottari Dasha, Varga charts and Shadbala.')

@section('content')
<div class="space-y-8">
    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-8 shadow-2xl">
        @php
            $currentTab   = $tab ?? 'overview';
            $currentVarga = $currentVarga ?? 'D9';
            $vargaOptions = $vargaOptions ?? [];
            $lagnaRashi   = $lagnaRashi ?? ($kundali->ascendant->rashi->name ?? '—');
            $lagnaDMS     = $lagnaDMS ?? '—';
        @endphp

        {{-- ── Header ──────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-white">Kundli {{ $currentTab === 'varga' ? "· $currentVarga" : '' }}</h1>
                <p class="mt-2 text-sm text-slate-400">Birth chart output with house placements, planetary longitudes, and ascendant details.</p>
            </div>
            <div class="rounded-3xl border border-slate-700 bg-slate-950/80 px-4 py-3 text-sm text-slate-300">
                <div class="flex items-center justify-between gap-4">
                    <span>Ascendant</span>
                    <span class="font-semibold text-amber-400">{{ !empty($featureUnavailable) ? 'Unavailable' : $lagnaRashi }}</span>
                </div>
                <div class="mt-2 flex items-center justify-between gap-4">
                    <span>Placements</span>
                    <span class="font-semibold text-white">{{ !empty($featureUnavailable) ? 0 : count($kundali->placements) }}</span>
                </div>
            </div>
        </div>

        {{-- ── Birth Input Form ──────────────────────────────────────── --}}
        <form method="GET" action="{{ url('/kundli') }}" class="mt-8 grid gap-4 lg:grid-cols-[1.3fr_0.7fr]">
            <input type="hidden" name="tab"   value="{{ $currentTab }}" />
            <input type="hidden" name="varga" value="{{ $currentVarga }}" />
            <div class="grid gap-4 rounded-3xl border border-slate-800 bg-slate-950 p-5">
                <div class="grid gap-3 sm:grid-cols-3">
                    <label class="block text-sm text-slate-300">
                        <span class="text-slate-100">Date</span>
                        <input name="date" type="date" value="{{ $input['date'] }}" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none" />
                    </label>
                    <label class="block text-sm text-slate-300">
                        <span class="text-slate-100">Time</span>
                        <input name="time" type="time" value="{{ $input['time'] }}" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none" />
                    </label>
                    <label class="block text-sm text-slate-300">
                        <span class="text-slate-100">Timezone</span>
                        <input name="tz" type="text" value="{{ $input['tz'] }}" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none" />
                    </label>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <label class="block text-sm text-slate-300">
                        <span class="text-slate-100">Latitude</span>
                        <input name="lat" type="number" step="0.0001" value="{{ $input['lat'] }}" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none" />
                    </label>
                    <label class="block text-sm text-slate-300">
                        <span class="text-slate-100">Longitude</span>
                        <input name="lon" type="number" step="0.0001" value="{{ $input['lon'] }}" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none" />
                    </label>
                    <label class="block text-sm text-slate-300">
                        <span class="text-slate-100">Elevation</span>
                        <input name="elev" type="number" step="1" value="{{ $input['elev'] }}" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none" />
                    </label>
                </div>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5 flex flex-col justify-between">
                <p class="text-sm text-slate-300">Enter birth details and submit to generate the Kundli with charts, planetary positions, and Vimshottari Dasha.</p>
                <button type="submit" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">Generate Chart</button>
            </div>
        </form>

        @if(!empty($featureUnavailable))
            <div class="mt-8 rounded-3xl border border-amber-500/20 bg-amber-500/10 p-5 text-sm text-amber-100">
                <strong>Astrology engine unavailable.</strong>
                The installed <code>vittix/panchang</code> version does not currently include the Kundli feature.
            </div>
        @else
            {{-- ── Tab Navigation ─────────────────────────────────────── --}}
            <div class="mt-8 overflow-hidden rounded-3xl border border-slate-800 bg-slate-950 px-3 py-3">
                <div class="flex flex-wrap gap-2">
                    @foreach(['overview' => 'Overview', 'dasha' => 'Dasha', 'varga' => 'Varga', 'shadbala' => 'Shadbala', 'yogas' => 'Yogas'] as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'varga' => $currentVarga]) }}"
                           class="inline-flex items-center rounded-2xl border px-4 py-2 text-sm font-semibold transition {{ $currentTab === $key ? 'border-amber-400 bg-amber-500/15 text-amber-100' : 'border-slate-700 bg-slate-900 text-slate-300 hover:border-slate-500 hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 space-y-6">

                {{-- ════════════════════════════════════════════════════════
                     OVERVIEW TAB
                ════════════════════════════════════════════════════════ --}}
                @if($currentTab === 'overview')

                    {{-- ── Section 1: Two North Indian Charts ──────────── --}}
                    <div class="grid gap-6 lg:grid-cols-2">

                        {{-- Lagna Chart --}}
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em] text-amber-400">Lagna Chart</h3>
                            @php
                                // North Indian chart: houses are fixed positions.
                                // House 1 is always top-center cell; assignments go clockwise.
                                // Cell positions (0-indexed row, col) in a 4×4 grid:
                                //   House:  1=top(1,1&1,2 merged)... we use SVG absolute coords.
                                // We render a square SVG with the classic North Indian layout.

                                // The 12 cell centers for the North Indian chart (in a 420×420 SVG):
                                // The layout: 4 cols × 4 rows, center 2×2 is the diamond.
                                // House positions (fixed, not ascendant-relative):
                                //  12  1  2  3
                                //  11  *  *  4
                                //  10  *  *  5
                                //   9  8  7  6
                                // But in North Indian, House 1 is always top-center-left:
                                //  12  1  2  3
                                //  11  [center] 4
                                //  10  [center] 5
                                //   9  8  7  6
                                // The cell geometry (420×420 total, cell=105×105):
                                $cellSize = 105;
                                // (row, col) for house number 1..12, 0-indexed
                                $houseCoords = [
                                    1  => [0, 1], 2  => [0, 2], 3  => [0, 3],
                                    4  => [1, 3], 5  => [2, 3], 6  => [3, 3],
                                    7  => [3, 2], 8  => [3, 1], 9  => [3, 0],
                                    10 => [2, 0], 11 => [1, 0], 12 => [0, 0],
                                ];
                            @endphp
                            <svg viewBox="0 0 420 420" class="w-full" xmlns="http://www.w3.org/2000/svg">
                                <rect width="420" height="420" fill="#020617" rx="12"/>

                                {{-- Draw the 12 outer cells --}}
                                @foreach($houseCoords as $hnum => [$row, $col])
                                    @php
                                        $x = $col * $cellSize;
                                        $y = $row * $cellSize;
                                        $cx = $x + $cellSize / 2;
                                        $cy = $y + $cellSize / 2;
                                    @endphp
                                    <rect x="{{ $x + 1 }}" y="{{ $y + 1 }}" width="{{ $cellSize - 2 }}" height="{{ $cellSize - 2 }}" fill="#0f172a" stroke="#334155" stroke-width="1"/>
                                    {{-- House number --}}
                                    <text x="{{ $x + 6 }}" y="{{ $y + 16 }}" font-size="10" fill="#475569" font-family="sans-serif" font-weight="600">{{ $hnum }}</text>
                                    {{-- Planet abbreviations --}}
                                    @php $hplanets = $houses[$hnum] ?? []; @endphp
                                    @foreach($hplanets as $pidx => $pinfo)
                                        @php
                                            $abbr  = is_array($pinfo) ? $pinfo['abbr']  : $pinfo;
                                            $color = is_array($pinfo) ? $pinfo['color'] : '#e2e8f0';
                                            $cols  = min(count($hplanets), 3);
                                            $px    = $cx + (($pidx % $cols) - ($cols - 1) / 2) * 24;
                                            $py    = $cy + floor($pidx / $cols) * 18;
                                        @endphp
                                        <text x="{{ $px }}" y="{{ $py + 6 }}" font-size="13" fill="{{ $color }}" font-family="sans-serif" font-weight="700" text-anchor="middle">{{ $abbr }}</text>
                                    @endforeach
                                @endforeach

                                {{-- Centre diamond (2×2 merged cells with X diagonal lines) --}}
                                @php $cd = $cellSize * 2; $cx0 = $cellSize; $cy0 = $cellSize; @endphp
                                <rect x="{{ $cx0 }}" y="{{ $cy0 }}" width="{{ $cd }}" height="{{ $cd }}" fill="#0a0f1e" stroke="#334155" stroke-width="1"/>
                                <line x1="{{ $cx0 }}" y1="{{ $cy0 }}" x2="{{ $cx0 + $cd }}" y2="{{ $cy0 + $cd }}" stroke="#1e40af" stroke-width="1.5"/>
                                <line x1="{{ $cx0 + $cd }}" y1="{{ $cy0 }}" x2="{{ $cx0 }}" y2="{{ $cy0 + $cd }}" stroke="#1e40af" stroke-width="1.5"/>

                                {{-- Lagna label in center --}}
                                <text x="210" y="204" font-size="11" fill="#f59e0b" font-family="sans-serif" font-weight="700" text-anchor="middle">{{ $lagnaRashi }}</text>
                                <text x="210" y="222" font-size="9"  fill="#64748b" font-family="sans-serif" text-anchor="middle">{{ $lagnaDMS }}</text>
                            </svg>
                        </div>

                        {{-- Navamsa (D9) Chart --}}
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em] text-amber-400">Navamsa Chart</h3>
                            @if(!empty($d9Houses))
                                <svg viewBox="0 0 420 420" class="w-full" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="420" height="420" fill="#020617" rx="12"/>
                                    @php $houseCoords2 = [
                                        1  => [0, 1], 2  => [0, 2], 3  => [0, 3],
                                        4  => [1, 3], 5  => [2, 3], 6  => [3, 3],
                                        7  => [3, 2], 8  => [3, 1], 9  => [3, 0],
                                        10 => [2, 0], 11 => [1, 0], 12 => [0, 0],
                                    ]; @endphp
                                    @foreach($houseCoords2 as $hnum => [$row, $col])
                                        @php
                                            $x = $col * $cellSize;
                                            $y = $row * $cellSize;
                                            $cx = $x + $cellSize / 2;
                                            $cy = $y + $cellSize / 2;
                                        @endphp
                                        <rect x="{{ $x + 1 }}" y="{{ $y + 1 }}" width="{{ $cellSize - 2 }}" height="{{ $cellSize - 2 }}" fill="#0f172a" stroke="#334155" stroke-width="1"/>
                                        <text x="{{ $x + 6 }}" y="{{ $y + 16 }}" font-size="10" fill="#475569" font-family="sans-serif" font-weight="600">{{ $hnum }}</text>
                                        @php $hplanets2 = $d9Houses[$hnum] ?? []; @endphp
                                        @foreach($hplanets2 as $pidx => $pinfo)
                                            @php
                                                $abbr  = is_array($pinfo) ? $pinfo['abbr']  : $pinfo;
                                                $color = is_array($pinfo) ? $pinfo['color'] : '#e2e8f0';
                                                $cols  = min(count($hplanets2), 3);
                                                $px    = $cx + (($pidx % $cols) - ($cols - 1) / 2) * 24;
                                                $py    = $cy + floor($pidx / $cols) * 18;
                                            @endphp
                                            <text x="{{ $px }}" y="{{ $py + 6 }}" font-size="13" fill="{{ $color }}" font-family="sans-serif" font-weight="700" text-anchor="middle">{{ $abbr }}</text>
                                        @endforeach
                                    @endforeach
                                    <rect x="{{ $cellSize }}" y="{{ $cellSize }}" width="{{ $cellSize * 2 }}" height="{{ $cellSize * 2 }}" fill="#0a0f1e" stroke="#334155" stroke-width="1"/>
                                    <line x1="{{ $cellSize }}" y1="{{ $cellSize }}" x2="{{ $cellSize * 3 }}" y2="{{ $cellSize * 3 }}" stroke="#1e40af" stroke-width="1.5"/>
                                    <line x1="{{ $cellSize * 3 }}" y1="{{ $cellSize }}" x2="{{ $cellSize }}" y2="{{ $cellSize * 3 }}" stroke="#1e40af" stroke-width="1.5"/>
                                    <text x="210" y="204" font-size="11" fill="#f59e0b" font-family="sans-serif" font-weight="700" text-anchor="middle">{{ $d9Lagna ?? '—' }}</text>
                                    <text x="210" y="222" font-size="9"  fill="#64748b" font-family="sans-serif" text-anchor="middle">D9</text>
                                </svg>
                            @else
                                <div class="flex h-64 items-center justify-center rounded-2xl border border-amber-500/20 bg-amber-500/10 text-sm text-amber-100">
                                    Navamsa chart not available
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── Section 2: Planetary Table + Dasha ─────────── --}}
                    <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">

                        {{-- Planetary Position Table --}}
                        <div class="overflow-x-auto rounded-3xl border border-slate-800 bg-slate-950 p-5">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em] text-amber-400">Planetary Positions</h3>
                            @if(!empty($enrichedPlacements))
                                <table class="min-w-full border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-800 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                            <th class="pb-3 pr-3">Planet</th>
                                            <th class="pb-3 pr-2 text-center" title="Combust">C</th>
                                            <th class="pb-3 pr-3 text-center" title="Retrograde">R</th>
                                            <th class="pb-3 pr-4">Rashi</th>
                                            <th class="pb-3 pr-4">Longitude</th>
                                            <th class="pb-3 pr-4">Nakshatra</th>
                                            <th class="pb-3 pr-3 text-center">Pada</th>
                                            <th class="pb-3">Relation</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        {{-- Ascendant row --}}
                                        @php
                                            $ascNak = \App\Services\KundaliHelper::nakshatra($kundali->ascendant->siderealLongitude);
                                        @endphp
                                        <tr class="text-slate-200 hover:bg-slate-900/50 transition">
                                            <td class="py-2.5 pr-3 font-semibold text-white">Asc</td>
                                            <td class="py-2.5 pr-2 text-center">—</td>
                                            <td class="py-2.5 pr-3 text-center">—</td>
                                            <td class="py-2.5 pr-4 text-slate-300">{{ $lagnaRashi }}</td>
                                            <td class="py-2.5 pr-4 font-mono text-xs text-slate-300">{{ $lagnaDMS }}</td>
                                            <td class="py-2.5 pr-4 text-slate-300">{{ $ascNak['name'] }}</td>
                                            <td class="py-2.5 pr-3 text-center text-slate-300">{{ $ascNak['pada'] }}</td>
                                            <td class="py-2.5 text-slate-500">—</td>
                                        </tr>
                                        @foreach($enrichedPlacements as $pl)
                                            <tr class="text-slate-200 hover:bg-slate-900/50 transition">
                                                <td class="py-2.5 pr-3">
                                                    <span class="font-semibold" style="color: {{ $pl['color'] }}">{{ $pl['name'] }}</span>
                                                </td>
                                                <td class="py-2.5 pr-2 text-center">
                                                    @if($pl['isCombust'])
                                                        <span class="inline-block h-2 w-2 rounded-full bg-orange-400" title="Combust"></span>
                                                    @else
                                                        <span class="text-slate-700">—</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 pr-3 text-center">
                                                    @if($pl['isRetro'])
                                                        <span class="text-xs font-bold text-sky-400" title="Retrograde">R</span>
                                                    @else
                                                        <span class="text-slate-700">—</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 pr-4 text-slate-300">{{ $pl['rashi'] }}</td>
                                                <td class="py-2.5 pr-4 font-mono text-xs text-slate-300">{{ $pl['dms'] }}</td>
                                                <td class="py-2.5 pr-4 text-slate-300">{{ $pl['nakshatra'] }}</td>
                                                <td class="py-2.5 pr-3 text-center text-slate-300">{{ $pl['pada'] }}</td>
                                                <td class="py-2.5">
                                                    @php
                                                        $rel = $pl['relation'];
                                                        $relColor = match(true) {
                                                            str_contains($rel, 'Own')        => 'text-amber-400',
                                                            str_contains($rel, 'Exalted')    => 'text-emerald-400',
                                                            str_contains($rel, 'Debilitated')=> 'text-red-400',
                                                            str_contains($rel, 'Friendly')   => 'text-sky-400',
                                                            str_contains($rel, 'Enemy')      => 'text-rose-400',
                                                            default                          => 'text-slate-500',
                                                        };
                                                    @endphp
                                                    <span class="text-xs font-medium {{ $relColor }}">{{ $rel }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <p class="mt-3 text-xs text-slate-600">
                                    Note: <span class="text-orange-400">●</span> Combust &nbsp;
                                    <span class="font-bold text-sky-400">R</span> Retrograde
                                </p>
                            @else
                                <p class="text-slate-500 text-sm">No placement data available.</p>
                            @endif
                        </div>

                        {{-- Vimshottari Dasha Compact Table --}}
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                            <h3 class="mb-1 text-sm font-semibold uppercase tracking-[0.15em] text-amber-400">Vimshottari Dasha</h3>
                            @if($vimshottariAvailable && !empty($dashaCompact))
                                @if($dashaBalance)
                                    <div class="mb-4 rounded-2xl border border-amber-500/20 bg-amber-500/8 px-3 py-2 text-center">
                                        <p class="text-xs text-slate-500">Balance of Dasha</p>
                                        <p class="mt-0.5 text-sm font-semibold text-amber-300">{{ $dashaBalance }}</p>
                                    </div>
                                @endif
                                <div class="space-y-1">
                                    @foreach($dashaCompact as $d)
                                        <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-900/60 px-3 py-2 text-sm hover:bg-slate-800/60 transition cursor-pointer">
                                            <span class="w-8 font-bold text-amber-400">{{ $d['abbr'] }}</span>
                                            <span class="font-mono text-xs text-slate-300">{{ $d['start'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-3 text-xs text-slate-600 text-center">Tap row to show Antar Dasha</p>
                            @else
                                <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-sm text-amber-100">
                                    Vimshottari Dasha not available with this package version.
                                </div>
                            @endif
                        </div>
                    </div>

                {{-- ════════════════════════════════════════════════════════
                     DASHA TAB
                ════════════════════════════════════════════════════════ --}}
                @elseif($currentTab === 'dasha')
                    <div class="rounded-3xl border border-slate-800 bg-slate-950 p-6 text-sm text-slate-300">
                        <h3 class="text-lg font-semibold text-white">Vimshottari Dasha</h3>
                        @if($vimshottariAvailable && !empty($dashas))
                            @if($dashaBalance ?? '')
                                <div class="mt-4 inline-block rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-2 text-sm text-amber-200">
                                    Balance of Dasha: <strong>{{ $dashaBalance }}</strong>
                                </div>
                            @endif
                            <div class="mt-6 space-y-4">
                                @foreach($dashas as $d)
                                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">
                                        <div class="flex items-center justify-between gap-4 text-white">
                                            <span class="font-semibold text-amber-300">{{ $d->body->name }}</span>
                                            <span class="font-mono text-xs text-slate-400">{{ $d->start->format('Y-m-d') }} — {{ $d->end->format('Y-m-d') }}</span>
                                        </div>
                                        @if(!empty($d->subPeriods))
                                            <ul class="mt-3 space-y-2 text-slate-300">
                                                @foreach($d->subPeriods as $s)
                                                    <li class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                                        <span class="font-semibold text-white">{{ $s->body->name }}</span>
                                                        <span class="block text-xs font-mono text-slate-500">{{ $s->start->format('Y-m-d') }} → {{ $s->end->format('Y-m-d') }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-4 rounded-3xl border border-amber-500/20 bg-amber-500/10 p-5 text-amber-100">
                                Vimshottari Dasha is not supported by the installed package version.
                            </div>
                        @endif
                    </div>

                {{-- ════════════════════════════════════════════════════════
                     VARGA TAB
                ════════════════════════════════════════════════════════ --}}
                @elseif($currentTab === 'varga')
                    <div class="space-y-6">
                        <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5 text-sm text-slate-300">
                            <form method="GET" action="{{ url('/kundli') }}" class="grid gap-4 sm:grid-cols-[1fr_auto] items-end">
                                <input type="hidden" name="tab"   value="varga" />
                                <input type="hidden" name="date"  value="{{ $input['date'] }}" />
                                <input type="hidden" name="time"  value="{{ $input['time'] }}" />
                                <input type="hidden" name="tz"    value="{{ $input['tz'] }}" />
                                <input type="hidden" name="lat"   value="{{ $input['lat'] }}" />
                                <input type="hidden" name="lon"   value="{{ $input['lon'] }}" />
                                <input type="hidden" name="elev"  value="{{ $input['elev'] }}" />
                                <label class="block text-sm text-slate-300">
                                    <span class="text-slate-100">Divisional chart</span>
                                    <select name="varga" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-white focus:border-amber-400 focus:outline-none">
                                        @foreach($vargaOptions as $option)
                                            <option value="{{ $option }}" {{ $currentVarga === $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <button type="submit" class="rounded-2xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-amber-300">Refresh</button>
                            </form>
                        </div>

                        @if($vargaAvailable && !empty($vargaData))
                            <div class="rounded-3xl border border-slate-800 bg-slate-950 p-5">
                                <h3 class="mb-4 text-lg font-semibold text-white">{{ $currentVarga }} Varga Chart</h3>
                                <div class="grid gap-8 lg:grid-cols-[1fr_1fr]">
                                    <div>
                                        <svg viewBox="0 0 420 420" class="w-full" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="420" height="420" fill="#020617" rx="12"/>
                                            @php 
                                                $cellSize = 105;
                                                $houseCoords3 = [
                                                    1  => [0, 1], 2  => [0, 2], 3  => [0, 3],
                                                    4  => [1, 3], 5  => [2, 3], 6  => [3, 3],
                                                    7  => [3, 2], 8  => [3, 1], 9  => [3, 0],
                                                    10 => [2, 0], 11 => [1, 0], 12 => [0, 0],
                                                ]; 
                                            @endphp
                                            @foreach($houseCoords3 as $hnum => [$row, $col])
                                                @php
                                                    $x = $col * $cellSize; $y = $row * $cellSize;
                                                    $cx = $x + $cellSize / 2; $cy = $y + $cellSize / 2;
                                                @endphp
                                                <rect x="{{ $x+1 }}" y="{{ $y+1 }}" width="{{ $cellSize-2 }}" height="{{ $cellSize-2 }}" fill="#0f172a" stroke="#334155" stroke-width="1"/>
                                                <text x="{{ $x+6 }}" y="{{ $y+16 }}" font-size="10" fill="#475569" font-family="sans-serif">{{ $hnum }}</text>
                                                @foreach($vargaHouses[$hnum] ?? [] as $pidx => $pName)
                                                    @php
                                                        $va = \App\Services\KundaliHelper::abbr($pName);
                                                        $vc = \App\Services\KundaliHelper::color($va);
                                                        $cols2 = min(count($vargaHouses[$hnum]), 3);
                                                        $vpx = $cx + (($pidx % $cols2) - ($cols2-1)/2)*24;
                                                        $vpy = $cy + floor($pidx/$cols2)*18;
                                                    @endphp
                                                    <text x="{{ $vpx }}" y="{{ $vpy+6 }}" font-size="13" fill="{{ $vc }}" font-family="sans-serif" font-weight="700" text-anchor="middle">{{ $va }}</text>
                                                @endforeach
                                            @endforeach
                                            <rect x="{{ $cellSize }}" y="{{ $cellSize }}" width="{{ $cellSize*2 }}" height="{{ $cellSize*2 }}" fill="#0a0f1e" stroke="#334155" stroke-width="1"/>
                                            <line x1="{{ $cellSize }}" y1="{{ $cellSize }}" x2="{{ $cellSize*3 }}" y2="{{ $cellSize*3 }}" stroke="#1e40af" stroke-width="1.5"/>
                                            <line x1="{{ $cellSize*3 }}" y1="{{ $cellSize }}" x2="{{ $cellSize }}" y2="{{ $cellSize*3 }}" stroke="#1e40af" stroke-width="1.5"/>
                                            <text x="210" y="210" font-size="12" fill="#f59e0b" font-family="sans-serif" font-weight="700" text-anchor="middle">{{ $currentVarga }}</text>
                                        </svg>
                                    </div>
                                    <div class="rounded-3xl border border-slate-800 bg-slate-900 p-4">
                                        <h4 class="mb-4 text-xs font-semibold uppercase tracking-widest text-amber-300">House Summary</h4>
                                        <div class="space-y-2">
                                            @foreach(range(1, 12) as $house)
                                                <div class="flex justify-between gap-4 rounded-xl bg-slate-950 px-3 py-2 text-sm">
                                                    <span class="text-slate-400">House {{ $house }}</span>
                                                    <span class="text-slate-200">{{ implode(', ', $vargaHouses[$house] ?? []) ?: '–' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-3xl border border-amber-500/20 bg-amber-500/10 p-5 text-amber-100">
                                Varga charts are not currently available with the installed package version.
                            </div>
                        @endif
                    </div>

                {{-- ════════════════════════════════════════════════════════
                     SHADBALA TAB
                ════════════════════════════════════════════════════════ --}}
                @elseif($currentTab === 'shadbala')
                    <div class="rounded-3xl border border-slate-800 bg-slate-950 p-6 text-sm text-slate-300">
                        <h3 class="text-lg font-semibold text-white">Shadbala</h3>
                        @if($shadbalaAvailable && !empty($shadbala))
                            <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-800 bg-slate-900 p-4">
                                <table class="min-w-full border-collapse text-sm text-slate-200">
                                    <thead>
                                        <tr class="border-b border-slate-700 text-left text-slate-400">
                                            <th class="py-3 pr-4">Planet</th>
                                            <th class="py-3 pr-4">StS</th>
                                            <th class="py-3 pr-4">Dik</th>
                                            <th class="py-3 pr-4">Kala</th>
                                            <th class="py-3 pr-4">Chesta</th>
                                            <th class="py-3 pr-4">Naisargika</th>
                                            <th class="py-3 pr-4">Drik</th>
                                            <th class="py-3 pr-4">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800">
                                        @foreach($shadbala->planets as $vals)
                                            <tr>
                                                <td class="py-3 pr-4 text-slate-100">{{ $vals->body->name }}</td>
                                                <td class="py-3 pr-4">{{ $vals->sthanaBala }}</td>
                                                <td class="py-3 pr-4">{{ $vals->dikBala }}</td>
                                                <td class="py-3 pr-4">{{ $vals->kalaBala }}</td>
                                                <td class="py-3 pr-4">{{ $vals->chestaBala }}</td>
                                                <td class="py-3 pr-4">{{ $vals->naisargikaBala }}</td>
                                                <td class="py-3 pr-4">{{ $vals->drikBala }}</td>
                                                <td class="py-3 pr-4">{{ number_format($vals->getTotalRupas(), 3) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="mt-4 rounded-3xl border border-amber-500/20 bg-amber-500/10 p-5 text-amber-100">
                                Shadbala is not currently available with the installed package version.
                            </div>
                        @endif
                    </div>

                {{-- ════════════════════════════════════════════════════════
                     YOGAS TAB
                ════════════════════════════════════════════════════════ --}}
                @elseif($currentTab === 'yogas')
                    <div class="rounded-3xl border border-slate-800 bg-slate-950 p-6 text-sm text-slate-300">
                        <h3 class="text-lg font-semibold text-white">Detected Yogas</h3>
                        @if($yogasAvailable && !empty($yogas))
                            <ul class="mt-6 space-y-3">
                                @foreach($yogas as $y)
                                    <li class="rounded-3xl border border-slate-800 bg-slate-900 p-4 text-slate-200">
                                        <span class="font-semibold text-white">{{ $y->name }}</span>
                                        <p class="mt-2 text-sm text-slate-300">{{ $y->description }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @elseif($yogasAvailable)
                            <div class="mt-4 rounded-3xl border border-slate-800 bg-slate-900 p-5 text-slate-300">
                                No classical yogas were detected for the provided birth details.
                            </div>
                        @else
                            <div class="mt-4 rounded-3xl border border-amber-500/20 bg-amber-500/10 p-5 text-amber-100">
                                Yoga detection is not currently available with the installed package version.
                            </div>
                        @endif
                    </div>
                @endif

            </div>{{-- /space-y-6 --}}
        @endif
    </div>
</div>
@endsection
