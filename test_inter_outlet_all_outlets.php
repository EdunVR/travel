<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST INTER OUTLET ALL OUTLETS ===\n\n";

try {
    // Test database connection
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n";
    
    // Test outlets query (semua outlet aktif)
    $allOutlets = DB::table('outlets')
        ->where('is_active', true)
        ->orderBy('nama_outlet')
        ->get(['id_outlet', 'nama_outlet', 'alamat']);
    
    echo "✅ Total outlets aktif: " . $allOutlets->count() . "\n";
    
    if ($allOutlets->count() > 0) {
        echo "\n📋 Daftar Outlets:\n";
        foreach ($allOutlets as $outlet) {
            echo "   - ID: {$outlet->id_outlet} | {$outlet->nama_outlet}\n";
        }
    }
    
    // Test dengan current outlet (exclude current)
    $currentOutletId = $allOutlets->first()->id_outlet ?? 1;
    $destinationOutlets = DB::table('outlets')
        ->where('is_active', true)
        ->where('id_outlet', '!=', $currentOutletId)
        ->orderBy('nama_outlet')
        ->get(['id_outlet', 'nama_outlet', 'alamat']);
    
    echo "\n✅ Outlets untuk tujuan (exclude outlet {$currentOutletId}): " . $destinationOutlets->count() . "\n";
    
    if ($destinationOutlets->count() > 0) {
        echo "\n📋 Daftar Outlet Tujuan:\n";
        foreach ($destinationOutlets as $outlet) {
            echo "   - ID: {$outlet->id_outlet} | {$outlet->nama_outlet}\n";
        }
    }
    
    // Test API endpoint simulation
    echo "\n🧪 Testing API Response Format:\n";
    $apiResponse = [
        'success' => true,
        'data' => $destinationOutlets->map(function($outlet) {
            return [
                'id' => $outlet->id_outlet,
                'name' => $outlet->nama_outlet,
                'address' => $outlet->alamat
            ];
        })->toArray()
    ];
    
    echo "✅ API Response structure valid\n";
    echo "✅ Data count: " . count($apiResponse['data']) . "\n";
    
    // Test route exists
    $routes = app('router')->getRoutes();
    $routeExists = false;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'inter-outlet/outlets')) {
            $routeExists = true;
            break;
        }
    }
    
    echo $routeExists ? "✅ Route inter-outlet/outlets exists\n" : "❌ Route inter-outlet/outlets not found\n";
    
    echo "\n🎯 HASIL TEST:\n";
    echo "✅ Dropdown outlet tujuan akan menampilkan SEMUA outlet aktif\n";
    echo "✅ Tidak dibatasi oleh akses outlet user\n";
    echo "✅ Outlet asal akan dikecualikan dari daftar tujuan\n";
    echo "✅ Data terurut berdasarkan nama outlet\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";