/**
 * Alpine.js Helpers - Prevent Multiple Initializations
 */

// Prevent multiple Alpine.js initialization
if (typeof window.alpineInitialized === 'undefined') {
    window.alpineInitialized = false;
}

// Override Alpine.start to prevent multiple calls
if (typeof Alpine !== 'undefined' && !window.alpineOverridden) {
    window.alpineOverridden = true;
    window.alpineOriginalStart = Alpine.start;
    
    Alpine.start = function() {
        if (!window.alpineInitialized) {
            console.log('🏔️ Starting Alpine.js (controlled)...');
            window.alpineOriginalStart.call(Alpine);
            window.alpineInitialized = true;
            console.log('✅ Alpine.js started successfully');
        } else {
            console.log('ℹ️ Alpine.js already initialized, ignoring duplicate call');
        }
    };
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Alpine.js initialized successfully');
});