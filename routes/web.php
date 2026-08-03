<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('hindutithi.home');
});

Route::get('/home', [DemoController::class, 'home'])->name('hindutithi.home');
Route::get('/help', function () {
    return view('hindutithi.help');
})->name('hindutithi.help');
Route::get('/api/docs', function () {
    return view('api.docs');
})->name('api.docs');
Route::post('/set-birth', [DemoController::class, 'setBirth'])->name('hindutithi.setBirth');

foreach (['day','moment','janmarashi','ascendant','kundali','varga','vimshottari','shadbala','yogas','calendar','festivals','muhurta','electional'] as $s) {
    Route::get("/$s", [DemoController::class, $s])->name("hindutithi.$s");
}

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/api-keys', [App\Http\Controllers\ApiKeyController::class, 'index'])->name('api.keys.index');
    Route::post('/api-keys', [App\Http\Controllers\ApiKeyController::class, 'store'])->name('api.keys.store');
    Route::delete('/api-keys/{apiKey}', [App\Http\Controllers\ApiKeyController::class, 'destroy'])->name('api.keys.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
