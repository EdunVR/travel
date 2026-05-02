<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

echo "=== PRE ORDER ADDITIONAL COSTS TEST ===\n\n";

try {
    // Test 1: Check if migration columns exist
    echo "1. Checking database schema...\n";
    
    $columns = DB::select("DESCRIBE pre_order_items");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = [
        'material_instalasi_biaya',
        'material_instalasi_satuan', 
        'material_instalasi_keterangan',
        'pemasangan_pelatihan_biaya',
        'pemasangan_pelatihan_satuan',
        'pemasangan_pelatihan_keterangan',
        'ongkos_kirim_biaya',
        'ongkos_kirim_satuan',
        'ongkos_kirim_komponen',
        'total_biaya_tambahan'
    ];
    
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (empty($missingColumns)) {
        echo "   ✅ All required columns exist\n";
    } else {
        echo "   ❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
        echo "   Please run the migration first!\n";
        exit(1);
    }
    
    // Test 2: Check PreOrderItem model
    echo "\n2. Testing PreOrderItem model...\n";
    
    $item = new \App\Models\PreOrderItem();
    $fillable = $item->getFillable();
    
    $requiredFillable = [
        'material_instalasi_biaya',
        'material_instalasi_satuan',
        'material_instalasi_keterangan',
        'pemasangan_pelatihan_biaya',
        'pemasangan_pelatihan_satuan', 
        'pemasangan_pelatihan_keterangan',
        'ongkos_kirim_biaya',
        'ongkos_kirim_satuan',
        'ongkos_kirim_komponen',
        'total_biaya_tambahan'
    ];
    
    $missingFillable = array_diff($requiredFillable, $fillable);
    
    if (empty($missingFillable)) {
        echo "   ✅ All fields are fillable\n";
    } else {
        echo "   ❌ Missing fillable fields: " . implode(', ', $missingFillable) . "\n";
    }
    
    // Test 3: Test model methods
    echo "\n3. Testing model methods...\n";
    
    if (method_exists($item, 'calculateTotalBiayaTambahan')) {
        echo "   ✅ calculateTotalBiayaTambahan method exists\n";
    } else {
        echo "   ❌ calculateTotalBiayaTambahan method missing\n";
    }
    
    if (method_exists($item, 'getTotalWithAdditionalCostsAttribute')) {
        echo "   ✅ getTotalWithAdditionalCostsAttribute accessor exists\n";
    } else {
        echo "   ❌ getTotalWithAdditionalCostsAttribute accessor missing\n";
    }
    
    // Test 4: Check if view file exists and contains new forms
    echo "\n4. Checking view file...\n";
    
    $viewPath = 'resources/views/admin/pre-orders/index.blade.php';
    if (file_exists($viewPath)) {
        $viewContent = file_get_contents($viewPath);
        
        $requiredElements = [
            'additional_costs_',
            'material_instalasi_biaya',
            'pemasangan_pelatihan_biaya', 
            'ongkos_kirim_biaya',
            'addOngkirKomponen',
            'ongkir_komponen_'
        ];
        
        $missingElements = [];
        foreach ($requiredElements as $element) {
            if (strpos($viewContent, $element) === false) {
                $missingElements[] = $element;
            }
        }
        
        if (empty($missingElements)) {
            echo "   ✅ View contains all required form elements\n";
        } else {
            echo "   ❌ Missing view elements: " . implode(', ', $missingElements) . "\n";
        }
    } else {
        echo "   ❌ View file not found: $viewPath\n";
    }
    
    // Test 5: Create test data
    echo "\n5. Testing data creation...\n";
    
    DB::beginTransaction();
    
    try {
        // Find or create test customer
        $customer = \App\Models\Member::first();
        if (!$customer) {
            echo "   ❌ No customer found. Please create a customer first.\n";
            DB::rollback();
            exit(1);
        }
        
        // Find or create test outlet
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            echo "   ❌ No outlet found. Please create an outlet first.\n";
            DB::rollback();
            exit(1);
        }
        
        // Create test pre order
        $preOrder = new \App\Models\PreOrder();
        $preOrder->kode_preorder = 'TEST-' . date('YmdHis');
        $preOrder->outlet_id = $outlet->id_outlet;
        $preOrder->customer_id = $customer->id_member;
        $preOrder->tanggal = date('Y-m-d');
        $preOrder->subtotal = 1500000;
        $preOrder->diskon = 0;
        $preOrder->pajak = 0;
        $preOrder->total = 1500000;
        $preOrder->status = 'penawaran';
        $preOrder->save();
        
        // Create test item with additional costs
        $item = new \App\Models\PreOrderItem();
        $item->pre_order_id = $preOrder->id;
        $item->produk_id = null;
        $item->deskripsi = 'Test Product with Additional Costs';
        $item->qty = 1;
        $item->harga = 1000000;
        $item->subtotal = 1000000;
        $item->material_instalasi_biaya = 200000;
        $item->material_instalasi_satuan = 'lot';
        $item->material_instalasi_keterangan = 'Material untuk instalasi';
        $item->pemasangan_pelatihan_biaya = 150000;
        $item->pemasangan_pelatihan_satuan = 'orang';
        $item->pemasangan_pelatihan_keterangan = 'Pelatihan operator';
        $item->ongkos_kirim_biaya = 150000;
        $item->ongkos_kirim_satuan = 'unit';
        $item->ongkos_kirim_komponen = [
            ['nama' => 'Fuso', 'biaya' => 100000],
            ['nama' => 'Forklift', 'biaya' => 50000]
        ];
        $item->total_biaya_tambahan = 500000;
        $item->save();
        
        echo "   ✅ Test data created successfully\n";
        echo "   Pre Order ID: {$preOrder->id}\n";
        echo "   Item ID: {$item->id}\n";
        
        // Test model methods with real data
        echo "\n6. Testing calculations...\n";
        
        $calculatedTotal = $item->calculateTotalBiayaTambahan();
        $expectedTotal = 500000;
        
        if ($calculatedTotal == $expectedTotal) {
            echo "   ✅ calculateTotalBiayaTambahan works correctly: Rp " . number_format($calculatedTotal) . "\n";
        } else {
            echo "   ❌ calculateTotalBiayaTambahan incorrect. Expected: $expectedTotal, Got: $calculatedTotal\n";
        }
        
        $totalWithAdditional = $item->total_with_additional_costs;
        $expectedTotalWithAdditional = 1500000; // 1000000 + 500000
        
        if ($totalWithAdditional == $expectedTotalWithAdditional) {
            echo "   ✅ getTotalWithAdditionalCosts works correctly: Rp " . number_format($totalWithAdditional) . "\n";
        } else {
            echo "   ❌ getTotalWithAdditionalCosts incorrect. Expected: $expectedTotalWithAdditional, Got: $totalWithAdditional\n";
        }
        
        // Test ongkir komponen formatting
        $formattedKomponen = $item->formatted_ongkos_kirim_komponen;
        if (is_array($formattedKomponen) && count($formattedKomponen) == 2) {
            echo "   ✅ Ongkir komponen formatting works correctly\n";
            foreach ($formattedKomponen as $komponen) {
                echo "      - {$komponen['nama']}: {$komponen['formatted_biaya']}\n";
            }
        } else {
            echo "   ❌ Ongkir komponen formatting failed\n";
        }
        
        DB::rollback(); // Rollback test data
        
    } catch (Exception $e) {
        DB::rollback();
        echo "   ❌ Error creating test data: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\nNext steps:\n";
    echo "1. Open browser and go to Pre Order Management\n";
    echo "2. Click 'Buat Pre Order'\n";
    echo "3. Select a product\n";
    echo "4. Verify additional cost forms appear\n";
    echo "5. Test all form inputs and calculations\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}