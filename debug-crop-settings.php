<?php
// Debug script untuk mengecek crop settings di database

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== DEBUG CROP SETTINGS ===\n\n";

$packages = TravelPackage::whereNotNull('image_path')->get();

echo "Total packages dengan gambar: " . $packages->count() . "\n\n";

foreach ($packages as $pkg) {
    echo "Package ID: {$pkg->id}\n";
    echo "Package Name: {$pkg->package_name}\n";
    echo "Image Path: {$pkg->image_path}\n";
    echo "Thumbnail Crop Settings: ";
    
    if ($pkg->thumbnail_crop_settings) {
        echo "\n";
        print_r($pkg->thumbnail_crop_settings);
        
        // Check if it's valid crop data
        if (is_array($pkg->thumbnail_crop_settings)) {
            $hasValidData = isset($pkg->thumbnail_crop_settings['x']) && 
                           isset($pkg->thumbnail_crop_settings['width']) && 
                           $pkg->thumbnail_crop_settings['width'] > 0;
            echo "Has Valid Crop Data: " . ($hasValidData ? "YES" : "NO") . "\n";
            
            if ($hasValidData) {
                echo "Crop Data:\n";
                echo "  - X: {$pkg->thumbnail_crop_settings['x']}\n";
                echo "  - Y: {$pkg->thumbnail_crop_settings['y']}\n";
                echo "  - Width: {$pkg->thumbnail_crop_settings['width']}\n";
                echo "  - Height: {$pkg->thumbnail_crop_settings['height']}\n";
                echo "  - Rotate: {$pkg->thumbnail_crop_settings['rotate']}\n";
            }
        } else {
            echo "ERROR: Not an array!\n";
        }
    } else {
        echo "NULL (no crop settings)\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "\n=== CHECKING HOMEPAGE DISPLAY LOGIC ===\n\n";

// Simulate homepage logic
$testPackage = $packages->first();
if ($testPackage) {
    echo "Testing with Package: {$testPackage->package_name}\n\n";
    
    $cropData = $testPackage->thumbnail_crop_settings ?? null;
    echo "Crop Data Retrieved: ";
    print_r($cropData);
    echo "\n";
    
    $shouldShowCrop = $cropData && is_array($cropData) && isset($cropData['x']) && isset($cropData['width']) && $cropData['width'] > 0;
    echo "Should Show Crop: " . ($shouldShowCrop ? "YES" : "NO") . "\n";
    
    if ($shouldShowCrop) {
        echo "\nGenerated CSS:\n";
        echo "position: absolute;\n";
        echo "left: " . (-($cropData['x'] ?? 0)) . "px;\n";
        echo "top: " . (-($cropData['y'] ?? 0)) . "px;\n";
        echo "width: " . ($cropData['width'] ?? 'auto') . "px;\n";
        echo "height: " . ($cropData['height'] ?? 'auto') . "px;\n";
        echo "transform: rotate(" . ($cropData['rotate'] ?? 0) . "deg) scaleX(" . ($cropData['scaleX'] ?? 1) . ") scaleY(" . ($cropData['scaleY'] ?? 1) . ");\n";
    }
}
