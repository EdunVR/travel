<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\CompanySetting;

// Test company settings logo fix
echo "=== TESTING COMPANY SETTINGS LOGO FIX ===\n\n";

try {
    // Test 1: Check if company_settings table exists and has company_logo column
    echo "1. Checking company_settings table structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM company_settings");
    $hasLogoColumn = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'company_logo') {
            $hasLogoColumn = true;
            echo "   ✓ company_logo column exists\n";
            break;
        }
    }
    
    if (!$hasLogoColumn) {
        echo "   ✗ company_logo column not found\n";
        return;
    }
    
    // Test 2: Get a company setting record
    echo "\n2. Testing CompanySetting model...\n";
    $setting = CompanySetting::first();
    
    if (!$setting) {
        echo "   Creating test company setting...\n";
        $setting = CompanySetting::create([
            'outlet_id' => 1,
            'company_name' => 'Test Company',
            'company_logo' => 'logos/test-logo.png',
            'company_address' => 'Test Address',
            'company_phone' => '081234567890',
            'company_email' => 'test@company.com'
        ]);
    }
    
    echo "   Company Name: " . $setting->company_name . "\n";
    echo "   Logo Field: " . ($setting->company_logo ?? 'NULL') . "\n";
    echo "   Logo URL Accessor: " . ($setting->logo_url ?? 'NULL') . "\n";
    
    // Test 3: Test HasCompanySettings trait
    echo "\n3. Testing HasCompanySettings trait...\n";
    
    // Create a test controller instance to test the trait
    $testController = new class {
        use App\Traits\HasCompanySettings;
        
        public function testGetCompanySettingsForPrint() {
            return $this->getCompanySettingsForPrint();
        }
        
        protected function getCurrentOutletId() {
            return 1; // Default outlet
        }
    };
    
    $printSettings = $testController->testGetCompanySettingsForPrint();
    
    echo "   Print Settings Keys: " . implode(', ', array_keys($printSettings)) . "\n";
    echo "   Logo (Indonesian key): " . ($printSettings['logo'] ?? 'NULL') . "\n";
    echo "   Logo URL (English key): " . ($printSettings['logo_url'] ?? 'NULL') . "\n";
    echo "   Company Name: " . ($printSettings['nama_perusahaan'] ?? 'NULL') . "\n";
    echo "   Address: " . ($printSettings['alamat'] ?? 'NULL') . "\n";
    
    // Test 4: Check if logo URL is properly formatted
    echo "\n4. Testing logo URL format...\n";
    if (isset($printSettings['logo']) && $printSettings['logo']) {
        $logoUrl = $printSettings['logo'];
        echo "   Logo URL: $logoUrl\n";
        
        if (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            echo "   ✓ Logo URL is valid\n";
        } else {
            echo "   ✗ Logo URL is not valid\n";
        }
        
        if (str_contains($logoUrl, 'http')) {
            echo "   ✓ Logo URL contains protocol\n";
        } else {
            echo "   ✗ Logo URL missing protocol\n";
        }
    } else {
        echo "   ⚠ No logo URL found\n";
    }
    
    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}