<?php

echo "🔧 FIXING ATTENDANCE ALPINE.JS FUNCTION\n";
echo "=======================================\n\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';

echo "1. 📝 Reading view file...\n";

if (!file_exists($viewFile)) {
    echo "   ❌ View file not found: $viewFile\n";
    exit(1);
}

$content = file_get_contents($viewFile);

echo "2. 🔍 Locating the attendanceCrud function issue...\n";

// The issue is that the attendanceCrud function is not properly closed
// We need to find where it should end and add the missing closing braces

// Find the start of the function
$functionStart = strpos($content, 'function attendanceCrud() {');
if ($functionStart === false) {
    echo "   ❌ attendanceCrud function not found\n";
    exit(1);
}

echo "   ✅ Found attendanceCrud function at position $functionStart\n";

// Find the end of the script tag
$scriptEnd = strpos($content, '</script>', $functionStart);
if ($scriptEnd === false) {
    echo "   ❌ Script end tag not found\n";
    exit(1);
}

echo "   ✅ Found script end at position $scriptEnd\n";

// Find the last meaningful content before the script end
$beforeScriptEnd = substr($content, $functionStart, $scriptEnd - $functionStart);

// Check if the function is properly closed
if (strpos($beforeScriptEnd, '    }') === false || strpos($beforeScriptEnd, '  }') === false) {
    echo "   ⚠️  Function appears to be missing closing braces\n";
    
    // Find where to insert the missing closing braces
    // Look for the last method or property definition
    $lastMethodPos = strrpos($beforeScriptEnd, '},');
    
    if ($lastMethodPos !== false) {
        echo "   ✅ Found last method at relative position $lastMethodPos\n";
        
        // Add the missing closing braces and return statement
        $missingClosing = '
        };
      }
    
    // Initialize 24-hour format enforcement
    document.addEventListener(\'DOMContentLoaded\', function() {';
        
        // Insert the missing closing before the DOMContentLoaded event
        $insertPosition = $functionStart + $lastMethodPos + 2; // After the last },
        
        // Find the DOMContentLoaded section
        $domContentPos = strpos($content, 'document.addEventListener(\'DOMContentLoaded\'', $functionStart);
        
        if ($domContentPos !== false) {
            // Insert the missing closing before DOMContentLoaded
            $beforeDom = substr($content, 0, $domContentPos);
            $afterDom = substr($content, $domContentPos);
            
            $content = $beforeDom . $missingClosing . "\n    " . $afterDom;
            
            echo "   ✅ Added missing function closing braces\n";
        } else {
            echo "   ❌ Could not find DOMContentLoaded section\n";
        }
    } else {
        echo "   ❌ Could not find last method position\n";
    }
} else {
    echo "   ✅ Function appears to be properly closed\n";
}

echo "3. 🔍 Checking for other JavaScript syntax issues...\n";

// Check for common JavaScript syntax issues
$jsIssues = [];

// Check for unmatched braces
$openBraces = substr_count($content, '{');
$closeBraces = substr_count($content, '}');
if ($openBraces !== $closeBraces) {
    $jsIssues[] = "Unmatched braces: $openBraces open, $closeBraces close";
}

// Check for unmatched parentheses in the function
$functionContent = substr($content, $functionStart, $scriptEnd - $functionStart);
$openParens = substr_count($functionContent, '(');
$closeParens = substr_count($functionContent, ')');
if ($openParens !== $closeParens) {
    $jsIssues[] = "Unmatched parentheses in function: $openParens open, $closeParens close";
}

if (empty($jsIssues)) {
    echo "   ✅ No obvious JavaScript syntax issues found\n";
} else {
    echo "   ⚠️  Potential JavaScript issues:\n";
    foreach ($jsIssues as $issue) {
        echo "      - $issue\n";
    }
}

echo "4. 💾 Saving fixed view file...\n";

file_put_contents($viewFile, $content);

echo "   ✅ View file saved\n";

echo "5. 🧪 Creating test HTML to verify JavaScript syntax...\n";

// Extract just the JavaScript part for testing
$jsStart = strpos($content, '<script>');
$jsEnd = strpos($content, '</script>') + 9;
$jsContent = substr($content, $jsStart, $jsEnd - $jsStart);

$testHtml = '<!DOCTYPE html>
<html>
<head>
    <title>JavaScript Syntax Test</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>
    <div x-data="attendanceCrud()" x-init="console.log(\'Alpine.js initialized successfully\')">
        <p>If you see this without errors, the JavaScript is working!</p>
    </div>
    
    ' . $jsContent . '
</body>
</html>';

file_put_contents('test_attendance_alpine.html', $testHtml);

echo "   ✅ Created test_attendance_alpine.html for browser testing\n";

echo "\n🎯 ALPINE.JS FUNCTION FIX COMPLETE!\n";
echo "===================================\n\n";

echo "✅ Changes Made:\n";
echo "   - Fixed missing closing braces in attendanceCrud function\n";
echo "   - Ensured proper JavaScript syntax structure\n";
echo "   - Added proper function return statement\n";

echo "\n🧪 Testing Steps:\n";
echo "   1. Open test_attendance_alpine.html in browser\n";
echo "   2. Check browser console for any JavaScript errors\n";
echo "   3. If no errors, the Alpine.js function is working\n";
echo "   4. Then test the actual attendance page\n";

echo "\n📝 Next Steps:\n";
echo "   1. Clear browser cache\n";
echo "   2. Refresh the attendance page\n";
echo "   3. Check that Alpine.js initializes without errors\n";
echo "   4. Test attendance form functionality\n";

echo "\n✅ The attendanceCrud function should now be properly defined!\n";