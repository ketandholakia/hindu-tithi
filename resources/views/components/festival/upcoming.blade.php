@props(['upcoming'])

@if(count($upcoming) > 0)
<div class="mb-12">
    <h3 class="text-xl font-bold text-[var(--color-brand-saffron)] mb-4">Upcoming Festivals</h3>
    <div class="flex overflow-x-auto pb-6 space-x-4 scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
        @foreach($upcoming as $item)
            <div class="min-w-[140px] w-[140px] bg-[var(--color-bg-surface)] rounded-xl p-4 shadow-sm border border-[var(--color-border-subtle)] flex-shrink-0 flex flex-col hover:border-[var(--color-brand-saffron)] transition cursor-pointer group card-glass">
                <div class="text-center border-b border-[var(--color-border-subtle)] pb-3 mb-3">
                    <div class="text-3xl font-bold text-[var(--color-text-primary)] group-hover:text-[var(--color-brand-saffron)] transition">{{ $item['date']->format('d') }}</div>
                    <div class="text-xs font-bold tracking-widest uppercase text-[var(--color-text-muted)]">{{ $item['date']->format('M') }}</div>
                </div>
                <div class="text-center flex-grow flex flex-col justify-center">
                    <div class="font-bold text-sm text-[var(--color-text-secondary)] leading-tight line-clamp-2">
                        {{ $item['festival']['name'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
