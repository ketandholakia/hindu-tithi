<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/90 backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- Logo / Brand --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('hindutithi.home') }}" class="flex items-center gap-2.5 group">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/20 text-amber-400 text-lg font-bold ring-1 ring-amber-400/30 group-hover:bg-amber-500/30 transition">☀</span>
                    <span class="text-base font-semibold text-white">Hindutithi</span>
                    <span class="hidden sm:inline text-xs text-slate-500 font-normal">Panchang Demo</span>
                </a>
            </div>

            {{-- Desktop Nav Links --}}
            <div class="hidden sm:flex sm:items-center sm:gap-1">
                @foreach([
                    ['hindutithi.home',     'Home'],
                    ['hindutithi.day',      'Day'],
                    ['hindutithi.moment',   'Moment'],
                    ['hindutithi.calendar', 'Calendar'],
                    ['hindutithi.festivals','Festivals'],
                    ['hindutithi.astrology','Astrology'],
                    ['hindutithi.kundli','Kundli'],
                    ['hindutithi.accuracy','Accuracy'],
                    ['hindutithi.help',     'Help'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}"
                       class="rounded-lg px-3 py-1.5 text-sm transition
                              {{ request()->routeIs($route)
                                   ? 'bg-white/10 text-white font-medium'
                                   : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach

                <span class="mx-1 h-5 w-px bg-white/10"></span>

                <a href="{{ route('api.docs') }}"
                   class="rounded-lg px-3 py-1.5 text-sm text-sky-400 hover:text-sky-300 hover:bg-sky-400/10 transition {{ request()->routeIs('api.docs') ? 'bg-sky-400/10' : '' }}">
                    API Docs
                </a>
            </div>

            {{-- Right side: Auth --}}
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-300 transition hover:bg-white/10 hover:text-white">
                                <span class="h-5 w-5 flex items-center justify-center rounded-full bg-amber-500/20 text-amber-400 text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                {{ Auth::user()->name }}
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
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
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-1.5 text-sm text-slate-400 hover:text-white transition">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-full bg-amber-500 px-4 py-1.5 text-sm font-semibold text-slate-900 transition hover:bg-amber-400">Register</a>
                    @endif
                @endauth
            </div>

            {{-- Hamburger (mobile) --}}
            <div class="flex sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path :class="{'hidden': open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open}" class="hidden"   stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden border-t border-white/10 sm:hidden">
        <div class="space-y-1 px-4 py-3">
            @foreach([
                ['hindutithi.home',     'Home'],
                ['hindutithi.day',      'Day'],
                ['hindutithi.moment',   'Moment'],
                ['hindutithi.calendar', 'Calendar'],
                ['hindutithi.festivals','Festivals'],
                ['hindutithi.astrology','Astrology'],
                ['hindutithi.kundli','Kundli'],
                ['hindutithi.accuracy','Accuracy'],
                ['hindutithi.help',     'Help'],
                ['api.docs',            'API Docs'],
            ] as [$route, $label])
                <a href="{{ route($route) }}"
                   class="block rounded-lg px-3 py-2 text-sm transition
                          {{ request()->routeIs($route)
                               ? 'bg-white/10 text-white font-medium'
                               : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <div class="border-t border-white/10 px-4 py-3">
            @auth
                <div class="mb-2 text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                <div class="mb-3 text-xs text-slate-500">{{ Auth::user()->email }}</div>
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-400 hover:text-white hover:bg-white/5">Profile</a>
                <a href="{{ route('api.keys.index') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-400 hover:text-white hover:bg-white/5">API Keys</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left rounded-lg px-3 py-2 text-sm text-slate-400 hover:text-white hover:bg-white/5">Log Out</button>
                </form>
            @else
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-medium text-amber-400 hover:text-amber-300">Register</a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
