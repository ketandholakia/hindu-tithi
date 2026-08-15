<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FestivalDefinition;
use App\Http\Resources\FestivalResource;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    /**
     * Display a listing of active festivals.
     */
    public function index(Request $request)
    {
        $query = FestivalDefinition::with(['rules', 'aliases'])->where('enabled', true);

        if ($request->has('month')) {
            $query->whereHas('rules', function ($q) use ($request) {
                $q->where('month', strtoupper($request->query('month')));
            });
        }

        return FestivalResource::collection($query->paginate(50));
    }

    /**
     * Display the specified festival by code.
     */
    public function show(string $code)
    {
        $festival = FestivalDefinition::with(['rules', 'aliases'])
            ->where('code', strtoupper($code))
            ->where('enabled', true)
            ->firstOrFail();

        return new FestivalResource($festival);
    }
}
