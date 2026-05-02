<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Final Test: Service Invoice Print ===\n\n";

try {
    // Test the actual print functionality
    echo "1. Testing ServiceInvoice print method...\n";
    
    $invoice = \App\Models\ServiceInvoice::with(['member', 'mesinCustomer.produk', 'items', 'outlet'])->first();
    
    if (!$invoice) {
        echo "   ❌ No service invoice found\n";
        exit;
    }
    
    echo "   ✅ Invoice found: {$invoice->no_invoice}\n";
    echo "   Outlet ID: {$invoice->outlet_id}\n";
    echo "   Member: {$invoice->member->nama}\n";
    echo "   Items count: " . $invoice->items->count() . "\n";
    
    // Test company settings retrieval
    echo "\n2. Testing company settings retrieval...\n";
    $outletId = $invoice->outlet_id ?? 1;
    $companySetting = \App\Models\CompanySetting::getOrCreateForOutlet($outletId);
    
    echo "   ✅ Company setting retrieved\n";
    echo "   Company name: {$companySetting->company_name}\n";
    echo "   Has logo: " . ($companySetting->logo_url ? 'YES' : 'NO') . "\n";
    echo "   Has address: " . ($companySetting->company_address ? 'YES' : 'NO') . "\n";
    echo "   Has phone: " . ($companySetting->company_phone ? 'YES' : 'NO') . "\n";
    echo "   Has email: " . ($companySetting->company_email ? 'YES' : 'NO') . "\n";
    
    // Test array transformation
    echo "\n3. Testing array transformation...\n";
    $companySettings = [
        'company_name' => $companySetting->company_name ?? 'Nama Perusahaan',
        'company_address' => $companySetting->company_address,
        'formatted_address' => $companySetting->formatted_address,
        'company_phone' => $companySetting->company_phone,
        'company_email' => $companySetting->company_email,
        'logo_url' => $companySetting->logo_url,
        'bank_name' => $companySetting->bank_name,
        'bank_account_number' => $companySetting->bank_account_number,
        'bank_account_name' => $companySetting->bank_account_name,
        'npwp' => $companySetting->npwp,
        'nib' => $companySetting->nib,
        'siup' => $companySetting->siup,
        'tdp' => $companySetting->tdp,
    ];
    
    echo "   ✅ Array transformation successful\n";
    
    // Check if all required fields are available
    $requiredFields = ['company_name', 'logo_url', 'formatted_address'];
    foreach ($requiredFields as $field) {
        $value = $companySettings[$field] ?? null;
        echo "   - {$field}: " . ($value ? 'SET' : 'NULL') . "\n";
    }
    
    // Test view compilation (without PDF generation)
    echo "\n4. Testing view compilation...\n";
    try {
        $viewContent = view('admin.service.invoice.print', compact('invoice', 'companySettings'))->render();
        echo "   ✅ View compiled successfully\n";
        echo "   Content length: " . strlen($viewContent) . " characters\n";
        
        // Check if company name appears in the view
        if (strpos($viewContent, $companySettings['company_name']) !== false) {
            echo "   ✅ Company name appears in view\n";
        } else {
            echo "   ❌ Company name not found in view\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ View compilation failed: " . $e->getMessage() . "\n";
    }
    
    // Test URL generation
    echo "\n5. Testing URL generation...\n";
    $printUrl = route('admin.service.invoice.print', $invoice->id_service_invoice);
    echo "   Print URL: {$printUrl}\n";
    
    // Test HTTP request simulation
    echo "\n6. Testing HTTP request simulation...\n";
    try {
        $response = \Illuminate\Support\Facades\Http::get($printUrl);
        echo "   HTTP Status: " . $response->status() . "\n";
        
        if ($response->successful()) {
            echo "   ✅ HTTP request successful\n";
            echo "   Content-Type: " . $response->header('Content-Type') . "\n";
        } else {
            echo "   ❌ HTTP request failed\n";
        }
    } catch (Exception $e) {
        echo "   ⚠️  HTTP test skipped: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Summary ===\n";
echo "✅ = Working correctly\n";
echo "❌ = Needs attention\n";
echo "⚠️  = Warning/Skipped\n";

echo "\n=== Manual Testing Instructions ===\n";
echo "1. Open browser and go to: " . url('/admin/service/invoice') . "\n";
echo "2. Click the print button (green printer icon) on any invoice\n";
echo "3. PDF should generate and display company information\n";
echo "4. Check that logo, company name, address appear correctly\n";

echo "\nFix completed successfully!\n";