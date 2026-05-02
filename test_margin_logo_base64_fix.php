<?php

require_once 'vendor/autoload.php';

// Test script untuk memverifikasi base64 logo fix
echo "=== TESTING MARGIN REPORT LOGO BASE64 FIX ===\n\n";

// Test 1: Check if controller has base64 conversion logic
echo "1. Testing controller base64 logic...\n";
$controllerFile = 'app/Http/Controllers/MarginReportController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for base64 conversion
    if (strpos($content, 'logo_base64') !== false) {
        echo "   ✓ Base64 conversion logic added\n";
    } else {
        echo "   ✗ Base64 conversion logic not found\n";
    }
    
    // Check for file path extraction
    if (strpos($content, 'storage_path(\'app/public/\'') !== false) {
        echo "   ✓ File path extraction logic found\n";
    } else {
        echo "   ✗ File path extraction logic not found\n";
    }
    
    // Check for error handling
    if (strpos($content, 'try {') !== false && strpos($content, 'catch') !== false) {
        echo "   ✓ Error handling added\n";
    } else {
        echo "   ✗ Error handling not found\n";
    }
    
} else {
    echo "   ✗ Controller file not found\n";
}

echo "\n";

// Test 2: Check template base64 support
echo "2. Testing template base64 support...\n";
$templateFile = 'resources/views/admin/penjualan/margin/pdf.blade.php';
if (file_exists($templateFile)) {
    $content = file_get_contents($templateFile);
    
    // Check for base64 condition
    if (strpos($content, 'logo_base64') !== false) {
        echo "   ✓ Base64 logo condition found\n";
    } else {
        echo "   ✗ Base64 logo condition not found\n";
    }
    
    // Check for fallback logic
    if (strpos($content, '@elseif') !== false) {
        echo "   ✓ Fallback logic implemented\n";
    } else {
        echo "   ✗ Fallback logic not found\n";
    }
    
    // Check for HTTP conversion
    if (strpos($content, 'str_replace(\'https://\', \'http://\'') !== false) {
        echo "   ✓ HTTPS to HTTP conversion found\n";
    } else {
        echo "   ✗ HTTPS to HTTP conversion not found\n";
    }
    
} else {
    echo "   ✗ Template file not found\n";
}

echo "\n";

// Test 3: Simulate logo file processing
echo "3. Testing logo file processing simulation...\n";
$testLogoUrl = 'https://poshan.my.id/tofu/storage/company/logos/NBxnm48a2z6KqelErbPvjCQG93rkCEmvSrEfVox6.png';

// Extract relative path
if (str_contains($testLogoUrl, '/storage/')) {
    $relativePath = substr($testLogoUrl, strpos($testLogoUrl, '/storage/') + 9);
    $expectedPath = 'storage/app/public/' . $relativePath;
    
    echo "   ✓ URL parsing works\n";
    echo "   → Original URL: $testLogoUrl\n";
    echo "   → Relative path: $relativePath\n";
    echo "   → Expected file path: $expectedPath\n";
    
    // Check if file would exist (simulation)
    if (file_exists($expectedPath)) {
        echo "   ✓ Logo file exists at expected path\n";
        $fileSize = filesize($expectedPath);
        echo "   → File size: $fileSize bytes\n";
        
        // Test base64 conversion
        $content = file_get_contents($expectedPath);
        $mimeType = mime_content_type($expectedPath);
        $base64 = base64_encode($content);
        $dataUri = "data:$mimeType;base64,$base64";
        
        echo "   ✓ Base64 conversion successful\n";
        echo "   → MIME type: $mimeType\n";
        echo "   → Base64 length: " . strlen($base64) . " characters\n";
        echo "   → Data URI length: " . strlen($dataUri) . " characters\n";
        
    } else {
        echo "   ⚠ Logo file not found at expected path\n";
        echo "   → You may need to download the logo file from the URL\n";
        echo "   → Or the file path structure is different\n";
    }
} else {
    echo "   ✗ URL doesn't contain /storage/ path\n";
}

echo "\n";

// Test 4: Check log output expectations
echo "4. Expected log output after fix...\n";
echo "   Look for these entries in Laravel logs:\n";
echo "   → 'Logo converted to base64' (success)\n";
echo "   → 'Logo file not found' (file missing)\n";
echo "   → 'Error processing logo for PDF' (processing error)\n";
echo "   → 'logo_base64_available' => 'YES' (in company settings log)\n";

echo "\n";

// Summary
echo "=== EXPECTED RESULTS ===\n";
echo "After this fix, the logo should appear because:\n\n";

echo "1. ✓ Base64 Encoding (Most Reliable)\n";
echo "   - Converts logo file to base64 data URI\n";
echo "   - Embeds directly in HTML (no external requests)\n";
echo "   - Works with all PDF generators\n\n";

echo "2. ✓ HTTPS to HTTP Fallback\n";
echo "   - Some PDF generators can't handle HTTPS\n";
echo "   - Automatically converts to HTTP URL\n";
echo "   - Maintains external URL access\n\n";

echo "3. ✓ Comprehensive Error Handling\n";
echo "   - Logs all processing steps\n";
echo "   - Graceful fallbacks if file not found\n";
echo "   - Clear debug information\n\n";

echo "4. ✓ Multiple Fallback Levels\n";
echo "   - Priority 1: Base64 embedded image\n";
echo "   - Priority 2: HTTP URL\n";
echo "   - Priority 3: Text placeholder\n\n";

echo "TESTING STEPS:\n";
echo "1. Export margin report PDF\n";
echo "2. Check Laravel logs for processing info\n";
echo "3. View PDF source for debug comments\n";
echo "4. Verify logo appears in header\n\n";

echo "Test completed!\n";