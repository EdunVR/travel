<?php

/**
 * Debug Production Duplication Issue
 * 
 * Masalah: Saat menambahkan data produksi, data tersimpan/terduplikasi menjadi 2 dalam 1x input
 * 
 * Kemungkinan penyebab:
 * 1. Double form submission
 * 2. Multiple event listeners
 * 3. Browser back/forward cache
 * 4. JavaScript error yang menyebabkan retry
 * 5. Database transaction issue
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

echo "🔍 DEBUGGING PRODUCTION DUPLICATION\n";
echo "===================================\n\n";

// 1. Cek produksi yang dibuat hari ini
echo "1️⃣ PRODUKSI HARI INI:\n";
$todayProductions = Production::whereDate('created_at', today())
    ->orderBy('created_at', 'desc')
    ->get();

if ($todayProductions->isEmpty()) {
    echo "   ❌ Tidak ada produksi yang dibuat hari ini\n";
} else {
    foreach ($todayProductions as $production) {
        echo "   📋 {$production->production_code} - {$production->created_at}\n";
        echo "      Target: {$production->target_quantity} | Status: {$production->status}\n";
        echo "      HPP Records: " . $production->hppRecords()->count() . "\n";
        echo "      Materials: " . $production->materials()->count() . "\n";
        echo "      Labor Costs: " . $production->laborCosts()->count() . "\n";
        echo "      Operational Costs: " . $production->operationalCosts()->count() . "\n\n";
    }
}

// 2. Cek duplikasi berdasarkan production_code yang sama
echo "2️⃣ DUPLIKASI BERDASARKAN PRODUCTION CODE:\n";
$duplicates = DB::table('productions')
    ->select('production_code', DB::raw('COUNT(*) as count'))
    ->groupBy('production_code')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "   ✅ Tidak ada duplikasi production code\n";
} else {
    foreach ($duplicates as $duplicate) {
        echo "   ⚠️ Production Code: {$duplicate->production_code} - {$duplicate->count} records\n";
        
        $productions = Production::where('production_code', $duplicate->production_code)
            ->orderBy('created_at')
            ->get();
        
        foreach ($productions as $production) {
            echo "      - ID: {$production->id} | Created: {$production->created_at}\n";
        }
        echo "\n";
    }
}

// 3. Cek duplikasi berdasarkan data yang sama dalam waktu dekat
echo "3️⃣ DUPLIKASI BERDASARKAN DATA SERUPA:\n";
$recentProductions = Production::where('created_at', '>=', now()->subHours(24))
    ->orderBy('created_at', 'desc')
    ->get();

$potentialDuplicates = [];
foreach ($recentProductions as $production) {
    $key = $production->outlet_id . '_' . $production->production_line . '_' . $production->target_quantity . '_' . $production->start_date;
    
    if (!isset($potentialDuplicates[$key])) {
        $potentialDuplicates[$key] = [];
    }
    
    $potentialDuplicates[$key][] = $production;
}

$foundDuplicates = false;
foreach ($potentialDuplicates as $key => $productions) {
    if (count($productions) > 1) {
        $foundDuplicates = true;
        echo "   ⚠️ Potential duplicates for key: {$key}\n";
        foreach ($productions as $production) {
            echo "      - {$production->production_code} (ID: {$production->id}) - {$production->created_at}\n";
        }
        echo "\n";
    }
}

if (!$foundDuplicates) {
    echo "   ✅ Tidak ada duplikasi data serupa dalam 24 jam terakhir\n";
}

// 4. Cek log untuk melihat pattern submission
echo "\n4️⃣ ANALISIS LOG SUBMISSION:\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    
    // Cari pattern production store
    $storePattern = '/Production Store Request.*?"production_code":"([^"]+)"/';
    preg_match_all($storePattern, $logContent, $storeMatches);
    
    if (!empty($storeMatches[1])) {
        $storeCounts = array_count_values($storeMatches[1]);
        foreach ($storeCounts as $code => $count) {
            if ($count > 1) {
                echo "   ⚠️ Production code {$code} submitted {$count} times\n";
            }
        }
    } else {
        echo "   ℹ️ Tidak ditemukan log Production Store Request\n";
    }
    
    // Cari pattern production created
    $createdPattern = '/Production Created Successfully.*?"production_code":"([^"]+)"/';
    preg_match_all($createdPattern, $logContent, $createdMatches);
    
    if (!empty($createdMatches[1])) {
        $createdCounts = array_count_values($createdMatches[1]);
        foreach ($createdCounts as $code => $count) {
            if ($count > 1) {
                echo "   ⚠️ Production code {$code} created {$count} times\n";
            }
        }
    } else {
        echo "   ℹ️ Tidak ditemukan log Production Created Successfully\n";
    }
} else {
    echo "   ❌ Log file tidak ditemukan\n";
}

// 5. Cek JavaScript files untuk multiple event listeners
echo "\n5️⃣ ANALISIS JAVASCRIPT:\n";

$jsFiles = [
    'resources/views/admin/produksi/produksi/index.blade.php',
    'public/js/production.js'
];

foreach ($jsFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Cek multiple addEventListener untuk form submission
        $submitListeners = substr_count($content, 'addEventListener(\'submit\'');
        $submitListeners += substr_count($content, 'addEventListener("submit"');
        
        echo "   📄 {$file}:\n";
        echo "      - Submit event listeners: {$submitListeners}\n";
        
        // Cek prevention mechanisms
        $hasPreventDefault = strpos($content, 'preventDefault()') !== false;
        $hasDoubleSubmitPrevention = strpos($content, 'submitting') !== false;
        
        echo "      - Has preventDefault: " . ($hasPreventDefault ? '✅' : '❌') . "\n";
        echo "      - Has double submit prevention: " . ($hasDoubleSubmitPrevention ? '✅' : '❌') . "\n";
        echo "\n";
    }
}

echo "🔧 KEMUNGKINAN PENYEBAB DAN SOLUSI:\n";
echo "===================================\n";
echo "1. Multiple Event Listeners:\n";
echo "   - Periksa apakah ada multiple addEventListener untuk form yang sama\n";
echo "   - Pastikan event listener hanya didaftarkan sekali\n\n";

echo "2. Browser Cache/Back Button:\n";
echo "   - Tambahkan cache control headers\n";
echo "   - Implement proper page reload handling\n\n";

echo "3. JavaScript Error Recovery:\n";
echo "   - Periksa console browser untuk error\n";
echo "   - Pastikan error handling tidak menyebabkan retry\n\n";

echo "4. Database Transaction:\n";
echo "   - Pastikan transaction rollback berfungsi dengan baik\n";
echo "   - Tambahkan unique constraint jika diperlukan\n\n";

echo "5. Network Issues:\n";
echo "   - Timeout yang menyebabkan retry\n";
echo "   - Slow connection yang membuat user click multiple times\n\n";

echo "✅ Analisis selesai!\n";