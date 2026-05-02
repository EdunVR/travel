<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== URL GENERATION TEST ===\n\n";

try {
    // Test URL generation
    $productId = 12; // Using the ID from the error
    
    echo "1. Using route() helper:\n";
    $routeUrl = route('admin.inventaris.produk.add-stock', ['productId' => $productId]);
    echo "   {$routeUrl}\n\n";
    
    echo "2. Using url() helper:\n";
    $urlHelper = url("admin/inventaris/produk/{$productId}/add-stock");
    echo "   {$urlHelper}\n\n";
    
    echo "3. Base URL:\n";
    echo "   " . url('/') . "\n\n";
    
    echo "4. App URL from config:\n";
    echo "   " . config('app.url') . "\n\n";
    
    echo "5. Current request URL (if available):\n";
    if (isset($_SERVER['HTTP_HOST'])) {
        echo "   http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}\n";
    } else {
        echo "   Not available (CLI mode)\n";
    }
    
    echo "\n=== RECOMMENDATION ===\n";
    echo "Use this URL in JavaScript:\n";
    echo "{{ url('admin/inventaris/produk') }}/\${this.form.id}/add-stock\n";
    echo "\nOr use route helper:\n";
    echo "{{ route('admin.inventaris.produk.add-stock', ['productId' => '__ID__']) }}\n";
    echo "Then replace __ID__ with actual ID in JavaScript\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}