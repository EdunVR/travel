@echo off
echo ===================================
echo DEPLOYING INTER OUTLET PDF MODAL
echo ===================================
echo.

echo 1. Testing PDF modal implementation...
php test_inter_outlet_pdf_modal.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
echo.

echo 3. Clearing config cache...
php artisan config:clear
echo.

echo 4. Clearing route cache...
php artisan route:clear
echo.

echo 5. Clearing view cache...
php artisan view:clear
echo.

echo 6. Optimizing application...
php artisan optimize
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================
echo.
echo PRINT INVOICE NOW USES PDF MODAL:
echo 1. No more new browser tabs
echo 2. PDF opens in elegant modal overlay
echo 3. Large viewing area (90vh height)
echo 4. Easy to close (X button or click outside)
echo 5. Smooth transitions and animations
echo.
echo MODAL FEATURES:
echo - Full-screen overlay with backdrop
echo - Responsive design (max-width 6xl)
echo - Header with title and close button
echo - PDF iframe for seamless viewing
echo - Click outside to close functionality
echo.
echo TEST INSTRUCTIONS:
echo 1. Create inter-outlet transaction
echo 2. Click "Print Invoice" from success modal
echo 3. Verify PDF opens in modal (not new tab)
echo 4. Test close functionality
echo 5. Test print from history modal
echo.
pause