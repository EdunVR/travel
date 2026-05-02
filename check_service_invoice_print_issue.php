<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Service Invoice Print Issue ===\n\n";

try {
    // Check setting table structure
    echo "1. Checking setting table...\n";
    $setting = DB::table('setting')->first();
    
    if ($setting) {
        echo "   Setting record found:\n";
        foreach ((array)$setting as $key => $value) {
            $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
            echo "   - $key: $displayValue\n";
        }
    } else {
        echo "   ❌ No setting record found\n";
    }
    
    // Check if we have a ServiceInvoice to test with
    echo "\n2. Checking ServiceInvoice records...\n";
    $invoiceCount = DB::table('service_invoices')->count();
    echo "   Total service invoices: $invoiceCount\n";
    
    if ($invoiceCount > 0) {
        $sampleInvoice = DB::table('service_invoices')->first();
        echo "   Sample invoice ID: {$sampleInvoice->id_service_invoice}\n";
        echo "   Sample invoice number: {$sampleInvoice->no_invoice}\n";
    }
    
    // Check what variables the print view expects
    echo "\n3. Analyzing print view requirements...\n";
    $printViewPath = 'resources/views/admin/service/invoice/print.blade.php';
    if (file_exists($printViewPath)) {
        $content = file_get_contents($printViewPath);
        
        // Find all $companySettings usage
        preg_match_all('/\$companySettings\[\'([^\']+)\'\]/', $content, $matches);
        if (!empty($matches[1])) {
            echo "   Required companySettings fields:\n";
            $fields = array_unique($matches[1]);
            foreach ($fields as $field) {
                echo "   - $field\n";
            }
        }
    }
    
    // Check CompanySettingController for reference
    echo "\n4. Checking CompanySettingController...\n";
    if (file_exists('app/Http/Controllers/CompanySettingController.php')) {
        echo "   ✅ CompanySettingController exists\n";
        
        // Check if there's a method to get company settings
        $controllerContent = file_get_contents('app/Http/Controllers/CompanySettingController.php');
        if (strpos($controllerContent, 'getCompanySettings') !== false) {
            echo "   ✅ getCompanySettings method found\n";
        } else {
            echo "   ⚠️  getCompanySettings method not found\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Analysis Complete ===\n";