@echo off
echo ===================================
echo DEPLOYING PRODUCTION MATERIALS UNIT FIX
echo ===================================
echo.

echo 1. Testing the fix...
php test_production_materials_unit_fix.php
echo.

echo 2. Clearing Laravel caches...
php artisan route:clear
php artisan cache:clear
php artisan config:clear
echo.

echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo WHAT WAS FIXED:
echo - Added unit field retrieval from bahan/produk tables
echo - Fixed ProductionMaterial::create() to include unit field
echo - Added proper fallback to 'unit' if no satuan found
echo - Fixed both store() and update() methods
echo.
echo ORIGINAL ERROR RESOLVED:
echo - Field 'unit' doesn't have a default value
echo - SQL insert now includes unit field with proper value
echo.
echo NEXT STEPS:
echo 1. Test production form submission with materials
echo 2. Verify materials are saved with correct units
echo 3. Check that no SQL errors occur
echo.
pause