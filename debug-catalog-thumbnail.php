<?php
// Debug script untuk cek thumbnail data di katalog
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== DEBUG CATALOG THUMBNAIL DATA ===\n\n";

$packages = TravelPackage::with('outlet')
    ->whereNotIn('status', ['draft', 'cancelled'])
    ->upcoming()
    ->take(3)
    ->get();

foreach ($packages as $pkg) {
    echo "Package ID: {$pkg->id}\n";
    echo "Package Name: {$pkg->package_name}\n";
    echo "Image Path: " . ($pkg->image_path ?? 'NULL') . "\n";
    echo "Image Path Type: " . gettype($pkg->image_path) . "\n";
    
    if ($pkg->image_path) {
        $fullPath = storage_path('app/public/' . $pkg->image_path);
        echo "Full Path: {$fullPath}\n";
        echo "File Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    }
    
    echo "Thumbnail Crop Settings: " . json_encode($pkg->thumbnail_crop_settings) . "\n";
    echo "Thumbnail Crop Type: " . gettype($pkg->thumbnail_crop_settings) . "\n";
    
    if (is_array($pkg->thumbnail_crop_settings)) {
        echo "Is Array: YES\n";
        echo "Has Width: " . (isset($pkg->thumbnail_crop_settings['width']) ? 'YES' : 'NO') . "\n";
        if (isset($pkg->thumbnail_crop_settings['width'])) {
            echo "Width Value: {$pkg->thumbnail_crop_settings['width']}\n";
        }
    }
    
    echo "Departure Date: " . ($pkg->departure_date ? $pkg->departure_date->format('Y-m-d') : 'NULL') . "\n";
    echo "Outlet: " . ($pkg->outlet ? $pkg->outlet->nama_outlet : 'NULL') . "\n";
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "Total packages found: " . $packages->count() . "\n";
