@echo off
echo ========================================
echo OpenWA WhatsApp Server - HM Tour
echo ========================================
echo.

REM Check if Node.js is installed
where node >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Node.js tidak terinstall!
    echo Download dari: https://nodejs.org/
    pause
    exit /b 1
)

echo Node.js version:
node --version
echo.

REM Check if node_modules exists
if not exist "node_modules" (
    echo Installing dependencies...
    call npm install
    echo.
)

REM Check if .env exists
if not exist ".env" (
    echo WARNING: File .env tidak ditemukan!
    echo Copying .env.example to .env...
    copy .env.example .env
    echo.
    echo PENTING: Edit file .env dan ganti API_KEY!
    echo.
    pause
)

echo Starting OpenWA server...
echo.
echo CATATAN:
echo - Browser Chrome akan terbuka otomatis
echo - Scan QR code dengan WhatsApp Anda
echo - Setelah tersambung, jangan tutup terminal ini
echo.
echo Press Ctrl+C to stop server
echo.

node server.js

pause
