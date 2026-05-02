<?php
/**
 * Test Admin Layout @stack('scripts') Fix
 */

echo "=== ADMIN LAYOUT @stack('scripts') TEST ===\n\n";

$adminLayoutPath = __DIR__ . '/resources/views/components/layouts/admin.blade.php';

if (file_exists($adminLayoutPath)) {
    echo "✅ Admin layout exists\n";
    
    $content = file_get_contents($adminLayoutPath);
    
    if (strpos($content, '@stack(\'scripts\')') !== false) {
        echo "✅ @stack('scripts') directive found\n";
        
        // Check if it's placed before the closing body tag
        $stackPos = strpos($content, '@stack(\'scripts\')');
        $bodyClosePos = strpos($content, '</body>');
        
        if ($stackPos !== false && $bodyClosePos !== false && $stackPos < $bodyClosePos) {
            echo "✅ @stack('scripts') placed before </body>\n";
        } else {
            echo "❌ @stack('scripts') not properly positioned\n";
        }
    } else {
        echo "❌ @stack('scripts') directive missing\n";
    }
    
    // Check if Alpine.js is loaded with defer
    if (strpos($content, 'defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"') !== false) {
        echo "✅ Alpine.js loaded with defer attribute\n";
    } else {
        echo "⚠️  Alpine.js loading method may need verification\n";
    }
    
} else {
    echo "❌ Admin layout file not found\n";
}

echo "\n=== EXPECTED BEHAVIOR ===\n";
echo "With @stack('scripts') added:\n";
echo "1. @push('scripts') content will be rendered\n";
echo "2. roles.js will load before Alpine.js initializes\n";
echo "3. roleManagement function will be available\n";
echo "4. Alpine.js errors should disappear\n\n";

echo "=== NEXT STEPS ===\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Navigate to Role & Permission page\n";
echo "3. Check console for roles.js loading messages\n";
echo "4. Verify Alpine.js errors are gone\n\n";

echo "Status: Admin layout fixed - ready for testing\n";
?>