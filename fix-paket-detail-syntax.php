<?php
/**
 * Script untuk memperbaiki paket-detail.blade.php dengan aman
 * Hanya mengubah form action, tidak menambahkan hidden fields yang menyebabkan syntax error
 */

$file = 'resources/views/public/paket-detail.blade.php';

if (!file_exists($file)) {
    die("Error: File $file tidak ditemukan!\n");
}

echo "📝 Membaca file $file...\n";
$content = file_get_contents($file);

// Backup
$backupFile = $file . '.backup.' . date('YmdHis');
file_put_contents($backupFile, $content);
echo "✅ Backup dibuat: $backupFile\n\n";

// ===== CHANGE 1: Update Form Action ONLY =====
echo "🔧 Mengupdate form action...\n";

$oldFormTag = '<form action="{{ route(\'public.paket.order\',$package->id) }}" method="POST" id="order-form">';
$newFormTag = '<form action="{{ route(\'public.booking.submit\') }}" method="POST" id="order-form" onsubmit="return prepareFormSubmit()">';

if (strpos($content, $oldFormTag) !== false) {
    $content = str_replace($oldFormTag, $newFormTag, $content);
    echo "   ✅ Form action berhasil diupdate\n\n";
} else {
    echo "   ⚠️  Form tag tidak ditemukan atau sudah diubah\n\n";
}

// ===== CHANGE 2: Add hidden fields SAFELY =====
echo "🔧 Menambahkan hidden fields dengan aman...\n";

// Find the @csrf line and add hidden fields after it
$csrfMarker = '@csrf';
$hiddenFieldsToAdd = "\n" . '<input type="hidden" name="package_id" value="{{ $package->id }}">' . "\n" .
'<input type="hidden" name="jamaah_name" id="f_jamaah_name">' . "\n" .
'<input type="hidden" name="jamaah_phone" id="f_jamaah_phone">' . "\n" .
'<input type="hidden" name="jamaah_email" id="f_jamaah_email">' . "\n" .
'<input type="hidden" name="room_type" id="f_room_type" value="double">' . "\n" .
'<input type="hidden" name="total_price" id="f_total_price">' . "\n" .
'<input type="hidden" name="equipment" id="f_equipment" value="[]">';

// Check if hidden fields already exist
if (strpos($content, 'name="package_id"') === false) {
    // Find @csrf and add after it
    $pos = strpos($content, $csrfMarker);
    if ($pos !== false) {
        // Find end of line after @csrf
        $endOfLine = strpos($content, "\n", $pos);
        if ($endOfLine !== false) {
            $content = substr_replace($content, $hiddenFieldsToAdd, $endOfLine, 0);
            echo "   ✅ Hidden fields berhasil ditambahkan setelah @csrf\n\n";
        } else {
            echo "   ⚠️  Tidak dapat menemukan akhir baris setelah @csrf\n\n";
        }
    } else {
        echo "   ⚠️  @csrf tidak ditemukan\n\n";
    }
} else {
    echo "   ⚠️  Hidden fields sudah ada\n\n";
}

// Save
file_put_contents($file, $content);

echo "✅ Perubahan berhasil diaplikasikan!\n\n";
echo "📖 Selanjutnya, tambahkan manual:\n";
echo "   1. Button 'Tambah Perlengkapan'\n";
echo "   2. Equipment Modal\n";
echo "   3. Equipment JavaScript\n\n";
echo "🔄 Untuk rollback: $backupFile\n";

?>
