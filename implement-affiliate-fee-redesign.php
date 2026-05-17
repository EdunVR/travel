<?php

/**
 * SCRIPT IMPLEMENTASI AFFILIATE FEE SYSTEM REDESIGN
 * 
 * Script ini membantu implementasi perubahan sistem fee affiliate:
 * 1. Disable fee keagenan
 * 2. Implementasi sistem termin untuk fee penjualan
 * 3. Register event & listener
 * 
 * Cara pakai:
 * php implement-affiliate-fee-redesign.php
 */

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔄 AFFILIATE FEE SYSTEM REDESIGN - IMPLEMENTATION HELPER\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;
use App\Models\AffiliateReferral;
use App\Models\Affiliator;

echo "📋 CHECKLIST IMPLEMENTASI:\n\n";

// 1. Check if files exist
echo "1️⃣  Checking required files...\n";
$files = [
    'app/Events/BookingFullyPaid.php',
    'app/Listeners/ReleaseTermin1OnPaymentComplete.php',
    'app/Console/Commands/ReleaseTermin2AfterDeparture.php',
];

$allFilesExist = true;
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "   ✅ {$file}\n";
    } else {
        echo "   ❌ {$file} - NOT FOUND!\n";
        $allFilesExist = false;
    }
}

if (!$allFilesExist) {
    echo "\n⚠️  Some files are missing. Please create them first.\n";
    echo "   See: AFFILIATE_FEE_SYSTEM_REDESIGN.md\n\n";
    exit(1);
}

echo "\n2️⃣  Disabling agency fee in database...\n";
try {
    $setting = CompanySetting::first();
    if ($setting) {
        $setting->update(['agency_fee_enabled' => false]);
        echo "   ✅ Agency fee disabled in company settings\n";
    } else {
        echo "   ⚠️  No company settings found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n3️⃣  Checking affiliate referrals status...\n";
try {
    $totalReferrals = AffiliateReferral::count();
    $termin1Released = AffiliateReferral::where('termin_1_released', true)->count();
    $termin2Released = AffiliateReferral::where('termin_2_released', true)->count();
    $bothReleased = AffiliateReferral::where('termin_1_released', true)
                                     ->where('termin_2_released', true)
                                     ->count();
    
    echo "   📊 Total referrals: {$totalReferrals}\n";
    echo "   📊 Termin 1 released: {$termin1Released}\n";
    echo "   📊 Termin 2 released: {$termin2Released}\n";
    echo "   📊 Both released: {$bothReleased}\n";
    
    // Referrals yang siap untuk termin 1
    $readyForTermin1 = AffiliateReferral::where('termin_1_released', false)
        ->whereHas('booking', function($q) {
            $q->where('payment_status', 'paid');
        })
        ->count();
    
    if ($readyForTermin1 > 0) {
        echo "\n   ⚠️  {$readyForTermin1} referral(s) ready for termin 1 release!\n";
        echo "      These bookings are fully paid but termin 1 not released yet.\n";
    }
    
    // Referrals yang siap untuk termin 2
    $readyForTermin2 = AffiliateReferral::where('termin_1_released', true)
        ->where('termin_2_released', false)
        ->whereHas('booking', function($q) {
            $q->where('departure_date', '<=', now());
        })
        ->count();
    
    if ($readyForTermin2 > 0) {
        echo "\n   ⚠️  {$readyForTermin2} referral(s) ready for termin 2 release!\n";
        echo "      Run: php artisan affiliate:release-termin2\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n4️⃣  Checking affiliator balances...\n";
try {
    $totalAffiliators = Affiliator::count();
    $withPending = Affiliator::where('pending_balance', '>', 0)->count();
    $withAvailable = Affiliator::where('available_balance', '>', 0)->count();
    
    $totalPending = Affiliator::sum('pending_balance');
    $totalAvailable = Affiliator::sum('available_balance');
    
    echo "   📊 Total affiliators: {$totalAffiliators}\n";
    echo "   📊 With pending balance: {$withPending}\n";
    echo "   📊 With available balance: {$withAvailable}\n";
    echo "   💰 Total pending: Rp " . number_format($totalPending, 0, ',', '.') . "\n";
    echo "   💰 Total available: Rp " . number_format($totalAvailable, 0, ',', '.') . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 NEXT STEPS:\n\n";

echo "1. Register Event & Listener:\n";
echo "   Edit: app/Providers/EventServiceProvider.php\n";
echo "   Add to \$listen array:\n";
echo "   \\App\\Events\\BookingFullyPaid::class => [\n";
echo "       \\App\\Listeners\\ReleaseTermin1OnPaymentComplete::class,\n";
echo "   ],\n\n";

echo "2. Register Command for Cron:\n";
echo "   Edit: app/Console/Kernel.php\n";
echo "   Add to schedule() method:\n";
echo "   \$schedule->command('affiliate:release-termin2')\n";
echo "            ->dailyAt('00:01')\n";
echo "            ->withoutOverlapping();\n\n";

echo "3. Trigger Event in PaymentController:\n";
echo "   Edit: app/Http/Controllers/PaymentController.php\n";
echo "   Add after payment verification when booking is fully paid:\n";
echo "   event(new \\App\\Events\\BookingFullyPaid(\$booking));\n\n";

echo "4. Update Views:\n";
echo "   - resources/views/admin/sistem/pengaturan/index.blade.php (disable agency fee)\n";
echo "   - resources/views/affiliate/dashboard.blade.php (add fee status section)\n";
echo "   - resources/views/admin/affiliate/show.blade.php (add termin status)\n";
echo "   - resources/views/admin/affiliate/index.blade.php (remove agency fee column)\n\n";

echo "5. Clear Cache:\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan route:clear\n\n";

echo "6. Test:\n";
echo "   - Test pelunasan booking → termin 1 release\n";
echo "   - Test command: php artisan affiliate:release-termin2\n";
echo "   - Test UI dashboard mitra\n";
echo "   - Test UI detail mitra admin\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Implementation helper completed!\n";
echo "📖 For detailed guide, see: AFFILIATE_FEE_SYSTEM_REDESIGN.md\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
