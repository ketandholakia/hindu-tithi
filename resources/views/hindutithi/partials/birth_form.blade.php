<form method="POST" action="{{ route('hindutithi.setBirth') }}" class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 backdrop-blur">
    @csrf

    {{-- ── Section header ─────────────────────────────────────────── --}}
    <div class="mb-4 flex items-center gap-2">
        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
        <span class="text-xs font-semibold uppercase tracking-widest text-amber-400">Session Parameters</span>
    </div>

    {{-- ── Date / time / location fields ──────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <div class="space-y-1">
            <label class="block text-xs font-medium text-slate-400">Date</label>
            <input name="date" type="date"
                   class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white focus:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition"
                   value="{{ $input['date'] ?? date('Y-m-d') }}">
        </div>
        <div class="space-y-1">
            <label class="block text-xs font-medium text-slate-400">Time</label>
            <input name="time" type="time"
                   class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white focus:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition"
                   value="{{ $input['time'] ?? '06:00' }}">
        </div>
        <div class="space-y-1">
            <label class="block text-xs font-medium text-slate-400">Timezone</label>
            <input name="tz"
                   class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition"
                   value="{{ $input['tz'] ?? 'Asia/Kolkata' }}" placeholder="Asia/Kolkata">
        </div>
        <div class="space-y-1">
            <label class="block text-xs font-medium text-slate-400">Latitude</label>
            <input name="lat" step="any" type="number"
                   class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white focus:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition"
                   value="{{ $input['lat'] ?? '23.0225' }}">
        </div>
        <div class="space-y-1">
            <label class="block text-xs font-medium text-slate-400">Longitude</label>
            <input name="lon" step="any" type="number"
                   class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white focus:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition"
                   value="{{ $input['lon'] ?? '72.5714' }}">
        </div>
        <div class="space-y-1">
            <label class="block text-xs font-medium text-slate-400">Elev. (m)</label>
            <input name="elev" step="1" type="number"
                   class="w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white focus:border-amber-500/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition"
                   value="{{ $input['elev'] ?? 0 }}">
        </div>
    </div>

    {{-- ── Language picker ─────────────────────────────────────────── --}}
    @php
        use App\Services\PanchangTranslator;
        $activeLang = $input['lang'] ?? 'en';
    @endphp
    <div class="mt-4 border-t border-white/5 pt-4">
        <div class="mb-2.5 flex items-center gap-2">
            <span class="text-xs font-medium text-slate-500">Language</span>
            <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-xs font-semibold text-amber-400">
                {{ PanchangTranslator::SUPPORTED[$activeLang] ?? 'English' }}
            </span>
        </div>
        {{-- Hidden input so the currently selected lang is always submitted --}}
        <input type="hidden" name="lang" id="lang-input" value="{{ $activeLang }}">
        <div class="flex flex-wrap gap-1.5">
            @foreach(PanchangTranslator::SUPPORTED as $code => $label)
                @php $isActive = ($code === $activeLang); @endphp
                <button type="submit"
                        name="lang" value="{{ $code }}"
                        title="{{ $label }}"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition
                               {{ $isActive
                                  ? 'border-amber-400/60 bg-amber-400/15 text-amber-300 shadow-sm shadow-amber-400/10'
                                  : 'border-white/10 bg-slate-800/60 text-slate-400 hover:border-amber-400/40 hover:bg-amber-400/10 hover:text-amber-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── City presets + Apply ─────────────────────────────────────── --}}
    <div class="mt-4 flex flex-wrap items-center gap-2">
        <span class="text-xs text-slate-600 mr-1">Quick:</span>
        @foreach([
            ['New Delhi',  '28.6139', '77.2090'],
            ['Mumbai',     '19.0760', '72.8777'],
            ['Bengaluru',  '12.9716', '77.5946'],
            ['Kolkata',    '22.5726', '88.3639'],
            ['Chennai',    '13.0827', '80.2707'],
        ] as [$city, $lat, $lon])
            <button type="button"
                    onclick="document.querySelector('[name=lat]').value='{{ $lat }}';document.querySelector('[name=lon]').value='{{ $lon }}';document.querySelector('[name=tz]').value='Asia/Kolkata'; this.closest('form').submit();"
                    class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-400 transition hover:border-amber-500/50 hover:text-amber-300 hover:bg-amber-500/10">
                {{ $city }}
            </button>
        @endforeach

        <div class="ml-auto">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-slate-900 transition hover:bg-amber-400 active:scale-95">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Apply
            </button>
        </div>
    </div>
</form>
