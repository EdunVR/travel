<?php

// Setup company settings for logo testing
echo "=== SETUP COMPANY SETTINGS FOR LOGO TEST ===\n\n";

// Create Artisan command to setup company settings
$artisanCommand = "
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Setup company settings
DB::table('company_settings')->updateOrInsert(
    ['outlet_id' => 1],
    [
        'company_name' => 'Test Company',
        'company_logo' => 'logos/test-logo.png',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]
);

echo 'Company settings updated for outlet 1';
";

file_put_contents('setup_company_settings.php', "<?php\n\nrequire_once 'vendor/autoload.php';\n\n// Bootstrap Laravel\n\$app = require_once 'bootstrap/app.php';\n\$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();\n\n" . $artisanCommand);

echo "✓ Created setup_company_settings.php\n";
echo "✓ Run: php setup_company_settings.php\n\n";

// Alternative: Create SQL file
$sql = "
-- Setup company settings for logo testing
INSERT INTO company_settings (outlet_id, company_name, company_logo, is_active, created_at, updated_at) 
VALUES (1, 'Test Company', 'logos/test-logo.png', 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE 
    company_logo = 'logos/test-logo.png', 
    company_name = 'Test Company',
    updated_at = NOW();

-- Verify the record
SELECT id, outlet_id, company_name, company_logo FROM company_settings WHERE outlet_id = 1;
";

file_put_contents('setup_company_settings.sql', $sql);

echo "✓ Created setup_company_settings.sql\n";
echo "✓ Import via phpMyAdmin or run: mysql -u root -p database_name < setup_company_settings.sql\n\n";

echo "=== TESTING CHECKLIST ===\n";
echo "1. ✓ Test logo created: storage/app/public/logos/test-logo.png\n";
echo "2. ✓ Storage link created: public/storage -> storage/app/public\n";
echo "3. □ Setup company settings (run setup_company_settings.php or .sql)\n";
echo "4. □ Test logo URL in browser: http://localhost/storage/logos/test-logo.png\n";
echo "5. □ Export margin report PDF and check debug info\n";
echo "6. □ Verify logo appears in PDF header\n\n";

echo "Setup completed!\n";