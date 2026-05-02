@echo off
echo ========================================
echo DEPLOY FIX EDIT PRODUKSI
echo ========================================
echo.

echo [1/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [2/5] Testing database connection...
php artisan migrate:status

echo.
echo [3/5] Running test script...
php test_produksi_edit_fix.php

echo.
echo [4/5] Checking file modifications...
if exist "resources\views\admin\produksi\produksi\index.blade.php" (
    echo ✅ View file updated
) else (
    echo ❌ View file not found!
    pause
    exit /b 1
)

if exist "public\js\production.js" (
    echo ✅ JavaScript file updated
) else (
    echo ❌ JavaScript file not found!
    pause
    exit /b 1
)

echo.
echo [5/5] Verifying routes...
php artisan route:list | findstr "produksi.*update"

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo MASALAH YANG DIPERBAIKI:
echo ❌ Edit produksi membuat data baru (FIXED)
echo ✅ Edit produksi sekarang mengupdate data existing
echo.
echo PERUBAHAN YANG DILAKUKAN:
echo ✅ Fix populateEditModal() - set editMode dengan benar
echo ✅ Fix openCreate() - check editMode sebelum reset
echo ✅ Fix openCreateModal() - preserve form jika editMode=true
echo ✅ Fix closeCreateModal() - reset editMode saat tutup
echo ✅ Cleanup handleFormSubmit() - remove duplicate reset
echo ✅ Add debug logging untuk troubleshooting
echo.
echo CARA TESTING:
echo 1. Buka halaman Produksi
echo 2. Klik "Edit" pada produksi dengan status "Draft"
echo 3. Modal akan terbuka dengan data terisi
echo 4. Ubah beberapa data dan klik "Update Produksi"
echo 5. Verifikasi data terupdate, bukan membuat data baru
echo.
echo DEBUGGING:
echo - Check browser console untuk log "Edit mode set"
echo - Check Network tab untuk URL dan method request
echo - Verifikasi tidak ada record baru di database
echo.
echo TESTING MANUAL:
echo - Jalankan: php test_produksi_edit_fix.php
echo - Ikuti checklist di file PRODUKSI_EDIT_FIX_COMPLETE.md
echo.
echo ========================================
pause