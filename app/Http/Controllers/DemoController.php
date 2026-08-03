<?php

namespace App\Http\Controllers;

use App\Services\PanchangTranslator;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;

class DemoController
{
    private function getInput(Request $request): array
    {
        $session = $request->session();

        $defaults = [
            'date' => $session->get('date', date('Y-m-d')),
            'time' => $session->get('time', '06:00'),
            'tz'   => $session->get('tz', 'Asia/Kolkata'),
            'lat'  => $session->get('lat', 23.0225),
            'lon'  => $session->get('lon', 72.5714),
            'elev' => $session->get('elev', 0),
            'lang' => $session->get('lang', 'en'),
        ];

        return array_merge($defaults, $request->only(['date', 'time', 'tz', 'lat', 'lon', 'elev', 'lang']));
    }

    public function home(Request $request)
    {
        $input = $this->getInput($request);
        return view('hindutithi.home', ['input' => $input]);
    }

    public function setBirth(Request $request)
    {
        $data = $request->only(['date', 'time', 'tz', 'lat', 'lon', 'elev', 'lang']);
        foreach ($data as $k => $v) {
            $request->session()->put($k, $v);
        }
        return redirect()->back();
    }

    private function makeMoment(array $input, ?string $timeOverride = null): DateTimeImmutable
    {
        $time = $timeOverride ?? ($input['time'] ?? '06:00');
        $tz   = new DateTimeZone($input['tz'] ?? 'UTC');
        return new DateTimeImmutable($input['date'] . ' ' . $time, $tz);
    }

    private function makeLocation(array $input): GeoLocation
    {
        return new GeoLocation((float) $input['lat'], (float) $input['lon'], (float) ($input['elev'] ?? 0.0));
    }

    private function cached(string $key, callable $fn)
    {
        return Cache::rememberForever($key, $fn);
    }

    private function dayToArray($day, PanchangTranslator $tr): array
    {
        $fmt = static fn($v) => $v?->format(DATE_ATOM);

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
            'tithiAtSunrise'     => $tr->translate($day->tithiAtSunrise),
            'nakshatraAtSunrise' => $tr->translate($day->nakshatraAtSunrise),
            'padaAtSunrise'      => $day->padaAtSunrise,
            'yogaAtSunrise'      => $tr->translate($day->yogaAtSunrise),
            'karanaAtSunrise'    => $tr->translate($day->karanaAtSunrise),
            'vara'               => $tr->translate($day->vara),
            'tithiEndsAt'        => $fmt($day->tithiEndsAt),
            'nakshatraEndsAt'    => $fmt($day->nakshatraEndsAt),
            'yogaEndsAt'         => $fmt($day->yogaEndsAt),
            'karanaEndsAt'       => $fmt($day->karanaEndsAt),
            'tithiSecond'        => $tr->translate($day->tithiSecond),
            'tithiSecondEndsAt'  => $fmt($day->tithiSecondEndsAt),
            'lunarMonth'         => $tr->translate($day->lunarMonth),
            'paksha'             => $tr->translate($day->paksha),
            'isAdhikaMasa'       => $day->isAdhikaMasa,
            'accuracy'           => ['class' => $day->accuracy::class],
        ];
    }

    private function calendarToArray($calendar, PanchangTranslator $tr): array
    {
        $fmt = static fn($v) => $v?->format(DATE_ATOM);

        return [
            'amantaMonth'     => $tr->translate($calendar->amantaMonth),
            'purnimantaMonth' => $tr->translate($calendar->purnimantaMonth),
            'isAdhikaMasa'    => $calendar->isAdhikaMasa,
            'previousNewMoon' => $fmt($calendar->previousNewMoon),
            'nextNewMoon'     => $fmt($calendar->nextNewMoon),
            'nextFullMoon'    => $fmt($calendar->nextFullMoon),
            'vikramSamvat'    => $calendar->vikramSamvat,
            'shakaSamvat'     => $calendar->shakaSamvat,
        ];
    }

    public function day(Request $request)
    {
        $input    = $this->getInput($request);
        $tr       = PanchangTranslator::fromSession();
        $panchang = app(Panchang::class);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz'] ?? 'UTC'));
        $loc      = $this->makeLocation($input);

        // Cache key is language-aware so each lang gets its own entry
        $key = "panchang:v2:day:{$date->format('Ymd')}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$date->getTimezone()->getName()}:{$input['lang']}";
        $day = $this->cached($key, fn() => $this->dayToArray($panchang->day($date, $loc), $tr));

        return view('hindutithi.sections.day', ['day' => $day, 'input' => $input]);
    }

    public function moment(Request $request)
    {
        $input    = $this->getInput($request);
        $tr       = PanchangTranslator::fromSession();
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->makeLocation($input);

        $key       = "panchang:moment:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$moment->getTimezone()->getName()}";
        $momentObj = $this->cached($key, fn() => $panchang->moment($moment, $loc));

        $dayDate = new DateTimeImmutable($moment->format('Y-m-d'), $moment->getTimezone());
        $dayKey  = "panchang:v2:day:{$dayDate->format('Ymd')}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$dayDate->getTimezone()->getName()}:{$input['lang']}";
        $dayObj  = $this->cached($dayKey, fn() => $this->dayToArray($panchang->day($dayDate, $loc), $tr));

        return view('hindutithi.sections.moment', ['moment' => $momentObj, 'day' => $dayObj, 'input' => $input, 'tr' => $tr]);
    }

    public function janmarashi(Request $request)
    {
        $input    = $this->getInput($request);
        $tr       = PanchangTranslator::fromSession();
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);

        $key   = "panchang:janmarashi:{$moment->format(DATE_ATOM)}:{$moment->getTimezone()->getName()}";
        $rashi = $this->cached($key, fn() => $panchang->janmarashi($moment));

        return view('hindutithi.sections.janmarashi', ['rashi' => $rashi, 'input' => $input, 'tr' => $tr]);
    }

    public function ascendant(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->makeLocation($input);

        if (!method_exists($panchang, 'ascendant')) {
            return view('hindutithi.sections.unavailable', ['feature' => 'Ascendant (ascendant)']);
        }

        $key = "panchang:ascendant:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}";
        $asc = $this->cached($key, fn() => $panchang->ascendant($moment, $loc));

        return view('hindutithi.sections.ascendant', ['ascendant' => $asc, 'input' => $input]);
    }

    public function kundali(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->makeLocation($input);

        if (!method_exists($panchang, 'kundali')) {
            return view('hindutithi.sections.unavailable', ['feature' => 'Kundali (kundali)']);
        }

        $key    = "panchang:kundali:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}";
        $kundali = $this->cached($key, fn() => $panchang->kundali($moment, $loc));

        $houses = array_fill(1, 12, []);
        foreach ($kundali->placements as $placement) {
            $houses[$placement->house][] = $placement->body->name;
        }

        return view('hindutithi.sections.kundali', ['kundali' => $kundali, 'houses' => $houses, 'input' => $input]);
    }

    public function varga(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->makeLocation($input);

        if (!method_exists($panchang, 'varga')) {
            return view('hindutithi.sections.unavailable', ['feature' => 'Varga (varga)']);
        }

        $varga = $request->get('varga', 'D9');
        $key   = "panchang:varga:{$varga}:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}";
        $k     = $this->cached($key, fn() => $panchang->varga($moment, $loc, \Vittix\Panchang\Enum\Varga::from($varga)));

        $houses = array_fill(1, 12, []);
        foreach ($k->placements as $placement) {
            $houses[$placement->house][] = $placement->body->name;
        }

        return view('hindutithi.sections.kundali', ['kundali' => $k, 'houses' => $houses, 'input' => $input, 'varga' => $varga]);
    }

    public function vimshottari(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);

        if (!method_exists($panchang, 'vimshottariDasha')) {
            return view('hindutithi.sections.unavailable', ['feature' => 'Vimshottari Dasha (vimshottariDasha)']);
        }

        $key    = "panchang:vimshottari:{$moment->format(DATE_ATOM)}";
        $dashas = $this->cached($key, fn() => $panchang->vimshottariDasha($moment));

        return view('hindutithi.sections.vimshottari', ['dashas' => $dashas, 'input' => $input]);
    }

    public function shadbala(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->makeLocation($input);

        if (!method_exists($panchang, 'shadbala')) {
            return view('hindutithi.sections.unavailable', ['feature' => 'Shadbala (shadbala)']);
        }

        $key      = "panchang:shadbala:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}";
        $shadbala = $this->cached($key, fn() => $panchang->shadbala($moment, $loc));

        return view('hindutithi.sections.shadbala', ['shadbala' => $shadbala, 'input' => $input]);
    }

    public function yogas(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $moment   = $this->makeMoment($input);
        $loc      = $this->makeLocation($input);

        if (!method_exists($panchang, 'yogas')) {
            return view('hindutithi.sections.unavailable', ['feature' => 'Yogas (yogas)']);
        }

        $key   = "panchang:yogas:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}";
        $yogas = $this->cached($key, fn() => $panchang->yogas($moment, $loc));

        return view('hindutithi.sections.yogas', ['yogas' => $yogas, 'input' => $input]);
    }

    public function calendar(Request $request)
    {
        $input    = $this->getInput($request);
        $tr       = PanchangTranslator::fromSession();
        $panchang = app(Panchang::class);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz'] ?? 'UTC'));

        $key = "panchang:v2:calendar:{$date->format('Ymd')}:{$date->getTimezone()->getName()}:{$input['lang']}";
        $cal = $this->cached($key, fn() => $this->calendarToArray($panchang->calendar($date), $tr));

        return view('hindutithi.sections.calendar', ['calendar' => $cal, 'input' => $input]);
    }

    public function festivals(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $loc      = $this->makeLocation($input);

        $engine = $panchang->festivals();
        $list   = [];
        $start  = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz'] ?? 'UTC'));

        for ($i = 0; $i < 120; $i++) {
            $d        = $start->modify("+$i day");
            $key      = "panchang:festival:{$d->format('Ymd')}:{$loc->latitude}:{$loc->longitude}";
            $festivals = $this->cached($key, fn() => $engine->getFestivalsForDate($d, $loc));
            if (!empty($festivals)) {
                $list[] = ['date' => $d, 'names' => $festivals];
            }
        }

        return view('hindutithi.sections.festivals', ['list' => $list, 'input' => $input]);
    }

    public function muhurta(Request $request)
    {
        $input    = $this->getInput($request);
        $tr       = PanchangTranslator::fromSession();
        $panchang = app(Panchang::class);
        $loc      = $this->makeLocation($input);
        $date     = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz'] ?? 'UTC'));

        $key     = "panchang:v2:day:{$date->format('Ymd')}:{$loc->latitude}:{$loc->longitude}:{$input['lang']}";
        $day     = $this->cached($key, fn() => $this->dayToArray($panchang->day($date, $loc), $tr));
        $muhurta = $panchang->muhurta();

        return view('hindutithi.sections.muhurta', ['muhurta' => $muhurta, 'day' => $day, 'input' => $input]);
    }

    public function electional(Request $request)
    {
        $input    = $this->getInput($request);
        $panchang = app(Panchang::class);
        $e        = $panchang->electional();
        $moment   = $this->makeMoment($input);

        return view('hindutithi.sections.electional', ['e' => $e, 'moment' => $moment, 'input' => $input]);
    }
}
