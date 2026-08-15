@extends('layouts.app')

@section('title', 'Telegram Bot Configuration - Hindutithi Admin')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl leading-tight">
            <a href="{{ route('admin.dashboard') }}" class="text-[var(--color-brand-saffron)] hover:underline">Admin</a>
            <span class="text-[var(--color-text-muted)] mx-2">/</span>
            Telegram Bot
        </h2>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="card-glass bg-[var(--color-bg-surface)] p-6 rounded-2xl">
            <h3 class="text-lg font-bold text-[var(--color-text-primary)] mb-4">Bot Status</h3>
            
            <div class="mb-6">
                <p class="text-sm text-[var(--color-text-muted)] mb-1">Environment Token (TELEGRAM_BOT_TOKEN)</p>
                @if($isConfigured)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-400 border border-green-700">
                        Configured
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/50 text-red-400 border border-red-700">
                        Missing or Empty
                    </span>
                    <p class="mt-2 text-sm text-[var(--color-text-muted)]">Please add <code class="bg-[var(--color-bg-body)] px-1 rounded">TELEGRAM_BOT_TOKEN</code> to your <code class="bg-[var(--color-bg-body)] px-1 rounded">.env</code> file to enable Telegram features.</p>
                @endif
            </div>

            @if($isConfigured && $webhookInfo)
                <div class="mb-4">
                    <p class="text-sm text-[var(--color-text-muted)] mb-1">Current Webhook URL</p>
                    @if(!empty($webhookInfo['url']))
                        <code class="block w-full bg-[var(--color-bg-body)] text-[var(--color-brand-saffron)] p-3 rounded-lg text-sm border border-[var(--color-border)]">{{ $webhookInfo['url'] }}</code>
                    @else
                        <p class="text-sm text-[var(--color-text-primary)]"><em>Not set</em></p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-[var(--color-bg-body)] p-3 rounded-lg border border-[var(--color-border)]">
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider">Pending Updates</p>
                        <p class="text-lg font-semibold text-[var(--color-text-primary)]">{{ $webhookInfo['pending_update_count'] ?? 0 }}</p>
                    </div>
                    <div class="bg-[var(--color-bg-body)] p-3 rounded-lg border border-[var(--color-border)]">
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider">Last Error</p>
                        <p class="text-sm font-semibold text-[var(--color-text-primary)] truncate" title="{{ $webhookInfo['last_error_message'] ?? 'None' }}">
                            {{ $webhookInfo['last_error_message'] ?? 'None' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        @if($isConfigured)
            <div class="card-glass bg-[var(--color-bg-surface)] p-6 rounded-2xl">
                <h3 class="text-lg font-bold text-[var(--color-text-primary)] mb-4">Set Webhook</h3>
                <p class="text-sm text-[var(--color-text-muted)] mb-4">
                    Register the URL where Telegram should send incoming updates. Your server must be accessible via HTTPS.
                </p>

                <form method="POST" action="{{ route('admin.telegram.webhook.set') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="url" value="Webhook URL" />
                        <x-text-input id="url" name="url" type="url" class="mt-1 block w-full bg-[var(--color-bg-body)] border-[var(--color-border)] text-[var(--color-text-primary)]" value="{{ old('url', url('/api/telegram/webhook')) }}" required />
                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                    </div>

                    <div class="flex items-center space-x-4">
                        <x-primary-button class="bg-[var(--color-brand-saffron)] text-black hover:bg-orange-500">
                            {{ __('Update Webhook') }}
                        </x-primary-button>
                    </div>
                </form>

                @if(!empty($webhookInfo['url']))
                    <div class="mt-8 pt-6 border-t border-[var(--color-border)]">
                        <h4 class="text-md font-bold text-red-500 mb-2">Remove Webhook</h4>
                        <p class="text-sm text-[var(--color-text-muted)] mb-4">
                            Removing the webhook will stop Telegram from pushing updates to your server.
                        </p>
                        <form method="POST" action="{{ route('admin.telegram.webhook.delete') }}" onsubmit="return confirm('Are you sure you want to remove the webhook?');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>
                                {{ __('Remove Webhook') }}
                            </x-danger-button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
