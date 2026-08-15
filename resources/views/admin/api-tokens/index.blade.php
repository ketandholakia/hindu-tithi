@extends('layouts.app')

@section('title', 'API Key Management - Admin')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        {{ __('API Key Management') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text-primary)]">API Keys</h3>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">Manage all API keys across users. View usage, revoke keys, and adjust rate limits.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] px-4 py-2 text-sm font-medium text-[var(--color-text-secondary)] transition hover:bg-[var(--color-bg-elevated)] hover:text-[var(--color-text-primary)]">
                Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters Card -->
        <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl border border-[var(--color-border-subtle)] p-6 mb-8">
            <h5 class="text-lg font-medium text-[var(--color-text-primary)] mb-4">Filters</h5>
            
            <form method="GET" action="{{ route('admin.api-tokens.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text-primary)] mb-1" for="user_id">User</label>
                    <select id="user_id" name="user_id" class="block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[var(--color-text-primary)] mb-1" for="status">Status</label>
                    <select id="status" name="status" class="block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] py-2 px-4 text-sm font-semibold text-[var(--color-text-primary)] shadow-sm hover:bg-[var(--color-bg-surface)] hover:text-[var(--color-brand-saffron)] focus:outline-none focus:ring-2 focus:ring-[var(--color-brand-saffron)] transition">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Tokens Table -->
        <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)]">
            <div class="px-6 py-4 border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] flex justify-between items-center">
                <h5 class="font-medium text-[var(--color-text-primary)]">API Tokens ({{ $tokens->total() }})</h5>
            </div>
            
            <div class="overflow-x-auto">
                @if ($tokens->isEmpty())
                    <div class="p-6 text-center text-[var(--color-text-muted)]">
                        No API keys found.
                    </div>
                @else
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-[var(--color-bg-elevated)] text-[var(--color-text-muted)]">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">User</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Name</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Status</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Last Used</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Usage Today</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)] text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-border-subtle)] text-[var(--color-text-primary)]">
                            @foreach ($tokens as $token)
                                <tr class="hover:bg-[var(--color-bg-elevated)]/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $token->user->name }}</div>
                                        <div class="text-xs text-[var(--color-text-muted)]">{{ $token->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium">
                                        {{ $token->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($token->revoked_at)
                                            <span class="inline-flex items-center rounded-full bg-gray-500/10 px-2 py-0.5 text-xs font-medium text-gray-400 ring-1 ring-inset ring-gray-500/20">Revoked</span>
                                        @elseif ($token->expires_at && $token->expires_at->isPast())
                                            <span class="inline-flex items-center rounded-full bg-yellow-500/10 px-2 py-0.5 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-500/20">Expired</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                        @if ($token->last_used_at)
                                            {{ $token->last_used_at->format('M j, Y H:i') }}
                                        @else
                                            <span class="text-[var(--color-text-muted)]">Never</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                        {{ $token->getUsageToday() }} requests
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.api-tokens.show', $token) }}" class="text-[var(--color-brand-saffron)] hover:text-[var(--color-brand-gold)] font-medium transition">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            
            @if($tokens->hasPages())
                <div class="border-t border-[var(--color-border-subtle)] px-6 py-4 bg-[var(--color-bg-elevated)]">
                    {{ $tokens->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
