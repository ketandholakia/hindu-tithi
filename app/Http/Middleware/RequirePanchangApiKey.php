<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePanchangApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.panchang_api.key', '');
        $provided = (string) $request->header('X-API-KEY', '');

        if ($expected !== '' && hash_equals($expected, $provided)) {
            return $next($request);
        }

        $keyHash = hash('sha256', $provided);
        $apiKey = ApiKey::query()
            ->where('key_hash', $keyHash)
            ->whereNull('revoked_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($apiKey === null) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        $apiKey->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }
}
