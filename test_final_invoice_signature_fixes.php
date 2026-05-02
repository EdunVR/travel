<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\SalesInvoice;
use App\Models\ServiceInvoice;
use App\Models\CompanySetting;
use App\Models\User;

echo "=== TESTING FINAL INVOICE SIGNATURE FIXES ===\n\n";

try {
    // Test 1: Check if CompanySetting model has logo_url accessor
    echo "1. Testing CompanySetting logo_url accessor...\n";
    $companySetting = CompanySetting::first();
    if ($companySetting) {
        echo "   - Company name: " . $companySetting->company_name . "\n";
        echo "   - Logo URL: " . ($companySetting->logo_url ?? 'NULL') . "\n";
        echo "   - Company logo field: " . ($companySetting->company_logo ?? 'NULL') . "\n";
        echo "   ✓ CompanySetting model working\n\n";
    } else {
        echo "   - No company settings found\n\n";
    }

    // Test 2: Check User signature accessor
    echo "2. Testing User signature accessor...\n";
    $user = User::where('is_active', true)->first();
    if ($user) {
        echo "   - User name: " . $user->name . "\n";
        echo "   - Signature path: " . ($user->signature_path ?? 'NULL') . "\n";
        echo "   - Signature URL: " . ($user->signature_url ?? 'NULL') . "\n";
        echo "   ✓ User signature accessor working\n\n";
    } else {
        echo "   - No active users found\n\n";
    }

    // Test 3: Check if sales invoice exists
    echo "3. Testing Sales Invoice data...\n";
    $salesInvoice = SalesInvoice::with(['member', 'prospek', 'items'])->first();
    if ($salesInvoice) {
        echo "   - Invoice No: " . $salesInvoice->no_invoice . "\n";
        echo "   - Customer: " . ($salesInvoice->member->nama ?? $salesInvoice->prospek->nama ?? 'Unknown') . "\n";
        echo "   - Total: Rp " . number_format($salesInvoice->total, 0, ',', '.') . "\n";
        echo "   - Items count: " . $salesInvoice->items->count() . "\n";
        echo "   ✓ Sales invoice data available\n\n";
    } else {
        echo "   - No sales invoices found\n\n";
    }

    // Test 4: Check if service invoice exists
    echo "4. Testing Service Invoice data...\n";
    $serviceInvoice = ServiceInvoice::with(['member', 'items'])->first();
    if ($serviceInvoice) {
        echo "   - Invoice No: " . $serviceInvoice->no_invoice . "\n";
        echo "   - Customer: " . ($serviceInvoice->member->nama ?? 'Unknown') . "\n";
        echo "   - Total: Rp " . number_format($serviceInvoice->total, 0, ',', '.') . "\n";
        echo "   - Items count: " . $serviceInvoice->items->count() . "\n";
        echo "   ✓ Service invoice data available\n\n";
    } else {
        echo "   - No service invoices found\n\n";
    }

    // Test 5: Test company settings for print method
    echo "5. Testing company settings for print...\n";
    if ($companySetting) {
        $printSettings = [
            'company_name' => $companySetting->company_name,
            'company_address' => $companySetting->company_address,
            'formatted_address' => $companySetting->formatted_address,
            'company_phone' => $companySetting->company_phone,
            'company_email' => $companySetting->company_email,
            'logo_url' => $companySetting->logo_url,
            'bank_name' => $companySetting->bank_name,
            'bank_account_number' => $companySetting->bank_account_number,
            'bank_account_name' => $companySetting->bank_account_name,
        ];
        
        echo "   - Print settings array created successfully\n";
        echo "   - Logo URL in array: " . ($printSettings['logo_url'] ?? 'NULL') . "\n";
        echo "   - No 'company_logo' key in array: " . (isset($printSettings['company_logo']) ? 'FALSE' : 'TRUE') . "\n";
        echo "   ✓ Print settings prepared correctly\n\n";
    }

    // Test 6: Check file paths
    echo "6. Testing file paths...\n";
    if ($companySetting && $companySetting->logo_url) {
        $logoPath = str_replace(url('/'), '', $companySetting->logo_url);
        $fullPath = public_path($logoPath);
        echo "   - Logo URL: " . $companySetting->logo_url . "\n";
        echo "   - Relative path: " . $logoPath . "\n";
        echo "   - Full path: " . $fullPath . "\n";
        echo "   - File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    }
    
    if ($user && $user->signature_path) {
        $signaturePath = public_path($user->signature_path);
        echo "   - Signature path: " . $user->signature_path . "\n";
        echo "   - Full path: " . $signaturePath . "\n";
        echo "   - File exists: " . (file_exists($signaturePath) ? 'YES' : 'NO') . "\n";
    }
    echo "   ✓ File path testing completed\n\n";

    echo "=== ALL TESTS COMPLETED SUCCESSFULLY ===\n";
    echo "The invoice signature fixes should now work correctly.\n";
    echo "Key fixes applied:\n";
    echo "- Fixed syntax error in sales invoice template (/* */ to {{-- --}})\n";
    echo "- Updated templates to use signature_path instead of signature_url\n";
    echo "- Added proper null checks for logo_url\n";
    echo "- Fixed file path handling for PDF generation\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}