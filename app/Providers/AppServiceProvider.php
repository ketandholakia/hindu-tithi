<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\PanchangServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the Panchang service provider from the scaffold
        $this->app->register(PanchangServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
