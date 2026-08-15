@extends('layouts.app')

@section('title', 'API Keys — Manage Your Panchang API Keys | Hindutithi')
@section('meta_description', 'Create and manage API keys for the Vittix Vedic Panchang REST API.')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">API Keys</h1>
        <p class="mt-1.5 text-sm text-slate-400 max-w-xl">
            Create and manage access tokens for the Hindutithi Panchang API.
            The full token is shown <span class="font-semibold text-slate-200">only once</span> after creation — store it securely.
        </p>
    </div>

    {{-- ── New key flash ────────────────────────────────────────────────── --}}
    @if (session('new_api_key'))
        <div x-data="{ copied: false }" class="rounded-xl border border-amber-500/40 bg-amber-500/10 p-4 space-y-3">
            <p class="text-sm font-semibold text-amber-300">🔑 New API token — copy it now, it will not be shown again</p>
            <div class="flex items-center gap-2">
                <code class="flex-1 break-all rounded-lg bg-slate-900/80 border border-white/10 px-3 py-2 text-xs text-amber-200 font-mono select-all leading-relaxed">{{ session('new_api_key') }}</code>
                <button @click="navigator.clipboard.writeText('{{ session('new_api_key') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="shrink-0 rounded-lg border border-amber-500/30 bg-amber-500/20 px-3 py-2 text-xs font-medium text-amber-300 hover:bg-amber-500/30 transition">
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak class="text-emerald-400">✓</span>
                </button>
            </div>
            <p class="text-xs text-slate-400">Usage: <code class="text-slate-300">Authorization: Bearer &lt;token&gt;</code></p>
        </div>
    @endif

    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Create form ──────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">

        {{-- Card header --}}
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4 bg-white/[0.02]">
            <div class="flex items-center gap-2.5">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-sky-500/20 text-sky-400 text-xs font-bold">+</span>
                <h2 class="text-sm font-semibold text-white">Generate New API Key</h2>
            </div>
            <span class="text-xs text-slate-600">{{ auth()->user()->apiKeys()->count() }} / 10 used</span>
        </div>

        <form method="POST" action="{{ route('api.keys.store') }}" class="px-5 py-5 space-y-5">
            @csrf

            {{-- Key name --}}
            <div class="space-y-1">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Key Label <span class="text-red-400 normal-case font-normal tracking-normal">*</span>
                </label>
                <input id="name" name="name" type="text" autocomplete="off"
                       value="{{ old('name') }}"
                       placeholder="e.g. Mobile App, Production, Local Dev"
                       required
                       class="w-full rounded-lg border bg-slate-950 px-3.5 py-2.5 text-sm text-white placeholder-slate-600
                              border-white/10 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500/50
                              @error('name') border-red-500/60 @enderror">
                @error('name')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Abilities --}}
            @if (!empty($abilities))
                <div class="space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Scopes / Abilities <span class="text-red-400 normal-case font-normal tracking-normal">*</span>
                    </label>

                    @php
                        $groups = [];
                        foreach ($abilities as $ability => $description) {
                            [$prefix] = explode(':', $ability, 2);
                            $groups[$prefix][$ability] = $description;
                        }
                    @endphp

                    <div class="space-y-2">
                        @foreach ($groups as $group => $groupAbilities)
                            <div class="rounded-xl border border-white/8 bg-white/[0.025] overflow-hidden">
                                {{-- Group label --}}
                                <div class="px-4 py-2 border-b border-white/5 bg-white/[0.02]">
                                    <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ ucfirst($group) }}</span>
                                </div>
                                {{-- Checkboxes --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2">
                                    @foreach ($groupAbilities as $ability => $description)
                                        <label for="ab_{{ $loop->parent->index }}_{{ $loop->index }}"
                                               class="flex items-start gap-3 px-4 py-3.5 cursor-pointer
                                                      hover:bg-white/5 transition
                                                      {{ !$loop->last || ($loop->count % 2 === 0 && !$loop->last) ? 'border-b border-white/5' : '' }}
                                                      {{ $loop->index % 2 === 0 && !$loop->last ? 'sm:border-r sm:border-white/5' : '' }}">
                                            <input type="checkbox"
                                                   id="ab_{{ $loop->parent->index }}_{{ $loop->index }}"
                                                   name="abilities[]"
                                                   value="{{ $ability }}"
                                                   {{ in_array($ability, old('abilities', [])) ? 'checked' : '' }}
                                                   class="mt-0.5 h-4 w-4 shrink-0 rounded border-white/20 bg-slate-800
                                                          text-sky-500 focus:ring-sky-500 focus:ring-offset-slate-900 cursor-pointer">
                                            <div class="min-w-0 leading-tight">
                                                <p class="text-xs font-mono font-semibold text-slate-200">{{ $ability }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $description }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('abilities')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Expiry --}}
            <div class="space-y-1">
                <label for="expires_at" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Expires At <span class="text-slate-600 normal-case font-normal tracking-normal">(optional)</span>
                </label>
                <input id="expires_at" name="expires_at" type="datetime-local"
                       value="{{ old('expires_at') }}"
                       class="rounded-lg border bg-slate-950 px-3.5 py-2.5 text-sm text-white
                              border-white/10 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500/50
                              [color-scheme:dark] @error('expires_at') border-red-500/60 @enderror">
                <p class="text-xs text-slate-600 mt-1">Leave blank for a non-expiring key.</p>
                @error('expires_at')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-4 pt-1 border-t border-white/5">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-6 py-2.5 text-sm font-semibold text-white
                               hover:bg-sky-400 active:scale-95 transition shadow-lg shadow-sky-500/25
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                    🔑 Generate API Key
                </button>
                <p class="text-xs text-slate-600">You can create up to 10 keys per account.</p>
            </div>
        </form>
    </div>

    {{-- ── Existing keys ────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-white/10 bg-slate-900/60 overflow-hidden">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-3.5 bg-white/[0.02]">
            <h2 class="text-sm font-semibold text-white">Your API Keys</h2>
            <span class="text-xs text-slate-500">{{ $keys->count() }} / 10</span>
        </div>

        @if ($keys->isEmpty())
            <div class="px-5 py-10 text-center">
                <p class="text-3xl mb-3">🔐</p>
                <p class="text-sm text-slate-400">No API keys yet. Create one above.</p>
            </div>
        @else
            <ul class="divide-y divide-white/5">
                @foreach ($keys as $key)
                    <li class="px-5 py-4 flex flex-col sm:flex-row sm:items-start gap-4">

                        {{-- Status + details --}}
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            {{-- Status dot --}}
                            @if ($key->revoked_at)
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-slate-600"></span>
                            @elseif ($key->expires_at && $key->expires_at->isPast())
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-amber-400"></span>
                            @else
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-emerald-400 shadow-[0_0_6px_2px_rgba(52,211,153,0.35)]"></span>
                            @endif

                            <div class="min-w-0 flex-1">
                                {{-- Name + badge --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-semibold text-white">{{ $key->name }}</span>
                                    @if ($key->revoked_at)
                                        <span class="rounded-full bg-slate-700 px-2 py-0.5 text-xs text-slate-400">Revoked</span>
                                    @elseif ($key->expires_at && $key->expires_at->isPast())
                                        <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-xs text-amber-300">Expired</span>
                                    @else
                                        <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-xs text-emerald-400">Active</span>
                                    @endif
                                </div>

                                {{-- Meta row --}}
                                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                                    Created {{ $key->created_at?->format('Y-m-d') }}
                                    @if ($key->last_used_at)
                                        · Last used {{ $key->last_used_at->diffForHumans() }}
                                    @else
                                        · Never used
                                    @endif
                                    @if ($key->expires_at && !$key->expires_at->isPast())
                                        · Expires {{ $key->expires_at->format('Y-m-d') }}
                                    @endif
                                    · {{ $key->rate_limit_per_minute }} req/min · {{ number_format($key->rate_limit_per_day) }} req/day
                                </p>

                                {{-- Ability badges --}}
                                @if (!empty($key->abilities))
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach ($key->abilities as $ability)
                                            <span class="rounded-md border border-sky-500/20 bg-sky-500/10 px-2 py-0.5 text-xs font-mono text-sky-300">
                                                {{ $ability }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Revoke button --}}
                        @if (!$key->revoked_at && (!$key->expires_at || !$key->expires_at->isPast()))
                            <form method="POST" action="{{ route('api.keys.destroy', $key) }}"
                                  class="shrink-0"
                                  onsubmit="return confirm('Revoke \'{{ addslashes($key->name) }}\'? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg border border-red-500/30 px-3 py-1.5 text-xs text-red-400
                                               hover:bg-red-500/10 hover:border-red-500/50 transition">
                                    Revoke
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- ── Quick usage guide ────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-white/8 bg-white/[0.025] px-5 py-4 space-y-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">📡 How to use</p>
        <p class="text-sm text-slate-400">
            Pass your token in the <code class="rounded bg-slate-800 px-1.5 py-0.5 text-xs text-slate-200">Authorization</code> header:
        </p>
        <pre class="overflow-x-auto rounded-lg bg-slate-950 border border-white/10 px-4 py-3 text-xs font-mono text-slate-300 leading-relaxed">curl "{{ url('/api/day') }}?date=2026-08-13&lat=23.02&lon=72.57" \
  -H "Authorization: Bearer hindutithi_live_..."</pre>
        <p class="text-xs text-slate-500">
            Every response carries <code class="text-sky-400">X-RateLimit-Remaining</code> and
            <code class="text-sky-400">X-RateLimit-Day-Remaining</code> headers showing your quota.
        </p>
    </div>

</div>
@endsection
