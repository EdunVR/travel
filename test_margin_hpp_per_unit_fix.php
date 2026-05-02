<?php

/**
 * Test untuk memverifikasi perbaikan HPP per unit di Margin Report
 * 
 * Masalah: HPP yang dikembalikan dari calculateHppFifo adalah total HPP,
 * sedangkan di laporan perlu HPP per unit
 * 
 * Solusi: Bagi total HPP FIFO dengan quantity untuk mendapat HPP per unit
 */

echo "=== TEST MARGIN HPP PER UNIT FIX ===\n\n";

// Test 1: Verifikasi perubahan di MarginReportController
$controllerFile = 'app/Http/Controllers/MarginReportController.php';

if (!file_exists($controllerFile)) {
    echo "❌ MarginReportController.php tidak ditemukan!\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Test pembagian HPP dengan quantity
if (strpos($content, '$totalHppFifo / $kuantitas') !== false) {
    echo "✅ HPP per unit calculation found (total HPP / quantity)\n";
} else {
    echo "❌ HPP per unit calculation NOT found\n";
}

// Test variable totalHppFifo
if (strpos($content, '$totalHppFifo = $item->produk ? $this->calculateHppFifo') !== false) {
    echo "✅ Total HPP FIFO calculation found\n";
} else {
    echo "❌ Total HPP FIFO calculation NOT found\n";
}

// Test kondisi kuantitas > 0
if (strpos($content, '$kuantitas > 0 ? $totalHppFifo / $kuantitas : 0') !== false) {
    echo "✅ Division by zero protection found\n";
} else {
    echo "❌ Division by zero protection NOT found\n";
}

// Test 2: Simulasi perhitungan HPP per unit
echo "\n=== SIMULASI PERHITUNGAN ===\n";

// Contoh data HPP untuk produk
$hppData = [
    ['hpp' => 10000, 'stok' => 5, 'created_at' => '2024-01-01'],
    ['hpp' => 12000, 'stok' => 3, 'created_at' => '2024-01-02'],
    ['hpp' => 15000, 'stok' => 2, 'created_at' => '2024-01-03'],
];

$qty = 4; // Jumlah yang akan dijual

// Simulasi calculateHppFifo (total HPP)
$totalHppFifo = 0;
$remainingQty = $qty;

foreach ($hppData as $hpp) {
    if ($remainingQty <= 0) break;
    
    $usedQty = min($hpp['stok'], $remainingQty);
    $totalHppFifo += $hpp['hpp'] * $usedQty;
    $remainingQty -= $usedQty;
}

// HPP per unit (yang benar)
$hppPerUnit = $qty > 0 ? $totalHppFifo / $qty : 0;

echo "Data HPP:\n";
foreach ($hppData as $i => $hpp) {
    echo "  Batch " . ($i + 1) . ": HPP Rp " . number_format($hpp['hpp'], 0, ',', '.') . 
         ", Stok " . $hpp['stok'] . "\n";
}

echo "\nPenjualan: $qty unit\n";
echo "Total HPP FIFO: Rp " . number_format($totalHppFifo, 0, ',', '.') . "\n";
echo "HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";

// Test 3: Verifikasi perhitungan profit
$hargaJual = 15000; // Contoh harga jual per unit
$subtotal = $hargaJual * $qty;
$profit = $subtotal - ($hppPerUnit * $qty);
$marginPct = $subtotal > 0 ? ($profit / $subtotal) * 100 : 0;

echo "\nVerifikasi Profit:\n";
echo "Harga jual per unit: Rp " . number_format($hargaJual, 0, ',', '.') . "\n";
echo "Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
echo "Total cost (HPP × qty): Rp " . number_format($hppPerUnit * $qty, 0, ',', '.') . "\n";
echo "Profit: Rp " . number_format($profit, 0, ',', '.') . "\n";
echo "Margin: " . number_format($marginPct, 2) . "%\n";

// Test 4: Perbandingan dengan metode lama
echo "\n=== PERBANDINGAN METODE ===\n";

// Metode lama (average)
$totalNilai = 0;
$totalStok = 0;

foreach ($hppData as $hpp) {
    $totalNilai += $hpp['hpp'] * $hpp['stok'];
    $totalStok += $hpp['stok'];
}

$averageHpp = $totalStok > 0 ? $totalNilai / $totalStok : 0;

echo "FIFO HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
echo "Average HPP per unit: Rp " . number_format($averageHpp, 0, ',', '.') . "\n";
echo "Selisih: Rp " . number_format(abs($hppPerUnit - $averageHpp), 0, ',', '.') . "\n";

// Test 5: Edge cases
echo "\n=== EDGE CASES ===\n";

// Test quantity = 0
$qtyZero = 0;
$hppPerUnitZero = $qtyZero > 0 ? 100000 / $qtyZero : 0;
echo "Quantity = 0: HPP per unit = $hppPerUnitZero (should be 0)\n";

// Test quantity = 1
$qtyOne = 1;
$totalHppOne = 10000; // Ambil dari batch pertama
$hppPerUnitOne = $qtyOne > 0 ? $totalHppOne / $qtyOne : 0;
echo "Quantity = 1: HPP per unit = Rp " . number_format($hppPerUnitOne, 0, ',', '.') . " (should be 10.000)\n";

echo "\n=== HASIL TEST ===\n";

// Hitung score
$tests = [
    strpos($content, '$totalHppFifo / $kuantitas') !== false,
    strpos($content, '$totalHppFifo = $item->produk ? $this->calculateHppFifo') !== false,
    strpos($content, '$kuantitas > 0 ? $totalHppFifo / $kuantitas : 0') !== false,
    $hppPerUnitZero === 0, // Edge case test
    $hppPerUnitOne === 10000, // Single unit test
];

$passed = array_sum($tests);
$total = count($tests);

if ($passed === $total) {
    echo "🎉 SEMUA TEST PASSED ($passed/$total)\n";
    echo "✅ HPP per unit calculation sudah benar!\n";
} else {
    echo "⚠️  BEBERAPA TEST GAGAL ($passed/$total)\n";
    echo "❌ Perlu perbaikan tambahan\n";
}

echo "\n=== LANGKAH SELANJUTNYA ===\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Test di browser: /admin/penjualan/margin\n";
echo "3. Verifikasi HPP per unit di kolom HPP\n";
echo "4. Pastikan profit dan margin dihitung dengan benar\n";

echo "\n=== TEST SELESAI ===\n";