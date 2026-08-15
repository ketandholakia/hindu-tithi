<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-base)]/80 backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Logo / Brand --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('hindutithi.home') }}" class="flex items-center gap-2.5 group">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] text-sm font-bold ring-1 ring-[var(--color-brand-saffron)]/20 group-hover:bg-[var(--color-brand-saffron)]/20 transition">☀</span>
                    <span class="text-base font-semibold text-[var(--color-text-primary)] tracking-wide">Vittix Panchang</span>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden sm:flex sm:items-center sm:gap-6 ml-4">
                    <a href="{{ route('hindutithi.home') }}" class="text-sm font-medium transition {{ request()->routeIs('hindutithi.home') ? 'text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]' }}">Product</a>
                    <a href="{{ route('hindutithi.day') }}" class="text-sm font-medium transition {{ request()->routeIs('hindutithi.day') ? 'text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]' }}">Panchang</a>
                    <a href="{{ route('hindutithi.astrology') }}" class="text-sm font-medium transition {{ request()->routeIs('hindutithi.astrology') ? 'text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]' }}">Astrology</a>
                    <a href="{{ route('hindutithi.festivals') }}" class="text-sm font-medium transition {{ request()->routeIs('hindutithi.festivals') ? 'text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]' }}">Festivals</a>
                    <a href="{{ route('api.docs') }}" class="text-sm font-medium transition {{ request()->routeIs('api.docs') ? 'text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]' }}">API Docs</a>
                    <a href="{{ route('hindutithi.accuracy') }}" class="text-sm font-medium transition {{ request()->routeIs('hindutithi.accuracy') ? 'text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]' }}">Accuracy</a>
                </div>
            </div>

            {{-- Right side: Auth & Actions --}}
            <div class="hidden sm:flex sm:items-center sm:gap-5">
                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">GitHub</a>
                
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] px-3 py-1.5 text-sm font-medium text-[var(--color-text-secondary)] transition hover:bg-[var(--color-bg-elevated)] hover:text-[var(--color-text-primary)]">
                                <span class="h-5 w-5 flex items-center justify-center rounded-full bg-[var(--color-brand-saffron)]/20 text-[var(--color-brand-saffron)] text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                {{ Auth::user()->name }}
                                <svg class="h-3.5 w-3.5 text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('admin.dashboard')">{{ __('Admin Dashboard') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('api.keys.index')">{{ __('API Keys') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Login</a>
                    <a href="{{ route('hindutithi.home') }}#get-started" class="rounded-full bg-[var(--color-brand-saffron)] px-4 py-1.5 text-sm font-semibold text-[#030817] transition hover:bg-[var(--color-brand-gold)]">Get Started</a>
                @endauth
            </div>

            {{-- Hamburger (mobile) --}}
            <div class="flex sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-bg-surface)] hover:text-[var(--color-text-primary)] transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path :class="{'hidden': open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open}" class="hidden"   stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden border-t border-[var(--color-border-subtle)] sm:hidden bg-[var(--color-bg-surface)]">
        <div class="space-y-1 px-4 py-3">
            @foreach([
                ['hindutithi.home',     'Product'],
                ['hindutithi.day',      'Panchang'],
                ['hindutithi.astrology','Astrology'],
                ['hindutithi.festivals','Festivals'],
                ['api.docs',            'API Docs'],
                ['hindutithi.accuracy', 'Accuracy'],
            ] as [$route, $label])
                <a href="{{ route($route) }}"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs($route)
                               ? 'bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)]'
                               : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]' }}">
                    {{ $label }}
                </a>
            @endforeach
            <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="block rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">GitHub</a>
        </div>
        <div class="border-t border-[var(--color-border-subtle)] px-4 py-3">
            @auth
                <div class="mb-2 text-sm font-medium text-[var(--color-text-primary)]">{{ Auth::user()->name }}</div>
                <div class="mb-3 text-xs text-[var(--color-text-muted)]">{{ Auth::user()->email }}</div>
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">Admin Dashboard</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">Profile</a>
                <a href="{{ route('api.keys.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">API Keys</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">Log Out</button>
                </form>
            @else
                <div class="flex flex-col gap-2 mt-2">
                    <a href="{{ route('login') }}" class="block text-center rounded-lg px-3 py-2 text-sm font-medium border border-[var(--color-border-subtle)] text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">Login</a>
                    <a href="{{ route('hindutithi.home') }}#get-started" class="block text-center rounded-lg px-3 py-2 text-sm font-semibold bg-[var(--color-brand-saffron)] text-[#030817] hover:bg-[var(--color-brand-gold)]">Get Started</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
