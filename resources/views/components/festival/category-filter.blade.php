@props(['categories', 'active'])

<div class="mb-8">
    <div class="flex overflow-x-auto pb-4 scrollbar-hide space-x-2 sm:space-x-3 items-center" style="scrollbar-width: none; -ms-overflow-style: none;">
        @foreach($categories as $category)
            @if($category === $active)
                <span class="whitespace-nowrap px-4 py-2 rounded-full bg-[var(--color-brand-saffron)] text-[#030817] font-bold shadow-md text-sm border border-[var(--color-brand-saffron)]">
                    {{ $category }}
                </span>
            @else
                <a href="?category={{ urlencode($category) }}{{ request('month') ? '&month='.request('month') : '' }}{{ request('year') ? '&year='.request('year') : '' }}" class="whitespace-nowrap px-4 py-2 rounded-full bg-[var(--color-bg-surface)] text-[var(--color-text-secondary)] font-medium text-sm shadow-sm border border-[var(--color-border-subtle)] hover:border-[var(--color-brand-saffron)] hover:text-[var(--color-text-primary)] transition">
                    {{ $category }}
                </a>
            @endif
        @endforeach
    </div>
</div>
