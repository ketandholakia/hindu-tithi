# Production Migration Instructions

The API key management system requires database schema changes to the `api_keys` table. The migration has been made production-safe with idempotency checks.

## What's Being Added

The migration adds three new columns to the `api_keys` table:
- `abilities` (JSON) - stores the list of scopes/abilities for the key
- `rate_limit_per_minute` (integer) - default 60
- `rate_limit_per_day` (integer) - default 1440

## Migration Details

**File:** `database/migrations/2026_08_05_000005_enhance_api_keys_table.php`

This migration is **production-safe** because it:
- Checks if columns exist before adding them (idempotent)
- Won't cause errors if run multiple times
- Won't cause errors if partial schema already exists

## How to Deploy

### Option 1: Run from Command Line (Recommended)

On your production server:

```bash
cd /path/to/hindutithi-app
php artisan migrate
```

This will run only the pending migrations. The migration will safely check if columns already exist before attempting to add them.

### Option 2: Verify Before Running

If you want to see what migrations are pending:

```bash
php artisan migrate:status
```

Look for the migration `2026_08_05_000005_enhance_api_keys_table.php` in the Pending list.

### Option 3: Manual SQL (If Direct Database Access)

If you need to run the SQL directly on your MySQL server:

```sql
-- Only if the columns don't already exist
ALTER TABLE `api_keys` ADD COLUMN `abilities` JSON DEFAULT '[]' AFTER `key_hash`;
ALTER TABLE `api_keys` ADD COLUMN `rate_limit_per_minute` INT DEFAULT 60 AFTER `abilities`;
ALTER TABLE `api_keys` ADD COLUMN `rate_limit_per_day` INT DEFAULT 1440 AFTER `rate_limit_per_minute`;
```

## Current Behavior (Before Migration)

While the migration is pending:
- ✅ API key creation works without scopes
- ✅ Existing keys continue to function
- ✅ The system uses sensible defaults
- ⚠️  The admin panel won't show full functionality (scopes/rate limits)

## After Migration

Once applied:
- ✅ Full scope/ability system works
- ✅ Admin can see and manage rate limits
- ✅ User dashboard shows scopes
- ✅ API endpoints enforce scope checks

## Troubleshooting

### Error: "Column 'abilities' already exists"

This means the column was already partially added. The new migration handles this gracefully. Just run:

```bash
php artisan migrate
```

### Error: "Unknown column 'abilities'"

This means the migration hasn't been run yet. Run:

```bash
php artisan migrate
```

### Database Lock Issues

If you get "database is locked" errors (rare on MySQL):

```bash
# Wait a moment and retry
sleep 5
php artisan migrate
```

## After Deployment

Once migrations are running:

1. **Optional: Update .env** to set custom rate limits:

   ```env
   API_RATE_LIMIT_PER_MINUTE=60
   API_RATE_LIMIT_PER_DAY=1440
   ```

2. **Clear config cache** (if you changed .env):

   ```bash
   php artisan config:clear
   ```

3. **Test the API key system**:
   - Log in to https://hindutithi.in/api-keys
   - Try creating a new key
   - Try using it against an API endpoint

## Rollback (If Needed)

To rollback the migration (will drop the columns):

```bash
php artisan migrate:rollback
```

**Warning:** This will delete `abilities` and rate limit data. Only do this if necessary.

## Support

If you encounter issues:

1. Check that the migration file exists at `database/migrations/2026_08_05_000005_enhance_api_keys_table.php`
2. Verify your database user has ALTER TABLE permissions
3. Check the Laravel logs: `storage/logs/laravel.log`
4. Ensure no other migrations are currently running
