@echo off
echo ========================================
echo DEPLOY SEMUA PERMISSION KEUANGAN (F^&A)
echo ========================================
echo.

echo [1/5] Membuat permission Daftar Akun...
php create_akun_permissions.php
if %errorlevel% neq 0 (
    echo ERROR: Gagal membuat permission Daftar Akun!
    pause
    exit /b 1
)

echo.
echo [2/5] Membuat permission Neraca Saldo...
php create_finance_reports_permissions.php
if %errorlevel% neq 0 (
    echo ERROR: Gagal membuat permission Neraca Saldo!
    pause
    exit /b 1
)

echo.
echo [3/5] Clear cache...
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo.
echo [4/5] Verifikasi permission...
echo.
echo === Daftar Akun ===
php check_akun_permissions.php

echo.
echo === Laporan Keuangan ===
php check_finance_reports_permissions.php

echo.
echo === Buku Besar ===
php check_buku_besar_permission.php

echo.
echo [5/5] Summary...
echo.
echo ========================================
echo DEPLOYMENT SELESAI!
echo ========================================
echo.
echo PERUBAHAN YANG DILAKUKAN:
echo 1. Permission Daftar Akun dibuat (4 permissions)
echo 2. Permission Neraca Saldo dibuat (5 permissions)
echo 3. Route alias laba-rugi dan arus-kas ditambah
echo 4. Permission check ditambah di view Neraca Saldo
echo 5. Config sidebar Buku Besar diperbaiki
echo.
echo TESTING:
echo 1. Buka halaman Role ^& Permission
echo 2. Cek modal - semua menu Keuangan harus muncul:
echo    - Daftar Akun
echo    - Neraca Saldo
echo    - Laporan Laba Rugi
echo    - Arus Kas
echo    - Buku Besar
echo 3. Cek sidebar - menu harus muncul sesuai permission user
echo.
pause
