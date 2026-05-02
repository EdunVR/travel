/**
 * Inter Outlet JavaScript Patch
 * Memperbaiki error "ALL is not defined"
 */

// Define constants
window.ALL = 'all';
const ALL = 'all';

// Ensure Alpine.js is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Alpine === 'undefined') {
        console.error('Alpine.js is not loaded. Inter-outlet functionality may not work properly.');
        return;
    }
    
    // Check if interOutletSaleApp is defined
    if (typeof window.interOutletSaleApp !== 'function') {
        console.error('interOutletSaleApp function is not defined. Please check if inter-outlet.js is loaded.');
        return;
    }
    
    console.log('✅ Inter-outlet JavaScript patch loaded successfully');
});

// Error handler for undefined variables
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('Caught ALL undefined error, using fallback value');
        window.ALL = 'all';
        return true; // Prevent default error handling
    }
});