#!/bin/bash

# ========================================
# Setup Clone Project - MORRA ERP
# ========================================
# Script ini akan mengkonfigurasi project
# yang di-clone dengan nama folder baru
# ========================================

echo ""
echo "========================================"
echo "  SETUP CLONE PROJECT - MORRA ERP"
echo "========================================"
echo ""
echo "Script ini akan:"
echo "1. Update APP_URL di .env"
echo "2. Update SESSION_PATH di .env"
echo "3. Clear semua cache Laravel"
echo "4. Regenerate config cache"
echo ""
echo "========================================"
echo ""

# Deteksi nama folder otomatis
CURRENT_FOLDER=$(basename "$PWD")
echo "Folder saat ini: $CURRENT_FOLDER"
echo ""

read -p "Gunakan nama folder '$CURRENT_FOLDER'? (Y/n): " CONFIRM
if [[ $CONFIRM == "n" || $CONFIRM == "N" ]]; then
    read -p "Masukkan nama folder project: " FOLDER_NAME
else
    FOLDER_NAME=$CURRENT_FOLDER
fi

echo ""
echo "========================================"
echo "Konfigurasi yang akan diterapkan:"
echo "========================================"
echo "Nama Folder  : $FOLDER_NAME"
echo "APP_URL      : http://localhost/$FOLDER_NAME"
echo "SESSION_PATH : /$FOLDER_NAME"
echo "========================================"
echo ""

read -p "Lanjutkan? (Y/n): " FINAL_CONFIRM
if [[ $FINAL_CONFIRM == "n" || $FINAL_CONFIRM == "N" ]]; then
    echo ""
    echo "Setup dibatalkan."
    exit 0
fi

echo ""
echo "[1/5] Backup .env file..."
BACKUP_FILE=".env.backup.$(date +%Y%m%d_%H%M%S)"
cp .env "$BACKUP_FILE"
echo "      Backup tersimpan sebagai $BACKUP_FILE"

echo ""
echo "[2/5] Updating .env file..."

# Update APP_URL
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|APP_URL=.*|APP_URL=http://localhost/$FOLDER_NAME|g" .env
    sed -i '' "s|SESSION_PATH=.*|SESSION_PATH=/$FOLDER_NAME|g" .env
    sed -i '' "s|SESSION_DOMAIN=.*|SESSION_DOMAIN=null|g" .env
    sed -i '' "s|SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=false|g" .env
else
    # Linux
    sed -i "s|APP_URL=.*|APP_URL=http://localhost/$FOLDER_NAME|g" .env
    sed -i "s|SESSION_PATH=.*|SESSION_PATH=/$FOLDER_NAME|g" .env
    sed -i "s|SESSION_DOMAIN=.*|SESSION_DOMAIN=null|g" .env
    sed -i "s|SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=false|g" .env
fi

echo "      - APP_URL updated"
echo "      - SESSION_PATH updated"
echo "      - SESSION_DOMAIN updated"
echo "      - SESSION_SECURE_COOKIE updated"

echo ""
echo "[3/5] Clearing Laravel cache..."
php artisan config:clear > /dev/null 2>&1
echo "      - Config cache cleared"
php artisan cache:clear > /dev/null 2>&1
echo "      - Application cache cleared"
php artisan route:clear > /dev/null 2>&1
echo "      - Route cache cleared"
php artisan view:clear > /dev/null 2>&1
echo "      - View cache cleared"

echo ""
echo "[4/5] Regenerating config cache..."
php artisan config:cache > /dev/null 2>&1
echo "      - Config cache regenerated"

echo ""
echo "[5/5] Creating storage link..."
php artisan storage:link > /dev/null 2>&1
echo "      - Storage link created"

echo ""
echo "========================================"
echo "  SETUP SELESAI!"
echo "========================================"
echo ""
echo "Konfigurasi baru:"
echo "  APP_URL      : http://localhost/$FOLDER_NAME"
echo "  SESSION_PATH : /$FOLDER_NAME"
echo ""
echo "LANGKAH SELANJUTNYA:"
echo ""
echo "1. RESTART WEB SERVER"
echo "   - Jika Apache: sudo service apache2 restart"
echo "   - Jika Nginx: sudo service nginx restart"
echo "   - Jika Artisan: Ctrl+C lalu php artisan serve"
echo ""
echo "2. CLEAR BROWSER CACHE"
echo "   - Tekan Ctrl+Shift+Delete"
echo "   - Pilih 'Cached images and files'"
echo "   - Klik 'Clear data'"
echo ""
echo "3. AKSES PROJECT"
echo "   - URL: http://localhost/$FOLDER_NAME/admin"
echo "   - Gunakan incognito mode untuk test pertama"
echo ""
echo "4. TEST MENU"
echo "   - Login ke sistem"
echo "   - Klik menu dari sidebar (contoh: Point of Sales)"
echo "   - Pastikan TIDAK ada nested layout"
echo ""
echo "========================================"
echo ""
echo "File backup: $BACKUP_FILE"
echo "Dokumentasi: FIX_INFINITE_MIRROR_COMPLETE.md"
echo ""
