/**
 * Enhanced DataTable Helper - Prevent Reinitialization Warnings
 */

console.log('📊 DataTable Helper loaded successfully');

// Global DataTable Manager
window.DataTableManager = {
    instances: new Map(),
    
    // Safe initialization with cleanup
    init(selector, options = {}) {
        console.log('🔄 Initializing DataTable for:', selector);
        
        // Cleanup existing instance
        this.destroy(selector);
        
        // Wait for cleanup to complete
        return new Promise((resolve) => {
            setTimeout(() => {
                try {
                    const table = $(selector).DataTable(options);
                    this.instances.set(selector, table);
                    console.log('✅ DataTable initialized successfully for:', selector);
                    resolve(table);
                } catch (error) {
                    console.error('❌ DataTable initialization error:', error);
                    resolve(null);
                }
            }, 100);
        });
    },
    
    // Safe destruction
    destroy(selector) {
        try {
            if ($.fn.DataTable.isDataTable(selector)) {
                console.log('🗑️ Destroying existing DataTable:', selector);
                const table = $(selector).DataTable();
                table.clear();
                table.destroy();
                this.instances.delete(selector);
                
                // Clean up DOM
                $(selector).removeClass('dataTable');
                $(selector).removeAttr('role aria-describedby');
                $(selector + '_wrapper').remove();
                
                console.log('✅ DataTable destroyed successfully:', selector);
            }
        } catch (error) {
            console.error('❌ DataTable destruction error:', error);
        }
    },
    
    // Get instance
    get(selector) {
        return this.instances.get(selector);
    },
    
    // Check if exists
    exists(selector) {
        return this.instances.has(selector);
    }
};

// Global cleanup function
window.cleanupDataTable = function(selector) {
    window.DataTableManager.destroy(selector);
};

// Global safe init function
window.initDataTableSafe = function(selector, options) {
    return window.DataTableManager.init(selector, options);
};

console.log('✅ DataTable Manager initialized');