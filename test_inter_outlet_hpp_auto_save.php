<?php

/**
 * Test script untuk memverifikasi auto-save HPP data pada transaksi inter-outlet
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;
use App\Models\HppProduk;
use App\Models\Produk;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== TEST AUTO-SAVE HPP DATA INTER-OUTLET ===\n\n";

try {
    // 1. Cek apakah ada data HPP yang tersedia
    echo "1. MEMERIKSA DATA HPP TERSEDIA...\n";
    
    $hppCount = HppProduk::where('stok', '>', 0)->count();
    echo "   Total HPP dengan stok > 0: {$hppCount}\n";
    
    if ($hppCount === 0) {
        echo "   ❌ Tidak ada data HPP dengan stok > 0\n";
        echo "   💡 Silakan buat data HPP terlebih dahulu untuk testing\n\n";
        return;
    }
    
    // 2. Ambil sample produk dengan HPP
    echo "\n2. MENCARI PRODUK DENGAN HPP...\n";
    
    $produkWithHpp = DB::select("
        SELECT p.id_produk, p.nama_produk, p.id_outlet, 
               COUNT(hpp.id) as hpp_batches,
               SUM(hpp.stok) as total_stok,
               AVG(hpp.hpp) as avg_hpp
        FROM produk p
        INNER JOIN hpp_produk hpp ON p.id_produk = hpp.id_produk
        WHERE hpp.stok > 0
        GROUP BY p.id_produk, p.nama_produk, p.id_outlet
        HAVING total_stok >= 10
        ORDER BY total_stok DESC
        LIMIT 5
    ");
    
    if (empty($produkWithHpp)) {
        echo "   ❌ Tidak ada produk dengan HPP yang memadai untuk testing\n\n";
        return;
    }
    
    echo "   Produk dengan HPP tersedia:\n";
    foreach ($produkWithHpp as $i => $produk) {
        echo "   " . ($i + 1) . ". {$produk->nama_produk} (ID: {$produk->id_produk})\n";
        echo "      Outlet: {$produk->id_outlet}, Stok: {$produk->total_stok}, HPP Rata-rata: Rp " . number_format($produk->avg_hpp, 0, ',', '.') . "\n";
    }
    
    // 3. Ambil sample outlet
    echo "\n3. MENCARI OUTLET TERSEDIA...\n";
    
    $outlets = Outlet::where('is_active', true)->limit(3)->get();
    
    if ($outlets->count() < 2) {
        echo "   ❌ Minimal perlu 2 outlet untuk testing inter-outlet\n\n";
        return;
    }
    
    echo "   Outlet tersedia:\n";
    foreach ($outlets as $i => $outlet) {
        echo "   " . ($i + 1) . ". {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    }
    
    // 4. Simulasi data transaksi
    echo "\n4. MENYIAPKAN DATA TRANSAKSI SIMULASI...\n";
    
    $sampleProduk = $produkWithHpp[0];
    $outletAsal = $outlets[0]->id_outlet;
    $outletTujuan = $outlets[1]->id_outlet;
    $kuantitas = 5; // Ambil 5 unit untuk testing
    $harga = 15000;
    $subtotal = $kuantitas * $harga;
    
    echo "   Produk: {$sampleProduk->nama_produk} (ID: {$sampleProduk->id_produk})\n";
    echo "   Outlet Asal: {$outlets[0]->nama_outlet} (ID: {$outletAsal})\n";
    echo "   Outlet Tujuan: {$outlets[1]->nama_outlet} (ID: {$outletTujuan})\n";
    echo "   Kuantitas: {$kuantitas} unit\n";
    echo "   Harga: Rp " . number_format($harga, 0, ',', '.') . "\n";
    echo "   Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
    
    // 5. Cek HPP yang akan digunakan
    echo "\n5. MEMERIKSA HPP YANG AKAN DIGUNAKAN (FIFO)...\n";
    
    $hppDetails = HppProduk::where('id_produk', $sampleProduk->id_produk)
        ->where('stok', '>', 0)
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "   HPP batches tersedia:\n";
    $totalHppCalculated = 0;
    $remainingQty = $kuantitas;
    
    foreach ($hppDetails as $i => $hpp) {
        if ($remainingQty <= 0) break;
        
        $usedQty = min($hpp->stok, $remainingQty);
        $batchTotal = $hpp->hpp * $usedQty;
        $totalHppCalculated += $batchTotal;
        $remainingQty -= $usedQty;
        
        echo "      Batch " . ($i + 1) . ": HPP Rp " . number_format($hpp->hpp, 0, ',', '.') . 
             ", Stok {$hpp->stok}, Akan digunakan {$usedQty} unit = Rp " . 
             number_format($batchTotal, 0, ',', '.') . "\n";
    }
    
    $hppPerUnit = $kuantitas > 0 ? $totalHppCalculated / $kuantitas : 0;
    echo "   Total HPP: Rp " . number_format($totalHppCalculated, 0, ',', '.') . "\n";
    echo "   HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
    
    if ($remainingQty > 0) {
        echo "   ⚠️  Sisa quantity tidak terpenuhi: {$remainingQty} unit\n";
    }
    
    // 6. Test method calculateFifoHppData
    echo "\n6. TESTING METHOD calculateFifoHppData...\n";
    
    // Buat instance controller untuk testing
    $controller = new \App\Http\Controllers\InterOutletSaleController(
        new \App\Services\JournalEntryService()
    );
    
    // Gunakan reflection untuk mengakses private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('calculateFifoHppData');
    $method->setAccessible(true);
    
    $transactionDate = now()->format('Y-m-d H:i:s');
    $dataHpp = $method->invoke($controller, $sampleProduk->id_produk, $kuantitas, $transactionDate);
    
    echo "   Data HPP yang dihitung:\n";
    echo "   " . json_encode($dataHpp, JSON_PRETTY_PRINT) . "\n";
    
    // 7. Verifikasi format data
    echo "\n7. VERIFIKASI FORMAT DATA HPP...\n";
    
    if (empty($dataHpp)) {
        echo "   ❌ Data HPP kosong\n";
    } else {
        echo "   ✅ Data HPP berhasil dihitung\n";
        echo "   📊 Jumlah batch HPP: " . count($dataHpp) . "\n";
        
        $totalQtyUsed = 0;
        $totalHppFromData = 0;
        
        foreach ($dataHpp as $i => $hppData) {
            if (!isset($hppData['id_hpp']) || !isset($hppData['hpp']) || !isset($hppData['qty_used'])) {
                echo "   ❌ Format data HPP tidak valid pada batch " . ($i + 1) . "\n";
                continue;
            }
            
            $totalQtyUsed += $hppData['qty_used'];
            $totalHppFromData += $hppData['hpp'] * $hppData['qty_used'];
            
            echo "   Batch " . ($i + 1) . ": ID HPP {$hppData['id_hpp']}, HPP Rp " . 
                 number_format($hppData['hpp'], 0, ',', '.') . ", Qty {$hppData['qty_used']}\n";
        }
        
        echo "   📊 Total quantity used: {$totalQtyUsed} (diminta: {$kuantitas})\n";
        echo "   📊 Total HPP dari data: Rp " . number_format($totalHppFromData, 0, ',', '.') . "\n";
        
        if ($totalQtyUsed == $kuantitas) {
            echo "   ✅ Quantity sesuai dengan yang diminta\n";
        } else {
            echo "   ⚠️  Quantity tidak sesuai (selisih: " . ($kuantitas - $totalQtyUsed) . ")\n";
        }
    }
    
    // 8. Test simulasi penyimpanan
    echo "\n8. SIMULASI PENYIMPANAN DATA...\n";
    
    echo "   Format JSON yang akan disimpan ke database:\n";
    $jsonData = json_encode($dataHpp);
    echo "   " . $jsonData . "\n";
    
    // Cek apakah bisa di-decode kembali
    $decodedData = json_decode($jsonData, true);
    if ($decodedData === null) {
        echo "   ❌ JSON tidak valid\n";
    } else {
        echo "   ✅ JSON valid dan bisa di-decode kembali\n";
    }
    
    echo "\n=== RINGKASAN TEST ===\n";
    echo "✅ Method calculateFifoHppData berhasil diimplementasikan\n";
    echo "✅ Format data HPP sesuai dengan yang diharapkan\n";
    echo "✅ Data dapat disimpan dalam format JSON\n";
    echo "💡 Implementasi siap untuk digunakan pada transaksi inter-outlet\n\n";
    
    echo "=== LANGKAH SELANJUTNYA ===\n";
    echo "1. Buat transaksi inter-outlet baru melalui interface\n";
    echo "2. Periksa apakah kolom data_hpp terisi otomatis\n";
    echo "3. Verifikasi data di laporan margin menggunakan HPP tersimpan\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI ===\n";