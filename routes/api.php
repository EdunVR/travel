<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceManagementController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProspekController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('morra')->group(function () {
    // API untuk absensi fingerprint
    Route::post('/api/attendance', [AttendanceManagementController::class, 'storeFromFingerprint']);
    Route::get('/api/available-fingerprint-id', [AttendanceManagementController::class, 'getAvailableFingerprintId']);
    Route::get('/api/employee/{fingerprint_id}', [AttendanceManagementController::class, 'getEmployeeByFingerprint']);
    Route::get('/api/today-attendance/{fingerprint_id}', [AttendanceManagementController::class, 'getTodayAttendance']);
    
    // API untuk ESP32 CAM RFID
    Route::post('/api/rfid/register', [AttendanceManagementController::class, 'registerRfidCard']);
    Route::get('/api/rfid/mode', [AttendanceManagementController::class, 'getRfidMode']);
    Route::post('/api/rfid/mode', [AttendanceManagementController::class, 'setRfidMode']);
    Route::post('/api/rfid/card-detected', [AttendanceManagementController::class, 'handleCardDetected']);
    
    // API untuk RFID Time Sync (ESP32)
    Route::get('/api/rfid/time', function() {
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
    
    // API untuk RFID Register UID (auto-fill form)
    Route::post('/api/rfid/register-uid', function(Request $request) {
        $uid = strtoupper($request->input('uid')); // Normalize ke uppercase
        
        // Store UID di tabel rfid_settings
        \DB::table('rfid_settings')->updateOrInsert(
            ['key' => 'detected_uid'],
            ['value' => $uid, 'updated_at' => now()]
        );
        
        // JANGAN reset mode ke attendance di sini!
        // Mode tetap "register" sampai user menyimpan UID ke karyawan di form web.
        // Mode akan dikembalikan ke attendance oleh registerRfidCard() setelah user klik Save.
        
        \Log::info('RFID UID stored in DB (mode kept as register)', ['uid' => $uid]);
        
        return response()->json([
            'success' => true,
            'message' => 'UID received and stored. Waiting for admin to assign to employee.',
            'uid' => $uid
        ]);
    });
    
    // API untuk Clear RFID UID (saat mode berubah)
    Route::post('/api/rfid/clear-uid', function() {
        \DB::table('rfid_settings')->updateOrInsert(
            ['key' => 'detected_uid'],
            ['value' => null, 'updated_at' => now()]
        );
        
        \Log::info('RFID UID cleared');
        
        return response()->json([
            'success' => true,
            'message' => 'UID cleared'
        ]);
    });
    
    // API untuk Time Settings
    Route::get('/api/attendance/time-settings', [AttendanceManagementController::class, 'getTimeSettings']);
    Route::post('/api/attendance/time-settings', [AttendanceManagementController::class, 'updateTimeSettings']);
    Route::post('/api/attendance/test-time-period', [AttendanceManagementController::class, 'testTimePeriod']);

    // =============================================
    // API untuk Announcement / TTS ke Mesin Absensi
    // =============================================

    // Admin kirim teks → generate audio via Google TTS → simpan di server
    Route::post('/api/rfid/announce', function(Request $request) {
        $text = $request->input('text', '');
        if (empty($text)) {
            return response()->json(['success' => false, 'message' => 'Text kosong']);
        }

        // Generate audio via Google TTS (tidak butuh API key, pakai endpoint publik)
        $encodedText = urlencode($text);
        $ttsUrl = "https://translate.google.com/translate_tts?ie=UTF-8&q={$encodedText}&tl=id&client=tw-ob";

        $audioData = null;
        try {
            $ch = curl_init($ttsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $audioData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($audioData) || strlen($audioData) < 1000) {
                \Log::error('Google TTS failed', ['code' => $httpCode, 'text' => $text, 'size' => strlen($audioData ?? '')]);
                return response()->json(['success' => false, 'message' => 'Gagal generate audio dari Google TTS (HTTP ' . $httpCode . ')']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }

        // Pastikan folder ada
        $storageDir = storage_path('app/public/announcements');
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        // Simpan langsung ke filesystem (bukan Storage::put agar lebih reliable)
        $filename = 'announce_' . time() . '.mp3';
        $filePath = $storageDir . '/' . $filename;
        
        $written = file_put_contents($filePath, $audioData);
        if ($written === false) {
            \Log::error('Failed to write announcement file', ['path' => $filePath]);
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan file audio']);
        }

        $publicUrl = url('storage/announcements/' . $filename);

        // Simpan URL di rfid_settings agar ESP32 bisa polling
        \DB::table('rfid_settings')->updateOrInsert(
            ['key' => 'announcement_url'],
            ['value' => $publicUrl, 'updated_at' => now()]
        );
        \DB::table('rfid_settings')->updateOrInsert(
            ['key' => 'announcement_text'],
            ['value' => $text, 'updated_at' => now()]
        );
        \DB::table('rfid_settings')->updateOrInsert(
            ['key' => 'announcement_played'],
            ['value' => '0', 'updated_at' => now()]
        );

        \Log::info('Announcement generated', ['text' => $text, 'url' => $publicUrl, 'size' => $written]);

        return response()->json([
            'success' => true,
            'message' => 'Audio berhasil dibuat',
            'text' => $text,
            'url' => $publicUrl
        ]);
    });

    // ESP32 polling: cek apakah ada announcement baru
    Route::get('/api/rfid/announcement', function() {
        $urlRow = \DB::table('rfid_settings')->where('key', 'announcement_url')->first();
        $playedRow = \DB::table('rfid_settings')->where('key', 'announcement_played')->first();

        $url = $urlRow ? $urlRow->value : null;
        $played = $playedRow ? $playedRow->value : '1';

        if ($url && $played === '0') {
            // Mark sebagai sudah dikirim ke ESP32 (bukan sudah diplay)
            \DB::table('rfid_settings')->where('key', 'announcement_played')->update(['value' => '1', 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'has_announcement' => true,
                'url' => $url
            ]);
        }

        return response()->json([
            'success' => true,
            'has_announcement' => false
        ]);
    });
});

// Legacy routes (for backward compatibility)
Route::post('/attendance', [AttendanceManagementController::class, 'storeApi']);

Route::get('/available-fingerprint-id', [FingerprintController::class, 'getAvailableId']);
Route::get('/employee/{fingerprint_id}', [FingerprintController::class, 'getEmployeeByFingerprintId']);

Route::group(['prefix' => 'api'], function() {
    Route::get('/wilayah/kabupaten/{provinsi_id}', [WilayahController::class, 'getKabupaten'])->name('api.wilayah.kabupaten');
    Route::get('/wilayah/kecamatan/{kabupaten_id}', [WilayahController::class, 'getKecamatan'])->name('api.wilayah.kecamatan');
    Route::get('/wilayah/desa/{kecamatan_id}', [WilayahController::class, 'getDesa'])->name('api.wilayah.desa');
    
    // Tambahkan endpoint untuk produk
    Route::get('/products', [ProdukController::class, 'apiIndex']);
    Route::get('/categories', [ProdukController::class, 'apiCategories']);
});

// API untuk outlets (untuk dashboard) - di luar group api karena sudah ada prefix api di URL
Route::get('/outlets', function() {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Outlet::where('is_active', true)
            ->orderBy('nama_outlet')
            ->get(['id_outlet', 'nama_outlet'])
    ]);
})->middleware('web');

Route::get('/produk/search', [ProdukController::class, 'search']);
Route::get('/produk/{id}/components', [ProdukController::class, 'getComponents']);
Route::get('/categories', [ProdukController::class, 'apiCategories']);
Route::get('/products/{id}', [ProdukController::class, 'apiShow']);
Route::get('/prospek/locations', [ProspekController::class, 'getLocations']);

Route::get('/investor/{investor}/accounts', function($investorId) {
    $accounts = App\Models\InvestorAccount::where('investor_id', $investorId)
        ->where('status', 'active')
        ->get()
        ->map(function($account) {
            return [
                'id' => $account->id,
                'account_number' => $account->account_number,
                'bank_name' => $account->bank_name,
                'total_investment' => $account->total_investment,
                'status' => $account->status
            ];
        });
    
    return response()->json($accounts);
});

Route::get('/investors/search', function(Request $request) {
    $query = $request->q;
    $investors = App\Models\Investor::where('status', 'active') // Hanya ambil yang aktif
        ->where('name', 'like', "%{$query}%")
        ->paginate(10);
    
    return response()->json($investors);
});

// API untuk mengecek UID RFID yang terdeteksi
Route::get('/detected-rfid-uid', function() {
    $row = \DB::table('rfid_settings')->where('key', 'detected_uid')->first();
    $uid = $row ? $row->value : null;
    
    if ($uid) {
        // Clear UID after reading (one-time read)
        \DB::table('rfid_settings')->where('key', 'detected_uid')->update(['value' => null, 'updated_at' => now()]);
        
        return response()->json([
            'success' => true,
            'uid' => $uid
        ]);
    }
    
    return response()->json([
        'success' => false,
        'uid' => null
    ]);
});


// ─── Mobile Attendance API (Flutter App) ──────────────────────────────────────
Route::prefix('mobile/v1')->group(function () {
    // Public — tidak perlu auth
    Route::post('/login', [\App\Http\Controllers\Api\MobileAttendanceController::class, 'login']);

    // Protected — butuh Sanctum token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',        [\App\Http\Controllers\Api\MobileAttendanceController::class, 'logout']);
        Route::get('/attendance/today',  [\App\Http\Controllers\Api\MobileAttendanceController::class, 'todayStatus']);
        Route::post('/attendance/clock-in',  [\App\Http\Controllers\Api\MobileAttendanceController::class, 'clockIn']);
        Route::post('/attendance/clock-out', [\App\Http\Controllers\Api\MobileAttendanceController::class, 'clockOut']);
        Route::get('/attendance/history',    [\App\Http\Controllers\Api\MobileAttendanceController::class, 'history']);
    });
});
