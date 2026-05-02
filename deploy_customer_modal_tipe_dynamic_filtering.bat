@echo off
echo ========================================
echo CUSTOMER MODAL TIPE DYNAMIC FILTERING
echo ========================================
echo.

echo 🚀 Deploying Customer Modal Tipe Dynamic Filtering...
echo.

echo 📋 Step 1: Clear cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 📋 Step 2: Test backend API...
php test_customer_modal_tipe_filtering.php

echo.
echo 📋 Step 3: Run comprehensive test...
php test_customer_modal_tipe_dynamic_complete.php

echo.
echo ✅ DEPLOYMENT COMPLETE!
echo.
echo 🧪 MANUAL TESTING REQUIRED:
echo 1. Open browser: http://localhost/tofu/admin/crm/pelanggan
echo 2. Click "Tambah Pelanggan" button
echo 3. Select different outlets - tipe dropdown should update
echo 4. Test edit mode with existing customers
echo 5. Test with different user access levels
echo.
echo 🎉 Customer Modal Tipe Dynamic Filtering is ready!
pause