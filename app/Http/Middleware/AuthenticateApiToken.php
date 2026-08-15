<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        // Check for static config API key first
        $expected = (string) config('services.panchang_api.key', '');
        $provided = (string) $request->header('X-API-KEY', '');

        if ($expected !== '' && hash_equals($expected, $provided)) {
            $request->attributes->set('api_token_validated', true);
            return $next($request);
        }

        // Check for Bearer token
        $token = $request->bearerToken();
        if (!$token) {
            return $this->unauthorizedResponse('Missing API token');
        }

        // Look up the API key by hash
        $keyHash = hash('sha256', $token);
        $apiKey = ApiKey::query()
            ->where('key_hash', $keyHash)
            ->with('user')
            ->first();

        if (!$apiKey) {
            return $this->unauthorizedResponse('Invalid API token');
        }

        // Check if revoked
        if ($apiKey->revoked_at) {
            return $this->unauthorizedResponse('API token has been revoked');
        }

        // Check if expired
        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return $this->unauthorizedResponse('API token has expired');
        }

        // Check ability/scope if required and column exists
        if ($ability && Schema::hasColumn('api_keys', 'abilities') && !$apiKey->hasAbility($ability)) {
            return response()->json([
                'message' => 'Insufficient permissions for this endpoint.',
            ], 403);
        }

        // Attach key and user to request
        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('api_user', $apiKey->user);

        // Update last_used_at
        $apiKey->update(['last_used_at' => now()]);

        return $next($request);
    }

    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }
}
