<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PanchangApiController;
use App\Http\Controllers\Api\AstrologyApiController;
use App\Http\Controllers\Api\PanchangExtendedApiController;
use App\Http\Controllers\Api\V1\CompatibilityController;
use App\Http\Controllers\Api\V1\FestivalController;
use App\Http\Controllers\Api\V1\FestivalOccurrenceController;

Route::middleware(['auth.api_token', 'throttle.api_token', 'log.api_usage'])->group(function () {
    Route::get('/', [PanchangApiController::class, 'index']);
    Route::get('/examples', [PanchangApiController::class, 'examples']);
    Route::get('/day', [PanchangApiController::class, 'day'])->middleware('auth.api_token:panchang:day');
    Route::get('/moment', [PanchangApiController::class, 'moment'])->middleware('auth.api_token:panchang:moment');
    Route::get('/calendar', [PanchangApiController::class, 'calendar'])->middleware('auth.api_token:panchang:calendar');
    Route::get('/muhurta', [PanchangApiController::class, 'muhurta'])->middleware('auth.api_token:panchang:muhurta');
    Route::get('/electional', [PanchangApiController::class, 'electional'])->middleware('auth.api_token:panchang:electional');

    // Extended endpoints
    Route::get('/timeline', [PanchangExtendedApiController::class, 'timeline'])->middleware('auth.api_token:panchang:timeline');
    Route::get('/sankranti', [PanchangExtendedApiController::class, 'sankranti'])->middleware('auth.api_token:panchang:sankranti');
    Route::get('/electional/evaluate', [PanchangExtendedApiController::class, 'evaluateElectional'])->middleware('auth.api_token:panchang:electional');
    Route::get('/astronomy', [PanchangExtendedApiController::class, 'astronomy'])->middleware('auth.api_token:panchang:astronomy');
    Route::get('/moon-sign', [PanchangExtendedApiController::class, 'moonSign'])->middleware('auth.api_token:panchang:moon-sign');

    Route::prefix('astrology')->group(function () {
        Route::get('/kundli', [AstrologyApiController::class, 'kundli'])->middleware('auth.api_token:astrology:kundli');
        Route::get('/varga/{varga}', [AstrologyApiController::class, 'varga'])->middleware('auth.api_token:astrology:varga');
        Route::get('/yogas', [AstrologyApiController::class, 'yogas'])->middleware('auth.api_token:astrology:yogas');
        Route::get('/shadbala', [AstrologyApiController::class, 'shadbala'])->middleware('auth.api_token:astrology:shadbala');
        Route::get('/dasha', [AstrologyApiController::class, 'dasha'])->middleware('auth.api_token:astrology:dasha');
    });
});

Route::middleware('panchang.api.key')->prefix('v1')->group(function () {
    Route::get('/panchang/today', [CompatibilityController::class, 'todayPanchang']);
    Route::get('/panchang/day', [CompatibilityController::class, 'dayPanchang']);
    Route::get('/calendar/month', [CompatibilityController::class, 'monthCalendar']);
    Route::get('/muhurta/today', [CompatibilityController::class, 'todayMuhurta']);
    Route::get('/festivals-old', [CompatibilityController::class, 'festivals']); // Renamed old endpoint to prevent conflict
    
    // New Festival Engine Endpoints
    Route::get('/festivals', [FestivalController::class, 'index']);
    Route::get('/festivals/{code}', [FestivalController::class, 'show']);
    Route::get('/festivals/occurrences/{year}', [FestivalOccurrenceController::class, 'byYear']);
    Route::get('/festivals/{code}/occurrences', [FestivalOccurrenceController::class, 'byFestival']);
    Route::get('/kundli', [CompatibilityController::class, 'kundli']);
    Route::get('/astrology/planet-positions', [CompatibilityController::class, 'planetPositions']);
    Route::get('/settings', [CompatibilityController::class, 'settings']);
});

use App\Http\Controllers\TelegramWebhookController;
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);
