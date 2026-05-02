<?php

/**
 * Final fix untuk attendance validation consistency
 */

echo "🔧 Applying final attendance validation fix...\n";

$controllerFile = "app/Http/Controllers/AttendanceManagementController.php";
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Backup
    $backupFile = $controllerFile . ".backup-validation-consistency." . date("Y-m-d-H-i-s");
    file_put_contents($backupFile, $content);
    echo "✅ Backup created: $backupFile\n";
    
    // Fix any remaining regex error messages
    $fixes = [
        "'clock_out.regex' => 'Format jam pulang harus HH:MM atau HH:MM:SS (24 jam)'," => "'clock_out.date_format' => 'Format jam pulang harus HH:MM (24 jam)',",
        "'clock_in.regex' => " => "'clock_in.date_format' => ",
        "'break_in.regex' => " => "'break_in.date_format' => ",
        "'break_out.regex' => " => "'break_out.date_format' => ",
        "'overtime_in.regex' => " => "'overtime_in.date_format' => ",
        "'overtime_out.regex' => " => "'overtime_out.date_format' => ",
    ];
    
    $changed = false;
    foreach ($fixes as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $changed = true;
            echo "✅ Fixed: $old\n";
        }
    }
    
    if ($changed) {
        file_put_contents($controllerFile, $content);
        echo "✅ Controller updated successfully\n";
    } else {
        echo "✅ No changes needed in controller\n";
    }
} else {
    echo "❌ Controller file not found\n";
}

echo "\n🎯 Fix completed! Please test the attendance form now.\n";

?>