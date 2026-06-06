<?php
/**
 * PATCH: Tambah GPS fields ke getDailyTable response
 * Akses: https://hmtourtravel.com/patch-attendance-gps.php
 * HAPUS file ini setelah selesai.
 */

define('LARAVEL_START', microtime(true));

// Temukan autoload
$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../laravel_app/vendor/autoload.php',
];
foreach ($autoloadPaths as $p) {
    if (file_exists($p)) { require_once $p; break; }
}

echo "<!DOCTYPE html><html><head><title>GPS Patch</title>";
echo "<style>body{font-family:monospace;padding:20px;max-width:900px} .ok{color:green;font-weight:bold} .err{color:red;font-weight:bold} .warn{color:orange;font-weight:bold} pre{background:#f4f4f4;padding:12px;overflow:auto;font-size:13px;border-radius:4px;} a.btn{display:inline-block;background:green;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin:5px;} a.btn-red{background:red;}</style>";
echo "</head><body>";
echo "<h2>🔧 GPS Attendance Patch Tool</h2>";

// Cari controller
$controllerPaths = [
    __DIR__ . '/app/Http/Controllers/AttendanceManagementController.php',
    __DIR__ . '/../app/Http/Controllers/AttendanceManagementController.php',
];

$controllerPath = null;
foreach ($controllerPaths as $cp) {
    if (file_exists($cp)) {
        $controllerPath = realpath($cp);
        break;
    }
}

if (!$controllerPath) {
    echo "<p class='err'>❌ Controller tidak ditemukan. Cek path Hostinger.</p></body></html>";
    exit;
}

echo "<p class='ok'>✅ Controller: <code>" . htmlspecialchars($controllerPath) . "</code></p>";

$content = file_get_contents($controllerPath);

// Deteksi apakah GPS sudah ada
$alreadyPatched = strpos($content, 'GPS / online attendance fields') !== false
               || strpos($content, "'latitude'         => \$attendance->latitude") !== false;

if ($alreadyPatched) {
    echo "<p class='ok'>✅ Controller SUDAH mengandung GPS fields.</p>";
    echo "<p>Masalah mungkin ada di <strong>Laravel view cache</strong> atau <strong>OPcache PHP</strong>.</p>";

    // Clear caches
    if (isset($_GET['clear_cache'])) {
        // Bootstrap Laravel untuk artisan-style cache clear
        try {
            $bootstrapPaths = [
                __DIR__ . '/bootstrap/app.php',
                __DIR__ . '/../bootstrap/app.php',
            ];
            foreach ($bootstrapPaths as $bp) {
                if (file_exists($bp)) {
                    $app = require $bp;
                    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
                    break;
                }
            }

            // Clear compiled views
            $viewCachePath = storage_path('framework/views');
            if (is_dir($viewCachePath)) {
                $files = glob($viewCachePath . '/*.php');
                foreach ($files as $f) { @unlink($f); }
                echo "<p class='ok'>✅ View cache cleared (" . count($files) . " files)</p>";
            }

            // Clear application cache
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            echo "<p class='ok'>✅ Application cache cleared</p>";

            // Clear OPcache
            if (function_exists('opcache_reset')) {
                opcache_reset();
                echo "<p class='ok'>✅ OPcache cleared</p>";
            } else {
                echo "<p class='warn'>⚠️ OPcache tidak tersedia atau tidak bisa di-reset</p>";
            }

            echo "<p class='ok'><strong>✅ Semua cache berhasil dihapus. Refresh halaman manajemen absensi.</strong></p>";
        } catch (\Exception $e) {
            echo "<p class='err'>❌ Error clearing cache: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<a href='?clear_cache=1' class='btn'>Clear Laravel Cache & OPcache</a>";
    }

} else {
    echo "<p class='err'>❌ Controller BELUM mengandung GPS fields.</p>";

    // Cari pattern yang akan di-patch
    $patterns = [
        "'notes' => \$attendance->notes ?? null,",
        "'notes' => \$attendance->notes,",
        "'notes' => \$attendance->notes ?? null",
    ];

    $foundPattern = null;
    foreach ($patterns as $pat) {
        if (strpos($content, $pat) !== false) {
            $foundPattern = $pat;
            break;
        }
    }

    if (!$foundPattern) {
        echo "<p class='err'>❌ Pattern 'notes' tidak ditemukan. Cari manual:</p>";
        preg_match('/\'notes\'.*?\n/', $content, $m);
        echo "<pre>" . htmlspecialchars($m[0] ?? 'tidak ditemukan') . "</pre>";
        echo "<p>Upload manual file <code>AttendanceManagementController.php</code></p>";
    } else {
        echo "<p class='ok'>✅ Pattern ditemukan: <code>" . htmlspecialchars($foundPattern) . "</code></p>";

        $gpsAddition = "\n                    // GPS / online attendance fields\n" .
                       "                    'source'           => \$attendance->source ?? 'fingerprint',\n" .
                       "                    'latitude'         => \$attendance->latitude ?? null,\n" .
                       "                    'longitude'        => \$attendance->longitude ?? null,\n" .
                       "                    'location_address' => \$attendance->location_address ?? null,\n" .
                       "                    'device_info'      => \$attendance->device_info ?? null,\n" .
                       "                    // GPS clock-out\n" .
                       "                    'clock_out_latitude'  => \$attendance->clock_out_latitude  ?? null,\n" .
                       "                    'clock_out_longitude' => \$attendance->clock_out_longitude ?? null,\n" .
                       "                    'clock_out_address'   => \$attendance->clock_out_address   ?? null,";

        if (isset($_GET['apply'])) {
            // Backup
            $backup = $controllerPath . '.bak.' . date('YmdHis');
            file_put_contents($backup, $content);

            // Patch — hanya replace occurrence PERTAMA (di getDailyTable)
            $pos      = strpos($content, $foundPattern);
            $patched  = substr($content, 0, $pos + strlen($foundPattern))
                      . $gpsAddition
                      . substr($content, $pos + strlen($foundPattern));

            if (file_put_contents($controllerPath, $patched) !== false) {
                // Clear OPcache
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($controllerPath, true);
                }
                echo "<p class='ok'>✅ Patch berhasil! Backup: <code>" . basename($backup) . "</code></p>";
                echo "<a href='?clear_cache=1' class='btn'>Lanjut: Clear Cache</a>";
            } else {
                echo "<p class='err'>❌ Gagal menulis file. Cek permission file controller.</p>";
                echo "<pre>" . htmlspecialchars($gpsAddition) . "</pre>";
            }
        } else {
            echo "<h3>Preview patch yang akan ditambahkan:</h3>";
            echo "<pre>" . htmlspecialchars($gpsAddition) . "</pre>";
            echo "<a href='?apply=1' class='btn'>Apply Patch Sekarang</a>";
        }
    }
}

echo "<hr><p style='color:red;font-weight:bold'>🔒 HAPUS file ini setelah selesai: <code>patch-attendance-gps.php</code></p>";
echo "</body></html>";
