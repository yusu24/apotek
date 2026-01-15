#!/bin/bash

# Deployment Script for Google Cloud (Production)
# Run this on the server to sync everything

echo "Starting synchronization..."

# 1. Pull latest code from Git
echo "Pulling latest changes from Git..."
git pull origin main

# 2. Update dependencies
echo "Installing/Updating Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing/Updating NPM dependencies..."
npm install

# 3. Rebuild Assets (Crucial for UI changes)
echo "Building assets with Vite..."
npm run build

# 4. Clear and Optimize Laravel
echo "Clearing and optimizing Laravel caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Synchronization complete! Please refresh your browser (Hard Refresh: Ctrl+F5)."
