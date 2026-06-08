<?php
/**
 * Patch: Tambah kolom selfie_in dan selfie_out ke tabel attendances
 * Akses: https://hmtourtravel.com/patch-selfie-attendance.php
 * HAPUS SETELAH DIJALANKAN!
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

echo "<style>body{font-family:Arial;padding:20px;max-width:700px}
.ok{color:green;font-weight:bold}.err{color:red;font-weight:bold}</style>";
echo "<h2>🔧 Patch: Selfie Attendance</h2>";
$errors = 0;

// Tambah kolom selfie_in
try {
    if (Schema::hasColumn('attendances', 'selfie_in')) {
        echo "<p class='ok'>✅ selfie_in sudah ada.</p>";
    } else {
        DB::statement("ALTER TABLE `attendances` ADD COLUMN `selfie_in` VARCHAR(255) NULL AFTER `device_info`");
        echo "<p class='ok'>✅ selfie_in ditambahkan.</p>";
    }
} catch (\Exception $e) {
    echo "<p class='err'>❌ selfie_in: " . htmlspecialchars($e->getMessage()) . "</p>"; $errors++;
}

// Tambah kolom selfie_out
try {
    if (Schema::hasColumn('attendances', 'selfie_out')) {
        echo "<p class='ok'>✅ selfie_out sudah ada.</p>";
    } else {
        DB::statement("ALTER TABLE `attendances` ADD COLUMN `selfie_out` VARCHAR(255) NULL AFTER `selfie_in`");
        echo "<p class='ok'>✅ selfie_out ditambahkan.</p>";
    }
} catch (\Exception $e) {
    echo "<p class='err'>❌ selfie_out: " . htmlspecialchars($e->getMessage()) . "</p>"; $errors++;
}

// Catat ke migrations
try {
    $migName = '2026_06_08_000005_add_selfie_to_attendances_table';
    if (!DB::table('migrations')->where('migration', $migName)->exists()) {
        $maxBatch = DB::table('migrations')->max('batch') ?? 0;
        DB::table('migrations')->insert(['migration' => $migName, 'batch' => $maxBatch + 1]);
        echo "<p class='ok'>✅ Migration dicatat.</p>";
    } else {
        echo "<p class='ok'>✅ Migration sudah tercatat.</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:orange'>⚠️ Tidak bisa catat migration: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Pastikan storage link ada
try {
    if (!is_dir(public_path('storage'))) {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        echo "<p class='ok'>✅ Storage link dibuat.</p>";
    } else {
        echo "<p class='ok'>✅ Storage link sudah ada.</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:orange'>⚠️ storage:link: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo $errors === 0
    ? "<p class='ok' style='font-size:1.1em'>✅ Patch selesai.</p>"
    : "<p class='err' style='font-size:1.1em'>❌ Ada $errors error.</p>";
echo "<p style='color:red;font-weight:bold;margin-top:20px'>🔒 HAPUS file ini!</p>";
?>
