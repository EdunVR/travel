/**
 * Resolution Settings - Global Application
 * Applies user resolution settings across the entire application
 */

(function() {
    'use strict';

    console.log('🖥️ Resolution Settings: Initializing...');

    // Get settings from cookie
    function getSettings() {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'resolution_settings') {
                try {
                    return JSON.parse(decodeURIComponent(value));
                } catch (e) {
                    console.error('Error parsing resolution settings:', e);
                    return null;
                }
            }
        }
        return null;
    }

    // Apply settings to page
    function applySettings() {
        const settings = getSettings();
        
        if (!settings) {
            console.log('📏 Resolution Settings: Using default settings');
            return;
        }

        console.log('📏 Resolution Settings: Applying settings', settings);

        const root = document.documentElement;

        // Apply scale
        if (settings.scale) {
            root.style.setProperty('--app-scale', settings.scale / 100);
            document.body.style.zoom = settings.scale + '%';
        }

        // Apply sidebar width
        if (settings.sidebar_width) {
            const sidebarWidths = {
                'compact': '240px',
                'normal': '320px',
                'wide': '400px'
            };
            root.style.setProperty('--sidebar-width', sidebarWidths[settings.sidebar_width] || '320px');
        }

        // Apply font size
        if (settings.font_size) {
            const fontSizes = {
                'small': '13px',
                'normal': '14px',
                'large': '16px'
            };
            root.style.setProperty('--base-font-size', fontSizes[settings.font_size] || '14px');
            document.body.style.fontSize = fontSizes[settings.font_size] || '14px';
        }

        // Apply spacing
        if (settings.spacing) {
            const spacings = {
                'compact': '0.75',
                'normal': '1',
                'comfortable': '1.25'
            };
            root.style.setProperty('--spacing-multiplier', spacings[settings.spacing] || '1');
        }

        // Add class to body for CSS targeting
        document.body.classList.add('resolution-settings-applied');
        document.body.setAttribute('data-sidebar-width', settings.sidebar_width || 'normal');
        document.body.setAttribute('data-font-size', settings.font_size || 'normal');
        document.body.setAttribute('data-spacing', settings.spacing || 'normal');

        console.log('✅ Resolution Settings: Applied successfully');
    }

    // Apply settings when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applySettings);
    } else {
        applySettings();
    }

    // Re-apply on page show (for back/forward navigation)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            applySettings();
        }
    });

    // Expose function globally for manual application
    window.applyResolutionSettings = applySettings;

    console.log('✅ Resolution Settings: Initialized');
})();
