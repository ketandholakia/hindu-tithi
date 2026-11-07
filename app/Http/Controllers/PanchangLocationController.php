<?php

namespace App\Http\Controllers;

use App\Models\PanchangLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PanchangLocationController
{
    public function index(Request $request)
    {
        return view('hindutithi.locations', ['locations' => $request->user()->panchangLocations()->orderBy('name')->get()]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->validate(['q' => ['required', 'string', 'min:2', 'max:160']])['q']);
        $results = Cache::remember('place-search:' . hash('sha256', mb_strtolower($query)), now()->addDay(), function () use ($query) {
            $response = Http::withHeaders([
                'User-Agent' => 'Hindutithi-Web/1.0 (location search)',
                'Accept-Language' => app()->getLocale(),
            ])->timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query, 'format' => 'jsonv2', 'limit' => 5, 'addressdetails' => 1,
            ]);

            if (!$response->successful()) {
                return [];
            }

            return collect($response->json())->map(fn ($place) => [
                'name' => $place['display_name'] ?? 'Selected place',
                'latitude' => (float) ($place['lat'] ?? 0),
                'longitude' => (float) ($place['lon'] ?? 0),
            ])->filter(fn ($place) => $place['latitude'] !== 0.0 || $place['longitude'] !== 0.0)->values()->all();
        });

        return response()->json(['results' => $results]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'elevation_meters' => ['nullable', 'numeric', 'between:-500,9000'],
            'timezone' => ['required', 'timezone'],
        ]);

        if ($request->user()->panchangLocations()->count() >= 20) {
            return back()->withErrors(['name' => 'You can save up to 20 locations.']);
        }

        $request->user()->panchangLocations()->create($data);

        return back()->with('status', 'Location saved.');
    }

    public function destroy(Request $request, PanchangLocation $location): RedirectResponse
    {
        abort_unless($location->user_id === $request->user()->id, 404);
        $location->delete();

        return back()->with('status', 'Location removed.');
    }
}
