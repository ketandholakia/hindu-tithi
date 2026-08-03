<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PanchangTranslator;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;

class PanchangApiController extends Controller
{
    public function index()
    {
        return response()->json([
            'name' => 'Hindutithi Panchang Test API',
            'version' => 'v1',
            'endpoints' => [
                [
                    'method' => 'GET',
                    'path' => '/api/day',
                    'description' => 'Daily Panchang summary for a date, timezone, and location.',
                    'query' => ['date', 'tz', 'lat', 'lon', 'elev'],
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/moment',
                    'description' => 'Panchang values for a specific instant.',
                    'query' => ['date', 'time', 'tz', 'lat', 'lon', 'elev'],
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/calendar',
                    'description' => 'Hindu calendar summary for a date.',
                    'query' => ['date', 'tz'],
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/muhurta',
                    'description' => 'Day muhurtas derived from sunrise and sunset.',
                    'query' => ['date', 'tz', 'lat', 'lon', 'elev'],
                ],
                [
                    'method' => 'GET',
                    'path' => '/api/electional',
                    'description' => 'Available electional evaluator capabilities.',
                    'query' => [],
                ],
            ],
        ]);
    }

    public function examples(Request $request)
    {
        $input = $this->input($request);
        $base = [
            'date' => $input['date'],
            'time' => $input['time'],
            'tz'   => $input['tz'],
            'lat'  => $input['lat'],
            'lon'  => $input['lon'],
            'elev' => $input['elev'],
            'lang' => $input['lang'],
        ];

        $query = http_build_query($base);

        return response()->json([
            'base_input'         => $base,
            'supported_languages' => PanchangTranslator::SUPPORTED,
            'examples'           => [
                [
                    'name' => 'day',
                    'url'  => url("/api/day?$query"),
                ],
                [
                    'name' => 'moment',
                    'url'  => url("/api/moment?$query"),
                ],
                [
                    'name' => 'calendar',
                    'url'  => url("/api/calendar?date={$base['date']}&tz={$base['tz']}&lang={$base['lang']}"),
                ],
                [
                    'name' => 'muhurta',
                    'url'  => url("/api/muhurta?date={$base['date']}&tz={$base['tz']}&lat={$base['lat']}&lon={$base['lon']}&elev={$base['elev']}&lang={$base['lang']}"),
                ],
                [
                    'name' => 'electional',
                    'url'  => url('/api/electional'),
                ],
            ],
        ]);
    }

    private function input(Request $request): array
    {
        return [
            'date' => $request->query('date', date('Y-m-d')),
            'time' => $request->query('time', '06:00'),
            'tz'   => $request->query('tz', 'Asia/Kolkata'),
            'lat'  => $request->query('lat', 23.0225),
            'lon'  => $request->query('lon', 72.5714),
            'elev' => $request->query('elev', 0),
            'lang' => PanchangTranslator::validLang((string) $request->query('lang', 'en')),
        ];
    }

    private function makeMoment(array $input): DateTimeImmutable
    {
        return new DateTimeImmutable($input['date'].' '.$input['time'], new DateTimeZone($input['tz']));
    }

    private function location(array $input): GeoLocation
    {
        return new GeoLocation((float) $input['lat'], (float) $input['lon'], (float) $input['elev']);
    }

    private function cached(string $key, callable $fn)
    {
        return Cache::rememberForever($key, $fn);
    }

    private function formatEnum($value, string $lang = 'en'): ?string
    {
        return PanchangTranslator::translateWith($value, $lang);
    }

    private function dayToArray($day, string $lang = 'en'): array
    {
        $fmt = static fn ($v) => $v?->format(DATE_ATOM);

        return [
            'date'     => $fmt($day->date),
            'timezone' => $day->timezone->getName(),
            'location' => [
                'latitude'        => $day->location->latitude,
                'longitude'       => $day->location->longitude,
                'elevationMeters' => $day->location->elevationMeters,
            ],
            'solarEvents' => [
                'sunrise'   => $fmt($day->solarEvents->sunrise),
                'solarNoon' => $fmt($day->solarEvents->solarNoon),
                'sunset'    => $fmt($day->solarEvents->sunset),
            ],
            'tithiAtSunrise'     => $this->formatEnum($day->tithiAtSunrise, $lang),
            'nakshatraAtSunrise' => $this->formatEnum($day->nakshatraAtSunrise, $lang),
            'padaAtSunrise'      => $day->padaAtSunrise,
            'yogaAtSunrise'      => $this->formatEnum($day->yogaAtSunrise, $lang),
            'karanaAtSunrise'    => $this->formatEnum($day->karanaAtSunrise, $lang),
            'vara'               => $this->formatEnum($day->vara, $lang),
            'tithiEndsAt'        => $fmt($day->tithiEndsAt),
            'nakshatraEndsAt'    => $fmt($day->nakshatraEndsAt),
            'yogaEndsAt'         => $fmt($day->yogaEndsAt),
            'karanaEndsAt'       => $fmt($day->karanaEndsAt),
            'tithiSecond'        => $this->formatEnum($day->tithiSecond, $lang),
            'tithiSecondEndsAt'  => $fmt($day->tithiSecondEndsAt),
            'lunarMonth'         => $this->formatEnum($day->lunarMonth, $lang),
            'paksha'             => $this->formatEnum($day->paksha, $lang),
            'isAdhikaMasa'       => $day->isAdhikaMasa,
        ];
    }

    private function calendarToArray($calendar, string $lang = 'en'): array
    {
        $fmt = static fn ($v) => $v?->format(DATE_ATOM);

        return [
            'amantaMonth'     => $this->formatEnum($calendar->amantaMonth, $lang),
            'purnimantaMonth' => $this->formatEnum($calendar->purnimantaMonth, $lang),
            'isAdhikaMasa'    => $calendar->isAdhikaMasa,
            'previousNewMoon' => $fmt($calendar->previousNewMoon),
            'nextNewMoon'     => $fmt($calendar->nextNewMoon),
            'nextFullMoon'    => $fmt($calendar->nextFullMoon),
            'vikramSamvat'    => $calendar->vikramSamvat,
            'shakaSamvat'     => $calendar->shakaSamvat,
        ];
    }

    private function momentToArray($moment, string $lang = 'en'): array
    {
        $fmt = static fn ($v) => $v?->format(DATE_ATOM);

        return [
            'instant' => [
                'utc'      => $fmt($moment->instant->utc),
                'local'    => $fmt($moment->instant->local),
                'timezone' => $moment->instant->timezone->getName(),
            ],
            'tithi'         => $this->formatEnum($moment->tithi, $lang),
            'nakshatra'     => $this->formatEnum($moment->nakshatra, $lang),
            'pada'          => $moment->pada,
            'yoga'          => $this->formatEnum($moment->yoga, $lang),
            'karana'        => $this->formatEnum($moment->karana, $lang),
            'vara'          => $this->formatEnum($moment->vara, $lang),
            'sunLongitude'  => $moment->sunLongitude,
            'moonLongitude' => $moment->moonLongitude,
            'ayanamsha'     => $moment->ayanamsha,
            'accuracy' => [
                'estimatedAngularErrorDegrees' => $moment->accuracy->estimatedAngularErrorDegrees,
                'estimatedTimeErrorSeconds'    => $moment->accuracy->estimatedTimeErrorSeconds,
                'nearBoundary'                 => $moment->accuracy->nearBoundary,
            ],
        ];
    }

    public function day(Request $request)
    {
        $input    = $this->input($request);
        $panchang = app(Panchang::class);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz']));
        $loc      = $this->location($input);
        $lang     = $input['lang'];
        $key      = "api:v2:day:{$date->format('Ymd')}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$date->getTimezone()->getName()}:{$lang}";

        return response()->json($this->cached($key, fn () => $this->dayToArray($panchang->day($date, $loc), $lang)));
    }

    public function moment(Request $request)
    {
        $input    = $this->input($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->location($input);
        $lang     = $input['lang'];
        $key      = "api:moment:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$moment->getTimezone()->getName()}:{$lang}";

        return response()->json($this->cached($key, fn () => $this->momentToArray($panchang->moment($moment, $loc), $lang)));
    }

    public function calendar(Request $request)
    {
        $input    = $this->input($request);
        $panchang = app(Panchang::class);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz']));
        $lang     = $input['lang'];
        $key      = "api:v2:calendar:{$date->format('Ymd')}:{$date->getTimezone()->getName()}:{$lang}";

        return response()->json($this->cached($key, fn () => $this->calendarToArray($panchang->calendar($date), $lang)));
    }

    public function muhurta(Request $request)
    {
        $input    = $this->input($request);
        $panchang = app(Panchang::class);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz']));
        $loc      = $this->location($input);
        $lang     = $input['lang'];
        $day      = $this->cached(
            "api:v2:day:{$date->format('Ymd')}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$date->getTimezone()->getName()}:{$lang}",
            fn () => $this->dayToArray($panchang->day($date, $loc), $lang)
        );

        $sunrise = $day['solarEvents']['sunrise'] ? new DateTimeImmutable($day['solarEvents']['sunrise']) : null;
        $sunset  = $day['solarEvents']['sunset']  ? new DateTimeImmutable($day['solarEvents']['sunset'])  : null;

        if ($sunrise === null || $sunset === null) {
            return response()->json([
                'day'      => $day,
                'muhurtas' => [],
            ]);
        }

        $muhurta = $panchang->muhurta();

        return response()->json([
            'day'      => $day,
            'muhurtas' => array_map(static fn ($item) => [
                'name'  => $item->name,
                'start' => $item->start->format(DATE_ATOM),
                'end'   => $item->end->format(DATE_ATOM),
            ], $muhurta->getDayMuhurtas($sunrise, $sunset)),
        ]);
    }

    public function electional(Request $request)
    {
        $panchang = app(Panchang::class);

        return response()->json([
            'evaluator' => get_class($panchang->electional()),
            'checks' => [
                'hasAmritSiddhiYoga',
                'hasSarvarthaSiddhiYoga',
                'hasGuruPushyaYoga',
                'hasRaviPushyaYoga',
                'getDishaShool',
                'isPanchakaDosha',
                'isBhadraActive',
            ],
        ]);
    }
}
