<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }" class="h-full bg-gradient-to-br from-slate-50 to-white overflow-x-hidden">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'MORRA ERP'); ?></title>

    
    <?php
        $companySetting = \App\Models\CompanySetting::where('is_active', true)->first();
        $faviconPath = $companySetting && $companySetting->company_favicon 
            ? asset('storage/' . $companySetting->company_favicon)
            : asset('img/logo_xx.png');
    ?>
    <link rel="icon" type="image/png" href="<?php echo e($faviconPath); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e($faviconPath); ?>">
    <link rel="apple-touch-icon" href="<?php echo e($faviconPath); ?>">

    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Configure Tailwind
        if (typeof tailwind !== 'undefined') {
            tailwind.config = {
                theme: {
                    container: { center: true, padding: '1rem' },
                    extend: {
                        colors: {
                            primary: {50:'#eef7ff',100:'#daecff',200:'#b6d8ff',300:'#87beff',400:'#55a0ff',500:'#2f86ff',600:'#186ae6',700:'#1354b4',800:'#0f418c',900:'#0c356f'},
                            ink: { 900:'#0f172a', 700:'#334155', 500:'#64748b' }
                        },
                        boxShadow: {
                            card: '0 6px 20px rgba(15,23,42,.06)',
                            float: '0 14px 40px rgba(15,23,42,.10)',
                        },
                        borderRadius: { '2xl': '1rem' }
                    }
                }
            }
        }
    </script>

    
    <link rel="stylesheet" href="<?php echo e(asset('css/resolution-settings.css')); ?>?v=<?php echo e(time()); ?>">
    
    
    <link rel="stylesheet" href="<?php echo e(asset('css/desktop-responsive-scaling.css')); ?>?v=<?php echo e(time()); ?>">
    
    
    <link rel="stylesheet" href="<?php echo e(asset('css/dropdown-fix.css')); ?>?v=<?php echo e(time()); ?>">
    
    
    <link rel="stylesheet" href="<?php echo e(asset('css/force-dropdown-full-text.css')); ?>?v=<?php echo e(time()); ?>">

    <!-- <script>
        if (location.hostname !== 'localhost') {
            console.log = function () {};
            console.warn = function () {};
            console.error = function () {};
            console.info = function () {};
            console.debug = function () {};
        }
    </script> -->


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <!-- Bootstrap (for modals) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    
    <script src="<?php echo e(asset('js/outlet-helper.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    
    <script src="<?php echo e(asset('js/resolution-settings.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    
    <script src="<?php echo e(asset('js/desktop-responsive-scaling.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    
    <script src="<?php echo e(asset('js/finance-components.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    
    <script src="<?php echo e(asset('js/chat-panel.js')); ?>?v=<?php echo e(time()); ?>"></script>

    
    <script src="<?php echo e(asset('js/tab-modal-handler.js')); ?>"></script>

    
    <script src="<?php echo e(asset('js/browser-reload-redirect.js')); ?>"></script>
    
    
    <script src="<?php echo e(asset('js/iframe-auto-height.js')); ?>"></script>
    
    
    <?php if(!app()->environment('production')): ?>
        <script src="<?php echo e(asset('js/responsive-scaling-helper.js')); ?>"></script>
    <?php endif; ?>
    
    
    <script src="<?php echo e(asset('js/viewport-scale-indicator.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    
    <script src="<?php echo e(asset('js/auto-resize-dropdown.js')); ?>?v=<?php echo e(time()); ?>"></script>

    
    
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
        // Only prevent if we're in admin area and not navigating away intentionally
        const currentUrl = window.location.href;
        const isAdminArea = currentUrl.includes("/admin") || 
                           currentUrl.includes("admin.") ||
                           window.TAB_SYSTEM_ACTIVE;
        
        if (isAdminArea && !window.NAVIGATING_AWAY) {
            console.log("🚫 Browser reload/close prevented for admin area:", currentUrl);
            
            // Show custom message (may not be displayed in modern browsers)
            const pageTitle = document.title.replace(" - MORRA ERP", "") || "halaman ini";
            const message = `🚨 PERHATIAN - Anda sedang di area admin ERP!\n\n` +
                          `📍 Halaman saat ini: ${pageTitle}\n\n` +
                          `❌ JANGAN reload browser (Ctrl+R/F5) karena akan merusak sistem tab!\n\n` +
                          `✅ GUNAKAN CARA INI UNTUK REFRESH:\n` +
                          `   • Klik tombol refresh (🔄) di tab aktif\n` +
                          `   • Atau klik menu lagi dari sidebar\n` +
                          `   • Atau gunakan tombol refresh yang ada di halaman\n\n` +
                          `💡 Tips: Sistem tab dirancang untuk multi-tasking yang efisien!`;
            
            event.preventDefault();
            event.returnValue = message;
            return message;
        } else {
            console.log("⏭️ Allowing navigation away from:", currentUrl);
        }
    });
    
    // Override Ctrl+R and F5 to refresh active tab instead
    document.addEventListener("keydown", function(event) {
        // Ctrl+R or F5
        if ((event.ctrlKey && event.key === "r") || event.key === "F5") {
            // Check if we're in the ERP admin area (any admin URL)
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
                    alert("❌ Gagal refresh tab!\n\n" +
                          "💡 Solusi:\n" +
                          "• Klik tombol refresh (🔄) di tab aktif\n" +
                          "• Atau klik menu lagi dari sidebar\n" +
                          "• Atau reload halaman jika diperlukan");
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
                    const confirmMsg = `🚨 HARD REFRESH DICEGAH!\n\n` +
                                     `📍 Halaman: ${pageTitle}\n\n` +
                                     `⚠️ Hard refresh akan merusak sistem tab!\n\n` +
                                     `✅ Gunakan tombol refresh di tab sebagai gantinya.\n\n` +
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
                const message = `🚨 SISTEM ERP PROTECTION!\n\n` +
                              `📍 Halaman: ${pageTitle}\n\n` +
                              `⚠️ Meninggalkan halaman akan:\n` +
                              `• Menutup semua tab\n` +
                              `• Kehilangan data belum tersimpan\n\n` +
                              `✅ Untuk refresh: gunakan tombol refresh di tab!`;
                
                event.preventDefault();
                event.returnValue = message;
                return message;
            }
        });
        
        // Note: Cannot override window.location.reload in modern browsers (read-only)
        // Instead, we rely on beforeunload and keydown event handlers above
        // to prevent unwanted reloads and guide users to use tab refresh
        
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
    
    </script>

    
    <script>
    (function() {
        'use strict';
        
        console.log('🚨 EARLY navigation blocker installing...');
        
        // Set flag immediately
        window.TAB_SYSTEM_ACTIVE = true;
        
        // CRITICAL: Check if this page is loaded WITHOUT tab system
        // If so, redirect to main admin page to ensure full layout
        function checkTabSystemPresence() {
            // Wait a bit for DOM to load
            setTimeout(() => {
                const hasTabSystem = document.querySelector('[x-data*="tabSystem"]');
                const isAdminArea = window.location.pathname.startsWith('/admin');
                const isMainAdminPage = window.location.pathname === '/admin';
                
                console.log('🔍 Tab system check:', {
                    hasTabSystem: !!hasTabSystem,
                    isAdminArea: isAdminArea,
                    isMainAdminPage: isMainAdminPage,
                    currentPath: window.location.pathname
                });
                
                // If we're in admin area but NOT on main admin page AND no tab system found
                if (isAdminArea && !isMainAdminPage && !hasTabSystem) {
                    console.log('🚨 CRITICAL: Admin page loaded without tab system!');
                    console.log('🔄 Redirecting to main admin page to restore full layout...');
                    
                    // Show notification before redirect
                    const notification = document.createElement('div');
                    notification.innerHTML = `
                        <div style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); 
                                    background: #f59e0b; color: white; padding: 12px 20px; 
                                    border-radius: 8px; z-index: 9999; font-family: sans-serif;">
                            🔄 Mengarahkan ke halaman utama untuk memuat layout lengkap...
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = '/admin';
                    }, 1000);
                    
                    return;
                }
                
                console.log('✅ Tab system presence check passed');
            }, 500);
        }
        
        // Run check when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkTabSystemPresence);
        } else {
            checkTabSystemPresence();
        }
        
        // Install handler on window with capture phase
        window.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            
            const href = link.getAttribute('href');
            const sidebar = link.closest('aside');
            
            // Only intercept sidebar links
            if (!sidebar) return;
            
            // Skip special cases
            if (!href || 
                href === '#' || 
                href.startsWith('javascript:') ||
                href.includes('logout') ||
                link.hasAttribute('onclick')) {
                return;
            }
            
            console.log('🚨 EARLY BLOCKER - Preventing navigation to:', href);
            
            // PREVENT NAVIGATION
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Extract title and icon
            const title = link.textContent.trim();
            const iconEl = link.querySelector('i[class*="bx"]');
            const icon = iconEl ? iconEl.className : 'bx bx-file';
            
            console.log('📦 Navigation details:', { href, title, icon });
            
            // Try to delegate to Alpine immediately
            const tryDelegate = function(attempt) {
                console.log('🔄 Delegation attempt', attempt);
                
                if (window.TAB_SYSTEM_COMPONENT && typeof window.TAB_SYSTEM_COMPONENT.loadInActiveTab === 'function') {
                    console.log('✅ Alpine component found, delegating...');
                    
                    try {
                        window.TAB_SYSTEM_COMPONENT.loadInActiveTab(href, title, icon);
                        console.log('✅ Delegation successful');
                        return true;
                    } catch (error) {
                        console.error('❌ Delegation error:', error);
                        return false;
                    }
                } else {
                    console.log('⏳ Alpine not ready yet, attempt', attempt);
                    return false;
                }
            };
            
            // Try immediately
            if (!tryDelegate(1)) {
                // Retry with delays
                let attempts = 1;
                const maxAttempts = 10;
                
                const retry = function() {
                    attempts++;
                    if (attempts > maxAttempts) {
                        console.error('❌ Failed to delegate after', maxAttempts, 'attempts');
                        alert('Tab system not ready. Please refresh the page.');
                        return;
                    }
                    
                    if (!tryDelegate(attempts)) {
                        setTimeout(retry, 100);
                    }
                };
                
                setTimeout(retry, 50);
            }
            
            return false;
        }, true); // Capture phase
        
        console.log('✅ EARLY navigation blocker installed');
    })();
    </script>

    
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <style>
        /* Tambahan kecil untuk jaga overflow */
        html, body { width: 100%; }
        body { overflow-x: hidden; }
        svg { display: block; max-width: 100%; height: auto; }
        img { max-width: 100%; height: auto; }
        /* Hindari scroll karena transform sidebar (off-canvas) */
        aside[style], aside { contain: layout paint size; }
        
        /* ========================================
           CHAT SYSTEM STYLES
           ======================================== */
        
        /* Chat Button Styles */
        .chat-button {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 50;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #2f86ff 0%, #186ae6 100%);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .chat-button:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.15), 0 15px 15px -5px rgba(0, 0, 0, 0.08);
        }
        
        .chat-button:active {
            transform: scale(0.95);
        }
        
        /* Chat Panel Styles */
        .chat-panel {
            position: fixed;
            bottom: 6rem;
            right: 1.5rem;
            z-index: 40;
            max-height: calc(100vh - 8rem);
            background: white;
            border-radius: 1rem;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.10);
            border: 1px solid #e2e8f0;
        }
        
        /* Responsive Chat Panel */
        @media (max-width: 640px) {
            .chat-panel {
                bottom: 0;
                right: 0;
                left: 0;
                width: 100% !important;
                max-height: 100vh;
                border-radius: 0;
            }
            
            .chat-button {
                bottom: 1rem;
                right: 1rem;
            }
        }
        
        /* Chat Message Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 0.75;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }
        
        @keyframes bounce-dot {
            0%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-8px);
            }
        }
        
        /* Custom Scrollbar for Chat */
        .chat-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .chat-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .chat-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Chat Message Bubble Styles */
        .message-bubble-sent {
            background: linear-gradient(135deg, #2f86ff 0%, #186ae6 100%);
            color: white;
            border-radius: 1rem;
            border-bottom-right-radius: 0.25rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 8px rgba(47, 134, 255, 0.2);
        }
        
        .message-bubble-received {
            background: white;
            color: #0f172a;
            border-radius: 1rem;
            border-bottom-left-radius: 0.25rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }
        
        .message-bubble-chatbot {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #581c87;
            border-radius: 1rem;
            border-bottom-left-radius: 0.25rem;
            padding: 0.75rem 1rem;
            border: 1px solid #d8b4fe;
        }
        
        /* Smooth Transitions */
        .chat-transition-enter {
            animation: slideInUp 0.3s ease-out;
        }
        
        /* Ensure chat components don't interfere with modals */
        .modal-backdrop {
            z-index: 1040;
        }
        
        .modal {
            z-index: 1050;
        }
        
        /* Chat notification badge */
        .chat-badge {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 0.375rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            background: #ef4444;
            border-radius: 9999px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Typing indicator animation */
        .typing-dot {
            animation: bounce-dot 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        /* Connection status indicator */
        .connection-indicator {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        
        .connection-indicator.online {
            background: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }
        
        .connection-indicator.offline {
            background: #ef4444;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Smooth fade transitions */
        .fade-enter-active, .fade-leave-active {
            transition: opacity 0.3s ease;
        }
        
        .fade-enter-from, .fade-leave-to {
            opacity: 0;
        }

        /* ========================================
           TAB SYSTEM STYLES
           ======================================== */
        
        .tab-content-wrapper {
            position: relative;
            isolation: isolate;
        }
        
        /* Tab scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 4px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        /* Modal overlay untuk tab system */
        .tab-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        
        /* Ensure modals stay within active tab context */
        .tab-content-wrapper .modal {
            position: fixed;
            z-index: 1050;
        }
        
        /* Tab transition */
        .tab-transition-enter {
            animation: slideInRight 0.2s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    
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
        }
    
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
    </style>
</head>
<body class="h-full text-ink-900 overflow-x-hidden" 
      x-data="{ sidebarOpen: false, loading: true }"
      x-init="window.addEventListener('load', () => loading = false)">
    
    <!-- GLOBAL LOADING OVERLAY -->
    <div id="global-loading"
        class="fixed inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-md z-[9999] transition-opacity duration-700 opacity-100">
    <!-- LOGO DENGAN ANIMASI PULSASI -->
    <div class="relative">
        <img src="<?php echo e(url(asset('img/logo_xx.png'))); ?>"
            class="w-20 h-20 animate-bounce drop-shadow-lg" />
        <!-- RING CAHAYA INTERAKTIF -->
        <div class="absolute inset-0 rounded-full border-4 border-red-500 animate-ping"></div>
    </div>

    <!-- TEKS LOADING DENGAN GRADIENT -->
    <div class="mt-6 text-lg font-semibold bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent animate-pulse">
        Memuat data, mohon tunggu...
    </div>
    </div>


    
    <div
        x-data="{ modalLoading: false }"
        x-show="modalLoading"
        x-transition.opacity
        id="modal-loader"
        class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/20 backdrop-blur-[1px]"
        style="display: none;"
    >
        <div class="bg-white rounded-2xl shadow-card px-6 py-4 flex items-center gap-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-primary-500 border-t-transparent"></div>
            <p class="text-sm text-primary-700 font-medium">MORRA Sedang Memuat, sabar ya...</p>
        </div>
    </div>


    
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-[2px] lg:hidden"
        aria-hidden="true"></div>

    
    <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginale5bc9b34dd139a393f71cdc403b71855 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5bc9b34dd139a393f71cdc403b71855 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notifications','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notifications'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5bc9b34dd139a393f71cdc403b71855)): ?>
<?php $attributes = $__attributesOriginale5bc9b34dd139a393f71cdc403b71855; ?>
<?php unset($__attributesOriginale5bc9b34dd139a393f71cdc403b71855); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5bc9b34dd139a393f71cdc403b71855)): ?>
<?php $component = $__componentOriginale5bc9b34dd139a393f71cdc403b71855; ?>
<?php unset($__componentOriginale5bc9b34dd139a393f71cdc403b71855); ?>
<?php endif; ?>

    
    <div class="lg:pl-80 transition-all overflow-x-hidden" x-data="tabSystem()">
        
        <header class="sticky top-0 z-20 border-b border-slate-200/70">
            <div class="relative">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-primary-50 via-transparent to-transparent"></div>
                <div class="backdrop-blur supports-[backdrop-filter]:bg-white/70 bg-white/60 shadow-sm">
                    <div class="h-16 px-4 lg:px-6 flex items-center gap-3">
                        <button
                            class="p-2 -m-2 rounded-lg hover:bg-slate-100 lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka Sidebar">
                            <i class='bx bx-menu text-xl'></i>
                        </button>
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-primary-100">
                                <i class='bx bxs-dashboard text-primary-700 text-lg'></i>
                            </span>
                            <span class="font-semibold tracking-wide truncate">MORRA ERP</span>
                        </div>
                        <div class="ml-auto flex items-center gap-3">
                            
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-primary-700 font-semibold text-sm">
                                            <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                                        </span>
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-medium text-slate-900"><?php echo e(auth()->user()->name); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo e(auth()->user()->role->display_name ?? 'User'); ?></p>
                                    </div>
                                    <i class='bx bx-chevron-down text-slate-400'></i>
                                </button>

                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50"
                                     style="display: none;">
                                    
                                    
                                    <div class="px-4 py-3 border-b border-slate-200">
                                        <p class="text-sm font-medium text-slate-900"><?php echo e(auth()->user()->name); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo e(auth()->user()->email); ?></p>
                                    </div>

                                    
                                    <a href="#" onclick="openProfileModal(); return false;" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i class='bx bx-user text-lg'></i>
                                        <span>Edit Profil</span>
                                    </a>
                                    
                                    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i class='bx bx-cog text-lg'></i>
                                        <span>Pengaturan</span>
                                    </a>

                                    <div class="border-t border-slate-200 my-2"></div>

                                    
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class='bx bx-log-out text-lg'></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        
        <div class="bg-white border-b border-slate-200 px-4 lg:px-6 overflow-x-auto">
            <div class="flex items-center gap-1 min-w-max">
                <template x-for="(tab, index) in tabs" :key="tab.id">
                    <div class="relative group flex items-center">
                        <button
                            @click="switchTab(tab.id)"
                            :class="activeTab === tab.id ? 'bg-primary-50 text-primary-700 border-b-2 border-primary-600' : 'text-slate-600 hover:bg-slate-50'"
                            class="flex items-center gap-2 px-4 py-3 text-sm font-medium transition-colors whitespace-nowrap"
                        >
                            <i :class="tab.icon" class="text-base"></i>
                            <span x-text="tab.title" class="max-w-[150px] truncate"></span>
                            
                            
                            <button
                                x-show="tab.type === 'iframe' && tab.url"
                                @click.stop="refreshTab(tab.id)"
                                class="ml-1 p-0.5 rounded hover:bg-slate-200 transition-colors"
                                :class="activeTab === tab.id ? 'hover:bg-primary-200' : ''"
                                title="Refresh Tab"
                            >
                                <i class='bx bx-refresh text-lg'></i>
                            </button>
                            
                            
                            <button
                                x-show="tabs.length > 1"
                                @click.stop="closeTab(tab.id)"
                                class="ml-1 p-0.5 rounded hover:bg-slate-200 transition-colors"
                                :class="activeTab === tab.id ? 'hover:bg-primary-200' : ''"
                                title="Close Tab"
                            >
                                <i class='bx bx-x text-lg'></i>
                            </button>
                        </button>
                    </div>
                </template>
                
                
                <button
                    @click="createNewTab()"
                    class="flex items-center justify-center w-10 h-10 text-slate-600 hover:bg-slate-50 rounded-lg transition-colors"
                    title="Tab Baru (Ctrl+T)"
                >
                    <i class='bx bx-plus text-xl'></i>
                </button>
            </div>
        </div>

        
        <div class="relative">
            <template x-for="tab in tabs" :key="tab.id">
                <div
                    x-show="activeTab === tab.id"
                    :id="'tab-content-' + tab.id"
                    class="tab-content-wrapper"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
                    
                    <div class="px-4 lg:px-6 py-6 overflow-x-hidden">
                        <template x-if="tab.type === 'initial'">
                            <div>
                                <?php echo e($slot); ?>

                            </div>
                        </template>
                        <template x-if="tab.type === 'empty'">
                            <div class="flex flex-col items-center justify-center py-20">
                                <div class="text-center max-w-md">
                                    <div class="mb-6">
                                        <i class='bx bx-tab text-6xl text-slate-300'></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-700 mb-2">Tab Baru</h3>
                                    <p class="text-slate-500 mb-6">
                                        Silakan klik menu di sidebar untuk menampilkan halaman di tab ini
                                    </p>
                                    <div class="flex items-center justify-center gap-2 text-sm text-slate-400">
                                        <i class='bx bx-info-circle'></i>
                                        <span>Atau tekan Ctrl+T untuk tab baru</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="tab.type === 'iframe'">
                            <iframe
                                :src="tab.url"
                                class="w-full border-0"
                                style="min-height: calc(100vh - 200px); height: calc(100vh - 200px);"
                                @load="() => { 
                                    const tabIndex = tabs.findIndex(t => t.id === tab.id);
                                    if (tabIndex !== -1) {
                                        tabs[tabIndex] = { ...tabs[tabIndex], loading: false };
                                        console.log('✅ Iframe loaded for tab:', tab.id);
                                    }
                                }"
                            ></iframe>
                        </template>
                        <template x-if="tab.type === 'ajax' && tab.content">
                            <div x-html="tab.content"></div>
                        </template>
                        <template x-if="tab.loading">
                            <div class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-8 w-8 border-2 border-primary-500 border-t-transparent"></div>
                                <span class="ml-3 text-slate-600">Memuat halaman...</span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <footer class="px-4 lg:px-6 pb-6 text-xs text-slate-500">
            © <?php echo e(date('Y')); ?> Morra ERP
        </footer>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal0308aecb0d47e515fda9f91c69b09273 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0308aecb0d47e515fda9f91c69b09273 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0308aecb0d47e515fda9f91c69b09273)): ?>
<?php $attributes = $__attributesOriginal0308aecb0d47e515fda9f91c69b09273; ?>
<?php unset($__attributesOriginal0308aecb0d47e515fda9f91c69b09273); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0308aecb0d47e515fda9f91c69b09273)): ?>
<?php $component = $__componentOriginal0308aecb0d47e515fda9f91c69b09273; ?>
<?php unset($__componentOriginal0308aecb0d47e515fda9f91c69b09273); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal2e34c6cb1e0dc04b2322c62dc15a0c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2e34c6cb1e0dc04b2322c62dc15a0c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat-panel','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2e34c6cb1e0dc04b2322c62dc15a0c51)): ?>
<?php $attributes = $__attributesOriginal2e34c6cb1e0dc04b2322c62dc15a0c51; ?>
<?php unset($__attributesOriginal2e34c6cb1e0dc04b2322c62dc15a0c51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2e34c6cb1e0dc04b2322c62dc15a0c51)): ?>
<?php $component = $__componentOriginal2e34c6cb1e0dc04b2322c62dc15a0c51; ?>
<?php unset($__componentOriginal2e34c6cb1e0dc04b2322c62dc15a0c51); ?>
<?php endif; ?>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('loader', {
        show() { document.querySelector('[x-show="loading"]').__x.$data.loading = true },
        hide() { document.querySelector('[x-show="loading"]').__x.$data.loading = false }
    });
});

document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        const href = link.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript')) {
            document.querySelector('[x-show="loading"]').style.display = 'flex';
        }
    });
});
</script>

<script>
// ========================================
// TAB SYSTEM - Multi-tasking dalam ERP
// ========================================

function tabSystem() {
    return {
        tabs: [],
        activeTab: null,
        tabCounter: 0,
        navigationIntercepted: false,

        init() {
            console.log('🚀 Tab system initializing...');
            
            // Set global flag IMMEDIATELY - BEFORE anything else
            window.TAB_SYSTEM_ACTIVE = true;
            
            // Store reference to this component globally for external access
            window.TAB_SYSTEM_COMPONENT = this;
            
            // Install navigation interception IMMEDIATELY
            this.interceptNavigation();
            console.log('✅ Navigation interception installed IMMEDIATELY');
            
            // Initialize with current page as first tab
            const initialTab = {
                id: this.generateTabId(),
                title: document.title.replace(' - MORRA ERP', '') || 'Dashboard',
                url: window.location.href,
                icon: 'bx bxs-home',
                type: 'initial',
                loading: false,
                content: null
            };
            
            this.tabs.push(initialTab);
            this.activeTab = initialTab.id;
            
            console.log('✅ Initial tab created:', initialTab.id);
            console.log('📊 Tab system initialized with', this.tabs.length, 'tab(s)');
            
            // Handle browser back/forward
            window.addEventListener('popstate', (e) => {
                console.log('🔙 Popstate event:', e.state);
                if (e.state && e.state.tabId) {
                    this.switchTab(e.state.tabId);
                }
            });
            
            // Keyboard shortcuts
            this.initKeyboardShortcuts();
            console.log('⌨️ Keyboard shortcuts enabled');
            
            // Save initial state
            history.replaceState({ tabId: initialTab.id }, '', window.location.href);
            
            // Signal that tab system is fully ready
            window.TAB_SYSTEM_READY = true;
            window.dispatchEvent(new CustomEvent('tab-system-ready'));
            
            console.log('🎉 Tab system ready!');
        },

        generateTabId() {
            return 'tab_' + (++this.tabCounter) + '_' + Date.now();
        },

        initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ctrl + T: New tab
                if (e.ctrlKey && e.key === 't') {
                    e.preventDefault();
                    this.createNewTab();
                }
                
                // Ctrl + W: Close tab
                if (e.ctrlKey && e.key === 'w') {
                    e.preventDefault();
                    this.closeTab(this.activeTab);
                }
                
                // Ctrl + Tab: Next tab
                if (e.ctrlKey && e.key === 'Tab') {
                    e.preventDefault();
                    const currentIndex = this.tabs.findIndex(t => t.id === this.activeTab);
                    const nextIndex = (currentIndex + 1) % this.tabs.length;
                    this.switchTab(this.tabs[nextIndex].id);
                }
                
                // Ctrl + 1-9: Switch to tab by number
                if (e.ctrlKey && e.key >= '1' && e.key <= '9') {
                    e.preventDefault();
                    const index = parseInt(e.key) - 1;
                    if (this.tabs[index]) {
                        this.switchTab(this.tabs[index].id);
                    }
                }
            });
        },

        createNewTab() {
            const newTab = {
                id: this.generateTabId(),
                title: 'Tab Baru',
                url: null,
                icon: 'bx bx-tab',
                type: 'empty',
                loading: false,
                content: null
            };
            
            this.tabs.push(newTab);
            
            // Use $nextTick to ensure Alpine.js reactivity
            this.$nextTick(() => {
                this.activeTab = newTab.id;
                
                console.log('New tab created:', newTab.id);
                console.log('Active tab is now:', this.activeTab);
                console.log('Total tabs:', this.tabs.length);
                
                // Update browser history
                history.pushState({ tabId: newTab.id }, 'Tab Baru', window.location.pathname);
            });
        },

        interceptNavigation() {
            const self = this;
            
            console.log('🔧 Installing navigation interception...');
            
            // CRITICAL: Install on window with capture phase for HIGHEST priority
            window.addEventListener('click', function(e) {
                if (!window.TAB_SYSTEM_ACTIVE) return;
                
                const link = e.target.closest('a[href]');
                if (!link) return;
                
                const href = link.getAttribute('href');
                
                console.log('🖱️ === WINDOW CLICK DETECTED ===');
                console.log('Target:', e.target);
                console.log('Link:', link);
                console.log('Href:', href);
                
                // Check if in sidebar FIRST
                const sidebar = link.closest('aside');
                const isInSidebar = sidebar !== null;
                
                console.log('📍 Is in sidebar:', isInSidebar);
                
                if (!isInSidebar) {
                    console.log('⏭️ Not in sidebar, allowing default');
                    return;
                }
                
                // Skip special cases
                if (!href || 
                    href === '#' || 
                    href.startsWith('javascript:') ||
                    href.includes('logout') ||
                    link.hasAttribute('onclick')) {
                    console.log('⏭️ Skipping (special case)');
                    return;
                }
                
                // PREVENT ALL NAVIGATION FROM SIDEBAR
                console.log('🚫 PREVENTING DEFAULT - Sidebar link!');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const title = self.extractTitle(link);
                const icon = self.extractIcon(link);
                
                console.log('📝 Loading in active tab:', { href, title, icon });
                self.loadInActiveTab(href, title, icon);
                
                return false;
            }, true); // Capture phase = HIGHEST PRIORITY
            
            console.log('✅ Window-level navigation interception installed');
        },

        extractTitle(link) {
            // Try to get title from link text
            const text = link.textContent.trim();
            if (text && text.length > 0 && text.length < 50) {
                return text;
            }
            return 'Halaman Baru';
        },

        extractIcon(link) {
            // Try to find icon in link
            const icon = link.querySelector('i[class*="bx"]');
            if (icon) {
                return icon.className;
            }
            return 'bx bx-file';
        },

        loadInActiveTab(url, title = 'Halaman Baru', icon = null) {
            console.log('🎯 loadInActiveTab called');
            console.log('  URL:', url);
            console.log('  Title:', title);
            console.log('  Icon:', icon);
            console.log('  Current activeTab:', this.activeTab);
            console.log('  All tabs:', this.tabs.map(t => ({ id: t.id, title: t.title, type: t.type })));
            
            const activeTabObj = this.tabs.find(t => t.id === this.activeTab);
            if (!activeTabObj) {
                console.error('❌ Active tab not found! activeTab:', this.activeTab);
                console.error('Available tabs:', this.tabs);
                return;
            }
            
            console.log('✅ Found active tab:', activeTabObj.id, activeTabObj.title);
            
            // Find tab index
            const tabIndex = this.tabs.findIndex(t => t.id === this.activeTab);
            if (tabIndex === -1) {
                console.error('❌ Tab index not found');
                return;
            }
            
            // Update tab to use iframe (simpler and more reliable)
            this.tabs[tabIndex] = {
                ...this.tabs[tabIndex],
                title: title,
                url: url, // Keep original URL for iframe
                icon: icon || 'bx bx-file',
                type: 'iframe',
                loading: true,
                content: null
            };
            
            console.log('✅ Tab updated to iframe mode:', {
                id: this.tabs[tabIndex].id,
                title: this.tabs[tabIndex].title,
                url: this.tabs[tabIndex].url,
                type: this.tabs[tabIndex].type
            });
            
            // CRITICAL: DO NOT change browser URL - keep it as /admin always
            // This ensures browser reload always goes back to /admin with full layout
            console.log('🔒 Browser URL kept as /admin (not changed to maintain layout on reload)');
            
            // Update document title to reflect current page
            document.title = title + ' - MORRA ERP';
            
            console.log('✅ loadInActiveTab completed');
        },

        async loadTabContent(tab) {
            console.log('📥 Loading content for tab:', tab.id, tab.url);
            console.log('🎯 Target URL:', tab.url);
            
            try {
                console.log('🌐 Fetching:', tab.url);
                
                const response = await fetch(tab.url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                });
                
                console.log('📡 Response status:', response.status, response.statusText);
                console.log('📡 Response URL:', response.url);
                
                if (!response.ok) {
                    throw new Error('Failed to load page: ' + response.status + ' ' + response.statusText);
                }
                
                const html = await response.text();
                console.log('📄 HTML received, length:', html.length);
                
                // Parse HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Log the page title to verify correct page
                const pageTitle = doc.querySelector('title')?.textContent || 'Unknown';
                console.log('📄 Page title:', pageTitle);
                
                // STEP 1: First, try to find and extract main content BEFORE removing layout
                let content = null;
                let contentSource = 'unknown';
                
                // Priority selectors - looking for actual page content (skip tab bar and header)
                const contentSelectors = [
                    // Look for the actual page content area (after header and tab bar)
                    { selector: '.lg\\:pl-80 > div:not(header):not(.bg-white.border-b)', name: 'main content (not header/tabbar)' },
                    { selector: '.lg\\:pl-80 > div > div.px-4.lg\\:px-6.py-6', name: 'page content with padding' },
                    { selector: 'main', name: 'main' },
                    { selector: '[role="main"]', name: 'role=main' },
                    { selector: '.main-content', name: '.main-content' },
                    { selector: '#content', name: '#content' }
                ];
                
                for (const { selector, name } of contentSelectors) {
                    try {
                        const el = doc.querySelector(selector);
                        if (el && el.innerHTML.trim().length > 100) {
                            // Clone the element to preserve it
                            const clone = el.cloneNode(true);
                            
                            // Remove unwanted elements from the clone
                            clone.querySelectorAll(`
                                aside,
                                nav,
                                header,
                                .tab-bar,
                                [x-data*="tabSystem"],
                                .modal,
                                .modal-backdrop,
                                [id*="Modal"],
                                [id*="modal"],
                                script,
                                style,
                                .bg-white.border-b.px-4,
                                div[class*="flex items-center gap-1"]
                            `).forEach(el => el.remove());
                            
                            // Check if still has meaningful content after cleanup
                            const cleanedContent = clone.innerHTML.trim();
                            if (cleanedContent.length > 500 && 
                                !cleanedContent.includes('@click="createNewTab()"') &&
                                !cleanedContent.includes('class="tab-bar"')) {
                                content = cleanedContent;
                                contentSource = name;
                                console.log('✂️ Content extracted from:', name, 'length:', content.length);
                                break;
                            } else {
                                console.log('⚠️ Skipping', name, '- too small or contains tab bar');
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to extract from', name, e);
                    }
                }
                
                // STEP 2: If no content found, try to get all content divs and filter
                if (!content || content.length < 500) {
                    console.log('⚠️ Primary extraction failed, trying to find content divs...');
                    
                    // Get all divs in the main area
                    const mainWrapper = doc.querySelector('.lg\\:pl-80');
                    if (mainWrapper) {
                        const allDivs = Array.from(mainWrapper.querySelectorAll(':scope > div'));
                        console.log('📦 Found', allDivs.length, 'direct child divs in main wrapper');
                        
                        // Find the largest div that's not header or tab bar
                        let largestDiv = null;
                        let largestSize = 0;
                        
                        allDivs.forEach((div, index) => {
                            const size = div.innerHTML.length;
                            const isHeader = div.querySelector('header') || div.classList.contains('sticky');
                            const isTabBar = div.innerHTML.includes('@click="createNewTab()"') || 
                                           div.innerHTML.includes('class="tab-bar"');
                            
                            console.log(`  Div ${index}: size=${size}, isHeader=${isHeader}, isTabBar=${isTabBar}`);
                            
                            if (!isHeader && !isTabBar && size > largestSize) {
                                largestDiv = div;
                                largestSize = size;
                            }
                        });
                        
                        if (largestDiv && largestSize > 500) {
                            const clone = largestDiv.cloneNode(true);
                            
                            // Clean the clone
                            clone.querySelectorAll(`
                                aside,
                                nav,
                                header,
                                .tab-bar,
                                [x-data*="tabSystem"],
                                .modal,
                                script,
                                style
                            `).forEach(el => el.remove());
                            
                            content = clone.innerHTML;
                            contentSource = 'largest content div';
                            console.log('✂️ Content extracted from:', contentSource, 'length:', content.length);
                        }
                    }
                }
                
                // STEP 3: Last resort - clone body and aggressive cleanup
                if (!content || content.length < 500) {
                    console.log('⚠️ Still no content, trying body clone with aggressive cleanup...');
                    
                    const bodyClone = doc.body.cloneNode(true);
                    
                    // Remove ALL layout and navigation elements
                    bodyClone.querySelectorAll(`
                        aside, 
                        nav, 
                        header,
                        .tab-bar,
                        [x-data*="tabSystem"],
                        [x-data*="sidebarOpen"],
                        .modal,
                        .modal-backdrop,
                        [id*="Modal"],
                        [id*="modal"],
                        script, 
                        style,
                        .fixed,
                        .sticky,
                        .bg-white.border-b.px-4,
                        div[class*="flex items-center gap-1 min-w-max"]
                    `).forEach(el => {
                        console.log('🗑️ Removing from clone:', el.tagName, el.className?.substring(0, 50) || el.id);
                        el.remove();
                    });
                    
                    content = bodyClone.innerHTML;
                    contentSource = 'body (aggressive cleanup)';
                    console.log('✂️ Content extracted from:', contentSource, 'length:', content.length);
                }
                
                // STEP 4: Additional cleanup
                if (content) {
                    // Remove any remaining tab system references
                    content = content
                        .replace(/x-data="tabSystem\(\)"/g, '')
                        .replace(/x-show="activeTab === tab\.id"/g, '')
                        .replace(/@click="createNewTab\(\)"/g, '')
                        .replace(/@click="switchTab\([^)]+\)"/g, '')
                        .replace(/@click="closeTab\([^)]+\)"/g, '')
                        .replace(/<template[^>]*x-for[^>]*>[\s\S]*?<\/template>/g, '');
                    
                    // Final check: ensure no tab system in content
                    if (content.includes('x-data="tabSystem()"') || 
                        content.includes('createNewTab()') ||
                        content.includes('class="tab-bar"')) {
                        console.warn('⚠️ Content still contains tab system, doing final cleanup...');
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = content;
                        tempDiv.querySelectorAll('[x-data*="tabSystem"], .tab-bar, aside, nav, .modal, header').forEach(el => el.remove());
                        content = tempDiv.innerHTML;
                        console.log('✂️ After final cleanup, length:', content.length);
                    }
                }
                
                // STEP 5: Verify we have meaningful content
                if (!content || content.trim().length < 200) {
                    throw new Error('No meaningful content could be extracted from the page');
                }
                
                console.log('✅ Final content preview (first 200 chars):', content.substring(0, 200));
                
                // Update tab with content
                const tabIndex = this.tabs.findIndex(t => t.id === tab.id);
                if (tabIndex !== -1) {
                    // CRITICAL: Create new object to trigger reactivity
                    this.tabs[tabIndex] = {
                        ...this.tabs[tabIndex],
                        content: content,
                        loading: false
                    };
                    
                    console.log('✅ Tab content loaded successfully');
                    console.log('✅ Tab state:', {
                        id: this.tabs[tabIndex].id,
                        title: this.tabs[tabIndex].title,
                        url: this.tabs[tabIndex].url,
                        loading: this.tabs[tabIndex].loading,
                        hasContent: !!this.tabs[tabIndex].content,
                        contentLength: this.tabs[tabIndex].content?.length || 0
                    });
                    
                    // Force Alpine to re-render
                    this.$nextTick(() => {
                        console.log('🔄 Forcing Alpine re-render');
                        // Execute scripts in loaded content
                        this.executeScripts(tab.id);
                    });
                } else {
                    console.error('❌ Tab not found in array after load');
                }
                
            } catch (error) {
                console.error('❌ Error loading tab content:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack
                });
                
                const tabIndex = this.tabs.findIndex(t => t.id === tab.id);
                if (tabIndex !== -1) {
                    this.tabs[tabIndex] = {
                        ...this.tabs[tabIndex],
                        content: `
                            <div class="flex items-center justify-center py-12">
                                <div class="text-center max-w-md">
                                    <i class='bx bx-error-circle text-5xl text-red-500 mb-4'></i>
                                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Gagal Memuat Halaman</h3>
                                    <p class="text-slate-600 mb-4">Terjadi kesalahan saat memuat konten: ${error.message}</p>
                                    <button onclick="location.reload()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                        Muat Ulang
                                    </button>
                                </div>
                            </div>
                        `,
                        loading: false
                    };
                    
                    console.log('✅ Error content set, loading stopped');
                }
            }
        },

        executeScripts(tabId) {
            const tabContent = document.getElementById('tab-content-' + tabId);
            if (!tabContent) return;
            
            const scripts = tabContent.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    newScript.textContent = script.textContent;
                }
                script.parentNode.replaceChild(newScript, script);
            });
        },

        switchTab(tabId) {
            const tab = this.tabs.find(t => t.id === tabId);
            if (!tab) {
                console.error('Tab not found:', tabId);
                return;
            }
            
            console.log('Switching to tab:', tabId, tab.title);
            console.log('Previous activeTab:', this.activeTab);
            
            this.activeTab = tabId;
            
            console.log('New activeTab:', this.activeTab);
            
            // CRITICAL: DO NOT change browser URL - keep it as /admin always
            // This ensures browser reload always goes back to /admin with full layout
            console.log('🔒 Browser URL kept as /admin (not changed to maintain layout on reload)');
            
            // Update document title to reflect current tab
            document.title = tab.title + ' - MORRA ERP';
        },

        closeTab(tabId) {
            const index = this.tabs.findIndex(t => t.id === tabId);
            if (index === -1) return;
            
            // Don't close if it's the only tab
            if (this.tabs.length === 1) {
                alert('Tidak dapat menutup tab terakhir');
                return;
            }
            
            // If closing active tab, switch to adjacent tab
            if (this.activeTab === tabId) {
                const newIndex = index > 0 ? index - 1 : index + 1;
                this.switchTab(this.tabs[newIndex].id);
            }
            
            // Remove tab
            this.tabs.splice(index, 1);
        },

        refreshTab(tabId = null) {
            const id = tabId || this.activeTab;
            const tab = this.tabs.find(t => t.id === id);
            if (!tab) return;
            
            console.log('🔄 Refreshing tab:', id, tab.title);
            
            if (tab.type === 'initial') {
                // Reload entire page for initial tab
                window.location.reload();
            } else if (tab.type === 'empty') {
                // Do nothing for empty tab
                console.log('⚠️ Cannot refresh empty tab');
                return;
            } else if (tab.type === 'iframe' && tab.url) {
                // Reload iframe by updating src
                const iframe = document.querySelector(`#tab-content-${id} iframe`);
                if (iframe) {
                    tab.loading = true;
                    // Force reload by setting src again
                    const currentSrc = iframe.src;
                    iframe.src = 'about:blank';
                    setTimeout(() => {
                        iframe.src = currentSrc;
                        console.log('✅ Iframe reloaded');
                    }, 100);
                }
            } else {
                // Reload content for AJAX tabs
                tab.loading = true;
                tab.content = null;
                this.loadTabContent(tab);
            }
        },

        reloadTab(tabId = null) {
            // Alias for refreshTab
            this.refreshTab(tabId);
        }
    };
}

// ========================================
// GLOBAL UTILITIES FOR ERP OPTIMIZATION
// ========================================
    
    // Modal Loader Utility
    window.ModalLoader = {
        show() {
            const el = document.querySelector('#modal-loader');
            if (el) el.style.display = 'flex';
        },
        hide() {
            const el = document.querySelector('#modal-loader');
            if (el) el.style.display = 'none';
        }
    };
    
    // API Cache Utility - Cache API responses untuk mengurangi request
    window.APICache = {
        cache: new Map(),
        ttl: 5 * 60 * 1000, // 5 menit default
        
        set(key, data, customTTL = null) {
            this.cache.set(key, {
                data,
                timestamp: Date.now(),
                ttl: customTTL || this.ttl
            });
        },
        
        get(key) {
            const item = this.cache.get(key);
            if (!item) return null;
            
            if (Date.now() - item.timestamp > item.ttl) {
                this.cache.delete(key);
                return null;
            }
            
            return item.data;
        },
        
        clear(key = null) {
            if (key) {
                this.cache.delete(key);
            } else {
                this.cache.clear();
            }
        }
    };
    
    // Optimized Fetch Utility dengan caching
    window.fetchWithCache = async function(url, options = {}, cacheTTL = null) {
        const cacheKey = url + JSON.stringify(options);
        
        // Check cache first
        const cached = window.APICache.get(cacheKey);
        if (cached) {
            console.log('📦 Cache hit:', url);
            return cached;
        }
        
        // Fetch from API
        console.log('🌐 API call:', url);
        const response = await fetch(url, options);
        const data = await response.json();
        
        // Store in cache
        window.APICache.set(cacheKey, data, cacheTTL);
        
        return data;
    };
    
    // Parallel Fetch Utility
    window.fetchParallel = async function(requests) {
        return Promise.all(
            requests.map(req => 
                fetch(req.url, req.options || {}).then(r => r.json())
            )
        );
    };
    
    // Debounce Utility untuk search/filter
    window.debounce = function(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Contoh otomatis jika kamu pakai jQuery untuk load modal
    if (window.$) {
        $(document).on('show.bs.modal', function (e) {
            const $modal = $(e.target);
            const url = $modal.data('url');
            if (url) {
                ModalLoader.show();
                $modal.find('.modal-content').load(url, function() {
                    ModalLoader.hide();
                });
            }
        });
    }

    // Kalau kamu pakai Fetch API untuk modal:
    window.loadModalContent = async function (selector, url) {
        try {
            ModalLoader.show();
            const res = await fetch(url);
            const html = await res.text();
            document.querySelector(selector).innerHTML = html;
        } catch (err) {
            console.error('Gagal memuat modal:', err);
        } finally {
            ModalLoader.hide();
        }
    }
</script>

<script>
  // Optimized loading - hide immediately when DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById("global-loading");
    if (overlay) {
      overlay.classList.add("opacity-0");
      setTimeout(() => overlay.style.display = "none", 300);
    }
  });
  
  // Fallback: hide after max 2 seconds
  setTimeout(() => {
    const overlay = document.getElementById("global-loading");
    if (overlay && overlay.style.display !== "none") {
      overlay.classList.add("opacity-0");
      setTimeout(() => overlay.style.display = "none", 300);
    }
  }, 2000);
</script>


<div class="modal fade" id="profileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-slate-200 bg-slate-50">
                <h5 class="modal-title font-semibold">Edit Profil</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="profileForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="profile_name" value="<?php echo e(auth()->user()->name); ?>" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" id="profile_email" value="<?php echo e(auth()->user()->email); ?>" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">No. Telepon</label>
                        <input type="text" name="phone" id="profile_phone" value="<?php echo e(auth()->user()->phone); ?>" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru <span class="text-xs text-slate-500">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="profile_password" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50">
                    <button type="button" class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" data-dismiss="modal">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openProfileModal() {
    $('#profileModal').modal('show');
}

$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '<?php echo e(route("admin.users.update", auth()->id())); ?>',
        type: 'PUT',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                alert('Profil berhasil diupdate');
                location.reload();
            }
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
        }
    });
});
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>


<script>
// ========================================
// TAB SYSTEM HELPER - Wait for initialization
// ========================================
(function() {
    'use strict';
    
    console.log('🔧 Tab system helper loading...');
    
    // Wait for tab system to be ready
    let checkCount = 0;
    const maxChecks = 20;
    
    const checkTabSystem = function() {
        checkCount++;
        
        if (window.TAB_SYSTEM_READY && window.TAB_SYSTEM_COMPONENT) {
            console.log('✅ Tab system confirmed ready after', checkCount, 'checks');
            return true;
        }
        
        if (checkCount < maxChecks) {
            setTimeout(checkTabSystem, 100);
        } else {
            console.warn('⚠️ Tab system not ready after', maxChecks, 'checks');
        }
        
        return false;
    };
    
    // Start checking when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkTabSystem);
    } else {
        checkTabSystem();
    }
    
    // Listen for tab system ready event
    window.addEventListener('tab-system-ready', function() {
        console.log('✅ Tab system ready event received');
    });
    
    console.log('✅ Tab system helper installed');
})();
</script>


<?php if(auth()->guard()->check()): ?>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    // Initialize Pusher and Laravel Echo
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '<?php echo e(config('broadcasting.connections.pusher.key')); ?>',
        cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
        forceTLS: true,
        encrypted: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        }
    });
    
    // Subscribe to user's private chat channel
    window.Echo.private('chat.<?php echo e(auth()->id()); ?>')
        .listen('.message.sent', (e) => {
            console.log('📨 New message received:', e.message);
            
            // Dispatch custom event for chat components to listen
            window.dispatchEvent(new CustomEvent('chat-message-received', {
                detail: e.message
            }));
        });
    
    console.log('✅ Laravel Echo initialized for user <?php echo e(auth()->id()); ?>');
</script>
<?php endif; ?>


<script>
// Disable all auto-opening modals on page load
document.addEventListener('DOMContentLoaded', function() {
    // Close all Bootstrap modals
    $('.modal').modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
    
    // Remove any inline styles that might keep modals visible
    $('.modal').css('display', 'none');
    
    console.log('✅ All modals closed on page load');
});

// Prevent modals from auto-opening
$(document).on('show.bs.modal', '.modal', function(e) {
    // Allow manual triggers only
    if (!e.namespace || e.namespace !== 'bs.modal') {
        console.warn('⚠️ Prevented auto-opening modal:', this.id);
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
});

    // Tab refresh notification function
    function showTabRefreshNotification(pageTitle = "Tab") {
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
                    <button onclick="this.closest('.tab-refresh-notification').remove()" 
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
                    <button onclick="this.closest('.reload-guidance-notification').remove()" 
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
    }
    
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
    urlChangeObserver.observe(document, { subtree: true, childList: true });
    </script>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\components\layouts\admin-with-tabs.blade.php ENDPATH**/ ?>