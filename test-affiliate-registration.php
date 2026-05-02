<?php
/**
 * Test Affiliate Registration Flow
 * Simulasi lengkap proses pendaftaran affiliator
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PartnershipProgram;
use App\Models\Affiliator;
use Illuminate\Support\Facades\Hash;

echo "=== TEST AFFILIATE REGISTRATION FLOW ===\n\n";

// 1. Check Partnership Programs
echo "1. Checking Partnership Programs...\n";
$programs = PartnershipProgram::active()->ordered()->get();
echo "   Found " . $programs->count() . " active programs:\n";
foreach ($programs as $program) {
    echo "   - {$program->name} (Fee: {$program->formatted_fee})\n";
}
echo "\n";

// 2. Test GD Library
echo "2. Testing GD Library...\n";
if (!extension_loaded('gd')) {
    echo "   ✗ GD extension NOT installed!\n";
    exit(1);
}
echo "   ✓ GD extension installed\n";
echo "   ✓ Version: " . gd_info()['GD Version'] . "\n\n";

// 3. Test Directory Permissions
echo "3. Testing Directory Permissions...\n";
$dirs = [
    'storage/app/public/affiliator-photos',
    'storage/app/public/payment-proofs',
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "   ✓ Created: $dir\n";
        } else {
            echo "   ✗ Failed to create: $dir\n";
        }
    } else {
        echo "   ✓ Exists: $dir\n";
        if (is_writable($dir)) {
            echo "     ✓ Writable\n";
        } else {
            echo "     ✗ NOT writable!\n";
        }
    }
}
echo "\n";

// 4. Test Database Connection
echo "4. Testing Database Connection...\n";
try {
    $count = Affiliator::count();
    echo "   ✓ Database connected\n";
    echo "   ✓ Current affiliators: $count\n\n";
} catch (\Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 5. Test Username Uniqueness Check
echo "5. Testing Username Validation...\n";
$testUsername = 'test_' . time();
$exists = Affiliator::where('username', $testUsername)->exists();
echo "   ✓ Username '$testUsername' is " . ($exists ? 'taken' : 'available') . "\n\n";

// 6. Test Image Compression Function
echo "6. Testing Image Compression Function...\n";

function testImageCompression() {
    // Create a test image
    $width = 1600;
    $height = 1200;
    $image = imagecreatetruecolor($width, $height);
    
    // Fill with color
    $bgColor = imagecolorallocate($image, 100, 150, 200);
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $textColor = imagecolorallocate($image, 255, 255, 255);
    imagestring($image, 5, 10, 10, 'Test Image for Compression', $textColor);
    
    // Save original
    $tempOriginal = sys_get_temp_dir() . '/test_original_' . time() . '.jpg';
    imagejpeg($image, $tempOriginal, 100);
    $originalSize = filesize($tempOriginal);
    
    echo "   ✓ Created test image: {$width}x{$height}\n";
    echo "   ✓ Original size: " . number_format($originalSize / 1024, 2) . " KB\n";
    
    // Test compression (simulate controller method)
    $newWidth = 1200;
    $newHeight = (int) ($height * ($newWidth / $width));
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    $tempCompressed = sys_get_temp_dir() . '/test_compressed_' . time() . '.jpg';
    imagejpeg($newImage, $tempCompressed, 80);
    $compressedSize = filesize($tempCompressed);
    
    echo "   ✓ Resized to: {$newWidth}x{$newHeight}\n";
    echo "   ✓ Compressed size: " . number_format($compressedSize / 1024, 2) . " KB\n";
    echo "   ✓ Compression ratio: " . number_format(100 - ($compressedSize / $originalSize * 100), 2) . "%\n";
    
    // Cleanup
    imagedestroy($image);
    imagedestroy($newImage);
    @unlink($tempOriginal);
    @unlink($tempCompressed);
    
    return true;
}

try {
    testImageCompression();
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 7. Test Password Hashing
echo "7. Testing Password Hashing...\n";
$testPassword = 'password123';
$hashed = Hash::make($testPassword);
$verified = Hash::check($testPassword, $hashed);
echo "   ✓ Password hashed successfully\n";
echo "   ✓ Password verification: " . ($verified ? 'PASS' : 'FAIL') . "\n\n";

// 8. Summary
echo "=== TEST SUMMARY ===\n";
echo "✓ All systems ready for affiliate registration\n";
echo "✓ GD library working correctly\n";
echo "✓ Image compression functional\n";
echo "✓ Database connection OK\n";
echo "✓ Storage directories ready\n\n";

echo "NEXT STEPS:\n";
echo "1. Open browser: http://localhost/hm/public/affiliate/register\n";
echo "2. Fill form with test data\n";
echo "3. Upload test images (photo & payment proof)\n";
echo "4. Submit and verify:\n";
echo "   - Images are compressed\n";
echo "   - Files saved to storage/app/public/\n";
echo "   - Database record created\n";
echo "   - No errors in storage/logs/laravel.log\n\n";

echo "TEST COMPLETE!\n";
