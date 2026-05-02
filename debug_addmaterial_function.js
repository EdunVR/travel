// Debug script to test addMaterial function
console.log('🔍 Debug: Starting addMaterial function test');

// Check if the function exists
if (typeof window.addMaterial === 'function') {
    console.log('✅ addMaterial function is defined');
} else {
    console.log('❌ addMaterial function is NOT defined');
}

// Check if required DOM elements exist
const container = document.getElementById("materialRequirements");
if (container) {
    console.log('✅ materialRequirements container found');
    console.log('   - Children count:', container.children.length);
} else {
    console.log('❌ materialRequirements container NOT found');
}

// Test the function manually
window.testAddMaterial = function() {
    console.log('🧪 Testing addMaterial function manually...');
    
    try {
        if (typeof window.addMaterial === 'function') {
            window.addMaterial();
            console.log('✅ addMaterial executed successfully');
        } else {
            console.log('❌ addMaterial function not available');
        }
    } catch (error) {
        console.error('❌ Error executing addMaterial:', error);
    }
};

// Check if production.js is loaded
console.log('🔍 Checking if production.js variables are available:');
console.log('   - state object:', typeof state !== 'undefined' ? 'defined' : 'undefined');
console.log('   - materialCount:', typeof materialCount !== 'undefined' ? 'defined' : 'undefined');

// List all window functions that contain 'Material'
console.log('🔍 Window functions containing "Material":');
Object.keys(window).filter(key => key.toLowerCase().includes('material')).forEach(key => {
    console.log('   -', key, ':', typeof window[key]);
});

console.log('🔍 Debug test completed. Run testAddMaterial() to test manually.');