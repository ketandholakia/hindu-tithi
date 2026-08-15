@props(['month', 'year'])

@php
    $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
    $prevDate = $currentDate->copy()->subMonth();
    $nextDate = $currentDate->copy()->addMonth();
    
    // Quick array of months for desktop list
    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];
@endphp

<div class="mb-10 text-[var(--color-text-primary)]">
    <div class="text-center mb-6">
        <h2 class="text-4xl font-serif text-[var(--color-brand-saffron)]">Festivals & Parva</h2>
        <p class="text-lg text-[var(--color-text-muted)] mt-2 font-medium">Explore Hindu festivals according to Panchang</p>
    </div>

    <!-- Desktop: Horizontal month list -->
    <div class="hidden sm:flex flex-col items-center justify-center">
        <div class="text-2xl font-bold mb-4">{{ $year }}</div>
        <div class="flex items-center space-x-2">
            @foreach($months as $m => $mName)
                @if($m === $month)
                    <span class="px-4 py-2 rounded bg-[var(--color-brand-saffron)] text-[#030817] font-semibold shadow-md">
                        {{ $mName }}
                    </span>
                @else
                    <a href="?month={{ $m }}&year={{ $year }}" class="px-4 py-2 rounded hover:bg-[var(--color-bg-surface)] font-medium transition text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">
                        {{ $mName }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Mobile: Arrows -->
    <div class="flex sm:hidden items-center justify-between bg-[var(--color-bg-surface)] shadow-sm rounded-lg p-4 border border-[var(--color-border-subtle)]">
        <a href="?month={{ $prevDate->month }}&year={{ $prevDate->year }}" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-brand-saffron)]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="text-lg font-bold">
            {{ $currentDate->format('F Y') }}
        </div>
        <a href="?month={{ $nextDate->month }}&year={{ $nextDate->year }}" class="p-2 text-[var(--color-text-muted)] hover:text-[var(--color-brand-saffron)]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <!-- Configuration Selectors (Mocked for visual, could be wired to query params) -->
    <div class="flex flex-wrap items-center justify-center gap-4 mt-8">
        <div class="text-sm font-semibold px-3 py-1 bg-[var(--color-bg-surface)] text-[var(--color-text-secondary)] border border-[var(--color-border-subtle)] rounded shadow-sm">
            Amanta / Purnimanta
        </div>
        <select class="text-sm font-semibold px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] border border-[var(--color-border-subtle)] rounded shadow-sm focus:ring-[var(--color-brand-saffron)]">
            <option>Ahmedabad</option>
            <option>Delhi</option>
            <option>Mumbai</option>
        </select>
        <select class="text-sm font-semibold px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] border border-[var(--color-border-subtle)] rounded shadow-sm focus:ring-[var(--color-brand-saffron)]">
            <option>Gujarati</option>
            <option>English</option>
            <option>Hindi</option>
        </select>
    </div>
</div>
