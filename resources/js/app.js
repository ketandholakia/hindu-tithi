

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-place-search]').forEach((widget) => {
        const query = widget.querySelector('[data-place-query]');
        const status = widget.querySelector('[data-place-status]');
        const results = widget.querySelector('[data-place-results]');
        const search = async () => {
            const term = query.value.trim();
            if (term.length < 2) { status.textContent = 'Enter at least two characters.'; return; }
            status.textContent = 'Searching…';
            results.replaceChildren();
            try {
                const url = new URL(widget.dataset.searchUrl, window.location.origin);
                url.searchParams.set('q', term);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Search is temporarily unavailable.');
                const { results: places } = await response.json();
                status.textContent = places.length ? 'Choose the matching place:' : 'No matches found. Try a nearby city or include the country.';
                places.forEach((place) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'rounded-lg border border-white/10 bg-slate-800/70 px-3 py-2 text-left text-xs text-slate-200 hover:border-amber-400/40';
                    button.textContent = place.name;
                    button.addEventListener('click', () => {
                        const form = widget.closest('form') ?? widget.parentElement?.querySelector('form');
                        const fields = { name: place.name, latitude: place.latitude, longitude: place.longitude, lat: place.latitude, lon: place.longitude };
                        Object.entries(fields).forEach(([name, value]) => {
                            const input = form?.querySelector(`[name="${name}"]`);
                            if (input) input.value = value;
                        });
                        status.textContent = 'Place selected. Set its timezone if needed.';
                    });
                    results.append(button);
                });
            } catch (error) { status.textContent = error.message || 'Could not search places right now.'; }
        };
        widget.querySelector('[data-place-submit]').addEventListener('click', search);
        query.addEventListener('keydown', (event) => { if (event.key === 'Enter') { event.preventDefault(); search(); } });
    });

    document.querySelectorAll('[data-saved-location]').forEach((select) => {
        select.addEventListener('change', () => {
            const option = select.selectedOptions[0];
            if (!option?.value) return;
            [['lat', 'lat'], ['lon', 'lon'], ['elev', 'elev'], ['tz', 'tz']].forEach(([field, data]) => {
                const input = select.closest('form')?.querySelector(`[name="${field}"]`);
                if (input) input.value = option.dataset[data];
            });
            select.closest('form')?.requestSubmit();
        });
    });
});
