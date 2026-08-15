@extends('layouts.app')

@section('title', 'Token Details - Admin')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        {{ __('Token Details') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text-primary)]">{{ $token->name }}</h3>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">User: <strong class="text-[var(--color-text-primary)]">{{ $token->user->name }}</strong> ({{ $token->user->email }})</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Token Info Card -->
            <div class="lg:col-span-2 card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)]">
                <div class="px-6 py-4 border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)]">
                    <h5 class="font-medium text-[var(--color-text-primary)]">Token Information</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-1">Status</p>
                            <p>
                                @if ($token->revoked_at)
                                    <span class="inline-flex items-center rounded-full bg-gray-500/10 px-2 py-0.5 text-xs font-medium text-gray-400 ring-1 ring-inset ring-gray-500/20">Revoked</span>
                                    <span class="block mt-1 text-xs text-[var(--color-text-muted)]">{{ $token->revoked_at->format('Y-m-d H:i') }}</span>
                                @elseif ($token->expires_at && $token->expires_at->isPast())
                                    <span class="inline-flex items-center rounded-full bg-yellow-500/10 px-2 py-0.5 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-500/20">Expired</span>
                                    <span class="block mt-1 text-xs text-[var(--color-text-muted)]">{{ $token->expires_at->format('Y-m-d H:i') }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">Active</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-1">Created</p>
                            <p class="text-sm text-[var(--color-text-primary)]">{{ $token->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-1">Last Used</p>
                            <p class="text-sm text-[var(--color-text-primary)]">
                                @if ($token->last_used_at)
                                    {{ $token->last_used_at->format('Y-m-d H:i') }}
                                @else
                                    <span class="text-[var(--color-text-muted)]">Never</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-1">Expires</p>
                            <p class="text-sm text-[var(--color-text-primary)]">
                                @if ($token->expires_at)
                                    {{ $token->expires_at->format('Y-m-d H:i') }}
                                @else
                                    <span class="text-[var(--color-text-muted)]">No expiry</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-2">Scopes/Abilities</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($token->abilities ?? [] as $ability)
                                <span class="inline-flex items-center rounded-full bg-[var(--color-brand-saffron)]/10 px-2 py-0.5 text-xs font-medium text-[var(--color-brand-saffron)] ring-1 ring-inset ring-[var(--color-brand-saffron)]/20">{{ $ability }}</span>
                            @empty
                                <span class="text-sm text-[var(--color-text-muted)]">No scopes</span>
                            @endforelse
                        </div>
                    </div>

                    @if (!$token->revoked_at)
                        <div class="mt-8 pt-6 border-t border-[var(--color-border-subtle)]">
                            <form method="POST" action="{{ route('admin.api-tokens.revoke', $token) }}" 
                                  onsubmit="return confirm('Are you sure you want to revoke this API key? This action cannot be undone.');">
                                @csrf
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-500 hover:bg-red-500/20 transition">
                                    Revoke This Token
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Rate Limits Card -->
            <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)]">
                <div class="px-6 py-4 border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)]">
                    <h5 class="font-medium text-[var(--color-text-primary)]">Rate Limits</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-[var(--color-bg-elevated)] rounded-lg p-3 border border-[var(--color-border-subtle)]">
                            <p class="text-xs text-[var(--color-text-muted)]">Current Usage (Min)</p>
                            <p class="text-lg font-semibold text-[var(--color-text-primary)]">{{ $usageThisMinute }} <span class="text-sm text-[var(--color-text-muted)] font-normal">/ {{ $token->rate_limit_per_minute }}</span></p>
                        </div>
                        <div class="bg-[var(--color-bg-elevated)] rounded-lg p-3 border border-[var(--color-border-subtle)]">
                            <p class="text-xs text-[var(--color-text-muted)]">Current Usage (Day)</p>
                            <p class="text-lg font-semibold text-[var(--color-text-primary)]">{{ $usageToday }} <span class="text-sm text-[var(--color-text-muted)] font-normal">/ {{ $token->rate_limit_per_day }}</span></p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.api-tokens.update-limits', $token) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text-primary)] mb-1" for="rate_limit_per_minute">Requests Per Minute</label>
                            <input type="number" id="rate_limit_per_minute" name="rate_limit_per_minute" 
                                   value="{{ $token->rate_limit_per_minute }}" min="1" required
                                   class="block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-text-primary)] mb-1" for="rate_limit_per_day">Requests Per Day</label>
                            <input type="number" id="rate_limit_per_day" name="rate_limit_per_day" 
                                   value="{{ $token->rate_limit_per_day }}" min="1" required
                                   class="block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] py-2 px-4 text-sm font-semibold text-[var(--color-text-primary)] shadow-sm hover:bg-[var(--color-bg-surface)] hover:text-[var(--color-brand-saffron)] transition">
                                Update Limits
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Usage Logs Card -->
        <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)]">
            <div class="px-6 py-4 border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)]">
                <h5 class="font-medium text-[var(--color-text-primary)]">Usage Logs</h5>
            </div>
            
            <div class="overflow-x-auto">
                @if ($logs->isEmpty())
                    <div class="p-6 text-center text-[var(--color-text-muted)]">
                        No usage logs found.
                    </div>
                @else
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-[var(--color-bg-elevated)] text-[var(--color-text-muted)]">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Endpoint</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Method</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Status</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">Response Time</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)]">IP Address</th>
                                <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider border-b border-[var(--color-border-subtle)] text-right">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-border-subtle)] text-[var(--color-text-primary)]">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-[var(--color-bg-elevated)]/50 transition">
                                    <td class="px-6 py-4 font-mono text-xs">
                                        {{ $log->endpoint }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-xs">
                                        {{ $log->method }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($log->status_code >= 200 && $log->status_code < 300)
                                            <span class="inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">{{ $log->status_code }}</span>
                                        @elseif ($log->status_code >= 400 && $log->status_code < 500)
                                            <span class="inline-flex items-center rounded-full bg-yellow-500/10 px-2 py-0.5 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-500/20">{{ $log->status_code }}</span>
                                        @elseif ($log->status_code >= 500)
                                            <span class="inline-flex items-center rounded-full bg-red-500/10 px-2 py-0.5 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-500/20">{{ $log->status_code }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-500/10 px-2 py-0.5 text-xs font-medium text-gray-400 ring-1 ring-inset ring-gray-500/20">{{ $log->status_code }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-[var(--color-text-secondary)] text-xs">
                                        {{ $log->response_time_ms }} ms
                                    </td>
                                    <td class="px-6 py-4 text-[var(--color-text-secondary)] text-xs font-mono">
                                        {{ $log->ip_address }}
                                    </td>
                                    <td class="px-6 py-4 text-[var(--color-text-secondary)] text-xs text-right">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            
            @if($logs->hasPages())
                <div class="border-t border-[var(--color-border-subtle)] px-6 py-4 bg-[var(--color-bg-elevated)]">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
