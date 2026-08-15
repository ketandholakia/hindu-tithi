<?php

namespace App\Http\Controllers;

use App\Services\FestivalService;
use App\Services\PanchangTranslator;
use Illuminate\Http\Request;
use Vittix\Panchang\ValueObject\GeoLocation;

class FestivalController extends Controller
{
    public function __construct(private FestivalService $festivalService) {}

    private function getInput(Request $request): array
    {
        $session = $request->session();

        $defaults = [
            'date' => $session->get('date', date('Y-m-d')),
            'tz'   => $session->get('tz', 'Asia/Kolkata'),
            'lat'  => $session->get('lat', 23.0225),
            'lon'  => $session->get('lon', 72.5714),
            'elev' => $session->get('elev', 0),
            'lang' => $session->get('lang', 'en'),
        ];

        return array_merge($defaults, $request->only(['date', 'tz', 'lat', 'lon', 'elev', 'lang']));
    }

    public function index(Request $request)
    {
        $input = $this->getInput($request);
        $location = new GeoLocation((float) $input['lat'], (float) $input['lon'], (float) ($input['elev'] ?? 0.0));
        
        $targetMonth = (int) $request->input('month', date('n', strtotime($input['date'])));
        $targetYear = (int) $request->input('year', date('Y', strtotime($input['date'])));

        $daysWithFestivals = $this->festivalService->getFestivalsForMonth(
            $targetMonth,
            $targetYear,
            $location,
            $input['tz'],
            $input['lang']
        );

        // Extract a flat list of upcoming festivals for the "Upcoming Festivals" section
        $upcoming = [];
        $today = date('Y-m-d');
        foreach ($daysWithFestivals as $day) {
            if ($day['date']->format('Y-m-d') >= $today) {
                foreach ($day['festivals'] as $f) {
                    $upcoming[] = [
                        'date' => $day['date'],
                        'festival' => $f,
                        'dayData' => $day['dayData'],
                    ];
                    if (count($upcoming) >= 4) break 2;
                }
            }
        }

        // Define available categories based on the result
        $categories = ['All', 'Major Festivals', 'Ekadashi', 'Purnima', 'Amavasya', 'Sankranti', 'Jayanti', 'Vrat', 'Regional'];

        return view('festivals.index', [
            'input' => $input,
            'daysWithFestivals' => $daysWithFestivals,
            'upcoming' => $upcoming,
            'month' => $targetMonth,
            'year' => $targetYear,
            'categories' => $categories,
            'activeCategory' => $request->input('category', 'All'),
        ]);
    }
}
