<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSaleItem;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG SPECIFIC ITEM HPP ===\n\n";

// Cek item ID 28 (Tofu Spesial Udang 120g, 8000 qty, 23 Jan 2026)
$item = InterOutletSaleItem::with(['interOutletSale', 'produk'])->find(28);

if ($item) {
    echo "Item ditemukan:\n";
    echo "ID: {$item->id}\n";
    echo "Produk: {$item->produk->nama_produk}\n";
    echo "Quantity: {$item->kuantitas}\n";
    echo "Tanggal: {$item->interOutletSale->tanggal}\n";
    echo "Data HPP: " . ($item->data_hpp ? json_encode($item->data_hpp, JSON_PRETTY_PRINT) : 'NULL') . "\n";
    echo "Data HPP empty check: " . (empty($item->data_hpp) ? 'TRUE' : 'FALSE') . "\n";
    echo "Data HPP is null: " . (is_null($item->data_hpp) ? 'TRUE' : 'FALSE') . "\n";
    echo "Data HPP count: " . (is_array($item->data_hpp) ? count($item->data_hpp) : 'NOT ARRAY') . "\n";
} else {
    echo "Item tidak ditemukan\n";
}

echo "\n=== SELESAI ===\n";