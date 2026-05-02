<?php

/**
 * Debug HPP Calculation Mismatch
 * 
 * Masalah: HPP yang tersimpan di database (hpp_produk.hpp) tidak sama dengan 
 * HPP/unit yang ditampilkan di card grid halaman produksi.
 * 
 * Analisis:
 * 1. Saat realisasi: HPP dihitung menggunakan harga_bahan.harga_beli (first record)
 * 2. Saat tampil grid: HPP dihitung menggunakan getFifoPrice() (FIFO method)
 * 
 * Penyebab: Perbedaan metode perhitungan harga material
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use App\Models\HppProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔍 ANALISIS HPP CALCULATION MISMATCH\n";
echo "=====================================\n\n";

// Ambil contoh produksi yang sudah ada realisasi
$productions = Production::with(['materials', 'laborCosts', 'operationalCosts', 'hppRecords'])
    ->whereHas('hppRecords', function($query) {
        $query->where('hpp', '>', 0);
    })
    ->limit(3)
    ->get();

if ($productions->isEmpty()) {
    echo "❌ Tidak ada produksi dengan HPP yang tersimpan\n";
    exit;
}

foreach ($productions as $production) {
    echo "📋 PRODUKSI: {$production->production_code}\n";
    echo "Target Quantity: " . number_format($production->target_quantity) . " unit\n";
    
    // 1. Hitung HPP menggunakan metode saat realisasi (harga_bahan.harga_beli first)
    echo "\n1️⃣ METODE REALISASI (harga_bahan.harga_beli first):\n";
    $realizationMaterialCost = $production->materials->sum(function($material) {
        if ($material->material_type === 'bahan') {
            $bahan = \App\Models\Bahan::with('hargaBahan')->find($material->material_id);
            if ($bahan && $bahan->hargaBahan && $bahan->hargaBahan->isNotEmpty()) {
                $hargaBahan = $bahan->hargaBahan->first();
                $cost = $material->quantity_required * ($hargaBahan->harga_beli ?? 0);
                echo "   - {$bahan->nama_bahan}: {$material->quantity_required} x Rp " . 
                     number_format($hargaBahan->harga_beli ?? 0) . " = Rp " . number_format($cost) . "\n";
                return $cost;
            }
            return 0;
        } else {
            $produk = \App\Models\Produk::find($material->material_id);
            if ($produk && method_exists($produk, 'calculateHpp')) {
                $hpp = $produk->calculateHpp() ?? 0;
                $cost = $material->quantity_required * $hpp;
                echo "   - {$produk->nama_produk}: {$material->quantity_required} x Rp " . 
                     number_format($hpp) . " = Rp " . number_format($cost) . "\n";
                return $cost;
            }
            return 0;
        }
    });
    
    $laborCost = $production->laborCosts->sum(function($labor) {
        return $labor->worker_count * $labor->cost_per_worker;
    });
    
    $operationalCost = $production->operationalCosts->sum('amount');
    $realizationTotalCost = $realizationMaterialCost + $laborCost + $operationalCost;
    $realizationHppPerUnit = $production->target_quantity > 0 ? $realizationTotalCost / $production->target_quantity : 0;
    
    echo "   Material Cost: Rp " . number_format($realizationMaterialCost) . "\n";
    echo "   Labor Cost: Rp " . number_format($laborCost) . "\n";
    echo "   Operational Cost: Rp " . number_format($operationalCost) . "\n";
    echo "   Total Cost: Rp " . number_format($realizationTotalCost) . "\n";
    echo "   HPP per Unit: Rp " . number_format($realizationHppPerUnit) . "\n";
    
    // 2. Hitung HPP menggunakan metode grid (FIFO)
    echo "\n2️⃣ METODE GRID (FIFO):\n";
    $gridMaterialCost = $production->materials->sum(function($material) {
        $fifoPrice = getFifoPrice($material->material_id, $material->material_type);
        $cost = $material->quantity_required * $fifoPrice;
        
        if ($material->material_type === 'bahan') {
            $bahan = \App\Models\Bahan::find($material->material_id);
            echo "   - {$bahan->nama_bahan}: {$material->quantity_required} x Rp " . 
                 number_format($fifoPrice) . " (FIFO) = Rp " . number_format($cost) . "\n";
        } else {
            $produk = \App\Models\Produk::find($material->material_id);
            echo "   - {$produk->nama_produk}: {$material->quantity_required} x Rp " . 
                 number_format($fifoPrice) . " (HPP) = Rp " . number_format($cost) . "\n";
        }
        
        return $cost;
    });
    
    $gridTotalCost = $gridMaterialCost + $laborCost + $operationalCost;
    $gridHppPerUnit = $production->target_quantity > 0 ? $gridTotalCost / $production->target_quantity : 0;
    
    echo "   Material Cost: Rp " . number_format($gridMaterialCost) . "\n";
    echo "   Labor Cost: Rp " . number_format($laborCost) . "\n";
    echo "   Operational Cost: Rp " . number_format($operationalCost) . "\n";
    echo "   Total Cost: Rp " . number_format($gridTotalCost) . "\n";
    echo "   HPP per Unit: Rp " . number_format($gridHppPerUnit) . "\n";
    
    // 3. HPP yang tersimpan di database
    echo "\n3️⃣ HPP TERSIMPAN DI DATABASE:\n";
    foreach ($production->hppRecords as $hppRecord) {
        echo "   - HPP Record ID {$hppRecord->id}: Rp " . number_format($hppRecord->hpp) . "\n";
    }
    
    // 4. Perbandingan
    echo "\n📊 PERBANDINGAN:\n";
    echo "   Metode Realisasi: Rp " . number_format($realizationHppPerUnit) . "\n";
    echo "   Metode Grid: Rp " . number_format($gridHppPerUnit) . "\n";
    echo "   Database: Rp " . number_format($production->hppRecords->first()->hpp ?? 0) . "\n";
    
    $difference = abs($realizationHppPerUnit - $gridHppPerUnit);
    if ($difference > 1) {
        echo "   ⚠️ SELISIH: Rp " . number_format($difference) . "\n";
        echo "   📝 PENYEBAB: Perbedaan metode perhitungan harga material\n";
    } else {
        echo "   ✅ HPP sudah konsisten\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// Function helper untuk FIFO price (copy dari controller)
function getFifoPrice($materialId, $materialType = 'bahan')
{
    if ($materialType === 'bahan') {
        // Get FIFO price from harga_bahan table (oldest first)
        $hargaBahan = DB::table('harga_bahan')
            ->where('id_bahan', $materialId)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO order
            ->first();
        
        if ($hargaBahan) {
            return $hargaBahan->harga_beli;
        }
        
        // Fallback to base price from bahan table
        $bahanData = \App\Models\Bahan::find($materialId);
        return $bahanData ? $bahanData->harga_beli : 0;
    } else {
        // For produk type materials
        $produk = \App\Models\Produk::find($materialId);
        if ($produk && method_exists($produk, 'calculateHpp')) {
            return $produk->calculateHpp() ?? 0;
        }
        return 0;
    }
}

echo "🔧 SOLUSI YANG DIREKOMENDASIKAN:\n";
echo "================================\n";
echo "1. Gunakan metode FIFO yang konsisten di kedua tempat\n";
echo "2. Update method addMultiProductRealization() untuk menggunakan getFifoPrice()\n";
echo "3. Atau update method getData() untuk menggunakan HPP yang tersimpan di database\n\n";

echo "✅ Analisis selesai!\n";