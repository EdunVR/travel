<?php
/**
 * Diagnosa: cek apakah selfie tersimpan di DB dan storage
 * Akses: https://hmtourtravel.com/check-selfie.php
 * HAPUS SETELAH DIGUNAKAN
 */
define('LARAVEL_START', microtime(true));
$paths = [__DIR__.'/vendor/autoload.php', __DIR__.'/../vendor/autoload.php'];
foreach ($paths as $p) { if (file_exists($p)) { require_once $p; break; } }
$bPaths = [__DIR__.'/bootstrap/app.php', __DIR__.'/../bootstrap/app.php'];
$app = null;
foreach ($bPaths as $b) { if (file_exists($b)) { $app = require_once $b; break; } }
if (!$app) die('bootstrap not found');
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

echo "<style>body{font-family:Arial;padding:20px;max-width:900px}
.ok{color:green;font-weight:bold}.err{color:red;font-weight:bold}.warn{color:orange;font-weight:bold}
pre{background:#f4f4f4;padding:10px;border-radius:4px;font-size:12px;overflow-x:auto}
img{max-width:200px;border-radius:8px;border:1px solid #ddd;margin:4px}
table{width:100%;border-collapse:collapse;font-size:12px}
th,td{border:1px solid #ddd;padding:6px 8px;text-align:left}th{background:#f4f4f4}</style>";

echo "<h2>🔍 Selfie Attendance Diagnostik</h2>";

// 1. Cek kolom ada di tabel
echo "<h3>1. Kolom di tabel attendances</h3>";
$hasSelfieIn  = Schema::hasColumn('attendances', 'selfie_in');
$hasSelfieOut = Schema::hasColumn('attendances', 'selfie_out');
echo "<p class='" . ($hasSelfieIn  ? 'ok' : 'err') . "'>" . ($hasSelfieIn  ? '✅' : '❌') . " selfie_in</p>";
echo "<p class='" . ($hasSelfieOut ? 'ok' : 'err') . "'>" . ($hasSelfieOut ? '✅' : '❌') . " selfie_out</p>";

if (!$hasSelfieIn || !$hasSelfieOut) {
    echo "<p class='err'>❌ Kolom belum ada! Jalankan patch-selfie-attendance.php dulu.</p>";
    die();
}

// 2. Cek 10 record terbaru
echo "<h3>2. Record absensi terbaru dengan selfie</h3>";
$records = DB::table('attendances')
    ->whereNotNull('selfie_in')
    ->orWhereNotNull('selfie_out')
    ->orderByDesc('id')
    ->limit(10)
    ->get(['id', 'date', 'employee_name', 'clock_in', 'clock_out', 'selfie_in', 'selfie_out']);

if ($records->isEmpty()) {
    echo "<p class='warn'>⚠️ Belum ada record dengan selfie. Kemungkinan kolom ada tapi foto belum tersimpan.</p>";

    // Cek apakah ada record hari ini tanpa selfie
    $today = DB::table('attendances')->where('date', date('Y-m-d'))->orderByDesc('id')->limit(5)->get();
    if ($today->isNotEmpty()) {
        echo "<h4>Record hari ini (tanpa selfie):</h4>";
        echo "<table><tr><th>ID</th><th>Nama</th><th>Clock In</th><th>selfie_in</th><th>selfie_out</th></tr>";
        foreach ($today as $r) {
            echo "<tr><td>{$r->id}</td><td>{$r->employee_name}</td><td>{$r->clock_in}</td>";
            echo "<td>" . ($r->selfie_in ?? '<span class="err">NULL</span>') . "</td>";
            echo "<td>" . ($r->selfie_out ?? '<span class="err">NULL</span>') . "</td></tr>";
        }
        echo "</table>";
        echo "<p class='warn'>⚠️ Ada absensi hari ini tapi selfie_in dan selfie_out NULL.</p>";
        echo "<p>Kemungkinan penyebab:<br>
        1. APK yang diinstall masih versi lama (sebelum update selfie)<br>
        2. Flutter gagal kirim data selfie (cek response API)<br>
        3. Backend gagal simpan selfie (lihat log Laravel)</p>";
    }
} else {
    echo "<table>";
    echo "<tr><th>ID</th><th>Tanggal</th><th>Nama</th><th>Clock In</th><th>Clock Out</th><th>Selfie Masuk</th><th>Selfie Keluar</th></tr>";
    foreach ($records as $r) {
        echo "<tr>";
        echo "<td>{$r->id}</td><td>{$r->date}</td><td>{$r->employee_name}</td>";
        echo "<td>{$r->clock_in}</td><td>{$r->clock_out}</td>";

        // Selfie In
        if ($r->selfie_in) {
            $fullPath = storage_path('app/public/' . $r->selfie_in);
            $exists   = file_exists($fullPath);
            $url      = url('storage/' . $r->selfie_in);
            echo "<td class='" . ($exists ? 'ok' : 'err') . "'>";
            echo ($exists ? '✅' : '❌ file missing') . "<br>";
            echo "<small><a href='$url' target='_blank'>$url</a></small>";
            if ($exists) echo "<br><img src='$url'>";
            echo "</td>";
        } else {
            echo "<td class='err'>NULL</td>";
        }

        // Selfie Out
        if ($r->selfie_out) {
            $fullPath = storage_path('app/public/' . $r->selfie_out);
            $exists   = file_exists($fullPath);
            $url      = url('storage/' . $r->selfie_out);
            echo "<td class='" . ($exists ? 'ok' : 'err') . "'>";
            echo ($exists ? '✅' : '❌ file missing') . "<br>";
            echo "<small><a href='$url' target='_blank'>$url</a></small>";
            if ($exists) echo "<br><img src='$url'>";
            echo "</td>";
        } else {
            echo "<td class='err'>NULL</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Cek storage/app/public/attendance_selfies directory
echo "<h3>3. Direktori storage selfie</h3>";
$selfieDir = storage_path('app/public/attendance_selfies');
if (is_dir($selfieDir)) {
    $files = glob($selfieDir . '/**/*.jpg');
    if (!$files) $files = glob($selfieDir . '/*.jpg') ?: [];
    $count = count($files);
    echo "<p class='" . ($count > 0 ? 'ok' : 'warn') . "'>" . ($count > 0 ? '✅' : '⚠️') . " $count file selfie ditemukan di storage/app/public/attendance_selfies/</p>";
    if ($count > 0) {
        echo "<p>File terbaru:</p><ul>";
        $latest = array_slice(array_reverse(array_filter($files, 'is_file')), 0, 5);
        foreach ($latest as $f) {
            echo "<li>" . htmlspecialchars(basename($f)) . " (" . round(filesize($f)/1024, 1) . " KB) - " . date('d/m/Y H:i', filemtime($f)) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='err'>❌ Direktori attendance_selfies tidak ada — belum ada selfie tersimpan.</p>";
}

// 4. Cek storage link
echo "<h3>4. Storage Link</h3>";
$storageLink = public_path('storage');
if (is_link($storageLink) || is_dir($storageLink)) {
    echo "<p class='ok'>✅ public/storage symlink ada.</p>";
} else {
    echo "<p class='err'>❌ public/storage TIDAK ADA. Jalankan: php artisan storage:link</p>";
}

// 5. Cek API endpoint — test apakah selfie_in diterima
echo "<h3>5. Cek API response format</h3>";
echo "<p>Untuk debug, buka browser developer tools di Flutter Web atau cek Laravel log saat absen.<br>";
echo "Path log: <code>" . htmlspecialchars(storage_path('logs/laravel.log')) . "</code></p>";

// Tampilkan 20 baris terakhir laravel.log yang berisi 'selfie' atau 'clock-in' atau 'Mobile'
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $relevant = array_filter($lines, fn($l) => stripos($l, 'selfie') !== false || stripos($l, 'Mobile clock') !== false);
    $relevant = array_slice(array_values($relevant), -20);
    if ($relevant) {
        echo "<pre>" . htmlspecialchars(implode('', $relevant)) . "</pre>";
    } else {
        echo "<p class='warn'>⚠️ Tidak ada log 'selfie' atau 'Mobile clock' dalam laravel.log.</p>";
    }
}

echo "<hr><p style='color:red;font-weight:bold'>🔒 HAPUS file ini setelah selesai!</p>";
?>
