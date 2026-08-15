@extends('layouts.app')

@section('title', 'API Settings - Admin')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        {{ __('API Settings') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text-primary)]">Global API Rate Limits</h3>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">Configure default API rate limits for new keys.</p>
            </div>
            <a href="{{ route('admin.api-tokens.index') }}" class="inline-flex items-center gap-2 rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] px-4 py-2 text-sm font-medium text-[var(--color-text-secondary)] transition hover:bg-[var(--color-bg-elevated)] hover:text-[var(--color-text-primary)]">
                Back to Tokens
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)] p-6 md:p-8">
            <div class="mb-6">
                <p class="text-sm text-[var(--color-text-secondary)]">
                    These default values are applied to new API keys created by users. Admins can override per-key limits from the individual token detail page.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.api-tokens.update-settings') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_per_minute" class="block text-sm font-medium text-[var(--color-text-primary)] mb-1">Requests Per Minute</label>
                        <input type="number" id="default_per_minute" name="default_per_minute" 
                               value="{{ old('default_per_minute', $defaultPerMinute) }}" min="1" required
                               class="block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        <p class="mt-2 text-xs text-[var(--color-text-muted)]">Changes apply immediately. Updates <code>API_RATE_LIMIT_PER_MINUTE</code>.</p>
                    </div>

                    <div>
                        <label for="default_per_day" class="block text-sm font-medium text-[var(--color-text-primary)] mb-1">Requests Per Day</label>
                        <input type="number" id="default_per_day" name="default_per_day" 
                               value="{{ old('default_per_day', $defaultPerDay) }}" min="1" required
                               class="block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        <p class="mt-2 text-xs text-[var(--color-text-muted)]">Changes apply immediately. Updates <code>API_RATE_LIMIT_PER_DAY</code>.</p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-[var(--color-border-subtle)]">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-[var(--color-brand-saffron)] py-2 px-6 text-sm font-semibold text-[#030817] shadow-sm hover:bg-[var(--color-brand-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--color-brand-saffron)] focus:ring-offset-2 transition">
                        Save Global Limits
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
