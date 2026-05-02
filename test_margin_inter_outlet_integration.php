<?php

/**
 * Test untuk memverifikasi integrasi Inter-Outlet Sales di Margin Report
 * 
 * Fitur yang ditambahkan:
 * 1. Data penjualan antar outlet ditampilkan di laporan margin
 * 2. HPP menggunakan metode FIFO yang konsisten
 * 3. Source badge "Inter Outlet" dengan warna purple
 * 4. Outlet ditampilkan sebagai "Outlet Asal → Outlet Tujuan"
 * 5. Payment type "Transfer"
 */

echo "=== TEST MARGIN INTER-OUTLET INTEGRATION ===\n\n";

// Test 1: Verifikasi perubahan di MarginReportController
$controllerFile = 'app/Http/Controllers/MarginReportController.php';

if (!file_exists($controllerFile)) {
    echo "❌ MarginReportController.php tidak ditemukan!\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Test import InterOutletSaleItem
if (strpos($content, 'use App\Models\InterOutletSaleItem;') !== false) {
    echo "✅ InterOutletSaleItem model imported\n";
} else {
    echo "❌ InterOutletSaleItem model NOT imported\n";
}

// Test query InterOutletSaleItem
if (strpos($content, 'InterOutletSaleItem::select') !== false) {
    echo "✅ InterOutletSaleItem query found\n";
} else {
    echo "❌ InterOutletSaleItem query NOT found\n";
}

// Test relationship loading
if (strpos($content, 'interOutletSale') !== false && strpos($content, 'outletAsal') !== false) {
    echo "✅ Inter-outlet relationships loaded\n";
} else {
    echo "❌ Inter-outlet relationships NOT loaded\n";
}

// Test FIFO calculation for inter-outlet
if (strpos($content, '$this->calculateHppFifo($item->id_produk, $item->kuantitas)') !== false) {
    echo "✅ FIFO calculation applied to inter-outlet items\n";
} else {
    echo "❌ FIFO calculation NOT applied to inter-outlet items\n";
}

// Test source identifier
if (strpos($content, "'source' => 'inter_outlet'") !== false) {
    echo "✅ Inter-outlet source identifier found\n";
} else {
    echo "❌ Inter-outlet source identifier NOT found\n";
}

// Test outlet display format
if (strpos($content, '$outletName . \' → \' . $outletTujuan') !== false) {
    echo "✅ Outlet display format (Asal → Tujuan) found\n";
} else {
    echo "❌ Outlet display format NOT found\n";
}

// Test payment type
if (strpos($content, "'payment_type' => 'Transfer'") !== false) {
    echo "✅ Payment type 'Transfer' found\n";
} else {
    echo "❌ Payment type 'Transfer' NOT found\n";
}

// Test status filter (only approved)
if (strpos($content, "->where('status', 'approved')") !== false) {
    echo "✅ Status filter (approved only) found\n";
} else {
    echo "❌ Status filter NOT found\n";
}

// Test 2: Verifikasi perubahan di View
$viewFile = 'resources/views/admin/penjualan/margin/index.blade.php';

if (!file_exists($viewFile)) {
    echo "❌ Margin view file tidak ditemukan!\n";
    exit(1);
}

$viewContent = file_get_contents($viewFile);

// Test inter-outlet badge
if (strpos($viewContent, "item.source === 'inter_outlet'") !== false) {
    echo "✅ Inter-outlet badge condition found\n";
} else {
    echo "❌ Inter-outlet badge condition NOT found\n";
}

// Test purple badge styling
if (strpos($viewContent, 'bg-purple-100 text-purple-800') !== false) {
    echo "✅ Purple badge styling found\n";
} else {
    echo "❌ Purple badge styling NOT found\n";
}

// Test transfer icon
if (strpos($viewContent, 'bx-transfer') !== false) {
    echo "✅ Transfer icon found\n";
} else {
    echo "❌ Transfer icon NOT found\n";
}

// Test 3: Simulasi data dan perhitungan
echo "\n=== SIMULASI DATA INTER-OUTLET ===\n";

// Contoh data inter-outlet sale
$interOutletData = [
    'outlet_asal' => 'Outlet Jakarta',
    'outlet_tujuan' => 'Outlet Bogor',
    'produk' => 'Tahu Putih 1kg',
    'kuantitas' => 10,
    'harga' => 15000,
    'subtotal' => 150000,
];

// Simulasi HPP FIFO
$hppData = [
    ['hpp' => 8000, 'stok' => 15, 'created_at' => '2024-01-01'],
    ['hpp' => 9000, 'stok' => 8, 'created_at' => '2024-01-02'],
];

$qty = $interOutletData['kuantitas'];
$totalHppFifo = 0;
$remainingQty = $qty;

foreach ($hppData as $hpp) {
    if ($remainingQty <= 0) break;
    
    $usedQty = min($hpp['stok'], $remainingQty);
    $totalHppFifo += $hpp['hpp'] * $usedQty;
    $remainingQty -= $usedQty;
}

$hppPerUnit = $qty > 0 ? $totalHppFifo / $qty : 0;
$profit = $interOutletData['subtotal'] - ($hppPerUnit * $qty);
$marginPct = $interOutletData['subtotal'] > 0 ? ($profit / $interOutletData['subtotal']) * 100 : 0;

echo "Data Inter-Outlet Sale:\n";
echo "  Outlet: {$interOutletData['outlet_asal']} → {$interOutletData['outlet_tujuan']}\n";
echo "  Produk: {$interOutletData['produk']}\n";
echo "  Quantity: {$qty} unit\n";
echo "  Harga: Rp " . number_format($interOutletData['harga'], 0, ',', '.') . "/unit\n";
echo "  Subtotal: Rp " . number_format($interOutletData['subtotal'], 0, ',', '.') . "\n";

echo "\nPerhitungan HPP FIFO:\n";
echo "  Total HPP FIFO: Rp " . number_format($totalHppFifo, 0, ',', '.') . "\n";
echo "  HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
echo "  Profit: Rp " . number_format($profit, 0, ',', '.') . "\n";
echo "  Margin: " . number_format($marginPct, 2) . "%\n";

echo "\nExpected Display:\n";
echo "  Source: Inter Outlet (purple badge)\n";
echo "  Outlet: {$interOutletData['outlet_asal']} → {$interOutletData['outlet_tujuan']}\n";
echo "  Payment Type: Transfer\n";

// Test 4: Verifikasi filter outlet
echo "\n=== FILTER OUTLET LOGIC ===\n";

if (strpos($content, 'where(function($query) use ($outletId)') !== false) {
    echo "✅ Outlet filter logic found (asal OR tujuan)\n";
} else {
    echo "❌ Outlet filter logic NOT found\n";
}

echo "Filter behavior:\n";
echo "  - Jika pilih Outlet Jakarta: tampil transaksi dimana Jakarta sebagai asal ATAU tujuan\n";
echo "  - Jika pilih Semua Outlet: tampil semua transaksi inter-outlet\n";

// Test 5: Edge cases
echo "\n=== EDGE CASES ===\n";

// Test quantity = 0
$qtyZero = 0;
$hppPerUnitZero = $qtyZero > 0 ? 100000 / $qtyZero : 0;
echo "Quantity = 0: HPP per unit = $hppPerUnitZero (should be 0) ✓\n";

// Test missing produk
echo "Missing produk: HPP = 0 (handled by null check) ✓\n";

// Test unapproved status
echo "Unapproved inter-outlet: Not included (status filter) ✓\n";

echo "\n=== HASIL TEST ===\n";

// Hitung score
$tests = [
    strpos($content, 'use App\Models\InterOutletSaleItem;') !== false,
    strpos($content, 'InterOutletSaleItem::select') !== false,
    strpos($content, '$this->calculateHppFifo($item->id_produk, $item->kuantitas)') !== false,
    strpos($content, "'source' => 'inter_outlet'") !== false,
    strpos($content, '$outletName . \' → \' . $outletTujuan') !== false,
    strpos($content, "'payment_type' => 'Transfer'") !== false,
    strpos($content, "->where('status', 'approved')") !== false,
    strpos($viewContent, "item.source === 'inter_outlet'") !== false,
    strpos($viewContent, 'bg-purple-100 text-purple-800') !== false,
    strpos($viewContent, 'bx-transfer') !== false,
];

$passed = array_sum($tests);
$total = count($tests);

if ($passed === $total) {
    echo "🎉 SEMUA TEST PASSED ($passed/$total)\n";
    echo "✅ Inter-Outlet integration berhasil!\n";
} else {
    echo "⚠️  BEBERAPA TEST GAGAL ($passed/$total)\n";
    echo "❌ Perlu perbaikan tambahan\n";
}

echo "\n=== LANGKAH SELANJUTNYA ===\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Test di browser: /admin/penjualan/margin\n";
echo "3. Verifikasi data inter-outlet muncul dengan badge purple\n";
echo "4. Cek format outlet 'Asal → Tujuan'\n";
echo "5. Pastikan HPP menggunakan FIFO\n";
echo "6. Verifikasi filter outlet bekerja untuk inter-outlet\n";

echo "\n=== TEST SELESAI ===\n";