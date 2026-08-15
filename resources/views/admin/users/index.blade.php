@extends('layouts.app')

@section('title', 'User Management - Admin')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        {{ __('User Management') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text-primary)]">Registered Users</h3>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">View and manage users registered in the application.</p>
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
        
        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[var(--color-bg-elevated)] border-b border-[var(--color-border-subtle)] text-[var(--color-text-muted)]">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider">Joined</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border-subtle)] text-[var(--color-text-primary)]">
                        @forelse ($users as $user)
                            <tr class="hover:bg-[var(--color-bg-elevated)]/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] text-xs font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                        <span class="font-medium">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                    {{ $user->email }}
                                    @if($user->hasVerifiedEmail())
                                        <span class="ml-2 inline-flex items-center rounded-full bg-green-500/10 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-500/20">Verified</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                    {{ $user->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-[var(--color-brand-saffron)] hover:text-[var(--color-brand-gold)] font-medium transition">
                                            Edit
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user? Their API tokens will also be permanently deleted.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 font-medium transition">
                                                Delete
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-[var(--color-text-muted)]">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="border-t border-[var(--color-border-subtle)] px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
