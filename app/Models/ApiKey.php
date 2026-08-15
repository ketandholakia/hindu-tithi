<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'key_hash',
        'abilities',
        'rate_limit_per_minute',
        'rate_limit_per_day',
        'last_used_at',
        'expires_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(ApiUsageLog::class);
    }

    /**
     * Check if this key is active (not revoked and not expired).
     */
    public function isActive(): bool
    {
        if ($this->revoked_at) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if this key has the given ability.
     *
     * Supports three forms:
     *   '*'            – full wildcard, access to everything
     *   'panchang:*'   – prefix wildcard, all panchang:* endpoints
     *   'panchang:day' – exact match
     */
    public function hasAbility(string $ability): bool
    {
        $abilities = $this->abilities ?? [];

        // Full wildcard
        if (in_array('*', $abilities, true)) {
            return true;
        }

        // Exact match
        if (in_array($ability, $abilities, true)) {
            return true;
        }

        // Prefix wildcard: 'panchang:*' covers 'panchang:day', 'panchang:moment', etc.
        $parts = explode(':', $ability, 2);
        if (count($parts) === 2 && in_array($parts[0] . ':*', $abilities, true)) {
            return true;
        }

        return false;
    }


    /**
     * Get the key's status (active, revoked, or expired).
     */
    public function getStatus(): string
    {
        if ($this->revoked_at) {
            return 'revoked';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    /**
     * Get usage count for the last N minutes.
     */
    public function getUsageInMinutes(int $minutes = 1): int
    {
        return $this->usageLogs()
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Get usage count for the current day.
     */
    public function getUsageToday(): int
    {
        return $this->usageLogs()
            ->whereDate('created_at', today())
            ->count();
    }
}
