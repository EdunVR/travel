/**
 * IFRAME AUTO HEIGHT & RESPONSIVE SCALING
 * 
 * Script ini menangani:
 * 1. Auto-adjust tinggi iframe sesuai konten
 * 2. Responsive scaling untuk konten iframe
 * 3. Prevent horizontal scroll
 * 4. Smooth resize handling
 */

(function() {
    'use strict';
    
    console.log('📐 Initializing iframe auto-height system...');
    
    // Fungsi untuk adjust tinggi iframe
    function adjustIframeHeight(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        
        try {
            const iframeDoc = iframe.contentWindow.document;
            const body = iframeDoc.body;
            const html = iframeDoc.documentElement;
            
            // Hitung tinggi konten yang sebenarnya
            const height = Math.max(
                body.scrollHeight,
                body.offsetHeight,
                html.clientHeight,
                html.scrollHeight,
                html.offsetHeight
            );
            
            // Set tinggi iframe dengan sedikit padding
            iframe.style.height = (height + 20) + 'px';
            
            console.log(`📏 Iframe height adjusted to: ${height}px`);
            
        } catch (error) {
            console.error('❌ Error adjusting iframe height:', error);
        }
    }
    
    // Fungsi untuk apply responsive scaling ke iframe content
    function applyIframeScaling(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        
        try {
            const iframeDoc = iframe.contentWindow.document;
            const iframeBody = iframeDoc.body;
            
            // Tambahkan class identifier untuk iframe content
            if (iframeBody && !iframeBody.classList.contains('iframe-content')) {
                iframeBody.classList.add('iframe-content');
                console.log('✅ Added iframe-content class to iframe body');
            }
            
            // Inject CSS untuk prevent horizontal scroll
            const style = iframeDoc.createElement('style');
            style.textContent = `
                body.iframe-content {
                    overflow-x: hidden !important;
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                }
                
                body.iframe-content * {
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                }
                
                /* Smooth scaling transitions */
                @media (min-width: 1024px) {
                    body.iframe-content {
                        transition: all 0.2s ease;
                    }
                }
            `;
            
            // Cek apakah style sudah ada
            if (!iframeDoc.querySelector('#iframe-scaling-style')) {
                style.id = 'iframe-scaling-style';
                iframeDoc.head.appendChild(style);
                console.log('✅ Injected scaling styles to iframe');
            }
            
        } catch (error) {
            console.error('❌ Error applying iframe scaling:', error);
        }
    }
    
    // Fungsi untuk setup iframe observer
    function setupIframeObserver(iframe) {
        if (!iframe || !iframe.contentWindow) return;
        
        try {
            const iframeDoc = iframe.contentWindow.document;
            
            // Observer untuk perubahan konten
            const observer = new MutationObserver(function() {
                adjustIframeHeight(iframe);
            });
            
            observer.observe(iframeDoc.body, {
                childList: true,
                subtree: true,
                attributes: true,
                characterData: true
            });
            
            console.log('👁️ Iframe observer setup complete');
            
            // Cleanup saat iframe di-unload
            iframe.contentWindow.addEventListener('beforeunload', function() {
                observer.disconnect();
            });
            
        } catch (error) {
            console.error('❌ Error setting up iframe observer:', error);
        }
    }
    
    // Fungsi untuk handle window resize
    let resizeTimeout;
    function handleWindowResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            console.log('🔄 Window resized, adjusting all iframes...');
            
            const iframes = document.querySelectorAll('.tab-content iframe');
            iframes.forEach(function(iframe) {
                adjustIframeHeight(iframe);
                applyIframeScaling(iframe);
            });
        }, 250);
    }
    
    // Fungsi untuk initialize iframe
    function initializeIframe(iframe) {
        console.log('🎬 Initializing iframe:', iframe.src);
        
        // Wait for iframe to load
        iframe.addEventListener('load', function() {
            console.log('✅ Iframe loaded:', iframe.src);
            
            // Apply scaling
            applyIframeScaling(iframe);
            
            // Adjust height
            setTimeout(function() {
                adjustIframeHeight(iframe);
            }, 100);
            
            // Setup observer
            setupIframeObserver(iframe);
            
            // Re-adjust after content fully rendered
            setTimeout(function() {
                adjustIframeHeight(iframe);
            }, 500);
            
            setTimeout(function() {
                adjustIframeHeight(iframe);
            }, 1000);
        });
        
        // Jika iframe sudah loaded
        if (iframe.contentWindow && iframe.contentWindow.document.readyState === 'complete') {
            applyIframeScaling(iframe);
            adjustIframeHeight(iframe);
            setupIframeObserver(iframe);
        }
    }
    
    // Fungsi untuk scan dan initialize semua iframe
    function scanAndInitializeIframes() {
        const iframes = document.querySelectorAll('.tab-content iframe');
        console.log(`🔍 Found ${iframes.length} iframes to initialize`);
        
        iframes.forEach(function(iframe) {
            if (!iframe.hasAttribute('data-auto-height-initialized')) {
                iframe.setAttribute('data-auto-height-initialized', 'true');
                initializeIframe(iframe);
            }
        });
    }
    
    // Observer untuk iframe yang ditambahkan secara dinamis
    function setupDynamicIframeObserver() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'IFRAME' && node.closest('.tab-content')) {
                            console.log('🆕 New iframe detected');
                            initializeIframe(node);
                        } else if (node.querySelectorAll) {
                            const iframes = node.querySelectorAll('.tab-content iframe');
                            if (iframes.length > 0) {
                                console.log(`🆕 ${iframes.length} new iframes detected in added node`);
                                iframes.forEach(initializeIframe);
                            }
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        console.log('👁️ Dynamic iframe observer setup complete');
    }
    
    // Initialize saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            scanAndInitializeIframes();
            setupDynamicIframeObserver();
        });
    } else {
        scanAndInitializeIframes();
        setupDynamicIframeObserver();
    }
    
    // Handle window resize
    window.addEventListener('resize', handleWindowResize);
    
    // Expose global function untuk manual trigger
    window.adjustAllIframes = function() {
        console.log('🔄 Manual iframe adjustment triggered');
        scanAndInitializeIframes();
    };
    
    console.log('✅ Iframe auto-height system initialized');
    
})();
