# 🚀 Vittix Vedic Panchang v2.0.0 — Astrology & Kundli Release

Release date: 2026-08-04

## Highlights

This is the largest release so far. v2.0 expands Vittix Vedic Panchang from a Panchang calculation engine into a full Vedic astrology platform with Kundli and planetary calculations.

## ✨ New Features

- Complete Kundli engine (birth chart generation, houses, ascendant)
- Planetary position endpoints and utilities
- Chart data endpoints for visualisation (`/api/chart`)
- Kundli demo page and detailed astrology docs

## 🚀 Improved

- Panchang accuracy and DST/timezone handling
- API: additional endpoints for astrology and improved examples
- Quick start documentation and homepage developer UX

## 🐞 Fixed

- Timezone edge cases affecting sunrise/sunset calculations

## 💥 Breaking changes

None for this release. If you use internal calculation helpers, please review the API docs.

## 📖 Documentation

Full docs are available at the `/api/docs` route in the demo. See `README.md`, `CHANGELOG.md`, and `docs/` for detailed guides.

## ⬆ Upgrade

To upgrade an existing installation:

```bash
composer update vittix/panchang
```

If you rely on cached outputs, consider clearing caches after upgrade.

## 🙏 Thanks

Thank you to everyone who contributed, tested features, filed issues and helped validate calculations.

View the GitHub release: https://github.com/ketandholakia/Vittix-Vedic-Panchang/releases/tag/v2.0.0
