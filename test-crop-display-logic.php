<?php
// Test script untuk memverifikasi logic crop display

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== TEST CROP DISPLAY LOGIC ===\n\n";

// Get package with crop settings
$package = TravelPackage::whereNotNull('thumbnail_crop_settings')
    ->whereNotNull('image_path')
    ->first();

if (!$package) {
    echo "No package found with crop settings!\n";
    exit;
}

echo "Testing Package: {$package->package_name}\n";
echo "Image Path: {$package->image_path}\n\n";

$cropData = $package->thumbnail_crop_settings;

if (!$cropData || !is_array($cropData)) {
    echo "Invalid crop data!\n";
    exit;
}

echo "Crop Data:\n";
print_r($cropData);
echo "\n";

// Simulate the calculation
$cropX = $cropData['x'] ?? 0;
$cropY = $cropData['y'] ?? 0;
$cropWidth = $cropData['width'] ?? 1;
$cropHeight = $cropData['height'] ?? 1;
$rotate = $cropData['rotate'] ?? 0;
$imgScaleX = $cropData['scaleX'] ?? 1;
$imgScaleY = $cropData['scaleY'] ?? 1;

echo "Extracted Values:\n";
echo "  Crop X: {$cropX}px\n";
echo "  Crop Y: {$cropY}px\n";
echo "  Crop Width: {$cropWidth}px\n";
echo "  Crop Height: {$cropHeight}px\n";
echo "  Rotate: {$rotate}deg\n";
echo "  Scale X: {$imgScaleX}\n";
echo "  Scale Y: {$imgScaleY}\n\n";

// Calculate scale factor
$scaleToFitX = 100 / $cropWidth * 100;
$scaleToFitY = 100 / $cropHeight * 100;
$scaleFactor = max($scaleToFitX, $scaleToFitY);

echo "Scale Calculation:\n";
echo "  Scale to Fit X: {$scaleToFitX}%\n";
echo "  Scale to Fit Y: {$scaleToFitY}%\n";
echo "  Final Scale Factor: {$scaleFactor}%\n\n";

// Calculate offset
$offsetX = -$cropX * ($scaleFactor / 100);
$offsetY = -$cropY * ($scaleFactor / 100);

echo "Offset Calculation:\n";
echo "  Offset X: {$offsetX}%\n";
echo "  Offset Y: {$offsetY}%\n\n";

// Transform origin
$originX = $cropX + $cropWidth/2;
$originY = $cropY + $cropHeight/2;

echo "Transform Origin:\n";
echo "  Origin X: {$originX}px\n";
echo "  Origin Y: {$originY}px\n\n";

echo "=== GENERATED CSS ===\n\n";
echo "position: absolute;\n";
echo "left: {$offsetX}%;\n";
echo "top: {$offsetY}%;\n";
echo "width: {$scaleFactor}%;\n";
echo "height: {$scaleFactor}%;\n";
echo "transform: rotate({$rotate}deg) scaleX({$imgScaleX}) scaleY({$imgScaleY});\n";
echo "transform-origin: {$originX}px {$originY}px;\n\n";

echo "=== EXPLANATION ===\n\n";
echo "Container: 100% x 100% (e.g., 400px x 192px for h-48)\n";
echo "Crop Area: {$cropWidth}px x {$cropHeight}px\n";
echo "Scale Factor: Makes crop area fill container\n";
echo "Offset: Positions image so crop area is at top-left of container\n";
echo "Transform Origin: Center of crop area for rotation\n";
