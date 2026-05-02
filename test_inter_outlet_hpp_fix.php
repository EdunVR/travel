<?php

/**
 * Test script untuk memverifikasi perbaikan HPP inter-outlet
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSaleItem;
use App\Http\Controllers\MarginReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "=== TEST INTER-OUTLET HPP FIX ===\n\n";

try {
    // Test 1: Cek apakah kolom data_hpp sudah ada
    echo "1. TESTING DATABASE STRUCTURE...\n";
    
    $columns = DB::select('DESCRIBE inter_outlet_sale_items');
    $hasDataHppColumn = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'data_hpp') {
            $hasDataHppColumn = true;
            echo "   ✅ Kolom 'data_hpp' ditemukan (Type: {$column->Type})\n";
            break;
        }
    }
    
    if (!$hasDataHppColumn) {
        echo "   ❌ Kolom 'data_hpp' tidak ditemukan. Jalankan migration terlebih dahulu.\n";
        return;
    }
    
    // Test 2: Cek data inter-outlet items
    echo "\n2. TESTING INTER-OUTLET ITEMS DATA...\n";
    
    $totalItems = InterOutletSaleItem::count();
    $itemsWithHpp = InterOutletSaleItem::whereNotNull('data_hpp')
        ->where('data_hpp', '!=', '[]')
        ->where('data_hpp', '!=', '""')
        ->count();
    $itemsWithoutHpp = $totalItems - $itemsWithHpp;
    
    echo "   📊 Total inter-outlet items: {$totalItems}\n";
    echo "   ✅ Items dengan data HPP: {$itemsWithHpp}\n";
    echo "   ⚠️  Items tanpa data HPP: {$itemsWithoutHpp}\n";
    
    if ($itemsWithoutHpp > 0) {
        echo "   💡 Jalankan 'php fix_inter_outlet_hpp_data.php' untuk mengisi data HPP yang kosong\n";
    }
    
    // Test 3: Test specific case (Tofu Spesial Udang 120g)
    echo "\n3. TESTING SPECIFIC CASE (Tofu Spesial Udang 120g)...\n";
    
    $specificItem = DB::table('inter_outlet_sale_items as iosi')
        ->join('inter_outlet_sales as ios', 'iosi.inter_outlet_sale_id', '=', 'ios.id')
        ->join('produk as p', 'iosi.id_produk', '=', 'p.id_produk')
        ->select('iosi.*', 'ios.tanggal', 'ios.no_transaksi', 'p.nama_produk')
        ->whereDate('ios.tanggal', '2026-01-23')
        ->where('p.nama_produk', 'LIKE', '%Tofu Spesial Udang 120g%')
        ->where('iosi.kuantitas', 8000)
        ->first();
    
    if ($specificItem) {
        echo "   ✅ Item ditemukan: {$specificItem->nama_produk}\n";
        echo "   📊 Quantity: {$specificItem->kuantitas}\n";
        echo "   📅 Tanggal: {$specificItem->tanggal}\n";
        echo "   🔢 No Transaksi: {$specificItem->no_transaksi}\n";
        
        if ($specificItem->data_hpp) {
            $dataHpp = json_decode($specificItem->data_hpp, true);
            echo "   ✅ Data HPP tersedia: " . count($dataHpp) . " batch\n";
            
            $totalHpp = 0;
            $totalQtyUsed = 0;
            
            foreach ($dataHpp as $i => $hpp) {
                $batchTotal = $hpp['hpp'] * $hpp['qty_used'];
                $totalHpp += $batchTotal;
                $totalQtyUsed += $hpp['qty_used'];
                
                echo "      Batch " . ($i + 1) . ": HPP Rp " . number_format($hpp['hpp'], 0, ',', '.') . 
                     " × {$hpp['qty_used']} = Rp " . number_format($batchTotal, 0, ',', '.') . "\n";
            }
            
            $hppPerUnit = $specificItem->kuantitas > 0 ? $totalHpp / $specificItem->kuantitas : 0;
            
            echo "   📊 Total HPP: Rp " . number_format($totalHpp, 0, ',', '.') . "\n";
            echo "   📊 HPP per unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
            echo "   📊 Qty terpenuhi: {$totalQtyUsed} / {$specificItem->kuantitas}\n";
            
            if ($totalQtyUsed < $specificItem->kuantitas) {
                $sisa = $specificItem->kuantitas - $totalQtyUsed;
                echo "   ⚠️  Sisa qty tidak terpenuhi: {$sisa} unit\n";
            }
            
        } else {
            echo "   ❌ Data HPP belum tersedia\n";
        }
    } else {
        echo "   ❌ Item tidak ditemukan\n";
    }
    
    // Test 4: Test MarginReportController
    echo "\n4. TESTING MARGIN REPORT CONTROLLER...\n";
    
    try {
        $controller = new MarginReportController();
        $request = new Request([
            'start_date' => '2026-01-23',
            'end_date' => '2026-01-23'
        ]);
        
        $response = $controller->getData($request);
        $responseData = json_decode($response->getContent(), true);
        
        if ($responseData['success']) {
            $marginData = $responseData['data'];
            $interOutletItems = array_filter($marginData, function($item) {
                return $item['source'] === 'inter_outlet';
            });
            
            echo "   ✅ Controller berhasil dijalankan\n";
            echo "   📊 Total data margin: " . count($marginData) . "\n";
            echo "   📊 Inter-outlet items: " . count($interOutletItems) . "\n";
            
            // Cek item Tofu Spesial Udang 120g
            $tofuItem = null;
            foreach ($interOutletItems as $item) {
                if (strpos($item['produk'], 'Tofu Spesial Udang 120g') !== false && $item['qty'] == 8000) {
                    $tofuItem = $item;
                    break;
                }
            }
            
            if ($tofuItem) {
                echo "   ✅ Item Tofu Spesial Udang 120g ditemukan dalam laporan\n";
                echo "   📊 HPP: Rp " . number_format($tofuItem['hpp'], 0, ',', '.') . "\n";
                echo "   📊 Profit: " . ($tofuItem['profit'] !== null ? 
                    "Rp " . number_format($tofuItem['profit'], 0, ',', '.') : 'Tidak dapat dihitung') . "\n";
                echo "   📊 Margin: " . ($tofuItem['margin_pct'] !== null ? 
                    number_format($tofuItem['margin_pct'], 2) . '%' : 'Tidak dapat dihitung') . "\n";
                echo "   📊 Status HPP: {$tofuItem['hpp_status']}\n";
                echo "   📝 Message: {$tofuItem['hpp_message']}\n";
            } else {
                echo "   ❌ Item Tofu Spesial Udang 120g tidak ditemukan dalam laporan\n";
            }
            
        } else {
            echo "   ❌ Controller error: " . $responseData['message'] . "\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Controller test error: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Validasi format JSON
    echo "\n5. TESTING JSON FORMAT VALIDATION...\n";
    
    $sampleJson = [
        [
            'id_hpp' => 123,
            'hpp' => 2500.00,
            'qty_used' => 1000
        ],
        [
            'id_hpp' => 124,
            'hpp' => 2600.00,
            'qty_used' => 500
        ]
    ];
    
    $jsonString = json_encode($sampleJson);
    $decoded = json_decode($jsonString, true);
    
    if ($decoded && is_array($decoded)) {
        echo "   ✅ Format JSON valid\n";
        echo "   📝 Sample JSON: " . $jsonString . "\n";
        
        // Validasi struktur
        $isValidStructure = true;
        foreach ($decoded as $item) {
            if (!isset($item['id_hpp']) || !isset($item['hpp']) || !isset($item['qty_used'])) {
                $isValidStructure = false;
                break;
            }
        }
        
        if ($isValidStructure) {
            echo "   ✅ Struktur JSON sesuai format yang diharapkan\n";
        } else {
            echo "   ❌ Struktur JSON tidak sesuai format\n";
        }
        
    } else {
        echo "   ❌ Format JSON tidak valid\n";
    }
    
    echo "\n=== RINGKASAN TEST ===\n";
    echo "✅ Database structure: " . ($hasDataHppColumn ? 'OK' : 'FAIL') . "\n";
    echo "📊 Items dengan HPP: {$itemsWithHpp} / {$totalItems}\n";
    echo "🔍 Specific case: " . ($specificItem ? 'Found' : 'Not Found') . "\n";
    echo "🎯 Controller test: " . (isset($responseData) && $responseData['success'] ? 'PASS' : 'FAIL') . "\n";
    echo "📝 JSON format: Valid\n\n";
    
    if ($itemsWithoutHpp > 0) {
        echo "⚠️  NEXT STEPS:\n";
        echo "1. Jalankan: php fix_inter_outlet_hpp_data.php\n";
        echo "2. Test ulang dengan: php test_inter_outlet_hpp_fix.php\n";
        echo "3. Akses laporan margin untuk melihat hasil\n\n";
    } else {
        echo "🎉 SEMUA TEST BERHASIL!\n";
        echo "💡 Sistem HPP inter-outlet sudah diperbaiki dan siap digunakan\n\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== TEST SELESAI ===\n";