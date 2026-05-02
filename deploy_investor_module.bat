@echo off
echo ========================================
echo DEPLOYING INVESTOR MODULE REDESIGN
echo ========================================

echo.
echo [1/5] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [2/5] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [3/5] Running database migrations (if any)...
php artisan migrate --force

echo.
echo [4/5] Testing investor routes...
echo Testing investor.profil.index route...
php artisan route:list | findstr "investor.profil"

echo Testing investor.bagi-hasil.index route...
php artisan route:list | findstr "investor.bagi-hasil"

echo Testing investor.pencairan.index route...
php artisan route:list | findstr "investor.pencairan"

echo.
echo [5/5] Testing controller JSON responses...
php test_investor_controllers.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo CHANGES APPLIED:
echo - Investor Profil page redesigned with Alpine.js
echo - Investor Bagi Hasil page redesigned with Alpine.js  
echo - Investor Pencairan page redesigned with Alpine.js
echo - Updated controllers to return proper JSON for Alpine.js
echo - Removed DataTables dependency completely
echo - Added grid/table toggle view
echo - Improved responsive design with Tailwind CSS
echo - Added toast notifications
echo - Fixed Alpine.js "Cannot read properties of null" errors
echo.
echo CONTROLLER UPDATES:
echo - InvestorProfilController: Updated index method for Alpine.js
echo - InvestorBagiHasilController: Updated index method for Alpine.js
echo - InvestorPencairanController: Updated index method for Alpine.js
echo - All controllers now return proper JSON responses
echo.
echo TESTING INSTRUCTIONS:
echo 1. Navigate to /admin/investor/profil
echo 2. Navigate to /admin/investor/bagi-hasil
echo 3. Navigate to /admin/investor/pencairan
echo 4. Test grid/table view toggle
echo 5. Test search and filtering
echo 6. Test CRUD operations
echo.
echo If you encounter any issues, check:
echo - Browser console for JavaScript errors
echo - Laravel logs for backend errors
echo - Ensure all routes are properly defined
echo - Verify controller methods return JSON
echo.
pause