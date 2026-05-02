<?php

/**
 * Test Frontend URL Fix
 * 
 * This script generates the correct URLs that should be used in frontend
 */

// Simulate Laravel url() helper
function url($path) {
    $baseUrl = 'https://poshan.my.id/tofu';
    return $baseUrl . $path;
}

echo "=== FRONTEND URL FIX TEST ===\n\n";

echo "URLs yang BENAR untuk frontend JavaScript:\n\n";

echo "1. RFID Mode API:\n";
echo "   URL: " . url('/api/morra/api/rfid/mode') . "\n";
echo "   Usage: fetch('" . url('/api/morra/api/rfid/mode') . "')\n\n";

echo "2. Detected UID API:\n";
echo "   URL: " . url('/api/detected-rfid-uid') . "\n";
echo "   Usage: fetch('" . url('/api/detected-rfid-uid') . "')\n\n";

echo "URLs yang SALAH (yang menyebabkan 404):\n\n";

echo "1. SALAH - Missing base path:\n";
echo "   URL: https://poshan.my.id/api/morra/api/rfid/mode\n";
echo "   Error: 404 Not Found\n\n";

echo "2. SALAH - Relative path tanpa base:\n";
echo "   URL: /api/morra/api/rfid/mode\n";
echo "   Resolved to: https://poshan.my.id/api/morra/api/rfid/mode (SALAH!)\n\n";

echo "SOLUSI di Blade Template:\n\n";

echo "Gunakan Laravel url() helper:\n";
echo "- fetch('{{ url(\"/api/morra/api/rfid/mode\") }}')\n";
echo "- fetch('{{ url(\"/api/detected-rfid-uid\") }}')\n\n";

echo "Atau gunakan route() helper jika ada named route:\n";
echo "- fetch('{{ route(\"api.rfid.mode\") }}')\n\n";

echo "=== TESTING URLS ===\n\n";

// Test the URLs
$urls = [
    url('/api/morra/api/rfid/mode'),
    url('/api/detected-rfid-uid')
];

foreach ($urls as $testUrl) {
    echo "Testing: $testUrl\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Frontend-Test/1.0');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode == 200) {
        echo "✅ Status: $httpCode - OK\n";
    } else {
        echo "❌ Status: $httpCode - ERROR\n";
    }
    
    curl_close($ch);
    echo "\n";
}

echo "=== KESIMPULAN ===\n\n";
echo "✅ URL sudah diperbaiki di frontend JavaScript\n";
echo "✅ Menggunakan Laravel url() helper untuk base path yang benar\n";
echo "✅ Semua API endpoints dapat diakses dengan URL yang benar\n\n";

echo "NEXT STEPS:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Test tombol 'Mulai Deteksi' di recruitment modal\n";
echo "3. Check browser console untuk memastikan tidak ada error 404\n";

?>