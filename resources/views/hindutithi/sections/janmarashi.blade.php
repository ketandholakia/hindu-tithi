@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <div class="text-xs font-semibold uppercase tracking-widest text-violet-400 mb-1">Moon Sign</div>
        <h1 class="text-2xl font-bold text-white">Janmarashi</h1>
        <p class="mt-1 text-sm text-slate-400">Moon sign (Rashi) at the given moment</p>
    </div>

    @include('hindutithi.partials.birth_form')

    <div class="rounded-2xl border border-violet-500/20 bg-violet-500/5 p-6">
        <div class="flex items-center gap-4">
            <span class="text-4xl">🌙</span>
            <div>
                <div class="text-xs font-medium text-slate-400 mb-1">Rashi (Moon Sign)</div>
                <div class="text-2xl font-bold text-violet-300">{{ $tr->translate($rashi) }}</div>
                @if($rashi->nameEnglish() !== $tr->translate($rashi))
                    <div class="text-sm text-slate-400 mt-0.5">{{ $rashi->nameEnglish() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
