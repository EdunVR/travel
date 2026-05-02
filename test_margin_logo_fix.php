<?php

require_once 'vendor/autoload.php';

// Test script untuk memverifikasi perbaikan logo di margin report PDF
echo "=== TESTING MARGIN REPORT LOGO FIX ===\n\n";

// Test 1: Verify MarginReportController uses HasCompanySettings trait
echo "1. Testing MarginReportController traits...\n";
$controllerFile = 'app/Http/Controllers/MarginReportController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check if HasCompanySettings trait is used
    if (strpos($content, 'use App\Traits\HasCompanySettings;') !== false) {
        echo "   ✓ HasCompanySettings trait imported\n";
    } else {
        echo "   ✗ HasCompanySettings trait not imported\n";
    }
    
    // Check if trait is used in class
    if (strpos($content, 'use HasOutletFilter, HasCompanySettings;') !== false) {
        echo "   ✓ HasCompanySettings trait used in class\n";
    } else {
        echo "   ✗ HasCompanySettings trait not used in class\n";
    }
    
    // Check if getCompanySettingsForPrint method is used
    if (strpos($content, '$this->getCompanySettingsForPrint()') !== false) {
        echo "   ✓ getCompanySettingsForPrint method used\n";
    } else {
        echo "   ✗ getCompanySettingsForPrint method not used\n";
    }
    
    // Check if outlet session is set
    if (strpos($content, "session(['selected_outlet_id' => \$outletId])") !== false) {
        echo "   ✓ Outlet session setting found\n";
    } else {
        echo "   ✗ Outlet session setting not found\n";
    }
    
} else {
    echo "   ✗ MarginReportController not found\n";
}

echo "\n";

// Test 2: Verify HasCompanySettings trait exists and has correct method
echo "2. Testing HasCompanySettings trait...\n";
$traitFile = 'app/Traits/HasCompanySettings.php';
if (file_exists($traitFile)) {
    $content = file_get_contents($traitFile);
    
    // Check if getCompanySettingsForPrint method exists
    if (strpos($content, 'protected function getCompanySettingsForPrint()') !== false) {
        echo "   ✓ getCompanySettingsForPrint method found\n";
    } else {
        echo "   ✗ getCompanySettingsForPrint method not found\n";
    }
    
    // Check if logo_url is included in return array
    if (strpos($content, "'logo_url' => \$settings->logo_url") !== false) {
        echo "   ✓ logo_url included in settings array\n";
    } else {
        echo "   ✗ logo_url not included in settings array\n";
    }
    
} else {
    echo "   ✗ HasCompanySettings trait not found\n";
}

echo "\n";

// Test 3: Compare logo implementation with inter-outlet
echo "3. Comparing logo implementation...\n";
$interOutletTemplate = 'resources/views/admin/penjualan/inter-outlet/print.blade.php';
$marginTemplate = 'resources/views/admin/penjualan/margin/pdf.blade.php';

if (file_exists($interOutletTemplate) && file_exists($marginTemplate)) {
    $interOutletContent = file_get_contents($interOutletTemplate);
    $marginContent = file_get_contents($marginTemplate);
    
    // Extract logo HTML from both files
    preg_match('/@if\(isset\(\$companySettings\[\'logo_url\'\]\).*?@endif/s', $interOutletContent, $interOutletLogo);
    preg_match('/@if\(isset\(\$companySettings\[\'logo_url\'\]\).*?@endif/s', $marginContent, $marginLogo);
    
    if (!empty($interOutletLogo) && !empty($marginLogo)) {
        if (trim($interOutletLogo[0]) === trim($marginLogo[0])) {
            echo "   ✓ Logo HTML implementation matches inter-outlet\n";
        } else {
            echo "   ⚠ Logo HTML implementation differs from inter-outlet\n";
            echo "   Inter-outlet: " . substr(trim($interOutletLogo[0]), 0, 100) . "...\n";
            echo "   Margin: " . substr(trim($marginLogo[0]), 0, 100) . "...\n";
        }
    } else {
        echo "   ✗ Could not extract logo HTML from templates\n";
    }
    
} else {
    echo "   ✗ Template files not found\n";
}

echo "\n";

// Test 4: Check CompanySetting model
echo "4. Testing CompanySetting model...\n";
$modelFile = 'app/Models/CompanySetting.php';
if (file_exists($modelFile)) {
    $content = file_get_contents($modelFile);
    
    // Check if getLogoUrlAttribute exists
    if (strpos($content, 'public function getLogoUrlAttribute()') !== false) {
        echo "   ✓ getLogoUrlAttribute accessor found\n";
    } else {
        echo "   ✗ getLogoUrlAttribute accessor not found\n";
    }
    
    // Check if URL generation logic exists
    if (strpos($content, 'Storage::url') !== false) {
        echo "   ✓ Storage URL generation found\n";
    } else {
        echo "   ✗ Storage URL generation not found\n";
    }
    
} else {
    echo "   ✗ CompanySetting model not found\n";
}

echo "\n";

// Summary and debugging tips
echo "=== DEBUGGING TIPS ===\n";
echo "If logo still doesn't appear:\n\n";

echo "1. Check company settings in database:\n";
echo "   SELECT * FROM company_settings WHERE outlet_id = [your_outlet_id];\n\n";

echo "2. Check if logo file exists:\n";
echo "   - Check storage/app/public/logos/ folder\n";
echo "   - Run: php artisan storage:link\n\n";

echo "3. Test logo URL directly:\n";
echo "   - Add this to margin PDF template for debugging:\n";
echo "   <!-- Debug: {{ json_encode(\$companySettings) }} -->\n\n";

echo "4. Check session outlet_id:\n";
echo "   - Add dd(session('selected_outlet_id')) in controller\n\n";

echo "5. Compare with working inter-outlet:\n";
echo "   - Export inter-outlet PDF and check if logo appears\n";
echo "   - If inter-outlet logo works, the issue is in data passing\n\n";

echo "=== QUICK FIX TEST ===\n";
echo "Add this debug line to margin PDF template after <body> tag:\n";
echo "<!-- DEBUG: Logo URL = {{ \$companySettings['logo_url'] ?? 'NOT SET' }} -->\n";
echo "<!-- DEBUG: Company Name = {{ \$companySettings['company_name'] ?? 'NOT SET' }} -->\n\n";

echo "Test completed!\n";