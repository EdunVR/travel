/**
 * RESPONSIVE SCALING HELPER
 * 
 * Utility functions untuk debugging dan fine-tuning responsive scaling
 */

(function() {
    'use strict';
    
    // Namespace untuk helper functions
    window.ResponsiveScalingHelper = {
        
        /**
         * Get current CSS variable values
         */
        getVariables: function() {
            const root = document.documentElement;
            const computed = getComputedStyle(root);
            
            const variables = {
                baseFontSize: computed.getPropertyValue('--base-font-size'),
                spacingXs: computed.getPropertyValue('--spacing-xs'),
                spacingSm: computed.getPropertyValue('--spacing-sm'),
                spacingMd: computed.getPropertyValue('--spacing-md'),
                spacingLg: computed.getPropertyValue('--spacing-lg'),
                spacingXl: computed.getPropertyValue('--spacing-xl'),
                spacing2xl: computed.getPropertyValue('--spacing-2xl'),
                buttonHeight: computed.getPropertyValue('--button-height'),
                inputHeight: computed.getPropertyValue('--input-height'),
                cardPadding: computed.getPropertyValue('--card-padding'),
                borderRadius: computed.getPropertyValue('--border-radius'),
                textXs: computed.getPropertyValue('--text-xs'),
                textSm: computed.getPropertyValue('--text-sm'),
                textBase: computed.getPropertyValue('--text-base'),
                textLg: computed.getPropertyValue('--text-lg'),
                textXl: computed.getPropertyValue('--text-xl'),
                text2xl: computed.getPropertyValue('--text-2xl'),
                text3xl: computed.getPropertyValue('--text-3xl'),
                text4xl: computed.getPropertyValue('--text-4xl')
            };
            
            console.table(variables);
            return variables;
        },
        
        /**
         * Get viewport information
         */
        getViewportInfo: function() {
            const info = {
                width: window.innerWidth,
                height: window.innerHeight,
                isDesktop: window.innerWidth >= 1024,
                isMobile: window.innerWidth < 1024,
                devicePixelRatio: window.devicePixelRatio,
                orientation: window.innerWidth > window.innerHeight ? 'landscape' : 'portrait'
            };
            
            console.table(info);
            return info;
        },
        
        /**
         * Test scaling at different viewport widths
         */
        testScaling: function() {
            console.log('🧪 Testing responsive scaling...');
            
            const testWidths = [1024, 1280, 1366, 1440, 1600, 1920, 2560, 3840];
            const results = [];
            
            testWidths.forEach(width => {
                // Calculate what the values would be at this width
                const vw = width / 100;
                
                results.push({
                    width: width + 'px',
                    baseFontSize: Math.min(Math.max(14, 0.8 * vw), 16).toFixed(2) + 'px',
                    spacingMd: Math.min(Math.max(0.75, 0.9 * vw / 16), 1).toFixed(3) + 'rem',
                    buttonHeight: Math.min(Math.max(2, 2.5 * vw / 16), 2.75).toFixed(3) + 'rem'
                });
            });
            
            console.table(results);
            return results;
        },
        
        /**
         * Check if element is using responsive scaling
         */
        checkElement: function(selector) {
            const element = document.querySelector(selector);
            if (!element) {
                console.error('Element not found:', selector);
                return null;
            }
            
            const computed = getComputedStyle(element);
            const info = {
                selector: selector,
                fontSize: computed.fontSize,
                padding: computed.padding,
                margin: computed.margin,
                width: computed.width,
                height: computed.height,
                borderRadius: computed.borderRadius
            };
            
            console.table(info);
            return info;
        },
        
        /**
         * Monitor scaling changes on window resize
         */
        monitorScaling: function(duration = 10000) {
            console.log(`📊 Monitoring scaling for ${duration/1000} seconds...`);
            console.log('Resize the window to see changes');
            
            const startTime = Date.now();
            const logs = [];
            
            const logScaling = () => {
                const elapsed = Date.now() - startTime;
                if (elapsed > duration) {
                    console.log('✅ Monitoring complete');
                    console.table(logs);
                    return;
                }
                
                const vars = this.getVariables();
                logs.push({
                    time: (elapsed / 1000).toFixed(1) + 's',
                    width: window.innerWidth + 'px',
                    baseFontSize: vars.baseFontSize,
                    spacingMd: vars.spacingMd
                });
                
                console.log(`[${(elapsed/1000).toFixed(1)}s] Width: ${window.innerWidth}px, Font: ${vars.baseFontSize}`);
            };
            
            const interval = setInterval(logScaling, 1000);
            
            window.addEventListener('resize', () => {
                clearInterval(interval);
                logScaling();
            });
            
            return logs;
        },
        
        /**
         * Get all iframes info
         */
        getIframesInfo: function() {
            const iframes = document.querySelectorAll('iframe');
            const info = [];
            
            iframes.forEach((iframe, index) => {
                info.push({
                    index: index,
                    src: iframe.src,
                    width: iframe.offsetWidth + 'px',
                    height: iframe.style.height || 'auto',
                    initialized: iframe.hasAttribute('data-auto-height-initialized'),
                    hasContent: !!iframe.contentWindow
                });
            });
            
            console.table(info);
            return info;
        },
        
        /**
         * Force refresh all iframes
         */
        refreshIframes: function() {
            console.log('🔄 Refreshing all iframes...');
            
            if (typeof window.adjustAllIframes === 'function') {
                window.adjustAllIframes();
                console.log('✅ Iframes refreshed');
            } else {
                console.error('❌ adjustAllIframes function not found');
            }
        },
        
        /**
         * Toggle scaling on/off for testing
         */
        toggleScaling: function(enable = null) {
            const root = document.documentElement;
            const currentState = !root.classList.contains('no-responsive-scaling');
            
            if (enable === null) {
                enable = !currentState;
            }
            
            if (enable) {
                root.classList.remove('no-responsive-scaling');
                console.log('✅ Responsive scaling enabled');
            } else {
                root.classList.add('no-responsive-scaling');
                console.log('❌ Responsive scaling disabled');
            }
            
            // Add CSS rule to disable scaling
            if (!document.getElementById('scaling-toggle-style')) {
                const style = document.createElement('style');
                style.id = 'scaling-toggle-style';
                style.textContent = `
                    html.no-responsive-scaling * {
                        font-size: inherit !important;
                        padding: inherit !important;
                        margin: inherit !important;
                    }
                `;
                document.head.appendChild(style);
            }
            
            return enable;
        },
        
        /**
         * Show visual grid for alignment testing
         */
        showGrid: function(show = true) {
            let grid = document.getElementById('responsive-scaling-grid');
            
            if (show && !grid) {
                grid = document.createElement('div');
                grid.id = 'responsive-scaling-grid';
                grid.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    pointer-events: none;
                    z-index: 99999;
                    background-image: 
                        repeating-linear-gradient(0deg, rgba(255,0,0,0.1) 0px, transparent 1px, transparent 20px),
                        repeating-linear-gradient(90deg, rgba(255,0,0,0.1) 0px, transparent 1px, transparent 20px);
                    background-size: 20px 20px;
                `;
                document.body.appendChild(grid);
                console.log('✅ Grid overlay shown');
            } else if (!show && grid) {
                grid.remove();
                console.log('❌ Grid overlay hidden');
            }
            
            return show;
        },
        
        /**
         * Export current settings
         */
        exportSettings: function() {
            const settings = {
                viewport: this.getViewportInfo(),
                variables: this.getVariables(),
                iframes: this.getIframesInfo(),
                timestamp: new Date().toISOString()
            };
            
            console.log('📦 Current settings:', settings);
            
            // Copy to clipboard
            const json = JSON.stringify(settings, null, 2);
            navigator.clipboard.writeText(json).then(() => {
                console.log('✅ Settings copied to clipboard');
            }).catch(err => {
                console.error('❌ Failed to copy to clipboard:', err);
            });
            
            return settings;
        },
        
        /**
         * Show help
         */
        help: function() {
            console.log(`
🎨 RESPONSIVE SCALING HELPER - Available Commands:

📊 Information:
  ResponsiveScalingHelper.getVariables()      - Show all CSS variables
  ResponsiveScalingHelper.getViewportInfo()   - Show viewport information
  ResponsiveScalingHelper.getIframesInfo()    - Show all iframes info
  ResponsiveScalingHelper.checkElement(sel)   - Check specific element

🧪 Testing:
  ResponsiveScalingHelper.testScaling()       - Test scaling at different widths
  ResponsiveScalingHelper.monitorScaling()    - Monitor scaling changes (10s)
  ResponsiveScalingHelper.toggleScaling()     - Toggle scaling on/off
  ResponsiveScalingHelper.showGrid()          - Show alignment grid

🔧 Actions:
  ResponsiveScalingHelper.refreshIframes()    - Refresh all iframes
  ResponsiveScalingHelper.exportSettings()    - Export settings to clipboard

❓ Help:
  ResponsiveScalingHelper.help()              - Show this help

Example:
  ResponsiveScalingHelper.getVariables()
  ResponsiveScalingHelper.checkElement('.my-button')
  ResponsiveScalingHelper.testScaling()
            `);
        }
    };
    
    // Auto-show help on load
    console.log('🎨 Responsive Scaling Helper loaded!');
    console.log('Type ResponsiveScalingHelper.help() for available commands');
    
    // Expose shorthand
    window.RSH = window.ResponsiveScalingHelper;
    console.log('💡 Shorthand available: RSH.help()');
    
})();
