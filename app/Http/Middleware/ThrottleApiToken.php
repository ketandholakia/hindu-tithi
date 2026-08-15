<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->attributes->get('api_key');

        if (!$apiKey) {
            return $next($request);
        }

        // ── Per-minute check ──────────────────────────────────────────────────
        $minuteKey = "api_token:{$apiKey->id}:minute";
        $minuteLimit = $apiKey->rate_limit_per_minute;

        if (RateLimiter::tooManyAttempts($minuteKey, $minuteLimit)) {
            return response()->json([
                'message'    => 'Rate limit exceeded (per minute).',
                'retry_after' => RateLimiter::availableIn($minuteKey),
            ], 429)
                ->header('Retry-After',             RateLimiter::availableIn($minuteKey))
                ->header('X-RateLimit-Limit',       $minuteLimit)
                ->header('X-RateLimit-Remaining',   0)
                ->header('X-RateLimit-Window',      '60');
        }

        RateLimiter::hit($minuteKey, 60); // 60-second window

        // ── Per-day check ─────────────────────────────────────────────────────
        $dayKey   = "api_token:{$apiKey->id}:day";
        $dayLimit = $apiKey->rate_limit_per_day;

        if (RateLimiter::tooManyAttempts($dayKey, $dayLimit)) {
            return response()->json([
                'message'    => 'Rate limit exceeded (per day).',
                'retry_after' => RateLimiter::availableIn($dayKey),
            ], 429)
                ->header('Retry-After',               RateLimiter::availableIn($dayKey))
                ->header('X-RateLimit-Day-Limit',     $dayLimit)
                ->header('X-RateLimit-Day-Remaining', 0);
        }

        RateLimiter::hit($dayKey, 86400); // 24-hour window

        // ── Pass through and append quota headers ─────────────────────────────
        $response = $next($request);

        $minuteRemaining = max(0, RateLimiter::remaining($minuteKey, $minuteLimit));
        $dayRemaining    = max(0, RateLimiter::remaining($dayKey, $dayLimit));

        return $response
            ->header('X-RateLimit-Limit',         $minuteLimit)
            ->header('X-RateLimit-Remaining',      $minuteRemaining)
            ->header('X-RateLimit-Window',         '60')
            ->header('X-RateLimit-Day-Limit',      $dayLimit)
            ->header('X-RateLimit-Day-Remaining',  $dayRemaining);
    }
}
