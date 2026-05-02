<?php

use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;

// Create test data
$sale = InterOutletSale::create([
    'no_transaksi' => 'TEST-001-' . date('YmdHis'),
    'tanggal' => now(),
    'outlet_asal' => 1,
    'outlet_tujuan' => 2,
    'id_user' => 1,
    'subtotal' => 100000,
    'total' => 100000,
    'status' => 'pending',
    'catatan' => 'Test data for debugging'
]);

// Create test item
InterOutletSaleItem::create([
    'inter_outlet_sale_id' => $sale->id,
    'id_produk' => 1,
    'kuantitas' => 1,
    'harga' => 100000,
    'subtotal' => 100000
]);

echo "Test data created successfully!\n";
echo "Sale ID: {$sale->id}\n";
echo "No Transaksi: {$sale->no_transaksi}\n";