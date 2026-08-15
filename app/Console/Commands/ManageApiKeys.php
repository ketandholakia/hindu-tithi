<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Interactive Artisan command for managing Hindutithi API keys.
 *
 * Usage:
 *   php artisan api:key list
 *   php artisan api:key create
 *   php artisan api:key show {id}
 *   php artisan api:key revoke {id}
 *   php artisan api:key restore {id}
 *   php artisan api:key delete {id}
 *   php artisan api:key stats {id}
 *   php artisan api:key abilities {id}
 *   php artisan api:key rotate {id}
 */
class ManageApiKeys extends Command
{
    protected $signature = 'api:key
        {action? : list|create|show|revoke|restore|delete|stats|abilities|rotate}
        {id?     : ApiKey ID (required for most actions)}';

    protected $description = 'Manage Hindutithi Panchang API keys';

    /** All known ability scopes grouped by feature */
    private const ABILITY_GROUPS = [
        'panchang' => [
            'panchang:*'          => 'All panchang endpoints',
            'panchang:day'        => 'Daily Panchang',
            'panchang:moment'     => 'Moment-level Panchang',
            'panchang:calendar'   => 'Hindu calendar',
            'panchang:muhurta'    => 'Muhurta (day segments)',
            'panchang:electional' => 'Electional evaluator',
            'panchang:timeline'   => 'Limb timeline over date range',
            'panchang:sankranti'  => 'Sankranti finder',
            'panchang:astronomy'  => 'Pure astronomy report',
            'panchang:moon-sign'  => 'Moon sign (Janmarashi)',
        ],
        'astrology' => [
            'astrology:*'         => 'All astrology endpoints',
            'astrology:kundli'    => 'Birth chart (Kundli)',
            'astrology:varga'     => 'Divisional charts (Varga)',
            'astrology:yogas'     => 'Planetary yogas',
            'astrology:shadbala'  => 'Six-fold strength (Shadbala)',
            'astrology:dasha'     => 'Vimshottari Dasha',
        ],
        'festivals' => [
            'festivals:*'         => 'All festival endpoints',
        ],
    ];

    public function handle(): int
    {
        $action = $this->argument('action');

        if (!$action) {
            $action = $this->choice('What would you like to do?', [
                'list'      => 'list      – List all API keys',
                'create'    => 'create    – Issue a new API key',
                'show'      => 'show      – Show key details',
                'revoke'    => 'revoke    – Revoke a key',
                'restore'   => 'restore   – Restore a revoked key',
                'rotate'    => 'rotate    – Rotate key secret (new token)',
                'abilities' => 'abilities – Update scopes/abilities',
                'stats'     => 'stats     – Show usage statistics',
                'delete'    => 'delete    – Permanently delete a key',
            ]);

            // Strip description (keep only the action word before the dash)
            $action = trim(explode('–', $action)[0]);
        }

        return match (trim($action)) {
            'list'      => $this->actionList(),
            'create'    => $this->actionCreate(),
            'show'      => $this->actionShow(),
            'revoke'    => $this->actionRevoke(),
            'restore'   => $this->actionRestore(),
            'rotate'    => $this->actionRotate(),
            'abilities' => $this->actionAbilities(),
            'stats'     => $this->actionStats(),
            'delete'    => $this->actionDelete(),
            default     => $this->invalidAction($action),
        };
    }

    // -------------------------------------------------------------------------
    // list
    // -------------------------------------------------------------------------

    private function actionList(): int
    {
        $keys = ApiKey::with('user')
            ->orderByDesc('created_at')
            ->get();

        if ($keys->isEmpty()) {
            $this->warn('No API keys found.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'User', 'Status', 'RPM', 'RPD', 'Abilities', 'Last Used', 'Expires'],
            $keys->map(fn($k) => [
                $k->id,
                $k->name,
                $k->user?->email ?? '—',
                $this->statusBadge($k),
                $k->rate_limit_per_minute,
                $k->rate_limit_per_day,
                implode(', ', $k->abilities ?? ['*']),
                $k->last_used_at?->diffForHumans() ?? 'Never',
                $k->expires_at?->format('Y-m-d') ?? 'Never',
            ])
        );

        $this->line('');
        $this->line("<fg=gray>Total: {$keys->count()} key(s)  |  Active: {$keys->filter->isActive()->count()}</>");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // create
    // -------------------------------------------------------------------------

    private function actionCreate(): int
    {
        $this->info('=== Create New API Key ===');
        $this->line('');

        // --- User ---
        $email = $this->ask('User email (leave blank for no user)');
        $user  = null;
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("No user found with email: {$email}");
                return self::FAILURE;
            }
            $this->line("<fg=green>✓</> Found user: {$user->name}");
        }

        // --- Name ---
        $name = $this->ask('Key name / label', 'API Key ' . now()->format('Y-m-d'));

        // --- Rate limits ---
        $rpm = (int) $this->ask('Rate limit per minute', 60);
        $rpd = (int) $this->ask('Rate limit per day', 10000);

        // --- Expiry ---
        $expiry = null;
        if ($this->confirm('Set an expiry date?', false)) {
            $expiry = $this->ask('Expiry date (Y-m-d)', now()->addYear()->format('Y-m-d'));
        }

        // --- Abilities ---
        $abilities = $this->selectAbilities();

        // --- Confirm ---
        $this->line('');
        $this->table(['Field', 'Value'], [
            ['User',               $user?->email ?? '(none)'],
            ['Name',               $name],
            ['Rate Limit/min',     $rpm],
            ['Rate Limit/day',     $rpd],
            ['Expiry',             $expiry ?? 'Never'],
            ['Abilities',          implode(', ', $abilities)],
        ]);

        if (!$this->confirm('Create this API key?', true)) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        // --- Generate & store ---
        $rawToken = Str::random(48);
        $keyHash  = hash('sha256', $rawToken);

        $apiKey = ApiKey::create([
            'user_id'               => $user?->id,
            'name'                  => $name,
            'key_hash'              => $keyHash,
            'abilities'             => $abilities,
            'rate_limit_per_minute' => $rpm,
            'rate_limit_per_day'    => $rpd,
            'expires_at'            => $expiry ? new \DateTime($expiry) : null,
        ]);

        $this->line('');
        $this->info('✅  API key created!');
        $this->line('');
        $this->warn('⚠  Copy this token now — it will NOT be shown again:');
        $this->line('');
        $this->line("   <fg=yellow;options=bold>Bearer Token: {$rawToken}</>");
        $this->line("   <fg=gray>Key ID: {$apiKey->id}</>");
        $this->line('');
        $this->line('Usage:  Authorization: Bearer ' . $rawToken);
        $this->line('        X-API-KEY: ' . $rawToken . '  (static config key)');

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // show
    // -------------------------------------------------------------------------

    private function actionShow(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        $this->line('');
        $this->info("=== API Key #{$apiKey->id}: {$apiKey->name} ===");
        $this->line('');

        $this->table(['Field', 'Value'], [
            ['ID',               $apiKey->id],
            ['Name',             $apiKey->name],
            ['User',             $apiKey->user?->email ?? '—'],
            ['Status',           $this->statusBadge($apiKey)],
            ['Rate Limit/min',   $apiKey->rate_limit_per_minute],
            ['Rate Limit/day',   $apiKey->rate_limit_per_day],
            ['Abilities',        implode(', ', $apiKey->abilities ?? ['*'])],
            ['Last Used',        $apiKey->last_used_at?->format('Y-m-d H:i:s') ?? 'Never'],
            ['Expires',          $apiKey->expires_at?->format('Y-m-d H:i:s') ?? 'Never'],
            ['Created',          $apiKey->created_at->format('Y-m-d H:i:s')],
            ['Revoked At',       $apiKey->revoked_at?->format('Y-m-d H:i:s') ?? '—'],
        ]);

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // revoke / restore
    // -------------------------------------------------------------------------

    private function actionRevoke(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        if ($apiKey->revoked_at) {
            $this->warn("Key #{$apiKey->id} is already revoked.");
            return self::SUCCESS;
        }

        if (!$this->confirm("Revoke key #{$apiKey->id} ({$apiKey->name})?", false)) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        $apiKey->update(['revoked_at' => now()]);
        $this->info("✅  Key #{$apiKey->id} has been revoked.");

        return self::SUCCESS;
    }

    private function actionRestore(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        if (!$apiKey->revoked_at) {
            $this->warn("Key #{$apiKey->id} is not revoked.");
            return self::SUCCESS;
        }

        if (!$this->confirm("Restore key #{$apiKey->id} ({$apiKey->name})?")) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        $apiKey->update(['revoked_at' => null]);
        $this->info("✅  Key #{$apiKey->id} has been restored.");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // rotate – generate a new token for an existing key
    // -------------------------------------------------------------------------

    private function actionRotate(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        $this->warn("This will invalidate the current token for key #{$apiKey->id} ({$apiKey->name}).");

        if (!$this->confirm('Continue with token rotation?', false)) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        $rawToken = Str::random(48);
        $apiKey->update(['key_hash' => hash('sha256', $rawToken)]);

        $this->info("✅  Token rotated for key #{$apiKey->id}!");
        $this->line('');
        $this->warn('⚠  New token (copy now — shown once only):');
        $this->line('');
        $this->line("   <fg=yellow;options=bold>Bearer Token: {$rawToken}</>");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // abilities – update the scopes on an existing key
    // -------------------------------------------------------------------------

    private function actionAbilities(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        $this->line("Current abilities: <fg=cyan>" . implode(', ', $apiKey->abilities ?? ['*']) . "</>");
        $this->line('');

        $abilities = $this->selectAbilities($apiKey->abilities ?? []);

        if (!$this->confirm("Update abilities for key #{$apiKey->id}?")) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        $apiKey->update(['abilities' => $abilities]);
        $this->info("✅  Abilities updated: " . implode(', ', $abilities));

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // stats – usage report for a key
    // -------------------------------------------------------------------------

    private function actionStats(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        $logs = $apiKey->usageLogs();

        $this->line('');
        $this->info("=== Usage Stats: #{$apiKey->id} {$apiKey->name} ===");
        $this->line('');

        $total       = $logs->count();
        $today       = $logs->whereDate('created_at', today())->count();
        $last1min    = $apiKey->getUsageInMinutes(1);
        $avgMs       = (int) $logs->avg('response_time_ms');
        $errorsToday = $logs->whereDate('created_at', today())->where('status_code', '>=', 400)->count();

        $this->table(['Metric', 'Value'], [
            ['Total requests',       number_format($total)],
            ['Requests today',       number_format($today) . " / {$apiKey->rate_limit_per_day}"],
            ['Requests last 1 min',  $last1min . " / {$apiKey->rate_limit_per_minute}"],
            ['Avg response time',    $avgMs . ' ms'],
            ['Errors today (≥400)',  $errorsToday],
            ['Last used',            $apiKey->last_used_at?->format('Y-m-d H:i:s') ?? 'Never'],
        ]);

        $this->line('');
        $this->info('Top 5 endpoints today:');

        $top = $logs->whereDate('created_at', today())
            ->selectRaw('endpoint, count(*) as hits')
            ->groupBy('endpoint')
            ->orderByDesc('hits')
            ->limit(5)
            ->get();

        if ($top->isEmpty()) {
            $this->line('  (no requests today)');
        } else {
            $this->table(['Endpoint', 'Hits'], $top->map(fn($r) => [$r->endpoint, $r->hits]));
        }

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // delete
    // -------------------------------------------------------------------------

    private function actionDelete(): int
    {
        $apiKey = $this->resolveKey();
        if (!$apiKey) {
            return self::FAILURE;
        }

        $this->error("⚠  This will PERMANENTLY delete key #{$apiKey->id} ({$apiKey->name}) and all its usage logs.");

        if (!$this->confirm('Are you absolutely sure?', false)) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        $apiKey->usageLogs()->delete();
        $apiKey->delete();

        $this->info("✅  Key #{$apiKey->id} permanently deleted.");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function resolveKey(): ?ApiKey
    {
        $id = $this->argument('id');

        if (!$id) {
            // Show a quick list first
            $keys = ApiKey::with('user')->orderBy('id')->get(['id', 'name', 'user_id', 'revoked_at', 'expires_at']);
            if ($keys->isEmpty()) {
                $this->warn('No API keys found.');
                return null;
            }

            $choices = $keys->mapWithKeys(fn($k) => [
                $k->id => "[{$k->id}] {$k->name}" . ($k->revoked_at ? ' (revoked)' : '')
            ])->toArray();

            $id = $this->choice('Select a key', $choices);
            $id = (int) explode(']', explode('[', $id)[1])[0];
        }

        $apiKey = ApiKey::with('user')->find((int) $id);

        if (!$apiKey) {
            $this->error("No API key found with ID: {$id}");
            return null;
        }

        return $apiKey;
    }

    private function selectAbilities(array $current = []): array
    {
        $this->line('Select abilities (scopes) for this key:');
        $this->line('');

        if ($this->confirm('Grant full access (wildcard *)?', empty($current))) {
            return ['*'];
        }

        $selected = [];

        foreach (self::ABILITY_GROUPS as $group => $groupAbilities) {
            $this->line("<fg=cyan;options=bold>── {$group} ──</>");

            $groupWildcard = "{$group}:*";

            if ($this->confirm("  Grant all {$group} endpoints ({$groupWildcard})?",
                in_array($groupWildcard, $current) || in_array('*', $current))) {
                $selected[] = $groupWildcard;
                continue;
            }

            foreach ($groupAbilities as $ability => $description) {
                if ($ability === $groupWildcard) {
                    continue; // Already asked above
                }

                $default = in_array($ability, $current, true);

                if ($this->confirm("    {$ability} – {$description}?", $default)) {
                    $selected[] = $ability;
                }
            }
        }

        if (empty($selected)) {
            $this->warn('No abilities selected. Adding read-only panchang:day as minimum.');
            $selected = ['panchang:day'];
        }

        return $selected;
    }

    private function statusBadge(ApiKey $key): string
    {
        if ($key->revoked_at) {
            return '🔴 revoked';
        }

        if ($key->expires_at && $key->expires_at->isPast()) {
            return '🟡 expired';
        }

        return '🟢 active';
    }

    private function invalidAction(string $action): int
    {
        $this->error("Unknown action: {$action}");
        $this->line('Available: list, create, show, revoke, restore, rotate, abilities, stats, delete');

        return self::FAILURE;
    }
}
