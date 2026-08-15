@props(['festival', 'date', 'weekday', 'dayData'])

<div class="bg-[var(--color-bg-surface)] rounded-2xl p-6 shadow-sm border border-[var(--color-border-subtle)] flex flex-col h-full hover:border-[var(--color-brand-saffron)] transition card-glass">
    <!-- Header: Date and Icon -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-full bg-[var(--color-brand-saffron)]/10 flex items-center justify-center text-[var(--color-brand-saffron)] text-2xl">
                <!-- Fallback emoji since we don't have the icon font ready -->
                @if(str_contains(strtolower($festival['name']), 'janmashtami')) 🦚
                @elseif(str_contains(strtolower($festival['name']), 'diwali') || str_contains(strtolower($festival['name']), 'deepavali')) 🪔
                @elseif(str_contains(strtolower($festival['name']), 'shiv')) 🔱
                @elseif(str_contains(strtolower($festival['name']), 'ganesh')) 🐘
                @elseif(str_contains(strtolower($festival['name']), 'raksha')) 🧵
                @elseif(str_contains(strtolower($festival['name']), 'ekadashi')) 🌙
                @elseif(str_contains(strtolower($festival['name']), 'purnima')) 🌕
                @elseif(str_contains(strtolower($festival['name']), 'amavasya')) 🌑
                @else ☀
                @endif
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs font-bold tracking-widest uppercase text-[var(--color-text-muted)]">{{ $date->format('M d') }}</div>
            <div class="text-sm font-semibold text-[var(--color-text-secondary)]">{{ $weekday }}</div>
        </div>
    </div>

    <!-- Title -->
    <div class="mb-4">
        <h3 class="text-xl font-bold text-[var(--color-brand-saffron)] leading-tight mb-1">{{ $festival['name'] }}</h3>
        <!-- You can inject regional translations here in the future -->
        <!-- <div class="text-sm text-[var(--color-text-muted)] font-medium">શ્રી કૃષ્ણ જન્માષ્ટમી</div> -->
    </div>

    <!-- Panchang Context -->
    <div class="mb-6 space-y-1">
        <div class="text-sm font-medium text-[var(--color-text-primary)]">
            {{ $dayData['lunarMonth'] ?? '' }} {{ $dayData['paksha'] ?? '' }} {{ $dayData['tithi'] ?? '' }}
        </div>
        <div class="text-sm text-[var(--color-text-muted)]">
            {{ $dayData['nakshatra'] ?? '' }} Nakshatra
        </div>
    </div>

    <hr class="border-t border-[var(--color-border-subtle)] my-4">

    <!-- Timings (if available) -->
    <div class="flex-grow">
        @if(!empty($festival['timings']))
            <div class="space-y-3">
                @foreach($festival['timings'] as $timingName => $timingValue)
                    <div>
                        <div class="text-xs font-bold text-[var(--color-text-muted)] uppercase tracking-wide">{{ $timingName }}</div>
                        <div class="text-sm font-medium text-[var(--color-text-secondary)]">{{ $timingValue }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-sm italic text-[var(--color-text-muted)]">All day observance</div>
        @endif
    </div>

    <!-- Action -->
    <div class="mt-6 pt-4 border-t border-[var(--color-border-subtle)] text-right">
        <a href="#" class="inline-flex items-center text-sm font-bold text-[var(--color-brand-saffron)] hover:text-[var(--color-brand-gold)] transition group">
            View details 
            <svg class="ml-1 w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    </div>
</div>
