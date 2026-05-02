<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CompanySetting;
use App\Models\CompanyBankAccount;
use Illuminate\Support\Facades\Storage;

echo "=== DEBUG INVOICE COMPANY SETTINGS ===\n\n";

// Test 1: Check CompanySetting data
echo "1. Checking CompanySetting data...\n";
$settings = CompanySetting::all();

if ($settings->count() > 0) {
    echo "   ✓ Found {$settings->count()} company settings\n\n";
    
    foreach ($settings as $setting) {
        echo "   Outlet ID: {$setting->outlet_id}\n";
        echo "   Company Name: {$setting->company_name}\n";
        echo "   Company Address: " . ($setting->company_address ?? 'NULL') . "\n";
        echo "   Company Phone: " . ($setting->company_phone ?? 'NULL') . "\n";
        echo "   Company Email: " . ($setting->company_email ?? 'NULL') . "\n";
        echo "   Company Logo (DB): " . ($setting->company_logo ?? 'NULL') . "\n";
        echo "   Logo URL (Accessor): " . ($setting->logo_url ?? 'NULL') . "\n";
        
        // Check if logo file exists
        if ($setting->company_logo) {
            $logoPath = storage_path('app/public/' . $setting->company_logo);
            if (file_exists($logoPath)) {
                echo "   ✓ Logo file exists: {$logoPath}\n";
            } else {
                echo "   ✗ Logo file NOT found: {$logoPath}\n";
            }
        }
        
        echo "   Bank Name: " . ($setting->bank_name ?? 'NULL') . "\n";
        echo "   Bank Account Number: " . ($setting->bank_account_number ?? 'NULL') . "\n";
        echo "   Bank Account Name: " . ($setting->bank_account_name ?? 'NULL') . "\n";
        echo "\n";
    }
} else {
    echo "   ✗ No company settings found\n\n";
}

// Test 2: Check CompanyBankAccount data
echo "2. Checking CompanyBankAccount data...\n";
$bankAccounts = CompanyBankAccount::where('is_active', 1)->get();

if ($bankAccounts->count() > 0) {
    echo "   ✓ Found {$bankAccounts->count()} active bank accounts\n\n";
    
    foreach ($bankAccounts as $bank) {
        echo "   ID: {$bank->id}\n";
        echo "   Outlet ID: " . ($bank->id_outlet ?? 'NULL (Global)') . "\n";
        echo "   Bank Name: {$bank->bank_name}\n";
        echo "   Account Number: {$bank->account_number}\n";
        echo "   Account Holder: {$bank->account_holder}\n";
        echo "   Sort Order: " . ($bank->sort_order ?? 0) . "\n";
        echo "\n";
    }
} else {
    echo "   ✗ No active bank accounts found\n\n";
}

// Test 3: Test HasCompanySettings trait
echo "3. Testing HasCompanySettings trait...\n";
try {
    $testController = new class {
        use App\Traits\HasCompanySettings;
        
        protected function getCurrentOutletId() {
            return 1; // Default outlet
        }
    };
    
    $printSettings = $testController->getCompanySettingsForPrint();
    
    echo "   ✓ Trait method works\n";
    echo "   Company Name: " . ($printSettings['company_name'] ?? 'NULL') . "\n";
    echo "   Company Address: " . ($printSettings['company_address'] ?? 'NULL') . "\n";
    echo "   Company Phone: " . ($printSettings['company_phone'] ?? 'NULL') . "\n";
    echo "   Company Email: " . ($printSettings['company_email'] ?? 'NULL') . "\n";
    echo "   Logo URL: " . ($printSettings['logo_url'] ?? 'NULL') . "\n";
    echo "   Bank Name: " . ($printSettings['bank_name'] ?? 'NULL') . "\n";
    echo "   Bank Account Number: " . ($printSettings['bank_account_number'] ?? 'NULL') . "\n";
    echo "   Bank Account Name: " . ($printSettings['bank_account_name'] ?? 'NULL') . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Check storage link
echo "4. Checking storage link...\n";
$publicStoragePath = public_path('storage');
if (is_link($publicStoragePath)) {
    echo "   ✓ Storage link exists\n";
    echo "   Link target: " . readlink($publicStoragePath) . "\n";
} else if (is_dir($publicStoragePath)) {
    echo "   ⚠ Storage directory exists but is not a symlink\n";
} else {
    echo "   ✗ Storage link does NOT exist\n";
    echo "   Run: php artisan storage:link\n";
}
echo "\n";

// Test 5: List logo files in storage
echo "5. Checking logo files in storage...\n";
$logosPath = storage_path('app/public/logos');
if (is_dir($logosPath)) {
    $files = scandir($logosPath);
    $logoFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });
    
    if (count($logoFiles) > 0) {
        echo "   ✓ Found " . count($logoFiles) . " files in logos directory:\n";
        foreach ($logoFiles as $file) {
            echo "      - {$file}\n";
        }
    } else {
        echo "   ⚠ Logos directory exists but is empty\n";
    }
} else {
    echo "   ✗ Logos directory does NOT exist: {$logosPath}\n";
}
echo "\n";

echo "=== RECOMMENDATIONS ===\n";
echo "1. If no company settings found:\n";
echo "   - Go to: /admin/sistem/pengaturan\n";
echo "   - Fill in company information and upload logo\n\n";

echo "2. If logo file not found:\n";
echo "   - Upload logo via pengaturan page\n";
echo "   - Or manually place logo in: storage/app/public/logos/\n\n";

echo "3. If storage link not found:\n";
echo "   - Run: php artisan storage:link\n\n";

echo "4. For bank accounts:\n";
echo "   - Option A: Use company_settings table (bank_name, bank_account_number, bank_account_name)\n";
echo "   - Option B: Use company_bank_accounts table (separate records for multiple banks)\n";
echo "   - Invoice currently uses company_bank_accounts table\n\n";

echo "=== END DEBUG ===\n";
