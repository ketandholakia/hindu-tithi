# Vittix Vedic Panchang

> Professional Vedic Panchang & Astrology engine for PHP, Laravel and REST APIs.

Vittix Vedic Panchang provides accurate Panchang (tithi, nakshatra, yoga, muhurta), a full festival calendar engine, and a complete Vedic astrology (Kundli) module with planetary positions, houses, divisional (varga) charts, dasha, shadbala, and yogas. This repository is the **Hindutithi.in** Laravel demo/production application that showcases the [`vittix/panchang`](https://packagist.org/packages/vittix/panchang) package and exposes it as a public REST API.

## Features

- **Panchang**
  - Daily and moment-based Panchang: Tithi, Nakshatra, Yoga, Karana, Vara
  - Sunrise / Sunset, Moonrise / Moonset
  - Muhurta windows, Sankranti, electional (Muhurta selection) checks
  - Hindu calendar (Amanta/Purnimanta months, Vikram/Shaka Samvat)
- **Astrology**
  - Kundli / birth chart generation with whole-sign houses
  - Ascendant (Lagna), Janmarashi (Moon sign)
  - Planetary positions and longitudes, raw astronomy data
  - Divisional (Varga) charts, e.g. D9/Navamsa
  - Vimshottari Dasha (major & sub periods)
  - Shadbala (six-fold planetary strength)
  - Classical yoga detection
- **Festival Engine**
  - Structured festival database with categories and per-year occurrences
  - Upcoming festivals view and API, festival detail lookups by code
- **Platform & APIs**
  - PHP library and Laravel package (`vittix/panchang`)
  - Versioned REST JSON APIs (current + `/api/v1` compatibility layer)
  - API key management with scopes, per-key rate limiting, and usage logging
  - Comprehensive Admin Dashboard (users, API tokens, rate-limit settings)
  - Telegram bot webhook integration
  - Timezone-aware calculations with DST support

## Installation (package)

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

## License

MIT — see `LICENSE` in the repository.

---

# Hindutithi.in — Demo & Production App

A Laravel application that showcases the `vittix/panchang` package — Daily Panchang, Hindu Calendar, Muhurta, Kundali, Varga charts, Vimshottari Dasha, Shadbala, Yogas, a full Festival engine, and a public JSON REST API with API-key management and a Telegram bot.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3 · Laravel ^13.8 |
| Astrology engine | `vittix/panchang ^2.4` |
| Auth scaffolding | Laravel Breeze ^2.4 |
| Frontend styling | Tailwind CSS **v4.1** (via `@tailwindcss/vite`, with `@tailwindcss/forms`) |
| JS interactivity | Alpine.js ^3.4 |
| Build tool | Vite ^8.0 |
| Database | SQLite (default) |
| Messaging | Telegram Bot API (webhook) |

---

## Requirements

- PHP **≥ 8.3** with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- Composer
- Node.js ≥ 18 & npm

---

## Installation

### 1 — Clone and install

```bash
git clone https://github.com/ketandholakia/hindu-tithi.git
cd hindu-tithi
```

### 2 — One-command setup (recommended)

```bash
composer setup
```

This runs, in sequence:

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

Starts four processes concurrently:

| Process | Command |
|---|---|
| HTTP server | `php artisan serve` |
| Queue worker | `php artisan queue:listen --tries=1 --timeout=0` |
| Log viewer | `php artisan pail --timeout=0` |
| Vite dev server | `npm run dev` |

Open **http://localhost:8000** in your browser.

---

## Application Pages

Most calculation pages share a persistent **session form** (`partials/birth_form.blade.php`) that lets you set date, time, timezone, and geo-location (with quick-select city presets), reused across views via the `NoCache` middleware group.

| Route | Page | Description |
|---|---|---|
| `/` | Home | Landing page / dashboard (also aliased as `hindutithi.home`) |
| `/home` | — | Redirects permanently to `/` |
| `/help` | Help | Usage notes and tips |
| `/accuracy` | Accuracy | Notes on calculation accuracy and methodology |
| `/astrology` | Astrology overview | Introduction to the Kundli/astrology module |
| `/whats-new` | What's New | Release highlights for site visitors |
| `/day` | Daily Panchang | Tithi, Nakshatra, Yoga, Karana, Vara, solar events for a date |
| `/moment` | Moment Panchang | Exact planetary positions at a specific date + time |
| `/calendar` | Hindu Calendar | Amanta/Purnimanta months, Vikram/Shaka Samvat, moon phases |
| `/muhurta` | Muhurta | Daytime muhurtas derived from sunrise and sunset |
| `/janmarashi` | Janmarashi | Moon-sign (Rashi) at a given moment |
| `/ascendant` | Ascendant | Lagna calculation at a given moment |
| `/kundli` | Kundali | Whole-sign house chart with planetary placements |
| `/varga` | Varga | Divisional charts (D9 etc.) — pass `?varga=D9` |
| `/vimshottari` | Vimshottari Dasha | Major and sub-dasha periods |
| `/shadbala` | Shadbala | Six-fold planetary strength |
| `/yogas` | Yogas | Classical yoga detection |
| `/festivals` | Festivals | Festival listing with category filters and month navigation |
| `/electional` | Electional | Available electional astrology checks |
| `/api/docs` | API Docs | In-app OpenAPI documentation viewer |
| `/openapi.yaml` | OpenAPI spec | Raw OpenAPI 3.1 YAML file |
| `/sitemap.xml` | Sitemap | Generated XML sitemap of public pages |
| `/dashboard` | Dashboard | Authenticated user dashboard *(requires login)* |
| `/api-keys` | API Keys | Personal API key management *(requires login)* |
| `/profile` | Profile | Account settings *(requires login)* |
| `/admin` | Admin Dashboard | System management, users, and API tokens *(requires admin)* |

> **Note:** Pages that require features not present in the installed `vittix/panchang` version fall back to an "unavailable" notice rather than throwing an error.

---

## Session Parameters

The birth/location form accepts:

| Field | Default | Description |
|---|---|---|
| `date` | today | Civil date for calculations (`YYYY-MM-DD`) |
| `time` | `06:00` | Used by moment-based views (`/moment`, `/janmarashi`, `/ascendant`, `/kundli`, etc.) |
| `tz` | `Asia/Kolkata` | PHP timezone identifier |
| `lat` | `23.0225` | Latitude in decimal degrees |
| `lon` | `72.5714` | Longitude in decimal degrees |
| `elev` | `0` | Elevation above sea level in metres |

Quick-select city presets (New Delhi, Mumbai, Bengaluru, Kolkata, Chennai) are provided for convenience.

---

## REST API

The current API (`/api/*`) is protected by the `auth.api_token` middleware plus scope checks, throttling, and usage logging. Include your key in the request header:

```
X-API-KEY: <your-key>
```

### Panchang & Astrology (current API)

| Method | Path | Scope | Description |
|---|---|---|---|
| `GET` | `/api` | — | Lists all endpoints |
| `GET` | `/api/examples` | — | Ready-to-copy example URLs for current inputs |
| `GET` | `/api/day` | `panchang:day` | Daily Panchang summary |
| `GET` | `/api/moment` | `panchang:moment` | Panchang values at an exact instant |
| `GET` | `/api/calendar` | `panchang:calendar` | Hindu calendar summary |
| `GET` | `/api/muhurta` | `panchang:muhurta` | Day muhurtas between sunrise and sunset |
| `GET` | `/api/electional` | `panchang:electional` | Available electional evaluator checks |
| `GET` | `/api/timeline` | `panchang:timeline` | Panchang element timeline |
| `GET` | `/api/sankranti` | `panchang:sankranti` | Sankranti (solar transit) data |
| `GET` | `/api/electional/evaluate` | `panchang:electional` | Evaluate a specific electional muhurta |
| `GET` | `/api/astronomy` | `panchang:astronomy` | Raw astronomical/ephemeris data |
| `GET` | `/api/moon-sign` | `panchang:moon-sign` | Moon sign (Janmarashi) at a moment |
| `GET` | `/api/astrology/kundli` | `astrology:kundli` | Kundli / birth chart JSON |
| `GET` | `/api/astrology/varga/{varga}` | `astrology:varga` | Divisional chart (e.g. `D9`) |
| `GET` | `/api/astrology/yogas` | `astrology:yogas` | Classical yoga detection |
| `GET` | `/api/astrology/shadbala` | `astrology:shadbala` | Six-fold planetary strength |
| `GET` | `/api/astrology/dasha` | `astrology:dasha` | Vimshottari Dasha periods |

Example:

```bash
curl "http://localhost:8000/api/day?date=2026-08-15&tz=Asia/Kolkata&lat=23.0225&lon=72.5714" \
  -H "X-API-KEY: your-key-here"
```

### `/api/v1` — Compatibility & Festival Engine

A separate, `panchang.api.key`-protected group under `/api/v1` provides simplified/legacy-compatible endpoints plus the newer festival engine:

| Method | Path | Description |
|---|---|---|
| `GET` | `/api/v1/panchang/today` | Today's Panchang (compatibility shape) |
| `GET` | `/api/v1/panchang/day` | Panchang for a given day |
| `GET` | `/api/v1/calendar/month` | Month calendar |
| `GET` | `/api/v1/muhurta/today` | Today's muhurtas |
| `GET` | `/api/v1/festivals` | Festival list (current festival engine) |
| `GET` | `/api/v1/festivals/{code}` | Single festival detail by code |
| `GET` | `/api/v1/festivals/occurrences/{year}` | All festival occurrences for a year |
| `GET` | `/api/v1/festivals/{code}/occurrences` | All occurrences of one festival |
| `GET` | `/api/v1/festivals-old` | Legacy festival endpoint (kept for backward compatibility) |
| `GET` | `/api/v1/kundli` | Kundli (compatibility shape) |
| `GET` | `/api/v1/astrology/planet-positions` | Planetary positions |
| `GET` | `/api/v1/settings` | Public settings/config |

All successful responses are JSON. The full OpenAPI 3.1 specification is at [`openapi.yaml`](./openapi.yaml) and is viewable in-app at `/api/docs`.

### API Keys

Authenticated users can generate and manage personal, scoped API keys via `/api-keys`. Each key supports per-minute and per-day rate limits and logs every request (endpoint, status, response time, IP) for analytics.

Administrators can oversee all API tokens, adjust rate limits, and view usage statistics via `/admin/api-tokens`.

The full key value is shown **only once** on creation — store it securely.

See [`API_KEY_MANAGEMENT.md`](./API_KEY_MANAGEMENT.md) for the full schema, scopes, and configuration details.

---

## Telegram Bot

A Telegram bot webhook is wired at `POST /telegram/webhook` (`TelegramWebhookController`). Configure it with:

```dotenv
TELEGRAM_BOT_TOKEN=
TELEGRAM_WEBHOOK_SECRET=
```

---

## Frontend

### Tailwind CSS v4

This project uses **Tailwind CSS v4** (plus `@tailwindcss/forms`) configured via the Vite plugin — there is no `tailwind.config.js`. All theme customisation lives in [`resources/css/app.css`](./resources/css/app.css) inside an `@theme` block:

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
      DemoController.php          # Panchang/astrology HTML page controllers
      FestivalController.php      # /festivals page controller
      ApiKeyController.php        # User-facing API key CRUD
      TelegramWebhookController.php
      Admin/
        DashboardController.php
        UserController.php
        AdminApiTokenController.php
      Api/
        PanchangApiController.php         # /api/day, /api/moment, ...
        PanchangExtendedApiController.php # /api/timeline, /api/sankranti, ...
        AstrologyApiController.php        # /api/astrology/*
        V1/
          CompatibilityController.php     # /api/v1 legacy-shaped endpoints
          FestivalController.php          # /api/v1/festivals
          FestivalOccurrenceController.php
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
      accuracy.blade.php          # Accuracy notes
      astrology.blade.php         # Astrology overview
      whats_new.blade.php         # Release highlights
      api-keys.blade.php          # API key management
      kundli.blade.php
      partials/
        birth_form.blade.php      # Reusable session parameter form
      sections/
        day.blade.php · moment.blade.php · calendar.blade.php
        muhurta.blade.php · janmarashi.blade.php · ascendant.blade.php
        kundali.blade.php (also used for /varga) · vimshottari.blade.php
        shadbala.blade.php · yogas.blade.php · electional.blade.php
        festivals.blade.php · unavailable.blade.php
    festivals/index.blade.php     # /festivals listing
    components/festival/          # card, category-filter, month-navigation, upcoming
    admin/
      dashboard.blade.php
      users/                      # index, edit
      api-tokens/                 # index, show, settings
    api/docs.blade.php            # /api/docs
routes/
  web.php                         # All browser routes
  api.php                         # REST API routes (/api and /api/v1)
festival_master.json              # Structured festival dataset
openapi.yaml                      # OpenAPI 3.1 spec
```

---

## Environment Variables

The default `.env.example` uses **SQLite** and **database-backed sessions/cache**, so no external services are required for local development. Key variables:

```dotenv
APP_NAME=Hindutithi
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite          # database/database.sqlite is auto-created

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

TELEGRAM_BOT_TOKEN=
TELEGRAM_WEBHOOK_SECRET=
```

---

## Running Tests

```bash
composer test
# or
php artisan test
```

---

## Further Reading

- [`API_KEY_MANAGEMENT.md`](./API_KEY_MANAGEMENT.md) — API key/scopes/rate-limit schema
- [`MIGRATION_PRODUCTION.md`](./MIGRATION_PRODUCTION.md) — production migration notes
- [`RELEASE_NOTES_V2.md`](./RELEASE_NOTES_V2.md) / [`CHANGELOG.md`](./CHANGELOG.md) — release history

## License

MIT
