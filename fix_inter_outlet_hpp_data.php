<?php

/**
 * Script untuk mengisi data HPP yang kosong pada inter_outlet_sale_items
 * 
 * Format JSON untuk data_hpp:
 * [
 *   {
 *     "id_hpp": 123,
 *     "hpp": 2500.00,
 *     "qty_used": 1000
 *   },
 *   {
 *     "id_hpp": 124,
 *     "hpp": 2600.00,
 *     "qty_used": 500
 *   }
 * ]
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSaleItem;
use App\Models\HppProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== FIXING INTER-OUTLET HPP DATA ===\n\n";

try {
    // Jalankan migration terlebih dahulu
    echo "1. MENJALANKAN MIGRATION...\n";
    $migrationResult = shell_exec('php artisan migrate --force 2>&1');
    echo $migrationResult . "\n";
    
    // Ambil semua inter-outlet sale items yang belum memiliki data_hpp
    echo "2. MENCARI INTER-OUTLET ITEMS TANPA DATA HPP...\n";
    
    $itemsWithoutHpp = InterOutletSaleItem::whereNull('data_hpp')
        ->orWhere('data_hpp', '[]')
        ->orWhere('data_hpp', '""')
        ->with(['interOutletSale', 'produk'])
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "Ditemukan " . $itemsWithoutHpp->count() . " items tanpa data HPP\n\n";
    
    if ($itemsWithoutHpp->isEmpty()) {
        echo "✅ Semua inter-outlet items sudah memiliki data HPP\n";
        return;
    }
    
    $successCount = 0;
    $errorCount = 0;
    $insufficientCount = 0;
    
    foreach ($itemsWithoutHpp as $item) {
        echo "3. MEMPROSES ITEM ID: {$item->id}...\n";
        echo "   Produk: " . ($item->produk->nama_produk ? $item->produk->nama_produk : 'Unknown') . " (ID: {$item->id_produk})\n";
        echo "   Quantity: {$item->kuantitas}\n";
        echo "   Tanggal Transaksi: {$item->interOutletSale->tanggal}\n";
        
        try {
            // Ambil data HPP yang tersedia pada saat transaksi (berdasarkan tanggal)
            $transactionDate = $item->interOutletSale->tanggal;
            
            $hppData = HppProduk::where('id_produk', $item->id_produk)
                ->where('stok', '>', 0)
                ->where('created_at', '<=', $transactionDate)
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($hppData->isEmpty()) {
                echo "   ❌ Tidak ada data HPP tersedia pada tanggal transaksi\n";
                echo "   📝 Mencoba menggunakan HPP terdekat setelah tanggal transaksi...\n";
                
                // Fallback: ambil HPP terdekat setelah tanggal transaksi
                $hppData = HppProduk::where('id_produk', $item->id_produk)
                    ->where('stok', '>', 0)
                    ->where('created_at', '>', $transactionDate)
                    ->orderBy('created_at', 'asc')
                    ->limit(5)
                    ->get();
                
                if ($hppData->isEmpty()) {
                    echo "   ❌ Tidak ada data HPP sama sekali untuk produk ini\n";
                    $errorCount++;
                    continue;
                }
            }
            
            // Hitung FIFO berdasarkan data HPP yang tersedia
            $dataHppJson = [];
            $remainingQty = floatval($item->kuantitas);
            $totalHppUsed = 0;
            
            echo "   📊 Data HPP tersedia:\n";
            foreach ($hppData as $i => $hpp) {
                echo "      Batch " . ($i + 1) . ": HPP Rp " . number_format($hpp->hpp, 0, ',', '.') . 
                     ", Stok {$hpp->stok}, Tanggal {$hpp->created_at}\n";
            }
            
            echo "   🧮 Perhitungan FIFO:\n";
            foreach ($hppData as $i => $hpp) {
                if ($remainingQty <= 0) break;
                
                $usedQty = min($hpp->stok, $remainingQty);
                $batchTotal = $hpp->hpp * $usedQty;
                $totalHppUsed += $batchTotal;
                $remainingQty -= $usedQty;
                
                $dataHppJson[] = [
                    'id_hpp' => $hpp->id,
                    'hpp' => floatval($hpp->hpp),
                    'qty_used' => $usedQty
                ];
                
                echo "      Batch " . ($i + 1) . ": Ambil {$usedQty} unit @ Rp " . 
                     number_format($hpp->hpp, 0, ',', '.') . " = Rp " . 
                     number_format($batchTotal, 0, ',', '.') . "\n";
            }
            
            if ($remainingQty > 0) {
                echo "   ⚠️  Sisa quantity tidak terpenuhi: {$remainingQty} unit\n";
                echo "   📝 Data HPP akan tetap disimpan untuk quantity yang terpenuhi\n";
                $insufficientCount++;
            }
            
            // Simpan data HPP ke database
            $item->data_hpp = $dataHppJson;
            $item->save();
            
            $hppPerUnit = $item->kuantitas > 0 ? $totalHppUsed / $item->kuantitas : 0;
            
            echo "   ✅ Data HPP berhasil disimpan\n";
            echo "   📊 Total HPP: Rp " . number_format($totalHppUsed, 0, ',', '.') . "\n";
            echo "   📊 HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
            echo "   📊 Data JSON: " . json_encode($dataHppJson, JSON_PRETTY_PRINT) . "\n\n";
            
            $successCount++;
            
        } catch (\Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n\n";
            $errorCount++;
            
            Log::error("Error fixing HPP data for inter-outlet item", [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    echo "=== RINGKASAN HASIL ===\n";
    echo "✅ Berhasil diproses: {$successCount} items\n";
    echo "⚠️  Stok tidak mencukupi: {$insufficientCount} items\n";
    echo "❌ Error: {$errorCount} items\n";
    echo "📊 Total items: " . $itemsWithoutHpp->count() . "\n\n";
    
    if ($successCount > 0) {
        echo "🎉 Data HPP berhasil diperbaiki!\n";
        echo "💡 Sekarang laporan margin akan menggunakan data HPP yang tersimpan saat transaksi\n\n";
    }
    
    // Contoh format JSON untuk manual input
    echo "=== FORMAT JSON UNTUK INPUT MANUAL ===\n";
    echo "Jika ada data yang perlu diisi manual, gunakan format berikut:\n\n";
    echo "```json\n";
    echo "[\n";
    echo "  {\n";
    echo "    \"id_hpp\": 123,\n";
    echo "    \"hpp\": 2500.00,\n";
    echo "    \"qty_used\": 1000\n";
    echo "  },\n";
    echo "  {\n";
    echo "    \"id_hpp\": 124,\n";
    echo "    \"hpp\": 2600.00,\n";
    echo "    \"qty_used\": 500\n";
    echo "  }\n";
    echo "]\n";
    echo "```\n\n";
    
    echo "Keterangan:\n";
    echo "- id_hpp: ID dari tabel hpp_produk\n";
    echo "- hpp: Nilai HPP per unit\n";
    echo "- qty_used: Jumlah quantity yang diambil dari batch HPP ini\n";
    echo "- Total qty_used harus sama dengan kuantitas item\n\n";
    
} catch (\Exception $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI ===\n";