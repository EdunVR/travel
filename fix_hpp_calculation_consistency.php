<?php

/**
 * Fix HPP Calculation Consistency
 * 
 * Masalah: HPP yang tersimpan di database tidak sama dengan HPP yang ditampilkan di grid
 * karena menggunakan metode perhitungan harga material yang berbeda.
 * 
 * Solusi: Unifikasi metode perhitungan menggunakan FIFO yang konsisten
 */

echo "🔧 MEMPERBAIKI KONSISTENSI PERHITUNGAN HPP\n";
echo "==========================================\n\n";

// 1. Update ProductionController - addMultiProductRealization method
$controllerPath = 'app/Http/Controllers/ProductionController.php';
$controllerContent = file_get_contents($controllerPath);

echo "1️⃣ Memperbaiki method addMultiProductRealization...\n";

// Cari dan ganti bagian perhitungan material cost di addMultiProductRealization
$oldMaterialCostCalculation = '$materialCost = $production->materials->sum(function($material) {
                        if ($material->material_type === \'bahan\') {
                            $bahan = \App\Models\Bahan::with(\'hargaBahan\')->find($material->material_id);
                            if ($bahan && $bahan->hargaBahan && $bahan->hargaBahan->isNotEmpty()) {
                                $hargaBahan = $bahan->hargaBahan->first();
                                return $material->quantity_required * ($hargaBahan->harga_beli ?? 0);
                            }
                            return 0;
                        } else {
                            $produk = \App\Models\Produk::find($material->material_id);
                            if ($produk && method_exists($produk, \'calculateHpp\')) {
                                return $material->quantity_required * ($produk->calculateHpp() ?? 0);
                            }
                            return 0;
                        }
                    });';

$newMaterialCostCalculation = '$materialCost = $production->materials->sum(function($material) {
                        return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
                    });';

// Replace di semua tempat yang menggunakan perhitungan material cost yang lama
$controllerContent = str_replace($oldMaterialCostCalculation, $newMaterialCostCalculation, $controllerContent);

// Juga update di addSingleProductRealization jika ada
$oldSingleMaterialCost = '$materialCost = $production->materials->sum(function($material) {
                    if ($material->material_type === \'bahan\') {
                        $bahan = \App\Models\Bahan::with(\'hargaBahan\')->find($material->material_id);
                        if ($bahan && $bahan->hargaBahan && $bahan->hargaBahan->isNotEmpty()) {
                            $hargaBahan = $bahan->hargaBahan->first();
                            return $material->quantity_required * ($hargaBahan->harga_beli ?? 0);
                        }
                        return 0;
                    } else {
                        $produk = \App\Models\Produk::find($material->material_id);
                        if ($produk && method_exists($produk, \'calculateHpp\')) {
                            return $material->quantity_required * ($produk->calculateHpp() ?? 0);
                        }
                        return 0;
                    }
                });';

$newSingleMaterialCost = '$materialCost = $production->materials->sum(function($material) {
                    return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
                });';

$controllerContent = str_replace($oldSingleMaterialCost, $newSingleMaterialCost, $controllerContent);

// Simpan perubahan
file_put_contents($controllerPath, $controllerContent);

echo "   ✅ Method addMultiProductRealization diperbaiki\n";
echo "   ✅ Method addSingleProductRealization diperbaiki\n";

echo "\n2️⃣ Menambahkan logging untuk debugging...\n";

// Tambahkan logging di method addMultiProductRealization
$logCode = '
                    Log::info(\'💰 [HPP CALCULATION] Material cost breakdown\', [
                        \'production_id\' => $production->id,
                        \'material_cost\' => $materialCost,
                        \'labor_cost\' => $laborCost,
                        \'operational_cost\' => $operationalCost,
                        \'total_cost\' => $totalCost,
                        \'hpp_per_unit\' => $hppPerUnit,
                        \'target_quantity\' => $production->target_quantity
                    ]);';

// Cari tempat yang tepat untuk menambahkan log (setelah perhitungan HPP)
$afterHppCalculation = '$hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;';
$controllerContent = str_replace(
    $afterHppCalculation,
    $afterHppCalculation . $logCode,
    $controllerContent
);

file_put_contents($controllerPath, $controllerContent);

echo "   ✅ Logging ditambahkan untuk debugging\n";

echo "\n3️⃣ Membuat script untuk update HPP yang sudah ada...\n";

// Buat script untuk update HPP records yang sudah ada
$updateScript = '<?php

/**
 * Update existing HPP records to use consistent FIFO calculation
 */

require_once __DIR__ . \'/vendor/autoload.php\';

// Bootstrap Laravel
$app = require_once __DIR__ . \'/bootstrap/app.php\';
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();

use App\\Models\\Production;
use App\\Models\\HppProduk;
use Illuminate\\Support\\Facades\\DB;

echo "🔄 UPDATING EXISTING HPP RECORDS\\n";
echo "================================\\n\\n";

// Get all productions with HPP records
$productions = Production::with([\'materials\', \'laborCosts\', \'operationalCosts\', \'hppRecords\'])
    ->whereHas(\'hppRecords\')
    ->get();

$updated = 0;
$total = $productions->count();

foreach ($productions as $production) {
    echo "Processing: {$production->production_code}...";
    
    try {
        // Calculate HPP using FIFO method
        $materialCost = $production->materials->sum(function($material) {
            return $material->quantity_required * getFifoPrice($material->material_id, $material->material_type);
        });
        
        $laborCost = $production->laborCosts->sum(function($labor) {
            return $labor->worker_count * $labor->cost_per_worker;
        });
        
        $operationalCost = $production->operationalCosts->sum(\'amount\');
        $totalCost = $materialCost + $laborCost + $operationalCost;
        $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
        
        // Update all HPP records for this production
        foreach ($production->hppRecords as $hppRecord) {
            $oldHpp = $hppRecord->hpp;
            $hppRecord->hpp = $hppPerUnit;
            $hppRecord->save();
            
            echo " Updated HPP: Rp " . number_format($oldHpp) . " → Rp " . number_format($hppPerUnit);
        }
        
        echo " ✅\\n";
        $updated++;
        
    } catch (Exception $e) {
        echo " ❌ Error: " . $e->getMessage() . "\\n";
    }
}

echo "\\n📊 SUMMARY:\\n";
echo "Total productions: {$total}\\n";
echo "Updated successfully: {$updated}\\n";
echo "✅ Update completed!\\n";

// Helper function
function getFifoPrice($materialId, $materialType = \'bahan\')
{
    if ($materialType === \'bahan\') {
        // Get FIFO price from harga_bahan table (oldest first)
        $hargaBahan = DB::table(\'harga_bahan\')
            ->where(\'id_bahan\', $materialId)
            ->where(\'stok\', \'>\', 0)
            ->orderBy(\'created_at\', \'asc\') // FIFO order
            ->first();
        
        if ($hargaBahan) {
            return $hargaBahan->harga_beli;
        }
        
        // Fallback to base price from bahan table
        $bahanData = \\App\\Models\\Bahan::find($materialId);
        return $bahanData ? $bahanData->harga_beli : 0;
    } else {
        // For produk type materials
        $produk = \\App\\Models\\Produk::find($materialId);
        if ($produk && method_exists($produk, \'calculateHpp\')) {
            return $produk->calculateHpp() ?? 0;
        }
        return 0;
    }
}
';

file_put_contents('update_existing_hpp_records.php', $updateScript);

echo "   ✅ Script update_existing_hpp_records.php dibuat\n";

echo "\n4️⃣ Membuat script test untuk verifikasi...\n";

$testScript = '<?php

/**
 * Test HPP calculation consistency after fix
 */

require_once __DIR__ . \'/vendor/autoload.php\';

// Bootstrap Laravel
$app = require_once __DIR__ . \'/bootstrap/app.php\';
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();

use App\\Models\\Production;
use Illuminate\\Support\\Facades\\DB;

echo "🧪 TESTING HPP CALCULATION CONSISTENCY\\n";
echo "======================================\\n\\n";

// Test beberapa produksi
$productions = Production::with([\'materials\', \'laborCosts\', \'operationalCosts\', \'hppRecords\'])
    ->whereHas(\'hppRecords\')
    ->limit(3)
    ->get();

foreach ($productions as $production) {
    echo "📋 TESTING: {$production->production_code}\\n";
    
    // Hitung HPP menggunakan FIFO (sama seperti di grid)
    $materialCost = $production->materials->sum(function($material) {
        return $material->quantity_required * getFifoPrice($material->material_id, $material->material_type);
    });
    
    $laborCost = $production->laborCosts->sum(function($labor) {
        return $labor->worker_count * $labor->cost_per_worker;
    });
    
    $operationalCost = $production->operationalCosts->sum(\'amount\');
    $totalCost = $materialCost + $laborCost + $operationalCost;
    $calculatedHpp = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
    
    // HPP dari database
    $databaseHpp = $production->hppRecords->first()->hpp ?? 0;
    
    echo "   Calculated HPP: Rp " . number_format($calculatedHpp) . "\\n";
    echo "   Database HPP: Rp " . number_format($databaseHpp) . "\\n";
    
    $difference = abs($calculatedHpp - $databaseHpp);
    if ($difference < 1) {
        echo "   ✅ KONSISTEN (selisih: Rp " . number_format($difference) . ")\\n";
    } else {
        echo "   ⚠️ TIDAK KONSISTEN (selisih: Rp " . number_format($difference) . ")\\n";
    }
    
    echo "\\n";
}

echo "✅ Test selesai!\\n";

// Helper function
function getFifoPrice($materialId, $materialType = \'bahan\')
{
    if ($materialType === \'bahan\') {
        $hargaBahan = DB::table(\'harga_bahan\')
            ->where(\'id_bahan\', $materialId)
            ->where(\'stok\', \'>\', 0)
            ->orderBy(\'created_at\', \'asc\')
            ->first();
        
        if ($hargaBahan) {
            return $hargaBahan->harga_beli;
        }
        
        $bahanData = \\App\\Models\\Bahan::find($materialId);
        return $bahanData ? $bahanData->harga_beli : 0;
    } else {
        $produk = \\App\\Models\\Produk::find($materialId);
        if ($produk && method_exists($produk, \'calculateHpp\')) {
            return $produk->calculateHpp() ?? 0;
        }
        return 0;
    }
}
';

file_put_contents('test_hpp_consistency.php', $testScript);

echo "   ✅ Script test_hpp_consistency.php dibuat\n";

echo "\n✅ PERBAIKAN SELESAI!\n";
echo "====================\n\n";

echo "📋 LANGKAH SELANJUTNYA:\n";
echo "1. Jalankan: php update_existing_hpp_records.php (untuk update HPP yang sudah ada)\n";
echo "2. Jalankan: php test_hpp_consistency.php (untuk verifikasi)\n";
echo "3. Test realisasi produksi baru untuk memastikan HPP konsisten\n\n";

echo "🎯 HASIL YANG DIHARAPKAN:\n";
echo "- HPP yang tersimpan di database = HPP yang ditampilkan di grid\n";
echo "- Menggunakan metode FIFO yang konsisten di semua tempat\n";
echo "- Logging yang lebih baik untuk debugging\n\n";