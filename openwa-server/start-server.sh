#!/bin/bash

echo "========================================"
echo "OpenWA WhatsApp Server - HM Tour"
echo "========================================"
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "ERROR: Node.js tidak terinstall!"
    echo "Install dengan: sudo apt install nodejs npm"
    exit 1
fi

echo "Node.js version:"
node --version
echo ""

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "Installing dependencies..."
    npm install
    echo ""
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "WARNING: File .env tidak ditemukan!"
    echo "Copying .env.example to .env..."
    cp .env.example .env
    echo ""
    echo "PENTING: Edit file .env dan ganti API_KEY!"
    echo ""
    read -p "Press Enter to continue..."
fi

echo "Starting OpenWA server..."
echo ""
echo "CATATAN:"
echo "- Browser Chrome akan terbuka otomatis"
echo "- Scan QR code dengan WhatsApp Anda"
echo "- Setelah tersambung, jangan tutup terminal ini"
echo ""
echo "Press Ctrl+C to stop server"
echo ""

node server.js
