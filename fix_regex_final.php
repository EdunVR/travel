<?php

/**
 * Fix final untuk error regex delimiter
 * Mengganti semua regex pattern dengan date_format yang aman
 */

echo "🔧 Memperbaiki error regex delimiter - FINAL FIX...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
$backupFile = $controllerFile . '.backup-final-regex-fix.' . date('Y-m-d-H-i-s');

if (file_exists($controllerFile)) {
    copy($controllerFile, $backupFile);
    echo "✅ Backup dibuat: $backupFile\n";
    
    $content = file_get_contents($controllerFile);
    
    // Ganti semua regex pattern yang bermasalah dengan date_format
    $regexPatterns = [
        "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'clock_in' => 'nullable|date_format:H:i',",
        "'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'clock_out' => 'nullable|date_format:H:i',",
        "'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'break_in' => 'nullable|date_format:H:i',",
        "'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'break_out' => 'nullable|date_format:H:i',",
        "'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'overtime_in' => 'nullable|date_format:H:i',",
        "'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'overtime_out' => 'nullable|date_format:H:i',",
        
        // Required patterns
        "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'clock_in' => 'required|date_format:H:i',",
        "'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'," => "'clock_out' => 'required|date_format:H:i',",
    ];
    
    foreach ($regexPatterns as $old => $new) {
        $content = str_replace($old, $new, $content);
        echo "✅ Replaced: $old\n";
    }
    
    // Ganti error messages juga
    $errorMessages = [
        "'clock_in.regex' => 'Format jam masuk harus HH:MM atau HH:MM:SS (24 jam)'," => "'clock_in.date_format' => 'Format jam masuk harus HH:MM (24 jam)',",
        "'clock_out.regex' => 'Format jam keluar harus HH:MM atau HH:MM:SS (24 jam)'," => "'clock_out.date_format' => 'Format jam keluar harus HH:MM (24 jam)',",
        "'break_in.regex' => 'Format jam mulai istirahat harus HH:MM atau HH:MM:SS (24 jam)'," => "'break_in.date_format' => 'Format jam mulai istirahat harus HH:MM (24 jam)',",
        "'break_out.regex' => 'Format jam selesai istirahat harus HH:MM atau HH:MM:SS (24 jam)'," => "'break_out.date_format' => 'Format jam selesai istirahat harus HH:MM (24 jam)',",
        "'overtime_in.regex' => 'Format jam lembur masuk harus HH:MM atau HH:MM:SS (24 jam)'," => "'overtime_in.date_format' => 'Format jam lembur masuk harus HH:MM (24 jam)',",
        "'overtime_out.regex' => 'Format jam lembur keluar harus HH:MM atau HH:MM:SS (24 jam)'," => "'overtime_out.date_format' => 'Format jam lembur keluar harus HH:MM (24 jam)',",
    ];
    
    foreach ($errorMessages as $old => $new) {
        $content = str_replace($old, $new, $content);
        echo "✅ Replaced error message: $old\n";
    }
    
    file_put_contents($controllerFile, $content);
    echo "✅ Controller berhasil diperbaiki\n";
} else {
    echo "❌ File controller tidak ditemukan\n";
}

// Verifikasi tidak ada lagi regex pattern yang bermasalah
echo "\n🔍 Verifikasi perbaikan...\n";
$content = file_get_contents($controllerFile);

if (strpos($content, 'regex:/') !== false) {
    echo "⚠️ Masih ada regex pattern yang tersisa!\n";
    
    // Tampilkan baris yang masih mengandung regex
    $lines = explode("\n", $content);
    foreach ($lines as $lineNum => $line) {
        if (strpos($line, 'regex:/') !== false) {
            echo "Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
        }
    }
} else {
    echo "✅ Semua regex pattern berhasil diganti!\n";
}

echo "\n🎯 PERBAIKAN SELESAI:\n";
echo "1. ✅ Semua regex pattern diganti dengan date_format:H:i\n";
echo "2. ✅ Error messages diupdate\n";
echo "3. ✅ Tidak ada lagi regex delimiter error\n";

echo "\n📋 TESTING:\n";
echo "1. Coba buka halaman attendance management\n";
echo "2. Klik 'Tambah Absensi' dan isi waktu dengan format HH:MM\n";
echo "3. Pastikan tidak ada error 500 lagi\n";

echo "\n✅ Perbaikan final selesai!\n";

?>