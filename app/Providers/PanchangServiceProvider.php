<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vittix\Panchang\Panchang;

class PanchangServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Panchang::class, function () {
            return Panchang::createDefault();
        });

        $this->app->alias(Panchang::class, 'panchang');
    }

    public function boot(): void
    {
        // No-op
    }
}
