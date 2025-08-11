# VergeFlow Multi-Tenant Database Architecture

## 🏗️ Overview

VergeFlow implements a true multi-tenant architecture with separate databases for each client, providing complete data isolation and scalability.

## 📊 Database Structure

### Main Database (`vergeflow_main`)
**Purpose:** Global application data and administration

**Tables:**
- `users` - Super admin and admin users only
- `clients` - Client registry and configuration
- `settings` - Global application settings
- `banners` - Global promotional banners
- `pages` - Global pages (terms, privacy, etc.)
- `migrations` - Migration tracking
- `password_reset_tokens` - Password reset tokens
- `personal_access_tokens` - API tokens
- `failed_jobs` - Failed job queue
- `jobs` - Job queue
- `job_batches` - Job batches

### Client Databases (`vergeflow_client_{id}`)
**Purpose:** Client-specific data and operations

**Tables:**
- `users` - Site users (customers) only
- `products` - Client product catalog
- `categories` - Client product categories
- `orders` - Client orders and transactions
- `order_items` - Order line items
- `cart_items` - Shopping cart data
- `customers` - Client customer data
- `coupons` - Client-specific promotions
- `addresses` - Customer addresses
- `product_reviews` - Product reviews and ratings
- `recently_viewed` - User activity tracking

## 🔧 Implementation Components

### 1. MultiTenantService
**File:** `app/Services/MultiTenantService.php`

**Key Methods:**
- `createClientDatabase(Client $client)` - Creates new client database
- `setClientDatabaseConnection($clientId, $databaseName)` - Sets up dynamic connection
- `switchToClientDatabase($clientId)` - Switches context to client DB
- `switchToMainDatabase()` - Switches context to main DB
- `deleteClientDatabase($clientId)` - Removes client database
- `getMainDatabaseTables()` - Lists global tables
- `getClientDatabaseTables()` - Lists client-specific tables

### 2. TenantMiddleware
**File:** `app/Http/Middleware/TenantMiddleware.php`

**Purpose:** Automatically switches database context based on request

**Detection Methods:**
1. User's client_id (if authenticated)
2. Subdomain matching
3. Domain matching
4. Request parameters
5. Session data

### 3. MultiTenant Trait
**File:** `app/Traits/MultiTenant.php`

**Purpose:** Provides automatic database connection management for models

**Features:**
- Automatic connection setting on model operations
- Client-specific model detection
- Tenant scoping capabilities

### 4. Migration Command
**File:** `app/Console/Commands/MigrateToMultiTenant.php`

**Command:** `php artisan migrate:multi-tenant`

**Purpose:** Migrates existing single-database setup to multi-tenant architecture

## 🚀 Setup Instructions

### 1. Environment Configuration
Update your `.env` file:

```env
# Main Database (Global Data)
DB_CONNECTION=main
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vergeflow_main
DB_USERNAME=root
DB_PASSWORD=

# Main Database Connection (Explicit)
MAIN_DB_HOST=127.0.0.1
MAIN_DB_PORT=3306
MAIN_DB_DATABASE=vergeflow_main
MAIN_DB_USERNAME=root
MAIN_DB_PASSWORD=

# Client Database Template
CLIENT_DB_HOST=127.0.0.1
CLIENT_DB_PORT=3306
CLIENT_DB_USERNAME=root
CLIENT_DB_PASSWORD=
```

### 2. Run Migration to Multi-Tenant
```bash
php artisan migrate:multi-tenant --force
```

### 3. Clear Configuration Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## 🔄 How It Works

### Client Creation Process
1. Super admin creates new client via `/super-admin/clients/create`
2. Client record is saved to main database
3. `MultiTenantService::createClientDatabase()` is called
4. New database `vergeflow_client_{id}` is created
5. Client-specific migrations are run
6. Admin user is created in client database
7. Database name is stored in client record

### Request Handling
1. Request comes in (e.g., `vault64.vergeflow.com`)
2. `TenantMiddleware` detects client from subdomain
3. Database context is switched to `vergeflow_client_1`
4. All subsequent queries use client database
5. Response is generated with client-specific data

### Client Deletion Process
1. Super admin deletes client via `/super-admin/clients/{id}`
2. `MultiTenantService::deleteClientDatabase()` is called
3. Entire client database is dropped
4. Client record is removed from main database
5. All client data is completely removed

## 🛡️ Security & Isolation

### Data Isolation
- **Complete separation:** Each client has their own database
- **No cross-contamination:** Impossible to access other client's data
- **Secure deletion:** Dropping database removes all traces

### Access Control
- **Super Admin:** Access to main database and all client databases
- **Admin:** Access to their specific client database only
- **Site Users:** Access to their specific client database only

### Connection Security
- Dynamic connections are created with same credentials
- No hardcoded database names in application code
- Automatic connection cleanup and management

## 📈 Benefits

### Scalability
- **Horizontal scaling:** Each client database can be on different servers
- **Performance isolation:** One client's load doesn't affect others
- **Independent backups:** Each client can have separate backup schedules

### Compliance
- **Data residency:** Client data can be stored in specific regions
- **GDPR compliance:** Complete data deletion is guaranteed
- **Audit trails:** Per-client logging and monitoring

### Customization
- **Schema flexibility:** Each client can have custom fields
- **Independent updates:** Clients can be migrated independently
- **Feature flags:** Different features per client database

## 🔧 Development Guidelines

### Model Usage
```php
// For client-specific models, use the MultiTenant trait
class Product extends Model
{
    use MultiTenant;
    // Model automatically uses correct database
}

// For global models, specify main connection
class Setting extends Model
{
    protected $connection = 'main';
}
```

### Service Usage
```php
$multiTenantService = app(MultiTenantService::class);

// Switch to client database
$multiTenantService->switchToClientDatabase($clientId);

// Perform client operations
$products = Product::all();

// Switch back to main database
$multiTenantService->switchToMainDatabase();
```

### Testing
```php
// In tests, you can switch contexts
$this->multiTenantService->switchToClientDatabase(1);
$this->assertDatabaseHas('products', ['name' => 'Test Product']);
```

## 🚨 Important Notes

### Migration Considerations
- Always backup before running multi-tenant migration
- Test migration on staging environment first
- Migration is irreversible without backup restoration

### Performance
- Connection pooling is handled automatically
- Database connections are cached and reused
- Minimal overhead for connection switching

### Monitoring
- Monitor connection counts across all databases
- Set up alerts for database creation/deletion
- Track query performance per client database

## 🆘 Troubleshooting

### Common Issues

**Connection Errors:**
```bash
# Clear config cache
php artisan config:clear

# Verify database connections
php artisan tinker
DB::connection('main')->getPdo()
```

**Migration Failures:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Verify database permissions
SHOW GRANTS FOR 'root'@'localhost';
```

**Client Database Not Found:**
```bash
# Recreate client database
php artisan tinker
$client = App\Models\Client::find(1);
$service = new App\Services\MultiTenantService();
$service->createClientDatabase($client);
```

## 📞 Support

For issues with multi-tenant setup:
1. Check Laravel logs in `storage/logs/`
2. Verify database connections in config
3. Ensure proper permissions for database creation
4. Contact system administrator for database server issues

---

**Status:** ✅ Production Ready
**Version:** 1.0
**Last Updated:** 2025-01-29
