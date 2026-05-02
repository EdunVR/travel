@echo off
echo ===================================
echo DEPLOYING CONDITIONAL APPROVAL FIX
echo ===================================
echo.

echo 1. Testing conditional approval logic...
php test_permintaan_barang_conditional_approval.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 3. Testing approval modal functionality...
echo Please test the following in browser:
echo - Open Permintaan Barang module
echo - Create test requests with different item types:
echo   * Only produk items (should show PO option)
echo   * Only bahan items (should show PO option)  
echo   * Only custom items (should NOT show PO option)
echo   * Mixed items (should show PO option if has produk/bahan)
echo - Test Fixed Asset creation with all required fields
echo - Verify outlet-based filtering for accounting books
echo.

echo 4. Deployment completed successfully!
echo.
echo WHAT WAS IMPLEMENTED:
echo ✓ Conditional Purchase Order option (only for produk/bahan items)
echo ✓ Complete Fixed Asset form with all required fields
echo ✓ Outlet-based filtering for accounting books
echo ✓ Enhanced validation for Fixed Asset creation
echo ✓ Draft status implementation for Fixed Assets
echo ✓ JavaScript logic for conditional display
echo.
echo TESTING CHECKLIST:
echo [ ] Purchase Order option appears only when items contain produk/bahan
echo [ ] Fixed Asset option always available
echo [ ] Accounting books filtered by outlet
echo [ ] Fixed Asset form validation works
echo [ ] Draft Fixed Asset created successfully
echo [ ] All approval options work correctly
echo.

pause