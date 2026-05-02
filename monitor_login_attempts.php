<?php
/**
 * Monitor Login Attempts - Real-time log monitoring
 */

echo "=== Monitor Login Attempts ===\n";
echo "Monitoring Laravel log for login attempts...\n";
echo "Try to login now and watch for logs.\n";
echo "Press Ctrl+C to stop.\n\n";

$logFile = 'storage/logs/laravel.log';
$lastSize = file_exists($logFile) ? filesize($logFile) : 0;

while (true) {
    if (file_exists($logFile)) {
        $currentSize = filesize($logFile);
        
        if ($currentSize > $lastSize) {
            // Read new content
            $handle = fopen($logFile, 'r');
            fseek($handle, $lastSize);
            $newContent = fread($handle, $currentSize - $lastSize);
            fclose($handle);
            
            // Filter for login-related logs
            $lines = explode("\n", $newContent);
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                if (strpos($line, 'Login') !== false || 
                    strpos($line, 'login') !== false ||
                    strpos($line, 'Auth') !== false ||
                    strpos($line, 'auth') !== false ||
                    strpos($line, 'attempt') !== false ||
                    strpos($line, 'session') !== false) {
                    
                    echo "[" . date('H:i:s') . "] " . trim($line) . "\n";
                }
            }
            
            $lastSize = $currentSize;
        }
    }
    
    sleep(1);
}