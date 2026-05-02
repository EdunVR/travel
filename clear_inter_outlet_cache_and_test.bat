@echo off
echo 🧹 CLEARING INTER OUTLET CACHE AND TESTING
echo ==========================================

echo.
echo 1. CLEARING LARAVEL CACHE:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. CLEARING COMPILED VIEWS:
if exist "storage/framework/views" (
    del /q "storage\framework\views\*.php" 2>nul
    echo    ✅ Cleared compiled views
) else (
    echo    ℹ️  No compiled views to clear
)

echo.
echo 3. TESTING CURRENT IMPLEMENTATION:
php test_inter_outlet_pdf_final_fix.php

echo.
echo 4. VERIFICATION COMPLETE!
echo.
echo 🧪 NEXT STEPS:
echo    1. Open browser and clear cache (Ctrl+Shift+Delete)
echo    2. Hard refresh the inter-outlet page (Ctrl+F5)
echo    3. Test the print functionality
echo    4. Check browser console for any remaining errors
echo.
echo ✅ Cache clearing complete!
pause