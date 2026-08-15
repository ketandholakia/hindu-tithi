<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PanchangTranslator;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\ValueObject\GeoLocation;

class CompatibilityController extends Controller
{
    private function input(Request $request): array
    {
        return [
            'date' => $request->query('date', date('Y-m-d')),
            'tz'   => $request->query('timezone', 'Asia/Kolkata'),
            'lat'  => $request->query('latitude', 23.0225),
            'lon'  => $request->query('longitude', 72.5714),
            'lang' => PanchangTranslator::validLang((string) $request->query('lang', 'en')),
        ];
    }

    private function location(array $input): GeoLocation
    {
        return new GeoLocation((float) $input['lat'], (float) $input['lon'], 0);
    }

    private function formatEnum($value, string $lang = 'en'): ?string
    {
        return PanchangTranslator::translateWith($value, $lang);
    }

    public function todayPanchang(Request $request, Panchang $panchang)
    {
        $input = $this->input($request);
        $date = new DateTimeImmutable('today', new DateTimeZone($input['tz']));
        $loc = $this->location($input);
        
        $day = $panchang->day($date, $loc);
        $calendar = $panchang->calendar($date);
        
        $lang = $input['lang'];

        $fmtTime = fn($dt) => $dt ? $dt->format('H:i') : null;

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'location' => "Lat: {$loc->latitude}, Lon: {$loc->longitude}",
                'date' => $date->format('Y-m-d'),
                'day' => $this->formatEnum($day->vara, $lang) ?? $date->format('l'),
                'sunrise' => $fmtTime($day->solarEvents->sunrise),
                'sunset' => $fmtTime($day->solarEvents->sunset),
                'moonrise' => null,
                'moonset' => null,
                'tithi' => $this->formatEnum($day->tithiAtSunrise, $lang),
                'tithiEndTime' => $day->tithiEndsAt ? $day->tithiEndsAt->format(DATE_ATOM) : null,
                'nakshatra' => $this->formatEnum($day->nakshatraAtSunrise, $lang),
                'nakshatra_pada' => (string) $day->padaAtSunrise,
                'yoga' => $this->formatEnum($day->yogaAtSunrise, $lang),
                'karana' => $this->formatEnum($day->karanaAtSunrise, $lang),
                'paksha' => $this->formatEnum($day->paksha, $lang),
                'sunRashi' => null,
                'moonRashi' => null,
                'vikramSamvat' => (string) $calendar->vikramSamvat,
                'shakaSamvat' => (string) $calendar->shakaSamvat,
                'mass' => $this->formatEnum($calendar->amantaMonth, $lang),
                'rasu' => null,
                'season' => null,
                'ayanamsha' => null,
                'festival' => null,
                'vrat' => null,
                'muhurta' => null,
                'rahu_kaal' => null,
                'yamaganda' => null,
                'gulikai' => null,
                'abhijit_muhurta' => null,
                'brahma_muhurta' => null,
                'godhuli' => null,
            ]
        ]);
    }
    
    public function dayPanchang(Request $request, Panchang $panchang)
    {
        // For day panchang, we use the requested date
        $input = $this->input($request);
        $date = new DateTimeImmutable($input['date'], new DateTimeZone($input['tz']));
        $loc = $this->location($input);
        
        $day = $panchang->day($date, $loc);
        $calendar = $panchang->calendar($date);
        
        $lang = $input['lang'];
        $fmtTime = fn($dt) => $dt ? $dt->format('H:i') : null;

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'location' => "Lat: {$loc->latitude}, Lon: {$loc->longitude}",
                'date' => $date->format('Y-m-d'),
                'day' => $this->formatEnum($day->vara, $lang) ?? $date->format('l'),
                'sunrise' => $fmtTime($day->solarEvents->sunrise),
                'sunset' => $fmtTime($day->solarEvents->sunset),
                'moonrise' => null,
                'moonset' => null,
                'tithi' => $this->formatEnum($day->tithiAtSunrise, $lang),
                'tithiEndTime' => $day->tithiEndsAt ? $day->tithiEndsAt->format(DATE_ATOM) : null,
                'nakshatra' => $this->formatEnum($day->nakshatraAtSunrise, $lang),
                'nakshatra_pada' => (string) $day->padaAtSunrise,
                'yoga' => $this->formatEnum($day->yogaAtSunrise, $lang),
                'karana' => $this->formatEnum($day->karanaAtSunrise, $lang),
                'paksha' => $this->formatEnum($day->paksha, $lang),
                'sunRashi' => null,
                'moonRashi' => null,
                'vikramSamvat' => (string) $calendar->vikramSamvat,
                'shakaSamvat' => (string) $calendar->shakaSamvat,
                'mass' => $this->formatEnum($calendar->amantaMonth, $lang),
                'rasu' => null,
                'season' => null,
                'ayanamsha' => null,
                'festival' => null,
                'vrat' => null,
                'muhurta' => null,
                'rahu_kaal' => null,
                'yamaganda' => null,
                'gulikai' => null,
                'abhijit_muhurta' => null,
                'brahma_muhurta' => null,
                'godhuli' => null,
            ]
        ]);
    }

    public function monthCalendar(Request $request, Panchang $panchang)
    {
        // MonthCalendarResponse expects a list of days
        $year = (int) $request->query('year', date('Y'));
        $month = (int) $request->query('month', date('n'));
        $input = $this->input($request);
        $lang = $input['lang'];
        $tz = new DateTimeZone($input['tz']);
        $loc = $this->location($input);
        
        $start = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month), $tz);
        $daysInMonth = (int) $start->format('t');
        
        $days = [];
        for ($i = 0; $i < $daysInMonth; $i++) {
            $currentDate = $start->modify("+{$i} days");
            $dayData = $panchang->day($currentDate, $loc);
            
            $days[] = [
                'date' => $currentDate->format('Y-m-d'),
                'tithi' => $this->formatEnum($dayData->tithiAtSunrise, $lang),
                'festival' => null,
                'isFasting' => false,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'year' => $year,
                'month' => $month,
                'days' => $days,
            ]
        ]);
    }

    public function todayMuhurta(Request $request, Panchang $panchang)
    {
        $input = $this->input($request);
        $date = new DateTimeImmutable('today', new DateTimeZone($input['tz']));
        $loc = $this->location($input);
        
        $day = $panchang->day($date, $loc);
        $sunrise = $day->solarEvents->sunrise;
        $sunset = $day->solarEvents->sunset;
        
        $muhurtaList = [];
        if ($sunrise && $sunset) {
            $muhurtas = $panchang->muhurta()->getDayMuhurtas($sunrise, $sunset);
            foreach ($muhurtas as $m) {
                $muhurtaList[] = [
                    'name' => $m->name,
                    'startTime' => $m->start->format('H:i:s'),
                    'endTime' => $m->end->format('H:i:s'),
                    'isAuspicious' => true, // Simple default
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'date' => $date->format('Y-m-d'),
                'muhurtas' => $muhurtaList,
            ]
        ]);
    }

    public function festivals(Request $request)
    {
        // Mocked response for features the backend no longer has
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'festivals' => []
            ]
        ]);
    }

    public function kundli(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'lagna' => 'Unknown',
                'planets' => [],
            ]
        ]);
    }

    public function planetPositions(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'positions' => []
            ]
        ]);
    }

    public function settings(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'language' => 'en',
                'notificationsEnabled' => true,
                'defaultLocation' => null,
            ]
        ]);
    }
}
