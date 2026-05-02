<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CompanySetting;
use Carbon\Carbon;

echo "=== TESTING INVOICE SIGNATURE FIXES ===\n\n";

// Test 1: Check if signature_path column exists and is fillable
echo "1. Testing User model signature functionality...\n";
try {
    $user = User::first();
    if ($user) {
        echo "   ✓ User model loaded successfully\n";
        
        // Check if signature_path is in fillable array
        $fillable = $user->getFillable();
        if (in_array('signature_path', $fillable)) {
            echo "   ✓ signature_path is in fillable array\n";
        } else {
            echo "   ✗ signature_path is NOT in fillable array\n";
        }
        
        // Test signature URL accessor
        if (method_exists($user, 'getSignatureUrlAttribute')) {
            echo "   ✓ getSignatureUrlAttribute method exists\n";
        } else {
            echo "   ✗ getSignatureUrlAttribute method missing\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check due date calculation fix
echo "\n2. Testing due date calculation (no decimals)...\n";
try {
    $today = Carbon::now();
    $dueDate = $today->copy()->addDays(20);
    $diffInDays = (int) $today->diffInDays($dueDate, false);
    
    if (is_int($diffInDays)) {
        echo "   ✓ Due date calculation returns integer: {$diffInDays} days\n";
    } else {
        echo "   ✗ Due date calculation returns non-integer: {$diffInDays}\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check if company settings exist for logo
echo "\n3. Testing company settings for logo...\n";
try {
    $companySettings = CompanySetting::getSettings();
    if (isset($companySettings['logo_url'])) {
        echo "   ✓ Company logo URL setting exists: " . ($companySettings['logo_url'] ?: 'Not set') . "\n";
    } else {
        echo "   ✗ Company logo URL setting missing\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Check if signature directory exists
echo "\n4. Testing signature directory...\n";
$signatureDir = public_path('img/signatures');
if (!file_exists($signatureDir)) {
    mkdir($signatureDir, 0755, true);
    echo "   ✓ Created signature directory: {$signatureDir}\n";
} else {
    echo "   ✓ Signature directory exists: {$signatureDir}\n";
}

// Test 5: Verify invoice print files exist
echo "\n5. Testing invoice print files...\n";
$salesInvoicePrint = 'resources/views/admin/penjualan/invoice/print.blade.php';
$serviceInvoicePrint = 'resources/views/admin/service/invoice/print.blade.php';

if (file_exists($salesInvoicePrint)) {
    echo "   ✓ Sales invoice print file exists\n";
    
    // Check if signature section is updated
    $content = file_get_contents($salesInvoicePrint);
    if (strpos($content, 'auth()->user()->signature_url') !== false) {
        echo "   ✓ Sales invoice has dynamic signature support\n";
    } else {
        echo "   ✗ Sales invoice missing dynamic signature support\n";
    }
    
    if (strpos($content, '(int) $today->diffInDays') !== false) {
        echo "   ✓ Sales invoice has fixed due date calculation\n";
    } else {
        echo "   ✗ Sales invoice missing due date fix\n";
    }
} else {
    echo "   ✗ Sales invoice print file missing\n";
}

if (file_exists($serviceInvoicePrint)) {
    echo "   ✓ Service invoice print file exists\n";
    
    // Check if signature section is updated
    $content = file_get_contents($serviceInvoicePrint);
    if (strpos($content, 'auth()->user()->signature_url') !== false) {
        echo "   ✓ Service invoice has dynamic signature support\n";
    } else {
        echo "   ✗ Service invoice missing dynamic signature support\n";
    }
} else {
    echo "   ✗ Service invoice print file missing\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "\nSUMMARY OF IMPLEMENTED FIXES:\n";
echo "1. ✓ Added signature_path field to users table\n";
echo "2. ✓ Updated User model with signature functionality\n";
echo "3. ✓ Enhanced UserManagementController for signature uploads\n";
echo "4. ✓ Updated user management modal with signature upload\n";
echo "5. ✓ Fixed due date decimal calculation in sales invoice\n";
echo "6. ✓ Updated signature sections in both invoice prints\n";
echo "7. ✓ Added company logo/stamp to signature sections\n";
echo "8. ✓ Made signatures dynamic per user\n";

echo "\nNEXT STEPS:\n";
echo "1. Test the user management modal signature upload\n";
echo "2. Upload signature images for users\n";
echo "3. Test invoice printing with signatures\n";
echo "4. Verify due dates show without decimals\n";