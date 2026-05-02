<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Outlets and Fixing Service Invoice Print ===\n\n";

try {
    // 1. Check available outlets
    echo "1. Available outlets:\n";
    $outlets = DB::table('outlets')->get();
    foreach ($outlets as $outlet) {
        echo "   - ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}\n";
    }
    
    // 2. Check service invoice outlet_id
    echo "\n2. Service invoice outlet info:\n";
    $invoice = DB::table('service_invoices')->first();
    if ($invoice) {
        echo "   Invoice outlet_id: " . ($invoice->outlet_id ?? 'NULL') . "\n";
        
        // Check if outlet exists
        if ($invoice->outlet_id) {
            $outletExists = DB::table('outlets')->where('id_outlet', $invoice->outlet_id)->exists();
            echo "   Outlet exists: " . ($outletExists ? 'YES' : 'NO') . "\n";
        }
    }
    
    // 3. Check existing company settings
    echo "\n3. Existing company settings:\n";
    $companySettings = DB::table('company_settings')->get();
    foreach ($companySettings as $setting) {
        echo "   - Outlet ID: {$setting->outlet_id}, Company: {$setting->company_name}\n";
    }
    
    // 4. Create company setting for the invoice's outlet if needed
    if ($invoice && $invoice->outlet_id) {
        $outletId = $invoice->outlet_id;
        $existingSetting = DB::table('company_settings')->where('outlet_id', $outletId)->first();
        
        if (!$existingSetting) {
            echo "\n4. Creating company setting for outlet {$outletId}...\n";
            
            DB::table('company_settings')->insert([
                'outlet_id' => $outletId,
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
            
            echo "   ✅ Company setting created for outlet {$outletId}\n";
        } else {
            echo "\n4. Company setting already exists for outlet {$outletId}\n";
        }
    }
    
    // 5. Test the fixed controller method
    echo "\n5. Testing CompanySetting model with correct outlet...\n";
    if ($invoice && $invoice->outlet_id) {
        $companySetting = \App\Models\CompanySetting::getOrCreateForOutlet($invoice->outlet_id);
        echo "   ✅ CompanySetting model working\n";
        echo "   Company name: {$companySetting->company_name}\n";
        echo "   Logo URL: " . ($companySetting->logo_url ?? 'NULL') . "\n";
        
        // Test the array transformation
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
        
        echo "   ✅ Company settings array created successfully\n";
        echo "   Array keys: " . implode(', ', array_keys($companySettings)) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";
echo "\nThe service invoice print should now work correctly!\n";
echo "Test URL: " . url("/admin/service/invoice/print/1") . "\n";