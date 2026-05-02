/**
 * Browser Reload Redirect System
 * Mengarahkan semua reload browser ke baseUrl/admin
 * Tanpa mengganggu sistem tab yang sudah ada
 */

(function() {
    'use strict';
    
    console.log('🚀 Browser Reload Redirect System initializing...');
    
    // Konfigurasi
    const config = {
        adminBaseUrl: '/admin',
        excludePatterns: [
            '/login',
            '/logout', 
            '/register',
            '/password',
            '/api/',
            '/broadcasting/',
            '/health'
        ],
        debug: true
    };
    
    // Helper functions
    function log(message, data = null) {
        if (config.debug) {
            console.log(`🔄 [RELOAD-REDIRECT] ${message}`, data || '');
        }
    }
    
    function isExcludedUrl(url) {
        return config.excludePatterns.some(pattern => url.includes(pattern));
    }
    
    function isAdminArea(url = window.location.href) {
        return url.includes('/admin') || url.includes('admin.');
    }
    
    function shouldRedirectToAdmin(url = window.location.href) {
        // Jangan redirect jika sudah di admin atau di halaman yang dikecualikan
        if (isAdminArea(url) || isExcludedUrl(url)) {
            return false;
        }
        
        // Redirect semua halaman lain ke admin
        return true;
    }
    
    // Main redirect function
    function redirectToAdmin() {
        const targetUrl = window.location.origin + config.adminBaseUrl;
        
        log('Redirecting to admin dashboard', {
            from: window.location.href,
            to: targetUrl
        });
        
        // Set flag untuk mencegah loop
        window.NAVIGATING_AWAY = true;
        
        // Redirect ke admin
        window.location.href = targetUrl;
    }
    
    // ========================================
    // INTEGRATION WITH EXISTING TAB SYSTEM
    // ========================================
    
    // Jika sudah di admin area, jangan lakukan redirect
    if (isAdminArea()) {
        log('Already in admin area, skipping redirect system');
        
        // Hanya tambahkan enhancement untuk tab system yang sudah ada
        document.addEventListener('keydown', function(event) {
            // Ctrl+R atau F5 - biarkan tab system yang handle
            if ((event.ctrlKey && event.key.toLowerCase() === 'r') || event.key === 'F5') {
                log('Refresh key detected in admin area - letting tab system handle');
                // Tab system sudah ada logic untuk handle ini
                return;
            }
        });
        
        log('Admin area enhancements loaded');
        return;
    }
    
    // ========================================
    // REDIRECT LOGIC FOR NON-ADMIN AREAS
    // ========================================
    
    // 1. Deteksi page load (termasuk reload)
    window.addEventListener('load', function() {
        log('Page load detected', {
            url: window.location.href,
            referrer: document.referrer,
            isAdminArea: isAdminArea(),
            shouldRedirect: shouldRedirectToAdmin()
        });
        
        // Jika bukan di admin area dan bukan halaman yang dikecualikan
        if (shouldRedirectToAdmin()) {
            log('Page load redirect triggered');
            
            // Delay sedikit untuk memastikan page sudah fully loaded
            setTimeout(() => {
                redirectToAdmin();
            }, 100);
        }
    });
    
    // 2. Override keyboard shortcuts untuk redirect
    document.addEventListener('keydown', function(event) {
        // Ctrl+R atau F5
        if ((event.ctrlKey && event.key.toLowerCase() === 'r') || event.key === 'F5') {
            const currentUrl = window.location.href;
            
            log('Refresh key detected', {
                key: event.key,
                ctrlKey: event.ctrlKey,
                url: currentUrl,
                shouldRedirect: shouldRedirectToAdmin(currentUrl)
            });
            
            // Jika bukan di admin area, redirect ke admin
            if (shouldRedirectToAdmin(currentUrl)) {
                log('Redirecting refresh to admin');
                event.preventDefault();
                redirectToAdmin();
                return false;
            }
        }
        
        // Ctrl+Shift+R (hard refresh)
        if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === 'r') {
            const currentUrl = window.location.href;
            
            log('Hard refresh detected', {
                url: currentUrl,
                shouldRedirect: shouldRedirectToAdmin(currentUrl)
            });
            
            if (shouldRedirectToAdmin(currentUrl)) {
                event.preventDefault();
                
                const confirmRedirect = confirm(
                    '🚨 HARD REFRESH DETECTED!\n\n' +
                    'Anda akan diarahkan ke dashboard admin.\n\n' +
                    'Lanjutkan?'
                );
                
                if (confirmRedirect) {
                    redirectToAdmin();
                }
                
                return false;
            }
        }
    });
    
    // 3. Deteksi navigation via history (back/forward)
    window.addEventListener('popstate', function(event) {
        const currentUrl = window.location.href;
        
        log('Popstate detected', {
            url: currentUrl,
            state: event.state,
            shouldRedirect: shouldRedirectToAdmin(currentUrl)
        });
        
        // Jika navigation membawa ke halaman non-admin, redirect ke admin
        if (shouldRedirectToAdmin(currentUrl)) {
            log('Popstate redirect triggered');
            
            // Delay sedikit untuk memastikan popstate selesai
            setTimeout(() => {
                redirectToAdmin();
            }, 50);
        }
    });
    
    // ========================================
    // NOTIFICATION SYSTEM
    // ========================================
    
    function showRedirectNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.redirect-notification').forEach(n => n.remove());
        
        const colors = {
            info: 'bg-blue-500',
            success: 'bg-green-500',
            warning: 'bg-yellow-500',
            error: 'bg-red-500'
        };
        
        const notification = document.createElement('div');
        notification.className = 'redirect-notification';
        notification.innerHTML = `
            <div class="fixed top-4 right-4 z-[99999] ${colors[type]} text-white px-6 py-4 rounded-lg shadow-2xl max-w-md">
                <div class="flex items-center">
                    <i class="bx bx-info-circle mr-3 text-xl"></i>
                    <div class="flex-1">
                        <div class="font-bold text-sm">🔄 REDIRECT AKTIF</div>
                        <div class="text-xs mt-1">${message}</div>
                    </div>
                    <button onclick="this.closest('.redirect-notification').remove()" class="ml-3 text-white hover:text-gray-200">
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
        
        log('Redirect notification shown', message);
    }
    
    // ========================================
    // INITIALIZATION COMPLETE
    // ========================================
    
    // Set global flag
    window.BROWSER_RELOAD_REDIRECT_ACTIVE = true;
    
    // Dispatch ready event
    window.dispatchEvent(new CustomEvent('browser-reload-redirect-ready'));
    
    log('Browser Reload Redirect System initialized successfully', {
        adminBaseUrl: config.adminBaseUrl,
        excludePatterns: config.excludePatterns,
        currentUrl: window.location.href,
        isAdminArea: isAdminArea(),
        shouldRedirect: shouldRedirectToAdmin()
    });
    
    // Show notification if system is active and will redirect
    if (shouldRedirectToAdmin()) {
        showRedirectNotification('Sistem akan mengarahkan reload ke dashboard admin', 'info');
    }
    
})();