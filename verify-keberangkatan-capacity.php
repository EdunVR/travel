<?php

/**
 * Script to verify keberangkatan capacity matches package capacity
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Keberangkatan;
use App\Models\TravelPackage;

echo "===========================================\n";
echo "VERIFY KEBERANGKATAN CAPACITY\n";
echo "===========================================\n\n";

try {
    $allKeberangkatan = Keberangkatan::with('travelPackage')->get();
    
    if ($allKeberangkatan->isEmpty()) {
        echo "⚠ Tidak ada data keberangkatan di database\n\n";
        exit(0);
    }
    
    echo "Total keberangkatan: " . $allKeberangkatan->count() . "\n\n";
    
    $correct = 0;
    $incorrect = 0;
    $noPackage = 0;
    $zeroCapacity = 0;
    
    $issues = [];
    
    foreach ($allKeberangkatan as $keberangkatan) {
        $package = $keberangkatan->travelPackage;
        
        if (!$package) {
            $noPackage++;
            $issues[] = [
                'id' => $keberangkatan->id,
                'code' => $keberangkatan->keberangkatan_code,
                'issue' => 'Paket tidak ditemukan',
                'capacity' => $keberangkatan->total_jamaah
            ];
            continue;
        }
        
        if ($keberangkatan->total_jamaah == 0) {
            $zeroCapacity++;
            $issues[] = [
                'id' => $keberangkatan->id,
                'code' => $keberangkatan->keberangkatan_code,
                'issue' => 'Kapasitas 0',
                'package_name' => $package->package_name,
                'package_capacity' => $package->capacity,
                'keberangkatan_capacity' => $keberangkatan->total_jamaah
            ];
            $incorrect++;
            continue;
        }
        
        if ($keberangkatan->total_jamaah != $package->capacity) {
            $issues[] = [
                'id' => $keberangkatan->id,
                'code' => $keberangkatan->keberangkatan_code,
                'issue' => 'Kapasitas tidak sesuai',
                'package_name' => $package->package_name,
                'package_capacity' => $package->capacity,
                'keberangkatan_capacity' => $keberangkatan->total_jamaah
            ];
            $incorrect++;
        } else {
            $correct++;
        }
    }
    
    // Display summary
    echo "RINGKASAN:\n";
    echo "─────────────────────────────────────────\n";
    echo "✓ Benar: {$correct}\n";
    echo "✗ Tidak sesuai: {$incorrect}\n";
    echo "⚠ Tanpa paket: {$noPackage}\n";
    echo "⊘ Kapasitas 0: {$zeroCapacity}\n\n";
    
    // Display issues
    if (!empty($issues)) {
        echo "DETAIL MASALAH:\n";
        echo "─────────────────────────────────────────\n\n";
        
        foreach ($issues as $issue) {
            echo "Keberangkatan #{$issue['id']}: {$issue['code']}\n";
            echo "  Issue: {$issue['issue']}\n";
            
            if (isset($issue['package_name'])) {
                echo "  Paket: {$issue['package_name']}\n";
                echo "  Kapasitas Paket: {$issue['package_capacity']}\n";
                echo "  Kapasitas Keberangkatan: {$issue['keberangkatan_capacity']}\n";
            } elseif (isset($issue['capacity'])) {
                echo "  Kapasitas Keberangkatan: {$issue['capacity']}\n";
            }
            
            echo "\n";
        }
        
        echo "─────────────────────────────────────────\n";
        echo "REKOMENDASI:\n";
        echo "Jalankan script: php fix-keberangkatan-capacity.php\n\n";
    } else {
        echo "✓ SEMUA DATA SUDAH BENAR!\n";
        echo "  Tidak ada masalah kapasitas keberangkatan\n\n";
    }
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n\n";
    exit(1);
}
