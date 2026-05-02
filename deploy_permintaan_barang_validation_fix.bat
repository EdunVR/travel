@echo off
echo === DEPLOYING PERMINTAAN BARANG VALIDATION FIX ===

echo.
echo 1. Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Optimizing for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo 3. Validation fix deployment complete!
echo.
echo === TESTING INSTRUCTIONS ===
echo 1. Open browser and navigate to Permintaan Barang module
echo 2. Click edit on any item
echo 3. Test validation by submitting invalid data
echo 4. Verify today's date is now accepted
echo 5. Check error messages are user-friendly
echo.
echo === STATUS: READY FOR USE ===
pause