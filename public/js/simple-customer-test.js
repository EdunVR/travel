
// Simple customer search test
window.testCustomerSearch = function() {
    console.log("🧪 [SIMPLE TEST] Testing customer search...");
    
    // Test 1: Check if we can access Alpine data
    const posElement = document.querySelector('[x-data="posApp()"]');
    if (posElement && posElement._x_dataStack) {
        const posData = posElement._x_dataStack[0];
        console.log("✅ [SIMPLE TEST] POS data accessible:", posData);
        console.log("   - Customers count:", posData.customers ? posData.customers.length : "undefined");
        console.log("   - Current search:", posData.ui ? posData.ui.customerSearch : "undefined");
        console.log("   - Dropdown visible:", posData.ui ? posData.ui.customerDropdown : "undefined");
        
        // Test 2: Try to trigger search manually
        if (posData.searchCustomer) {
            console.log("🧪 [SIMPLE TEST] Calling searchCustomer() manually...");
            posData.searchCustomer();
        } else {
            console.log("❌ [SIMPLE TEST] searchCustomer method not found");
        }
        
        // Test 3: Check filteredCustomers
        if (posData.filteredCustomers) {
            console.log("🧪 [SIMPLE TEST] Calling filteredCustomers() manually...");
            const filtered = posData.filteredCustomers();
            console.log("   - Filtered customers:", filtered ? filtered.length : "undefined");
        } else {
            console.log("❌ [SIMPLE TEST] filteredCustomers method not found");
        }
    } else {
        console.log("❌ [SIMPLE TEST] Cannot access POS data");
    }
};

// Add test button to console
console.log("🧪 [SIMPLE TEST] Test function available: window.testCustomerSearch()");
