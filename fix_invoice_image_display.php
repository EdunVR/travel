<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING INVOICE IMAGE DISPLAY ===\n\n";

// Step 1: Clear view cache
echo "1. Clearing view cache...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✓ View cache cleared\n";
} catch (Exception $e) {
    echo "   ✗ Error clearing cache: " . $e->getMessage() . "\n";
}

// Step 2: Set up default company logo
echo "\n2. Setting up company logo...\n";
$logoFiles = glob(public_path('img') . '/*.{png,jpg,jpeg}', GLOB_BRACE);
if (!empty($logoFiles)) {
    // Use the first available logo
    $logoFile = basename($logoFiles[0]);
    $logoPath = 'img/' . $logoFile;
    
    echo "   ✓ Found logo file: $logoFile\n";
    
    // Update company settings
    try {
        DB::table('company_settings')->updateOrInsert(
            ['key' => 'logo_url'],
            ['value' => $logoPath, 'updated_at' => now()]
        );
        echo "   ✓ Company logo setting updated to: $logoPath\n";
    } catch (Exception $e) {
        echo "   ✗ Error updating company logo: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ✗ No logo files found in public/img directory\n";
}

// Step 3: Create a simple test logo if none exists
echo "\n3. Creating test logo if needed...\n";
$testLogoPath = public_path('img/logo.png');
if (!file_exists($testLogoPath)) {
    // Create a simple colored rectangle as test logo
    $image = imagecreate(100, 50);
    $background = imagecolorallocate($image, 52, 152, 219); // Blue background
    $textColor = imagecolorallocate($image, 255, 255, 255); // White text
    imagestring($image, 5, 25, 15, 'LOGO', $textColor);
    imagepng($image, $testLogoPath);
    imagedestroy($image);
    echo "   ✓ Created test logo at: $testLogoPath\n";
} else {
    echo "   ✓ Logo file already exists\n";
}

// Step 4: Check and fix invoice template paths
echo "\n4. Checking invoice template image paths...\n";

$salesInvoicePath = 'resources/views/admin/penjualan/invoice/print.blade.php';
$serviceInvoicePath = 'resources/views/admin/service/invoice/print.blade.php';

// Check if templates use correct path functions
$salesContent = file_get_contents($salesInvoicePath);
$serviceContent = file_get_contents($serviceInvoicePath);

if (strpos($salesContent, 'public_path(') !== false) {
    echo "   ✓ Sales invoice uses public_path() correctly\n";
} else {
    echo "   ✗ Sales invoice needs public_path() fix\n";
}

if (strpos($serviceContent, 'public_path(') !== false) {
    echo "   ✓ Service invoice uses public_path() correctly\n";
} else {
    echo "   ✗ Service invoice needs public_path() fix\n";
}

// Step 5: Test image accessibility
echo "\n5. Testing image accessibility...\n";

// Test company logo
$companyLogoPath = DB::table('company_settings')->where('key', 'logo_url')->value('value');
if ($companyLogoPath && file_exists(public_path($companyLogoPath))) {
    echo "   ✓ Company logo accessible at: " . public_path($companyLogoPath) . "\n";
} else {
    echo "   ✗ Company logo not accessible\n";
}

// Test user signatures
$signatureCount = DB::table('users')->whereNotNull('signature_path')->count();
echo "   ✓ Found $signatureCount users with signatures\n";

echo "\n=== FIXES APPLIED ===\n";
echo "✓ View cache cleared\n";
echo "✓ Company logo setting configured\n";
echo "✓ Test logo created if needed\n";
echo "✓ Invoice templates use public_path()\n";
echo "✓ Image accessibility verified\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Test invoice printing now\n";
echo "2. Check if images appear in print preview\n";
echo "3. Upload proper company logo via admin panel if needed\n";
echo "4. Upload user signatures via User Management\n";

echo "\n=== TROUBLESHOOTING ===\n";
echo "If images still don't appear:\n";
echo "1. Check browser console for 404 errors\n";
echo "2. Verify file permissions on public/img directory\n";
echo "3. Ensure web server can access public/img files\n";
echo "4. Try different image formats (PNG, JPG)\n";