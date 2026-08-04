# Vittix Vedic Panchang

> Professional Vedic Panchang & Astrology engine for PHP, Laravel and REST APIs.

Vittix Vedic Panchang provides accurate Panchang (tithi, nakshatra, yoga, muhurta), festival calendars, and a full Vedic astrology (Kundli) module with planetary positions, houses, and chart generation. This repository also includes the HinduTithi demo site that showcases the package and REST API.

## Features

- Panchang
  - Daily Panchang, Tithi, Nakshatra, Yoga, Karana, Vara
  - Sunrise / Sunset, Moonrise / Moonset
  - Muhurta windows, Festival calendar, Sankranti, Ekadashi
- Astrology
  - Kundli / Birth chart generation
  - Planet positions and longitudes
  - Ascendant (Lagna) and house calculations
  - Rashi, Nakshatra, basic yogas
- Platform & APIs
  - PHP library and Laravel package
  - REST JSON APIs for Panchang and Astrology
  - Timezone-aware calculations and DST support

## Installation

Install via Composer:

```bash
composer require vittix/panchang
```

Quick start (Panchang):

```php
use Vittix\Panchang\Panchang;

$panchang = Panchang::today('Mumbai');
echo $panchang->tithi->name;
echo $panchang->nakshatra->name;
echo $panchang->sunrise;
```

Quick start (Kundli):

```php
use Vittix\Panchang\Kundli;

$kundli = Kundli::fromBirthDetails([
    'date' => '1990-01-01',
    'time' => '10:30',
    'lat' => 19.0760,
    'lon' => 72.8777,
    'tz' => 'Asia/Kolkata',
]);

echo $kundli->ascendant->name;
echo $kundli->planets->get('sun')->longitude;
```

## API Examples

GET /api/day — returns JSON Panchang for a date & location.

GET /api/kundli?sdate=1990-01-01&time=10:30&lat=19.076&lon=72.8777&tz=Asia/Kolkata — returns Kundli JSON.

Example JSON (Panchang):

```json
{
  "tithi": "Ekadashi",
  "nakshatra": "Rohini",
  "sunrise": "06:08",
  "sunset": "18:54"
}
```

## Documentation

See the documentation site or `/api/docs` for full API reference, installation notes, accuracy details, and examples.

## Roadmap

- v2.0 — Kundli & Astrology module (this release)
- v2.1 — Dasha/vimshottari, Transit (Gochar) APIs, additional yogas
- v2.2 — Multi-language support, SVG charts, PDF reports

## Contributing

Please read `CONTRIBUTING.md` (if present) and open issues or pull requests on GitHub. Follow coding standards and include tests for new features.

## License

MIT — see `LICENSE` in the repository.

## Changelog

See `CHANGELOG.md` for release history and migration notes.

# Hindutithi Panchang Demo

A Laravel application that demonstrates the features of the [`vittix/panchang`](https://packagist.org/packages/vittix/panchang) PHP package — including daily Panchang, Hindu calendar, Muhurta, Kundali, Vimshottari Dasha, Yogas, Festivals, and a JSON REST API.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3 · Laravel 13 |
| Astrology engine | `vittix/panchang ^1.1` |
| Auth scaffolding | Laravel Breeze |
| Frontend styling | Tailwind CSS **v4** (via `@tailwindcss/vite`) |
| JS interactivity | Alpine.js v3 |
| Build tool | Vite 8 |
| Database | SQLite (default) |

---

## Requirements

- PHP **≥ 8.3** with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- Composer
- Node.js ≥ 18 & npm

---

## Installation

### 1 — Clone and install

```bash
git clone <repo-url> hindutithi-app
cd hindutithi-app
```

### 2 — One-command setup (recommended)

```bash
composer setup
```

This single command runs in sequence:

1. `composer install`
2. Copies `.env.example` → `.env` (if absent)
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install --ignore-scripts`
6. `npm run build`

### 3 — Manual setup (alternative)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

---

## Running Locally

```bash
composer dev
```

This starts all four processes concurrently:

| Process | Command |
|---|---|
| HTTP server | `php artisan serve` |
| Queue worker | `php artisan queue:listen` |
| Log viewer | `php artisan pail` |
| Vite dev server | `npm run dev` |

Open **http://localhost:8000** in your browser.

---

## Application Pages

All pages share a persistent **session form** at the top that lets you change the date, time, timezone, and geo-location. Values are stored in the session and reused across all views.

| Route | Page | Description |
|---|---|---|
| `/` | Home | Redirects to `/home` |
| `/home` | Dashboard | Overview and quick-links to all sections |
| `/day` | Daily Panchang | Tithi, Nakshatra, Yoga, Karana, Vara, solar events for a date |
| `/moment` | Moment Panchang | Exact planetary positions at a specific date + time |
| `/calendar` | Hindu Calendar | Amanta/Purnimanta months, Vikram/Shaka Samvat, moon phases |
| `/muhurta` | Muhurta | Daytime muhurtas derived from sunrise and sunset |
| `/janmarashi` | Janmarashi | Moon-sign (Rashi) at a given moment |
| `/kundali` | Kundali | Whole-sign house chart with planetary placements |
| `/varga` | Varga | Divisional charts (D9 etc.) — pass `?varga=D9` |
| `/vimshottari` | Vimshottari Dasha | Major and sub-dasha periods |
| `/shadbala` | Shadbala | Six-fold planetary strength |
| `/yogas` | Yogas | Classical yoga detection |
| `/festivals` | Festivals | Upcoming festivals for the next 120 days |
| `/electional` | Electional | Available electional astrology checks |
| `/help` | Help | Usage notes and tips |

> **Note:** Pages that require features not present in the installed package version will show an "unavailable" notice rather than throwing an error.

---

## Session Parameters

The form on every page accepts:

| Field | Default | Description |
|---|---|---|
| `date` | today | Civil date for calculations (`YYYY-MM-DD`) |
| `time` | `06:00` | Used by moment-based views (`/moment`, `/janmarashi`, `/kundali`, etc.) |
| `tz` | `Asia/Kolkata` | PHP timezone identifier |
| `lat` | `23.0225` | Latitude in decimal degrees |
| `lon` | `72.5714` | Longitude in decimal degrees |
| `elev` | `0` | Elevation above sea level in metres |

Quick-select city presets (New Delhi, Mumbai, Bengaluru, Kolkata, Chennai) are provided for convenience.

---

## REST API

The API is protected by the `panchang.api.key` middleware. When API key protection is enabled, include your key in the request header:

```
X-API-KEY: <your-key>
```

### Endpoints

| Method | Path | Query params | Description |
|---|---|---|---|
| `GET` | `/api` | — | Lists all endpoints |
| `GET` | `/api/examples` | `date, time, tz, lat, lon, elev` | Returns ready-to-copy example URLs for current inputs |
| `GET` | `/api/day` | `date, tz, lat, lon, elev` | Daily Panchang summary |
| `GET` | `/api/moment` | `date, time, tz, lat, lon, elev` | Panchang values at an exact instant |
| `GET` | `/api/calendar` | `date, tz` | Hindu calendar summary |
| `GET` | `/api/muhurta` | `date, tz, lat, lon, elev` | Day muhurtas between sunrise and sunset |
| `GET` | `/api/electional` | — | Available electional evaluator checks |

All successful responses are JSON. Example request:

```bash
curl "http://localhost:8000/api/day?date=2026-08-03&tz=Asia/Kolkata&lat=23.0225&lon=72.5714" \
  -H "X-API-KEY: your-key-here"
```

The full OpenAPI 3.1 specification is at [`openapi.yaml`](./openapi.yaml) and is viewable in-app at `/api/docs`.

### API Keys

Authenticated users can generate and revoke personal API keys at `/api-keys`. The full key value is shown **only once** on creation — store it securely.

---

## Frontend

### Tailwind CSS v4

This project uses **Tailwind CSS v4** configured via the Vite plugin — there is no `tailwind.config.js`. All theme customisation lives in [`resources/css/app.css`](./resources/css/app.css) inside an `@theme` block:

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    /* custom color tokens, radii, etc. */
}
```

### Vite configuration

```js
// vite.config.js
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [laravel({ ... }), tailwindcss()],
});
```

`postcss.config.js` is intentionally minimal — Tailwind v4 no longer requires a PostCSS plugin.

### Alpine.js

Alpine.js is used for the mobile navigation toggle and any in-page interactivity. It is imported and started in [`resources/js/app.js`](./resources/js/app.js).

---

## Caching

All Panchang calculations are cached **forever** in the configured `CACHE_STORE` (defaults to `database`). Cache keys include the date, timezone, latitude, longitude, and elevation so different inputs never collide.

To clear the cache:

```bash
php artisan cache:clear
```

---

## Directory Structure (key files)

```
app/
  Http/
    Controllers/
      DemoController.php          # All HTML page controllers
      ApiKeyController.php        # API key CRUD
      Api/
        PanchangApiController.php # JSON API endpoints
resources/
  css/app.css                     # Tailwind v4 entry + @theme
  js/app.js                       # Alpine.js bootstrap
  views/
    layouts/
      app.blade.php               # Main layout (dark, Inter font)
      navigation.blade.php        # Sticky dark navbar
    hindutithi/
      home.blade.php              # Landing / dashboard
      help.blade.php              # Help page
      api-keys.blade.php          # API key management
      partials/
        birth_form.blade.php      # Reusable session parameter form
      sections/
        day.blade.php             # /day
        moment.blade.php          # /moment
        calendar.blade.php        # /calendar
        muhurta.blade.php         # /muhurta
        kundali.blade.php         # /kundali  (also used for /varga)
        vimshottari.blade.php     # /vimshottari
        shadbala.blade.php        # /shadbala
        yogas.blade.php           # /yogas
        festivals.blade.php       # /festivals
        electional.blade.php      # /electional
        janmarashi.blade.php      # /janmarashi
        unavailable.blade.php     # Shown when a feature isn't in the package
routes/
  web.php                         # All browser routes
  api.php                         # REST API routes (under /api)
openapi.yaml                      # OpenAPI 3.1 spec
```

---

## Environment Variables

The default `.env.example` uses **SQLite** and **database-backed sessions/cache**, so no external services are required for local development. The most relevant variables:

```dotenv
APP_NAME=Hindutithi
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite          # database/database.sqlite is auto-created

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## Running Tests

```bash
composer test
# or
php artisan test
```

---

## License

MIT
