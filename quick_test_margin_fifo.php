<?php

/**
 * Quick Test untuk Margin Report FIFO Fix
 * Test sederhana untuk memastikan perubahan sudah diterapkan
 */

echo "=== QUICK TEST MARGIN REPORT FIFO ===\n\n";

// Test 1: Verifikasi file controller ada dan berisi method yang benar
$controllerFile = 'app/Http/Controllers/MarginReportController.php';

if (!file_exists($controllerFile)) {
    echo "❌ MarginReportController.php tidak ditemukan!\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Test method calculateHppFifo ada
if (strpos($content, 'private function calculateHppFifo') !== false) {
    echo "✅ Method calculateHppFifo ditemukan\n";
} else {
    echo "❌ Method calculateHppFifo tidak ditemukan\n";
}

// Test penggunaan FIFO di getData method
if (strpos($content, '$this->calculateHppFifo($item->id_produk, $item->kuantitas)') !== false) {
    echo "✅ POS items menggunakan FIFO calculation\n";
} else {
    echo "❌ POS items tidak menggunakan FIFO calculation\n";
}

// Test method lama tidak digunakan lagi
if (strpos($content, 'calculateHppBarangDagang()') === false) {
    echo "✅ Method lama (average) sudah dihapus dari POS calculation\n";
} else {
    echo "❌ Method lama (average) masih digunakan\n";
}

// Test 2: Verifikasi struktur method calculateHppFifo
if (strpos($content, 'orderBy(\'created_at\', \'asc\')') !== false) {
    echo "✅ FIFO ordering (created_at ASC) ditemukan\n";
} else {
    echo "❌ FIFO ordering tidak ditemukan\n";
}

if (strpos($content, 'min($hpp->stok, $remainingQty)') !== false) {
    echo "✅ FIFO logic (min stock calculation) ditemukan\n";
} else {
    echo "❌ FIFO logic tidak ditemukan\n";
}

echo "\n=== HASIL TEST ===\n";

// Hitung score
$tests = [
    strpos($content, 'private function calculateHppFifo') !== false,
    strpos($content, '$this->calculateHppFifo($item->id_produk, $item->kuantitas)') !== false,
    strpos($content, 'calculateHppBarangDagang()') === false,
    strpos($content, 'orderBy(\'created_at\', \'asc\')') !== false,
    strpos($content, 'min($hpp->stok, $remainingQty)') !== false
];

$passed = array_sum($tests);
$total = count($tests);

if ($passed === $total) {
    echo "🎉 SEMUA TEST PASSED ($passed/$total)\n";
    echo "✅ Margin Report sekarang menggunakan FIFO!\n";
} else {
    echo "⚠️  BEBERAPA TEST GAGAL ($passed/$total)\n";
    echo "❌ Perlu perbaikan tambahan\n";
}

echo "\n=== LANGKAH SELANJUTNYA ===\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Test di browser: /admin/penjualan/margin\n";
echo "3. Verifikasi HPP menggunakan FIFO\n";
echo "4. Bandingkan dengan data sebelumnya\n";

echo "\n=== TEST SELESAI ===\n";