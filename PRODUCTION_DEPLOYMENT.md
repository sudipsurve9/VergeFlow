# VergeFlow Production Deployment Guide

## 🚀 Overview

This guide covers the production deployment of VergeFlow, a multi-tenant e-commerce platform with isolated client databases.

## 📋 Prerequisites

### System Requirements
- **PHP**: 8.1 or higher
- **MySQL**: 8.0 or higher
- **Composer**: Latest version
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **SSL Certificate**: Required for production
- **Redis**: For caching and sessions (recommended)

### Server Specifications
- **CPU**: 2+ cores
- **RAM**: 4GB+ (8GB recommended)
- **Storage**: 50GB+ SSD
- **Network**: Stable internet connection

## 🔧 Installation Steps

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-redis composer git unzip

# Install Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server
```

### 2. Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE vergeflow_main;
CREATE USER 'vergeflow_user'@'localhost' IDENTIFIED BY 'YOUR_SECURE_PASSWORD';
GRANT ALL PRIVILEGES ON vergeflow_main.* TO 'vergeflow_user'@'localhost';
GRANT CREATE ON *.* TO 'vergeflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Clone or upload application
cd /var/www
sudo git clone YOUR_REPOSITORY_URL vergeflow
sudo chown -R www-data:www-data vergeflow
sudo chmod -R 755 vergeflow

# Run deployment script
cd vergeflow
chmod +x deploy_production.sh
./deploy_production.sh
```

### 4. Environment Configuration

Edit `.env` file with production settings:

```env
APP_NAME=VergeFlow
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vergeflow_main
DB_USERNAME=vergeflow_user
DB_PASSWORD=YOUR_SECURE_PASSWORD

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 5. Web Server Configuration

#### Nginx Configuration

Create `/etc/nginx/sites-available/vergeflow`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    
    root /var/www/vergeflow/public;
    index index.php index.html index.htm;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/vergeflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 Security Configuration

### 1. Firewall Setup

```bash
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 2. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 3. File Permissions

```bash
sudo chown -R www-data:www-data /var/www/vergeflow
sudo chmod -R 755 /var/www/vergeflow
sudo chmod -R 775 /var/www/vergeflow/storage
sudo chmod -R 775 /var/www/vergeflow/bootstrap/cache
```

## 📊 Monitoring and Maintenance

### 1. Health Checks

```bash
# Manual health check
php artisan vergeflow:health-check

# Automated health check (runs daily at 2 AM)
# Configured in app/Console/Kernel.php
```

### 2. Database Backups

```bash
# Manual backup
php artisan vergeflow:backup-databases --all

# Automated backup (runs daily at 3 AM)
# Configured in app/Console/Kernel.php
```

### 3. System Optimization

```bash
# Manual optimization
php artisan vergeflow:optimize-system

# Automated optimization (runs monthly)
# Configured in app/Console/Kernel.php
```

### 4. Log Monitoring

```bash
# View application logs
tail -f /var/www/vergeflow/storage/logs/laravel.log

# View nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
```

## 🔄 Backup and Recovery

### 1. Automated Backups

The system automatically creates daily backups:
- **Location**: `/var/backups/vergeflow/`
- **Retention**: 30 days
- **Content**: All client databases + main database

### 2. Manual Backup

```bash
# Backup all databases
php artisan vergeflow:backup-databases --all

# Backup specific client
php artisan vergeflow:backup-databases --client-id=1

# Backup main database only
php artisan vergeflow:backup-databases --main
```

### 3. Restore from Backup

```bash
# Restore main database
mysql -u vergeflow_user -p vergeflow_main < backup_file.sql

# Restore client database
mysql -u vergeflow_user -p client_database_name < client_backup.sql
```

## 🚨 Troubleshooting

### Common Issues

#### 1. Database Connection Issues
```bash
# Check database service
sudo systemctl status mysql

# Test connection
mysql -u vergeflow_user -p -h localhost
```

#### 2. Permission Issues
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/vergeflow
sudo chmod -R 755 /var/www/vergeflow
```

#### 3. Cache Issues
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Client Database Issues
```bash
# Debug database creation
php artisan vergeflow:debug-db-creation

# Fix missing databases
php artisan vergeflow:fix-missing-databases

# Migrate all client databases (IMPORTANT: Run after deployment)
php artisan clients:migrate --force

# Migrate specific client database
php artisan clients:migrate --client=1 --force

# Run fresh migrations on client database (WARNING: Drops all data)
php artisan clients:migrate --client=1 --fresh --force
```

### Performance Optimization

#### 1. Database Optimization
```bash
# Optimize all databases
php artisan vergeflow:optimize-system
```

#### 2. Cache Configuration
```bash
# Enable Redis caching
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 3. Queue Processing
```bash
# Start queue worker
php artisan queue:work --daemon

# Monitor queue
php artisan queue:monitor
```

## 📈 Scaling Considerations

### 1. Horizontal Scaling
- Use load balancer for multiple application servers
- Implement Redis cluster for session sharing
- Use database replication for read scaling

### 2. Vertical Scaling
- Increase server resources (CPU, RAM, Storage)
- Optimize database queries and indexes
- Implement caching strategies

### 3. Database Scaling
- Consider database sharding for large clients
- Implement read replicas
- Use connection pooling

## 🔧 Maintenance Commands

### Daily Tasks
```bash
# Health check
php artisan vergeflow:health-check

# Check disk space
df -h

# Monitor logs
tail -n 100 storage/logs/laravel.log
```

### Weekly Tasks
```bash
# Clean up old backups
php artisan vergeflow:cleanup-backups

# Update system packages
sudo apt update && sudo apt upgrade
```

### Monthly Tasks
```bash
# System optimization
php artisan vergeflow:optimize-system

# Review and rotate logs
sudo logrotate /etc/logrotate.conf
```

## 📞 Support

For production support:
1. Check the logs: `tail -f storage/logs/laravel.log`
2. Run health check: `php artisan vergeflow:health-check`
3. Review system resources: `htop`, `df -h`, `free -h`
4. Check database status: `sudo systemctl status mysql`

## 📝 Change Log

- **v1.0.0**: Initial production deployment
- Multi-database architecture implemented
- Automated backup and health check systems
- Production optimization and security measures 