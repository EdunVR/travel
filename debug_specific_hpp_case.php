<?php

/**
 * Debug script untuk kasus spesifik HPP inter-outlet
 * Tanggal: 23 Jan 2026, Produk: Tofu Spesial Udang 120g, Qty: 8000, HPP: Rp 1.333
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;
use App\Models\Produk;
use App\Models\HppProduk;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG SPECIFIC HPP CASE ===\n\n";

try {
    // Step 1: Cari data inter-outlet sale pada tanggal 23 Jan 2026
    echo "1. MENCARI DATA INTER-OUTLET SALE TANGGAL 23 JAN 2026...\n";
    
    $interOutletSales = DB::table('inter_outlet_sales as ios')
        ->join('inter_outlet_sale_items as iosi', 'ios.id', '=', 'iosi.inter_outlet_sale_id')
        ->join('produk as p', 'iosi.id_produk', '=', 'p.id_produk')
        ->join('outlets as oa', 'ios.outlet_asal', '=', 'oa.id_outlet')
        ->join('outlets as ot', 'ios.outlet_tujuan', '=', 'ot.id_outlet')
        ->select([
            'ios.id', 'ios.no_transaksi', 'ios.tanggal', 'ios.outlet_asal', 'ios.outlet_tujuan', 'ios.status',
            'iosi.id as item_id', 'iosi.id_produk', 'iosi.kuantitas', 'iosi.harga', 'iosi.subtotal',
            'p.nama_produk', 'oa.nama_outlet as outlet_asal_nama', 'ot.nama_outlet as outlet_tujuan_nama'
        ])
        ->whereDate('ios.tanggal', '2026-01-23')
        ->where('p.nama_produk', 'LIKE', '%Tofu Spesial Udang 120g%')
        ->where('iosi.kuantitas', 8000)
        ->orderBy('ios.created_at', 'desc')
        ->get();
    
    if ($interOutletSales->isEmpty()) {
        echo "❌ Tidak ditemukan data inter-outlet sale dengan kriteria tersebut\n";
        echo "   Mencoba pencarian yang lebih luas...\n\n";
        
        // Cari semua inter-outlet sale pada tanggal 23 Jan 2026
        $allSales = DB::table('inter_outlet_sales')
            ->whereDate('tanggal', '2026-01-23')
            ->get();
        
        echo "Data inter-outlet sale pada 23 Jan 2026: " . $allSales->count() . " transaksi\n";
        foreach ($allSales as $sale) {
            echo "   - ID: {$sale->id}, No: {$sale->no_transaksi}, Status: {$sale->status}\n";
        }
        
        // Cari produk dengan nama mirip
        $products = DB::table('produk')
            ->where('nama_produk', 'LIKE', '%Tofu%Udang%')
            ->orWhere('nama_produk', 'LIKE', '%Tofu Spesial%')
            ->get();
        
        echo "\nProduk dengan nama mirip:\n";
        foreach ($products as $product) {
            echo "   - ID: {$product->id_produk}, Nama: {$product->nama_produk}\n";
        }
        
    } else {
        echo "✅ Ditemukan " . $interOutletSales->count() . " data inter-outlet sale\n\n";
        
        foreach ($interOutletSales as $sale) {
            echo "Data Inter-Outlet Sale:\n";
            echo "   ID: {$sale->id}\n";
            echo "   No Transaksi: {$sale->no_transaksi}\n";
            echo "   Tanggal: {$sale->tanggal}\n";
            echo "   Outlet: {$sale->outlet_asal_nama} → {$sale->outlet_tujuan_nama}\n";
            echo "   Status: {$sale->status}\n";
            echo "   Item ID: {$sale->item_id}\n";
            echo "   Produk ID: {$sale->id_produk}\n";
            echo "   Produk: {$sale->nama_produk}\n";
            echo "   Qty: {$sale->kuantitas}\n";
            echo "   Harga: Rp " . number_format($sale->harga, 0, ',', '.') . "\n";
            echo "   Subtotal: Rp " . number_format($sale->subtotal, 0, ',', '.') . "\n\n";
            
            // Step 2: Cari data HPP untuk produk ini
            echo "2. MENCARI DATA HPP PRODUK ID: {$sale->id_produk}...\n";
            
            $hppData = DB::table('hpp_produk')
                ->where('id_produk', $sale->id_produk)
                ->where('stok', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($hppData->isEmpty()) {
                echo "❌ Tidak ditemukan data HPP untuk produk ID: {$sale->id_produk}\n\n";
            } else {
                echo "✅ Ditemukan " . $hppData->count() . " batch HPP\n\n";
                
                echo "Data HPP (urutan FIFO):\n";
                foreach ($hppData as $i => $hpp) {
                    echo "   Batch " . ($i + 1) . ":\n";
                    echo "     ID HPP: {$hpp->id}\n";
                    echo "     HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . "\n";
                    echo "     Stok: {$hpp->stok}\n";
                    echo "     Created: {$hpp->created_at}\n\n";
                }
                
                // Step 3: Hitung FIFO manual
                echo "3. PERHITUNGAN FIFO MANUAL UNTUK QTY {$sale->kuantitas}...\n";
                
                $totalHppFifo = 0;
                $remainingQty = $sale->kuantitas;
                $usedBatches = [];
                
                foreach ($hppData as $i => $hpp) {
                    if ($remainingQty <= 0) break;
                    
                    $usedQty = min($hpp->stok, $remainingQty);
                    $batchTotal = $hpp->hpp * $usedQty;
                    $totalHppFifo += $batchTotal;
                    $remainingQty -= $usedQty;
                    
                    $usedBatches[] = [
                        'batch' => $i + 1,
                        'id_hpp' => $hpp->id,
                        'hpp' => $hpp->hpp,
                        'stok_tersedia' => $hpp->stok,
                        'used_qty' => $usedQty,
                        'batch_total' => $batchTotal
                    ];
                    
                    echo "   Batch " . ($i + 1) . " (ID: {$hpp->id}):\n";
                    echo "     Ambil: {$usedQty} unit dari {$hpp->stok} unit tersedia\n";
                    echo "     HPP: Rp " . number_format($hpp->hpp, 0, ',', '.') . " per unit\n";
                    echo "     Total batch: Rp " . number_format($batchTotal, 0, ',', '.') . "\n";
                    echo "     Sisa qty: {$remainingQty}\n\n";
                }
                
                $hppPerUnit = $sale->kuantitas > 0 ? $totalHppFifo / $sale->kuantitas : 0;
                
                echo "HASIL PERHITUNGAN FIFO:\n";
                echo "   Total HPP FIFO: Rp " . number_format($totalHppFifo, 0, ',', '.') . "\n";
                echo "   HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
                echo "   Expected HPP: Rp 1.333\n";
                echo "   Selisih: Rp " . number_format(abs($hppPerUnit - 1333), 0, ',', '.') . "\n\n";
                
                // Step 4: Cek apakah ada masalah dengan perhitungan
                if (abs($hppPerUnit - 1333) > 1) {
                    echo "⚠️  DITEMUKAN PERBEDAAN SIGNIFIKAN!\n";
                    echo "   HPP yang dihitung: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
                    echo "   HPP yang diharapkan: Rp 1.333\n";
                    echo "   Selisih: Rp " . number_format(abs($hppPerUnit - 1333), 0, ',', '.') . "\n\n";
                    
                    echo "KEMUNGKINAN PENYEBAB:\n";
                    echo "1. Data HPP di database berbeda dengan yang digunakan di laporan\n";
                    echo "2. Ada transaksi lain yang mengubah stok HPP setelah inter-outlet sale\n";
                    echo "3. Method calculateHppFifo di controller ada bug\n";
                    echo "4. Data yang ditampilkan di laporan menggunakan cache lama\n\n";
                } else {
                    echo "✅ HPP calculation sesuai dengan expected value\n\n";
                }
                
                // Step 5: Test method calculateHppFifo dari controller
                echo "4. TEST METHOD calculateHppFifo DARI CONTROLLER...\n";
                
                // Simulate the controller method
                $controllerHpp = simulateCalculateHppFifo($sale->id_produk, $sale->kuantitas);
                $controllerHppPerUnit = $sale->kuantitas > 0 ? $controllerHpp / $sale->kuantitas : 0;
                
                echo "   Controller HPP total: Rp " . number_format($controllerHpp, 0, ',', '.') . "\n";
                echo "   Controller HPP per unit: Rp " . number_format($controllerHppPerUnit, 0, ',', '.') . "\n";
                echo "   Manual calculation: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
                echo "   Difference: Rp " . number_format(abs($controllerHppPerUnit - $hppPerUnit), 0, ',', '.') . "\n\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

/**
 * Simulate the calculateHppFifo method from MarginReportController
 */
function simulateCalculateHppFifo($id_produk, $jumlah)
{
    $hppDetails = DB::table('hpp_produk')
        ->where('id_produk', $id_produk)
        ->where('stok', '>', 0)
        ->orderBy('created_at', 'asc')
        ->get();

    if ($hppDetails->isEmpty()) {
        return 0;
    }

    $totalHpp = 0;
    $remainingQty = $jumlah;

    foreach ($hppDetails as $hpp) {
        if ($remainingQty <= 0) {
            break;
        }

        $usedQty = min($hpp->stok, $remainingQty);
        $totalHpp += $hpp->hpp * $usedQty;
        $remainingQty -= $usedQty;
    }

    return $totalHpp;
}

echo "=== DEBUG SELESAI ===\n";