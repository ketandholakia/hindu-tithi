<div data-place-search data-search-url="{{ route('locations.search') }}" class="space-y-2">
    <label class="block text-xs font-medium text-slate-400">Search by city or place</label>
    <div class="flex gap-2">
        <input data-place-query type="search" autocomplete="off" placeholder="e.g. Ahmedabad, India" class="min-w-0 flex-1 rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-amber-500/50 focus:outline-none">
        <button data-place-submit type="button" class="rounded-lg border border-amber-500/30 px-4 py-2 text-sm font-medium text-amber-300 hover:bg-amber-500/10">Search</button>
    </div>
    <div data-place-status aria-live="polite" class="text-xs text-slate-400"></div>
    <div data-place-results class="grid gap-2"></div>
</div>
