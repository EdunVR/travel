@echo off
echo ========================================
echo DEPLOY PERMISSION LAPORAN KEUANGAN
echo ========================================
echo.

echo [1/4] Membuat permission untuk Neraca Saldo...
php create_finance_reports_permissions.php
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal membuat permission!
    pause
    exit /b 1
)

echo.
echo [2/4] Verifikasi permission...
php check_finance_reports_permissions.php

echo.
echo [3/4] Clear cache...
php artisan route:clear
php artisan config:clear
php artisan view:clear

echo.
echo [4/4] Verifikasi final...
php check_akun_permissions.php

echo.
echo ========================================
echo DEPLOYMENT SELESAI!
echo ========================================
echo.
echo PERUBAHAN:
echo 1. Permission Neraca Saldo sudah dibuat
echo 2. Route alias laba-rugi dan arus-kas sudah ditambah
echo 3. Permission check sudah ditambah di view Neraca Saldo
echo.
echo TESTING:
echo 1. Buka halaman Role ^& Permission
echo 2. Cek menu Keuangan (F^&A)
echo 3. Verifikasi submenu berikut muncul:
echo    - Neraca Saldo
echo    - Laporan Laba Rugi
echo    - Arus Kas
echo.
pause
