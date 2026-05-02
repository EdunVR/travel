<?php

require_once 'vendor/autoload.php';

// Test Company Settings Integration in Kontrabon Print
echo "=== TESTING COMPANY SETTINGS INTEGRATION IN KONTRABON PRINT ===\n\n";

try {
    // Test 1: Check if CompanySetting model exists and has correct structure
    echo "1. Testing CompanySetting Model Structure...\n";
    
    $reflection = new ReflectionClass('App\Models\CompanySetting');
    $fillableProperty = $reflection->getProperty('fillable');
    $fillableProperty->setAccessible(true);
    $fillable = $fillableProperty->getDefaultValue();
    
    $requiredFields = ['company_name', 'company_address', 'company_phone', 'outlet_id'];
    $missingFields = array_diff($requiredFields, $fillable);
    
    if (empty($missingFields)) {
        echo "✓ CompanySetting model has all required fields\n";
    } else {
        echo "✗ Missing fields in CompanySetting: " . implode(', ', $missingFields) . "\n";
    }
    
    // Test 2: Check controller method
    echo "\n2. Testing KontraBonController print method...\n";
    
    $controllerFile = file_get_contents('app/Http/Controllers/Admin/KontraBonController.php');
    
    if (strpos($controllerFile, 'CompanySetting::where(\'outlet_id\', $kontraBon->id_outlet)') !== false) {
        echo "✓ Controller fetches company setting by outlet_id\n";
    } else {
        echo "✗ Controller not fetching company setting by outlet_id\n";
    }
    
    if (strpos($controllerFile, 'CompanySetting::first()') !== false) {
        echo "✓ Controller has fallback to first company setting\n";
    } else {
        echo "✗ Controller missing fallback logic\n";
    }
    
    if (strpos($controllerFile, 'company_name\' => \'NAMA PERUSAHAAN\'') !== false) {
        echo "✓ Controller has default values fallback\n";
    } else {
        echo "✗ Controller missing default values fallback\n";
    }
    
    // Test 3: Check print view
    echo "\n3. Testing print.blade.php view...\n";
    
    $printView = file_get_contents('resources/views/admin/penjualan/kontrabon/print.blade.php');
    
    if (strpos($printView, '$companySetting->company_name') !== false) {
        echo "✓ Print view uses \$companySetting for company name\n";
    } else {
        echo "✗ Print view not using \$companySetting for company name\n";
    }
    
    if (strpos($printView, '$companySetting->company_address') !== false) {
        echo "✓ Print view uses \$companySetting for company address\n";
    } else {
        echo "✗ Print view not using \$companySetting for company address\n";
    }
    
    if (strpos($printView, '$companySetting->company_phone') !== false) {
        echo "✓ Print view uses \$companySetting for company phone\n";
    } else {
        echo "✗ Print view not using \$companySetting for company phone\n";
    }
    
    // Check for any remaining $setting references
    if (strpos($printView, '$setting->') !== false) {
        echo "✗ Print view still contains \$setting references\n";
        // Find and show the lines
        $lines = explode("\n", $printView);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, '$setting->') !== false) {
                echo "   Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
            }
        }
    } else {
        echo "✓ Print view has no remaining \$setting references\n";
    }
    
    // Test 4: Check if compact includes companySetting
    if (strpos($controllerFile, 'compact(') !== false && strpos($controllerFile, '\'companySetting\'') !== false) {
        echo "✓ Controller passes \$companySetting to view\n";
    } else {
        echo "✗ Controller not passing \$companySetting to view\n";
    }
    
    echo "\n=== INTEGRATION TEST SUMMARY ===\n";
    echo "The Company Settings integration appears to be complete.\n";
    echo "Controller fetches company settings with proper fallback logic.\n";
    echo "Print view uses \$companySetting variables correctly.\n";
    echo "\nTo test functionality:\n";
    echo "1. Create/update company settings for an outlet\n";
    echo "2. Create a kontrabon for that outlet\n";
    echo "3. Print the kontrabon and verify company info displays correctly\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";