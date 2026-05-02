<?php

require_once 'vendor/autoload.php';

// Test script untuk memverifikasi data company settings di margin report
echo "=== TESTING MARGIN REPORT LOGO DATA ===\n\n";

// Test 1: Check database for company settings
echo "1. Checking database for company settings...\n";
try {
    // Simulate Laravel environment
    $pdo = new PDO('mysql:host=localhost;dbname=tofu', 'root', '');
    
    $stmt = $pdo->query("SELECT * FROM company_settings LIMIT 5");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($settings)) {
        echo "   ✗ No company settings found in database\n";
        echo "   → Create company settings first:\n";
        echo "      INSERT INTO company_settings (outlet_id, company_name, company_logo, is_active) VALUES (1, 'Test Company', 'logos/test-logo.png', 1);\n";
    } else {
        echo "   ✓ Found " . count($settings) . " company settings records\n";
        foreach ($settings as $setting) {
            echo "      - Outlet ID: {$setting['outlet_id']}, Name: {$setting['company_name']}, Logo: " . ($setting['company_logo'] ?: 'NOT SET') . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check storage directory
echo "2. Checking storage directory...\n";
$storagePath = 'storage/app/public/logos';
if (is_dir($storagePath)) {
    $files = scandir($storagePath);
    $logoFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(png|jpg|jpeg|gif|svg)$/i', $file);
    });
    
    if (empty($logoFiles)) {
        echo "   ⚠ Storage directory exists but no logo files found\n";
        echo "   → Upload logo files to: $storagePath\n";
    } else {
        echo "   ✓ Found " . count($logoFiles) . " logo files:\n";
        foreach ($logoFiles as $file) {
            echo "      - $file\n";
        }
    }
} else {
    echo "   ✗ Storage directory not found: $storagePath\n";
    echo "   → Create directory: mkdir -p $storagePath\n";
}

echo "\n";

// Test 3: Check storage link
echo "3. Checking storage link...\n";
$publicStorageLink = 'public/storage';
if (is_link($publicStorageLink)) {
    $target = readlink($publicStorageLink);
    echo "   ✓ Storage link exists: $publicStorageLink -> $target\n";
} else if (is_dir($publicStorageLink)) {
    echo "   ⚠ Storage path exists as directory (should be symlink)\n";
} else {
    echo "   ✗ Storage link not found\n";
    echo "   → Run: php artisan storage:link\n";
}

echo "\n";

// Test 4: Check controller modifications
echo "4. Checking controller modifications...\n";
$controllerFile = 'app/Http/Controllers/MarginReportController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for debug logging
    if (strpos($content, 'Log::info(\'Margin Report Company Settings\'') !== false) {
        echo "   ✓ Debug logging added to controller\n";
    } else {
        echo "   ✗ Debug logging not found in controller\n";
    }
    
    // Check for trait usage
    if (strpos($content, 'use HasOutletFilter, HasCompanySettings;') !== false) {
        echo "   ✓ HasCompanySettings trait used\n";
    } else {
        echo "   ✗ HasCompanySettings trait not used\n";
    }
    
} else {
    echo "   ✗ Controller file not found\n";
}

echo "\n";

// Test 5: Check template modifications
echo "5. Checking template modifications...\n";
$templateFile = 'resources/views/admin/penjualan/margin/pdf.blade.php';
if (file_exists($templateFile)) {
    $content = file_get_contents($templateFile);
    
    // Check for debug comments
    if (strpos($content, 'DEBUG: Full Company Settings') !== false) {
        echo "   ✓ Enhanced debug info added to template\n";
    } else {
        echo "   ✗ Enhanced debug info not found in template\n";
    }
    
    // Check for logo debug
    if (strpos($content, 'DEBUG: Checking logo conditions') !== false) {
        echo "   ✓ Logo-specific debug added to template\n";
    } else {
        echo "   ✗ Logo-specific debug not found in template\n";
    }
    
} else {
    echo "   ✗ Template file not found\n";
}

echo "\n";

// Test 6: Generate test URL for logo
echo "6. Testing logo URL generation...\n";
$testLogoPath = 'logos/test-logo.png';
$expectedUrl = 'http://localhost/storage/' . $testLogoPath;
echo "   Expected logo URL format: $expectedUrl\n";
echo "   → Test this URL in browser to verify accessibility\n";

echo "\n";

// Summary and next steps
echo "=== NEXT STEPS ===\n";
echo "1. Export margin report PDF\n";
echo "2. Check Laravel logs for debug info:\n";
echo "   tail -f storage/logs/laravel.log\n";
echo "3. View PDF source and look for debug comments\n";
echo "4. Compare debug output with inter-outlet PDF\n";
echo "5. If logo_url is 'NOT SET', check database and storage\n";
echo "6. If logo_url is set but image doesn't show, check file permissions\n\n";

echo "Test completed!\n";