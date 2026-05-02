@echo off
echo ==========================================
echo DEPLOYING HIERARCHICAL ASSET ACCOUNTS
echo ==========================================
echo.

echo 1. Checking asset accounts structure...
php check_asset_accounts_by_outlet.php
echo.

echo 2. Testing hierarchical structure...
php test_permintaan_barang_hierarchical_accounts.php
echo.

echo 3. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo 4. Deployment completed successfully!
echo.
echo WHAT WAS IMPLEMENTED:
echo ✓ Hierarchical asset account display
echo ✓ Parent accounts with children are disabled (not selectable)
echo ✓ Child accounts are indented and selectable
echo ✓ Parent accounts without children remain selectable
echo ✓ Visual indicators (📁 for parents, 📄 for children)
echo ✓ Proper CSS styling for disabled vs selectable accounts
echo ✓ Outlet-based filtering maintained
echo.
echo HIERARCHICAL STRUCTURE:
echo - 📁 Parent Account (DISABLED if has children)
echo -     📄 Child Account 1 (SELECTABLE)
echo -     📄 Child Account 2 (SELECTABLE)
echo - 📄 Parent Account (SELECTABLE if no children)
echo.
echo TESTING CHECKLIST:
echo [ ] Parent accounts with children appear but are disabled
echo [ ] Child accounts are indented and selectable
echo [ ] Parent accounts without children are selectable
echo [ ] Visual hierarchy with icons and indentation
echo [ ] Cannot select disabled parent accounts
echo [ ] Form validation works with hierarchical selection
echo [ ] Fixed Asset creation works with selected child account
echo.
echo RECOMMENDED TEST OUTLETS:
echo - Outlet 2 (Pelindung Hewan): 27 asset accounts
echo - Outlet 3 (Bojong Kunci): 30 asset accounts
echo.

pause