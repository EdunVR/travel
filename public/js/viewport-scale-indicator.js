/**
 * VIEWPORT SCALE INDICATOR
 * 
 * Menampilkan informasi viewport dan scale secara real-time
 */

(function() {
    'use strict';
    
    // Create indicator element
    function createIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'viewport-scale-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(15, 23, 42, 0.95);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.6;
            z-index: 99999;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 280px;
            transition: all 0.3s ease;
            cursor: move;
        `;
        
        indicator.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 8px;">
                <div style="font-weight: bold; font-size: 13px; color: #60a5fa;">📊 Viewport Info</div>
                <div style="display: flex; gap: 8px;">
                    <button id="toggle-indicator-size" style="background: rgba(255,255,255,0.1); border: none; color: white; padding: 4px 8px; border-radius: 6px; cursor: pointer; font-size: 11px;" title="Toggle Size">
                        ⇅
                    </button>
                    <button id="close-indicator" style="background: rgba(239, 68, 68, 0.2); border: none; color: #fca5a5; padding: 4px 8px; border-radius: 6px; cursor: pointer; font-size: 11px;" title="Close (Ctrl+Shift+V to reopen)">
                        ✕
                    </button>
                </div>
            </div>
            <div id="indicator-content">
                <div style="margin-bottom: 4px;">
                    <span style="color: #94a3b8;">Width:</span> 
                    <span id="viewport-width" style="color: #22d3ee; font-weight: bold;">-</span>
                </div>
                <div style="margin-bottom: 4px;">
                    <span style="color: #94a3b8;">Height:</span> 
                    <span id="viewport-height" style="color: #22d3ee; font-weight: bold;">-</span>
                </div>
                <div style="margin-bottom: 4px;">
                    <span style="color: #94a3b8;">Scale:</span> 
                    <span id="viewport-scale" style="color: #34d399; font-weight: bold;">-</span>
                </div>
                <div style="margin-bottom: 4px;">
                    <span style="color: #94a3b8;">DPR:</span> 
                    <span id="device-pixel-ratio" style="color: #a78bfa; font-weight: bold;">-</span>
                </div>
                <div style="margin-bottom: 4px;">
                    <span style="color: #94a3b8;">Font:</span> 
                    <span id="base-font-size" style="color: #fbbf24; font-weight: bold;">-</span>
                </div>
                <div>
                    <span style="color: #94a3b8;">Type:</span> 
                    <span id="viewport-type" style="color: #fb923c; font-weight: bold;">-</span>
                </div>
            </div>
        `;
        
        document.body.appendChild(indicator);
        
        // Make draggable
        makeDraggable(indicator);
        
        // Add event listeners
        document.getElementById('close-indicator').addEventListener('click', hideIndicator);
        document.getElementById('toggle-indicator-size').addEventListener('click', toggleIndicatorSize);
        
        return indicator;
    }
    
    // Make element draggable
    function makeDraggable(element) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        
        element.onmousedown = dragMouseDown;
        
        function dragMouseDown(e) {
            e = e || window.event;
            // Don't drag if clicking buttons
            if (e.target.tagName === 'BUTTON') return;
            
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }
        
        function elementDrag(e) {
            e = e || window.event;
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            element.style.top = (element.offsetTop - pos2) + "px";
            element.style.left = (element.offsetLeft - pos1) + "px";
            element.style.bottom = 'auto';
        }
        
        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }
    
    // Update indicator values
    function updateIndicator() {
        const width = window.innerWidth;
        const height = window.innerHeight;
        const dpr = window.devicePixelRatio || 1;
        
        // Calculate scale based on desktop-responsive-scaling.js logic
        let scale = 1;
        if (width >= 1024) {
            if (width <= 1366) {
                scale = 0.85;
            } else if (width <= 1600) {
                scale = 0.90;
            } else if (width <= 1920) {
                scale = 0.95;
            } else {
                scale = 1.0;
            }
        }
        
        // Get computed font size
        const baseFontSize = getComputedStyle(document.documentElement).fontSize;
        
        // Determine viewport type
        let type = 'Mobile';
        if (width >= 1024 && width < 1280) type = 'Desktop (Small)';
        else if (width >= 1280 && width < 1440) type = 'Desktop (Medium)';
        else if (width >= 1440 && width < 1920) type = 'Desktop (Large)';
        else if (width >= 1920) type = 'Desktop (XL)';
        
        // Update DOM
        document.getElementById('viewport-width').textContent = width + 'px';
        document.getElementById('viewport-height').textContent = height + 'px';
        document.getElementById('viewport-scale').textContent = (scale * 100).toFixed(0) + '%';
        document.getElementById('device-pixel-ratio').textContent = dpr.toFixed(2);
        document.getElementById('base-font-size').textContent = baseFontSize;
        document.getElementById('viewport-type').textContent = type;
    }
    
    // Hide indicator
    function hideIndicator() {
        const indicator = document.getElementById('viewport-scale-indicator');
        if (indicator) {
            indicator.style.opacity = '0';
            indicator.style.transform = 'translateY(20px)';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 300);
        }
        
        // Save state
        localStorage.setItem('viewport-indicator-hidden', 'true');
    }
    
    // Show indicator
    function showIndicator() {
        let indicator = document.getElementById('viewport-scale-indicator');
        if (!indicator) {
            indicator = createIndicator();
        }
        
        indicator.style.display = 'block';
        setTimeout(() => {
            indicator.style.opacity = '1';
            indicator.style.transform = 'translateY(0)';
        }, 10);
        
        updateIndicator();
        
        // Save state
        localStorage.setItem('viewport-indicator-hidden', 'false');
    }
    
    // Toggle indicator size
    function toggleIndicatorSize() {
        const content = document.getElementById('indicator-content');
        const isMinimized = content.style.display === 'none';
        
        if (isMinimized) {
            content.style.display = 'block';
            localStorage.setItem('viewport-indicator-minimized', 'false');
        } else {
            content.style.display = 'none';
            localStorage.setItem('viewport-indicator-minimized', 'true');
        }
    }
    
    // Keyboard shortcut: Ctrl+Shift+V to toggle
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'V') {
            e.preventDefault();
            const indicator = document.getElementById('viewport-scale-indicator');
            if (indicator && indicator.style.display !== 'none') {
                hideIndicator();
            } else {
                showIndicator();
            }
        }
    });
    
    // Initialize
    function init() {
        // Check if should be hidden
        const isHidden = localStorage.getItem('viewport-indicator-hidden') === 'true';
        const isMinimized = localStorage.getItem('viewport-indicator-minimized') === 'true';
        
        if (!isHidden) {
            const indicator = createIndicator();
            
            if (isMinimized) {
                document.getElementById('indicator-content').style.display = 'none';
            }
            
            updateIndicator();
            
            // Update on resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(updateIndicator, 100);
            });
            
            // Update every second (for dynamic changes)
            setInterval(updateIndicator, 1000);
        }
        
        console.log('📊 Viewport Scale Indicator loaded!');
        console.log('💡 Press Ctrl+Shift+V to toggle indicator');
        console.log('💡 Or use: window.toggleViewportIndicator()');
    }
    
    // Expose global functions
    window.showViewportIndicator = showIndicator;
    window.hideViewportIndicator = hideIndicator;
    window.toggleViewportIndicator = function() {
        const indicator = document.getElementById('viewport-scale-indicator');
        if (indicator && indicator.style.display !== 'none') {
            hideIndicator();
        } else {
            showIndicator();
        }
    };
    
    // Auto-init when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
