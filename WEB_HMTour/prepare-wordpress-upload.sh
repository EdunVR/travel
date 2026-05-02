#!/bin/bash

# Script untuk mempersiapkan WordPress sebelum upload ke Hostinger
# Jalankan dari folder WEB_HMTour: bash prepare-wordpress-upload.sh

echo "=========================================="
echo "Persiapan Upload WordPress ke Hostinger"
echo "=========================================="
echo ""

# 1. Backup database
echo "1. Backing up database..."
read -p "Enter database name (default: u127727849_hikamimandiri): " db_name
db_name=${db_name:-u127727849_hikamimandiri}

read -p "Enter database user (default: u127727849_hikamimandiri): " db_user
db_user=${db_user:-u127727849_hikamimandiri}

mysqldump -u "$db_user" -p "$db_name" > wordpress_backup_$(date +%Y%m%d).sql

if [ $? -eq 0 ]; then
    echo "✓ Database backup created: wordpress_backup_$(date +%Y%m%d).sql"
else
    echo "✗ Database backup failed. Please backup manually via phpMyAdmin"
fi
echo ""

# 2. Create production wp-config
echo "2. Creating production wp-config..."
if [ ! -f wp-config-production.php ]; then
    cp wp-config.php wp-config-production.php
    echo "✓ wp-config-production.php created"
    echo ""
    echo "⚠ IMPORTANT: Edit wp-config-production.php with Hostinger database credentials:"
    echo "   - DB_NAME"
    echo "   - DB_USER"
    echo "   - DB_PASSWORD"
    echo "   - DB_HOST (change to 'localhost')"
else
    echo "⚠ wp-config-production.php already exists - skipping"
fi
echo ""

# 3. Clear cache and temporary files
echo "3. Cleaning cache and temporary files..."
rm -rf wp-content/cache/*
rm -rf wp-content/uploads/cache/*
find . -name "*.log" -type f -delete
find . -name ".DS_Store" -type f -delete
echo "✓ Cache and temporary files cleaned"
echo ""

# 4. Create compressed archive
echo "4. Creating compressed archive..."
echo "Excluding: .git, node_modules, cache, logs"

tar -czf ../wordpress-hmtour-$(date +%Y%m%d).tar.gz \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='wp-content/cache/*' \
    --exclude='wp-content/uploads/cache/*' \
    --exclude='*.log' \
    --exclude='prepare-wordpress-upload.sh' \
    .

if [ $? -eq 0 ]; then
    echo "✓ Archive created: ../wordpress-hmtour-$(date +%Y%m%d).tar.gz"
else
    echo "✗ Archive creation failed"
fi
echo ""

# 5. Create SQL script for URL replacement
echo "5. Creating URL replacement SQL script..."
cat > update_urls.sql << 'EOF'
-- Update WordPress URLs
-- IMPORTANT: Replace 'https://your-domain.com' with your actual domain

-- Update site URL
UPDATE wp_options 
SET option_value = 'https://your-domain.com' 
WHERE option_name = 'siteurl';

-- Update home URL
UPDATE wp_options 
SET option_value = 'https://your-domain.com' 
WHERE option_name = 'home';

-- Update post content URLs
UPDATE wp_posts 
SET post_content = REPLACE(post_content, 'http://localhost/WEB_HMTour', 'https://your-domain.com');

-- Update post meta
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'http://localhost/WEB_HMTour', 'https://your-domain.com');

-- Update post GUID (optional, but recommended)
UPDATE wp_posts 
SET guid = REPLACE(guid, 'http://localhost/WEB_HMTour', 'https://your-domain.com');

SELECT 'URLs updated successfully!' as Status;
EOF

echo "✓ SQL script created: update_urls.sql"
echo ""

echo "=========================================="
echo "Persiapan Selesai!"
echo "=========================================="
echo ""
echo "Files yang sudah dibuat:"
echo "1. wordpress_backup_$(date +%Y%m%d).sql - Database backup"
echo "2. wordpress-hmtour-$(date +%Y%m%d).tar.gz - Compressed WordPress files"
echo "3. wp-config-production.php - Production config template"
echo "4. update_urls.sql - SQL script untuk update URLs"
echo ""
echo "Langkah selanjutnya:"
echo "1. Edit wp-config-production.php dengan credentials Hostinger"
echo "2. Upload wordpress-hmtour-*.tar.gz ke server"
echo "3. Extract di public_html"
echo "4. Rename wp-config-production.php ke wp-config.php"
echo "5. Import wordpress_backup_*.sql ke database Hostinger"
echo "6. Edit dan jalankan update_urls.sql di phpMyAdmin"
echo "7. Baca PANDUAN_UPLOAD_WORDPRESS_HOSTINGER.md untuk detail lengkap"
echo ""
