<?php

echo "=== FIXING LOGO NAVIGATION AND BROWSER RELOAD ISSUES ===\n\n";

// 1. Fix sidebar logo navigation
$sidebarFile = 'resources/views/components/sidebar.blade.php';
if (file_exists($sidebarFile)) {
    echo "🔧 Fixing logo navigation in sidebar...\n";
    
    $content = file_get_contents($sidebarFile);
    
    // Add special handling for logo link to prevent tab loading
    $oldLogoLink = '<a href="{{ route(\'admin.dashboard\') }}" class="block w-fit mx-auto">';
    $newLogoLink = '<a href="{{ route(\'admin.dashboard\') }}" class="block w-fit mx-auto" data-logo-link="true" onclick="handleLogoClick(event)">';
    
    if (strpos($content, $oldLogoLink) !== false) {
        $content = str_replace($oldLogoLink, $newLogoLink, $content);
        file_put_contents($sidebarFile, $content);
        echo "✅ Logo link updated with special handling\n";
    } else {
        echo "⚠️ Logo link pattern not found, may already be updated\n";
    }
} else {
    echo "❌ Sidebar file not found\n";
}

// 2. Update admin layout with navigation fixes
$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';
if (file_exists($layoutFile)) {
    echo "\n🔧 Adding logo navigation and browser reload fixes to layout...\n";
    
    $content = file_get_contents($layoutFile);
    
    // Find the position to insert the new script (before the existing navigation blocker)
    $insertPosition = strpos($content, '{{-- CRITICAL: Install navigation blocker BEFORE Alpine.js --}}');
    
    if ($insertPosition !== false) {
        $logoAndReloadScript = '
    {{-- LOGO NAVIGATION AND BROWSER RELOAD FIXES --}}
    <script>
    // Handle logo click to force full page reload (prevent tab loading)
    window.handleLogoClick = function(event) {
        console.log("🏠 Logo clicked - forcing full page reload");
        event.preventDefault();
        event.stopPropagation();
        
        // Force full page navigation (not in tab)
        window.location.href = event.target.closest("a").href;
        return false;
    };
    
    // Prevent browser reload and redirect to tab refresh
    window.addEventListener("beforeunload", function(event) {
        // Only prevent if we\'re in tab system and not navigating away
        if (window.TAB_SYSTEM_ACTIVE && !window.NAVIGATING_AWAY) {
            console.log("🚫 Browser reload prevented - use tab refresh instead");
            
            // Show custom message (modern browsers may not show this)
            const message = "Gunakan tombol refresh di tab untuk memuat ulang halaman, bukan refresh browser.";
            event.preventDefault();
            event.returnValue = message;
            return message;
        }
    });
    
    // Override Ctrl+R and F5 to refresh active tab instead
    document.addEventListener("keydown", function(event) {
        // Ctrl+R or F5
        if ((event.ctrlKey && event.key === "r") || event.key === "F5") {
            if (window.TAB_SYSTEM_ACTIVE && window.TAB_SYSTEM_COMPONENT) {
                console.log("🔄 Browser refresh key intercepted - refreshing active tab instead");
                event.preventDefault();
                event.stopPropagation();
                
                // Refresh active tab instead of whole page
                window.TAB_SYSTEM_COMPONENT.refreshTab();
                
                // Show notification
                if (window.showNotification) {
                    window.showNotification("Tab disegarkan", "success");
                } else {
                    // Fallback notification
                    const notification = document.createElement("div");
                    notification.innerHTML = `
                        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                            <i class="bx bx-check-circle mr-2"></i>
                            Tab disegarkan
                        </div>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 3000);
                }
                
                return false;
            }
        }
        
        // Ctrl+Shift+R (hard refresh) - allow but warn
        if (event.ctrlKey && event.shiftKey && event.key === "R") {
            if (window.TAB_SYSTEM_ACTIVE) {
                const confirmReload = confirm("Anda akan melakukan hard refresh yang akan menutup semua tab. Lanjutkan?");
                if (!confirmReload) {
                    event.preventDefault();
                    return false;
                }
                // If confirmed, set flag to allow reload
                window.NAVIGATING_AWAY = true;
            }
        }
    });
    
    // Handle navigation away from tab system
    document.addEventListener("click", function(event) {
        const link = event.target.closest("a[href]");
        if (link) {
            const href = link.getAttribute("href");
            
            // Check if this is a navigation away from the current domain/app
            if (href && (
                href.startsWith("http") && !href.includes(window.location.hostname) ||
                href.includes("logout") ||
                link.hasAttribute("data-logo-link")
            )) {
                console.log("🚪 Navigating away from tab system:", href);
                window.NAVIGATING_AWAY = true;
            }
        }
    });
    
    console.log("✅ Logo navigation and browser reload fixes installed");
    </script>

    ';
        
        // Insert the script before the existing navigation blocker
        $content = substr_replace($content, $logoAndReloadScript, $insertPosition, 0);
        
        file_put_contents($layoutFile, $content);
        echo "✅ Logo navigation and browser reload fixes added to layout\n";
    } else {
        echo "⚠️ Could not find insertion point in layout file\n";
    }
} else {
    echo "❌ Layout file not found\n";
}

echo "\n=== FIXES APPLIED ===\n";
echo "✅ Logo click now forces full page reload (prevents mirroring)\n";
echo "✅ Browser refresh (Ctrl+R, F5) now refreshes active tab only\n";
echo "✅ Hard refresh (Ctrl+Shift+R) shows confirmation dialog\n";
echo "✅ beforeunload event prevents accidental browser reload\n";
echo "\n📝 WHAT WAS FIXED:\n";
echo "1. Logo link now has special handling to prevent tab loading\n";
echo "2. Browser refresh keys (Ctrl+R, F5) intercepted and redirected to tab refresh\n";
echo "3. beforeunload event warns users about browser reload\n";
echo "4. Hard refresh (Ctrl+Shift+R) requires confirmation\n";
echo "5. Navigation away from app (logout, external links) properly handled\n";

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. Click logo - should reload entire page, not load in tab\n";
echo "2. Press Ctrl+R or F5 - should refresh active tab only\n";
echo "3. Try to close browser tab - should show warning\n";
echo "4. Press Ctrl+Shift+R - should ask for confirmation\n";
echo "5. Click logout - should work normally without warnings\n";

echo "\n=== FIX COMPLETE ===\n";