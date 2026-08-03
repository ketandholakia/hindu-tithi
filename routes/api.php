<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PanchangApiController;

Route::middleware('panchang.api.key')->group(function () {
    Route::get('/', [PanchangApiController::class, 'index']);
    Route::get('/examples', [PanchangApiController::class, 'examples']);
    Route::get('/day', [PanchangApiController::class, 'day']);
    Route::get('/moment', [PanchangApiController::class, 'moment']);
    Route::get('/calendar', [PanchangApiController::class, 'calendar']);
    Route::get('/muhurta', [PanchangApiController::class, 'muhurta']);
    Route::get('/electional', [PanchangApiController::class, 'electional']);
});
