@echo off
echo ========================================
echo DEPLOYING FIXED ASSET DRAFT STATUS
echo ========================================

echo.
echo 1. Running migration for draft status...
php artisan migrate --path=database/migrations/2024_12_24_000001_add_draft_status_to_fixed_assets_table.php --force

echo.
echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 3. Testing database structure...
php test_fixed_asset_draft_simple.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Test creating new fixed asset (should be draft)
echo 2. Test activating draft asset
echo 3. Verify journal entry creation
echo 4. Check that activated assets cannot be deleted
echo.
pause