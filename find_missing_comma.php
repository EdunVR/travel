<?php
/**
 * Find Missing Comma in JavaScript Methods
 */

$viewFile = 'resources/views/admin/finance/aktiva-tetap/index.blade.php';
$content = file_get_contents($viewFile);

// Extract JavaScript content
$jsStart = strpos($content, 'function fixedAssetsManagement()');
$jsEnd = strrpos($content, '</script>');
$jsContent = substr($content, $jsStart, $jsEnd - $jsStart);

// Split into lines for analysis
$lines = explode("\n", $jsContent);

echo "=== SEARCHING FOR MISSING COMMAS ===\n\n";

$inMethod = false;
$methodName = '';
$braceCount = 0;
$lineNumber = 0;

foreach ($lines as $line) {
    $lineNumber++;
    $trimmed = trim($line);
    
    // Skip empty lines and comments
    if (empty($trimmed) || strpos($trimmed, '//') === 0) {
        continue;
    }
    
    // Count braces
    $braceCount += substr_count($line, '{') - substr_count($line, '}');
    
    // Check for method definition
    if (preg_match('/^\s*(async\s+)?(\w+)\s*\([^)]*\)\s*\{/', $trimmed, $matches)) {
        $methodName = $matches[2];
        $inMethod = true;
        echo "Found method: {$methodName} at line {$lineNumber}\n";
    }
    
    // Check for method end (closing brace at start of line or with minimal indentation)
    if ($inMethod && $braceCount === 0 && preg_match('/^\s*\}/', $trimmed)) {
        echo "Method {$methodName} ends at line {$lineNumber}\n";
        
        // Check next non-empty line
        $nextLineIndex = $lineNumber;
        $nextLine = '';
        while ($nextLineIndex < count($lines)) {
            $nextLine = trim($lines[$nextLineIndex]);
            if (!empty($nextLine) && strpos($nextLine, '//') !== 0) {
                break;
            }
            $nextLineIndex++;
        }
        
        // Check if next line starts a new method without comma
        if (preg_match('/^\s*(async\s+)?(\w+)\s*\([^)]*\)\s*\{/', $nextLine)) {
            echo "❌ MISSING COMMA after method {$methodName} at line {$lineNumber}\n";
            echo "   Current line: {$trimmed}\n";
            echo "   Next line: {$nextLine}\n\n";
        } else if (preg_match('/^\s*\}/', $nextLine)) {
            echo "✅ Method {$methodName} properly ended (next line is closing brace)\n\n";
        } else if (!empty($nextLine) && $nextLine !== '};') {
            echo "✅ Method {$methodName} has comma or proper separator\n\n";
        }
        
        $inMethod = false;
        $methodName = '';
    }
}

echo "=== ANALYSIS COMPLETE ===\n";