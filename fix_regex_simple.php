<?php

/**
 * Fix sederhana untuk error regex delimiter
 */

echo "🔧 Memperbaiki error regex dengan pendekatan sederhana...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
$backupFile = $controllerFile . '.backup-simple-fix.' . date('Y-m-d-H-i-s');

if (file_exists($controllerFile)) {
    copy($controllerFile, $backupFile);
    echo "✅ Backup dibuat: $backupFile\n";
    
    $content = file_get_contents($controllerFile);
    
    // Ganti semua regex dengan date_format yang lebih sederhana
    // Untuk store method
    $oldStoreValidation = "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    
    $newStoreValidation = "'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'break_in' => 'nullable|date_format:H:i',
            'break_out' => 'nullable|date_format:H:i',
            'overtime_in' => 'nullable|date_format:H:i',
            'overtime_out' => 'nullable|date_format:H:i',";
    
    $content = str_replace($oldStoreValidation, $newStoreValidation, $content);
    
    // Untuk update method
    $oldUpdateValidation = "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    
    $newUpdateValidation = "'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'break_in' => 'nullable|date_format:H:i',
            'break_out' => 'nullable|date_format:H:i',
            'overtime_in' => 'nullable|date_format:H:i',
            'overtime_out' => 'nullable|date_format:H:i',";
    
    $content = str_replace($oldUpdateValidation, $newUpdateValidation, $content);
    
    // Untuk setWorkHours method
    $oldWorkHoursValidation = "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    
    $newWorkHoursValidation = "'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',";
    
    $content = str_replace($oldWorkHoursValidation, $newWorkHoursValidation, $content);
    
    // Update error messages juga
    $oldErrorMessages = "'clock_in.regex' => 'Format jam masuk harus HH:MM atau HH:MM:SS (24 jam)',
            'clock_out.regex' => 'Format jam keluar harus HH:MM atau HH:MM:SS (24 jam)',
            'break_in.regex' => 'Format jam mulai istirahat harus HH:MM atau HH:MM:SS (24 jam)',
            'break_out.regex' => 'Format jam selesai istirahat harus HH:MM atau HH:MM:SS (24 jam)',
            'overtime_in.regex' => 'Format jam lembur masuk harus HH:MM atau HH:MM:SS (24 jam)',
            'overtime_out.regex' => 'Format jam lembur keluar harus HH:MM atau HH:MM:SS (24 jam)',";
    
    $newErrorMessages = "'clock_in.date_format' => 'Format jam masuk harus HH:MM (24 jam)',
            'clock_out.date_format' => 'Format jam keluar harus HH:MM (24 jam)',
            'break_in.date_format' => 'Format jam mulai istirahat harus HH:MM (24 jam)',
            'break_out.date_format' => 'Format jam selesai istirahat harus HH:MM (24 jam)',
            'overtime_in.date_format' => 'Format jam lembur masuk harus HH:MM (24 jam)',
            'overtime_out.date_format' => 'Format jam lembur keluar harus HH:MM (24 jam)',";
    
    $content = str_replace($oldErrorMessages, $newErrorMessages, $content);
    
    file_put_contents($controllerFile, $content);
    echo "✅ Controller berhasil diperbaiki dengan date_format validation\n";
} else {
    echo "❌ File controller tidak ditemukan\n";
}

// Sekarang update view untuk menghilangkan step="1" yang menyebabkan format HH:MM:SS
echo "\n🔧 Memperbaiki view untuk menggunakan format HH:MM...\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($viewFile)) {
    $viewBackup = $viewFile . '.backup-simple-fix.' . date('Y-m-d-H-i-s');
    copy($viewFile, $viewBackup);
    echo "✅ Backup view dibuat: $viewBackup\n";
    
    $viewContent = file_get_contents($viewFile);
    
    // Hilangkan step="1" dari semua input time
    $viewContent = str_replace('step="1"', '', $viewContent);
    
    // Update pattern untuk HH:MM saja
    $viewContent = str_replace('pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"', 'pattern="[0-9]{2}:[0-9]{2}"', $viewContent);
    
    // Update placeholder
    $viewContent = str_replace('placeholder="HH:MM" ', 'placeholder="HH:MM (24 jam)" ', $viewContent);
    
    file_put_contents($viewFile, $viewContent);
    echo "✅ View berhasil diperbaiki untuk format HH:MM\n";
}

echo "\n🎯 PERBAIKAN YANG DILAKUKAN:\n";
echo "1. ✅ Mengganti regex validation dengan date_format:H:i\n";
echo "2. ✅ Menghilangkan step='1' dari input time\n";
echo "3. ✅ Update pattern untuk HH:MM saja\n";
echo "4. ✅ Update error messages\n";

echo "\n📋 TESTING:\n";
echo "1. Coba buka halaman attendance management\n";
echo "2. Klik 'Tambah Absensi' dan isi waktu dengan format HH:MM (misal: 16:21)\n";
echo "3. Pastikan tidak ada error regex lagi\n";
echo "4. Pastikan validasi berjalan normal\n";

echo "\n✅ Perbaikan selesai!\n";

?>