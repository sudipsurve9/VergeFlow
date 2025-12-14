# Fix Live Server - Client Database Migration

## Issue
The admin products page at https://vault64.in/admin/dashboard is showing errors because client databases don't have the required tables (products, categories, etc.).

## Solution
Run migrations on all client databases on the live server.

## Steps to Fix

### 1. SSH into the Live Server
```bash
ssh user@vault64.in
cd /path/to/vergeflow  # Navigate to your VergeFlow installation
```

### 2. Run Client Database Migrations

**Option A: Migrate All Client Databases (Recommended)**
```bash
php artisan clients:migrate --force
```

This will:
- Find all active clients with databases
- Run migrations on each client database
- Show progress and results for each client

**Option B: Migrate Specific Client**
```bash
# Replace 1 with your client ID
php artisan clients:migrate --client=1 --force
```

**Option C: Fresh Migration (WARNING: Drops all data)**
```bash
# Only use if you want to start fresh
php artisan clients:migrate --client=1 --fresh --force
```

### 3. Verify the Fix

After running migrations, test the admin dashboard:
1. Go to https://vault64.in/admin/dashboard
2. Click on "Products" in the menu
3. The products page should load without errors

### 4. Clear Caches (If Needed)

If you still see issues, clear all caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Error: "Database connection [client_1] not configured"
This means the client database connection isn't set up. The command should handle this automatically, but if it fails:

1. Check that the client has a `database_name` set:
```bash
php artisan tinker
>>> \App\Models\Client::on('main')->find(1)->database_name
```

2. Verify the database exists:
```bash
mysql -u your_user -p -e "SHOW DATABASES LIKE 'vergeflow_%';"
```

### Error: "Table 'database.products' doesn't exist"
This is the exact issue we're fixing. Run the migration command:
```bash
php artisan clients:migrate --force
```

### Error: "Migration failed"
Check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Common issues:
- Database permissions
- Missing migration files
- Database connection issues

## Alternative: Manual Migration

If the command doesn't work, you can manually migrate:

```bash
# For each client, get the connection name
php artisan tinker
>>> $client = \App\Models\Client::on('main')->find(1);
>>> $dbService = new \App\Services\DatabaseService();
>>> $conn = $dbService->getClientConnection($client);
>>> echo $conn;  # e.g., "client_1"

# Then run migrations manually
php artisan migrate --database=client_1 --force
```

## Prevention

To prevent this issue in the future:

1. **Add to deployment script**: The `deploy_production.sh` script now includes client database migrations
2. **Run after code updates**: Always run `php artisan clients:migrate --force` after deploying new code
3. **Monitor logs**: Check `storage/logs/laravel.log` regularly for database errors

## Quick Fix Command

Run this single command to fix everything:
```bash
php artisan clients:migrate --force && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan config:cache && php artisan route:cache
```

