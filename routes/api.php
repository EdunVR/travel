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
        
        // Reset mode ke attendance setelah UID terdeteksi
        \DB::table('rfid_settings')->updateOrInsert(
            ['key' => 'mode'],
            ['value' => 'attendance', 'updated_at' => now()]
        );
        
        \Log::info('RFID UID stored in DB, mode reset to attendance', ['uid' => $uid]);
        
        return response()->json([
            'success' => true,
            'message' => 'UID received and stored, mode reset to attendance',
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