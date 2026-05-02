#!/bin/bash

# Script untuk mempersiapkan project Laravel sebelum upload ke Hostinger
# Jalankan: bash prepare-for-upload.sh

echo "=========================================="
echo "Persiapan Upload ke Hostinger"
echo "=========================================="
echo ""

# 1. Clear cache
echo "1. Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✓ Cache cleared"
echo ""

# 2. Install dependencies production
echo "2. Installing production dependencies..."
composer install --optimize-autoloader --no-dev
echo "✓ Dependencies installed"
echo ""

# 3. Build assets
echo "3. Building production assets..."
npm install
npm run build
echo "✓ Assets built"
echo ""

# 4. Create production .env
echo "4. Creating production .env template..."
if [ ! -f .env.production ]; then
    cp .env.production.example .env.production
    echo "✓ .env.production created - EDIT FILE INI SEBELUM UPLOAD!"
else
    echo "⚠ .env.production already exists - skipping"
fi
echo ""

# 5. Create upload package
echo "5. Creating upload package..."
echo "Excluding: node_modules, .git, storage/logs, tests"

# Create temp directory
mkdir -p upload_package

# Copy files (excluding unnecessary ones)
rsync -av --progress \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*' \
    --exclude='tests' \
    --exclude='.env' \
    --exclude='.env.local' \
    --exclude='upload_package' \
    --exclude='*.md' \
    . upload_package/

# Create logs directory structure
mkdir -p upload_package/storage/logs
touch upload_package/storage/logs/.gitkeep

echo "✓ Package created in 'upload_package' folder"
echo ""

# 6. Create compressed archive
echo "6. Creating compressed archive..."
tar -czf laravel-app-$(date +%Y%m%d).tar.gz -C upload_package .
echo "✓ Archive created: laravel-app-$(date +%Y%m%d).tar.gz"
echo ""

echo "=========================================="
echo "Persiapan Selesai!"
echo "=========================================="
echo ""
echo "Langkah selanjutnya:"
echo "1. Edit file .env.production dengan konfigurasi Hostinger"
echo "2. Upload file laravel-app-*.tar.gz ke server"
echo "3. Extract di server dan ikuti PANDUAN_UPLOAD_HOSTINGER.md"
echo ""
echo "File yang perlu diupload:"
echo "- laravel-app-$(date +%Y%m%d).tar.gz (aplikasi utama)"
echo "- .env.production (rename ke .env di server)"
echo ""
