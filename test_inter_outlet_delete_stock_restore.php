<?php

/**
 * Test script untuk memverifikasi pengembalian stok saat menghapus transaksi inter-outlet
 * Stok harus dikembalikan dari outlet tujuan ke outlet asal sesuai data HPP yang tersimpan
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
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== TEST INTER-OUTLET DELETE STOCK RESTORE ===\n\n";

try {
    // 1. Cari transaksi inter-outlet yang bisa dihapus untuk testing
    echo "1. MENCARI TRANSAKSI INTER-OUTLET UNTUK TESTING...\n";
    
    $testTransactions = InterOutletSale::with(['items.produk', 'outletAsal', 'outletTujuan'])
        ->where('status', 'approved')
        ->whereHas('items', function($query) {
            $query->whereNotNull('data_hpp')
                  ->where('data_hpp', '!=', '[]')
                  ->where('data_hpp', '!=', '');
        })
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    if ($testTransactions->isEmpty()) {
        echo "   ❌ Tidak ada transaksi inter-outlet dengan data HPP untuk testing\n";
        echo "   💡 Silakan buat transaksi inter-outlet terlebih dahulu\n\n";
        return;
    }
    
    echo "   Transaksi yang tersedia untuk testing:\n";
    foreach ($testTransactions as $i => $transaction) {
        echo "   " . ($i + 1) . ". {$transaction->no_transaksi} - {$transaction->tanggal}\n";
        echo "      {$transaction->outletAsal->nama_outlet} → {$transaction->outletTujuan->nama_outlet}\n";
        echo "      Items: " . $transaction->items->count() . ", Total: Rp " . number_format($transaction->total, 0, ',', '.') . "\n";
        
        // Cek data HPP
        $hasHppData = false;
        foreach ($transaction->items as $item) {
            if (!empty($item->data_hpp) && is_array($item->data_hpp)) {
                $hasHppData = true;
                break;
            }
        }
        echo "      Data HPP: " . ($hasHppData ? "✅ Ada" : "❌ Tidak ada") . "\n";
    }
    
    // 2. Pilih transaksi untuk testing
    $testTransaction = $testTransactions->first();
    echo "\n2. MENGGUNAKAN TRANSAKSI: {$testTransaction->no_transaksi}\n";
    echo "   Outlet Asal: {$testTransaction->outletAsal->nama_outlet} (ID: {$testTransaction->outlet_asal})\n";
    echo "   Outlet Tujuan: {$testTransaction->outletTujuan->nama_outlet} (ID: {$testTransaction->outlet_tujuan})\n";
    echo "   Status: {$testTransaction->status}\n";
    echo "   Items: " . $testTransaction->items->count() . "\n";
    
    // 3. Catat kondisi stok sebelum delete
    echo "\n3. KONDISI STOK SEBELUM DELETE...\n";
    
    $stockBefore = [];
    foreach ($testTransaction->items as $i => $item) {
        echo "\n   ITEM " . ($i + 1) . ": {$item->produk->nama_produk}\n";
        echo "   Kuantitas: {$item->kuantitas}\n";
        echo "   Data HPP: " . json_encode($item->data_hpp) . "\n";
        
        // Stok di outlet asal
        $produkAsal = Produk::find($item->id_produk);
        $hppAsal = HppProduk::where('id_produk', $item->id_produk)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        echo "   Stok di outlet asal ({$testTransaction->outletAsal->nama_outlet}):\n";
        $totalStokAsal = 0;
        foreach ($hppAsal as $j => $hpp) {
            echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                 ", Stok: {$hpp->stok}\n";
            $totalStokAsal += $hpp->stok;
        }
        echo "   Total stok asal: {$totalStokAsal}\n";
        
        // Stok di outlet tujuan
        $produkTujuan = Produk::where('kode_produk', $produkAsal->kode_produk)
            ->where('id_outlet', $testTransaction->outlet_tujuan)
            ->first();
        
        if ($produkTujuan) {
            $hppTujuan = HppProduk::where('id_produk', $produkTujuan->id_produk)
                ->where('stok', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            echo "   Stok di outlet tujuan ({$testTransaction->outletTujuan->nama_outlet}):\n";
            $totalStokTujuan = 0;
            foreach ($hppTujuan as $j => $hpp) {
                echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                     ", Stok: {$hpp->stok}\n";
                $totalStokTujuan += $hpp->stok;
            }
            echo "   Total stok tujuan: {$totalStokTujuan}\n";
        } else {
            echo "   Produk tidak ditemukan di outlet tujuan\n";
            $totalStokTujuan = 0;
        }
        
        // Simpan data untuk perbandingan
        $stockBefore[$item->id] = [
            'asal_total' => $totalStokAsal,
            'tujuan_total' => $totalStokTujuan,
            'kuantitas' => $item->kuantitas,
            'data_hpp' => $item->data_hpp
        ];
    }
    
    // 4. Konfirmasi delete
    echo "\n4. KONFIRMASI DELETE TRANSAKSI...\n";
    echo "   Apakah Anda yakin ingin menghapus transaksi {$testTransaction->no_transaksi}?\n";
    echo "   Ini akan mengembalikan stok dari outlet tujuan ke outlet asal.\n";
    echo "   Ketik 'yes' untuk melanjutkan atau 'no' untuk membatalkan: ";
    
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirmation) !== 'yes') {
        echo "   ❌ Delete dibatalkan oleh user\n";
        return;
    }
    
    // 5. Hapus transaksi menggunakan SalesReportController
    echo "\n5. MENGHAPUS TRANSAKSI...\n";
    
    // Login sebagai user
    $user = User::first();
    auth()->login($user);
    
    // Panggil method delete
    $controller = new \App\Http\Controllers\SalesReportController();
    
    // Gunakan reflection untuk mengakses private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('deleteInterOutlet');
    $method->setAccessible(true);
    
    try {
        $method->invoke($controller, $testTransaction->id);
        echo "   ✅ Transaksi berhasil dihapus\n";
    } catch (\Exception $e) {
        echo "   ❌ Error saat menghapus transaksi: " . $e->getMessage() . "\n";
        return;
    }
    
    // 6. Verifikasi kondisi stok setelah delete
    echo "\n6. KONDISI STOK SETELAH DELETE...\n";
    
    $allSuccess = true;
    
    foreach ($testTransaction->items as $i => $item) {
        echo "\n   ITEM " . ($i + 1) . ": {$item->produk->nama_produk}\n";
        
        $beforeData = $stockBefore[$item->id];
        
        // Stok di outlet asal setelah delete
        $produkAsal = Produk::find($item->id_produk);
        $hppAsal = HppProduk::where('id_produk', $item->id_produk)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        echo "   Stok di outlet asal setelah delete:\n";
        $totalStokAsalAfter = 0;
        foreach ($hppAsal as $j => $hpp) {
            echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                 ", Stok: {$hpp->stok}\n";
            $totalStokAsalAfter += $hpp->stok;
        }
        
        // Stok di outlet tujuan setelah delete
        $produkTujuan = Produk::where('kode_produk', $produkAsal->kode_produk)
            ->where('id_outlet', $testTransaction->outlet_tujuan)
            ->first();
        
        $totalStokTujuanAfter = 0;
        if ($produkTujuan) {
            $hppTujuan = HppProduk::where('id_produk', $produkTujuan->id_produk)
                ->where('stok', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            echo "   Stok di outlet tujuan setelah delete:\n";
            foreach ($hppTujuan as $j => $hpp) {
                echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                     ", Stok: {$hpp->stok}\n";
                $totalStokTujuanAfter += $hpp->stok;
            }
        }
        
        // Verifikasi perubahan stok
        echo "\n   VERIFIKASI PERUBAHAN STOK:\n";
        echo "   Outlet Asal - Sebelum: {$beforeData['asal_total']}, Sesudah: {$totalStokAsalAfter}\n";
        echo "   Outlet Tujuan - Sebelum: {$beforeData['tujuan_total']}, Sesudah: {$totalStokTujuanAfter}\n";
        
        // Expected: stok asal bertambah sesuai kuantitas, stok tujuan berkurang sesuai kuantitas
        $expectedAsalIncrease = $beforeData['kuantitas'];
        $expectedTujuanDecrease = $beforeData['kuantitas'];
        
        $actualAsalIncrease = $totalStokAsalAfter - $beforeData['asal_total'];
        $actualTujuanDecrease = $beforeData['tujuan_total'] - $totalStokTujuanAfter;
        
        echo "   Expected - Asal +{$expectedAsalIncrease}, Tujuan -{$expectedTujuanDecrease}\n";
        echo "   Actual - Asal +{$actualAsalIncrease}, Tujuan -{$actualTujuanDecrease}\n";
        
        if (abs($actualAsalIncrease - $expectedAsalIncrease) < 0.01 && 
            abs($actualTujuanDecrease - $expectedTujuanDecrease) < 0.01) {
            echo "   ✅ Stok berhasil dikembalikan dengan benar\n";
        } else {
            echo "   ❌ Stok tidak dikembalikan dengan benar\n";
            $allSuccess = false;
        }
    }
    
    // 7. Verifikasi transaksi sudah terhapus
    echo "\n7. VERIFIKASI TRANSAKSI TERHAPUS...\n";
    
    $deletedTransaction = InterOutletSale::find($testTransaction->id);
    if (!$deletedTransaction) {
        echo "   ✅ Transaksi berhasil dihapus dari database\n";
    } else {
        echo "   ❌ Transaksi masih ada di database\n";
        $allSuccess = false;
    }
    
    // 8. Ringkasan hasil
    echo "\n=== RINGKASAN HASIL ===\n";
    
    if ($allSuccess) {
        echo "✅ SEMUA TEST BERHASIL!\n";
        echo "✅ Stok berhasil dikembalikan dari outlet tujuan ke outlet asal\n";
        echo "✅ Pengembalian stok menggunakan data HPP yang tersimpan\n";
        echo "✅ Transaksi berhasil dihapus dari database\n";
        echo "✅ Implementasi delete dengan stock restore berfungsi dengan benar\n";
    } else {
        echo "❌ ADA MASALAH DALAM TEST\n";
        echo "💡 Periksa log untuk detail error\n";
        echo "💡 Verifikasi implementasi method deleteInterOutlet\n";
    }
    
    echo "\n=== LANGKAH SELANJUTNYA ===\n";
    echo "1. Test delete dari halaman laporan penjualan\n";
    echo "2. Test delete dari halaman inter-outlet (method destroy)\n";
    echo "3. Verifikasi journal entries juga terhapus\n";
    echo "4. Monitor log untuk memastikan tidak ada error\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI ===\n";