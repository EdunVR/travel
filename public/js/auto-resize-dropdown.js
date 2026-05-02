/**
 * AUTO RESIZE DROPDOWN
 * 
 * Automatically resize dropdown width based on selected option text
 * to prevent text truncation
 */

(function() {
    'use strict';
    
    // Function to calculate text width
    function getTextWidth(text, font) {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        context.font = font;
        const metrics = context.measureText(text);
        return metrics.width;
    }
    
    // Function to resize select element
    function resizeSelect(select) {
        if (!select || select.tagName !== 'SELECT') return;
        
        // Get selected option text
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption) return;
        
        const text = selectedOption.textContent || selectedOption.innerText;
        
        // Get computed font
        const computedStyle = window.getComputedStyle(select);
        const font = `${computedStyle.fontSize} ${computedStyle.fontFamily}`;
        
        // Calculate required width
        const textWidth = getTextWidth(text, font);
        const padding = 50; // Account for padding and arrow
        const minWidth = 180; // Minimum width
        const maxWidth = 400; // Maximum width to prevent too wide
        
        let newWidth = Math.ceil(textWidth + padding);
        newWidth = Math.max(minWidth, Math.min(newWidth, maxWidth));
        
        // Apply new width
        select.style.width = newWidth + 'px';
        select.style.minWidth = minWidth + 'px';
        select.style.maxWidth = maxWidth + 'px';
        
        console.log('📏 Resized dropdown:', {
            text: text.substring(0, 30) + '...',
            textWidth: textWidth,
            newWidth: newWidth
        });
    }
    
    // Function to process all selects
    function processAllSelects() {
        const selects = document.querySelectorAll('select');
        
        selects.forEach(select => {
            // Initial resize
            resizeSelect(select);
            
            // Add change listener
            if (!select.hasAttribute('data-auto-resize-initialized')) {
                select.addEventListener('change', function() {
                    resizeSelect(this);
                });
                
                select.setAttribute('data-auto-resize-initialized', 'true');
            }
        });
        
        console.log('✅ Auto-resize initialized for', selects.length, 'dropdowns');
    }
    
    // Initialize on DOM ready
    function init() {
        processAllSelects();
        
        // Re-process when new elements are added (for dynamic content)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.tagName === 'SELECT') {
                                resizeSelect(node);
                            } else {
                                const selects = node.querySelectorAll('select');
                                selects.forEach(resizeSelect);
                            }
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        console.log('📊 Auto-resize dropdown initialized');
    }
    
    // Wait for DOM and Alpine.js
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(init, 500); // Wait for Alpine to render
        });
    } else {
        setTimeout(init, 500);
    }
    
    // Re-process on window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(processAllSelects, 300);
    });
    
    // Expose global function
    window.resizeAllDropdowns = processAllSelects;
    
})();
