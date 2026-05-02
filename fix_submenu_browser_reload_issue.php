<?php

echo "=== FIXING SUBMENU BROWSER RELOAD ISSUE ===\n\n";

$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';

if (!file_exists($layoutFile)) {
    echo "❌ Layout file not found: $layoutFile\n";
    exit(1);
}

echo "🔧 Updating browser reload prevention for submenu URLs...\n";

$content = file_get_contents($layoutFile);

// Find the existing keyboard event handler
$searchPattern = '/document\.addEventListener\("keydown", function\(event\) \{[\s\S]*?\/\/ Ctrl\+R or F5[\s\S]*?\}\);/';

if (preg_match($searchPattern, $content, $matches)) {
    echo "✅ Found existing keyboard event handler\n";
    
    // Create the improved keyboard event handler
    $newKeyboardHandler = 'document.addEventListener("keydown", function(event) {
        // Ctrl+R or F5
        if ((event.ctrlKey && event.key === "r") || event.key === "F5") {
            // Check if we\'re in the ERP admin area (any admin URL)
            const currentUrl = window.location.href;
            const isAdminArea = currentUrl.includes("/admin") || 
                               currentUrl.includes("admin.") ||
                               window.TAB_SYSTEM_ACTIVE;
            
            console.log("🔍 Refresh key pressed:", {
                key: event.key,
                ctrlKey: event.ctrlKey,
                currentUrl: currentUrl,
                isAdminArea: isAdminArea,
                tabSystemActive: window.TAB_SYSTEM_ACTIVE
            });
            
            if (isAdminArea && window.TAB_SYSTEM_COMPONENT) {
                console.log("🚫 PREVENTING browser reload for admin area");
                console.log("🔄 Redirecting to tab refresh instead");
                
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                
                // Refresh active tab instead of whole page
                try {
                    window.TAB_SYSTEM_COMPONENT.refreshTab();
                    console.log("✅ Tab refreshed successfully");
                    
                    // Show notification with current page info
                    const pageTitle = document.title.replace(" - MORRA ERP", "") || "Halaman";
                    showTabRefreshNotification(pageTitle);
                    
                } catch (error) {
                    console.error("❌ Error refreshing tab:", error);
                    // Fallback: show error message
                    alert("Gagal refresh tab. Silakan gunakan tombol refresh di tab atau reload halaman.");
                }
                
                return false;
            } else {
                console.log("⏭️ Allowing normal browser refresh (not in admin area)");
            }
        }
        
        // Ctrl+Shift+R (hard refresh) - ask confirmation for admin area
        if (event.ctrlKey && event.shiftKey && event.key === "R") {
            const currentUrl = window.location.href;
            const isAdminArea = currentUrl.includes("/admin") || 
                               currentUrl.includes("admin.") ||
                               window.TAB_SYSTEM_ACTIVE;
                               
            if (isAdminArea) {
                console.log("⚠️ Hard refresh requested in admin area");
                const pageTitle = document.title.replace(" - MORRA ERP", "") || "halaman ini";
                const confirmReload = confirm(
                    `Anda akan melakukan hard refresh yang akan:\n\n` +
                    `• Menutup semua tab yang terbuka\n` +
                    `• Memuat ulang seluruh aplikasi\n` +
                    `• Kehilangan data yang belum disimpan\n\n` +
                    `Saat ini Anda di: ${pageTitle}\n\n` +
                    `Lanjutkan hard refresh?`
                );
                
                if (!confirmReload) {
                    console.log("🚫 Hard refresh cancelled by user");
                    event.preventDefault();
                    return false;
                } else {
                    console.log("✅ Hard refresh confirmed by user");
                    // Set flag to allow reload
                    window.NAVIGATING_AWAY = true;
                }
            }
        }
    });';
    
    // Replace the old handler with the new one
    $content = preg_replace($searchPattern, $newKeyboardHandler, $content);
    
    echo "✅ Keyboard event handler updated\n";
} else {
    echo "❌ Could not find existing keyboard event handler\n";
    exit(1);
}

// Also update the beforeunload handler to be more specific about admin areas
$beforeunloadPattern = '/window\.addEventListener\("beforeunload", function\(event\) \{[\s\S]*?\}\);/';

if (preg_match($beforeunloadPattern, $content, $matches)) {
    echo "✅ Found existing beforeunload handler\n";
    
    $newBeforeunloadHandler = 'window.addEventListener("beforeunload", function(event) {
        // Only prevent if we\'re in admin area and not navigating away intentionally
        const currentUrl = window.location.href;
        const isAdminArea = currentUrl.includes("/admin") || 
                           currentUrl.includes("admin.") ||
                           window.TAB_SYSTEM_ACTIVE;
        
        if (isAdminArea && !window.NAVIGATING_AWAY) {
            console.log("🚫 Browser reload/close prevented for admin area:", currentUrl);
            
            // Show custom message (may not be displayed in modern browsers)
            const pageTitle = document.title.replace(" - MORRA ERP", "") || "halaman ini";
            const message = `Anda sedang berada di ${pageTitle}.\n\n` +
                          `Gunakan tombol refresh di tab untuk memuat ulang halaman, ` +
                          `atau tombol refresh tab (🔄) untuk menyegarkan konten.`;
            
            event.preventDefault();
            event.returnValue = message;
            return message;
        } else {
            console.log("⏭️ Allowing navigation away from:", currentUrl);
        }
    });';
    
    $content = preg_replace($beforeunloadPattern, $newBeforeunloadHandler, $content);
    echo "✅ beforeunload handler updated\n";
} else {
    echo "❌ Could not find existing beforeunload handler\n";
}

// Add the notification function if it doesn\'t exist
if (strpos($content, 'function showTabRefreshNotification') === false) {
    echo "🔧 Adding tab refresh notification function...\n";
    
    $notificationFunction = '
    // Tab refresh notification function
    function showTabRefreshNotification(pageTitle = "Tab") {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll(".tab-refresh-notification");
        existingNotifications.forEach(n => n.remove());
        
        // Create notification element
        const notification = document.createElement("div");
        notification.className = "tab-refresh-notification";
        notification.innerHTML = `
            <div class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg border-l-4 border-blue-300 max-w-sm">
                <div class="flex items-center">
                    <i class="bx bx-refresh mr-2 text-lg"></i>
                    <div>
                        <div class="font-semibold">Tab Disegarkan</div>
                        <div class="text-sm opacity-90">${pageTitle}</div>
                    </div>
                    <button onclick="this.closest(\'.tab-refresh-notification\').remove()" 
                            class="ml-3 text-white hover:text-blue-200">
                        <i class="bx bx-x text-lg"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 4000);
        
        console.log("📢 Tab refresh notification shown for:", pageTitle);
    }';
    
    // Insert the function before the closing script tag
    $scriptEndPos = strrpos($content, '</script>');
    if ($scriptEndPos !== false) {
        $content = substr_replace($content, $notificationFunction . "\n    ", $scriptEndPos, 0);
        echo "✅ Tab refresh notification function added\n";
    }
}

// Write the updated content back to the file
file_put_contents($layoutFile, $content);

echo "\n=== SUBMENU BROWSER RELOAD FIX APPLIED ===\n";
echo "✅ Enhanced URL detection for admin areas\n";
echo "✅ Improved keyboard event handling\n";
echo "✅ Better beforeunload prevention\n";
echo "✅ Enhanced notification system\n";

echo "\n📝 WHAT WAS IMPROVED:\n";
echo "1. URL Detection: Now detects ANY admin URL (not just tab system)\n";
echo "2. Submenu Support: Works for all admin submenus and pages\n";
echo "3. Better Logging: More detailed console messages for debugging\n";
echo "4. Enhanced Notifications: Shows which page was refreshed\n";
echo "5. Improved Confirmations: More informative hard refresh dialog\n";

echo "\n🧪 TEST SCENARIOS:\n";
echo "1. Open admin dashboard → Ctrl+R → Should refresh tab only\n";
echo "2. Navigate to finance submenu → F5 → Should refresh tab only\n";
echo "3. Open inventory page → Ctrl+R → Should refresh tab only\n";
echo "4. Any admin/* URL → Refresh keys → Should refresh tab only\n";
echo "5. Non-admin pages → Refresh keys → Should work normally\n";

echo "\n=== FIX COMPLETE ===\n";