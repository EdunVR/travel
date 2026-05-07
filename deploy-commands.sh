#!/bin/bash
# Script untuk deploy affiliate fee spesifik di production server
# Jalankan di server: bash deploy-commands.sh

echo "=== DEPLOYMENT: Affiliate Fee Spesifik ==="
echo ""

# 1. Check current directory
echo "1. Checking directory..."
pwd
echo ""

# 2. Pull latest changes (jika belum auto-deploy)
echo "2. Pulling latest changes..."
git pull origin main
echo ""

# 3. Run migration
echo "3. Running migration..."
php artisan migrate --force
echo ""

# 4. Clear all cache
echo "4. Clearing cache..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo ""

# 5. Verify migration
echo "5. Verifying migration..."
php artisan migrate:status | grep "add_specific_affiliator_fee_settings"
echo ""

# 6. Check database columns
echo "6. Checking database columns..."
php artisan tinker --execute="
\$columns = DB::select('SHOW COLUMNS FROM affiliate_hierarchy_settings WHERE Field IN (\'from_affiliator_id\', \'to_affiliator_id\')');
echo count(\$columns) === 2 ? '✅ Kolom baru ada' : '❌ Kolom belum ada';
echo PHP_EOL;
"
echo ""

echo "=== DEPLOYMENT COMPLETE ==="
echo ""
echo "Next steps:"
echo "1. Buka https://hmtourtravel.com/admin/inventaris/affiliate/hierarchy/tree"
echo "2. Test fee setting untuk 2 downline dengan level sama"
echo "3. Verifikasi fee tersimpan spesifik (tidak saling mempengaruhi)"
echo ""
