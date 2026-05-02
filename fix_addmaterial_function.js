// Temporary fix for addMaterial function
console.log('🔧 Loading addMaterial function fix...');

// Define the addMaterial function directly on window
window.addMaterial = function () {
    console.log('🔧 addMaterial function called');
    
    const container = document.getElementById("materialRequirements");
    if (!container) {
        console.error('❌ materialRequirements container not found');
        return;
    }
    
    const index = container.children.length;
    console.log('📝 Adding material at index:', index);
    
    // Save all current selected values BEFORE adding new row
    const currentSelections = [];
    const existingSelects = container.querySelectorAll('select[name*="material_id"]');
    existingSelects.forEach((select, i) => {
        currentSelections[i] = select.value;
    });

    const newRow = document.createElement("div");
    newRow.className = "material-row bg-slate-50 rounded-lg p-3 space-y-3";
    newRow.innerHTML = 
        '<input type="hidden" name="materials[' + index + '][material_type]" value="bahan">' +
        '<div class="flex items-center gap-3">' +
            '<select name="materials[' + index + '][material_id]" ' +
                    'class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                    'onchange="updateMaterialUnit(this, ' + index + '); calculateHppPreview();" required>' +
                '<option value="">Pilih Material</option>' +
            '</select>' +
            '<input type="number" name="materials[' + index + '][quantity]" min="0.01" step="0.01" ' +
                   'class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                   'placeholder="Qty" onchange="calculateHppPreview();" ' +
                   'oninput="calculateHppPreview();" required>' +
            '<input type="text" name="materials[' + index + '][unit]" readonly ' +
                   'class="w-24 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 focus:outline-none" ' +
                   'placeholder="Unit">' +
            '<button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">' +
                '<i class="bx bx-trash"></i>' +
            '</button>' +
        '</div>';

    container.appendChild(newRow);
    console.log('✅ New material row added');
    
    // Populate ONLY the new select with materials
    const newSelect = newRow.querySelector('select[name*="material_id"]');
    if (newSelect) {
        // Check if materials data is available
        if (typeof state !== 'undefined' && state.materials && state.materials.length > 0) {
            console.log('📋 Populating select with', state.materials.length, 'materials');
            state.materials.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = material.name + " (Stok: " + material.stock + " " + material.unit + ")";
                option.dataset.type = material.type;
                option.dataset.unit = material.unit;
                newSelect.appendChild(option);
            });
        } else {
            console.warn('⚠️ No materials data available');
            // Try to load materials if not available
            if (typeof loadMaterials === 'function') {
                loadMaterials();
            }
        }
    }
    
    // Restore all previous selections
    setTimeout(() => {
        const allSelects = container.querySelectorAll('select[name*="material_id"]');
        allSelects.forEach((select, i) => {
            if (i < currentSelections.length && currentSelections[i]) {
                select.value = currentSelections[i];
                
                // Trigger change event to update unit
                const event = new Event('change', { bubbles: true });
                select.dispatchEvent(event);
            }
        });
        
        // Recalculate HPP after adding material
        if (typeof calculateHppPreview === 'function') {
            calculateHppPreview();
        }
    }, 200);
};

// Also define removeMaterial if it doesn't exist
if (typeof window.removeMaterial !== 'function') {
    window.removeMaterial = function (button) {
        console.log('🗑️ removeMaterial function called');
        
        const row = button.closest('.material-row');
        const container = document.getElementById("materialRequirements");
        
        if (!container) {
            console.error('❌ materialRequirements container not found');
            return;
        }
        
        if (container.children.length > 1) {
            row.remove();
            console.log('✅ Material row removed');
        } else {
            console.warn('⚠️ Cannot remove last material row');
            if (typeof showNotification === 'function') {
                showNotification('Minimal harus ada satu material', 'warning');
            } else {
                alert('Minimal harus ada satu material');
            }
        }
    };
}

// Also define updateMaterialUnit if it doesn't exist
if (typeof window.updateMaterialUnit !== 'function') {
    window.updateMaterialUnit = function (select, index) {
        console.log('🔄 updateMaterialUnit function called for index:', index);
        
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.dataset.unit) {
            const unitInput = document.querySelector('input[name="materials[' + index + '][unit]"]');
            const typeInput = document.querySelector('input[name="materials[' + index + '][material_type]"]');
            
            if (unitInput) {
                unitInput.value = selectedOption.dataset.unit;
                console.log('✅ Unit updated to:', selectedOption.dataset.unit);
            }
            
            if (typeInput && selectedOption.dataset.type) {
                typeInput.value = selectedOption.dataset.type;
                console.log('✅ Type updated to:', selectedOption.dataset.type);
            }
        }
    };
}

console.log('✅ addMaterial function fix loaded successfully');
console.log('🔍 Available functions:');
console.log('   - addMaterial:', typeof window.addMaterial);
console.log('   - removeMaterial:', typeof window.removeMaterial);
console.log('   - updateMaterialUnit:', typeof window.updateMaterialUnit);