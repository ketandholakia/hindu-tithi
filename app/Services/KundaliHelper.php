<?php

namespace App\Services;

class KundaliHelper
{
    // 27 Nakshatras in sidereal order starting from 0° Aries
    public const NAKSHATRAS = [
        'Ashwini', 'Bharani', 'Krittika', 'Rohini', 'Mrigashira', 'Ardra',
        'Punarvasu', 'Pushya', 'Ashlesha', 'Magha', 'Purva Phalguni', 'Uttara Phalguni',
        'Hasta', 'Chitra', 'Swati', 'Vishakha', 'Anuradha', 'Jyeshtha',
        'Mula', 'Purva Ashadha', 'Uttara Ashadha', 'Shravana', 'Dhanishtha',
        'Shatabhisha', 'Purva Bhadrapada', 'Uttara Bhadrapada', 'Revati',
    ];

    // Short abbreviations for planet display in chart cells
    public const PLANET_ABBR = [
        'Sun'      => 'Su', 'Moon'     => 'Mo', 'Mars'    => 'Ma',
        'Mercury'  => 'Me', 'Jupiter'  => 'Ju', 'Venus'   => 'Ve',
        'Saturn'   => 'Sa', 'RahuMean' => 'Ra', 'KetuMean'=> 'Ke',
        'Rahu'     => 'Ra', 'Ketu'     => 'Ke',
        'Uranus'   => 'Ur', 'Neptune'  => 'Ne', 'Pluto'   => 'Pl',
    ];

    // Colors for planet abbreviations in the chart
    public const PLANET_COLORS = [
        'Su' => '#e97c2a', // amber-orange (Sun)
        'Mo' => '#a78bfa', // violet (Moon)
        'Ma' => '#f87171', // red (Mars)
        'Me' => '#34d399', // green (Mercury)
        'Ju' => '#fbbf24', // yellow (Jupiter)
        'Ve' => '#60a5fa', // blue (Venus)
        'Sa' => '#94a3b8', // slate (Saturn)
        'Ra' => '#c084fc', // purple (Rahu)
        'Ke' => '#f472b6', // pink (Ketu)
        'Ur' => '#67e8f9', 'Ne' => '#6ee7b7', 'Pl' => '#fb923c',
    ];

    // Combust orbs in degrees (classical Jyotish values)
    private const COMBUST_ORBS = [
        'Moon'     => 12.0,
        'Mars'     => 17.0,
        'Mercury'  => 14.0, // when retrograde use 12
        'Jupiter'  => 11.0,
        'Venus'    => 10.0,
        'Saturn'   => 15.0,
    ];

    // Planetary dignity/relation by rashi (0=Aries..11=Pisces)
    // Values: 'own','exalted','debilitated','friendly','neutral','enemy'
    private const PLANET_RASHI_RELATION = [
        'Sun' => [
            'Leo' => 'Own', 'Aries' => 'Exalted', 'Libra' => 'Debilitated',
            'Sagittarius' => 'Friendly', 'Scorpio' => 'Friendly', 'Gemini' => 'Friendly',
            'Capricorn' => 'Neutral', 'Aquarius' => 'Neutral',
            'Taurus' => 'Enemy', 'Virgo' => 'Enemy',
            'Cancer' => 'Neutral', 'Pisces' => 'Friendly',
        ],
        'Moon' => [
            'Cancer' => 'Own', 'Taurus' => 'Exalted', 'Scorpio' => 'Debilitated',
            'Sagittarius' => 'Friendly', 'Pisces' => 'Friendly', 'Aries' => 'Friendly',
            'Gemini' => 'Neutral', 'Leo' => 'Neutral', 'Virgo' => 'Neutral', 'Libra' => 'Neutral',
            'Capricorn' => 'Neutral', 'Aquarius' => 'Neutral', 'Pisces' => 'Friendly',
        ],
        'Mars' => [
            'Aries' => 'Own', 'Scorpio' => 'Own', 'Capricorn' => 'Exalted', 'Cancer' => 'Debilitated',
            'Leo' => 'Friendly', 'Sagittarius' => 'Friendly', 'Pisces' => 'Friendly',
            'Gemini' => 'Neutral', 'Virgo' => 'Neutral',
            'Taurus' => 'Enemy', 'Libra' => 'Enemy', 'Aquarius' => 'Enemy',
        ],
        'Mercury' => [
            'Gemini' => 'Own', 'Virgo' => 'Own|Exalted', 'Pisces' => 'Debilitated',
            'Leo' => 'Friendly', 'Libra' => 'Friendly', 'Sagittarius' => 'Friendly',
            'Capricorn' => 'Neutral', 'Aquarius' => 'Neutral', 'Scorpio' => 'Neutral',
            'Aries' => 'Enemy', 'Taurus' => 'Enemy', 'Cancer' => 'Enemy',
        ],
        'Jupiter' => [
            'Sagittarius' => 'Own', 'Pisces' => 'Own', 'Cancer' => 'Exalted', 'Capricorn' => 'Debilitated',
            'Aries' => 'Friendly', 'Leo' => 'Friendly', 'Scorpio' => 'Friendly',
            'Taurus' => 'Neutral', 'Aquarius' => 'Neutral',
            'Gemini' => 'Enemy', 'Virgo' => 'Enemy', 'Libra' => 'Neutral',
        ],
        'Venus' => [
            'Taurus' => 'Own', 'Libra' => 'Own', 'Pisces' => 'Exalted', 'Virgo' => 'Debilitated',
            'Capricorn' => 'Friendly', 'Aquarius' => 'Friendly', 'Gemini' => 'Friendly',
            'Cancer' => 'Neutral', 'Leo' => 'Neutral', 'Scorpio' => 'Neutral',
            'Aries' => 'Enemy', 'Sagittarius' => 'Enemy',
        ],
        'Saturn' => [
            'Capricorn' => 'Own', 'Aquarius' => 'Own', 'Libra' => 'Exalted', 'Aries' => 'Debilitated',
            'Gemini' => 'Friendly', 'Virgo' => 'Friendly', 'Taurus' => 'Friendly',
            'Sagittarius' => 'Neutral', 'Pisces' => 'Neutral',
            'Cancer' => 'Enemy', 'Leo' => 'Enemy', 'Scorpio' => 'Enemy',
        ],
    ];

    /**
     * Compute nakshatra name and pada from a sidereal longitude (0–360).
     */
    public static function nakshatra(float $longitude): array
    {
        $longitude = fmod($longitude, 360.0);
        if ($longitude < 0) {
            $longitude += 360.0;
        }
        $nakshatraSpan = 360.0 / 27; // 13.333...°
        $padaSpan      = $nakshatraSpan / 4; // 3.333...°

        $index = (int) floor($longitude / $nakshatraSpan);
        $pada  = (int) floor(($longitude - ($index * $nakshatraSpan)) / $padaSpan) + 1;

        return [
            'name' => self::NAKSHATRAS[$index] ?? '—',
            'pada' => $pada,
        ];
    }

    /**
     * Is the planet retrograde? Rahu/Ketu are always "retrograde" by convention.
     */
    public static function isRetrograde(float $dailyMotion, string $planetName = ''): bool
    {
        if (in_array($planetName, ['RahuMean', 'KetuMean', 'Rahu', 'Ketu'], true)) {
            return true; // always retrograde by convention
        }
        return $dailyMotion < 0;
    }

    /**
     * Is the planet combust (too close to the Sun)?
     * Both longitudes are sidereal.
     */
    public static function isCombust(float $planetLon, float $sunLon, string $planetName): bool
    {
        if (!isset(self::COMBUST_ORBS[$planetName])) {
            return false; // Rahu/Ketu/Asc don't combust
        }
        $orb  = self::COMBUST_ORBS[$planetName];
        $diff = abs($planetLon - $sunLon);
        if ($diff > 180) {
            $diff = 360 - $diff;
        }
        return $diff <= $orb;
    }

    /**
     * Get the planetary relation with a rashi.
     */
    public static function relation(string $planetName, string $rashiName): string
    {
        return self::PLANET_RASHI_RELATION[$planetName][$rashiName] ?? '—';
    }

    /**
     * Convert a decimal longitude to DMS string e.g. "18-25-12".
     */
    public static function toDMS(float $longitude): string
    {
        $longitude = fmod($longitude, 30.0); // degree within sign (0–30)
        if ($longitude < 0) {
            $longitude += 30.0;
        }
        $deg = (int) floor($longitude);
        $min = (int) floor(($longitude - $deg) * 60);
        $sec = (int) round((($longitude - $deg) * 60 - $min) * 60);
        return sprintf('%02d-%02d-%02d', $deg, $min, $sec);
    }

    /**
     * Full DMS without modulo (for ascendant, absolute sidereal lon within sign).
     */
    public static function degToDMS(float $degrees): string
    {
        $degrees = abs($degrees);
        $deg = (int) floor($degrees);
        $min = (int) floor(($degrees - $deg) * 60);
        $sec = (int) round((($degrees - $deg) * 60 - $min) * 60);
        return sprintf('%02d-%02d-%02d', $deg, $min, $sec);
    }

    /**
     * Get planet abbreviation for chart display.
     */
    public static function abbr(string $bodyName): string
    {
        return self::PLANET_ABBR[$bodyName] ?? substr($bodyName, 0, 2);
    }

    /**
     * Get planet color for chart display.
     */
    public static function color(string $abbr): string
    {
        return self::PLANET_COLORS[$abbr] ?? '#e2e8f0';
    }

    /**
     * Build the North Indian chart house map.
     * Returns [houseNumber => [planet_abbr, ...]] for SVG rendering.
     * Also appends the ascendant rashi number to house 1.
     */
    public static function buildChartHouseMap(array $placements, string $lagnaCellLabel = ''): array
    {
        $map = array_fill(1, 12, []);
        foreach ($placements as $pl) {
            $map[$pl['house']][] = [
                'abbr'  => $pl['abbr'],
                'color' => self::color($pl['abbr']),
            ];
        }
        return $map;
    }

    /**
     * Compute Vimshottari dasha balance from an array of DashaPeriod objects.
     * Returns string like "3 Y 8 M 20 D" for the first dasha that is still running.
     */
    public static function dashaBalance(array $dashas, \DateTimeImmutable $birthMoment): string
    {
        if (empty($dashas)) {
            return '';
        }
        $first = $dashas[0];
        // The "balance" is how long remains of the first dasha from birth
        $endTs   = $first->end->getTimestamp();
        $birthTs = $birthMoment->getTimestamp();
        $diff    = $endTs - $birthTs;
        if ($diff <= 0) {
            return '';
        }
        $years  = (int) floor($diff / (365.25 * 86400));
        $remain = $diff - (int) ($years * 365.25 * 86400);
        $months = (int) floor($remain / (30.44 * 86400));
        $remain -= (int) ($months * 30.44 * 86400);
        $days   = (int) floor($remain / 86400);
        return $first->body->name . ' ' . $years . ' Y ' . $months . ' M ' . $days . ' D';
    }
}
