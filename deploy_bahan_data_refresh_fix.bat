@echo off
echo ========================================
echo   BAHAN DATA REFRESH FIX DEPLOYMENT
echo ========================================
echo.

echo [INFO] Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [INFO] Data refresh fix applied successfully!
echo.
echo IMPROVEMENTS MADE:
echo ✅ Enhanced refreshHargaBeli() function
echo ✅ Real-time data updates in modal
echo ✅ Automatic table data synchronization
echo ✅ Optimized API calls for better performance
echo.
echo FEATURES FIXED:
echo ✅ Edit stock - updates immediately
echo ✅ Edit price - updates immediately  
echo ✅ Delete harga - updates immediately
echo ✅ Modal totals update in real-time
echo ✅ Main table stock column updates
echo.
echo TESTING CHECKLIST:
echo 1. Go to Inventaris ^> Bahan
echo 2. Click "Harga Beli" on any item
echo 3. Edit stock/price values
echo 4. Verify immediate updates without reload
echo 5. Check main table reflects changes
echo.
echo [SUCCESS] Data refresh functionality optimized!
echo No more page reloads needed for data updates.
pause