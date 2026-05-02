<?php

echo "=== FORCE FIXING BROWSER RELOAD PREVENTION ===\n\n";

$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';

if (!file_exists($layoutFile)) {
    echo "❌ Layout file not found: $layoutFile\n";
    exit(1);
}

echo "🔧 Force implementing browser reload prevention...\n";

$content = file_get_contents($layoutFile);

// Find the position after the logo navigation script
$insertPosition = strpos($content, 'console.log("✅ Logo navigation and browser reload fixes installed");');

if ($insertPosition === false) {
    echo "❌ Could not find insertion point\n";
    exit(1);
}

// Move to after the console.log line
$insertPosition = strpos($content, "\n", $insertPosition) + 1;

echo "✅ Found insertion point\n";

// Create the complete reload prevention script
$reloadPreventionScript = '
    // ========================================
    // AGGRESSIVE BROWSER RELOAD PREVENTION
    // ========================================
    
    console.log("🚫 Installing aggressive reload prevention...");
    
    // CRITICAL: Override ALL refresh methods with highest priority
    (function() {
        "use strict";
        
        // Flag to track if we are in admin area
        function isInAdminArea() {
            const url = window.location.href;
            return url.includes("/admin") || url.includes("admin.") || window.TAB_SYSTEM_ACTIVE;
        }
        
        // Enhanced notification function
        function showReloadBlockedNotification(message, type = "warning") {
            // Remove existing notifications
            document.querySelectorAll(".reload-blocked-notification").forEach(n => n.remove());
            
            const colors = {
                success: "bg-green-500",
                warning: "bg-yellow-500", 
                error: "bg-red-500",
                info: "bg-blue-500"
            };
            
            const notification = document.createElement("div");
            notification.className = "reload-blocked-notification";
            notification.innerHTML = `
                <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[99999] ${colors[type]} text-white px-6 py-4 rounded-lg shadow-2xl max-w-md">
                    <div class="flex items-center">
                        <i class="bx bx-shield mr-3 text-xl"></i>
                        <div class="flex-1">
                            <div class="font-bold text-sm">🚫 RELOAD DICEGAH!</div>
                            <div class="text-xs mt-1">${message}</div>
                        </div>
                        <button onclick="this.remove()" class="ml-3 text-white hover:text-gray-200">
                            <i class="bx bx-x text-lg"></i>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 5000);
            
            console.log("📢 Reload blocked notification:", message);
        }
        
        // AGGRESSIVE keyboard event interception
        document.addEventListener("keydown", function(event) {
            const isRefreshKey = (event.ctrlKey && event.key.toLowerCase() === "r") || 
                                event.key === "F5" || 
                                (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === "r");
            
            if (isRefreshKey && isInAdminArea()) {
                console.log("🚫 BLOCKING refresh key:", event.key, "Ctrl:", event.ctrlKey, "Shift:", event.shiftKey);
                
                // IMMEDIATELY prevent all default behavior
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                
                const pageTitle = document.title.replace(" - MORRA ERP", "") || "Halaman ini";
                
                if (event.ctrlKey && event.shiftKey) {
                    // Hard refresh attempt
                    const confirmMsg = `🚨 HARD REFRESH DICEGAH!\\n\\n` +
                                     `📍 Halaman: ${pageTitle}\\n\\n` +
                                     `⚠️ Hard refresh akan merusak sistem tab!\\n\\n` +
                                     `✅ Gunakan tombol refresh di tab sebagai gantinya.\\n\\n` +
                                     `Tetap lanjutkan hard refresh?`;
                    
                    if (confirm(confirmMsg)) {
                        window.NAVIGATING_AWAY = true;
                        window.location.reload(true);
                    } else {
                        showReloadBlockedNotification("Hard refresh dibatalkan. Gunakan refresh tab!", "info");
                    }
                } else {
                    // Regular refresh attempt
                    showReloadBlockedNotification(`Refresh browser dicegah untuk "${pageTitle}". Gunakan refresh tab!`, "warning");
                    
                    // Try to refresh tab if available
                    if (window.TAB_SYSTEM_COMPONENT && window.TAB_SYSTEM_COMPONENT.refreshTab) {
                        setTimeout(() => {
                            try {
                                window.TAB_SYSTEM_COMPONENT.refreshTab();
                                showReloadBlockedNotification(`Tab "${pageTitle}" berhasil disegarkan!`, "success");
                            } catch (e) {
                                console.error("Tab refresh failed:", e);
                            }
                        }, 1000);
                    }
                }
                
                return false;
            }
        }, true); // Capture phase for highest priority
        
        // Enhanced beforeunload prevention
        window.addEventListener("beforeunload", function(event) {
            if (isInAdminArea() && !window.NAVIGATING_AWAY) {
                console.log("🚫 BLOCKING beforeunload for admin area");
                
                const pageTitle = document.title.replace(" - MORRA ERP", "") || "halaman ini";
                const message = `🚨 SISTEM ERP PROTECTION!\\n\\n` +
                              `📍 Halaman: ${pageTitle}\\n\\n` +
                              `⚠️ Meninggalkan halaman akan:\\n` +
                              `• Menutup semua tab\\n` +
                              `• Kehilangan data belum tersimpan\\n\\n` +
                              `✅ Untuk refresh: gunakan tombol refresh di tab!`;
                
                event.preventDefault();
                event.returnValue = message;
                return message;
            }
        });
        
        // Override window.location.reload if called directly
        const originalReload = window.location.reload;
        window.location.reload = function(forceReload) {
            if (isInAdminArea() && !window.NAVIGATING_AWAY) {
                console.log("🚫 BLOCKING direct location.reload() call");
                
                const confirmMsg = `🚨 RELOAD DICEGAH!\\n\\n` +
                                 `Anda mencoba reload halaman admin.\\n\\n` +
                                 `✅ Gunakan refresh tab sebagai gantinya!\\n\\n` +
                                 `Tetap lanjutkan reload?`;
                
                if (confirm(confirmMsg)) {
                    window.NAVIGATING_AWAY = true;
                    originalReload.call(this, forceReload);
                } else {
                    showReloadBlockedNotification("Reload dibatalkan. Gunakan refresh tab!", "info");
                }
            } else {
                originalReload.call(this, forceReload);
            }
        };
        
        // Monitor for potential reload attempts via URL changes
        let lastUrl = window.location.href;
        const urlObserver = new MutationObserver(() => {
            const currentUrl = window.location.href;
            if (currentUrl !== lastUrl) {
                console.log("🔍 URL changed from", lastUrl, "to", currentUrl);
                
                if (isInAdminArea() && currentUrl === lastUrl) {
                    // Same URL = potential reload
                    console.log("⚠️ Potential reload detected");
                    showReloadBlockedNotification("Reload terdeteksi - sistem tab tetap aman!", "info");
                }
                
                lastUrl = currentUrl;
            }
        });
        
        urlObserver.observe(document, { subtree: true, childList: true });
        
        console.log("✅ Aggressive reload prevention installed");
        
    })();
    
';

// Insert the script
$content = substr_replace($content, $reloadPreventionScript, $insertPosition, 0);

// Add CSS for the notification
$cssPattern = '/<\/style>/';
$additionalCSS = '
        /* Reload blocked notification */
        .reload-blocked-notification {
            animation: slideInFromTop 0.3s ease-out;
        }
        
        @keyframes slideInFromTop {
            from {
                transform: translate(-50%, -100px);
                opacity: 0;
            }
            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }
        
        .reload-blocked-notification > div {
            backdrop-filter: blur(10px);
        }
    </style>';

$content = preg_replace($cssPattern, $additionalCSS, $content);

// Write the updated content
file_put_contents($layoutFile, $content);

echo "✅ Aggressive browser reload prevention implemented\n";

echo "\n=== WHAT WAS IMPLEMENTED ===\n";
echo "✅ Capture phase keyboard event interception\n";
echo "✅ Immediate preventDefault() for all refresh keys\n";
echo "✅ Enhanced beforeunload prevention\n";
echo "✅ Direct location.reload() override\n";
echo "✅ URL change monitoring\n";
echo "✅ Prominent center-screen notifications\n";
echo "✅ Automatic tab refresh when possible\n";

echo "\n🧪 TEST NOW:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Navigate to any admin page\n";
echo "3. Press Ctrl+R → Should see notification\n";
echo "4. Press F5 → Should see notification\n";
echo "5. Try browser close → Should see warning\n";

echo "\n=== FORCE FIX COMPLETE ===\n";