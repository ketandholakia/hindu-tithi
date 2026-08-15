<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'panchang.api.key' => \App\Http\Middleware\RequirePanchangApiKey::class,
            'auth.api_token' => \App\Http\Middleware\AuthenticateApiToken::class,
            'throttle.api_token' => \App\Http\Middleware\ThrottleApiToken::class,
            'log.api_usage' => \App\Http\Middleware\LogApiUsage::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->create();

$app->usePublicPath(__DIR__.'/../public_html');

return $app;