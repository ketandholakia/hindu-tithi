<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_their_api_keys(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('api.keys.index'));

        $response->assertStatus(200);
        $response->assertViewIs('hindutithi.api-keys');
    }

    /** @test */
    public function unauthenticated_user_cannot_view_api_keys(): void
    {
        $response = $this->get(route('api.keys.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_create_api_key_with_scopes(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('api.keys.store'), [
                'name' => 'Test Key',
                'abilities' => ['panchang:day', 'panchang:calendar'],
                'expires_at' => null,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('new_api_key');

        $this->assertDatabaseHas('api_keys', [
            'user_id' => $this->user->id,
            'name' => 'Test Key',
        ]);

        $key = ApiKey::where('user_id', $this->user->id)->first();
        $this->assertSame(['panchang:day', 'panchang:calendar'], $key->abilities);
    }

    /** @test */
    public function user_cannot_create_more_than_10_keys(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->user->apiKeys()->create([
                'name' => "Key $i",
                'key_hash' => hash('sha256', Str::random(40)),
                'abilities' => ['panchang:day'],
                'rate_limit_per_minute' => 60,
                'rate_limit_per_day' => 1440,
            ]);
        }

        $response = $this->actingAs($this->user)
            ->post(route('api.keys.store'), [
                'name' => 'Eleventh Key',
                'abilities' => ['panchang:day'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_revoke_their_own_key(): void
    {
        $key = $this->user->apiKeys()->create([
            'name' => 'Revokable Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('api.keys.destroy', $key));

        $response->assertRedirect();
        $this->assertNotNull($key->fresh()->revoked_at);
    }

    /** @test */
    public function user_cannot_revoke_another_users_key(): void
    {
        $otherUser = User::factory()->create();
        $key = $otherUser->apiKeys()->create([
            'name' => 'Other User Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('api.keys.destroy', $key));

        $response->assertStatus(403);
        $this->assertNull($key->fresh()->revoked_at);
    }

    /** @test */
    public function api_key_validation_requires_abilities(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('api.keys.store'), [
                'name' => 'Test Key',
                'abilities' => [],
            ]);

        $response->assertSessionHasErrors('abilities');
    }

    /** @test */
    public function api_endpoint_requires_valid_token(): void
    {
        $response = $this->getJson('/api/day');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Missing API token']);
    }

    /** @test */
    public function api_endpoint_rejects_invalid_token(): void
    {
        $response = $this->getJson('/api/day', [
            'Authorization' => 'Bearer invalid_token_12345',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid API token']);
    }

    /** @test */
    public function api_endpoint_accepts_valid_bearer_token(): void
    {
        $plainKey = 'hindutithi_live_' . Str::random(40);
        $this->user->apiKeys()->create([
            'name' => 'Valid Key',
            'key_hash' => hash('sha256', $plainKey),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        // Mock the controller to test auth passes
        $response = $this->getJson('/api/day', [
            'Authorization' => "Bearer $plainKey",
        ]);

        // Since there's no real endpoint, we expect a 404 or specific response
        // But the auth should pass (not 401)
        $this->assertNotSame(401, $response->getStatusCode());
    }

    /** @test */
    public function revoked_key_is_rejected(): void
    {
        $plainKey = 'hindutithi_live_' . Str::random(40);
        $key = $this->user->apiKeys()->create([
            'name' => 'Revoked Key',
            'key_hash' => hash('sha256', $plainKey),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
            'revoked_at' => now(),
        ]);

        $response = $this->getJson('/api/day', [
            'Authorization' => "Bearer $plainKey",
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'API token has been revoked']);
    }

    /** @test */
    public function expired_key_is_rejected(): void
    {
        $plainKey = 'hindutithi_live_' . Str::random(40);
        $key = $this->user->apiKeys()->create([
            'name' => 'Expired Key',
            'key_hash' => hash('sha256', $plainKey),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/day', [
            'Authorization' => "Bearer $plainKey",
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'API token has expired']);
    }

    /** @test */
    public function token_without_required_ability_is_rejected(): void
    {
        $plainKey = 'hindutithi_live_' . Str::random(40);
        $key = $this->user->apiKeys()->create([
            'name' => 'Limited Key',
            'key_hash' => hash('sha256', $plainKey),
            'abilities' => ['panchang:calendar'], // Only calendar, not day
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $response = $this->getJson('/api/day', [
            'Authorization' => "Bearer $plainKey",
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Insufficient permissions for this endpoint.']);
    }

    /** @test */
    public function admin_can_view_all_api_tokens(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $otherUser = User::factory()->create();
        $otherUser->apiKeys()->create([
            'name' => 'Other User Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.api-tokens.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.api-tokens.index');
    }

    /** @test */
    public function non_admin_cannot_access_admin_api_tokens(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.api-tokens.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_revoke_any_token(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $otherUser = User::factory()->create();
        $key = $otherUser->apiKeys()->create([
            'name' => 'Other User Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.api-tokens.revoke', $key));

        $response->assertRedirect();
        $this->assertNotNull($key->fresh()->revoked_at);
    }

    /** @test */
    public function admin_can_update_token_rate_limits(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $key = $this->user->apiKeys()->create([
            'name' => 'Test Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.api-tokens.update-limits', $key), [
                'rate_limit_per_minute' => 120,
                'rate_limit_per_day' => 2880,
            ]);

        $response->assertRedirect();
        $this->assertSame(120, $key->fresh()->rate_limit_per_minute);
        $this->assertSame(2880, $key->fresh()->rate_limit_per_day);
    }

    /** @test */
    public function api_key_model_has_ability_check(): void
    {
        $key = $this->user->apiKeys()->create([
            'name' => 'Test Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day', 'panchang:calendar'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $this->assertTrue($key->hasAbility('panchang:day'));
        $this->assertTrue($key->hasAbility('panchang:calendar'));
        $this->assertFalse($key->hasAbility('panchang:moment'));
    }

    /** @test */
    public function api_key_model_has_status_check(): void
    {
        $activeKey = $this->user->apiKeys()->create([
            'name' => 'Active Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
        ]);

        $this->assertSame('active', $activeKey->getStatus());

        $expiredKey = $this->user->apiKeys()->create([
            'name' => 'Expired Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertSame('expired', $expiredKey->getStatus());

        $revokedKey = $this->user->apiKeys()->create([
            'name' => 'Revoked Key',
            'key_hash' => hash('sha256', Str::random(40)),
            'abilities' => ['panchang:day'],
            'rate_limit_per_minute' => 60,
            'rate_limit_per_day' => 1440,
            'revoked_at' => now(),
        ]);

        $this->assertSame('revoked', $revokedKey->getStatus());
    }
}
