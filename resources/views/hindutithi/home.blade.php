@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 shadow-2xl shadow-slate-950/20">
            <div class="grid gap-8 px-6 py-8 md:px-10 lg:grid-cols-[minmax(0,1.8fr)_minmax(320px,1fr)] lg:px-12 lg:py-12">
                <div class="space-y-6">
                    <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">
                        Hindutithi Panchang demo
                    </span>
                    <div class="space-y-4">
                        <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Explore Panchang calculations, calendar data, and API testing in one workspace.
                        </h1>
                        <p class="max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">
                            Set a date, time, timezone, and location once, then inspect day-based results,
                            moment-based astrology, calendar summaries, and developer APIs without leaving the app.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('hindutithi.day') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                            Open Day View
                        </a>
                        <a href="{{ route('hindutithi.help') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Help
                        </a>
                        <a href="{{ route('api.docs') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            API Docs
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/10 p-6 backdrop-blur">
                    <div class="mb-5 text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Current session</div>
                    <dl class="space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                            <dt class="text-slate-300">Date</dt>
                            <dd class="font-medium text-white">{{ $input['date'] ?? '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                            <dt class="text-slate-300">Time</dt>
                            <dd class="font-medium text-white">{{ $input['time'] ?? '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-3">
                            <dt class="text-slate-300">Timezone</dt>
                            <dd class="font-medium text-white">{{ $input['tz'] ?? '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-300">Location</dt>
                            <dd class="font-medium text-white">
                                {{ number_format((float) ($input['lat'] ?? 0), 4) }}, {{ number_format((float) ($input['lon'] ?? 0), 4) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-950/10">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-400">Core views</div>
                <h2 class="mt-3 text-xl font-semibold text-white">Panchang outputs</h2>
                <p class="mt-3 text-sm leading-6 text-slate-300">View day, moment, calendar, muhurta, and natal astrology pages.</p>
                <a href="{{ route('hindutithi.day') }}" class="mt-5 inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-white">
                    Start with Day
                </a>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-950/10">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-400">Developer tools</div>
                <h2 class="mt-3 text-xl font-semibold text-white">API and docs</h2>
                <p class="mt-3 text-sm leading-6 text-slate-300">Use the JSON API, Swagger UI, examples endpoint, and personal API keys.</p>
                <a href="{{ route('api.docs') }}" class="mt-5 inline-flex rounded-full border border-slate-700 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    Open API Docs
                </a>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-950/10">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-400">Support</div>
                <h2 class="mt-3 text-xl font-semibold text-white">Help and account</h2>
                <p class="mt-3 text-sm leading-6 text-slate-300">Read usage notes, create API keys, and sign in with Breeze.</p>
                <a href="{{ route('hindutithi.help') }}" class="mt-5 inline-flex rounded-full border border-slate-700 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    Read Help
                </a>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-950/10 lg:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Explore</div>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Available sections</h2>
                </div>
                <a href="{{ route('api.keys.index') }}" class="inline-flex rounded-full border border-slate-700 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    API Keys
                </a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    'day' => 'Daily Panchang',
                    'moment' => 'Panchang at a Moment',
                    'calendar' => 'Hindu Calendar',
                    'muhurta' => 'Muhurta',
                    'janmarashi' => 'Janmarashi',
                    'kundali' => 'Kundali',
                    'varga' => 'Varga',
                    'vimshottari' => 'Vimshottari',
                    'shadbala' => 'Shadbala',
                    'yogas' => 'Yogas',
                    'festivals' => 'Festivals',
                    'electional' => 'Electional',
                ] as $route => $label)
                    <a href="{{ route('hindutithi.' . $route) }}" class="group rounded-2xl border border-slate-800 bg-slate-950/60 p-4 transition hover:border-slate-600 hover:bg-slate-950">
                        <div class="font-medium text-white">{{ $label }}</div>
                        <div class="mt-1 text-sm text-slate-400 group-hover:text-slate-300">Open the {{ $label }} view for the current session values.</div>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection
