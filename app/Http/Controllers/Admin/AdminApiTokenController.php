<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminApiTokenController
{
    /**
     * Display all API tokens across all users.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_admin, 403);

        $query = ApiKey::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->whereNull('revoked_at')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            } elseif ($status === 'revoked') {
                $query->whereNotNull('revoked_at');
            } elseif ($status === 'expired') {
                $query->where('expires_at', '<=', now());
            }
        }

        $tokens = $query->latest()->paginate(50);

        return view('admin.api-tokens.index', [
            'tokens' => $tokens,
            'users' => User::orderBy('name')->get(),
            'statuses' => ['active' => 'Active', 'revoked' => 'Revoked', 'expired' => 'Expired'],
        ]);
    }

    /**
     * Display usage logs for a specific token.
     */
    public function show(Request $request, ApiKey $apiKey): View
    {
        abort_unless($request->user()?->is_admin, 403);

        $logs = $apiKey->usageLogs()
            ->latest()
            ->paginate(50);

        $usageToday = $apiKey->getUsageToday();
        $usageThisMinute = $apiKey->getUsageInMinutes(1);

        return view('admin.api-tokens.show', [
            'token' => $apiKey,
            'logs' => $logs,
            'usageToday' => $usageToday,
            'usageThisMinute' => $usageThisMinute,
        ]);
    }

    /**
     * Revoke a token (admin action).
     */
    public function revoke(Request $request, ApiKey $apiKey): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        $apiKey->update(['revoked_at' => now()]);

        return back()->with('success', "API token '{$apiKey->name}' has been revoked.");
    }

    /**
     * Update rate limits for a token.
     */
    public function updateLimits(Request $request, ApiKey $apiKey): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        $data = $request->validate([
            'rate_limit_per_minute' => ['required', 'integer', 'min:1'],
            'rate_limit_per_day' => ['required', 'integer', 'min:1'],
        ]);

        $apiKey->update($data);

        return back()->with('success', 'Rate limits updated.');
    }

    /**
     * Admin settings for default rate limits.
     */
    public function settings(Request $request): View
    {
        abort_unless($request->user()?->is_admin, 403);

        return view('admin.api-tokens.settings', [
            'defaultPerMinute' => config('api.rate_limits.per_minute'),
            'defaultPerDay' => config('api.rate_limits.per_day'),
        ]);
    }

    /**
     * Update default rate limits in the .env file.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        $request->validate([
            'default_per_minute' => ['required', 'integer', 'min:1'],
            'default_per_day' => ['required', 'integer', 'min:1'],
        ]);

        $this->updateEnv('API_RATE_LIMIT_PER_MINUTE', $request->input('default_per_minute'));
        $this->updateEnv('API_RATE_LIMIT_PER_DAY', $request->input('default_per_day'));

        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return back()->with('success', 'Default global rate limits updated successfully!');
    }

    /**
     * Helper to update .env variables safely.
     */
    private function updateEnv($key, $value)
    {
        $path = base_path('.env');
        if (!file_exists($path)) {
            return;
        }

        $envContent = file_get_contents($path);

        $pattern = "/^{$key}=(.*)$/m";
        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
        } else {
            $envContent .= "\n{$key}={$value}\n";
        }

        file_put_contents($path, $envContent);
    }
}
