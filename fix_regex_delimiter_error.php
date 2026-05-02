<?php

/**
 * Fix untuk error regex delimiter yang hilang
 * Error: preg_match(): No ending delimiter '/' found
 */

echo "🔧 Memperbaiki error regex delimiter...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
$backupFile = $controllerFile . '.backup-regex-fix.' . date('Y-m-d-H-i-s');

if (file_exists($controllerFile)) {
    copy($controllerFile, $backupFile);
    echo "✅ Backup dibuat: $backupFile\n";
    
    $content = file_get_contents($controllerFile);
    
    // Fix regex pattern - pastikan ada delimiter yang benar
    $oldPattern = "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    $newPattern = "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    
    // Perbaiki semua field time
    $timeFields = [
        'clock_in',
        'clock_out', 
        'break_in',
        'break_out',
        'overtime_in',
        'overtime_out'
    ];
    
    foreach ($timeFields as $field) {
        // Pattern lama yang salah
        $oldPattern = "'{$field}' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
        $newPattern = "'{$field}' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
        
        $content = str_replace($oldPattern, $newPattern, $content);
        
        // Juga untuk required field
        $oldPatternRequired = "'{$field}' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
        $newPatternRequired = "'{$field}' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
        
        $content = str_replace($oldPatternRequired, $newPatternRequired, $content);
    }
    
    // Alternatif: gunakan format yang lebih sederhana dan aman
    // Ganti semua regex dengan date_format yang lebih fleksibel
    
    // Method 1: Gunakan custom validation rule yang lebih aman
    $customValidation = "
    /**
     * Validate time format (supports both HH:MM and HH:MM:SS)
     */
    private function validateTimeFormat(\$value) {
        if (empty(\$value)) {
            return true; // nullable
        }
        
        // Check HH:MM format
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', \$value)) {
            return true;
        }
        
        // Check HH:MM:SS format  
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', \$value)) {
            return true;
        }
        
        return false;
    }";
    
    // Tambahkan method custom validation sebelum method store
    if (strpos($content, 'private function validateTimeFormat') === false) {
        $content = str_replace(
            'public function store(Request $request)',
            $customValidation . "\n\n    public function store(Request \$request)",
            $content
        );
    }
    
    // Ganti semua regex validation dengan custom validation
    $content = preg_replace(
        "/'(clock_in|clock_out|break_in|break_out|overtime_in|overtime_out)' => '(nullable|required)\|regex:\/\^.*?\$\/',/",
        "'$1' => '$2|string',",
        $content
    );
    
    // Tambahkan custom validation di method store
    $oldStoreValidation = 'if ($validator->fails()) {
            return response()->json([
                \'success\' => false,
                \'message\' => \'Validasi gagal\',
                \'errors\' => $validator->errors()
            ], 422);
        }';
    
    $newStoreValidation = 'if ($validator->fails()) {
            return response()->json([
                \'success\' => false,
                \'message\' => \'Validasi gagal\',
                \'errors\' => $validator->errors()
            ], 422);
        }
        
        // Custom time format validation
        $timeFields = [\'clock_in\', \'clock_out\', \'break_in\', \'break_out\', \'overtime_in\', \'overtime_out\'];
        foreach ($timeFields as $field) {
            if ($request->has($field) && !$this->validateTimeFormat($request->$field)) {
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'Format waktu tidak valid\',
                    \'errors\' => [$field => [\'Format harus HH:MM atau HH:MM:SS (24 jam)\']]
                ], 422);
            }
        }';
    
    $content = str_replace($oldStoreValidation, $newStoreValidation, $content);
    
    // Lakukan hal yang sama untuk method update
    $content = str_replace(
        'if ($validator->fails()) {
            return response()->json([
                \'success\' => false,
                \'message\' => \'Validasi gagal\',
                \'errors\' => $validator->errors()
            ], 422);
        }

        try {',
        'if ($validator->fails()) {
            return response()->json([
                \'success\' => false,
                \'message\' => \'Validasi gagal\',
                \'errors\' => $validator->errors()
            ], 422);
        }
        
        // Custom time format validation
        $timeFields = [\'clock_in\', \'clock_out\', \'break_in\', \'break_out\', \'overtime_in\', \'overtime_out\'];
        foreach ($timeFields as $field) {
            if ($request->has($field) && !$this->validateTimeFormat($request->$field)) {
                return response()->json([
                    \'success\' => false,
                    \'message\' => \'Format waktu tidak valid\',
                    \'errors\' => [$field => [\'Format harus HH:MM atau HH:MM:SS (24 jam)\']]
                ], 422);
            }
        }

        try {'
    );
    
    file_put_contents($controllerFile, $content);
    echo "✅ Controller berhasil diperbaiki dengan custom validation\n";
} else {
    echo "❌ File controller tidak ditemukan\n";
}

echo "\n🎯 PERBAIKAN YANG DILAKUKAN:\n";
echo "1. ✅ Mengganti regex validation dengan custom validation method\n";
echo "2. ✅ Menambahkan validateTimeFormat() method yang aman\n";
echo "3. ✅ Mendukung format HH:MM dan HH:MM:SS\n";
echo "4. ✅ Menghindari error regex delimiter\n";

echo "\n📋 TESTING:\n";
echo "1. Coba buka halaman attendance management\n";
echo "2. Klik 'Tambah Absensi' dan isi waktu dengan format HH:MM:SS\n";
echo "3. Pastikan tidak ada error regex lagi\n";

echo "\n✅ Perbaikan selesai!\n";

?>