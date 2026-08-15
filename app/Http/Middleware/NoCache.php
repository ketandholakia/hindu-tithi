<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Tell LiteSpeed and proxies not to cache pages that depend on user sessions.
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        // These routes render session-dependent data, so keep them out of the index.
        $response->headers->set('X-Robots-Tag', 'noindex, follow');

        return $response;
    }
}
