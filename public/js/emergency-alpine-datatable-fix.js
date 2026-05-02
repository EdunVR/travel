/**
 * Emergency Fix for Alpine.js and DataTable Conflicts
 * Simplified version to prevent conflicts
 */

console.log('🚨 Loading emergency Alpine.js and DataTable fix...');

// Prevent multiple Alpine.js initialization
if (typeof window.alpineStarted === 'undefined') {
    window.alpineStarted = false;
}

// Only override Alpine.start if it hasn't been overridden yet
if (typeof Alpine !== 'undefined' && !window.alpineOverridden) {
    window.alpineOverridden = true;
    window.alpineOriginalStart = Alpine.start;
    
    Alpine.start = function() {
        if (!window.alpineStarted) {
            console.log('🏔️ Starting Alpine.js (emergency override)...');
            window.alpineOriginalStart.call(Alpine);
            window.alpineStarted = true;
            console.log('✅ Alpine.js started successfully');
        } else {
            console.log('ℹ️ Alpine.js already started, ignoring duplicate call');
        }
    };
}

// Enhanced DataTable cleanup - only if needed
window.emergencyDataTableCleanup = function(tableId) {
    console.log('🚨 Emergency DataTable cleanup for:', tableId);
    
    try {
        if ($.fn.DataTable.isDataTable(tableId)) {
            const table = $(tableId).DataTable();
            table.clear();
            table.destroy();
        }
        
        // Clean up DataTable artifacts
        $(tableId).removeClass('dataTable');
        $(tableId).removeAttr('role aria-describedby');
        $(tableId + '_wrapper').remove();
        $(tableId).empty();
        
        console.log('✅ Emergency cleanup completed for:', tableId);
    } catch (error) {
        console.error('❌ Emergency cleanup error:', error);
    }
};

// Only run auto-cleanup if there are issues
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚨 Emergency fix loaded and ready');
    
    // Check for problematic DataTables only
    setTimeout(() => {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('.dataTable').each(function() {
                const tableId = '#' + this.id;
                if (tableId !== '#' && $(this).hasClass('error')) {
                    console.log('🚨 Found problematic DataTable, cleaning up:', tableId);
                    window.emergencyDataTableCleanup(tableId);
                }
            });
        }
    }, 1000);
});

console.log('🚨 Emergency fix script loaded');
