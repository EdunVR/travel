<?php

/**
 * Test script untuk memverifikasi perbaikan HPP di outlet tujuan
 * HPP di outlet tujuan seharusnya = harga jual dari outlet asal
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

echo "=== TEST INTER-OUTLET HPP DESTINATION FIX ===\n\n";

try {
    // 1. Cek transaksi inter-outlet terbaru
    echo "1. MEMERIKSA TRANSAKSI INTER-OUTLET TERBARU...\n";
    
    $recentTransactions = InterOutletSale::with(['items.produk', 'outletAsal', 'outletTujuan'])
        ->where('status', 'approved')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    if ($recentTransactions->isEmpty()) {
        echo "   ❌ Tidak ada transaksi inter-outlet yang approved\n";
        echo "   💡 Silakan buat transaksi inter-outlet terlebih dahulu untuk testing\n\n";
        return;
    }
    
    echo "   Transaksi inter-outlet terbaru:\n";
    foreach ($recentTransactions as $i => $transaction) {
        echo "   " . ($i + 1) . ". {$transaction->no_transaksi} - {$transaction->tanggal}\n";
        echo "      {$transaction->outletAsal->nama_outlet} → {$transaction->outletTujuan->nama_outlet}\n";
        echo "      Items: " . $transaction->items->count() . ", Total: Rp " . number_format($transaction->total, 0, ',', '.') . "\n";
    }
    
    // 2. Pilih transaksi untuk analisis
    echo "\n2. MENGANALISIS TRANSAKSI PERTAMA...\n";
    
    $transaction = $recentTransactions->first();
    echo "   Transaksi: {$transaction->no_transaksi}\n";
    echo "   Tanggal: {$transaction->tanggal}\n";
    echo "   Outlet Asal: {$transaction->outletAsal->nama_outlet} (ID: {$transaction->outlet_asal})\n";
    echo "   Outlet Tujuan: {$transaction->outletTujuan->nama_outlet} (ID: {$transaction->outlet_tujuan})\n";
    
    // 3. Analisis setiap item
    echo "\n3. ANALISIS SETIAP ITEM TRANSAKSI...\n";
    
    foreach ($transaction->items as $i => $item) {
        echo "\n   ITEM " . ($i + 1) . ":\n";
        echo "   Produk: {$item->produk->nama_produk} (ID: {$item->id_produk})\n";
        echo "   Kuantitas: {$item->kuantitas}\n";
        echo "   Harga Jual: Rp " . number_format($item->harga, 0, ',', '.') . "\n";
        echo "   Subtotal: Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
        
        // 4. Cek produk di outlet asal
        echo "\n   PRODUK DI OUTLET ASAL ({$transaction->outletAsal->nama_outlet}):\n";
        
        $produkAsal = Produk::where('id_produk', $item->id_produk)
            ->where('id_outlet', $transaction->outlet_asal)
            ->first();
        
        if ($produkAsal) {
            echo "   ✅ Produk ditemukan di outlet asal\n";
            echo "   Kode: {$produkAsal->kode_produk}\n";
            echo "   Nama: {$produkAsal->nama_produk}\n";
            
            // Cek HPP di outlet asal
            $hppAsal = HppProduk::where('id_produk', $produkAsal->id_produk)
                ->where('stok', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            if ($hppAsal->isNotEmpty()) {
                echo "   HPP di outlet asal (3 terbaru):\n";
                foreach ($hppAsal as $j => $hpp) {
                    echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                         ", Stok: {$hpp->stok}, Tanggal: {$hpp->created_at}\n";
                }
            } else {
                echo "   ⚠️  Tidak ada HPP dengan stok > 0 di outlet asal\n";
            }
        } else {
            echo "   ❌ Produk tidak ditemukan di outlet asal\n";
        }
        
        // 5. Cek produk di outlet tujuan
        echo "\n   PRODUK DI OUTLET TUJUAN ({$transaction->outletTujuan->nama_outlet}):\n";
        
        $produkTujuan = Produk::where('kode_produk', $produkAsal->kode_produk ?? '')
            ->where('id_outlet', $transaction->outlet_tujuan)
            ->first();
        
        if ($produkTujuan) {
            echo "   ✅ Produk ditemukan di outlet tujuan\n";
            echo "   ID Produk: {$produkTujuan->id_produk}\n";
            echo "   Kode: {$produkTujuan->kode_produk}\n";
            echo "   Nama: {$produkTujuan->nama_produk}\n";
            
            // Cek HPP di outlet tujuan setelah transaksi
            $hppTujuan = HppProduk::where('id_produk', $produkTujuan->id_produk)
                ->where('created_at', '>=', $transaction->created_at)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            if ($hppTujuan->isNotEmpty()) {
                echo "   HPP di outlet tujuan (setelah transaksi):\n";
                foreach ($hppTujuan as $j => $hpp) {
                    echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                         ", Stok: {$hpp->stok}, Tanggal: {$hpp->created_at}\n";
                    
                    // Verifikasi apakah HPP = harga jual
                    if (abs($hpp->hpp - $item->harga) < 0.01) {
                        echo "         ✅ HPP sesuai dengan harga jual (Rp " . number_format($item->harga, 0, ',', '.') . ")\n";
                    } else {
                        echo "         ❌ HPP tidak sesuai dengan harga jual!\n";
                        echo "         Expected: Rp " . number_format($item->harga, 0, ',', '.') . 
                             ", Actual: Rp " . number_format($hpp->hpp, 0, ',', '.') . "\n";
                    }
                }
            } else {
                echo "   ❌ Tidak ada HPP baru di outlet tujuan setelah transaksi\n";
                echo "   💡 Ini menunjukkan masalah dalam penambahan stok\n";
            }
            
            // Cek semua HPP di outlet tujuan
            $allHppTujuan = HppProduk::where('id_produk', $produkTujuan->id_produk)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            echo "   Semua HPP di outlet tujuan (5 terbaru):\n";
            if ($allHppTujuan->isNotEmpty()) {
                foreach ($allHppTujuan as $j => $hpp) {
                    echo "      " . ($j + 1) . ". HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                         ", Stok: {$hpp->stok}, Tanggal: {$hpp->created_at}\n";
                }
            } else {
                echo "      ❌ Tidak ada HPP sama sekali di outlet tujuan\n";
            }
            
        } else {
            echo "   ❌ Produk tidak ditemukan di outlet tujuan\n";
            echo "   💡 Produk seharusnya dibuat otomatis saat transaksi inter-outlet\n";
        }
    }
    
    // 6. Ringkasan dan rekomendasi
    echo "\n=== RINGKASAN ANALISIS ===\n";
    
    $hasIssues = false;
    $recommendations = [];
    
    foreach ($transaction->items as $item) {
        $produkAsal = Produk::where('id_produk', $item->id_produk)
            ->where('id_outlet', $transaction->outlet_asal)
            ->first();
        
        if (!$produkAsal) {
            $hasIssues = true;
            $recommendations[] = "Produk ID {$item->id_produk} tidak ditemukan di outlet asal";
            continue;
        }
        
        $produkTujuan = Produk::where('kode_produk', $produkAsal->kode_produk)
            ->where('id_outlet', $transaction->outlet_tujuan)
            ->first();
        
        if (!$produkTujuan) {
            $hasIssues = true;
            $recommendations[] = "Produk {$produkAsal->kode_produk} tidak dibuat di outlet tujuan";
            continue;
        }
        
        $hppTujuan = HppProduk::where('id_produk', $produkTujuan->id_produk)
            ->where('created_at', '>=', $transaction->created_at)
            ->where('hpp', $item->harga)
            ->first();
        
        if (!$hppTujuan) {
            $hasIssues = true;
            $recommendations[] = "HPP untuk produk {$produkAsal->nama_produk} di outlet tujuan tidak sesuai dengan harga jual";
        }
    }
    
    if (!$hasIssues) {
        echo "✅ Semua item transaksi sudah benar!\n";
        echo "✅ HPP di outlet tujuan sesuai dengan harga jual dari outlet asal\n";
        echo "✅ Implementasi perbaikan berhasil\n";
    } else {
        echo "❌ Ditemukan masalah dalam transaksi:\n";
        foreach ($recommendations as $i => $rec) {
            echo "   " . ($i + 1) . ". {$rec}\n";
        }
        echo "\n💡 Perbaikan diperlukan untuk transaksi yang sudah ada\n";
        echo "💡 Transaksi baru seharusnya sudah menggunakan logika yang benar\n";
    }
    
    echo "\n=== LANGKAH SELANJUTNYA ===\n";
    echo "1. Buat transaksi inter-outlet baru untuk menguji perbaikan\n";
    echo "2. Verifikasi HPP di outlet tujuan = harga jual dari outlet asal\n";
    echo "3. Periksa laporan margin untuk memastikan perhitungan benar\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI ===\n";