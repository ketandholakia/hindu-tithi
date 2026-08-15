<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PanchangTranslator;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\Enum\AyanamshaSystem;
use Vittix\Panchang\Enum\Body;
use Vittix\Panchang\Enum\Rashi;
use Vittix\Panchang\Panchanga\JanmarashiCalculator;
use Vittix\Panchang\Panchanga\KaranaCalculator;
use Vittix\Panchang\Panchanga\NakshatraCalculator;
use Vittix\Panchang\Panchanga\TimelineCalculator;
use Vittix\Panchang\Panchanga\TithiCalculator;
use Vittix\Panchang\Panchanga\TransitionFinder;
use Vittix\Panchang\Panchanga\YogaCalculator;
use Vittix\Panchang\Panchanga\PanchangService;
use Vittix\Panchang\Time\AstroTimeFactory;
use Vittix\Panchang\ValueObject\AstroInstant;
use Vittix\Panchang\ValueObject\GeoLocation;
use Vittix\Panchang\Astronomy\Engine\EngineFactory;
use Vittix\Panchang\Math\Angle;

/**
 * Extended Panchang API endpoints.
 *
 * Exposes advanced features from the vittix/panchang library that are not
 * available via the base PanchangApiController:
 *
 *   GET /api/timeline           – Panchanga limb windows over a date range
 *   GET /api/sankranti          – Exact Sun-enters-Rashi (solar transit) moments
 *   GET /api/electional/evaluate – Evaluate electional checks for a given day
 *   GET /api/astronomy          – Pure astronomical report (no Panchang overlay)
 *   GET /api/moon-sign          – Moon's Rashi (Janmarashi) at a given moment
 */
class PanchangExtendedApiController extends Controller
{
    // -------------------------------------------------------------------------
    // Shared input helpers
    // -------------------------------------------------------------------------

    private function input(Request $request): array
    {
        return [
            'date'         => $request->query('date', date('Y-m-d')),
            'time'         => $request->query('time', '06:00'),
            'tz'           => $request->query('tz', 'Asia/Kolkata'),
            'lat'          => $request->query('lat', 23.0225),
            'lon'          => $request->query('lon', 72.5714),
            'elev'         => $request->query('elev', 0),
            'lang'         => PanchangTranslator::validLang((string) $request->query('lang', 'en')),
            'ayanamsha'    => $request->query('ayanamsha', 'lahiri'),
            'month_system' => $request->query('month_system', 'amanta'),
        ];
    }

    private function makeMoment(array $input): DateTimeImmutable
    {
        return new DateTimeImmutable($input['date'] . ' ' . $input['time'], new DateTimeZone($input['tz']));
    }

    private function location(array $input): GeoLocation
    {
        return new GeoLocation((float) $input['lat'], (float) $input['lon'], (float) $input['elev']);
    }

    private function cached(string $key, callable $fn): mixed
    {
        return Cache::rememberForever($key, $fn);
    }

    private function formatEnum(mixed $value, string $lang = 'en'): ?string
    {
        return PanchangTranslator::translateWith($value, $lang);
    }

    private function fmtInstant(?AstroInstant $inst, ?DateTimeZone $tz = null): ?string
    {
        if ($inst === null) {
            return null;
        }
        $dt = $tz ? $inst->utc->setTimezone($tz) : $inst->utc;
        return $dt->format(DATE_ATOM);
    }

    // -------------------------------------------------------------------------
    // 1. Timeline – Panchanga limb windows over a date range
    // -------------------------------------------------------------------------

    /**
     * GET /api/timeline
     *
     * Query params:
     *   date        – start date (Y-m-d)           default: today
     *   end_date    – end date (Y-m-d)             default: date + 1 day
     *   tz          – IANA timezone                default: Asia/Kolkata
     *   lat, lon    – observer coordinates
     *   fields      – comma-separated subset:      default: tithi,nakshatra,yoga,karana
     *                 tithi | nakshatra | yoga | karana
     *   lang        – language code                default: en
     *   ayanamsha   – ayanamsha system             default: lahiri
     *
     * Returns an object with one array per requested field, each entry having
     * { name, start, end } in ISO 8601 timestamps.
     */
    public function timeline(Request $request): JsonResponse
    {
        $input   = $this->input($request);
        $lang    = $input['lang'];
        $tz      = new DateTimeZone($input['tz']);
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);

        $startDate = new DateTimeImmutable($input['date'], $tz);
        $endDate   = new DateTimeImmutable(
            $request->query('end_date', $startDate->modify('+1 day')->format('Y-m-d')),
            $tz
        );

        // Clamp range to 7 days to prevent runaway calculations
        $diffDays = (int) $startDate->diff($endDate)->days;
        if ($diffDays > 7) {
            return response()->json(['error' => 'Date range must not exceed 7 days.'], 422);
        }
        if ($endDate <= $startDate) {
            return response()->json(['error' => 'end_date must be after date.'], 422);
        }

        $fields = array_filter(
            array_map('trim', explode(',', $request->query('fields', 'tithi,nakshatra,yoga,karana'))),
            fn($f) => in_array($f, ['tithi', 'nakshatra', 'yoga', 'karana'], true)
        );
        if (empty($fields)) {
            $fields = ['tithi', 'nakshatra', 'yoga', 'karana'];
        }

        $cacheKey = 'api:timeline:' . $startDate->format('Ymd') . ':' . $endDate->format('Ymd')
            . ':' . $input['lat'] . ':' . $input['lon'] . ':' . $input['tz']
            . ':' . implode(',', $fields) . ':' . $lang . ':' . $ayanamsha->value;

        $data = $this->cached($cacheKey, function () use ($startDate, $endDate, $tz, $fields, $lang, $ayanamsha) {
            $engine  = EngineFactory::createPurePhpEngine($ayanamsha);
            $factory = new AstroTimeFactory();
            $finder  = new TransitionFinder($engine);
            $calc    = new TimelineCalculator(
                $finder,
                new TithiCalculator($engine),
                new NakshatraCalculator($engine),
                new YogaCalculator($engine),
                new KaranaCalculator($engine),
            );

            $from  = $factory->fromDateTime($startDate);
            $until = $factory->fromDateTime($endDate);

            $result = [];

            if (in_array('tithi', $fields, true)) {
                $result['tithi'] = array_map(
                    fn($w) => [
                        'name'  => PanchangTranslator::translateWith($w['tithi'], $lang),
                        'start' => $this->fmtInstant($w['start'], $tz),
                        'end'   => $this->fmtInstant($w['end'], $tz),
                    ],
                    $calc->getTithiWindows($from, $until)
                );
            }

            if (in_array('nakshatra', $fields, true)) {
                $result['nakshatra'] = array_map(
                    fn($w) => [
                        'name'  => PanchangTranslator::translateWith($w['nakshatra'], $lang),
                        'start' => $this->fmtInstant($w['start'], $tz),
                        'end'   => $this->fmtInstant($w['end'], $tz),
                    ],
                    $calc->getNakshatraWindows($from, $until)
                );
            }

            if (in_array('yoga', $fields, true)) {
                $result['yoga'] = array_map(
                    fn($w) => [
                        'name'  => PanchangTranslator::translateWith($w['yoga'], $lang),
                        'start' => $this->fmtInstant($w['start'], $tz),
                        'end'   => $this->fmtInstant($w['end'], $tz),
                    ],
                    $calc->getYogaWindows($from, $until)
                );
            }

            if (in_array('karana', $fields, true)) {
                $result['karana'] = array_map(
                    fn($w) => [
                        'name'  => PanchangTranslator::translateWith($w['karana'], $lang),
                        'start' => $this->fmtInstant($w['start'], $tz),
                        'end'   => $this->fmtInstant($w['end'], $tz),
                    ],
                    $calc->getKaranaWindows($from, $until)
                );
            }

            return $result;
        });

        return response()->json([
            'from'   => $startDate->format(DATE_ATOM),
            'until'  => $endDate->format(DATE_ATOM),
            'fields' => array_values($fields),
            'data'   => $data,
        ]);
    }

    // -------------------------------------------------------------------------
    // 2. Sankranti – Exact solar Rashi-entry moments
    // -------------------------------------------------------------------------

    /**
     * GET /api/sankranti
     *
     * Query params:
     *   year        – Gregorian year                default: current year
     *   rashi       – 0-11 (Mesha=0) or name like 'mesha', 'vrishabha', …
     *                 Omit to return all 12 Sankrantis for the year.
     *   tz          – IANA timezone                 default: Asia/Kolkata
     *   ayanamsha   – ayanamsha system              default: lahiri
     *   lang        – language code                 default: en
     *
     * Returns { rashi, utc, local } for each Sankranti found.
     */
    public function sankranti(Request $request): JsonResponse
    {
        $input      = $this->input($request);
        $lang       = $input['lang'];
        $tz         = new DateTimeZone($input['tz']);
        $ayanamsha  = AyanamshaSystem::from($input['ayanamsha']);
        $year       = (int) $request->query('year', (int) date('Y'));
        $rashiParam = $request->query('rashi');

        // Resolve rashi filter
        $rashiFilter = null;
        if ($rashiParam !== null) {
            if (is_numeric($rashiParam)) {
                $idx = (int) $rashiParam;
                if ($idx < 0 || $idx > 11) {
                    return response()->json(['error' => 'rashi index must be 0–11.'], 422);
                }
                $rashiFilter = $idx;
            } else {
                // Match by case name (e.g. 'mesha', 'Makara', 'KARKA')
                $matched = null;
                foreach (Rashi::cases() as $case) {
                    if (strtolower($case->name) === strtolower($rashiParam)) {
                        $matched = $case->value;
                        break;
                    }
                }
                if ($matched === null) {
                    return response()->json(['error' => "Unknown rashi: {$rashiParam}. Use index 0–11 or a rashi name (Mesha, Vrishabha, …Meena)."], 422);
                }
                $rashiFilter = $matched;
            }
        }


        $cacheKey = "api:sankranti:{$year}:{$ayanamsha->value}:{$tz->getName()}:{$lang}:" . ($rashiFilter ?? 'all');

        $data = $this->cached($cacheKey, function () use ($year, $rashiFilter, $tz, $ayanamsha, $lang) {
            $engine  = EngineFactory::createPurePhpEngine($ayanamsha);
            $factory = new AstroTimeFactory();
            $results = [];

            // Determine which rashis to search
            $targets = ($rashiFilter !== null) ? [$rashiFilter] : range(0, 11);

            foreach ($targets as $rashiIdx) {
                $rashi = Rashi::fromIndex($rashiIdx);

                // Search window: full calendar year
                $searchStart = new DateTimeImmutable("{$year}-01-01 00:00:00", new DateTimeZone('UTC'));
                $searchEnd   = new DateTimeImmutable("{$year}-12-31 23:59:59", new DateTimeZone('UTC'));

                $fromInst  = $factory->fromDateTime($searchStart);
                $untilInst = $factory->fromDateTime($searchEnd);

                // Use TransitionFinder to find the exact moment Sun's sidereal Rashi index changes
                $finder = new TransitionFinder($engine);
                $crossingFn = static function (AstroInstant $inst) use ($engine): int {
                    $lon = Angle::normalize($engine->siderealLongitude(Body::Sun, $inst));
                    return (int) floor($lon / 30.0);
                };

                $transition = $finder->find($fromInst, $untilInst, $crossingFn);


                if ($transition !== null) {
                    $localDt = $transition->utc->setTimezone($tz);
                    $results[] = [
                        'rashi'     => [
                            'index'  => $rashiIdx,
                            'name'   => PanchangTranslator::translateWith($rashi, $lang) ?? $rashi->name,
                        ],
                        'utc'   => $transition->utc->format(DATE_ATOM),
                        'local' => $localDt->format(DATE_ATOM),
                    ];
                }
            }

            return $results;
        });

        return response()->json([
            'year'    => $year,
            'rashi'   => $rashiFilter !== null ? $rashiFilter : 'all',
            'results' => $data,
        ]);
    }

    // -------------------------------------------------------------------------
    // 3. Electional Evaluation
    // -------------------------------------------------------------------------

    /**
     * GET /api/electional/evaluate
     *
     * Query params:
     *   date, time, tz, lat, lon, elev, ayanamsha, lang (standard params)
     *
     * Returns evaluated auspiciousness checks for the given moment:
     *   amritSiddhiYoga, sarvarthaSiddhiYoga, guruPushyaYoga, raviPushyaYoga,
     *   dishaShool, panchakaDosha, isBhadraActive
     * along with the underlying Panchang limbs used for the evaluation.
     */
    public function evaluateElectional(Request $request): JsonResponse
    {
        $input    = $this->input($request);
        $lang     = $input['lang'];
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $panchang  = Panchang::createDefault($ayanamsha);
        $moment   = $this->makeMoment($input);
        $loc      = $this->location($input);

        $key = 'api:electional:evaluate:' . $moment->format(DATE_ATOM)
            . ':' . $loc->latitude . ':' . $loc->longitude . ':' . $ayanamsha->value . ':' . $lang;

        $data = $this->cached($key, function () use ($panchang, $moment, $loc, $lang) {
            $day        = $panchang->calculator()->forDay($moment, $loc);
            $evaluator  = $panchang->electional();

            $weekdayIndex = (int) $moment->format('w'); // 0 = Sunday … 6 = Saturday
            $nakshatra    = $day->nakshatraAtSunrise;
            $karana       = $day->karanaAtSunrise;

            return [
                'input' => [
                    'date'         => $moment->format('Y-m-d'),
                    'time'         => $moment->format('H:i'),
                    'weekday'      => $moment->format('l'),
                    'weekdayIndex' => $weekdayIndex,
                ],
                'panchangLimbs' => [
                    'tithi'    => $this->formatEnum($day->tithiAtSunrise, $lang),
                    'nakshatra' => $this->formatEnum($nakshatra, $lang),
                    'yoga'     => $this->formatEnum($day->yogaAtSunrise, $lang),
                    'karana'   => $this->formatEnum($karana, $lang),
                    'vara'     => $this->formatEnum($day->vara, $lang),
                    'paksha'   => $this->formatEnum($day->paksha, $lang),
                ],
                'electional' => [
                    'amritSiddhiYoga'    => $evaluator->hasAmritSiddhiYoga($weekdayIndex, $nakshatra),
                    'sarvarthaSiddhiYoga' => $evaluator->hasSarvarthaSiddhiYoga($weekdayIndex, $nakshatra),
                    'guruPushyaYoga'     => $evaluator->hasGuruPushyaYoga($weekdayIndex, $nakshatra),
                    'raviPushyaYoga'     => $evaluator->hasRaviPushyaYoga($weekdayIndex, $nakshatra),
                    'dishaShool'         => $evaluator->getDishaShool($weekdayIndex),
                    'panchakaDosha'      => $evaluator->isPanchakaDosha($nakshatra),
                    'isBhadraActive'     => $evaluator->isBhadraActive($karana),
                ],
                // Computed overall auspiciousness summary
                'summary' => [
                    'auspicious'    => $evaluator->hasAmritSiddhiYoga($weekdayIndex, $nakshatra)
                                    || $evaluator->hasSarvarthaSiddhiYoga($weekdayIndex, $nakshatra)
                                    || $evaluator->hasGuruPushyaYoga($weekdayIndex, $nakshatra)
                                    || $evaluator->hasRaviPushyaYoga($weekdayIndex, $nakshatra),
                    'inauspicious'  => $evaluator->isBhadraActive($karana)
                                    || $evaluator->isPanchakaDosha($nakshatra),
                ],
            ];
        });

        return response()->json($data);
    }

    // -------------------------------------------------------------------------
    // 4. Pure Astronomy Report
    // -------------------------------------------------------------------------

    /**
     * GET /api/astronomy
     *
     * Query params: date, tz, lat, lon, elev, ayanamsha, lang (standard params)
     *
     * Returns raw astronomical data for the date and location:
     *   solar events, lunar events, ayanamsha, sidereal time,
     *   Sun sidereal longitude, Moon sidereal longitude, solar month.
     *
     * No Panchang (Tithi, Nakshatra, etc.) is included.
     */
    public function astronomy(Request $request): JsonResponse
    {
        $input    = $this->input($request);
        $lang     = $input['lang'];
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz']));
        $loc      = $this->location($input);

        $key = 'api:astronomy:' . $date->format('Ymd') . ':' . $loc->latitude . ':' . $loc->longitude
            . ':' . $loc->elevationMeters . ':' . $date->getTimezone()->getName() . ':' . $ayanamsha->value;

        $data = $this->cached($key, function () use ($date, $loc, $ayanamsha, $lang) {
            $service = PanchangService::createDefault($ayanamsha);
            $result  = $service->getAstronomyReport($date, $loc);

            $fmt = static fn($v) => $v?->format(DATE_ATOM);
            $tz  = $date->getTimezone();

            return [
                'date'     => $date->format('Y-m-d'),
                'timezone' => $tz->getName(),
                'location' => [
                    'latitude'        => $loc->latitude,
                    'longitude'       => $loc->longitude,
                    'elevationMeters' => $loc->elevationMeters,
                ],
                'solar' => [
                    'sunrise'          => $fmt($result->solarEvents->sunrise?->setTimezone($tz)),
                    'solarNoon'        => $fmt($result->solarEvents->solarNoon?->setTimezone($tz)),
                    'sunset'           => $fmt($result->solarEvents->sunset?->setTimezone($tz)),
                    'dayLengthSeconds' => $result->solarEvents->dayLengthSeconds(),
                    'siderealLongitude' => round($result->sunSiderealLongitude, 6),
                ],
                'lunar' => [
                    'moonrise'         => $fmt($result->lunarEvents->moonrise?->setTimezone($tz)),
                    'transit'          => $fmt($result->lunarEvents->transit?->setTimezone($tz)),
                    'moonset'          => $fmt($result->lunarEvents->moonset?->setTimezone($tz)),
                    'siderealLongitude' => round($result->moonSiderealLongitude, 6),
                ],
                'ayanamsha'    => round($result->ayanamsha, 6),
                'siderealTime' => round($result->siderealTime, 6),
                'solarMonth'   => [
                    'name'    => PanchangTranslator::translateWith($result->solarMonth, $lang)
                                  ?? $result->solarMonth->nameEnglish(),
                    'western' => $result->solarMonth->westernName(),
                    'index'   => $result->solarMonth->value,
                ],
            ];
        });

        return response()->json($data);
    }

    // -------------------------------------------------------------------------
    // 5. Moon Sign (Janmarashi)
    // -------------------------------------------------------------------------

    /**
     * GET /api/moon-sign
     *
     * Query params: date, time, tz, ayanamsha, lang (standard params)
     * Location not required (Moon longitude is geocentric).
     *
     * Returns:
     *   { rashi: { index, name }, moonSiderealLongitude, degreeInSign }
     */
    public function moonSign(Request $request): JsonResponse
    {
        $input    = $this->input($request);
        $lang     = $input['lang'];
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $moment   = $this->makeMoment($input);

        $key = 'api:moon-sign:' . $moment->format(DATE_ATOM) . ':' . $ayanamsha->value . ':' . $lang;

        $data = $this->cached($key, function () use ($moment, $ayanamsha, $lang) {
            $engine   = EngineFactory::createPurePhpEngine($ayanamsha);
            $factory  = new AstroTimeFactory();
            $calc     = new JanmarashiCalculator($engine);
            $instant  = $factory->fromDateTime($moment);

            $rashi       = $calc->calculate($instant);
            $rashiIndex  = $calc->index($instant);
            $moonLon     = Angle::normalize($engine->siderealLongitude(Body::Moon, $instant));
            $degInSign   = fmod($moonLon, 30.0);

            return [
                'rashi' => [
                    'index' => $rashiIndex,
                    'name'  => PanchangTranslator::translateWith($rashi, $lang) ?? $rashi->name,
                ],
                'moonSiderealLongitude' => round($moonLon, 6),
                'degreeInSign'          => round($degInSign, 4),
            ];
        });

        return response()->json(array_merge(
            ['instant' => $moment->format(DATE_ATOM)],
            $data
        ));
    }
}
