<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CompanySetting;

echo "=== DEBUGGING INVOICE IMAGE ISSUES ===\n\n";

// Test 1: Check syntax errors in files
echo "1. Checking for syntax errors in invoice files...\n";

$salesInvoicePath = 'resources/views/admin/penjualan/invoice/print.blade.php';
$serviceInvoicePath = 'resources/views/admin/service/invoice/print.blade.php';

// Check for unclosed brackets or quotes
function checkSyntaxIssues($filePath) {
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $issues = [];
    
    foreach ($lines as $lineNum => $line) {
        // Check for unclosed quotes in img src
        if (strpos($line, 'src="{{') !== false && strpos($line, '}}"') === false) {
            $issues[] = "Line " . ($lineNum + 1) . ": Possible unclosed quote in img src";
        }
        
        // Check for mismatched brackets
        $openBrackets = substr_count($line, '{{');
        $closeBrackets = substr_count($line, '}}');
        if ($openBrackets !== $closeBrackets) {
            $issues[] = "Line " . ($lineNum + 1) . ": Mismatched brackets - Open: $openBrackets, Close: $closeBrackets";
        }
    }
    
    return $issues;
}

$salesIssues = checkSyntaxIssues($salesInvoicePath);
$serviceIssues = checkSyntaxIssues($serviceInvoicePath);

if (empty($salesIssues)) {
    echo "   ✓ Sales invoice: No syntax issues found\n";
} else {
    echo "   ✗ Sales invoice issues:\n";
    foreach ($salesIssues as $issue) {
        echo "     - $issue\n";
    }
}

if (empty($serviceIssues)) {
    echo "   ✓ Service invoice: No syntax issues found\n";
} else {
    echo "   ✗ Service invoice issues:\n";
    foreach ($serviceIssues as $issue) {
        echo "     - $issue\n";
    }
}

// Test 2: Check company settings
echo "\n2. Checking company settings...\n";
try {
    // Check if we can get company settings
    $companySettings = [];
    
    // Try to get logo setting
    $logoPath = 'img/logo.png'; // Default path
    if (file_exists(public_path($logoPath))) {
        echo "   ✓ Default logo file exists at: " . public_path($logoPath) . "\n";
        $companySettings['logo_url'] = $logoPath;
    } else {
        echo "   ✗ Default logo file not found at: " . public_path($logoPath) . "\n";
    }
    
    // Check if logo directory exists
    $logoDir = public_path('img');
    if (is_dir($logoDir)) {
        echo "   ✓ Logo directory exists: $logoDir\n";
        $logoFiles = glob($logoDir . '/*.{png,jpg,jpeg,gif}', GLOB_BRACE);
        if (!empty($logoFiles)) {
            echo "   ✓ Found logo files:\n";
            foreach ($logoFiles as $file) {
                echo "     - " . basename($file) . "\n";
            }
        } else {
            echo "   ✗ No logo files found in directory\n";
        }
    } else {
        echo "   ✗ Logo directory does not exist: $logoDir\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error checking company settings: " . $e->getMessage() . "\n";
}

// Test 3: Check user signatures
echo "\n3. Checking user signatures...\n";
try {
    $users = User::whereNotNull('signature_path')->get();
    
    if ($users->count() > 0) {
        echo "   ✓ Found " . $users->count() . " users with signatures\n";
        foreach ($users as $user) {
            $signaturePath = public_path($user->signature_path);
            if (file_exists($signaturePath)) {
                echo "     ✓ {$user->name}: Signature file exists\n";
            } else {
                echo "     ✗ {$user->name}: Signature file missing at $signaturePath\n";
            }
        }
    } else {
        echo "   ✗ No users have signature files uploaded\n";
    }
    
    // Check signature directory
    $signatureDir = public_path('img/signatures');
    if (is_dir($signatureDir)) {
        echo "   ✓ Signature directory exists: $signatureDir\n";
        $signatureFiles = glob($signatureDir . '/*.{png,jpg,jpeg,gif}', GLOB_BRACE);
        if (!empty($signatureFiles)) {
            echo "   ✓ Found " . count($signatureFiles) . " signature files\n";
        } else {
            echo "   ✗ No signature files found in directory\n";
        }
    } else {
        echo "   ✗ Signature directory does not exist: $signatureDir\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error checking user signatures: " . $e->getMessage() . "\n";
}

// Test 4: Check file paths in invoice templates
echo "\n4. Checking image path usage in templates...\n";

$salesContent = file_get_contents($salesInvoicePath);
$serviceContent = file_get_contents($serviceInvoicePath);

// Check if using public_path
if (strpos($salesContent, 'public_path(') !== false) {
    echo "   ✓ Sales invoice uses public_path() for images\n";
} else {
    echo "   ✗ Sales invoice not using public_path() for images\n";
}

if (strpos($serviceContent, 'public_path(') !== false) {
    echo "   ✓ Service invoice uses public_path() for images\n";
} else {
    echo "   ✗ Service invoice not using public_path() for images\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Clear view cache: php artisan view:clear\n";
echo "2. Ensure logo file exists in public/img/ directory\n";
echo "3. Upload user signatures via User Management\n";
echo "4. Test with actual invoice data\n";
echo "5. Check browser console for image loading errors\n";

echo "\n=== QUICK FIXES ===\n";
echo "1. Create default logo: copy any logo to public/img/logo.png\n";
echo "2. Create signature directory: mkdir public/img/signatures\n";
echo "3. Set proper permissions: chmod 755 public/img/signatures\n";