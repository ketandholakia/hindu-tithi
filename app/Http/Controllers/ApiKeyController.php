<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function index(Request $request): View
    {
        return view('hindutithi.api-keys', [
            'keys' => $request->user()->apiKeys()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $plainKey = 'pk_' . Str::random(40);

        $request->user()->apiKeys()->create([
            'name' => $data['name'],
            'key_hash' => hash('sha256', $plainKey),
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return back()->with('new_api_key', $plainKey);
    }

    public function destroy(Request $request, ApiKey $apiKey): RedirectResponse
    {
        abort_unless($apiKey->user_id === $request->user()->id, 403);

        $apiKey->forceFill(['revoked_at' => now()])->save();

        return back();
    }
}
