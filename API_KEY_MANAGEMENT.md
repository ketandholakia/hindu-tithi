# API Key Management System

This document describes the API key management system for Hindutithi.in, including user-facing and admin functionality.

## Overview

The API key management system provides:
- **User Dashboard**: Create, view, and revoke API keys with specific scopes
- **Rate Limiting**: Per-key rate limits (per minute and per day)
- **Usage Logging**: Track all API usage for analytics and abuse detection
- **Admin Panel**: Manage all users' keys, view detailed logs, and adjust rate limits
- **Scope-Based Access**: Keys are scoped to specific endpoint groups (panchang:day, panchang:calendar, etc.)

## Database Schema

### `api_keys` Table
Enhanced with new columns for ability-based access control:
- `id` - Primary key
- `user_id` - Foreign key to users table
- `name` - User-given label (e.g., "Mobile App", "Local Dev")
- `key_hash` - SHA256 hash of the token (never plaintext)
- `abilities` - JSON array of scopes (e.g., `["panchang:day", "panchang:calendar"]`)
- `rate_limit_per_minute` - Requests allowed per minute
- `rate_limit_per_day` - Requests allowed per day
- `last_used_at` - Timestamp of last API call
- `expires_at` - Optional expiry date
- `revoked_at` - Optional revocation timestamp
- `created_at`, `updated_at` - Timestamps

### `api_usage_logs` Table
Tracks every API request for analytics and debugging:
- `id` - Primary key
- `api_key_id` - Foreign key to api_keys
- `endpoint` - The API endpoint called (e.g., `/api/day`)
- `method` - HTTP method (GET, POST, etc.)
- `status_code` - HTTP response status
- `response_time_ms` - Time taken to process request
- `ip_address` - Client IP address
- `created_at` - Timestamp of the request

Indexed on `(api_key_id, created_at)` for efficient dashboard queries.

## Available Scopes/Abilities

```
panchang:day            - Panchang Day API
panchang:calendar       - Panchang Calendar API
panchang:moment         - Panchang Moment API
panchang:muhurta        - Panchang Muhurta API
panchang:electional     - Panchang Electional API
```

Define these in `config/api.php` under the `abilities` key.

## Configuration

### Environment Variables

Add to `.env`:

```env
# Default rate limits for new API keys
API_RATE_LIMIT_PER_MINUTE=60
API_RATE_LIMIT_PER_DAY=1440

# Log retention (days)
API_LOG_RETENTION_DAYS=90
```

### Config File

See `config/api.php` for:
- Default rate limits
- Available scopes/abilities
- Log retention settings

## User Features

### Dashboard: API Keys Page

**Route**: `/api-keys` (requires authentication)

**Features**:
- **List Keys**: View all personal API keys with status, scopes, creation date, and last used
- **Create Key**: 
  - Name the key
  - Select one or more scopes
  - Optional expiry date
  - Shows plaintext token exactly once (copy warning displayed)
- **Revoke Key**: Immediately disable a key
- **Status Indicator**: Active, Expired, or Revoked

**Controller**: `App\Http\Controllers\ApiKeyController`

### Token Format

Generated tokens follow the format:
```
hindutithi_live_<40 random characters>
```

Example:
```
hindutithi_live_AbCdEfGhIjKlMnOpQrStUvWxYz1234567890
```

### Using an API Key

Include the token in the `Authorization` header as a Bearer token:

```bash
curl -H "Authorization: Bearer hindutithi_live_..." https://hindutithi.in/api/day
```

Or use the `X-API-KEY` header:

```bash
curl -H "X-API-KEY: hindutithi_live_..." https://hindutithi.in/api/day
```

## Admin Features

### Admin Routes

Prefix: `/admin` (requires admin user)

- `GET /admin/api-tokens` - List all API tokens with filters
- `GET /admin/api-tokens/{id}` - View token details and usage logs
- `POST /admin/api-tokens/{id}/revoke` - Revoke any token
- `PATCH /admin/api-tokens/{id}/limits` - Update rate limits for a token
- `GET /admin/api-tokens/settings` - View and configure default rate limits

### Admin Dashboard: API Tokens Management

**Features**:
- **List All Tokens**: Paginated table of all users' keys
  - Filter by user
  - Filter by status (active, revoked, expired)
  - Sortable columns
  - Shows requests today
- **Token Details**: Full information page including:
  - Token metadata (status, created date, expiry, scopes)
  - Current usage stats (this minute, today)
  - Usage logs with pagination
  - Ability to revoke the token
  - Form to override rate limits
- **Usage Logs**: Paginated history with:
  - Endpoint called
  - HTTP method and status code
  - Response time
  - Client IP address
  - Timestamp

### Admin Settings

View default rate limit configuration. To change:
1. Update `.env` file with `API_RATE_LIMIT_PER_MINUTE` and `API_RATE_LIMIT_PER_DAY`
2. Run `php artisan config:clear`

## Middleware

### AuthenticateApiToken

Validates API tokens and checks abilities.

**Behavior**:
- Checks Bearer token in `Authorization` header
- Falls back to static `X-API-KEY` header for backward compatibility
- Returns 401 if token missing, invalid, revoked, or expired
- Returns 403 if token lacks required ability
- Updates `last_used_at` timestamp on success

**Usage**:
```php
Route::get('/day', [Controller::class, 'day'])
    ->middleware('auth.api_token:panchang:day'); // Requires panchang:day ability
```

### ThrottleApiToken

Implements per-token rate limiting.

**Behavior**:
- Tracks requests per token (not IP)
- Enforces per-minute and per-day limits
- Returns 429 with `Retry-After` header on breach
- Uses Laravel's RateLimiter facade with token ID as key

### LogApiUsage

Logs all API requests asynchronously.

**Behavior**:
- Records endpoint, method, status code, response time, IP
- Dispatches `LogApiUsageJob` to queue (non-blocking)
- Includes timestamp for usage analysis

## API Routes

All routes under `/api/*` now include the middleware stack:

```php
Route::middleware(['auth.api_token', 'throttle.api_token', 'log.api_usage'])->group(function () {
    Route::get('/day', [...])
        ->middleware('auth.api_token:panchang:day');
    Route::get('/calendar', [...])
        ->middleware('auth.api_token:panchang:calendar');
    // ... etc
});
```

Endpoints:
- `GET /api/` - API meta info (no ability required)
- `GET /api/examples` - API examples (no ability required)
- `GET /api/day` - Requires `panchang:day`
- `GET /api/moment` - Requires `panchang:moment`
- `GET /api/calendar` - Requires `panchang:calendar`
- `GET /api/muhurta` - Requires `panchang:muhurta`
- `GET /api/electional` - Requires `panchang:electional`

## Error Responses

### 401 Unauthorized

```json
{
  "message": "Invalid API token"
}
```

Possible messages:
- "Missing API token"
- "Invalid API token"
- "API token has been revoked"
- "API token has expired"

### 403 Forbidden

```json
{
  "message": "Insufficient permissions for this endpoint."
}
```

### 429 Too Many Requests

```json
{
  "message": "Rate limit exceeded (per minute)."
}
```

Includes `Retry-After` header with seconds to wait.

## Models

### ApiKey

Methods:
- `isActive()` - Check if key is active (not revoked/expired)
- `hasAbility(string $ability)` - Check if key has a specific ability
- `getStatus()` - Get status string (active, revoked, expired)
- `getUsageInMinutes(int $minutes = 1)` - Get request count in past N minutes
- `getUsageToday()` - Get request count for today

Relations:
- `user()` - BelongsTo User
- `usageLogs()` - HasMany ApiUsageLog

### ApiUsageLog

Relations:
- `apiKey()` - BelongsTo ApiKey

## Testing

Run feature tests:

```bash
php artisan test tests/Feature/ApiKeyManagementTest.php
```

Tests cover:
- ✅ Create, view, revoke keys
- ✅ Rate limiting enforcement
- ✅ Ability/scope checking
- ✅ Token validation (revoked, expired, missing)
- ✅ Admin access control
- ✅ Admin abilities (revoke, override limits)

## Security Considerations

1. **Tokens Never Stored in Plaintext**
   - Tokens hashed with SHA256 immediately before storage
   - Plaintext shown to user only once at creation

2. **Rate Limit Key Creation**
   - Max 10 keys per user (enforced in controller)
   - Prevents abuse

3. **CSRF Protection**
   - All forms use `@csrf` token
   - Required by Laravel by default

4. **Admin Authorization**
   - Uses `is_admin` flag on User model
   - All admin routes check this flag
   - Could be extended with Spatie/permission package for granular RBAC

5. **Audit Logging** (Future Enhancement)
   - Consider adding audit log entries when:
     - Key is created
     - Key is revoked
     - Rate limits are changed
   - Can integrate with existing audit system

## Installation & Migration

1. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

2. **Seed Default Admin** (if needed):
   ```bash
   php artisan db:seed --class=AdminUserSeeder
   ```

3. **Clear Config Cache** (if changing .env):
   ```bash
   php artisan config:clear
   ```

4. **Seed Example Keys** (optional):
   ```bash
   php artisan tinker
   # Then in tinker shell:
   # $user = User::find(1);
   # $user->apiKeys()->create([...])
   ```

## Future Enhancements

- [ ] Usage charts/graphs in user dashboard
- [ ] Webhook notifications for rate limit threshold
- [ ] API key activity email digest
- [ ] Integration with Spatie/permission for granular RBAC
- [ ] Daily usage summary table to keep logs pruned
- [ ] Scheduled job to prune logs older than N days
- [ ] OAuth2 flows for third-party apps
- [ ] API documentation with interactive testing
