<?php

require_once 'vendor/autoload.php';

echo "=== DEBUG OUTLET RELATIONSHIP ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking raw SQL join...\n";
    $permintaan = DB::select('SELECT pb.id, pb.nomor_permintaan, pb.outlet_id, o.nama_outlet 
                              FROM permintaan_barang pb 
                              LEFT JOIN outlets o ON pb.outlet_id = o.id_outlet 
                              LIMIT 3');
    
    foreach ($permintaan as $p) {
        echo "   ID: {$p->id}, Nomor: {$p->nomor_permintaan}, Outlet ID: {$p->outlet_id}, Outlet Name: {$p->nama_outlet}\n";
    }
    
    echo "\n2. Testing Eloquent relationship...\n";
    $permintaanModel = App\Models\PermintaanBarang::with('outlet')->first();
    if ($permintaanModel) {
        echo "   Permintaan: {$permintaanModel->nomor_permintaan}\n";
        echo "   Outlet ID: {$permintaanModel->outlet_id}\n";
        echo "   Outlet Object: " . ($permintaanModel->outlet ? $permintaanModel->outlet->nama_outlet : 'null') . "\n";
        echo "   Outlet Relationship: " . ($permintaanModel->outlet ? 'loaded' : 'not loaded') . "\n";
    }
    
    echo "\n3. Testing getData method directly...\n";
    $controller = new App\Http\Controllers\PermintaanBarangController();
    $request = new Illuminate\Http\Request();
    $request->merge(['per_page' => 2]);
    
    $response = $controller->getData($request);
    $data = json_decode($response->getContent(), true);
    
    foreach ($data['data'] as $item) {
        echo "   Permintaan: {$item['nomor_permintaan']}\n";
        echo "   Outlet ID: " . ($item['outlet_id'] ?? 'null') . "\n";
        echo "   Outlet Data: " . json_encode($item['outlet'] ?? null) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";