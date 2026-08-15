@extends('layouts.app')

@section('title', 'Admin Dashboard - Hindutithi')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        {{ __('Admin Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h3 class="text-lg font-medium text-[var(--color-text-primary)]">Application Settings</h3>
            <p class="mt-1 text-sm text-[var(--color-text-muted)]">Manage core configurations and administrative tools for Hindutithi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- API Token Management -->
            <a href="{{ route('admin.api-tokens.index') }}" class="block card-glass bg-[var(--color-bg-surface)] p-6 rounded-2xl hover:border-[var(--color-brand-saffron)] transition">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-[var(--color-brand-saffron)]/10 flex items-center justify-center text-[var(--color-brand-saffron)] text-2xl mb-4">
                        🔑
                    </div>
                </div>
                <h4 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">API Tokens</h4>
                <p class="text-sm text-[var(--color-text-muted)]">View, revoke, and manage usage limits for user API tokens.</p>
            </a>

            <!-- API Rate Limits Settings -->
            <a href="{{ route('admin.api-tokens.settings') }}" class="block card-glass bg-[var(--color-bg-surface)] p-6 rounded-2xl hover:border-[var(--color-brand-saffron)] transition">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-[var(--color-brand-saffron)]/10 flex items-center justify-center text-[var(--color-brand-saffron)] text-2xl mb-4">
                        ⚙️
                    </div>
                </div>
                <h4 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">API Global Settings</h4>
                <p class="text-sm text-[var(--color-text-muted)]">Configure default API rate limits and environment rules.</p>
            </a>
            
            <!-- User Management -->
            <a href="{{ route('admin.users.index') }}" class="block card-glass bg-[var(--color-bg-surface)] p-6 rounded-2xl hover:border-[var(--color-brand-saffron)] transition">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-[var(--color-brand-saffron)]/10 flex items-center justify-center text-[var(--color-brand-saffron)] text-2xl mb-4">
                        👥
                    </div>
                </div>
                <h4 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">User Management</h4>
                <p class="text-sm text-[var(--color-text-muted)]">View and manage registered users and their details.</p>
            </a>
            
            <!-- Telegram Bot Config -->
            <a href="{{ route('admin.telegram.index') }}" class="block card-glass bg-[var(--color-bg-surface)] p-6 rounded-2xl hover:border-[var(--color-brand-saffron)] transition">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-[#0088cc]/10 flex items-center justify-center text-[#0088cc] text-2xl mb-4">
                        🤖
                    </div>
                </div>
                <h4 class="text-xl font-bold text-[var(--color-text-primary)] mb-2">Telegram Bot</h4>
                <p class="text-sm text-[var(--color-text-muted)]">Manage your Telegram bot webhook and configuration.</p>
            </a>

        </div>
    </div>
</div>
@endsection
