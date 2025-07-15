#!/bin/bash

# VergeFlow Production Deployment Script
# This script automates the deployment of VergeFlow to production

set -e  # Exit on any error

echo "=== VergeFlow Production Deployment ==="
echo "Starting deployment process..."

# Configuration
APP_NAME="VergeFlow"
DEPLOY_PATH="/var/www/vergeflow"
BACKUP_PATH="/var/backups/vergeflow"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Step 1: Pre-deployment checks
print_status "Step 1: Pre-deployment checks"

# Check if running as root or with sudo
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# Check if required tools are installed
command -v composer >/dev/null 2>&1 || { print_error "Composer is required but not installed. Aborting."; exit 1; }
command -v php >/dev/null 2>&1 || { print_error "PHP is required but not installed. Aborting."; exit 1; }
command -v mysql >/dev/null 2>&1 || { print_error "MySQL client is required but not installed. Aborting."; exit 1; }

print_status "✓ All required tools are available"

# Step 2: Create backup
print_status "Step 2: Creating backup of current system"

if [ -d "$DEPLOY_PATH" ]; then
    # Create backup directory
    mkdir -p "$BACKUP_PATH"
    
    # Backup current application
    tar -czf "$BACKUP_PATH/vergeflow_backup_$TIMESTAMP.tar.gz" -C "$(dirname $DEPLOY_PATH)" "$(basename $DEPLOY_PATH)"
    print_status "✓ Application backup created: vergeflow_backup_$TIMESTAMP.tar.gz"
    
    # Backup databases
    cd "$DEPLOY_PATH"
    php artisan vergeflow:backup-databases --all --path="$BACKUP_PATH"
    print_status "✓ Database backups completed"
else
    print_warning "No existing deployment found, skipping backup"
fi

# Step 3: Deploy application
print_status "Step 3: Deploying application"

# Create deployment directory if it doesn't exist
mkdir -p "$DEPLOY_PATH"

# Copy application files (assuming current directory is the source)
cp -r . "$DEPLOY_PATH/"
print_status "✓ Application files copied"

# Step 4: Set up environment
print_status "Step 4: Setting up environment"

cd "$DEPLOY_PATH"

# Copy production environment file
if [ -f "env_production_template.txt" ]; then
    cp env_production_template.txt .env
    print_warning "Please edit .env file with your production settings"
    print_warning "Press Enter when ready to continue..."
    read
else
    print_error "Production environment template not found"
    exit 1
fi

# Step 5: Install dependencies
print_status "Step 5: Installing dependencies"

composer install --no-dev --optimize-autoloader
print_status "✓ Dependencies installed"

# Step 6: Set permissions
print_status "Step 6: Setting permissions"

sudo chown -R www-data:www-data "$DEPLOY_PATH"
sudo chmod -R 755 "$DEPLOY_PATH"
sudo chmod -R 775 storage bootstrap/cache
print_status "✓ Permissions set"

# Step 7: Generate application key
print_status "Step 7: Generating application key"

php artisan key:generate
print_status "✓ Application key generated"

# Step 8: Run database migrations
print_status "Step 8: Running database migrations"

php artisan migrate --force
print_status "✓ Database migrations completed"

# Step 9: Run seeders
print_status "Step 9: Running seeders"

php artisan db:seed --force
print_status "✓ Database seeders completed"

# Step 10: Run multi-database migration
print_status "Step 10: Setting up multi-database architecture"

php artisan vergeflow:migrate-multi-db --force
print_status "✓ Multi-database architecture configured"

# Step 11: Clear caches
print_status "Step 11: Clearing caches"

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_status "✓ Caches cleared"

# Step 12: Optimize for production
print_status "Step 12: Optimizing for production"

php artisan config:cache
php artisan route:cache
php artisan view:cache
print_status "✓ Application optimized"

# Step 13: Set up scheduled tasks
print_status "Step 13: Setting up scheduled tasks"

# Add to crontab if not already present
(crontab -l 2>/dev/null; echo "* * * * * cd $DEPLOY_PATH && php artisan schedule:run >> /dev/null 2>&1") | crontab -
print_status "✓ Scheduled tasks configured"

# Step 14: Test the deployment
print_status "Step 14: Testing deployment"

php test_multi_db.php
print_status "✓ Deployment test completed"

# Step 15: Final checks
print_status "Step 15: Final checks"

# Check if application is accessible
if curl -f -s http://localhost > /dev/null 2>&1; then
    print_status "✓ Application is accessible"
else
    print_warning "Application may not be accessible - check your web server configuration"
fi

# Check database connections
php artisan vergeflow:debug-db-creation
print_status "✓ Database connections verified"

echo ""
echo "=== Deployment Complete ==="
echo "VergeFlow has been successfully deployed to production!"
echo ""
echo "Next steps:"
echo "1. Configure your web server (Apache/Nginx)"
echo "2. Set up SSL certificates"
echo "3. Configure monitoring and logging"
echo "4. Set up automated backups"
echo "5. Test all client functionality"
echo ""
echo "Backup location: $BACKUP_PATH"
echo "Application location: $DEPLOY_PATH"
echo ""
print_status "Deployment completed successfully!" 