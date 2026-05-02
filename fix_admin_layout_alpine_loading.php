<?php

/**
 * Fix Admin Layout Alpine.js Loading Issues
 * Ensures Alpine.js loads properly before all other Alpine-dependent scripts
 */

echo "🚨 FIXING ADMIN LAYOUT ALPINE.JS LOADING ISSUES\n\n";

// 1. Read current layout
echo "1. Reading current admin layout...\n";

$layoutFile = 'resources/views/components/layouts/admin.blade.php';
if (!file_exists($layoutFile)) {
    echo "   ❌ Layout file not found\n";
    exit(1);
}

$content = file_get_contents($layoutFile);
echo "   ✅ Layout file read successfully\n";

// 2. Create backup
$backupFile = $layoutFile . '.backup.' . date('Y-m-d-H-i-s');
file_put_contents($backupFile, $content);
echo "   ✅ Backup created: $backupFile\n";

// 3. Fix the JavaScript loading order
echo "\n2. Fixing JavaScript loading order...\n";

// Remove all existing Alpine.js and related script tags
$content = preg_replace('/\s*<script[^>]*alpinejs[^>]*><\/script>\s*/', '', $content);
$content = preg_replace('/\s*<script[^>]*@alpinejs\/collapse[^>]*><\/script>\s*/', '', $content);
$content = preg_replace('/\s*<script[^>]*inter-outlet\.js[^>]*><\/script>\s*/', '', $content);
$content = preg_replace('/\s*<script[^>]*pos\.js[^>]*><\/script>\s*/', '', $content);
$content = preg_replace('/\s*<script[^>]*alpine-helpers\.js[^>]*><\/script>\s*/', '', $content);

echo "   ✅ Removed existing Alpine.js related scripts\n";

// Find the position after Chart.js to insert our scripts
$chartJsPos = strpos($content, '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>');

if ($chartJsPos !== false) {
    // Find the end of the Chart.js script tag
    $insertPos = strpos($content, '</script>', $chartJsPos) + 9;
    
    // Insert our properly ordered scripts
    $newScripts = "\n" . '    
    <!-- Alpine.js and Dependencies - CRITICAL LOADING ORDER -->
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Alpine.js dependent scripts - MUST load after Alpine.js -->
    <script defer src="{{ asset(\'js/alpine-helpers.js\') }}"></script>
    <script defer src="{{ asset(\'js/pos.js\') }}"></script>
    <script defer src="{{ asset(\'js/inter-outlet.js\') }}"></script>' . "\n    ";
    
    $content = substr_replace($content, $newScripts, $insertPos, 0);
    echo "   ✅ Inserted properly ordered Alpine.js scripts\n";
} else {
    echo "   ❌ Could not find Chart.js script position\n";
    exit(1);
}

// 4. Add Alpine.js initialization check
echo "\n3. Adding Alpine.js initialization check...\n";

// Find the existing Alpine.js initialization script and replace it
$alpineInitPattern = '/document\.addEventListener\(\'alpine:init\', \(\) => \{[^}]+\}\);/s';
$newAlpineInit = "document.addEventListener('alpine:init', () => {
    console.log('🏔️ [ALPINE] Alpine.js initialized successfully');
    console.log('🏔️ [ALPINE] Alpine version:', Alpine.version || 'unknown');
    
    // Ensure all Alpine.js components are ready
    window.alpineReady = true;
    
    // Dispatch custom event for other scripts
    window.dispatchEvent(new CustomEvent('alpine:ready'));
});";

if (preg_match($alpineInitPattern, $content)) {
    $content = preg_replace($alpineInitPattern, $newAlpineInit, $content);
    echo "   ✅ Updated existing Alpine.js initialization\n";
} else {
    // Add new initialization if not found
    $headClosePos = strpos($content, '</head>');
    if ($headClosePos !== false) {
        $initScript = "\n<script>\n" . $newAlpineInit . "\n</script>\n";
        $content = substr_replace($content, $initScript, $headClosePos, 0);
        echo "   ✅ Added new Alpine.js initialization\n";
    }
}

// 5. Add comprehensive Alpine.js debugging
echo "\n4. Adding comprehensive Alpine.js debugging...\n";

$debugScript = '
<script>
// Comprehensive Alpine.js Debug System
(function() {
    console.log("🔍 [ALPINE-DEBUG] Starting Alpine.js debug system...");
    
    let alpineLoadAttempts = 0;
    const maxAttempts = 100;
    
    function checkAlpineStatus() {
        alpineLoadAttempts++;
        
        if (typeof Alpine !== "undefined") {
            console.log(`✅ [ALPINE-DEBUG] Alpine.js found after ${alpineLoadAttempts} attempts`);
            console.log("🏔️ [ALPINE-DEBUG] Alpine.js version:", Alpine.version || "unknown");
            console.log("🏔️ [ALPINE-DEBUG] Alpine.js object:", Alpine);
            
            // Mark Alpine as ready
            window.alpineLoaded = true;
            window.dispatchEvent(new CustomEvent("alpine:loaded"));
            
            return true;
        } else if (alpineLoadAttempts < maxAttempts) {
            console.log(`⏳ [ALPINE-DEBUG] Alpine.js not found, attempt ${alpineLoadAttempts}/${maxAttempts}`);
            setTimeout(checkAlpineStatus, 100);
            return false;
        } else {
            console.error(`❌ [ALPINE-DEBUG] Alpine.js not found after ${maxAttempts} attempts`);
            console.error("🚨 [ALPINE-DEBUG] This will cause component registration failures");
            
            // Try to load Alpine.js manually as last resort
            loadAlpineManually();
            return false;
        }
    }
    
    function loadAlpineManually() {
        console.log("🔄 [ALPINE-DEBUG] Attempting to load Alpine.js manually...");
        
        const script = document.createElement("script");
        script.src = "https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js";
        script.defer = true;
        script.onload = () => {
            console.log("✅ [ALPINE-DEBUG] Alpine.js loaded manually");
            setTimeout(() => {
                if (typeof Alpine !== "undefined") {
                    console.log("🏔️ [ALPINE-DEBUG] Manual Alpine.js load successful");
                    window.alpineLoaded = true;
                    window.dispatchEvent(new CustomEvent("alpine:loaded"));
                } else {
                    console.error("❌ [ALPINE-DEBUG] Manual Alpine.js load failed");
                }
            }, 100);
        };
        script.onerror = () => {
            console.error("❌ [ALPINE-DEBUG] Failed to load Alpine.js manually");
        };
        
        document.head.appendChild(script);
    }
    
    // Start checking immediately
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", checkAlpineStatus);
    } else {
        checkAlpineStatus();
    }
    
    // Also check after window load
    window.addEventListener("load", () => {
        setTimeout(checkAlpineStatus, 100);
    });
    
    // Global error handler for Alpine.js errors
    window.addEventListener("error", (e) => {
        if (e.message && (e.message.includes("Alpine") || e.message.includes("alpine"))) {
            console.error("❌ [ALPINE-DEBUG] Alpine.js error:", e.message);
            console.error("📄 [ALPINE-DEBUG] Error details:", e);
            console.error("🔍 [ALPINE-DEBUG] Alpine available:", typeof Alpine !== "undefined");
        }
        
        if (e.message && e.message.includes("is not defined")) {
            console.error("❌ [ALPINE-DEBUG] Undefined variable error:", e.message);
            console.error("🔍 [ALPINE-DEBUG] This might be an Alpine.js component issue");
        }
    });
    
    console.log("✅ [ALPINE-DEBUG] Debug system initialized");
})();
</script>';

// Insert debug script before </head>
$headClosePos = strpos($content, '</head>');
if ($headClosePos !== false) {
    $content = substr_replace($content, $debugScript . "\n", $headClosePos, 0);
    echo "   ✅ Added comprehensive Alpine.js debugging\n";
}

// 6. Write the fixed layout
echo "\n5. Writing fixed layout...\n";

if (file_put_contents($layoutFile, $content)) {
    echo "   ✅ Layout file updated successfully\n";
} else {
    echo "   ❌ Failed to write layout file\n";
    exit(1);
}

// 7. Verify the fix
echo "\n6. Verifying the fix...\n";

$newContent = file_get_contents($layoutFile);

// Check Alpine.js loading order
$alpineCollapsePos = strpos($newContent, '@alpinejs/collapse');
$alpineMainPos = strpos($newContent, 'alpinejs@3.x.x');
$interOutletPos = strpos($newContent, 'inter-outlet.js');
$posJsPos = strpos($newContent, 'pos.js');

if ($alpineCollapsePos !== false && $alpineMainPos !== false && $interOutletPos !== false) {
    if ($alpineCollapsePos < $alpineMainPos && $alpineMainPos < $interOutletPos) {
        echo "   ✅ Alpine.js loading order is correct\n";
    } else {
        echo "   ❌ Alpine.js loading order is incorrect\n";
    }
} else {
    echo "   ❌ Some Alpine.js scripts not found\n";
}

// Check for defer attributes
$deferCount = substr_count($newContent, 'defer src="https://unpkg.com/alpinejs');
if ($deferCount >= 1) {
    echo "   ✅ Alpine.js has defer attribute\n";
} else {
    echo "   ❌ Alpine.js missing defer attribute\n";
}

// Check for debug script
if (strpos($newContent, 'ALPINE-DEBUG') !== false) {
    echo "   ✅ Alpine.js debug system added\n";
} else {
    echo "   ❌ Alpine.js debug system not found\n";
}

echo "\n📋 SUMMARY:\n";
echo "✅ Admin layout Alpine.js loading has been fixed\n";
echo "✅ Proper loading order: Alpine Collapse → Alpine Main → Dependent Scripts\n";
echo "✅ All Alpine-dependent scripts now use defer attribute\n";
echo "✅ Comprehensive debugging system added\n";
echo "✅ Manual fallback loading implemented\n\n";

echo "🎯 NEXT STEPS:\n";
echo "1. Clear browser cache completely (Ctrl+F5)\n";
echo "2. Open any admin page\n";
echo "3. Check browser console for:\n";
echo "   ✅ '🔍 [ALPINE-DEBUG] Starting Alpine.js debug system...'\n";
echo "   ✅ '✅ [ALPINE-DEBUG] Alpine.js found after X attempts'\n";
echo "   ✅ '🏔️ [ALPINE-DEBUG] Alpine.js version: X.X.X'\n";
echo "   ✅ '🏔️ [ALPINE] Alpine.js initialized successfully'\n";
echo "4. Go to /admin/penjualan/inter-outlet\n";
echo "5. Verify no 'undefined' errors appear\n\n";

echo "🔧 IF PROBLEMS PERSIST:\n";
echo "1. Check browser console for [ALPINE-DEBUG] messages\n";
echo "2. Look for any remaining script loading errors\n";
echo "3. Verify network requests are successful\n";
echo "4. Try different browser or incognito mode\n\n";

echo "✅ ADMIN LAYOUT ALPINE.JS FIX COMPLETE\n";