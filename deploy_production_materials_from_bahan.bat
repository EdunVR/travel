@echo off
echo ========================================
echo DEPLOYING PRODUCTION MATERIALS WITH HARGA_BAHAN STOCK
echo ========================================
echo.

echo 1. Testing materials endpoint with FIFO stock...
php test_production_materials_from_bahan.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache
echo.

echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo CHANGES APPLIED:
echo - Materials stock now accumulated from 'harga_bahan' table
echo - Implements proper FIFO system for pricing
echo - Uses average price from harga_bahan for accuracy
echo - HPP calculation uses FIFO pricing (oldest batch first)
echo - Only shows materials with actual stock in harga_bahan
echo - Maintains all previous features (merk, unit, etc.)
echo.
echo FIFO SYSTEM BENEFITS:
echo - Accurate stock tracking from purchase history
echo - Proper cost calculation using oldest prices first
echo - Better inventory management
echo - More precise HPP calculations
echo.
echo NEXT STEPS:
echo 1. Test production form in browser
echo 2. Verify material dropdown shows correct stock from harga_bahan
echo 3. Test HPP preview with FIFO pricing
echo 4. Verify stock calculations are accurate
echo 5. Check that materials without harga_bahan stock are hidden
echo.
pause