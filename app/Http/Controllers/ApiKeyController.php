<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function index(Request $request): View
    {
        $keys = $request->user()->apiKeys()
            ->latest()
            ->get();

        return view('hindutithi.api-keys', [
            'keys' => $keys,
            'abilities' => config('api.abilities', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Limit keys per user
        if ($request->user()->apiKeys()->count() >= 10) {
            return back()->with('error', 'Maximum of 10 API keys per user.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', 'in:' . implode(',', array_keys(config('api.abilities', [])))],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $plainKey = 'hindutithi_live_' . Str::random(40);

        $createData = [
            'name' => $data['name'],
            'key_hash' => hash('sha256', $plainKey),
            'expires_at' => $data['expires_at'] ?? null,
        ];

        // Only add these fields if the columns exist
        if (Schema::hasColumn('api_keys', 'abilities')) {
            $createData['abilities'] = $data['abilities'] ?? [];
        }
        if (Schema::hasColumn('api_keys', 'rate_limit_per_minute')) {
            $createData['rate_limit_per_minute'] = config('api.rate_limits.per_minute', 60);
        }
        if (Schema::hasColumn('api_keys', 'rate_limit_per_day')) {
            $createData['rate_limit_per_day'] = config('api.rate_limits.per_day', 1440);
        }

        $request->user()->apiKeys()->create($createData);

        return back()->with('new_api_key', $plainKey);
    }

    public function destroy(Request $request, ApiKey $apiKey): RedirectResponse
    {
        abort_unless($apiKey->user_id === $request->user()->id, 403);

        $apiKey->forceFill(['revoked_at' => now()])->save();

        return back()->with('success', 'API key revoked.');
    }
}
