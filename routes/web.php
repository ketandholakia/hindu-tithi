<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\NoCache;
use Illuminate\Support\Facades\Route;

Route::get('/', [DemoController::class, 'home'])->name('hindutithi.home');
Route::get('/home', function () {
    return redirect()->route('hindutithi.home', [], 301);
});

Route::get('/sitemap.xml', function () {
    $urls = [
        route('hindutithi.home'),
        route('hindutithi.kundli'),
        route('hindutithi.accuracy'),
        route('hindutithi.astrology'),
        route('hindutithi.help'),
        route('hindutithi.whats_new'),
        route('api.docs'),
    ];

    $lastmod = now()->format('Y-m-d');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    foreach ($urls as $url) {
        $xml .= "  <url>" . PHP_EOL
            . "    <loc>{$url}</loc>" . PHP_EOL
            . "    <lastmod>{$lastmod}</lastmod>" . PHP_EOL
            . "    <changefreq>weekly</changefreq>" . PHP_EOL
            . "  </url>" . PHP_EOL;
    }

    $xml .= '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml');
});

Route::get('/help', function () {
    return view('hindutithi.help');
})->name('hindutithi.help');
Route::get('/accuracy', function () {
    return view('hindutithi.accuracy');
})->name('hindutithi.accuracy');
Route::get('/astrology', function () {
    return view('hindutithi.astrology');
})->name('hindutithi.astrology');
Route::get('/whats-new', function () {
    return view('hindutithi.whats_new');
})->name('hindutithi.whats_new');
Route::get('/api/docs', function () {
    return view('api.docs');
})->name('api.docs');

Route::get('/openapi.yaml', function () {
    return response()->file(public_path('openapi.yaml'), [
        'Content-Type' => 'text/yaml'
    ]);
});

Route::post('/set-birth', [DemoController::class, 'setBirth'])->name('hindutithi.setBirth');

Route::get('/kundli', [DemoController::class, 'kundali'])->name('hindutithi.kundli');

Route::middleware([NoCache::class])->group(function () {
    foreach (['day','moment','janmarashi','ascendant','varga','vimshottari','shadbala','yogas','calendar','muhurta','electional'] as $s) {
        Route::get("/$s", [DemoController::class, $s])->name("hindutithi.$s");
    }
    Route::get("/festivals", [\App\Http\Controllers\FestivalController::class, 'index'])->name("hindutithi.festivals");
});

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

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store', 'show']);
    // API Tokens
    Route::get('/api-tokens', [App\Http\Controllers\Admin\AdminApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::get('/api-tokens/settings', [App\Http\Controllers\Admin\AdminApiTokenController::class, 'settings'])->name('api-tokens.settings');
    Route::post('/api-tokens/settings', [App\Http\Controllers\Admin\AdminApiTokenController::class, 'updateSettings'])->name('api-tokens.update-settings');
    Route::get('/api-tokens/{apiKey}', [App\Http\Controllers\Admin\AdminApiTokenController::class, 'show'])->name('api-tokens.show');
    Route::post('/api-tokens/{apiKey}/revoke', [App\Http\Controllers\Admin\AdminApiTokenController::class, 'revoke'])->name('api-tokens.revoke');
    Route::patch('/api-tokens/{apiKey}/limits', [App\Http\Controllers\Admin\AdminApiTokenController::class, 'updateLimits'])->name('api-tokens.update-limits');
    
    // Telegram Config
    Route::get('/telegram', [App\Http\Controllers\Admin\TelegramConfigController::class, 'index'])->name('telegram.index');
    Route::post('/telegram/webhook', [App\Http\Controllers\Admin\TelegramConfigController::class, 'setWebhook'])->name('telegram.webhook.set');
    Route::delete('/telegram/webhook', [App\Http\Controllers\Admin\TelegramConfigController::class, 'deleteWebhook'])->name('telegram.webhook.delete');
});

require __DIR__.'/auth.php';
