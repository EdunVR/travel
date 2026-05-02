@echo off
echo ========================================
echo DEPLOY SPAREPART EXPORT PDF IMPROVEMENTS
echo ========================================
echo.

echo [INFO] Deploying Sparepart Export PDF improvements...
echo - Simple table view for no history export
echo - Stream PDF for all PDF exports
echo - Improved export modal with history options
echo.

echo [INFO] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [INFO] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [SUCCESS] Sparepart Export PDF improvements deployed successfully!
echo.
echo IMPROVEMENTS IMPLEMENTED:
echo 1. ✓ Simple table PDF view (tanpa history)
echo 2. ✓ Detailed PDF view (dengan history)
echo 3. ✓ All PDF exports use stream (open in browser)
echo 4. ✓ Excel exports still download as files
echo 5. ✓ Landscape orientation for better table display
echo 6. ✓ Form submission for PDF stream
echo.
echo TESTING CHECKLIST:
echo [ ] Test export tanpa history - simple table view
echo [ ] Test export dengan history - detailed view
echo [ ] Verify PDF opens in new browser tab
echo [ ] Test Excel export downloads as file
echo [ ] Test with selected items vs all data
echo.
pause