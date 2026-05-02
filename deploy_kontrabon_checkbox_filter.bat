@echo off
echo ===================================
echo KONTRA BON CHECKBOX FILTER DEPLOYMENT
echo ===================================
echo.

echo 1. Testing implementation...
php test_kontrabon_checkbox_filter.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared successfully!
echo.

echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo Optimization complete!
echo.

echo 4. Testing Kontra Bon page access...
echo Please test the following:
echo - Navigate to Penjualan ^> Kontra Bon
echo - Test Piutang tab with outlet filter
echo - Test List Kontra Bon tab with outlet filter
echo - Verify checkbox functionality
echo - Test select all/clear all buttons
echo.

echo ===================================
echo DEPLOYMENT COMPLETE!
echo ===================================
echo.
echo IMPORTANT: Test both tabs thoroughly!
echo.
pause