@echo off
echo ========================================
echo DEPLOY PERMISSION DAFTAR AKUN
echo ========================================
echo.

echo [1/2] Membuat permission untuk Daftar Akun...
php create_akun_permissions.php
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal membuat permission!
    pause
    exit /b 1
)

echo.
echo [2/2] Verifikasi permission...
php check_akun_permissions.php

echo.
echo ========================================
echo DEPLOYMENT SELESAI!
echo ========================================
echo.
echo Submenu "Daftar Akun" sekarang sudah tersedia di modal Role Permission.
echo.
echo TESTING:
echo 1. Buka halaman Role ^& Permission
echo 2. Klik "Tambah Role" atau edit role
echo 3. Cek menu Keuangan (F^&A)
echo 4. Verifikasi submenu "Daftar Akun" muncul dengan checkbox CRUD
echo.
pause
