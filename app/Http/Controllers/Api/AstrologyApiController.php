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
use Vittix\Panchang\Enum\AyanamshaSystem;
use Vittix\Panchang\Enum\Varga;
use Vittix\Panchang\ValueObject\Kundali;

class AstrologyApiController extends Controller
{
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
            'ayanamsha' => $request->query('ayanamsha', 'lahiri'),
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

    private function kundaliToArray(Kundali $kundali, string $lang = 'en'): array
    {
        $houses = [];
        for ($i = 1; $i <= 12; $i++) {
            $house = $kundali->getHouse($i);
            $planets = array_map(function($p) use ($lang) {
                return [
                    'body' => $this->formatEnum($p->body, $lang),
                    'longitude' => $p->longitude,
                    'isRetrograde' => $p->isRetrograde,
                    'dignity' => $this->formatEnum($p->dignity, $lang)
                ];
            }, $house->planets);

            $houses[] = [
                'number' => $i,
                'rashi' => $this->formatEnum($house->rashi, $lang),
                'planets' => $planets,
            ];
        }

        return [
            'ascendant' => [
                'rashi' => $this->formatEnum($kundali->ascendant->rashi, $lang),
                'longitude' => $kundali->ascendant->longitude,
            ],
            'houses' => $houses,
        ];
    }

    public function kundli(Request $request)
    {
        $input = $this->input($request);
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $panchang = Panchang::createDefault($ayanamsha);
        $moment = $this->makeMoment($input);
        $loc = $this->location($input);
        $lang = $input['lang'];
        $key = "api:v2:kundli:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$ayanamsha->value}:{$lang}";

        return response()->json($this->cached($key, function () use ($panchang, $moment, $loc, $lang) {
            $kundali = $panchang->kundali($moment, $loc);
            return $this->kundaliToArray($kundali, $lang);
        }));
    }

    public function varga(Request $request, string $varga)
    {
        $input = $this->input($request);
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $panchang = Panchang::createDefault($ayanamsha);
        $moment = $this->makeMoment($input);
        $loc = $this->location($input);
        $lang = $input['lang'];
        $vargaEnum = Varga::from($varga);
        $key = "api:v2:varga:{$vargaEnum->value}:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$ayanamsha->value}:{$lang}";

        return response()->json($this->cached($key, function () use ($panchang, $moment, $loc, $vargaEnum, $lang) {
            $kundali = $panchang->varga($moment, $loc, $vargaEnum);
            return $this->kundaliToArray($kundali, $lang);
        }));
    }

    public function yogas(Request $request)
    {
        $input = $this->input($request);
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $panchang = Panchang::createDefault($ayanamsha);
        $moment = $this->makeMoment($input);
        $loc = $this->location($input);
        $lang = $input['lang'];
        $key = "api:v2:yogas:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$ayanamsha->value}:{$lang}";

        return response()->json($this->cached($key, function () use ($panchang, $moment, $loc, $lang) {
            $yogas = $panchang->yogas($moment, $loc);
            return array_map(function($yoga) use ($lang) {
                return [
                    'name' => $yoga->name,
                    'type' => $yoga->type,
                    'description' => $yoga->description,
                    'planets' => array_map(fn($p) => $this->formatEnum($p, $lang), $yoga->participatingPlanets),
                ];
            }, $yogas);
        }));
    }

    public function shadbala(Request $request)
    {
        $input = $this->input($request);
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $panchang = Panchang::createDefault($ayanamsha);
        $moment = $this->makeMoment($input);
        $loc = $this->location($input);
        $lang = $input['lang'];
        $key = "api:v2:shadbala:{$moment->format(DATE_ATOM)}:{$loc->latitude}:{$loc->longitude}:{$loc->elevationMeters}:{$ayanamsha->value}:{$lang}";

        return response()->json($this->cached($key, function () use ($panchang, $moment, $loc, $lang) {
            $shadbala = $panchang->shadbala($moment, $loc);
            $result = [];
            foreach ($shadbala->planetStrengths as $body => $strength) {
                $result[$this->formatEnum($body, $lang)] = [
                    'totalRupas' => $strength->totalRupas(),
                    'isStrong' => $strength->isStrong(),
                    'components' => [
                        'sthana' => $strength->sthana->total(),
                        'dik' => $strength->dik->total(),
                        'kala' => $strength->kala->total(),
                        'chesta' => $strength->chesta->total(),
                        'naisargika' => $strength->naisargika->total(),
                        'drik' => $strength->drik->total(),
                    ]
                ];
            }
            return $result;
        }));
    }

    public function dasha(Request $request)
    {
        $input = $this->input($request);
        $ayanamsha = AyanamshaSystem::from($input['ayanamsha']);
        $panchang = Panchang::createDefault($ayanamsha);
        $moment = $this->makeMoment($input);
        $key = "api:v2:dasha:{$moment->format(DATE_ATOM)}:{$ayanamsha->value}";

        return response()->json($this->cached($key, function () use ($panchang, $moment) {
            $dashas = $panchang->vimshottariDasha($moment);
            return array_map(function($dasha) {
                return [
                    'planet' => $dasha->planet->name,
                    'start' => $dasha->start->format(DATE_ATOM),
                    'end' => $dasha->end->format(DATE_ATOM),
                    'durationYears' => $dasha->durationYears,
                    'antardashas' => array_map(function($ad) {
                        return [
                            'planet' => $ad->planet->name,
                            'start' => $ad->start->format(DATE_ATOM),
                            'end' => $ad->end->format(DATE_ATOM),
                        ];
                    }, $dasha->antardashas)
                ];
            }, $dashas);
        }));
    }
}
