<?php

/**
 * Script to fix existing keberangkatan records with total_jamaah = 0
 * Updates them to match their package's capacity
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Keberangkatan;
use App\Models\TravelPackage;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "FIX KEBERANGKATAN CAPACITY SCRIPT\n";
echo "===========================================\n\n";

try {
    // Find all keberangkatan with total_jamaah = 0
    $keberangkatanList = Keberangkatan::where('total_jamaah', 0)
        ->with('travelPackage')
        ->get();
    
    if ($keberangkatanList->isEmpty()) {
        echo "✓ Tidak ada keberangkatan dengan kapasitas 0\n";
        echo "  Semua data sudah benar!\n\n";
        exit(0);
    }
    
    echo "Ditemukan " . $keberangkatanList->count() . " keberangkatan dengan kapasitas 0\n\n";
    
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    
    DB::beginTransaction();
    
    foreach ($keberangkatanList as $keberangkatan) {
        $package = $keberangkatan->travelPackage;
        
        if (!$package) {
            echo "✗ Keberangkatan #{$keberangkatan->id} ({$keberangkatan->keberangkatan_code})\n";
            echo "  SKIPPED: Paket tidak ditemukan\n\n";
            $skipped++;
            continue;
        }
        
        if (!$package->capacity || $package->capacity <= 0) {
            echo "✗ Keberangkatan #{$keberangkatan->id} ({$keberangkatan->keberangkatan_code})\n";
            echo "  SKIPPED: Paket '{$package->package_name}' juga memiliki kapasitas 0\n\n";
            $skipped++;
            continue;
        }
        
        try {
            $oldCapacity = $keberangkatan->total_jamaah;
            $newCapacity = $package->capacity;
            
            $keberangkatan->total_jamaah = $newCapacity;
            $keberangkatan->save();
            
            echo "✓ Keberangkatan #{$keberangkatan->id} ({$keberangkatan->keberangkatan_code})\n";
            echo "  Paket: {$package->package_name}\n";
            echo "  Kapasitas: {$oldCapacity} → {$newCapacity}\n\n";
            
            $updated++;
        } catch (\Exception $e) {
            echo "✗ Keberangkatan #{$keberangkatan->id} ({$keberangkatan->keberangkatan_code})\n";
            echo "  ERROR: {$e->getMessage()}\n\n";
            $errors++;
        }
    }
    
    if ($errors > 0) {
        DB::rollBack();
        echo "\n===========================================\n";
        echo "ROLLBACK: Ada error saat update\n";
        echo "===========================================\n";
        echo "Updated: {$updated}\n";
        echo "Skipped: {$skipped}\n";
        echo "Errors: {$errors}\n\n";
        exit(1);
    }
    
    DB::commit();
    
    echo "\n===========================================\n";
    echo "SELESAI!\n";
    echo "===========================================\n";
    echo "✓ Berhasil update: {$updated} keberangkatan\n";
    echo "⊘ Dilewati: {$skipped} keberangkatan\n";
    echo "✗ Error: {$errors}\n\n";
    
    if ($updated > 0) {
        echo "Semua keberangkatan sudah disesuaikan dengan kapasitas paket!\n\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n\n";
    exit(1);
}
