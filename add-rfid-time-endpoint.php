<?php
/**
 * RFID Time Endpoint - Add to Laravel
 * 
 * CARA INSTALL:
 * 1. Buka routes/api.php
 * 2. Tambahkan route di bawah ini
 * 3. Test: https://poshan.my.id/hm/api/morra/api/rfid/time
 */

// ============================================
// TAMBAHKAN INI DI routes/api.php
// ============================================

/*
Route::get('/morra/api/rfid/time', function() {
    // Set timezone ke WIB (Waktu Indonesia Barat)
    date_default_timezone_set('Asia/Jakarta');
    
    return response()->json([
        'success' => true,
        'time' => date('H:i:s'),
        'date' => date('Y-m-d'),
        'day' => date('l'),
        'timestamp' => time(),
        'timezone' => 'Asia/Jakarta (WIB)'
    ]);
});
*/

// ============================================
// ATAU JIKA SUDAH ADA RfidController
// ============================================

/*
// Di RfidController.php, tambahkan method:

public function getTime()
{
    date_default_timezone_set('Asia/Jakarta');
    
    return response()->json([
        'success' => true,
        'time' => date('H:i:s'),
        'date' => date('Y-m-d'),
        'day' => date('l'),
        'timestamp' => time(),
        'timezone' => 'Asia/Jakarta (WIB)'
    ]);
}

// Di routes/api.php:
Route::get('/morra/api/rfid/time', [RfidController::class, 'getTime']);
*/

// ============================================
// TEST SCRIPT (Jalankan file ini untuk test)
// ============================================

echo "=== RFID TIME ENDPOINT TEST ===\n\n";

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Simulate response
$response = [
    'success' => true,
    'time' => date('H:i:s'),
    'date' => date('Y-m-d'),
    'day' => date('l'),
    'timestamp' => time(),
    'timezone' => 'Asia/Jakarta (WIB)'
];

echo "Response yang akan dikirim ke ESP32:\n";
echo json_encode($response, JSON_PRETTY_PRINT);
echo "\n\n";

echo "Waktu saat ini (WIB): " . date('H:i:s') . "\n";
echo "Tanggal: " . date('Y-m-d') . "\n";
echo "Hari: " . date('l') . "\n";
echo "\n";

echo "✅ Jika waktu di atas benar, endpoint siap digunakan!\n";
echo "\n";
echo "NEXT STEPS:\n";
echo "1. Copy route di atas ke routes/api.php\n";
echo "2. Test: curl https://poshan.my.id/hm/api/morra/api/rfid/time\n";
echo "3. Upload code ESP32\n";
echo "4. Check Serial Monitor untuk 'Time synced'\n";
?>
