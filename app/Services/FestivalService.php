<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\Cache;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;

class FestivalService
{
    public function __construct(
        private Panchang $panchang,
        private PanchangTranslator $translator
    ) {}

    /**
     * Get all festivals for a specific month and year.
     */
    public function getFestivalsForMonth(int $month, int $year, GeoLocation $location, string $timezone, string $lang = 'en'): array
    {
        $engine = $this->panchang->festivals();
        $tz = new DateTimeZone($timezone);
        $start = new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $month), $tz);
        $daysInMonth = (int) $start->format('t');

        $list = [];

        for ($i = 0; $i < $daysInMonth; $i++) {
            $date = $start->modify("+$i day");
            
            // Cache the engine call (using v3 prefix to bypass stale cache)
            $cacheKey = "panchang:v3:festival:{$date->format('Ymd')}:{$location->latitude}:{$location->longitude}";
            $festivals = Cache::rememberForever($cacheKey, fn() => $engine->getFestivalsForDate($date, $location));

            if (!empty($festivals)) {
                // Enrich each festival with day context for the UI card
                $dayCacheKey = "panchang:v3:day:{$date->format('Ymd')}:{$location->latitude}:{$location->longitude}:{$lang}";
                
                $dayData = Cache::rememberForever($dayCacheKey, function() use ($date, $location) {
                    return $this->panchang->day($date, $location);
                });

                $formattedFestivals = [];
                foreach ($festivals as $festival) {
                    // Try to determine category based on festival name rules (simplified)
                    $name = is_string($festival) ? $festival : $festival->name;
                    $category = $this->determineCategory($name);
                    
                    // We generate a "slug" based on the English name for the URL route
                    // The panchang engine returns festival Enum instances or strings. 
                    // Let's assume it returns strings or enums that have a 'value' or 'name'.
                    $slug = \Illuminate\Support\Str::slug($name);

                    // If $festival is an object with timings, extract them, otherwise default to empty
                    $timings = [];
                    if (is_object($festival) && property_exists($festival, 'timings')) {
                        $timings = $festival->timings;
                    }

                    $formattedFestivals[] = [
                        'name' => $name, // Native/translated name handling can be expanded here
                        'slug' => $slug,
                        'category' => $category,
                        'timings' => $timings,
                    ];
                }

                $list[] = [
                    'date' => $date,
                    'weekday' => $date->format('l'), // e.g. "Sunday"
                    'dayData' => [
                        'tithi' => $this->translator->translate($dayData->tithiAtSunrise),
                        'nakshatra' => $this->translator->translate($dayData->nakshatraAtSunrise),
                        'lunarMonth' => $this->translator->translate($dayData->lunarMonth),
                        'paksha' => $this->translator->translate($dayData->paksha),
                    ],
                    'festivals' => $formattedFestivals,
                ];
            }
        }

        return $list;
    }

    /**
     * Determine a UI category for filtering based on the festival name.
     */
    private function determineCategory(string $name): string
    {
        $name = strtolower($name);
        
        if (str_contains($name, 'ekadashi')) return 'Ekadashi';
        if (str_contains($name, 'purnima')) return 'Purnima';
        if (str_contains($name, 'amavasya')) return 'Amavasya';
        if (str_contains($name, 'sankranti')) return 'Sankranti';
        if (str_contains($name, 'jayanti')) return 'Jayanti';
        if (str_contains($name, 'vrat') || str_contains($name, 'pradosh')) return 'Vrat';
        
        // Some major festivals hardcoded
        $major = ['diwali', 'deepavali', 'holi', 'janmashtami', 'navratri', 'mahashivratri', 'raksha bandhan', 'ganesh chaturthi', 'dussehrah', 'vijayadashami'];
        foreach ($major as $m) {
            if (str_contains($name, $m)) return 'Major Festivals';
        }

        return 'Regional';
    }
}
