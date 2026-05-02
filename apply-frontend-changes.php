<?php
/**
 * Script untuk mengaplikasikan perubahan frontend pada paket-detail.blade.php
 * 
 * Usage: php apply-frontend-changes.php
 */

$file = 'resources/views/public/paket-detail.blade.php';

if (!file_exists($file)) {
    die("Error: File $file tidak ditemukan!\n");
}

echo "📝 Membaca file $file...\n";
$content = file_get_contents($file);

// Backup original file
$backupFile = $file . '.backup.' . date('YmdHis');
file_put_contents($backupFile, $content);
echo "✅ Backup dibuat: $backupFile\n\n";

// ===== CHANGE 1: Remove DP Payment Option =====
echo "🔧 Change 1: Menghapus opsi DP payment...\n";

$dpSection = <<<'HTML'
<div>
<label class="block text-xs font-bold text-gray-700 mb-2">Opsi Pembayaran *</label>
<div class="grid grid-cols-2 gap-2" id="payment-options">
<label class="payment-opt-btn cursor-pointer">
<input type="radio" name="payment_type" value="full" class="sr-only" onchange="updatePaymentDisplay()">
<div class="payment-opt-card border-2 border-gray-200 rounded-xl p-3 text-center transition-all hover:border-green-brand">
<div class="text-xs font-bold text-gray-700">Bayar Full</div>
<div class="text-xs text-gray-400 mt-0.5">Lunas sekarang</div>
</div>
</label>
<label class="payment-opt-btn cursor-pointer">
<input type="radio" name="payment_type" value="dp" class="sr-only" checked onchange="updatePaymentDisplay()">
<div class="payment-opt-card border-2 border-green-brand bg-green-pale rounded-xl p-3 text-center transition-all">
<div class="text-xs font-bold text-green-brand">Bayar DP</div>
<div class="text-xs text-green-600 mt-0.5">Pilih opsi DP</div>
</div>
</label>
</div>

<!-- Pilihan DP -->
<div id="dp-options-section" class="mt-3 space-y-2">
<label class="text-xs font-semibold text-gray-600 block">Pilih Opsi DP:</label>
<div class="grid grid-cols-2 gap-2">
<label class="dp-opt-btn cursor-pointer">
<input type="radio" name="dp_option" value="25_percent" class="sr-only" checked onchange="updatePaymentDisplay()">
<div class="dp-opt-card border-2 border-green-brand bg-green-pale rounded-xl p-2 text-center transition-all">
<div class="text-xs font-bold text-green-brand">DP 25%</div>
<div class="text-xs text-green-600 mt-0.5" id="dp-25-amount">-</div>
</div>
</label>
<label class="dp-opt-btn cursor-pointer">
<input type="radio" name="dp_option" value="5_million" class="sr-only" onchange="updatePaymentDisplay()">
<div class="dp-opt-card border-2 border-gray-200 rounded-xl p-2 text-center transition-all hover:border-green-brand">
<div class="text-xs font-bold text-gray-700">DP Flat</div>
<div class="text-xs text-gray-500 mt-0.5">Rp 5.000.000</div>
</div>
</label>
</div>
</div>

<div id="payment-amount-info" class="mt-2 p-2 bg-yellow-50 rounded-lg border border-yellow-200 text-xs text-yellow-800 hidden">
<i class="fas fa-info-circle mr-1"></i>
<span id="payment-amount-text"></span>
</div>
</div>
HTML;

$replacement = <<<'HTML'
<!-- Hidden input for payment type - always full -->
<input type="hidden" name="payment_type" value="full">
HTML;

if (strpos($content, $dpSection) !== false) {
    $content = str_replace($dpSection, $replacement, $content);
    echo "   ✅ Opsi DP berhasil dihapus\n\n";
} else {
    echo "   ⚠️  Pattern DP section tidak ditemukan (mungkin sudah diubah)\n\n";
}

// ===== CHANGE 2: Update Form Action =====
echo "🔧 Change 2: Mengupdate form action...\n";

$oldFormTag = '<form action="{{ route(\'public.paket.order\',$package->id) }}" method="POST" id="order-form">';
$newFormTag = '<form action="{{ route(\'public.booking.submit\') }}" method="POST" id="order-form" onsubmit="return prepareFormSubmit()">';

if (strpos($content, $oldFormTag) !== false) {
    $content = str_replace($oldFormTag, $newFormTag, $content);
    echo "   ✅ Form action berhasil diupdate\n\n";
} else {
    echo "   ⚠️  Form tag tidak ditemukan (mungkin sudah diubah)\n\n";
}

// ===== CHANGE 3: Add Hidden Fields =====
echo "🔧 Change 3: Menambahkan hidden fields...\n";

$hiddenFieldsMarker = '<input type="hidden" name="selected_price" id="f_price"';
$hiddenFieldsToAdd = <<<'HTML'

<input type="hidden" name="package_id" value="{{ $package->id }}">
<input type="hidden" name="jamaah_name" id="f_jamaah_name">
<input type="hidden" name="jamaah_phone" id="f_jamaah_phone">
<input type="hidden" name="jamaah_email" id="f_jamaah_email">
<input type="hidden" name="room_type" id="f_room_type" value="double">
<input type="hidden" name="total_price" id="f_total_price">
<input type="hidden" name="equipment" id="f_equipment" value="[]">
HTML;

if (strpos($content, $hiddenFieldsMarker) !== false && strpos($content, 'name="package_id"') === false) {
    // Find the end of the line containing the marker
    $pos = strpos($content, $hiddenFieldsMarker);
    $endOfLine = strpos($content, '>', $pos) + 1;
    $content = substr_replace($content, $hiddenFieldsToAdd, $endOfLine, 0);
    echo "   ✅ Hidden fields berhasil ditambahkan\n\n";
} else {
    echo "   ⚠️  Hidden fields sudah ada atau marker tidak ditemukan\n\n";
}

// Save modified content
file_put_contents($file, $content);

echo "✅ Perubahan dasar berhasil diaplikasikan!\n\n";
echo "⚠️  PERHATIAN: Perubahan berikut harus dilakukan MANUAL:\n";
echo "   1. Tambahkan button 'Tambah Perlengkapan' setelah section family members\n";
echo "   2. Tambahkan Equipment Modal sebelum closing </body> tag\n";
echo "   3. Tambahkan JavaScript untuk equipment management\n";
echo "   4. Update fungsi updatePricePreview\n\n";
echo "📖 Lihat file FRONTEND_CHANGES_PAKET_DETAIL.md untuk detail lengkap\n\n";
echo "🔄 Untuk rollback, gunakan file backup: $backupFile\n";

?>
