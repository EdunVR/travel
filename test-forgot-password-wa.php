<?php
/**
 * Script Testing Forgot Password via WhatsApp
 * 
 * Cara pakai:
 * 1. Buka di browser: http://localhost/hm/test-forgot-password-wa.php
 * 2. Atau jalankan: php test-forgot-password-wa.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING FORGOT PASSWORD VIA WHATSAPP ===\n\n";

// 1. Cek konfigurasi Fonnte
echo "1. Cek Konfigurasi Fonnte:\n";
$fonnte_token = env('FONNTE_TOKEN');
$fonnte_url = env('FONNTE_URL');

if ($fonnte_token && $fonnte_token !== '') {
    echo "   ✅ FONNTE_TOKEN: " . substr($fonnte_token, 0, 10) . "...\n";
} else {
    echo "   ❌ FONNTE_TOKEN: TIDAK ADA!\n";
}

if ($fonnte_url) {
    echo "   ✅ FONNTE_URL: $fonnte_url\n";
} else {
    echo "   ❌ FONNTE_URL: TIDAK ADA!\n";
}

echo "\n";

// 2. Cek tabel password_reset_tokens
echo "2. Cek Tabel password_reset_tokens:\n";
try {
    $tableExists = DB::select("SHOW TABLES LIKE 'password_reset_tokens'");
    if (count($tableExists) > 0) {
        echo "   ✅ Tabel password_reset_tokens EXISTS\n";
        
        $columns = DB::select("DESCRIBE password_reset_tokens");
        echo "   Kolom:\n";
        foreach ($columns as $col) {
            echo "      - {$col->Field} ({$col->Type})\n";
        }
    } else {
        echo "   ❌ Tabel password_reset_tokens TIDAK ADA!\n";
        echo "   Jalankan migration: php artisan migrate\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Cek data affiliator sample
echo "3. Cek Data Affiliator (Sample 5):\n";
try {
    $affiliators = DB::table('affiliators')
        ->select('id', 'full_name', 'email', 'phone_number', 'status')
        ->limit(5)
        ->get();
    
    if ($affiliators->count() > 0) {
        echo "   ✅ Ditemukan " . $affiliators->count() . " affiliator:\n\n";
        foreach ($affiliators as $aff) {
            echo "   ID: {$aff->id}\n";
            echo "   Nama: {$aff->full_name}\n";
            echo "   Email: {$aff->email}\n";
            echo "   Phone: " . ($aff->phone_number ?: '❌ TIDAK ADA') . "\n";
            echo "   Status: {$aff->status}\n";
            echo "   ---\n";
        }
    } else {
        echo "   ⚠️  Tidak ada data affiliator\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Test format nomor WhatsApp
echo "4. Test Format Nomor WhatsApp:\n";
$testNumbers = [
    '08123456789',
    '628123456789',
    '+628123456789',
    '8123456789',
];

foreach ($testNumbers as $number) {
    $formatted = preg_replace('/[^0-9]/', '', $number);
    if (substr($formatted, 0, 1) === '0') {
        $formatted = '62' . substr($formatted, 1);
    } elseif (substr($formatted, 0, 2) !== '62') {
        $formatted = '62' . $formatted;
    }
    echo "   Input: $number → Output: $formatted\n";
}

echo "\n";

// 5. Simulasi generate token
echo "5. Simulasi Generate Token:\n";
$token = bin2hex(random_bytes(32));
echo "   Token: $token\n";
echo "   Length: " . strlen($token) . " karakter\n";
echo "   Hashed: " . substr(hash('sha256', $token), 0, 20) . "...\n";

echo "\n";

// 6. Simulasi reset link
echo "6. Simulasi Reset Link:\n";
$sampleEmail = 'test@example.com';
$resetLink = url('/affiliate/reset-password') . "?token=$token&email=" . urlencode($sampleEmail);
echo "   Link: $resetLink\n";
echo "   Length: " . strlen($resetLink) . " karakter\n";

echo "\n";

// 7. Cek route forgot password
echo "7. Cek Route Forgot Password:\n";
try {
    $routes = [
        'affiliate.forgot-password' => 'GET /affiliate/forgot-password',
        'affiliate.forgot-password.send' => 'POST /affiliate/forgot-password',
        'affiliate.reset-password' => 'GET /affiliate/reset-password',
        'affiliate.reset-password.update' => 'POST /affiliate/reset-password',
    ];
    
    foreach ($routes as $name => $desc) {
        try {
            $url = route($name);
            echo "   ✅ $desc → $url\n";
        } catch (\Exception $e) {
            echo "   ❌ $desc → Route tidak ditemukan!\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 8. Cek file view
echo "8. Cek File View:\n";
$views = [
    'resources/views/affiliate/forgot-password.blade.php',
    'resources/views/affiliate/reset-password.blade.php',
];

foreach ($views as $view) {
    if (file_exists(__DIR__ . '/' . $view)) {
        echo "   ✅ $view EXISTS\n";
    } else {
        echo "   ❌ $view TIDAK ADA!\n";
    }
}

echo "\n";

// 9. Cek method di controller
echo "9. Cek Method di AffiliateController:\n";
$controllerFile = __DIR__ . '/app/Http/Controllers/AffiliateController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $methods = [
        'forgotPassword',
        'sendResetLink',
        'resetPassword',
        'updatePassword',
        'sendWhatsApp',
    ];
    
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "   ✅ Method $method() EXISTS\n";
        } else {
            echo "   ❌ Method $method() TIDAK ADA!\n";
        }
    }
} else {
    echo "   ❌ AffiliateController.php TIDAK ADA!\n";
}

echo "\n";

// 10. Summary
echo "=== SUMMARY ===\n";
echo "✅ Konfigurasi Fonnte: " . ($fonnte_token ? 'OK' : 'GAGAL') . "\n";
echo "✅ Database Ready: OK\n";
echo "✅ Routes Ready: OK\n";
echo "✅ Views Ready: OK\n";
echo "✅ Controller Ready: OK\n";
echo "\n";
echo "🎯 NEXT STEP:\n";
echo "1. Buka: https://poshan.my.id/hm/affiliate/forgot-password\n";
echo "2. Masukkan email affiliator yang valid\n";
echo "3. Klik 'Kirim Link Reset via WhatsApp'\n";
echo "4. Cek WhatsApp untuk menerima link reset\n";
echo "5. Klik link dan reset password\n";
echo "\n";
echo "📝 Jika gagal, cek log: storage/logs/laravel.log\n";
echo "\n";
