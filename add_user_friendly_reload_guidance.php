<?php

echo "=== ADDING USER-FRIENDLY RELOAD GUIDANCE ===\n\n";

$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';

if (!file_exists($layoutFile)) {
    echo "❌ Layout file not found: $layoutFile\n";
    exit(1);
}

echo "🔧 Adding user-friendly reload guidance messages...\n";

$content = file_get_contents($layoutFile);

// 1. Update beforeunload message to be more user-friendly
$beforeunloadPattern = '/const message = `Anda sedang berada di \$\{pageTitle\}\.\\n\\n` \+[\s\S]*?`atau tombol refresh tab \(🔄\) untuk menyegarkan konten\.`;/';

if (preg_match($beforeunloadPattern, $content)) {
    echo "✅ Found existing beforeunload message\n";
    
    $newBeforeunloadMessage = 'const message = `🚨 PERHATIAN - Anda sedang di area admin ERP!\\n\\n` +
                          `📍 Halaman saat ini: ${pageTitle}\\n\\n` +
                          `❌ JANGAN reload browser (Ctrl+R/F5) karena akan merusak sistem tab!\\n\\n` +
                          `✅ GUNAKAN CARA INI UNTUK REFRESH:\\n` +
                          `   • Klik tombol refresh (🔄) di tab aktif\\n` +
                          `   • Atau klik menu lagi dari sidebar\\n` +
                          `   • Atau gunakan tombol refresh yang ada di halaman\\n\\n` +
                          `💡 Tips: Sistem tab dirancang untuk multi-tasking yang efisien!`;';
    
    $content = preg_replace($beforeunloadPattern, $newBeforeunloadMessage, $content);
    echo "✅ beforeunload message updated with user guidance\n";
} else {
    echo "⚠️ Could not find existing beforeunload message pattern\n";
}

// 2. Add a more prominent notification system for tab refresh
$existingNotificationFunction = 'function showTabRefreshNotification(pageTitle = "Tab") {';
$newNotificationFunction = 'function showTabRefreshNotification(pageTitle = "Tab") {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll(".tab-refresh-notification, .reload-guidance-notification");
        existingNotifications.forEach(n => n.remove());
        
        // Create main refresh notification
        const notification = document.createElement("div");
        notification.className = "tab-refresh-notification";
        notification.innerHTML = `
            <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg border-l-4 border-green-300 max-w-sm">
                <div class="flex items-center">
                    <i class="bx bx-check-circle mr-2 text-lg"></i>
                    <div>
                        <div class="font-semibold">✅ Tab Berhasil Disegarkan</div>
                        <div class="text-sm opacity-90">${pageTitle}</div>
                        <div class="text-xs opacity-75 mt-1">Gunakan cara ini untuk refresh!</div>
                    </div>
                    <button onclick="this.closest(\'.tab-refresh-notification\').remove()" 
                            class="ml-3 text-white hover:text-green-200">
                        <i class="bx bx-x text-lg"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
        
        console.log("📢 Tab refresh notification shown for:", pageTitle);
    }
    
    // Function to show reload guidance notification
    function showReloadGuidanceNotification(currentPage = "halaman ini") {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll(".tab-refresh-notification, .reload-guidance-notification");
        existingNotifications.forEach(n => n.remove());
        
        const guidanceNotification = document.createElement("div");
        guidanceNotification.className = "reload-guidance-notification";
        guidanceNotification.innerHTML = `
            <div class="fixed top-4 right-4 z-50 bg-orange-500 text-white px-4 py-3 rounded-lg shadow-lg border-l-4 border-orange-300 max-w-md">
                <div class="flex items-start">
                    <i class="bx bx-info-circle mr-2 text-lg mt-0.5"></i>
                    <div class="flex-1">
                        <div class="font-semibold">💡 Tips Refresh yang Benar</div>
                        <div class="text-sm opacity-90 mt-1">Anda sedang di: ${currentPage}</div>
                        <div class="text-sm mt-2 space-y-1">
                            <div>✅ Gunakan tombol refresh di tab (🔄)</div>
                            <div>✅ Atau klik menu lagi dari sidebar</div>
                            <div>❌ Jangan reload browser (Ctrl+R/F5)</div>
                        </div>
                    </div>
                    <button onclick="this.closest(\'.reload-guidance-notification\').remove()" 
                            class="ml-2 text-white hover:text-orange-200">
                        <i class="bx bx-x text-lg"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(guidanceNotification);
        
        // Auto remove after 8 seconds (longer for guidance)
        setTimeout(() => {
            if (guidanceNotification.parentNode) {
                guidanceNotification.remove();
            }
        }, 8000);
        
        console.log("📢 Reload guidance notification shown for:", currentPage);
    }';

if (strpos($content, $existingNotificationFunction) !== false) {
    $content = str_replace($existingNotificationFunction, $newNotificationFunction, $content);
    echo "✅ Enhanced notification functions added\n";
} else {
    echo "⚠️ Could not find existing notification function\n";
}

// 3. Update keyboard event handler to show guidance
$keyboardPattern = '/console\.log\("🔄 Redirecting to tab refresh instead"\);[\s\S]*?showTabRefreshNotification\(pageTitle\);/';

if (preg_match($keyboardPattern, $content)) {
    echo "✅ Found keyboard event handler\n";
    
    $newKeyboardHandling = 'console.log("🔄 Redirecting to tab refresh instead");
                
                // Refresh active tab instead of whole page
                try {
                    window.TAB_SYSTEM_COMPONENT.refreshTab();
                    console.log("✅ Tab refreshed successfully");
                    
                    // Show success notification with guidance
                    const pageTitle = document.title.replace(" - MORRA ERP", "") || "Halaman";
                    showTabRefreshNotification(pageTitle);
                    
                    // Also show guidance notification after a short delay
                    setTimeout(() => {
                        showReloadGuidanceNotification(pageTitle);
                    }, 2000);
                    
                } catch (error) {
                    console.error("❌ Error refreshing tab:", error);
                    // Show error with guidance
                    alert("❌ Gagal refresh tab!\\n\\n" +
                          "💡 Solusi:\\n" +
                          "• Klik tombol refresh (🔄) di tab aktif\\n" +
                          "• Atau klik menu lagi dari sidebar\\n" +
                          "• Atau reload halaman jika diperlukan");
                }';
    
    $content = preg_replace($keyboardPattern, $newKeyboardHandling, $content);
    echo "✅ Keyboard event handler updated with guidance\n";
} else {
    echo "⚠️ Could not find keyboard event handler pattern\n";
}

// 4. Add CSS for better notification styling
$cssPattern = '/<style>[\s\S]*?<\/style>/';

if (preg_match($cssPattern, $content, $matches)) {
    echo "✅ Found existing CSS\n";
    
    $additionalCSS = '
        /* Enhanced notification styles */
        .tab-refresh-notification,
        .reload-guidance-notification {
            animation: slideInFromRight 0.3s ease-out;
        }
        
        @keyframes slideInFromRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .reload-guidance-notification {
            max-width: 350px;
        }
        
        .reload-guidance-notification .text-sm {
            line-height: 1.4;
        }
        
        /* Responsive notifications */
        @media (max-width: 640px) {
            .tab-refresh-notification,
            .reload-guidance-notification {
                position: fixed !important;
                top: 10px !important;
                left: 10px !important;
                right: 10px !important;
                max-width: none !important;
            }
        }';
    
    // Insert additional CSS before closing style tag
    $newCSS = str_replace('</style>', $additionalCSS . "\n    </style>", $matches[0]);
    $content = str_replace($matches[0], $newCSS, $content);
    echo "✅ Enhanced CSS added for notifications\n";
} else {
    echo "⚠️ Could not find existing CSS to enhance\n";
}

// 5. Add a helper function to detect if user is trying to reload
$helperFunction = '
    // Helper function to detect reload attempts and show guidance
    function detectReloadAttempt() {
        const currentUrl = window.location.href;
        const isAdminArea = currentUrl.includes("/admin") || 
                           currentUrl.includes("admin.") ||
                           window.TAB_SYSTEM_ACTIVE;
        
        if (isAdminArea) {
            const pageTitle = document.title.replace(" - MORRA ERP", "") || "halaman ini";
            
            // Show guidance notification
            showReloadGuidanceNotification(pageTitle);
            
            console.log("🔍 Reload attempt detected for admin area:", currentUrl);
            console.log("📢 Showing reload guidance to user");
        }
    }
    
    // Monitor for potential reload attempts (when user navigates back to same page)
    let lastUrl = window.location.href;
    const urlChangeObserver = new MutationObserver(() => {
        if (window.location.href !== lastUrl) {
            lastUrl = window.location.href;
            
            // Small delay to let page settle
            setTimeout(() => {
                const isAdminArea = window.location.href.includes("/admin") || 
                                   window.location.href.includes("admin.") ||
                                   window.TAB_SYSTEM_ACTIVE;
                
                if (isAdminArea && window.TAB_SYSTEM_COMPONENT) {
                    console.log("📍 Admin area navigation detected:", window.location.href);
                }
            }, 500);
        }
    });
    
    // Start observing
    urlChangeObserver.observe(document, { subtree: true, childList: true });';

// Insert helper function before the closing script tag
$scriptEndPos = strrpos($content, '</script>');
if ($scriptEndPos !== false) {
    $content = substr_replace($content, $helperFunction . "\n    ", $scriptEndPos, 0);
    echo "✅ Helper functions added for reload detection\n";
}

// Write the updated content back to the file
file_put_contents($layoutFile, $content);

echo "\n=== USER-FRIENDLY RELOAD GUIDANCE ADDED ===\n";
echo "✅ Enhanced beforeunload message with clear instructions\n";
echo "✅ Improved notification system with guidance\n";
echo "✅ Better error messages with solutions\n";
echo "✅ Enhanced CSS for better UX\n";
echo "✅ Helper functions for reload detection\n";

echo "\n📝 NEW USER EXPERIENCE:\n";
echo "1. 🚨 beforeunload: Clear warning with step-by-step guidance\n";
echo "2. ✅ Tab refresh: Success notification + guidance tip\n";
echo "3. 💡 Guidance notification: Shows correct refresh methods\n";
echo "4. ❌ Error handling: Helpful solutions when things go wrong\n";
echo "5. 📱 Mobile responsive: Works well on all devices\n";

echo "\n🎯 USER MESSAGES NOW INCLUDE:\n";
echo "• Clear explanation of why not to reload browser\n";
echo "• Step-by-step instructions for correct refresh\n";
echo "• Visual indicators (✅ ❌ 💡 🔄)\n";
echo "• Current page context\n";
echo "• Multiple solution options\n";

echo "\n🧪 TEST THE NEW EXPERIENCE:\n";
echo "1. Navigate to any admin submenu\n";
echo "2. Press Ctrl+R → See success + guidance notifications\n";
echo "3. Try to close browser → See detailed warning message\n";
echo "4. Press Ctrl+Shift+R → See enhanced confirmation\n";
echo "5. Check mobile responsiveness\n";

echo "\n=== ENHANCEMENT COMPLETE ===\n";