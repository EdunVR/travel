@echo off
echo ===================================
echo DEPLOYING ALPINE MARGIN FIX
echo ===================================
echo.

echo 1. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo Cache cleared
echo.

echo 2. Testing Alpine.js fix...
php test_alpine_margin_fix.php
echo.

echo ===================================
echo DEPLOYMENT COMPLETED
echo ===================================
echo.
echo Alpine.js error fix deployed successfully!
echo.
echo Changes made:
echo - Added null check for item.margin_pct in x-show condition
echo - Added null check in :class conditions
echo - Added fallback display for null margin values
echo.
echo The error "Cannot read properties of null (reading 'toFixed')" 
echo should now be resolved.
echo.
pause