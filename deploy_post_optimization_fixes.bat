
@echo off
echo Deploying post-optimization fixes...

echo.
echo 1. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo 3. Restarting queue workers...
php artisan queue:restart

echo.
echo 4. Running migrations (if any)...
php artisan migrate --force

echo.
echo ✅ Post-optimization fixes deployed successfully!
echo.
echo Please test the following:
echo - Sparepart page Alpine.js functionality
echo - Production form product_id saving
echo - All modal interactions
echo.
pause
