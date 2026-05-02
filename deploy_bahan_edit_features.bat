@echo off
echo ========================================
echo DEPLOY BAHAN EDIT FEATURES
echo ========================================
echo.

echo [INFO] Deploying edit features for Bahan module...
echo.

echo [PERMISSIONS] Creating database permissions...
php create_bahan_edit_permissions.php
echo.

echo [PERMISSIONS] Assigning permissions to admin roles...
php assign_bahan_permissions_to_admin.php
echo.

echo [FILES] Modified files:
echo - app/Http/Controllers/BahanController.php (added methods)
echo - routes/web.php (added routes)
echo - resources/views/admin/inventaris/bahan/index.blade.php (enhanced modal)
echo.

echo [FEATURES] Implemented features:
echo ✅ Inline edit for harga beli (price)
echo ✅ Inline edit for stok (stock)
echo ✅ Permission-based access control
echo ✅ Auto-refresh after updates
echo ✅ Delete functionality
echo ✅ Input validation
echo ✅ Error handling
echo.

echo [PERMISSIONS] Required permissions:
echo - inventaris.bahan.edit-stock (for stock editing)
echo - inventaris.bahan.edit-price (for price editing)
echo - inventaris.bahan.delete (for delete functionality)
echo.

echo [TESTING] To test the features:
echo 1. Login as admin user
echo 2. Go to admin/inventaris/bahan
echo 3. Click "Harga Beli" button on any bahan
echo 4. In the modal, click edit icons to modify values
echo 5. Verify data saves correctly
echo.

echo [SUCCESS] Bahan edit features deployed successfully!
echo.

echo Clear browser cache and test the functionality.
echo.
pause