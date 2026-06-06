<?php
/**
 * Fix: personal_access_tokens.id tidak punya AUTO_INCREMENT
 * Error: Field 'id' doesn't have a default value
 *
 * Akses: https://hmtourtravel.com/fix-sanctum-token-table.php
 * HAPUS FILE INI SETELAH DIJALANKAN!
 */

// Dari error log: vendor ada di public_html/vendor (bukan laravel_app)
// Path: /home/u127727849/domains/hmtourtravel.com/public_html/vendor/...

define('LARAVEL_START', microtime(true));

// Coba berbagai path autoload
$paths = [
    __DIR__ . '/vendor/autoload.php',           // vendor di public_html
    __DIR__ . '/../laravel_app/vendor/autoload.php',  // vendor di laravel_app
    __DIR__ . '/../vendor/autoload.php',         // vendor satu level atas
];

$autoloaded = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloaded = true;
        echo "<p style='color:green'>✅ Autoload dari: <code>" . htmlspecialchars($path) . "</code></p>";
        break;
    }
}

if (!$autoloaded) {
    die("<p style='color:red'>❌ vendor/autoload.php tidak ditemukan di path manapun.</p>");
}

// Coba load bootstrap
$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    __DIR__ . '/../laravel_app/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
];

$app = null;
foreach ($bootstrapPaths as $bPath) {
    if (file_exists($bPath)) {
        $app = require_once $bPath;
        echo "<p style='color:green'>✅ Bootstrap dari: <code>" . htmlspecialchars($bPath) . "</code></p>";
        break;
    }
}

if (!$app) {
    die("<p style='color:red'>❌ bootstrap/app.php tidak ditemukan.</p>");
}

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<style>
    body{font-family:Arial;padding:20px;max-width:900px;margin:0 auto;}
    .ok{color:green;font-weight:bold}
    .err{color:red;font-weight:bold}
    .warn{color:orange;font-weight:bold}
    pre{background:#f4f4f4;padding:15px;border-radius:4px;font-size:13px;}
    h2{border-bottom:2px solid #333;padding-bottom:8px;}
</style>";

echo "<h2>🔧 Fix Sanctum personal_access_tokens</h2>";

use Illuminate\Support\Facades\DB;

try {
    // ── 1. Cek & fix personal_access_tokens.id ────────────────────────────
    echo "<h3>1. Tabel personal_access_tokens</h3>";

    // Cek apakah tabel ada
    $tableExists = DB::select("
        SELECT COUNT(*) as cnt
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'personal_access_tokens'
    ");

    if (($tableExists[0]->cnt ?? 0) == 0) {
        echo "<p class='err'>❌ Tabel personal_access_tokens TIDAK ADA.</p>";
        echo "<p>Perlu buat tabel. Menjalankan migration...</p>";

        // Buat tabel manual
        DB::statement("
            CREATE TABLE `personal_access_tokens` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `tokenable_type` VARCHAR(255) NOT NULL,
                `tokenable_id` BIGINT UNSIGNED NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `token` VARCHAR(64) NOT NULL UNIQUE,
                `abilities` TEXT NULL,
                `last_used_at` TIMESTAMP NULL,
                `expires_at` TIMESTAMP NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p class='ok'>✅ Tabel berhasil dibuat!</p>";
    } else {
        // Tabel ada — cek kolom id
        $columns = DB::select("
            SELECT COLUMN_NAME, COLUMN_TYPE, EXTRA, COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'personal_access_tokens' 
              AND COLUMN_NAME = 'id'
        ");

        $col   = $columns[0];
        $extra = strtolower($col->EXTRA ?? '');

        echo "<pre>";
        echo "Tipe   : " . $col->COLUMN_TYPE . "\n";
        echo "Extra  : " . ($col->EXTRA ?: '(kosong — ini masalahnya!)') . "\n";
        echo "Default: " . ($col->COLUMN_DEFAULT ?? 'NULL') . "\n";
        echo "</pre>";

        if (strpos($extra, 'auto_increment') !== false) {
            echo "<p class='ok'>✅ Kolom id sudah AUTO_INCREMENT. Tidak perlu fix.</p>";
        } else {
            echo "<p class='warn'>⚠️ Kolom id BELUM AUTO_INCREMENT — sedang memperbaiki...</p>";

            DB::statement("
                ALTER TABLE `personal_access_tokens` 
                MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
            ");

            // Verifikasi
            $v = DB::select("
                SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'personal_access_tokens' 
                  AND COLUMN_NAME = 'id'
            ");
            $newExtra = strtolower($v[0]->EXTRA ?? '');

            if (strpos($newExtra, 'auto_increment') !== false) {
                echo "<p class='ok'>✅ BERHASIL DIPERBAIKI! Login Flutter seharusnya berfungsi sekarang.</p>";
            } else {
                echo "<p class='err'>❌ Gagal. Coba perbaiki manual via phpMyAdmin.</p>";
            }
        }
    }

    // ── 2. Cek kolom GPS di attendances ───────────────────────────────────
    echo "<h3>2. Kolom GPS di tabel attendances</h3>";
    $gpsColumns = ['source', 'latitude', 'longitude', 'location_address', 'device_info'];
    $missingCols = [];

    foreach ($gpsColumns as $colName) {
        $exists = DB::select("
            SELECT COUNT(*) as cnt
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'attendances'
              AND COLUMN_NAME = ?
        ", [$colName]);

        $found = ($exists[0]->cnt ?? 0) > 0;
        echo "<p class='" . ($found ? 'ok' : 'err') . "'>";
        echo $found ? "✅" : "❌";
        echo " <code>$colName</code>: " . ($found ? 'ADA' : 'TIDAK ADA');
        echo "</p>";

        if (!$found) $missingCols[] = $colName;
    }

    if (!empty($missingCols)) {
        echo "<p class='warn'>⚠️ Ada kolom GPS yang belum ada. Menambahkan...</p>";

        $definitions = [
            'source'           => "VARCHAR(20) NOT NULL DEFAULT 'fingerprint'",
            'latitude'         => "DECIMAL(10,7) NULL",
            'longitude'        => "DECIMAL(10,7) NULL",
            'location_address' => "VARCHAR(500) NULL",
            'device_info'      => "VARCHAR(255) NULL",
        ];

        foreach ($missingCols as $colName) {
            $def = $definitions[$colName];
            DB::statement("ALTER TABLE `attendances` ADD COLUMN `$colName` $def");
            echo "<p class='ok'>✅ Kolom <code>$colName</code> berhasil ditambahkan.</p>";
        }
    } else {
        echo "<p class='ok'>✅ Semua kolom GPS sudah ada.</p>";
    }

    // ── 3. Cek tabel migrations ───────────────────────────────────────────
    echo "<h3>3. Status Migrations</h3>";
    $ran = DB::table('migrations')
        ->where('migration', 'like', '%sanctum%')
        ->orWhere('migration', 'like', '%personal_access%')
        ->orWhere('migration', 'like', '%online_attendance%')
        ->get();

    if ($ran->isEmpty()) {
        echo "<p class='warn'>⚠️ Migration Sanctum & GPS attendance belum tercatat di tabel migrations.</p>";
    } else {
        foreach ($ran as $m) {
            echo "<p class='ok'>✅ " . $m->migration . "</p>";
        }
    }

    echo "<hr>";
    echo "<h3>✅ Selesai! Langkah selanjutnya:</h3>";
    echo "<ol>";
    echo "<li>Buka aplikasi Flutter di Android</li>";
    echo "<li>Login dengan email & password yang sama dengan web</li>";
    echo "<li>Jika berhasil, hapus file ini dari Hostinger</li>";
    echo "</ol>";

} catch (\Exception $e) {
    echo "<p class='err'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars(substr($e->getTraceAsString(), 0, 2000)) . "</pre>";
}

echo "<p style='color:red;margin-top:30px;font-weight:bold;'>🔒 PENTING: HAPUS file ini setelah selesai!</p>";
?>
