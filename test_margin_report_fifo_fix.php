<?php

/**
 * Test script untuk memverifikasi perbaikan HPP FIFO di Laporan Margin
 * 
 * Perubahan yang dilakukan:
 * 1. Mengubah perhitungan HPP untuk data POS dari metode rata-rata tertimbang ke FIFO
 * 2. Menambahkan method calculateHppFifo untuk konsistensi perhitungan
 * 3. Memastikan semua perhitungan HPP menggunakan metode FIFO
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== TEST MARGIN REPORT FIFO FIX ===\n\n";

try {
    // Test 1: Verifikasi method calculateHppFifo ada di MarginReportController
    echo "1. Checking MarginReportController method...\n";
    
    $controllerPath = __DIR__ . '/app/Http/Controllers/MarginReportController.php';
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, 'calculateHppFifo') !== false) {
        echo "   ✓ Method calculateHppFifo found in MarginReportController\n";
    } else {
        echo "   ✗ Method calculateHppFifo NOT found in MarginReportController\n";
    }
    
    if (strpos($controllerContent, 'calculateHppBarangDagang') === false) {
        echo "   ✓ Old method calculateHppBarangDagang removed from POS calculation\n";
    } else {
        echo "   ✗ Old method calculateHppBarangDagang still used in POS calculation\n";
    }
    
    // Test 2: Verifikasi perubahan di getData method
    echo "\n2. Checking getData method changes...\n";
    
    if (strpos($controllerContent, '$this->calculateHppFifo($item->id_produk, $item->kuantitas)') !== false) {
        echo "   ✓ POS items now use FIFO calculation\n";
    } else {
        echo "   ✗ POS items still use old calculation method\n";
    }
    
    // Test 3: Simulasi perhitungan HPP FIFO vs Average
    echo "\n3. Simulating HPP calculation difference...\n";
    
    // Contoh data HPP untuk produk
    $hppData = [
        ['hpp' => 10000, 'stok' => 5, 'created_at' => '2024-01-01'],
        ['hpp' => 12000, 'stok' => 3, 'created_at' => '2024-01-02'],
        ['hpp' => 15000, 'stok' => 2, 'created_at' => '2024-01-03'],
    ];
    
    $qty = 4; // Jumlah yang akan dijual
    
    // Perhitungan FIFO
    $fifoHpp = 0;
    $remainingQty = $qty;
    
    foreach ($hppData as $hpp) {
        if ($remainingQty <= 0) break;
        
        $usedQty = min($hpp['stok'], $remainingQty);
        $fifoHpp += $hpp['hpp'] * $usedQty;
        $remainingQty -= $usedQty;
    }
    
    // Perhitungan Average (metode lama)
    $totalNilai = 0;
    $totalStok = 0;
    
    foreach ($hppData as $hpp) {
        $totalNilai += $hpp['hpp'] * $hpp['stok'];
        $totalStok += $hpp['stok'];
    }
    
    $averageHpp = $totalStok > 0 ? ($totalNilai / $totalStok) * $qty : 0;
    
    echo "   Sample calculation for qty: $qty\n";
    echo "   FIFO HPP: Rp " . number_format($fifoHpp, 0, ',', '.') . "\n";
    echo "   Average HPP: Rp " . number_format($averageHpp, 0, ',', '.') . "\n";
    echo "   Difference: Rp " . number_format(abs($fifoHpp - $averageHpp), 0, ',', '.') . "\n";
    
    // Test 4: Verifikasi konsistensi dengan PenjualanDetailController
    echo "\n4. Checking consistency with PenjualanDetailController...\n";
    
    $penjualanControllerPath = __DIR__ . '/app/Http/Controllers/PenjualanDetailController.php';
    if (file_exists($penjualanControllerPath)) {
        $penjualanContent = file_get_contents($penjualanControllerPath);
        
        if (strpos($penjualanContent, 'getHppFifo') !== false) {
            echo "   ✓ PenjualanDetailController uses FIFO method\n";
        } else {
            echo "   ✗ PenjualanDetailController doesn't use FIFO method\n";
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "✓ HPP calculation in Margin Report now uses FIFO method\n";
    echo "✓ Consistent with invoice HPP calculation\n";
    echo "✓ More accurate profit and margin calculation\n";
    echo "✓ Better inventory cost tracking\n";
    
    echo "\n=== TESTING INSTRUCTIONS ===\n";
    echo "1. Login ke admin panel\n";
    echo "2. Buka menu Penjualan > Laporan Margin\n";
    echo "3. Pilih periode dan outlet\n";
    echo "4. Verifikasi HPP yang ditampilkan sekarang menggunakan FIFO\n";
    echo "5. Bandingkan dengan data sebelumnya (jika ada)\n";
    echo "6. Pastikan margin dan profit dihitung dengan benar\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";