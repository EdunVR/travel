<?php

echo "=== FIXING BEFOREUNLOAD MESSAGE MANUALLY ===\n\n";

$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';

if (!file_exists($layoutFile)) {
    echo "❌ Layout file not found: $layoutFile\n";
    exit(1);
}

echo "🔧 Manually updating beforeunload message...\n";

$content = file_get_contents($layoutFile);

// Find and replace the beforeunload message more broadly
$patterns = [
    // Pattern 1: Look for the message variable assignment
    '/const message = `[^`]*pageTitle[^`]*`;/',
    // Pattern 2: Look for the entire message construction
    '/const message = `Anda sedang berada di.*?untuk menyegarkan konten\.`;/s',
    // Pattern 3: Look for any message with pageTitle
    '/const message = `.*?\$\{pageTitle\}.*?`;/s'
];

$newMessage = 'const message = `🚨 PERHATIAN - Anda sedang di area admin ERP!\\n\\n` +
                          `📍 Halaman saat ini: ${pageTitle}\\n\\n` +
                          `❌ JANGAN reload browser (Ctrl+R/F5) karena akan merusak sistem tab!\\n\\n` +
                          `✅ GUNAKAN CARA INI UNTUK REFRESH:\\n` +
                          `   • Klik tombol refresh (🔄) di tab aktif\\n` +
                          `   • Atau klik menu lagi dari sidebar\\n` +
                          `   • Atau gunakan tombol refresh yang ada di halaman\\n\\n` +
                          `💡 Tips: Sistem tab dirancang untuk multi-tasking yang efisien!`;';

$messageUpdated = false;

foreach ($patterns as $i => $pattern) {
    if (preg_match($pattern, $content)) {
        echo "✅ Found beforeunload message with pattern " . ($i + 1) . "\n";
        $content = preg_replace($pattern, $newMessage, $content);
        $messageUpdated = true;
        break;
    }
}

if (!$messageUpdated) {
    echo "⚠️ Could not find beforeunload message with any pattern\n";
    echo "🔍 Searching for beforeunload function...\n";
    
    // Try to find the entire beforeunload function and replace it
    $beforeunloadPattern = '/window\.addEventListener\("beforeunload", function\(event\) \{[\s\S]*?\}\);/';
    
    if (preg_match($beforeunloadPattern, $content)) {
        echo "✅ Found entire beforeunload function\n";
        
        $newBeforeunloadFunction = 'window.addEventListener("beforeunload", function(event) {
        // Only prevent if we\'re in admin area and not navigating away intentionally
        const currentUrl = window.location.href;
        const isAdminArea = currentUrl.includes("/admin") || 
                           currentUrl.includes("admin.") ||
                           window.TAB_SYSTEM_ACTIVE;
        
        if (isAdminArea && !window.NAVIGATING_AWAY) {
            console.log("🚫 Browser reload/close prevented for admin area:", currentUrl);
            
            // Enhanced user-friendly message
            const pageTitle = document.title.replace(" - MORRA ERP", "") || "halaman ini";
            const message = `🚨 PERHATIAN - Anda sedang di area admin ERP!\\n\\n` +
                          `📍 Halaman saat ini: ${pageTitle}\\n\\n` +
                          `❌ JANGAN reload browser (Ctrl+R/F5) karena akan merusak sistem tab!\\n\\n` +
                          `✅ GUNAKAN CARA INI UNTUK REFRESH:\\n` +
                          `   • Klik tombol refresh (🔄) di tab aktif\\n` +
                          `   • Atau klik menu lagi dari sidebar\\n` +
                          `   • Atau gunakan tombol refresh yang ada di halaman\\n\\n` +
                          `💡 Tips: Sistem tab dirancang untuk multi-tasking yang efisien!`;
            
            event.preventDefault();
            event.returnValue = message;
            return message;
        } else {
            console.log("⏭️ Allowing navigation away from:", currentUrl);
        }
    });';
        
        $content = preg_replace($beforeunloadPattern, $newBeforeunloadFunction, $content);
        echo "✅ Entire beforeunload function updated\n";
        $messageUpdated = true;
    }
}

if ($messageUpdated) {
    file_put_contents($layoutFile, $content);
    echo "✅ beforeunload message successfully updated\n";
} else {
    echo "❌ Could not update beforeunload message\n";
    
    // Show what we found for debugging
    echo "\n🔍 DEBUGGING - Looking for beforeunload content...\n";
    if (strpos($content, 'beforeunload') !== false) {
        echo "✅ 'beforeunload' text found in file\n";
        
        // Extract a snippet around beforeunload
        $pos = strpos($content, 'beforeunload');
        $start = max(0, $pos - 200);
        $length = 400;
        $snippet = substr($content, $start, $length);
        
        echo "📄 Snippet around 'beforeunload':\n";
        echo "---\n";
        echo $snippet;
        echo "\n---\n";
    } else {
        echo "❌ 'beforeunload' text not found in file\n";
    }
}

echo "\n=== MANUAL BEFOREUNLOAD FIX COMPLETE ===\n";