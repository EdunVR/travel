<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InterOutletSaleItem;
use App\Http\Controllers\MarginReportController;
use Illuminate\Http\Request;

echo "=== TEST SUFFICIENT STOCK ITEM ===\n\n";

// Test item with sufficient stock (ID 24, qty 2000)
$item = InterOutletSaleItem::with(['interOutletSale', 'produk'])->find(24);

if ($item) {
    echo "Testing item ID: {$item->id}\n";
    echo "Produk: {$item->produk->nama_produk}\n";
    echo "Quantity: {$item->kuantitas}\n";
    echo "Tanggal: {$item->interOutletSale->tanggal}\n";
    echo "Data HPP: " . json_encode($item->data_hpp) . "\n\n";
    
    // Create controller instance
    $controller = new MarginReportController();
    
    // Use reflection to access private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('calculateHppFromStoredData');
    $method->setAccessible(true);
    
    // Call the method
    $result = $method->invoke($controller, $item);
    
    echo "Result from calculateHppFromStoredData:\n";
    echo "HPP per unit: " . $result['hpp_per_unit'] . "\n";
    echo "Can calculate: " . ($result['can_calculate'] ? 'TRUE' : 'FALSE') . "\n";
    echo "Status: " . $result['status'] . "\n";
    echo "Message: " . $result['message'] . "\n\n";
    
    // Test full controller for this date
    echo "Testing full controller getData method...\n";
    $request = new Request([
        'start_date' => '2026-01-21',
        'end_date' => '2026-01-21'
    ]);
    
    $response = $controller->getData($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        $marginData = $responseData['data'];
        $interOutletItems = array_filter($marginData, function($item) {
            return $item['source'] === 'inter_outlet';
        });
        
        echo "Found " . count($interOutletItems) . " inter-outlet items on 2026-01-21\n";
        
        foreach ($interOutletItems as $marginItem) {
            if (strpos($marginItem['produk'], 'Tofu Spesial Udang 120g') !== false && $marginItem['qty'] == 2000) {
                echo "Found target item in margin data:\n";
                echo "HPP: " . $marginItem['hpp'] . "\n";
                echo "Profit: " . ($marginItem['profit'] !== null ? $marginItem['profit'] : 'NULL') . "\n";
                echo "Margin: " . ($marginItem['margin_pct'] !== null ? $marginItem['margin_pct'] . '%' : 'NULL') . "\n";
                echo "Status: " . $marginItem['hpp_status'] . "\n";
                echo "Message: " . $marginItem['hpp_message'] . "\n";
                break;
            }
        }
    } else {
        echo "Controller error: " . $responseData['message'] . "\n";
    }
    
} else {
    echo "Item not found\n";
}

echo "\n=== SELESAI ===\n";