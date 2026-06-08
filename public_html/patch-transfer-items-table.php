<?php
/**
 * Patch: Buat tabel transfer_request_items dan tambah kolom nomor_surat_jalan
 * ke permintaan_pengiriman (untuk fitur multi-item per transfer request).
 *
 * Akses: https://hmtourtravel.com/patch-transfer-items-table.php
 * HAPUS FILE INI SETELAH DIJALANKAN!
 */
define('LARAVEL_START', microtime(true));

// ── Bootstrap Laravel ──────────────────────────────────────────────────────
$paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../laravel_app/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];
$autoloaded = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloaded = true;
        break;
    }
}
if (!$autoloaded) die("<p style='color:red'>❌ vendor/autoload.php tidak ditemukan.</p>");

$bootstrapPaths = [
    __DIR__ . '/bootstrap/app.php',
    __DIR__ . '/../laravel_app/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
];
$app = null;
foreach ($bootstrapPaths as $bPath) {
    if (file_exists($bPath)) { $app = require_once $bPath; break; }
}
if (!$app) die("<p style='color:red'>❌ bootstrap/app.php tidak ditemukan.</p>");

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<style>
body{font-family:Arial;padding:20px;max-width:900px;margin:0 auto;line-height:1.6}
.ok{color:#16a34a;font-weight:bold}
.err{color:#dc2626;font-weight:bold}
.warn{color:#d97706;font-weight:bold}
pre{background:#f4f4f4;padding:12px;border-radius:4px;font-size:12px;overflow-x:auto}
h2{border-bottom:2px solid #374151;padding-bottom:8px}
h3{color:#374151}
</style>";
echo "<h2>🔧 Patch: Transfer Gudang Multi-Item</h2>";

$errors = 0;

// ── 1. Kolom nomor_surat_jalan di permintaan_pengiriman ────────────────────
echo "<h3>1. Kolom <code>nomor_surat_jalan</code> di tabel <code>permintaan_pengiriman</code></h3>";
try {
    $exists = DB::select("
        SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = 'permintaan_pengiriman'
          AND COLUMN_NAME  = 'nomor_surat_jalan'
    ");
    if (($exists[0]->cnt ?? 0) > 0) {
        echo "<p class='ok'>✅ Kolom sudah ada — skip.</p>";
    } else {
        DB::statement("ALTER TABLE `permintaan_pengiriman` ADD COLUMN `nomor_surat_jalan` VARCHAR(100) NULL AFTER `status`");
        echo "<p class='ok'>✅ Kolom berhasil ditambahkan.</p>";
    }
} catch (\Exception $e) {
    echo "<p class='err'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $errors++;
}

// ── 2. Tabel transfer_request_items ───────────────────────────────────────
echo "<h3>2. Tabel <code>transfer_request_items</code></h3>";
try {
    $tableExists = DB::select("
        SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = 'transfer_request_items'
    ");
    if (($tableExists[0]->cnt ?? 0) > 0) {
        echo "<p class='ok'>✅ Tabel sudah ada — skip.</p>";
    } else {
        DB::statement("
            CREATE TABLE `transfer_request_items` (
                `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `transfer_request_id`  BIGINT UNSIGNED NOT NULL,
                `item_type`            ENUM('produk','bahan','inventori') NOT NULL,
                `item_id`              BIGINT UNSIGNED NOT NULL,
                `item_name`            VARCHAR(255) NOT NULL,
                `jumlah`               INT NOT NULL DEFAULT 1,
                `unit`                 VARCHAR(50) NULL,
                `created_at`           TIMESTAMP NULL,
                `updated_at`           TIMESTAMP NULL,
                PRIMARY KEY (`id`),
                INDEX `tri_request_idx` (`transfer_request_id`),
                CONSTRAINT `fk_tri_request`
                    FOREIGN KEY (`transfer_request_id`)
                    REFERENCES `permintaan_pengiriman`(`id`)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p class='ok'>✅ Tabel berhasil dibuat.</p>";
    }
} catch (\Exception $e) {
    echo "<p class='err'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $errors++;
}

// ── 3. Catat ke tabel migrations ──────────────────────────────────────────
echo "<h3>3. Mencatat ke tabel <code>migrations</code></h3>";
try {
    $migName = '2026_06_08_000003_add_nomor_surat_jalan_and_items_to_transfer';
    $alreadyRan = DB::table('migrations')->where('migration', $migName)->exists();
    if ($alreadyRan) {
        echo "<p class='ok'>✅ Sudah tercatat di migrations — skip.</p>";
    } else {
        $maxBatch = DB::table('migrations')->max('batch') ?? 0;
        DB::table('migrations')->insert([
            'migration' => $migName,
            'batch'     => $maxBatch + 1,
        ]);
        echo "<p class='ok'>✅ Dicatat sebagai batch " . ($maxBatch + 1) . ".</p>";
    }
} catch (\Exception $e) {
    echo "<p class='warn'>⚠️ Tidak bisa catat ke migrations: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// ── 4. Verifikasi akhir ────────────────────────────────────────────────────
echo "<h3>4. Verifikasi</h3>";
try {
    $cols = DB::select("SHOW COLUMNS FROM `permintaan_pengiriman` LIKE 'nomor_surat_jalan'");
    echo $cols ? "<p class='ok'>✅ permintaan_pengiriman.nomor_surat_jalan: ADA</p>"
               : "<p class='err'>❌ permintaan_pengiriman.nomor_surat_jalan: TIDAK ADA</p>";

    $tbl = DB::select("SHOW TABLES LIKE 'transfer_request_items'");
    echo $tbl ? "<p class='ok'>✅ transfer_request_items: ADA</p>"
              : "<p class='err'>❌ transfer_request_items: TIDAK ADA</p>";

    // Cek struktur kolom tabel baru
    if ($tbl) {
        echo "<details><summary>Struktur tabel transfer_request_items</summary><pre>";
        $structure = DB::select("DESCRIBE `transfer_request_items`");
        foreach ($structure as $col) {
            echo sprintf("%-25s %-30s %s\n", $col->Field, $col->Type, $col->Extra);
        }
        echo "</pre></details>";
    }
} catch (\Exception $e) {
    echo "<p class='err'>❌ Verifikasi error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
if ($errors === 0) {
    echo "<p class='ok' style='font-size:1.1em'>✅ Patch selesai tanpa error. Fitur transfer multi-item sudah aktif.</p>";
} else {
    echo "<p class='err' style='font-size:1.1em'>❌ Ada $errors error. Periksa output di atas.</p>";
}
echo "<p style='color:#dc2626;font-weight:bold;margin-top:20px'>🔒 HAPUS FILE INI setelah selesai: <code>public_html/patch-transfer-items-table.php</code></p>";
?>
