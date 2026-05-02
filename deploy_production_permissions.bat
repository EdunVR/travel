@echo off
echo ========================================
echo DEPLOY PERMISSION PRODUKSI (MRP)
echo ========================================
echo.

echo [1/4] Membuat permission untuk Produksi...
php fix_production_permissions_complete.php
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal membuat permission!
    pause
    exit /b 1
)

echo.
echo [2/4] Clear cache...
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo.
echo [3/4] Verifikasi permission...
php check_production_complete.php

echo.
echo [4/4] Summary...
echo.
echo ========================================
echo DEPLOYMENT SELESAI!
echo ========================================
echo.
echo PERUBAHAN:
echo 1. Permission Data Produksi dibuat (6 permissions)
echo 2. Permission 'edit' ditambahkan untuk BOM, Production Plan, Work Order
echo 3. Config sidebar diperbaiki
echo 4. Permission check ditambahkan di view actions
echo.
echo Total: 9 permission baru dibuat
echo Total permission Produksi: 25 permissions
echo.
echo TESTING:
echo 1. Buka halaman Role ^& Permission
echo 2. Cek modal - submenu Data Produksi harus muncul dengan checkbox:
echo    - View, Create, Edit, Delete, Approve
echo 3. Cek sidebar - menu Produksi harus muncul sesuai permission
echo 4. Cek tombol Edit/Delete/Approve hanya muncul jika ada permission
echo.
pause
