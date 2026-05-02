// Production Management JavaScript - Clean Version
(function () {
    "use strict";

    // State management
    const state = {
        selectedOutlet: null,
        products: [],
        materials: [],
        dataTable: null,
        operationalCostIndex: 0,
    };

    // Initialize
    document.addEventListener("DOMContentLoaded", function () {
        // Set initial outlet
        state.selectedOutlet = getCurrentOutletId();
        
        initializeOutletSelector();
        initializeDataTable();
        initializeEventListeners();
        
        // Load data with delay to ensure Alpine.js is ready
        setTimeout(() => {
            loadProducts();
            loadMaterials();
            loadStatistics();
        }, 500);
        
        initializeOperationalCosts();
    });

    // Initialize outlet selector
    function initializeOutletSelector() {
        const outletSelect = document.getElementById("outletSelect");
        if (outletSelect) {
            state.selectedOutlet = outletSelect.value;
            
            outletSelect.addEventListener("change", function () {
                state.selectedOutlet = this.value;
                reloadData();
            });
        } else {
            // Try to get from Alpine.js component
            const currentOutlet = getCurrentOutletId();
            if (currentOutlet) {
                state.selectedOutlet = currentOutlet;
            }
        }
    }

    // Initialize DataTable
    function initializeDataTable() {
        const table = $("#productionTable");
        if (table.length === 0) {
            return;
        }

        state.dataTable = table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: productionDataUrl,
                data: function (d) {
                    d.outlet_id = state.selectedOutlet;
                    d.status = $("#filterStatus").val();
                    d.production_line = $("#filterLine").val();
                    d.start_date = $("#filterStartDate").val();
                    d.end_date = $("#filterEndDate").val();
                },
            },
            columns: [
                { data: "production_code", name: "production_code", title: "ID Produksi" },
                { data: "product_name", name: "product.nama_produk", title: "Produk" },
                { data: "production_line", name: "production_line", title: "Lini" },
                {
                    data: "target_quantity",
                    name: "target_quantity",
                    title: "Target",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: "realized_quantity",
                    name: "realized_quantity",
                    title: "Realisasi",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: "progress",
                    name: "progress",
                    title: "Progress",
                    orderable: false,
                    render: function (data, type, row) {
                        return data;
                    },
                },
                {
                    data: "hpp_per_unit",
                    name: "hpp_per_unit",
                    title: "HPP/Unit",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: "total_cost",
                    name: "total_cost",
                    title: "Total Biaya",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: "status",
                    name: "status",
                    title: "Status",
                    render: function (data, type, row) {
                        return data;
                    },
                },
                {
                    data: "created_at",
                    name: "created_at",
                    title: "Dibuat",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: "actions",
                    name: "actions",
                    title: "Aksi",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return data;
                    },
                },
            ],
            order: [[9, "desc"]],
            pageLength: 25,
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
            },
        });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        const createBtn = document.getElementById("createProductionBtn");
        if (createBtn) {
            createBtn.addEventListener("click", openCreateModal);
        }

        // Filter event listeners
        $("#filterStatus, #filterLine, #filterStartDate, #filterEndDate").on("change", function () {
            if (state.dataTable) {
                state.dataTable.ajax.reload();
            }
        });

        // Form submission
        const form = document.getElementById("productionForm");
        if (form) {
            // form.addEventListener("submit", handleFormSubmit); // DISABLED: Using inline handler instead
        }

        // Realization form submission
        const realizationForm = document.getElementById("realizationForm");
        if (realizationForm) {
            realizationForm.addEventListener("submit", handleRealizationSubmit);
        }
    }

    // Get current outlet ID from Alpine.js
    function getCurrentOutletId() {
        try {
            // Try multiple ways to get outlet ID
            const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
            if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                return alpineComponent._x_dataStack[0].outletFilter;
            }
            
            // Fallback: try to get from URL params or other sources
            const urlParams = new URLSearchParams(window.location.search);
            const outletFromUrl = urlParams.get('outlet_id');
            if (outletFromUrl) {
                return outletFromUrl;
            }
            
            // Fallback: try to get from any outlet select element
            const outletSelect = document.querySelector('select[name="outlet_id"]');
            if (outletSelect && outletSelect.value) {
                return outletSelect.value;
            }
            
            // Default fallback
            return '3'; // Default outlet ID
        } catch (error) {
            return '3'; // Default outlet ID
        }
    }

    // Reload data
    function reloadData() {
        if (state.dataTable) {
            state.dataTable.ajax.reload();
        }
        loadStatistics();
    }

    // Load products
    function loadProducts() {
        if (!productsUrl) return;
        
        const currentOutlet = state.selectedOutlet || getCurrentOutletId();
        if (!currentOutlet) return;

        fetch(productsUrl + "?outlet_id=" + currentOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    state.products = data.data;
                    populateProductSelects();
                }
            })
            .catch((error) => {
                // Handle error silently
            });
    }

    // Load materials
    function loadMaterials() {
        if (!materialsUrl) return;
        
        const currentOutlet = state.selectedOutlet || getCurrentOutletId();
        if (!currentOutlet) return;

        fetch(materialsUrl + "?outlet_id=" + currentOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    state.materials = data.data;
                    populateMaterialSelects();
                }
            })
            .catch((error) => {
                // Handle error silently
            });
    }

    // Load statistics
    function loadStatistics() {
        if (!statisticsUrl) return;
        
        const currentOutlet = state.selectedOutlet || getCurrentOutletId();
        if (!currentOutlet) return;

        fetch(statisticsUrl + "?outlet_id=" + currentOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    updateStatistics(data.data);
                }
            })
            .catch((error) => {
                // Handle error silently
            });
    }

    // Update statistics
    function updateStatistics(stats) {
        const activeCount = document.getElementById("activeCount");
        if (activeCount) {
            activeCount.textContent = stats.active || 0;
        }
    }

    // Update modal outlet name
    function updateModalOutletName() {
        const outletSelect = document.getElementById("outletSelect");
        const modalOutletName = document.getElementById("modalOutletName");
        
        if (outletSelect && modalOutletName) {
            const selectedOption = outletSelect.options[outletSelect.selectedIndex];
            modalOutletName.textContent = selectedOption ? selectedOption.text : "Outlet";
        }
    }

    // Open create modal
    function openCreateModal() {
        const modal = document.getElementById("createModal");
        if (modal) {
            modal.classList.remove("hidden");
            modal.style.display = "flex";
            document.body.style.overflow = "hidden";
            updateModalOutletName();
            
            // Ensure we have current outlet
            const currentOutlet = getCurrentOutletId();
            if (currentOutlet) {
                state.selectedOutlet = currentOutlet;
            }
            
            // Load fresh data
            loadMaterials();
            loadProducts();
            
            // Initialize product search
            setTimeout(() => {
                initializeProductSearch();
            }, 100);
            
            // Initialize form only if not in edit mode
            const form = document.getElementById("productionForm");
            if (form) {
                const isEditMode = form.dataset.editMode === 'true';
                
                if (!isEditMode) {
                    // Only reset form if not in edit mode
                    form.reset();
                    
                    // Set outlet
                    const outletSelect = document.getElementById("outletSelect");
                    if (outletSelect) {
                        outletSelect.value = state.selectedOutlet || currentOutlet;
                    }
                    
                    // Add initial material row
                    const materialContainer = document.getElementById('materialRequirements');
                    if (materialContainer && materialContainer.children.length === 0) {
                        window.addMaterial();
                    }
                }
                
                // Setup realtime HPP calculation
                setupRealtimeHppCalculation();
                
                // Initial HPP calculation
                setTimeout(() => {
                    calculateHppPreview();
                }, 300);
            }
        }
    }

    // Make openCreateModal globally available
    window.openCreateModal = openCreateModal;

    // Close create modal
    window.closeCreateModal = function() {
        const modal = document.getElementById("createModal");
        if (modal) {
            modal.classList.add("hidden");
            modal.style.display = "none";
            document.body.style.overflow = "auto";
            
            // Reset form and edit mode
            const form = document.getElementById("productionForm");
            if (form) {
                form.reset();
                form.dataset.editMode = 'false';
                form.dataset.productionId = '';
                
                // Clear materials
                const materialContainer = document.getElementById('materialRequirements');
                if (materialContainer) {
                    materialContainer.innerHTML = '';
                    // Add one empty material row
                    window.addMaterial();
                }
                
                // Clear operational costs
                const operationalContainer = document.getElementById('operationalCosts');
                if (operationalContainer) {
                    operationalContainer.innerHTML = '';
                }
            }
            
            // Reset modal title and button text
            const modalTitle = document.querySelector('#createModal .font-semibold');
            if (modalTitle) {
                modalTitle.textContent = 'Buat Produksi Baru';
            }
            
            const submitButton = document.querySelector('#createModal button[type="submit"]');
            if (submitButton) {
                submitButton.textContent = 'Simpan Produksi';
            }
        }
    };

    // Helper function for number formatting
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }

    // Realization Modal Functions
    window.showRealizationModal = function(production) {
        const modal = document.getElementById('realizationModal');
        if (modal) {
            // Populate production info
            document.getElementById('realizationProductionCode').textContent = production.production_code;
            document.getElementById('realizationProductName').textContent = production.product_name;
            document.getElementById('realizationTarget').textContent = formatNumber(production.target_quantity) + ' unit';
            document.getElementById('realizationCurrent').textContent = formatNumber(production.realized_quantity) + ' unit';
            
            // Store production ID for form submission
            const form = document.getElementById('realizationForm');
            if (form) {
                form.dataset.productionId = production.id;
                form.reset();
            }
            
            // Show modal
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeRealizationModal = function() {
        const modal = document.getElementById('realizationModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            
            // Reset form
            const form = document.getElementById('realizationForm');
            if (form) {
                form.reset();
                delete form.dataset.productionId;
            }
        }
    };

    // Product search functionality
    let productSearchTimeout;
    
    function initializeProductSearch() {
        const productSearch = document.getElementById('productSearch');
        const productResults = document.getElementById('productResults');
        
        if (!productSearch || !productResults) return;
        
        const currentOutlet = state.selectedOutlet || getCurrentOutletId();
        
        productSearch.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(productSearchTimeout);
            
            if (query.length < 2) {
                productResults.classList.add('hidden');
                return;
            }
            
            productSearchTimeout = setTimeout(() => {
                searchProducts(query, currentOutlet);
            }, 300);
        });
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!productSearch.contains(e.target) && !productResults.contains(e.target)) {
                productResults.classList.add('hidden');
            }
        });
    }
    
    function searchProducts(query, outlet) {
        if (!productsUrl) return;
        
        fetch(productsUrl + "?search=" + encodeURIComponent(query) + "&outlet_id=" + outlet)
            .then(response => response.json())
            .then(data => {
                displayProductResults(data.data || []);
            })
            .catch(error => {
                // Handle error silently
            });
    }
    
    function displayProductResults(products) {
        const productResults = document.getElementById('productResults');
        if (!productResults) return;
        
        if (products.length === 0) {
            productResults.innerHTML = '<div class="p-3 text-slate-500">Tidak ada produk ditemukan</div>';
        } else {
            productResults.innerHTML = products.map(product => 
                '<div class="p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-b-0" onclick="selectProduct(' + product.id + ', \'' + product.name.replace(/'/g, "\\'") + '\', \'' + product.code + '\')">' +
                    '<div class="font-medium">' + product.name + '</div>' +
                    '<div class="text-sm text-slate-500">' + product.code + ' • Stok: ' + (product.stock || 0) + '</div>' +
                '</div>'
            ).join('');
        }
        
        productResults.classList.remove('hidden');
    }
    
    window.selectProduct = function(id, name, code) {
        const productIdInput = document.getElementById('productId');
        const productSearch = document.getElementById('productSearch');
        const productResults = document.getElementById('productResults');
        
        if (productIdInput) productIdInput.value = id;
        if (productSearch) productSearch.value = name;
        if (productResults) productResults.classList.add('hidden');
        
        // Trigger HPP calculation
        calculateHppPreview();
    };

    // Populate product selects
    function populateProductSelects() {
        const selects = document.querySelectorAll('select[name*="product_id"]');
        selects.forEach(select => {
            // Clear existing options except first
            while (select.children.length > 1) {
                select.removeChild(select.lastChild);
            }
            
            // Add product options
            state.products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                select.appendChild(option);
            });
        });
    }

    // Populate material selects
    function populateMaterialSelects() {
        const selects = document.querySelectorAll('select[name*="material_id"]');
        selects.forEach((select, index) => {
            // Only populate if select is empty (no selection made)
            if (!select.value || select.value === '') {
                // Clear existing options except first
                while (select.children.length > 1) {
                    select.removeChild(select.lastChild);
                }
                
                // Add material options
                state.materials.forEach(material => {
                    const option = document.createElement('option');
                    option.value = material.id;
                    option.textContent = material.name + " (Stok: " + material.stock + " " + material.unit + ")";
                    option.dataset.type = material.type;
                    option.dataset.unit = material.unit;
                    select.appendChild(option);
                });
            }
        });
    }

    // Material management
    let materialCount = 1;

    window.addMaterial = function () {
        const container = document.getElementById("materialRequirements");
        const index = container.children.length;
        
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
        
        // Populate ONLY the new select
        const newSelect = newRow.querySelector('select[name*="material_id"]');
        if (newSelect && state.materials && state.materials.length > 0) {
            state.materials.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = material.name + " (Stok: " + material.stock + " " + material.unit + ")";
                option.dataset.type = material.type;
                option.dataset.unit = material.unit;
                newSelect.appendChild(option);
            });
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
            calculateHppPreview();
        }, 200);
    };

    window.removeMaterial = function (button) {
        const row = button.closest('.material-row');
        const container = document.getElementById("materialRequirements");
        
        if (container.children.length > 1) {
            row.remove();
        } else {
            showNotification('Minimal harus ada satu material', 'warning');
        }
    };

    window.updateMaterialUnit = function (select, index) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.dataset.unit) {
            const unitInput = document.querySelector('input[name="materials[' + index + '][unit]"]');
            const typeInput = document.querySelector('input[name="materials[' + index + '][material_type]"]');
            
            if (unitInput) {
                unitInput.value = selectedOption.dataset.unit;
            }
            if (typeInput) {
                typeInput.value = selectedOption.dataset.type || 'bahan';
            }
            
            // Trigger HPP recalculation
            calculateHppPreview();
        }
    };

    // Form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        
        // Prevent double submission
        if (form.dataset.submitting === "true") {
            console.log("Form already being submitted, ignoring...");
            return;
        }
        form.dataset.submitting = "true";
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Check if this is edit mode
        const isEditMode = form.dataset.editMode === 'true';
        const productionId = form.dataset.productionId;
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin mr-2"></i>' + (isEditMode ? 'Mengupdate...' : 'Menyimpan...');
        }

        // Convert FormData to object and clean up
        const data = {};
        
        // Debug: Log all FormData entries
        console.log('=== FormData Entries ===');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        for (let [key, value] of formData.entries()) {
            if (key.includes('[') && key.includes(']')) {
                // Handle nested arrays
                const matches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    const [, arrayName, index, fieldName] = matches;
                    if (!data[arrayName]) data[arrayName] = [];
                    if (!data[arrayName][index]) data[arrayName][index] = {};
                    data[arrayName][index][fieldName] = value;
                } else {
                    // Handle simple nested objects
                    const matches2 = key.match(/(\w+)\[(\w+)\]/);
                    if (matches2) {
                        const [, objectName, fieldName] = matches2;
                        if (!data[objectName]) data[objectName] = {};
                        data[objectName][fieldName] = value;
                    }
                }
            } else {
                data[key] = value;
            }
        }

        // Debug: Log parsed data
        console.log('=== Parsed Form Data ===', data);
        
        // Special debug for products
        if (data.products) {
            console.log('=== Products Before Filter ===');
            data.products.forEach((product, index) => {
                console.log(`Product ${index}:`, product);
            });
        } else {
            console.log('=== No Products Array Found ===');
        }

        // Clean up empty materials
        if (data.materials) {
            data.materials = data.materials.filter(material => 
                material && material.material_id && material.quantity && parseFloat(material.quantity) > 0
            );
        }

        // Clean up empty operational costs
        if (data.operational_costs) {
            console.log('=== Operational Costs Before Filter ===', data.operational_costs);
            
            data.operational_costs = data.operational_costs.filter(cost => {
                // Handle both manual (cost_type) and auto-generated (description) operational costs
                const hasValidType = cost && (cost.cost_type || cost.description);
                const hasValidAmount = cost && cost.amount && parseFloat(cost.amount) > 0;
                const isValid = hasValidType && hasValidAmount;
                
                console.log(`Operational cost validation:`, {
                    cost: cost,
                    cost_type: cost?.cost_type,
                    description: cost?.description,
                    amount: cost?.amount,
                    hasValidType: hasValidType,
                    hasValidAmount: hasValidAmount,
                    isValid: isValid
                });
                
                return isValid;
            });
            
            console.log('=== Operational Costs After Filter ===', data.operational_costs);
        }

        // Clean up empty products (for multi-product support)
        if (data.products) {
            console.log('=== Products Before Filter ===', data.products);
            
            data.products = data.products.filter(product => {
                const isValid = product && 
                               product.product_id && 
                               product.target_quantity && 
                               parseInt(product.target_quantity) > 0;
                
                console.log(`Product validation:`, {
                    product: product,
                    product_id: product?.product_id,
                    target_quantity: product?.target_quantity,
                    target_quantity_int: parseInt(product?.target_quantity || 0),
                    isValid: isValid
                });
                
                return isValid;
            });
            
            console.log('=== Products After Filter ===', data.products);
            
            // Validate that we have at least one product
            if (data.products.length === 0) {
                console.error('=== NO VALID PRODUCTS FOUND ===');
                showNotification('Minimal harus ada satu produk dengan target quantity > 0', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = isEditMode ? 'Update Produksi' : 'Simpan Produksi';
                }
                return;
            }
        } else {
            // If no products array found, show error
            console.error('=== NO PRODUCTS ARRAY FOUND ===');
            showNotification('Data produk tidak ditemukan. Pastikan Anda telah mengisi produk.', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = isEditMode ? 'Update Produksi' : 'Simpan Produksi';
            }
            return;
        }

        // Validate required fields
        const requiredFields = [
            { field: 'outlet_id', name: 'Outlet' },
            { field: 'production_line', name: 'Lini Produksi' },
            { field: 'start_date', name: 'Tanggal Mulai' },
            { field: 'end_date', name: 'Tanggal Selesai' },
            { field: 'priority', name: 'Prioritas' }
        ];

        const missingFields = [];
        for (const { field, name } of requiredFields) {
            if (!data[field] || data[field].toString().trim() === '') {
                missingFields.push(name);
            }
        }

        if (missingFields.length > 0) {
            showNotification(`Field berikut harus diisi: ${missingFields.join(', ')}`, 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = isEditMode ? 'Update Produksi' : 'Simpan Produksi';
            }
            return;
        }

        // Validate date range
        if (data.start_date && data.end_date) {
            const startDate = new Date(data.start_date);
            const endDate = new Date(data.end_date);
            
            if (endDate < startDate) {
                showNotification('Tanggal selesai tidak boleh lebih awal dari tanggal mulai', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = isEditMode ? 'Update Produksi' : 'Simpan Produksi';
                }
                return;
            }
        }

        // Clean up tofu data if business type is not tofu
        if (data.business_type !== 'tofu' && data.tofu_data) {
            delete data.tofu_data;
        }

        console.log('Form data to submit:', {
            isEditMode: isEditMode,
            productionId: productionId,
            url: isEditMode && productionId ? updateUrl.replace(':id', productionId) : storeUrl,
            method: isEditMode && productionId ? 'PUT' : 'POST',
            data: data
        });

        // Determine URL and method
        let url, method;
        if (isEditMode && productionId) {
            url = updateUrl.replace(':id', productionId);
            method = 'PUT';
        } else {
            url = storeUrl;
            method = 'POST';
        }

        // Submit to server
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response URL:', response.url);
            console.log('Response method:', method);
            
            // Handle both success and error responses
            return response.json().then(data => ({
                status: response.status,
                ok: response.ok,
                data: data
            }));
        })
        .then(result => {
            console.log('Full response:', result);
            
            if (result.ok && result.data.success) {
                showNotification(isEditMode ? 'Produksi berhasil diupdate' : 'Produksi berhasil dibuat', 'success');
                closeCreateModal();
                
                // Auto reload data using Alpine.js component
                const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
                if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                    const component = alpineComponent._x_dataStack[0];
                    component.fetchData();
                    component.fetchStats();
                } else if (state.dataTable) {
                    // Fallback to DataTable reload
                    state.dataTable.ajax.reload();
                }
                
                loadStatistics();
            } else {
                // Handle validation errors (422) and other errors
                if (result.status === 422 && result.data.errors) {
                    console.error('Validation errors (422):', result.data.errors);
                    let errorMessage = 'Validasi gagal:\n';
                    for (const field in result.data.errors) {
                        if (Array.isArray(result.data.errors[field])) {
                            errorMessage += `• ${field}: ${result.data.errors[field].join(', ')}\n`;
                        } else {
                            errorMessage += `• ${field}: ${result.data.errors[field]}\n`;
                        }
                    }
                    showNotification(errorMessage, 'error');
                } else {
                    console.error('Server error:', result.data);
                    showNotification(result.data.message || (isEditMode ? 'Gagal mengupdate produksi' : 'Gagal membuat produksi'), 'error');
                }
            }
        })
        .catch(error => {
            console.error('Request error:', error);
            showNotification('Terjadi kesalahan saat menyimpan', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = isEditMode ? 'Update Produksi' : 'Simpan Produksi';
            }
        });
    }

    // Handle realization form submission
    function handleRealizationSubmit(e) {
        e.preventDefault();
        
        
        // Prevent double submission
        if (form.dataset.submitting === "true") {
            console.log("Form already being submitted, ignoring...");
            return;
        }
        form.dataset.submitting = "true";
        const form = e.target;
        const productionId = form.dataset.productionId;
        
        if (!productionId) {
            showNotification('ID produksi tidak ditemukan', 'error');
            return;
        }
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...';
        }
        
        // Convert FormData to JSON
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        const url = addRealizationUrl.replace(':id', productionId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Realisasi berhasil ditambahkan', 'success');
                closeRealizationModal();
                
                // Auto reload data using Alpine.js component
                const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
                if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                    const component = alpineComponent._x_dataStack[0];
                    component.fetchData();
                    component.fetchStats();
                } else if (state.dataTable) {
                    // Fallback to DataTable reload
                    state.dataTable.ajax.reload();
                }
                
                loadStatistics();
            } else {
                showNotification(data.message || 'Gagal menambahkan realisasi', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menyimpan realisasi', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Tambah Realisasi';
            }
        });
    }

    // Add realization
    window.addRealization = function(productionId, quantity, notes) {
        if (!addRealizationUrl) return;
        
        const submitBtn = document.querySelector('#realizationModal button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }

        // Use Laravel route URL from Blade template
        const url = addRealizationUrl.replace(':id', window.currentProductionId);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity_produced: parseInt(quantity),
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Realisasi berhasil ditambahkan', 'success');
                closeRealizationModal();
                
                // Auto reload data using Alpine.js component
                const alpineComponent = document.querySelector('[x-data*="productionCrud"]');
                if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                    const component = alpineComponent._x_dataStack[0];
                    component.fetchData();
                    component.fetchStats();
                } else if (state.dataTable) {
                    // Fallback to DataTable reload
                    state.dataTable.ajax.reload();
                }
                
                loadStatistics();
            } else {
                showNotification(data.message || 'Gagal menambahkan realisasi', 'error');
            }
        })
        .catch(error => {
            showNotification('Terjadi kesalahan saat menambahkan realisasi', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Realisasi';
            }
        });
    };

    // Initialize operational costs
    function initializeOperationalCosts() {
        // Add initial operational cost row if container is empty
        const container = document.getElementById('operationalCosts');
        if (container && container.children.length === 0) {
            addOperationalCost();
        }
    }

    // Add operational cost row
    window.addOperationalCost = function() {
        const container = document.getElementById('operationalCosts');
        const index = state.operationalCostIndex++;
        
        const row = document.createElement('div');
        row.className = 'operational-cost-row';
        row.innerHTML = 
            '<div class="flex items-center gap-3">' +
                '<select name="operational_costs[' + index + '][cost_type]" ' +
                        'class="w-40 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                        'onchange="calculateHppPreview()">' +
                    '<option value="">Pilih Jenis</option>' +
                    '<option value="listrik">Listrik</option>' +
                    '<option value="air">Air</option>' +
                    '<option value="gas">Gas</option>' +
                    '<option value="bahan_bakar">Bahan Bakar</option>' +
                    '<option value="maintenance">Maintenance</option>' +
                    '<option value="lainnya">Lainnya</option>' +
                    '<option value="Biaya Listrik (Harian)">Biaya Listrik (Harian)</option>' +
                    '<option value="Biaya Air (Harian)">Biaya Air (Harian)</option>' +
                    '<option value="Biaya Bahan Bakar (Harian)">Biaya Bahan Bakar (Harian)</option>' +
                    '<option value="Biaya Gas (Harian)">Biaya Gas (Harian)</option>' +
                    '<option value="Gaji Office (Harian)">Gaji Office (Harian)</option>' +
                '</select>' +
                '<div class="flex-1">' +
                    '<input type="number" name="operational_costs[' + index + '][amount]" min="0" step="0.01" ' +
                           'class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                           'placeholder="Jumlah biaya" onchange="calculateHppPreview(); updateOperationalCostDisplay(this)" oninput="updateOperationalCostDisplay(this)">' +
                    '<div class="text-xs text-slate-500 mt-1 operational-cost-display">Format: Rp 0</div>' +
                '</div>' +
                '<input type="text" name="operational_costs[' + index + '][description]" ' +
                       'class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" ' +
                       'placeholder="Deskripsi (opsional)">' +
                '<button type="button" onclick="removeOperationalCost(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">' +
                    '<i class="bx bx-trash"></i>' +
                '</button>' +
            '</div>';
        
        container.appendChild(row);
        
        // Trigger HPP recalculation
        calculateHppPreview();
    };

    // Remove operational cost row
    window.removeOperationalCost = function(button) {
        const row = button.closest('.operational-cost-row');
        if (row) {
            row.remove();
            calculateHppPreview();
        }
    };

    // Update labor cost display
    window.updateLaborCostDisplay = function(input) {
        const value = parseFloat(input.value) || 0;
        const displayElement = document.getElementById('costPerWorkerDisplay');
        if (displayElement) {
            displayElement.textContent = 'Format: ' + formatCurrency(value);
        }
    };

    // Update operational cost display
    window.updateOperationalCostDisplay = function(input) {
        const value = parseFloat(input.value) || 0;
        const displayElement = input.parentElement.querySelector('.operational-cost-display');
        if (displayElement) {
            displayElement.textContent = 'Format: ' + formatCurrency(value);
        }
    };

    // Calculate labor cost
    window.calculateLaborCost = function() {
        console.log('🔧 calculateLaborCost() called');
        
        const workerCountInput = document.querySelector('input[name="labor_costs[worker_count]"]');
        const costPerWorkerInput = document.querySelector('input[name="labor_costs[cost_per_worker]"]');
        const totalLaborCostInput = document.getElementById('totalLaborCost');
        const laborCostHiddenInput = document.getElementById('laborCostHidden');
        
        console.log('🔍 Labor cost inputs found:', {
            workerCountInput: !!workerCountInput,
            costPerWorkerInput: !!costPerWorkerInput,
            totalLaborCostInput: !!totalLaborCostInput,
            laborCostHiddenInput: !!laborCostHiddenInput
        });
        
        if (!workerCountInput || !costPerWorkerInput) {
            console.warn('⚠️ Labor cost inputs not found');
            return;
        }
        
        const workerCount = parseInt(workerCountInput.value) || 0;
        const costPerWorker = parseFloat(costPerWorkerInput.value) || 0;
        const totalCost = workerCount * costPerWorker;
        
        console.log('💰 Labor cost calculation:', {
            workerCount: workerCount,
            costPerWorker: costPerWorker,
            totalCost: totalCost,
            formattedCost: formatCurrency(totalCost),
            workerCountValue: workerCountInput.value,
            costPerWorkerValue: costPerWorkerInput.value
        });
        
        // Update display fields
        if (totalLaborCostInput) {
            totalLaborCostInput.value = formatCurrency(totalCost);
        }
        
        if (laborCostHiddenInput) {
            laborCostHiddenInput.value = totalCost;
        }
        
        // Debounce HPP calculation to prevent multiple rapid calls
        clearTimeout(window.laborCostTimeout);
        window.laborCostTimeout = setTimeout(() => {
            console.log('🚀 Calling calculateHppPreview() after debounce');
            calculateHppPreview();
        }, 300);
    };

    // Calculate HPP preview with debouncing
    window.calculateHppPreview = function() {
        // Clear any existing timeout
        clearTimeout(window.hppPreviewTimeout);
        
        // Set new timeout for debouncing
        window.hppPreviewTimeout = setTimeout(() => {
            executeHppPreview();
        }, 500);
    };
    
    // Execute HPP preview calculation
    function executeHppPreview() {
        const form = document.getElementById('productionForm');
        if (!form) {
            console.warn('Production form not found');
            return;
        }
        
        if (!hppPreviewUrl) {
            console.warn('HPP preview URL not defined');
            return;
        }
        
        console.log('🔄 Executing HPP preview calculation');
        
        const formData = new FormData(form);
        
        // Convert FormData to regular object for easier handling
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key.includes('[') && key.includes(']')) {
                // Handle nested arrays
                const matches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    const [, arrayName, index, fieldName] = matches;
                    if (!data[arrayName]) data[arrayName] = [];
                    if (!data[arrayName][index]) data[arrayName][index] = {};
                    data[arrayName][index][fieldName] = value;
                } else {
                    // Handle simple nested objects
                    const matches2 = key.match(/(\w+)\[(\w+)\]/);
                    if (matches2) {
                        const [, objectName, fieldName] = matches2;
                        if (!data[objectName]) data[objectName] = {};
                        data[objectName][fieldName] = value;
                    }
                }
            } else {
                data[key] = value;
            }
        }
        
        // Also collect data from all form elements (including empty ones)
        const allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            const name = input.name;
            if (name && name.includes('[') && name.includes(']')) {
                const matches = name.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    const [, arrayName, index, fieldName] = matches;
                    if (!data[arrayName]) data[arrayName] = [];
                    if (!data[arrayName][index]) data[arrayName][index] = {};
                    // Always update with current value
                    data[arrayName][index][fieldName] = input.value || '';
                } else {
                    const matches2 = name.match(/(\w+)\[(\w+)\]/);
                    if (matches2) {
                        const [, objectName, fieldName] = matches2;
                        if (!data[objectName]) data[objectName] = {};
                        data[objectName][fieldName] = input.value || '';
                    }
                }
            } else if (name) {
                data[name] = input.value || '';
            }
        });
        
        // Clean up empty materials and operational costs
        if (data.materials) {
            data.materials = data.materials.filter(material => 
                material && material.material_id && material.quantity
            );
        }
        
        if (data.operational_costs) {
            console.log('=== HPP Preview Operational Costs Before Filter ===', data.operational_costs);
            
            data.operational_costs = data.operational_costs.filter(cost => {
                // Handle both manual (cost_type) and auto-generated (description) operational costs
                const hasValidType = cost && (cost.cost_type || cost.description);
                const hasValidAmount = cost && cost.amount && parseFloat(cost.amount) > 0;
                const isValid = hasValidType && hasValidAmount;
                
                return isValid;
            });
            
            console.log('=== HPP Preview Operational Costs After Filter ===', data.operational_costs);
        }
        
        // Also check for calculated operational cost from monthly costs
        const calculatedOpCost = document.getElementById('calculatedOperationalCost');
        if (calculatedOpCost && calculatedOpCost.value) {
            data.calculatedOperationalCost = calculatedOpCost.value;
        }
        
        // Ensure auto-generated operational costs are included
        // Re-collect operational costs from all visible form fields
        const operationalContainer = document.getElementById('operationalCosts');
        if (operationalContainer) {
            const operationalRows = operationalContainer.querySelectorAll('div[class*="operational-row"], div[class*="auto-operational-row"]');
            const operationalCosts = [];
            
            operationalRows.forEach((row, index) => {
                const descInput = row.querySelector('input[name*="[description]"]');
                const amountInput = row.querySelector('input[name*="[amount]"]');
                
                if (descInput && amountInput && amountInput.value && parseFloat(amountInput.value) > 0) {
                    operationalCosts.push({
                        description: descInput.value || '',
                        amount: parseFloat(amountInput.value) || 0
                    });
                }
            });
            
            // Override the operational_costs array with fresh data
            if (operationalCosts.length > 0) {
                data.operational_costs = operationalCosts;
                console.log('🔧 Auto operational costs included in HPP preview:', operationalCosts);
            }
        }
        
        console.log('HPP Preview Data:', data);
        
        // Debug labor costs specifically
        if (data.labor_costs) {
            console.log('💰 Labor costs in HPP preview:', data.labor_costs);
        } else {
            console.warn('⚠️ No labor costs found in HPP preview data');
        }
        
        // Convert data to FormData for proper CSRF handling
        const requestFormData = new FormData();
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            requestFormData.append('_token', csrfToken.getAttribute('content'));
        }
        
        // Add all form data
        for (const [key, value] of Object.entries(data)) {
            if (typeof value === 'object' && value !== null) {
                // Handle nested objects (materials, labor_costs, operational_costs)
                if (Array.isArray(value)) {
                    // Handle arrays like materials or operational_costs
                    value.forEach((item, index) => {
                        if (typeof item === 'object' && item !== null) {
                            for (const [subKey, subValue] of Object.entries(item)) {
                                if (subValue !== null && subValue !== '') {
                                    requestFormData.append(key + '[' + index + '][' + subKey + ']', subValue);
                                }
                            }
                        }
                    });
                } else {
                    // Handle objects like labor_costs
                    for (const [subKey, subValue] of Object.entries(value)) {
                        if (subValue !== null && subValue !== '') {
                            requestFormData.append(key + '[' + subKey + ']', subValue);
                        }
                    }
                }
            } else if (value !== null && value !== '') {
                requestFormData.append(key, value);
            }
        }
        
        // Debug: Log what we're sending
        console.log('Sending HPP Preview Request to:', hppPreviewUrl);
        
        fetch(hppPreviewUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: requestFormData
        })
        .then(response => {
            console.log('HPP Preview Response Status:', response.status);
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('HPP Preview Response:', data);
            if (data.success) {
                // Access data directly from controller response
                const responseData = data.data;
                
                // Debug labor cost in response
                console.log('💰 Labor cost in response:', responseData.labor_cost);
                
                // Update preview elements with safe formatting
                const previewMaterialCost = document.getElementById('previewMaterialCost');
                const previewLaborCost = document.getElementById('previewLaborCost');
                const previewOperationalCost = document.getElementById('previewOperationalCost');
                const previewTotalCost = document.getElementById('previewTotalCost');
                const previewHppPerUnit = document.getElementById('previewHppPerUnit');
                
                console.log('🎯 Updating UI elements:', {
                    previewLaborCost: !!previewLaborCost,
                    laborCostValue: responseData.labor_cost || 0,
                    formattedValue: safeFormatCurrency(responseData.labor_cost || 0)
                });
                
                if (previewMaterialCost) previewMaterialCost.textContent = safeFormatCurrency(responseData.material_cost);
                if (previewLaborCost) previewLaborCost.textContent = safeFormatCurrency(responseData.labor_cost || 0);
                if (previewOperationalCost) previewOperationalCost.textContent = safeFormatCurrency(responseData.operational_cost);
                if (previewTotalCost) previewTotalCost.textContent = safeFormatCurrency(responseData.total_cost);
                if (previewHppPerUnit) previewHppPerUnit.textContent = safeFormatCurrency(responseData.hpp_per_unit);
                
                // Display material breakdown if available
                if (responseData.breakdown && responseData.breakdown.materials && responseData.breakdown.materials.length > 0) {
                    if (typeof displayMaterialBreakdown === 'function') {
                        displayMaterialBreakdown(responseData.breakdown.materials);
                    }
                } else {
                    if (typeof hideMaterialBreakdown === 'function') {
                        hideMaterialBreakdown();
                    }
                }
                
                // Display operational cost breakdown if available
                if (responseData.breakdown && responseData.breakdown.operational_costs && responseData.breakdown.operational_costs.length > 0) {
                    console.log('Operational Details:', responseData.breakdown.operational_costs);
                }
            } else {
                console.error('HPP Preview Error:', data.message);
                // Reset preview values on error
                resetHppPreviewValues();
            }
        })
        .catch(error => {
            console.error('HPP Preview Request Error:', error);
            // Reset preview values on error
            resetHppPreviewValues();
        });
    };
    
    // Helper function to reset HPP preview values
    function resetHppPreviewValues() {
        const previewMaterialCost = document.getElementById('previewMaterialCost');
        const previewLaborCost = document.getElementById('previewLaborCost');
        const previewOperationalCost = document.getElementById('previewOperationalCost');
        const previewTotalCost = document.getElementById('previewTotalCost');
        const previewHppPerUnit = document.getElementById('previewHppPerUnit');
        
        if (previewMaterialCost) previewMaterialCost.textContent = 'Rp 0';
        if (previewLaborCost) previewLaborCost.textContent = 'Rp 0';
        if (previewOperationalCost) previewOperationalCost.textContent = 'Rp 0';
        if (previewTotalCost) previewTotalCost.textContent = 'Rp 0';
        if (previewHppPerUnit) previewHppPerUnit.textContent = 'Rp 0';
        
        if (typeof hideMaterialBreakdown === 'function') {
            hideMaterialBreakdown();
        }
    }

    // Safe number formatting function
    function safeFormatCurrency(amount) {
        try {
            const numAmount = parseFloat(amount) || 0;
            if (isNaN(numAmount) || !isFinite(numAmount)) {
                return 'Rp 0';
            }
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(numAmount);
        } catch (error) {
            console.warn('Currency formatting error:', error);
            return 'Rp 0';
        }
    }

    // Safe string formatting function
    function safeString(value, fallback = '') {
        if (value === null || value === undefined || value === 'null' || value === 'undefined') {
            return fallback;
        }
        return String(value);
    }

    // Display material breakdown
    function displayMaterialBreakdown(materialDetails) {
        const breakdown = document.getElementById('materialBreakdown');
        const content = document.getElementById('materialBreakdownContent');
        
        if (!breakdown || !content) return;
        
        // Clear previous content
        content.innerHTML = '';
        
        // Check if materialDetails is valid
        if (!materialDetails || !Array.isArray(materialDetails) || materialDetails.length === 0) {
            hideMaterialBreakdown();
            return;
        }
        
        materialDetails.forEach((material, index) => {
            const materialDiv = document.createElement('div');
            materialDiv.className = 'flex justify-between items-center py-2 border-b border-slate-100 last:border-b-0';
            
            // Safe property access with fallbacks using helper functions
            const materialName = safeString(material.name || material.material_name, 'Unknown Material');
            const materialCode = safeString(material.code || material.material_code);
            const materialMerk = safeString(material.merk);
            const quantity = parseFloat(material.quantity) || 0;
            const unitPrice = parseFloat(material.unit_price) || 0;
            const totalCost = parseFloat(material.total_cost) || 0;
            const unit = safeString(material.unit, 'Unit');
            const fifoUsed = Boolean(material.fifo_used);
            
            // Validate numeric values
            if (isNaN(quantity) || isNaN(unitPrice) || isNaN(totalCost)) {
                console.warn('Invalid numeric values in material breakdown:', material);
                return; // Skip this material if numeric values are invalid
            }
            
            // Create FIFO indicator
            const fifoIndicator = fifoUsed ? 
                '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">FIFO</span>' : 
                '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">Base</span>';
            
            // Build material display name
            let displayName = materialName;
            if (materialCode) {
                displayName += ` (${materialCode})`;
            }
            if (materialMerk) {
                displayName += ` - ${materialMerk}`;
            }
            
            // Create the material row HTML
            materialDiv.innerHTML = 
                '<div class="flex-1">' +
                    '<div class="flex items-center gap-2">' +
                        '<span class="font-medium text-sm">' + displayName + '</span>' +
                        fifoIndicator +
                    '</div>' +
                    '<div class="text-xs text-slate-500 mt-1">' +
                        'Qty: ' + quantity + ' ' + unit + ' × ' + safeFormatCurrency(unitPrice) + '/' + unit +
                    '</div>' +
                '</div>' +
                '<div class="text-right">' +
                    '<div class="font-medium text-sm">' + safeFormatCurrency(totalCost) + '</div>' +
                '</div>';
            
            content.appendChild(materialDiv);
        });
        
        // Only show breakdown if we have valid materials
        if (content.children.length > 0) {
            breakdown.classList.remove('hidden');
        } else {
            hideMaterialBreakdown();
        }
    }

    // Hide material breakdown
    function hideMaterialBreakdown() {
        const breakdown = document.getElementById('materialBreakdown');
        if (breakdown) {
            breakdown.classList.add('hidden');
        }
    }

    // Auto-calculate HPP when inputs change
    function setupRealtimeHppCalculation() {
        const form = document.getElementById('productionForm');
        if (!form) {
            return;
        }
        
        // Use event delegation to handle dynamically added elements
        form.addEventListener('input', function(e) {
            if (e.target.matches('input[type="number"], input[type="text"], select, textarea')) {
                debouncedCalculateHpp();
            }
        });
        
        form.addEventListener('change', function(e) {
            if (e.target.matches('input[type="number"], input[type="text"], select, textarea')) {
                calculateHppPreview();
            }
        });
    }

    // Debounce function to prevent too many API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Create a debounced version of calculateHppPreview
    const debouncedCalculateHpp = debounce(calculateHppPreview, 500);

    // Format currency input
    window.formatCurrencyInput = function(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('id-ID');
        }
    };

    // Format currency for display
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm';
        
        // Set colors based on type
        switch (type) {
            case 'success':
                notification.className += ' bg-green-500 text-white';
                break;
            case 'error':
                notification.className += ' bg-red-500 text-white';
                break;
            case 'warning':
                notification.className += ' bg-yellow-500 text-white';
                break;
            default:
                notification.className += ' bg-blue-500 text-white';
        }
        
        notification.textContent = message;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Initialize product search when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        // Delay initialization to ensure DOM is ready
        setTimeout(() => {
            initializeProductSearch();
        }, 1000);
    });

    // Toggle attendance date function
    window.toggleAttendanceDate = function(checkbox) {
        // Handle both cases: called with checkbox parameter or without
        if (!checkbox) {
            checkbox = document.getElementById('fromAttendance');
        }
        
        if (!checkbox) {
            console.error('Checkbox not found for toggleAttendanceDate');
            return;
        }

        const section = document.getElementById('attendanceDateSection');
        if (section) {
            if (checkbox.checked) {
                section.classList.remove('hidden');
                // Set default date to today
                const dateInput = section.querySelector('input[type="date"]');
                if (dateInput && !dateInput.value) {
                    dateInput.value = new Date().toISOString().split('T')[0];
                }
            } else {
                section.classList.add('hidden');
                // Clear the date value
                const dateInput = section.querySelector('input[type="date"]');
                if (dateInput) {
                    dateInput.value = '';
                }
            }
        } else {
            console.error('attendanceDateSection not found');
        }
        
        // Recalculate labor costs if needed
        if (typeof calculateLaborCost === 'function') {
            calculateLaborCost();
        }
    };

    // Get attendance count
    window.getAttendanceCount = function() {
        const dateInput = document.getElementById('attendanceDate');
        const resultDiv = document.getElementById('attendanceResult');
        
        if (!dateInput || !dateInput.value) {
            if (resultDiv) {
                resultDiv.innerHTML = '<span class="text-red-600">Pilih tanggal terlebih dahulu</span>';
            }
            return;
        }
        
        if (resultDiv) {
            resultDiv.innerHTML = '<span class="text-blue-600">Mengambil data...</span>';
        }
        
        const outletId = document.getElementById('outletSelect')?.value;
        
        fetch(attendanceCountUrl + '?' + new URLSearchParams({
            date: dateInput.value,
            outlet_id: outletId || ''
        }))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const count = data.data.count || 0;
                const avgSalary = data.data.average_salary || 0;
                
                // Update worker count
                const workerCountInput = document.querySelector('input[name="labor_costs[worker_count]"]');
                if (workerCountInput) {
                    workerCountInput.value = count;
                }
                
                // Update cost per worker if available
                if (avgSalary > 0) {
                    const costPerWorkerInput = document.querySelector('input[name="labor_costs[cost_per_worker]"]');
                    if (costPerWorkerInput) {
                        costPerWorkerInput.value = avgSalary;
                    }
                }
                
                // Update result display
                if (resultDiv) {
                    resultDiv.innerHTML = `
                        <span class="text-green-600">
                            Ditemukan ${count} pekerja hadir
                            ${avgSalary > 0 ? ` dengan rata-rata gaji Rp ${new Intl.NumberFormat('id-ID').format(avgSalary)}` : ''}
                        </span>
                    `;
                }
                
                // Recalculate labor costs
                if (typeof calculateLaborCost === 'function') {
                    calculateLaborCost();
                }
            } else {
                if (resultDiv) {
                    resultDiv.innerHTML = `<span class="text-red-600">${data.message || 'Gagal mengambil data absensi'}</span>`;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching attendance:', error);
            if (resultDiv) {
                resultDiv.innerHTML = '<span class="text-red-600">Terjadi kesalahan saat mengambil data</span>';
            }
        });
    };

    // Toggle monthly costs checkbox for operational costs
    window.toggleMonthlyCosts = function(checkbox) {
        const monthlyContainer = checkbox.closest('.operational-cost-section').querySelector('.monthly-costs-container');
        if (monthlyContainer) {
            if (checkbox.checked) {
                monthlyContainer.classList.remove('hidden');
                // Load monthly costs data for current outlet
                loadMonthlyCostsForOperational();
            } else {
                monthlyContainer.classList.add('hidden');
                // Clear monthly costs inputs
                clearMonthlyCostsInputs();
            }
        }
    };

    // Load monthly costs for operational costs section
    function loadMonthlyCostsForOperational() {
        const outletId = state.selectedOutlet || getCurrentOutletId();
        if (!outletId || outletId === 'ALL') return;

        // Fetch monthly costs data
        fetch(`/admin/produksi/monthly-production-costs/data?outlet_id=${outletId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.current) {
                    populateMonthlyCostsData(data.current);
                }
            })
            .catch(error => {
                console.error('Error loading monthly costs:', error);
            });
    }

    // Populate monthly costs data in operational section
    function populateMonthlyCostsData(monthlyData) {
        const totalCost = monthlyData.total_cost || 0;
        
        // Update display
        const totalDisplay = document.getElementById('monthlyTotalCost');
        if (totalDisplay) {
            totalDisplay.textContent = formatCurrency(totalCost);
        }
        
        // Set hidden input for calculation
        const hiddenInput = document.getElementById('monthlyTotalCostValue');
        if (hiddenInput) {
            hiddenInput.value = totalCost;
        }
    }

    // Clear monthly costs inputs
    function clearMonthlyCostsInputs() {
        const workDaysInput = document.getElementById('workDaysCount');
        const officePercentInput = document.getElementById('officePercentage');
        const totalDisplay = document.getElementById('monthlyTotalCost');
        const hiddenInput = document.getElementById('monthlyTotalCostValue');
        
        if (workDaysInput) workDaysInput.value = '';
        if (officePercentInput) officePercentInput.value = '';
        if (totalDisplay) totalDisplay.textContent = 'Rp 0';
        if (hiddenInput) hiddenInput.value = '0';
        
        // Recalculate operational costs
        calculateOperationalFromMonthly();
    }

    // Calculate operational costs from monthly data
    window.calculateOperationalFromMonthly = function() {
        const workDays = parseInt(document.getElementById('workDaysCount')?.value) || 0;
        const officePercent = parseFloat(document.getElementById('officePercentage')?.value) || 0;
        const monthlyTotal = parseFloat(document.getElementById('monthlyTotalCostValue')?.value) || 0;
        
        if (workDays > 0 && monthlyTotal > 0) {
            // Calculate daily cost
            const dailyCost = monthlyTotal / 30; // Assume 30 days per month
            const totalOperationalCost = dailyCost * workDays;
            
            // Add office salary percentage if specified
            let finalCost = totalOperationalCost;
            if (officePercent > 0) {
                const officeAddition = (totalOperationalCost * officePercent) / 100;
                finalCost = totalOperationalCost + officeAddition;
            }
            
            // Update display
            const resultDisplay = document.getElementById('operationalCostResult');
            if (resultDisplay) {
                resultDisplay.textContent = formatCurrency(finalCost);
            }
            
            // Update hidden input for form submission
            const hiddenInput = document.getElementById('calculatedOperationalCost');
            if (hiddenInput) {
                hiddenInput.value = finalCost;
            }
            
            // Trigger HPP recalculation
            if (typeof calculateHppPreview === 'function') {
                calculateHppPreview();
            }
        }
    };

})();