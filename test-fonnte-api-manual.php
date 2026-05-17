<?php
/**
 * Test Manual Fonnte API
 * 
 * Script ini untuk test langsung API Fonnte tanpa Laravel
 * Untuk memastikan token dan konfigurasi Fonnte bekerja dengan baik
 * 
 * Cara pakai:
 * 1. Edit nomor test di bawah
 * 2. Jalankan: php test-fonnte-api-manual.php
 * 3. Cek WhatsApp apakah pesan masuk
 */

echo "=== TEST FONNTE API MANUAL ===\n\n";

// Konfigurasi
$token = 'NkeAHMTNUSHGyN9wNHGX'; // Token dari .env
$testPhone = '089672626577'; // GANTI dengan nomor test Anda (format: 08xxx)

echo "1. Konfigurasi:\n";
echo "   Token: " . substr($token, 0, 10) . "...\n";
echo "   Test Phone: $testPhone\n\n";

// Format nomor
$phone = preg_replace('/[^0-9]/', '', $testPhone);
if (substr($phone, 0, 1) === '0') {
    $phone = '62' . substr($phone, 1);
} elseif (substr($phone, 0, 2) !== '62') {
    $phone = '62' . $phone;
}

echo "2. Format Nomor:\n";
echo "   Input: $testPhone\n";
echo "   Output: $phone\n\n";

// Pesan test
$message = "*TEST FORGOT PASSWORD - HM TOUR* 🔐\n\n";
$message .= "Halo, ini adalah test pesan reset password.\n\n";
$message .= "Jika Anda menerima pesan ini, berarti konfigurasi Fonnte sudah benar! ✅\n\n";
$message .= "Link reset password (contoh):\n";
$message .= "https://poshan.my.id/hm/affiliate/reset-password?token=xxx\n\n";
$message .= "Terima kasih! 🙏\n\n";
$message .= "_HM Tour - Your Trusted Travel Partner_";

echo "3. Pesan:\n";
echo "   Length: " . strlen($message) . " karakter\n";
echo "   Preview:\n";
echo "   " . str_replace("\n", "\n   ", substr($message, 0, 200)) . "...\n\n";

// Kirim via Fonnte
echo "4. Mengirim ke Fonnte API...\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query([
        'target' => $phone,
        'message' => $message,
        'countryCode' => '62',
    ]),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $token,
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);
curl_close($curl);

echo "\n5. Response:\n";
echo "   HTTP Code: $httpCode\n";

if ($curlError) {
    echo "   ❌ cURL Error: $curlError\n";
} else {
    echo "   ✅ No cURL Error\n";
}

echo "   Raw Response:\n";
echo "   " . str_replace("\n", "\n   ", $response) . "\n\n";

// Parse response
$result = json_decode($response, true);

echo "6. Parsed Response:\n";
if ($result) {
    foreach ($result as $key => $value) {
        if (is_array($value)) {
            echo "   $key: " . json_encode($value) . "\n";
        } else {
            echo "   $key: $value\n";
        }
    }
} else {
    echo "   ⚠️  Response bukan JSON valid\n";
}

echo "\n";

// Kesimpulan
echo "=== KESIMPULAN ===\n";

if ($httpCode === 200) {
    if (isset($result['status']) && ($result['status'] === true || $result['status'] === 'success')) {
        echo "✅ SUKSES! Pesan berhasil dikirim.\n";
        echo "   Cek WhatsApp Anda di nomor: $testPhone\n";
    } else {
        echo "⚠️  HTTP 200 tapi status tidak jelas.\n";
        echo "   Response: " . json_encode($result) . "\n";
        echo "   Cek WhatsApp Anda untuk memastikan.\n";
    }
} else {
    echo "❌ GAGAL! HTTP Code: $httpCode\n";
    
    if ($httpCode === 401) {
        echo "   Penyebab: Token tidak valid atau expired\n";
        echo "   Solusi:\n";
        echo "   1. Login ke https://fonnte.com\n";
        echo "   2. Generate token baru\n";
        echo "   3. Update di .env: FONNTE_TOKEN=token_baru\n";
        echo "   4. Jalankan: php artisan config:clear\n";
    } elseif ($httpCode === 403) {
        echo "   Penyebab: Device WhatsApp tidak terkoneksi\n";
        echo "   Solusi:\n";
        echo "   1. Login ke https://fonnte.com\n";
        echo "   2. Cek status device\n";
        echo "   3. Jika disconnected, scan QR code lagi\n";
    } elseif ($httpCode === 429) {
        echo "   Penyebab: Rate limit (terlalu banyak request)\n";
        echo "   Solusi: Tunggu beberapa menit dan coba lagi\n";
    } elseif ($httpCode === 500) {
        echo "   Penyebab: Server Fonnte error\n";
        echo "   Solusi: Tunggu beberapa menit dan coba lagi\n";
    } else {
        echo "   Penyebab: Unknown error\n";
        echo "   Solusi: Cek response di atas untuk detail\n";
    }
}

echo "\n";

// Next steps
echo "=== NEXT STEPS ===\n";
echo "1. Jika SUKSES:\n";
echo "   - Coba test di halaman forgot password\n";
echo "   - Buka: https://poshan.my.id/hm/affiliate/forgot-password\n";
echo "   - Masukkan email: edun.vr.ar@gmail.com\n";
echo "   - Cek WhatsApp untuk link reset\n\n";

echo "2. Jika GAGAL:\n";
echo "   - Cek token di dashboard Fonnte\n";
echo "   - Pastikan device terkoneksi\n";
echo "   - Coba ganti nomor test\n";
echo "   - Baca TROUBLESHOOTING_FORGOT_PASSWORD_WA.md\n\n";

echo "3. Monitoring:\n";
echo "   - Cek log Laravel: storage/logs/laravel.log\n";
echo "   - Cek dashboard Fonnte untuk history pesan\n\n";

echo "📝 Dokumentasi lengkap: FORGOT_PASSWORD_WA_FIX_COMPLETE.md\n";
echo "\n";
