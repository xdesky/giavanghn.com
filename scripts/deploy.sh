#!/bin/bash
set -e

APP_DIR="/var/www/giavanghn"
PHP_VERSION="8.2"

RED='\033[0;31m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${CYAN}── Deploying GiaVangHN ──${NC}"

cd "$APP_DIR"

# Pull latest
git pull origin main

# Dependencies
composer install --no-dev --optimize-autoloader

# Build assets
npm ci
npm run build
rm -rf node_modules

# Migrate
php artisan migrate --force

# Storage link
php artisan storage:link 2>/dev/null || true

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart services
systemctl restart php${PHP_VERSION}-fpm
systemctl restart giavanghn-worker

# Permissions
chown -R www-data:www-data "$APP_DIR"

echo -e "${GREEN}[✓] Deploy hoàn tất!${NC}"
