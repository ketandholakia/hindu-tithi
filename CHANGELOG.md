# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2026-08-04

### Added

- Complete Kundli engine (birth chart generation)
- Planetary position APIs (`/api/planets`)
- Chart / Kundli JSON endpoints (`/api/kundli`, `/api/chart`)
- Kundli demo page and astrology overview on the site
- Documentation pages for accuracy and astrology

### Improved

- Panchang accuracy and DST handling
- API: Added timezone-aware endpoints and extended examples
- Developer experience: Quick start examples and improved homepage

### Fixed

- Several timezone and formatting issues in Panchang output

### Upgrade

Run:

```bash
composer update vittix/panchang
```

See `RELEASE_NOTES_V2.md` for a high-level release summary and migration tips.
