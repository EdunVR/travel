@echo off
echo ========================================
echo FIXING SPAREPART ROUTE ERROR
echo ========================================

echo.
echo [1/3] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/3] Caching routes...
php artisan route:cache
php artisan config:cache

echo.
echo [3/3] Testing route...
php artisan route:list --name=sparepart.adjust-price

echo.
echo ========================================
echo ROUTE FIX COMPLETED!
echo ========================================
echo.
echo Fixed Issues:
echo - Updated route names to use correct prefix
echo - Added outlet_id field to sparepart form
echo - Fixed JavaScript form object
echo - Cleared and cached routes
echo.
echo Please test:
echo 1. Access sparepart page
echo 2. Try to add new sparepart
echo 3. Try to adjust price (if have permission)
echo 4. Try to adjust stock
echo.
pause