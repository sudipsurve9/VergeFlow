# VergeFlow Multi-Database Architecture

## Overview

VergeFlow has been transformed from a single-database application to a multi-database architecture where each client has their own separate database. This provides better data isolation, security, and scalability.

## Architecture Changes

### 1. Project Name Change
- **From**: Vault64
- **To**: VergeFlow
- All references, titles, and branding have been updated throughout the application

### 2. Database Structure

#### Main Database (`vergeflow_main`)
- Contains global system data
- Client information
- System settings
- API integrations
- Super admin users

#### Client Databases (`vergeflow_[client_name]_[id]`)
- Each client has their own database
- Contains all client-specific data:
  - Products
  - Categories
  - Orders
  - Customers
  - Users (client admins and customers)
  - Settings
  - Banners
  - Pages
  - Coupons

### 3. Vault64 as First Client
- All existing data has been preserved and migrated to the Vault64 client
- Vault64 serves as the reference client with all original data
- Database: `vergeflow_vault64_1`

## New Features

### 1. DatabaseService
Location: `app/Services/DatabaseService.php`

**Key Methods:**
- `createClientDatabase(Client $client)`: Creates a new database for a client
- `getClientConnection(Client $client)`: Gets the database connection for a client
- `createVault64Client()`: Creates Vault64 as the first client
- `migrateExistingData()`: Migrates existing data to separate databases

### 2. HasClientDatabase Trait
Location: `app/Traits/HasClientDatabase.php`

**Features:**
- Automatic database connection switching based on client context
- Client-specific model operations
- Database isolation for each client

### 3. ClientDatabaseMiddleware
Location: `app/Http/Middleware/ClientDatabaseMiddleware.php`

**Purpose:**
- Automatically sets the correct database connection based on user's client
- Handles client context for database operations

## Migration Process

### 1. Run the Migration Command
```bash
php artisan vergeflow:migrate-multi-db --force
```

This command will:
1. Create Vault64 as the first client
2. Migrate all existing data to Vault64's database
3. Create separate databases for existing clients
4. Set up proper database connections

### 2. Database Creation for New Clients
When a new client is created through the admin panel:
1. Client record is created in the main database
2. A new database is automatically created for the client
3. Migrations are run on the new database
4. Admin user is created for the client

## Configuration

### Environment Variables
Add these to your `.env` file:

```env
# Main Database
MAIN_DB_HOST=127.0.0.1
MAIN_DB_PORT=3306
MAIN_DB_DATABASE=vergeflow_main
MAIN_DB_USERNAME=root
MAIN_DB_PASSWORD=

# Client Database (default settings)
CLIENT_DB_HOST=127.0.0.1
CLIENT_DB_PORT=3306
CLIENT_DB_USERNAME=root
CLIENT_DB_PASSWORD=
```

### Database Configuration
The `config/database.php` file includes:
- `main` connection for global data
- `client` connection template for client databases
- Dynamic client connections created at runtime

## Usage

### 1. Creating a New Client
```php
// Through admin panel or programmatically
$client = Client::create([
    'name' => 'New Store',
    'company_name' => 'New Store Inc.',
    'contact_email' => 'admin@newstore.vergeflow.com',
    'subdomain' => 'newstore',
    // ... other fields
]);

// Database is automatically created
$databaseService = new DatabaseService();
$databaseService->createClientDatabase($client);
```

### 2. Working with Client Data
```php
// Models automatically use the correct database
$products = Product::all(); // Uses current client's database

// Explicitly work with a specific client
$client = Client::find(1);
$products = Product::forClient($client)->get();
```

### 3. Super Admin Access
Super admins can access all databases and see all client data through the main database connection.

## Security Features

1. **Data Isolation**: Each client's data is completely isolated in separate databases
2. **Connection Security**: Database connections are created dynamically and securely
3. **Access Control**: Users can only access their assigned client's database
4. **Super Admin Override**: Super admins can access all databases for management

## Benefits

1. **Scalability**: Each client can have their own database server if needed
2. **Security**: Complete data isolation between clients
3. **Performance**: No cross-client data queries
4. **Backup**: Individual client database backups
5. **Compliance**: Better data protection and compliance capabilities

## Maintenance

### Database Backups
Each client database can be backed up individually:
```bash
mysqldump -u root -p vergeflow_clientname_1 > backup_clientname.sql
```

### Monitoring
Monitor database connections and performance for each client separately.

### Troubleshooting
- Check database connections in `storage/logs/laravel.log`
- Verify client database names in the `clients` table
- Ensure proper permissions for database creation

## Future Enhancements

1. **Database Sharding**: Distribute client databases across multiple servers
2. **Read Replicas**: Add read replicas for high-traffic clients
3. **Database Versioning**: Track database schema versions per client
4. **Automated Backups**: Scheduled backups for each client database
5. **Performance Monitoring**: Individual client database performance metrics

## Support

For issues related to the multi-database architecture:
1. Check the Laravel logs for database connection errors
2. Verify client database names and connections
3. Ensure proper MySQL permissions for database creation
4. Contact the development team for complex issues 