<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FestivalOccurrence;
use App\Models\FestivalDefinition;
use App\Http\Resources\FestivalOccurrenceResource;
use App\Services\Festival\FestivalEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FestivalOccurrenceController extends Controller
{
    /**
     * Display occurrences for a specific year.
     */
    public function byYear(Request $request, int $year)
    {
        $lat = $request->query('lat', 23.0225); // Default Ahmedabad
        $lon = $request->query('lon', 72.5714);
        $calendarSystem = $request->query('calendar_system', 'Amanta');

        // Note: In a production environment, you might want to call the FestivalEngine here
        // if occurrences for this year/location haven't been computed yet.
        // For now, we just query the database.
        
        $occurrences = FestivalOccurrence::with(['definition.rules', 'definition.aliases'])
            ->whereYear('date', $year)
            ->where('calendar_system', $calendarSystem)
            ->orderBy('date')
            ->paginate(50);

        return FestivalOccurrenceResource::collection($occurrences);
    }

    /**
     * Display history/future for a specific festival.
     */
    public function byFestival(Request $request, string $code)
    {
        $festival = FestivalDefinition::where('code', strtoupper($code))->firstOrFail();

        $occurrences = FestivalOccurrence::with(['definition'])
            ->where('festival_id', $festival->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return FestivalOccurrenceResource::collection($occurrences);
    }
}
