<?php

return [
    /**
     * Default rate limits for new API keys.
     */
    'rate_limits' => [
        'per_minute' => env('API_RATE_LIMIT_PER_MINUTE', 60),
        'per_day' => env('API_RATE_LIMIT_PER_DAY', 1440),
    ],

    /**
     * Available API key scopes/abilities.
     * Maps scope names to descriptions.
     */
    'abilities' => [
        // ── Panchang ──────────────────────────────────────────────────────────
        'panchang:day'        => 'Daily Panchang (tithi, nakshatra, yoga, karana, vara)',
        'panchang:moment'     => 'Panchang at a specific moment',
        'panchang:calendar'   => 'Hindu calendar (Samvat, lunar months)',
        'panchang:muhurta'    => 'Muhurta day segments (Rahu Kaal, Abhijit, etc.)',
        'panchang:electional' => 'Electional evaluator & auspiciousness checks',
        'panchang:timeline'   => 'Limb timeline over a date range (up to 7 days)',
        'panchang:sankranti'  => 'Sankranti finder (Sun enters Rashi)',
        'panchang:astronomy'  => 'Pure astronomy report (ephemeris, sidereal time)',
        'panchang:moon-sign'  => 'Moon sign / Janmarashi',
        // ── Astrology ─────────────────────────────────────────────────────────
        'astrology:kundli'    => 'Birth chart / Kundli with planetary positions',
        'astrology:varga'     => 'Divisional charts (D2 through D60)',
        'astrology:yogas'     => 'Classical planetary yogas',
        'astrology:shadbala'  => 'Six-fold planetary strength (Shadbala)',
        'astrology:dasha'     => 'Vimshottari Dasha periods',
    ],


    /**
     * Number of days to retain usage logs before pruning.
     */
    'log_retention_days' => env('API_LOG_RETENTION_DAYS', 90),
];
