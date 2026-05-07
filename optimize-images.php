<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ImageOptimizationService;
use App\Models\TravelPackage;

echo "=== PACKAGE IMAGE OPTIMIZATION ===\n\n";

$service = new ImageOptimizationService();

// Get all packages with images
$packages = TravelPackage::whereNotNull('image_path')->get();

echo "Found " . $packages->count() . " packages with images\n\n";

$optimized = 0;
$skipped = 0;
$failed = 0;

foreach ($packages as $package) {
    echo "Package: {$package->package_name}\n";
    echo "Image: {$package->image_path}\n";
    
    // Check if needs optimization
    if ($service->needsOptimization($package->image_path)) {
        echo "Status: Needs optimization\n";
        
        $result = $service->optimizeImage($package->image_path, [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 75
        ]);
        
        if ($result) {
            echo "Result: ✅ Optimized\n";
            $optimized++;
        } else {
            echo "Result: ❌ Failed\n";
            $failed++;
        }
    } else {
        echo "Status: Already optimized\n";
        $skipped++;
    }
    
    echo "---\n\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total Packages: " . $packages->count() . "\n";
echo "Optimized: {$optimized}\n";
echo "Skipped (already optimized): {$skipped}\n";
echo "Failed: {$failed}\n";

if ($optimized > 0) {
    echo "\n✅ Optimization complete!\n";
    echo "Images will now load faster on slow connections.\n";
} else {
    echo "\n✅ All images are already optimized!\n";
}
