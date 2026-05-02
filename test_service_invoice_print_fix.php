<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Service Invoice Print Fix ===\n\n";

try {
    // 1. Check if we have service invoices
    echo "1. Checking ServiceInvoice records...\n";
    $invoices = DB::table('service_invoices')->get();
    echo "   Total service invoices: " . $invoices->count() . "\n";
    
    if ($invoices->count() > 0) {
        $testInvoice = $invoices->first();
        echo "   Test invoice ID: {$testInvoice->id_service_invoice}\n";
        echo "   Test invoice number: {$testInvoice->no_invoice}\n";
        echo "   Test invoice outlet_id: " . ($testInvoice->outlet_id ?? 'NULL') . "\n";
    }
    
    // 2. Check CompanySetting table
    echo "\n2. Checking CompanySetting table...\n";
    $companySettings = DB::table('company_settings')->get();
    echo "   Total company settings: " . $companySettings->count() . "\n";
    
    if ($companySettings->count() === 0) {
        echo "   Creating default company setting for outlet 1...\n";
        
        // Create default company setting
        DB::table('company_settings')->insert([
            'outlet_id' => 1,
            'company_name' => 'MORRA ERP',
            'company_address' => 'Jl. Contoh No. 123, Jakarta',
            'company_phone' => '021-12345678',
            'company_email' => 'info@morra.com',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'tax_rate' => 11.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "   ✅ Default company setting created\n";
    } else {
        $setting = $companySettings->first();
        echo "   ✅ Company setting exists for outlet: {$setting->outlet_id}\n";
        echo "   Company name: {$setting->company_name}\n";
    }
    
    // 3. Test the CompanySetting model
    echo "\n3. Testing CompanySetting model...\n";
    $companySetting = \App\Models\CompanySetting::getOrCreateForOutlet(1);
    echo "   ✅ CompanySetting model working\n";
    echo "   Company name: {$companySetting->company_name}\n";
    echo "   Logo URL: " . ($companySetting->logo_url ?? 'NULL') . "\n";
    echo "   Formatted address: " . ($companySetting->formatted_address ?? 'NULL') . "\n";
    
    // 4. Test print URL (if we have invoices)
    if ($invoices->count() > 0) {
        echo "\n4. Testing print URL...\n";
        $testInvoiceId = $invoices->first()->id_service_invoice;
        $printUrl = url("/admin/service/invoice/print/{$testInvoiceId}");
        echo "   Print URL: {$printUrl}\n";
        echo "   ✅ You can test this URL in browser\n";
    }
    
    // 5. Check if ServiceInvoice has outlet relationship
    echo "\n5. Checking ServiceInvoice model relationships...\n";
    if ($invoices->count() > 0) {
        $invoice = \App\Models\ServiceInvoice::with('outlet')->first();
        if ($invoice->outlet) {
            echo "   ✅ ServiceInvoice has outlet relationship\n";
            echo "   Outlet name: {$invoice->outlet->nama_outlet}\n";
        } else {
            echo "   ⚠️  ServiceInvoice outlet relationship not found\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nNext steps:\n";
echo "1. Test print invoice in browser\n";
echo "2. Check if PDF generates without errors\n";
echo "3. Verify company information appears correctly\n";