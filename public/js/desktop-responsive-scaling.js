/**
 * ========================================
 * DESKTOP RESPONSIVE SCALING SYSTEM
 * ========================================
 * 
 * JavaScript untuk menangani:
 * - Dynamic iframe height adjustment
 * - Viewport-based scaling calculation
 * - Smooth resize handling
 * - Iframe content scaling
 */

(function() {
    'use strict';
    
    console.log('🖥️ Desktop Responsive Scaling System initializing...');
    
    // ========================================
    // CONFIGURATION
    // ========================================
    const CONFIG = {
        desktopMin: 768,      // Mulai dari tablet landscape
        desktopBase: 1440,
        desktopMax: 2560,
        minScale: 0.4,        // Scale minimum 40% untuk ukuran kecil
        maxScale: 1.25,
        resizeDebounce: 150,
        iframeHeightPadding: 20
    };
    
    // ========================================
    // UTILITY FUNCTIONS
    // ========================================
    
    /**
     * Check if current viewport is desktop
     */
    function isDesktop() {
        return window.innerWidth >= CONFIG.desktopMin;
    }
    
    /**
     * Calculate scale factor based on viewport width
     */
    function calculateScaleFactor() {
        if (!isDesktop()) return 1;
        
        const viewportWidth = window.innerWidth;
        
        // Linear scaling: semakin kecil window, semakin kecil scale
        // 1024px = 0.75x (minimum)
        // 1440px = 1.0x (base)
        // 2560px = 1.25x (maximum)
        
        if (viewportWidth <= CONFIG.desktopMin) {
            return CONFIG.minScale; // 0.75
        } else if (viewportWidth >= CONFIG.desktopMax) {
            return CONFIG.maxScale; // 1.25
        } else {
            // Linear interpolation
            const ratio = (viewportWidth - CONFIG.desktopMin) / (CONFIG.desktopMax - CONFIG.desktopMin);
            const scale = CONFIG.minScale + (ratio * (CONFIG.maxScale - CONFIG.minScale));
            return scale;
        }
    }
    
    /**
     * Debounce function untuk resize events
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // ========================================
    // IFRAME HEIGHT AUTO-ADJUSTMENT
    // ========================================
    
    /**
     * Adjust iframe height to match content
     */
    function adjustIframeHeight(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        
        // Skip if iframe has data-no-auto-resize attribute
        if (iframe.hasAttribute('data-no-auto-resize')) {
            console.log('⏭️ Skipping iframe height adjustment (data-no-auto-resize)');
            return;
        }
        
        try {
            const iframeDoc = iframe.contentWindow.document;
            if (!iframeDoc || !iframeDoc.body) return;
            
            // Get content height
            const contentHeight = Math.max(
                iframeDoc.body.scrollHeight,
                iframeDoc.body.offsetHeight,
                iframeDoc.documentElement.scrollHeight,
                iframeDoc.documentElement.offsetHeight
            );
            
            // Set iframe height with padding
            const newHeight = contentHeight + CONFIG.iframeHeightPadding;
            iframe.style.height = newHeight + 'px';
            
            console.log('📏 Iframe height adjusted:', newHeight + 'px');
            
        } catch (error) {
            // Cross-origin iframe - cannot access content
            console.log('⚠️ Cannot adjust iframe height (cross-origin)');
        }
    }
    
    /**
     * Setup iframe height observer
     */
    function setupIframeHeightObserver(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        
        // Skip if iframe has data-no-auto-resize attribute
        if (iframe.hasAttribute('data-no-auto-resize')) {
            console.log('⏭️ Skipping iframe height observer (data-no-auto-resize)');
            return;
        }
        
        try {
            const iframeDoc = iframe.contentWindow.document;
            if (!iframeDoc) return;
            
            // Initial adjustment
            adjustIframeHeight(iframe);
            
            // Apply scaling immediately
            applyIframeContentScaling(iframe);
            
            // Watch for content changes
            const observer = new MutationObserver(() => {
                adjustIframeHeight(iframe);
            });
            
            // Observe body changes
            if (iframeDoc.body) {
                observer.observe(iframeDoc.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    characterData: true
                });
            }
            
            // Also adjust on iframe load
            iframe.addEventListener('load', () => {
                setTimeout(() => {
                    adjustIframeHeight(iframe);
                    applyIframeContentScaling(iframe);
                }, 100);
                setTimeout(() => {
                    adjustIframeHeight(iframe);
                    applyIframeContentScaling(iframe);
                }, 500);
                setTimeout(() => {
                    adjustIframeHeight(iframe);
                    applyIframeContentScaling(iframe);
                }, 1000);
            });
            
            console.log('👁️ Iframe height observer setup complete');
            
        } catch (error) {
            console.log('⚠️ Cannot setup iframe observer (cross-origin)');
        }
    }
    
    /**
     * Initialize all iframes on page
     */
    function initializeIframes() {
        const iframes = document.querySelectorAll('iframe');
        
        iframes.forEach(iframe => {
            // Prevent horizontal scroll
            iframe.style.overflowX = 'hidden';
            iframe.style.width = '100%';
            iframe.style.display = 'block';
            
            // Setup height observer
            setupIframeHeightObserver(iframe);
            
            // Apply scaling to iframe content
            applyIframeContentScaling(iframe);
        });
        
        console.log(`✅ Initialized ${iframes.length} iframes`);
    }
    
    // ========================================
    // IFRAME CONTENT SCALING
    // ========================================
    
    /**
     * Apply scaling to iframe content
     */
    function applyIframeContentScaling(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        
        try {
            const iframeDoc = iframe.contentWindow.document;
            if (!iframeDoc) return;
            
            const scaleFactor = calculateScaleFactor();
            const viewportWidth = window.innerWidth;
            const sidebarWidth = Math.max(240, Math.min(320, viewportWidth * 0.2));
            
            // Check if iframe has fixed-layout attribute
            const isFixedLayout = iframe.hasAttribute('data-fixed-layout') || 
                                 iframe.src.includes('/pos') || 
                                 iframe.src.includes('/penjualan-antar-outlet') ||
                                 iframe.src.includes('point-of-sales');
            
            // Inject scaling CSS into iframe
            let styleEl = iframeDoc.getElementById('responsive-scaling-injected');
            
            if (!styleEl) {
                styleEl = iframeDoc.createElement('style');
                styleEl.id = 'responsive-scaling-injected';
                iframeDoc.head.appendChild(styleEl);
            }
            
            if (isFixedLayout) {
                // Fixed layout mode - scale font only, keep positions fixed
                styleEl.textContent = `
                    /* ========================================
                       IFRAME FIXED LAYOUT MODE (POS, etc)
                       ======================================== */
                    @media (min-width: ${CONFIG.desktopMin}px) {
                        html {
                            /* Font scale proporsional dengan viewport */
                            font-size: ${16 * scaleFactor}px !important;
                        }
                        
                        body {
                            overflow-x: hidden !important;
                            max-width: 100% !important;
                            margin: 0 !important;
                            /* Keep original padding - don't scale */
                        }
                        
                        * {
                            max-width: 100% !important;
                            box-sizing: border-box !important;
                        }
                        
                        img, svg, canvas {
                            max-width: 100% !important;
                            height: auto !important;
                        }
                        
                        /* Keep fixed positions */
                        .fixed, [class*="fixed"] {
                            position: fixed !important;
                        }
                        
                        /* Keep absolute positions */
                        .absolute, [class*="absolute"] {
                            position: absolute !important;
                        }
                        
                        /* Prevent layout shift */
                        .container,
                        [class*="container"] {
                            max-width: 100% !important;
                            /* Keep original padding */
                        }
                        
                        /* Scale text proportionally - IMPORTANT: mengecil saat window kecil */
                        button, .btn, [class*="btn-"] {
                            /* Font mengikuti root font-size yang sudah ter-scale */
                        }
                        
                        input, select, textarea {
                            /* Font mengikuti root font-size yang sudah ter-scale */
                        }
                        
                        table {
                            width: 100% !important;
                            /* Font mengikuti root font-size yang sudah ter-scale */
                        }
                        
                        /* Prevent horizontal scroll */
                        .overflow-x-auto {
                            overflow-x: auto !important;
                        }
                        
                        .table-responsive {
                            overflow-x: auto !important;
                            -webkit-overflow-scrolling: touch !important;
                        }
                    }
                    
                    @media (max-width: ${CONFIG.desktopMin - 1}px) {
                        html {
                            font-size: 16px !important;
                        }
                    }
                `;
                
                console.log('🎨 Iframe FIXED LAYOUT scaling applied:', {
                    viewportWidth: viewportWidth,
                    scaleFactor: scaleFactor.toFixed(3),
                    fontSize: (16 * scaleFactor).toFixed(2) + 'px',
                    mode: 'fixed-layout'
                });
                
            } else {
                // Normal responsive mode - scale everything
                styleEl.textContent = `
                    /* ========================================
                       IFRAME RESPONSIVE SCALING
                       ======================================== */
                    @media (min-width: ${CONFIG.desktopMin}px) {
                        html {
                            font-size: ${16 * scaleFactor}px !important;
                        }
                        
                        body {
                            overflow-x: hidden !important;
                            max-width: 100% !important;
                            margin: 0 !important;
                            padding: clamp(1rem, 2vw, 2rem) !important;
                        }
                        
                        * {
                            max-width: 100% !important;
                            box-sizing: border-box !important;
                        }
                        
                        img, svg, canvas {
                            max-width: 100% !important;
                            height: auto !important;
                        }
                        
                        /* Container scaling */
                        .container,
                        [class*="container"] {
                            max-width: 100% !important;
                            padding-left: clamp(1rem, 2vw, 2rem) !important;
                            padding-right: clamp(1rem, 2vw, 2rem) !important;
                        }
                        
                        /* Card scaling */
                        .card,
                        [class*="card"],
                        [class*="shadow-card"] {
                            padding: clamp(1rem, 1.5vw, 2rem) !important;
                            border-radius: clamp(0.5rem, 0.75vw, 1rem) !important;
                        }
                        
                        /* Button scaling */
                        button,
                        .btn,
                        [class*="btn-"] {
                            padding: clamp(0.5rem, 0.75vw, 1rem) clamp(1rem, 1.5vw, 2rem) !important;
                            font-size: clamp(0.875rem, 1vw, 1rem) !important;
                            border-radius: clamp(0.375rem, 0.5vw, 0.5rem) !important;
                        }
                        
                        /* Input scaling */
                        input,
                        select,
                        textarea {
                            padding: clamp(0.5rem, 0.75vw, 0.75rem) clamp(0.75rem, 1vw, 1rem) !important;
                            font-size: clamp(0.875rem, 1vw, 1rem) !important;
                            border-radius: clamp(0.375rem, 0.5vw, 0.5rem) !important;
                        }
                        
                        /* Table scaling */
                        table {
                            font-size: clamp(0.75rem, 0.9vw, 0.875rem) !important;
                            width: 100% !important;
                        }
                        
                        table th,
                        table td {
                            padding: clamp(0.5rem, 0.75vw, 1rem) !important;
                        }
                        
                        /* Heading scaling */
                        h1 { font-size: clamp(1.5rem, 2.5vw, 2.5rem) !important; }
                        h2 { font-size: clamp(1.25rem, 2vw, 2rem) !important; }
                        h3 { font-size: clamp(1.125rem, 1.5vw, 1.5rem) !important; }
                        h4 { font-size: clamp(1rem, 1.25vw, 1.25rem) !important; }
                        h5 { font-size: clamp(0.875rem, 1.125vw, 1.125rem) !important; }
                        h6 { font-size: clamp(0.75rem, 1vw, 1rem) !important; }
                        
                        /* Icon scaling */
                        i[class*="bx"],
                        .icon {
                            font-size: clamp(1rem, 1.5vw, 1.5rem) !important;
                        }
                        
                        /* Modal scaling */
                        .modal-dialog {
                            max-width: clamp(500px, 50vw, 800px) !important;
                            margin: clamp(1rem, 2vh, 2rem) auto !important;
                        }
                        
                        .modal-content {
                            border-radius: clamp(0.5rem, 0.75vw, 1rem) !important;
                        }
                        
                        .modal-header,
                        .modal-body,
                        .modal-footer {
                            padding: clamp(1rem, 1.5vw, 1.5rem) !important;
                        }
                        
                        /* DataTables scaling */
                        .dataTables_wrapper {
                            font-size: clamp(0.75rem, 0.9vw, 0.875rem) !important;
                        }
                        
                        .dataTables_wrapper .dataTables_length,
                        .dataTables_wrapper .dataTables_filter,
                        .dataTables_wrapper .dataTables_info,
                        .dataTables_wrapper .dataTables_paginate {
                            padding: clamp(0.5rem, 0.75vw, 0.75rem) !important;
                        }
                        
                        .dataTables_wrapper select,
                        .dataTables_wrapper input {
                            padding: clamp(0.25rem, 0.5vw, 0.5rem) clamp(0.5rem, 0.75vw, 0.75rem) !important;
                            font-size: clamp(0.75rem, 0.9vw, 0.875rem) !important;
                        }
                        
                        /* Spacing utilities */
                        .space-y-1 > * + * { margin-top: clamp(0.25rem, 0.5vh, 0.5rem) !important; }
                        .space-y-2 > * + * { margin-top: clamp(0.5rem, 0.75vh, 0.75rem) !important; }
                        .space-y-3 > * + * { margin-top: clamp(0.75rem, 1vh, 1rem) !important; }
                        .space-y-4 > * + * { margin-top: clamp(1rem, 1.5vh, 1.5rem) !important; }
                        .space-y-6 > * + * { margin-top: clamp(1.5rem, 2vh, 2rem) !important; }
                        
                        /* Gap utilities */
                        .gap-1 { gap: clamp(0.25rem, 0.5vw, 0.5rem) !important; }
                        .gap-2 { gap: clamp(0.5rem, 0.75vw, 0.75rem) !important; }
                        .gap-3 { gap: clamp(0.75rem, 1vw, 1rem) !important; }
                        .gap-4 { gap: clamp(1rem, 1.5vw, 1.5rem) !important; }
                        .gap-6 { gap: clamp(1.5rem, 2vw, 2rem) !important; }
                        
                        /* Prevent horizontal scroll */
                        .overflow-x-auto {
                            overflow-x: auto !important;
                        }
                        
                        /* Responsive tables */
                        .table-responsive {
                            overflow-x: auto !important;
                            -webkit-overflow-scrolling: touch !important;
                        }
                    }
                    
                    /* Mobile - no scaling */
                    @media (max-width: ${CONFIG.desktopMin - 1}px) {
                        html {
                            font-size: 16px !important;
                        }
                    }
                `;
                
                console.log('🎨 Iframe RESPONSIVE scaling applied:', {
                    viewportWidth: viewportWidth,
                    scaleFactor: scaleFactor.toFixed(3),
                    fontSize: (16 * scaleFactor).toFixed(2) + 'px',
                    mode: 'responsive'
                });
            }
            
        } catch (error) {
            console.log('⚠️ Cannot apply iframe content scaling (cross-origin)');
        }
    }
    
    // ========================================
    // VIEWPORT SCALING
    // ========================================
    
    /**
     * Apply viewport-based scaling
     */
    function applyViewportScaling() {
        if (!isDesktop()) {
            console.log('📱 Mobile detected - skipping desktop scaling');
            return;
        }
        
        const scaleFactor = calculateScaleFactor();
        const baseFontSize = 16 * scaleFactor;
        
        // Apply to root element
        document.documentElement.style.fontSize = baseFontSize + 'px';
        
        // Update CSS custom property
        document.documentElement.style.setProperty('--scale-factor', scaleFactor);
        
        // Calculate and update sidebar width
        const viewportWidth = window.innerWidth;
        const sidebarWidth = Math.max(200, Math.min(320, viewportWidth * 0.18)); // 18% dari viewport
        document.documentElement.style.setProperty('--sidebar-width', sidebarWidth + 'px');
        
        console.log('📐 Viewport scaling applied:', {
            viewportWidth: window.innerWidth,
            scaleFactor: scaleFactor.toFixed(3),
            baseFontSize: baseFontSize.toFixed(2) + 'px',
            sidebarWidth: sidebarWidth.toFixed(0) + 'px',
            scalePercentage: (scaleFactor * 100).toFixed(1) + '%'
        });
        
        // Update debug indicator if exists
        updateDebugIndicator(scaleFactor, viewportWidth);
        
        // Re-apply iframe scaling
        document.querySelectorAll('iframe').forEach(applyIframeContentScaling);
    }
    
    /**
     * Update debug indicator
     */
    function updateDebugIndicator(scaleFactor, viewportWidth) {
        let indicator = document.getElementById('scaling-debug-indicator');
        
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'scaling-debug-indicator';
            indicator.style.cssText = `
                position: fixed;
                bottom: 10px;
                right: 10px;
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 8px 12px;
                border-radius: 6px;
                font-family: monospace;
                font-size: 11px;
                z-index: 99999;
                pointer-events: none;
                line-height: 1.4;
                display: none; /* DEFAULT: HIDDEN */
            `;
            document.body.appendChild(indicator);
        }
        
        const percentage = (scaleFactor * 100).toFixed(0);
        const color = scaleFactor < 0.9 ? '#ef4444' : scaleFactor > 1.1 ? '#10b981' : '#f59e0b';
        
        indicator.innerHTML = `
            <div style="color: ${color}; font-weight: bold;">Scale: ${percentage}%</div>
            <div style="font-size: 10px; opacity: 0.8;">
                Viewport: ${viewportWidth}px<br>
                Factor: ${scaleFactor.toFixed(3)}
            </div>
        `;
    }
    
    // ========================================
    // RESIZE HANDLER
    // ========================================
    
    const handleResize = debounce(() => {
        console.log('🔄 Window resized, recalculating scaling...');
        
        applyViewportScaling();
        
        // Re-adjust all iframe heights
        document.querySelectorAll('iframe').forEach(iframe => {
            adjustIframeHeight(iframe);
            applyIframeContentScaling(iframe);
        });
        
        console.log('✅ Resize handling complete');
        
    }, CONFIG.resizeDebounce);
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    /**
     * Initialize responsive scaling system
     */
    function initialize() {
        console.log('🚀 Initializing Desktop Responsive Scaling System');
        
        // Apply initial scaling
        applyViewportScaling();
        
        // Initialize iframes
        initializeIframes();
        
        // Watch for new iframes
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'IFRAME') {
                            setupIframeHeightObserver(node);
                            applyIframeContentScaling(node);
                        } else if (node.querySelectorAll) {
                            node.querySelectorAll('iframe').forEach(iframe => {
                                setupIframeHeightObserver(iframe);
                                applyIframeContentScaling(iframe);
                            });
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Listen to window resize
        window.addEventListener('resize', handleResize);
        
        // Listen to orientation change
        window.addEventListener('orientationchange', () => {
            setTimeout(handleResize, 300);
        });
        
        console.log('✅ Desktop Responsive Scaling System initialized');
    }
    
    // ========================================
    // AUTO-START
    // ========================================
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
    
    // Also run after full page load
    window.addEventListener('load', () => {
        setTimeout(initialize, 100);
    });
    
    // ========================================
    // GLOBAL API
    // ========================================
    
    window.DesktopResponsiveScaling = {
        recalculate: applyViewportScaling,
        adjustIframe: adjustIframeHeight,
        scaleIframe: applyIframeContentScaling,
        getScaleFactor: calculateScaleFactor,
        isDesktop: isDesktop,
        
        // Force refresh all iframes
        refreshAllIframes: function() {
            console.log('🔄 Force refreshing all iframes...');
            document.querySelectorAll('iframe').forEach(iframe => {
                adjustIframeHeight(iframe);
                applyIframeContentScaling(iframe);
            });
            console.log('✅ All iframes refreshed');
        },
        
        // Apply scaling to specific iframe
        applyToIframe: function(iframeSelector) {
            const iframe = typeof iframeSelector === 'string' 
                ? document.querySelector(iframeSelector) 
                : iframeSelector;
            
            if (iframe && iframe.tagName === 'IFRAME') {
                console.log('🎨 Applying scaling to iframe:', iframeSelector);
                adjustIframeHeight(iframe);
                applyIframeContentScaling(iframe);
                return true;
            }
            
            console.warn('⚠️ Iframe not found:', iframeSelector);
            return false;
        },
        
        // Toggle debug indicator
        toggleDebug: function() {
            const indicator = document.getElementById('scaling-debug-indicator');
            if (indicator) {
                indicator.style.display = indicator.style.display === 'none' ? 'block' : 'none';
                console.log('🐛 Debug indicator:', indicator.style.display === 'none' ? 'hidden' : 'visible');
            } else {
                console.log('🐛 Debug indicator not found, triggering recalculate...');
                applyViewportScaling();
            }
        },
        
        // Get current scaling info
        getInfo: function() {
            const scaleFactor = calculateScaleFactor();
            const viewportWidth = window.innerWidth;
            return {
                viewportWidth: viewportWidth,
                scaleFactor: scaleFactor,
                scalePercentage: (scaleFactor * 100).toFixed(1) + '%',
                baseFontSize: (16 * scaleFactor).toFixed(2) + 'px',
                isDesktop: isDesktop(),
                config: CONFIG
            };
        }
    };
    
    console.log('✅ Desktop Responsive Scaling API available:', Object.keys(window.DesktopResponsiveScaling));
    
})();
