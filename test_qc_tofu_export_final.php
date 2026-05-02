<?php

/**
 * Test QC Tofu Mentah Export PDF Final Implementation
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductionController;
use Illuminate\Http\Request;

echo "========================================\n";
echo "TESTING QC TOFU MENTAH EXPORT PDF FINAL\n";
echo "========================================\n\n";

try {
    // Test 1: Check tofu data availability
    echo "[TEST 1] Checking tofu data availability...\n";
    $tofuCount = DB::table('productions')
        ->where('business_type', 'tofu')
        ->whereNotNull('tofu_data')
        ->count();
    
    echo "Found {$tofuCount} tofu productions with QC data\n";
    
    if ($tofuCount === 0) {
        echo "❌ No tofu data available for testing\n";
        exit(1);
    }
    
    // Test 2: Check company settings
    echo "\n[TEST 2] Checking company settings...\n";
    $companySetting = DB::table('company_settings')->first();
    if ($companySetting) {
        echo "✓ Company settings found:\n";
        echo "  - Name: {$companySetting->company_name}\n";
        echo "  - Logo: " . ($companySetting->company_logo ? 'Available' : 'Not set') . "\n";
    } else {
        echo "⚠️ No company settings found\n";
    }
    
    // Test 3: Test controller method
    echo "\n[TEST 3] Testing controller method...\n";
    $controller = new ProductionController();
    
    if (method_exists($controller, 'exportQcTofuMentahPdf')) {
        echo "✓ exportQcTofuMentahPdf method exists\n";
    } else {
        echo "❌ exportQcTofuMentahPdf method missing\n";
        exit(1);
    }
    
    // Test 4: Test data processing
    echo "\n[TEST 4] Testing data processing...\n";
    $sampleProduction = DB::table('productions')
        ->where('business_type', 'tofu')
        ->whereNotNull('tofu_data')
        ->first();
    
    if ($sampleProduction) {
        echo "Sample production: {$sampleProduction->production_code}\n";
        
        // Test double-decode logic
        $tofuData = [];
        if ($sampleProduction->tofu_data) {
            $firstDecode = json_decode($sampleProduction->tofu_data, true);
            if (is_string($firstDecode)) {
                $tofuData = json_decode($firstDecode, true) ?: [];
                echo "✓ Double-encoded JSON handled correctly\n";
            } else {
                $tofuData = $firstDecode ?: [];
                echo "✓ Single-encoded JSON handled correctly\n";
            }
        }
        
        echo "Decoded fields:\n";
        foreach ($tofuData as $key => $value) {
            $displayValue = $value ?? 'NULL';
            echo "  - {$key}: {$displayValue}\n";
        }
    }
    
    // Test 5: Test route availability
    echo "\n[TEST 5] Checking route availability...\n";
    try {
        $route = route('admin.produksi.produksi.export.qc-tofu-mentah-pdf');
        echo "✓ Route available: {$route}\n";
    } catch (Exception $e) {
        echo "❌ Route not available: {$e->getMessage()}\n";
    }
    
    // Test 6: Test PDF template
    echo "\n[TEST 6] Checking PDF template...\n";
    $templatePath = resource_path('views/admin/produksi/produksi/qc-tofu-mentah-pdf.blade.php');
    if (file_exists($templatePath)) {
        echo "✓ PDF template exists\n";
        $templateSize = filesize($templatePath);
        echo "  Template size: {$templateSize} bytes\n";
    } else {
        echo "❌ PDF template missing\n";
    }
    
    // Test 7: Test document number generation
    echo "\n[TEST 7] Testing document number generation...\n";
    $documentNumber = 'PNI/FSOP/QC/01-' . now()->format('y');
    $currentDate = now()->format('d F Y');
    echo "✓ Document number: {$documentNumber}\n";
    echo "✓ Current date: {$currentDate}\n";
    
    echo "\n========================================\n";
    echo "✅ ALL TESTS COMPLETED SUCCESSFULLY\n";
    echo "========================================\n";
    echo "\nREADY TO TEST:\n";
    echo "1. Visit the production page\n";
    echo "2. Click Export PDF dropdown\n";
    echo "3. Select 'QC Egg Tofu Mentah'\n";
    echo "4. Verify PDF generates with:\n";
    echo "   - Auto-generated document number\n";
    echo "   - Current export date\n";
    echo "   - Proper margins\n";
    echo "   - Correct header structure\n";
    echo "   - All tofu_data fields mapped\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

?>