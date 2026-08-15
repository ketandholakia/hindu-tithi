<?php

namespace App\Http\Middleware;

use App\Jobs\LogApiUsageJob;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiUsage
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $this->logUsage($request, $response, $startTime);

        return $response;
    }

    private function logUsage(Request $request, Response $response, float $startTime): void
    {
        $apiKey = $request->attributes->get('api_key');

        if (!$apiKey) {
            return;
        }

        $responseTime = (int) (round((microtime(true) - $startTime) * 1000));

        // Dispatch job to log asynchronously
        LogApiUsageJob::dispatch(
            $apiKey->id,
            $request->path(),
            $request->method(),
            $response->getStatusCode(),
            $responseTime,
            $request->ip()
        );
    }
}
