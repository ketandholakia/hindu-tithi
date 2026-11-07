<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vittix\Panchang\Panchang;
use Vittix\Panchang\Panchanga\PanchangService;

class PanchangServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Panchang::class, function () {
            return Panchang::createDefault();
        });

        $this->app->singleton(PanchangService::class, function ($app) {
            return PanchangService::fromPanchang($app->make(Panchang::class));
        });

        $this->app->alias(Panchang::class, 'panchang');
    }

    public function boot(): void
    {
        // No-op
    }
}
