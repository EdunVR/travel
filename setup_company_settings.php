<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();


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
