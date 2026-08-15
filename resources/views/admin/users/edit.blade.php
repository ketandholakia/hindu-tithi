@extends('layouts.app')

@section('title', 'Edit User - Admin')

@section('header')
    <h2 class="font-semibold text-xl leading-tight">
        {{ __('Edit User') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-[var(--color-text-primary)]">Edit User: {{ $user->name }}</h3>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">Update user details and contact information.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] px-4 py-2 text-sm font-medium text-[var(--color-text-secondary)] transition hover:bg-[var(--color-bg-elevated)] hover:text-[var(--color-text-primary)]">
                Back to Users
            </a>
        </div>

        <div class="card-glass bg-[var(--color-bg-surface)] rounded-2xl overflow-hidden border border-[var(--color-border-subtle)] p-6 md:p-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--color-text-primary)]">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                            class="mt-1 block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        @error('name')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--color-text-primary)]">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                            class="mt-1 block w-full rounded-md border-[var(--color-border-subtle)] bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] shadow-sm focus:border-[var(--color-brand-saffron)] focus:ring focus:ring-[var(--color-brand-saffron)] focus:ring-opacity-50">
                        @error('email')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        @if($user->hasVerifiedEmail())
                            <p class="mt-2 text-xs text-green-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Email Verified
                            </p>
                        @else
                            <p class="mt-2 text-xs text-yellow-500">Email not verified.</p>
                        @endif
                    </div>
                    
                    <div class="pt-4 border-t border-[var(--color-border-subtle)]">
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-[var(--color-brand-saffron)] py-2 px-4 text-sm font-semibold text-[#030817] shadow-sm hover:bg-[var(--color-brand-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--color-brand-saffron)] focus:ring-offset-2 transition">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
