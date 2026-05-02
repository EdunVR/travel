<?php

/**
 * Script untuk testing filter CRM tipe customer berdasarkan outlet
 * 
 * Jalankan dengan: php artisan tinker
 * Kemudian: include 'test_produk_tipe_outlet_filter.php';
 */

use App\Models\Outlet;
use App\Models\Tipe;
use App\Http\Controllers\CustomerTypeController;
use Illuminate\Support\Facades\DB;

echo "=== Testing CRM Tipe Customer Outlet Filter ===\n\n";

// Test 1: Cek struktur tabel tipe
echo "Test 1: Cek struktur tabel tipe\n";
try {
    $columns = DB::select("DESCRIBE tipe");
    $hasOutletId = false;
    foreach ($columns as $column) {
        if ($column->Field === 'id_outlet') {
            $hasOutletId = true;
            echo "✓ Field id_outlet ditemukan\n";
            echo "  - Type: {$column->Type}\n";
            echo "  - Null: {$column->Null}\n";
            echo "  - Key: {$column->Key}\n";
            break;
        }
    }
    
    if (!$hasOutletId) {
        echo "⚠️  Field id_outlet tidak ditemukan. Jalankan migration terlebih dahulu:\n";
        echo "   php artisan migrate\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Cek data outlet
echo "Test 2: Cek data outlet\n";
try {
    $outlets = Outlet::take(5)->get();
    
    if ($outlets->count() > 0) {
        echo "✓ Ditemukan {$outlets->count()} outlet:\n";
        foreach ($outlets as $outlet) {
            echo "  - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
        }
    } else {
        echo "⚠️  Tidak ada data outlet. Tambahkan data outlet terlebih dahulu.\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Cek data tipe
echo "Test 3: Cek data tipe\n";
try {
    $tipes = Tipe::with('outlet')->take(10)->get();
    
    if ($tipes->count() > 0) {
        echo "✓ Ditemukan {$tipes->count()} tipe:\n";
        foreach ($tipes as $tipe) {
            $outletName = $tipe->outlet ? $tipe->outlet->nama_outlet : 'Tidak ada outlet';
            echo "  - {$tipe->nama_tipe} (Outlet: {$outletName})\n";
        }
    } else {
        echo "⚠️  Tidak ada data tipe. Akan menggunakan hardcoded types.\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test endpoint getData tanpa filter
echo "Test 4: Test endpoint getData tanpa filter\n";
try {
    $controller = new CustomerTypeController();
    $request = new \Illuminate\Http\Request();
    
    $response = $controller->getData($request);
    $data = json_decode($response->getContent(), true);
    
    echo "✓ Endpoint getData berhasil\n";
    echo "  - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "  - Total tipe: " . count($data['data']) . "\n";
    
    foreach ($data['data'] as $type) {
        echo "    - {$type['nama_tipe']} (Outlet: {$type['outlet_name']})\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Test endpoint getData dengan filter outlet
echo "Test 5: Test endpoint getData dengan filter outlet\n";
try {
    $firstOutlet = Outlet::first();
    
    if ($firstOutlet) {
        $controller = new CustomerTypeController();
        $request = new \Illuminate\Http\Request(['outlet_id' => $firstOutlet->id_outlet]);
        
        $response = $controller->getData($request);
        $data = json_decode($response->getContent(), true);
        
        echo "✓ Endpoint getData dengan filter outlet '{$firstOutlet->nama_outlet}' berhasil\n";
        echo "  - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "  - Total tipe: " . count($data['data']) . "\n";
        
        foreach ($data['data'] as $type) {
            echo "    - {$type['nama_tipe']} (Outlet: {$type['outlet_name']})\n";
        }
    } else {
        echo "⚠️  Tidak ada outlet untuk testing filter\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Statistik relasi
echo "Test 6: Statistik relasi outlet-tipe\n";
try {
    $stats = DB::select("
        SELECT 
            COUNT(DISTINCT o.id_outlet) as total_outlets,
            COUNT(DISTINCT t.id_tipe) as total_tipes,
            COUNT(DISTINCT CASE WHEN t.id_outlet IS NOT NULL THEN t.id_tipe END) as tipes_with_outlet,
            COUNT(DISTINCT CASE WHEN t.id_outlet IS NULL THEN t.id_tipe END) as tipes_without_outlet
        FROM outlets o
        LEFT JOIN tipe t ON o.id_outlet = t.id_outlet
    ")[0];
    
    echo "Statistik:\n";
    echo "  - Total outlets: {$stats->total_outlets}\n";
    echo "  - Total tipes: {$stats->total_tipes}\n";
    echo "  - Tipes dengan outlet: {$stats->tipes_with_outlet}\n";
    echo "  - Tipes tanpa outlet: {$stats->tipes_without_outlet}\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Selesai ===\n";

// Saran setup jika belum ada data
echo "\nSaran Setup:\n";
echo "1. Jika belum ada data tipe, buat data sample:\n";
echo "   \$outlet1 = Outlet::first();\n";
echo "   \$outlet2 = Outlet::skip(1)->first();\n";
echo "   \n";
echo "   Tipe::create(['nama_tipe' => 'Barang Dagang', 'id_outlet' => \$outlet1->id_outlet]);\n";
echo "   Tipe::create(['nama_tipe' => 'Jasa', 'id_outlet' => \$outlet1->id_outlet]);\n";
echo "   Tipe::create(['nama_tipe' => 'Paket Travel', 'id_outlet' => \$outlet2->id_outlet]);\n";
echo "   Tipe::create(['nama_tipe' => 'Produk Kustom', 'id_outlet' => \$outlet2->id_outlet]);\n\n";

echo "2. Untuk testing di browser:\n";
echo "   - Buka halaman admin/crm/tipe\n";
echo "   - Pilih outlet di filter dropdown\n";
echo "   - Lihat apakah data tipe berubah sesuai outlet\n";
echo "   - Test juga create/edit tipe dengan pilihan outlet\n\n";

echo "3. Cek network tab di browser untuk memastikan:\n";
echo "   - Request ke /admin/crm/tipe/data berhasil\n";
echo "   - Response berisi data tipe yang sesuai filter\n";
echo "   - Tidak ada error 500 atau 404\n\n";

echo "4. Test API endpoints:\n";
echo "   - GET /admin/crm/tipe/data (tanpa filter)\n";
echo "   - GET /admin/crm/tipe/data?outlet_id=1 (dengan filter)\n";
echo "   - POST /admin/crm/tipe (create dengan outlet)\n";
echo "   - PUT /admin/crm/tipe/{id} (update dengan outlet)\n";