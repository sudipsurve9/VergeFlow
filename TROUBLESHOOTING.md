# VergeFlow Multi-Database Troubleshooting Guide

## Common Issues and Solutions

### 1. "Database connection [mysql] not configured"

**Problem:** Laravel can't find the mysql connection in database.php

**Solution:**
- Make sure you've copied the updated `config/database.php` file
- Verify your `.env` file has the correct database settings
- Run: `php setup_vergeflow.php` to check configuration

### 2. "Duplicate entry for key 'clients_subdomain_unique'"

**Problem:** Vault64 client already exists in the database

**Solution:**
- This is normal if you've already run the migration before
- The migration will now skip creating duplicate clients
- Run: `php artisan vergeflow:migrate-multi-db --force` again

### 3. "Failed to create test client database"

**Problem:** New client database creation is failing

**Solutions:**
1. **Check MySQL permissions:**
   ```sql
   GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
   FLUSH PRIVILEGES;
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Fix existing clients:**
   ```bash
   php artisan vergeflow:fix-client-databases
   ```

4. **Check MySQL is running:**
   ```bash
   # Windows (XAMPP)
   net start mysql
   
   # Linux/Mac
   sudo service mysql start
   ```

### 4. "Access denied for user 'root'@'localhost'"

**Problem:** MySQL authentication issue

**Solutions:**
1. **Check your .env file:**
   ```env
   DB_USERNAME=root
   DB_PASSWORD=your_password_here
   ```

2. **Reset MySQL root password:**
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
   FLUSH PRIVILEGES;
   ```

3. **Create a new MySQL user:**
   ```sql
   CREATE USER 'vergeflow'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON *.* TO 'vergeflow'@'localhost' WITH GRANT OPTION;
   FLUSH PRIVILEGES;
   ```

### 5. "Database 'vergeflow_main' doesn't exist"

**Problem:** Main database not created

**Solution:**
```sql
CREATE DATABASE vergeflow_main;
```

### 6. "Migration table not found"

**Problem:** Migrations table doesn't exist

**Solution:**
```bash
php artisan migrate:install
php artisan migrate
```

### 7. "Client database connection failed"

**Problem:** Can't connect to client-specific database

**Solutions:**
1. **Check if database exists:**
   ```sql
   SHOW DATABASES LIKE 'vergeflow_%';
   ```

2. **Recreate client database:**
   ```bash
   php artisan vergeflow:fix-client-databases --client-id=1
   ```

3. **Check database permissions:**
   ```sql
   SHOW GRANTS FOR 'root'@'localhost';
   ```

## Diagnostic Commands

### Check System Status
```bash
# Test database connection
php setup_vergeflow.php

# Check all clients and their databases
php artisan vergeflow:fix-client-databases

# Test multi-database functionality
php test_multi_db.php
```

### Check Laravel Logs
```bash
# View recent errors
tail -f storage/logs/laravel.log

# Search for database errors
grep -i "database\|mysql\|connection" storage/logs/laravel.log
```

### Check Database Status
```sql
-- List all databases
SHOW DATABASES;

-- Check client table
USE vergeflow_main;
SELECT id, name, database_name, subdomain FROM clients;

-- Check if client databases exist
SHOW DATABASES LIKE 'vergeflow_%';
```

## Environment Checklist

Make sure your `.env` file has these settings:

```env
APP_NAME=VergeFlow
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vergeflow_main
DB_USERNAME=root
DB_PASSWORD=your_password

MAIN_DB_HOST=127.0.0.1
MAIN_DB_PORT=3306
MAIN_DB_DATABASE=vergeflow_main
MAIN_DB_USERNAME=root
MAIN_DB_PASSWORD=your_password

CLIENT_DB_HOST=127.0.0.1
CLIENT_DB_PORT=3306
CLIENT_DB_USERNAME=root
CLIENT_DB_PASSWORD=your_password
```

## Step-by-Step Recovery

If everything is broken, follow this recovery process:

1. **Backup existing data:**
   ```bash
   mysqldump -u root -p vergeflow_main > backup_main.sql
   ```

2. **Reset environment:**
   ```bash
   cp env_template.txt .env
   # Edit .env with your database settings
   ```

3. **Recreate main database:**
   ```sql
   DROP DATABASE IF EXISTS vergeflow_main;
   CREATE DATABASE vergeflow_main;
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate:fresh
   php artisan db:seed
   ```

5. **Run multi-database migration:**
   ```bash
   php artisan vergeflow:migrate-multi-db --force
   ```

6. **Test the setup:**
   ```bash
   php test_multi_db.php
   ```

## Getting Help

If you're still having issues:

1. **Check the logs:** `storage/logs/laravel.log`
2. **Run diagnostics:** `php setup_vergeflow.php`
3. **Check MySQL status:** `mysql -u root -p -e "SHOW DATABASES;"`
4. **Verify permissions:** Check if your MySQL user can create databases

## Common Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| `SQLSTATE[HY000] [1045] Access denied` | Wrong password | Update DB_PASSWORD in .env |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL not running | Start MySQL service |
| `SQLSTATE[42S01] Table already exists` | Migration already run | Use `--force` flag |
| `SQLSTATE[42000] Access denied for database` | No CREATE privilege | Grant privileges to user |
| `SQLSTATE[23000] Duplicate entry` | Data already exists | Skip creation or use unique values | 