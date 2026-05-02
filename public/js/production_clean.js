// Production Management JavaScript - Clean Version
(function () {
    "use strict";

    console.log('Production.js loaded');

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
        console.log('DOM Content Loaded - Initializing production module');
        initializeOutletSelector();
        initializeDataTable();
        initializeEventListeners();
        loadProducts();
        loadMaterials();
        loadStatistics();
        initializeOperationalCosts();
        
        // Test modal immediately
        setTimeout(() => {
            const createBtn = document.getElementById("createProductionBtn");
            const modal = document.getElementById("createModal");
            console.log('After DOM load - Create button:', createBtn);
            console.log('After DOM load - Modal:', modal);
        }, 1000);
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
        }
    }

    // Initialize DataTable
    function initializeDataTable() {
        const table = $("#productionTable");
        if (table.length === 0) return;

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
                { data: "production_code", name: "production_code" },
                { data: "product_name", name: "product.nama_produk" },
                { data: "production_line", name: "production_line" },
                {
                    data: "target_quantity",
                    name: "target_quantity",
                    render: $.fn.dataTable.render.number(",", ".", 0),
                },
                {
                    data: "realized_quantity",
                    name: "realized_quantity",
                    render: $.fn.dataTable.render.number(",", ".", 0),
                },
                {
                    data: "progress",
                    name: "progress",
                    render: function (data, type, row) {
                        return `
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-slate-100 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: ${data}%"></div>
                                </div>
                                <span class="text-sm text-slate-600">${data}%</span>
                            </div>
                        `;
                    },
                },
                {
                    data: "status_badge",
                    name: "status",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "start_date",
                    name: "start_date",
                    render: function (data) {
                        return new Date(data).toLocaleDateString("id-ID", {
                            day: "2-digit",
                            month: "short",
                            year: "numeric",
                        });
                    },
                },
                {
                    data: "actions",
                    name: "actions",
                    orderable: false,
                    searchable: false,
                },
            ],
            order: [[0, "desc"]],
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
            },
        });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        console.log('Initializing event listeners...');
        
        // Create button
        const createBtn = document.getElementById("createProductionBtn");
        console.log('Create button found:', createBtn);
        
        if (createBtn) {
            createBtn.addEventListener("click", function(e) {
                e.preventDefault();
                console.log('Create button clicked');
                openCreateModal();
            });
        } else {
            console.error('Create button not found!');
        }

        // Form submission
        const productionForm = document.getElementById("productionForm");
        if (productionForm) {
            productionForm.addEventListener("submit", handleFormSubmit);
        }

        // Filter buttons
        $("#filterStatus, #filterLine, #filterStartDate, #filterEndDate").on(
            "change",
            function () {
                if (state.dataTable) {
                    state.dataTable.ajax.reload();
                }
            }
        );

        // Close modal on ESC
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                closeCreateModal();
            }
        });
    }

    // Load products
    function loadProducts() {
        state.products = [];
    }

    // Load materials
    function loadMaterials() {
        if (!materialsUrl || !state.selectedOutlet) return;
        
        fetch(materialsUrl + "?outlet_id=" + state.selectedOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    state.materials = data.data;
                    populateMaterialSelects();
                }
            })
            .catch((error) => console.error("Error loading materials:", error));
    }

    // Load statistics
    function loadStatistics() {
        if (!statisticsUrl || !state.selectedOutlet) return;
        
        fetch(statisticsUrl + "?outlet_id=" + state.selectedOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    updateStatistics(data.data);
                }
            })
            .catch((error) =>
                console.error("Error loading statistics:", error)
            );
    }

    // Update statistics
    function updateStatistics(stats) {
        const activeCount = document.getElementById("activeCount");
        if (activeCount) {
            activeCount.textContent = stats.active || 0;
        }
    }

    // Modal functions
    window.openCreateModal = function () {
        console.log('openCreateModal called');
        const modal = document.getElementById("createModal");
        console.log('Modal element found:', modal);
        
        if (modal) {
            modal.classList.remove("hidden");
            document.body.style.overflow = "hidden";
            console.log('Modal should be visible now');
            
            // Load materials when modal opens
            loadMaterials();
            
            // Set default values
            const today = new Date().toISOString().split('T')[0];
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            
            if (startDateInput && !startDateInput.value) {
                startDateInput.value = today;
            }
            
            // Set end date to 7 days from start date
            if (endDateInput && !endDateInput.value && startDateInput.value) {
                const startDate = new Date(startDateInput.value);
                startDate.setDate(startDate.getDate() + 7);
                endDateInput.value = startDate.toISOString().split('T')[0];
            }
        } else {
            console.error('Modal element not found!');
        }
    };

    window.closeCreateModal = function () {
        console.log('closeCreateModal called');
        const modal = document.getElementById("createModal");
        if (modal) {
            modal.classList.add("hidden");
            document.body.style.overflow = "auto";
            
            // Reset form
            const form = document.getElementById("productionForm");
            if (form) {
                form.reset();
            }
        }
    };

    // Populate material selects
    function populateMaterialSelects() {
        const selects = document.querySelectorAll('select[name*="material_id"]');
        selects.forEach(select => {
            // Clear existing options except first
            while (select.children.length > 1) {
                select.removeChild(select.lastChild);
            }
            
            // Add material options
            state.materials.forEach(material => {
                const option = document.createElement('option');
                option.value = material.id;
                option.textContent = `${material.name} (Stok: ${material.stock} ${material.unit})`;
                option.dataset.type = material.type;
                option.dataset.unit = material.unit;
                select.appendChild(option);
            });
        });
    }

    // Material management
    let materialCount = 1;

    window.addMaterial = function () {
        const container = document.getElementById("materialRequirements");
        const newRow = document.createElement("div");
        newRow.className = "flex items-center gap-3 material-row";
        newRow.innerHTML = `
            <input type="hidden" name="materials[${materialCount}][material_type]" value="">
            <select name="materials[${materialCount}][material_id]" 
                    class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    onchange="updateMaterialUnit(this, ${materialCount})">
                <option value="">Pilih Material</option>
            </select>
            <input type="number" name="materials[${materialCount}][quantity]" min="1" step="0.01"
                   class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Qty" onchange="calculateHppPreview()">
            <select name="materials[${materialCount}][unit]"
                    class="w-24 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="kg">kg</option>
                <option value="pcs">pcs</option>
                <option value="roll">roll</option>
                <option value="unit">unit</option>
            </select>
            <button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                <i class='bx bx-trash'></i>
            </button>
        `;

        container.appendChild(newRow);
        materialCount++;
        populateMaterialSelects();
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
            const unitSelect = document.querySelector(`select[name="materials[${index}][unit]"]`);
            const typeInput = document.querySelector(`input[name="materials[${index}][material_type]"]`);
            
            if (unitSelect) {
                unitSelect.value = selectedOption.dataset.unit;
            }
            if (typeInput) {
                typeInput.value = selectedOption.dataset.type || 'bahan';
            }
        }
    };

    // Form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const submitBtn = e.target.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...';
        }

        fetch(storeUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Produksi berhasil dibuat', 'success');
                closeCreateModal();
                if (state.dataTable) {
                    state.dataTable.ajax.reload();
                }
                loadStatistics();
            } else {
                showNotification(data.message || 'Gagal menyimpan produksi', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menyimpan', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan Produksi';
            }
        });
    }

    // Reload data
    function reloadData() {
        if (state.dataTable) {
            state.dataTable.ajax.reload();
        }
        loadMaterials();
        loadStatistics();
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Simple alert for now - can be enhanced with toast notifications
        if (type === 'error') {
            alert('Error: ' + message);
        } else if (type === 'success') {
            alert('Success: ' + message);
        } else {
            alert(message);
        }
    }

    // Initialize operational costs
    function initializeOperationalCosts() {
        state.operationalCostIndex = 1;
    }

    // Add operational cost row
    window.addOperationalCost = function() {
        const container = document.getElementById('operationalCosts');
        const index = state.operationalCostIndex++;
        
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 operational-cost-row';
        row.innerHTML = `
            <select name="operational_costs[${index}][cost_type]"
                    class="w-40 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Jenis</option>
                <option value="listrik">Listrik</option>
                <option value="air">Air</option>
                <option value="gas">Gas</option>
                <option value="bahan_bakar">Bahan Bakar</option>
                <option value="maintenance">Maintenance</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <input type="number" name="operational_costs[${index}][amount]" min="0" step="0.01"
                   class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Jumlah biaya" onchange="calculateHppPreview()" oninput="formatCurrencyInput(this)">
            <input type="text" name="operational_costs[${index}][description]"
                   class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Deskripsi (opsional)">
            <button type="button" onclick="removeOperationalCost(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                <i class='bx bx-trash'></i>
            </button>
        `;
        
        container.appendChild(row);
    };

    // Remove operational cost row
    window.removeOperationalCost = function(button) {
        const row = button.closest('.operational-cost-row');
        if (row) {
            row.remove();
            calculateHppPreview();
        }
    };

    // Calculate labor cost
    window.calculateLaborCost = function() {
        const workerCount = parseFloat(document.querySelector('input[name="labor_costs[worker_count]"]').value) || 0;
        const costPerWorker = parseFloat(document.querySelector('input[name="labor_costs[cost_per_worker]"]').value) || 0;
        const totalCost = workerCount * costPerWorker;
        
        document.getElementById('totalLaborCost').value = formatCurrency(totalCost);
        calculateHppPreview();
    };

    // Toggle attendance date section
    window.toggleAttendanceDate = function() {
        const checkbox = document.getElementById('fromAttendance');
        const section = document.getElementById('attendanceDateSection');
        
        if (checkbox.checked) {
            section.classList.remove('hidden');
        } else {
            section.classList.add('hidden');
            // Clear worker count when unchecked
            document.querySelector('input[name="labor_costs[worker_count]"]').value = '';
            calculateLaborCost();
        }
    };

    // Get attendance count
    window.getAttendanceCount = function() {
        const dateInput = document.getElementById('attendanceDate');
        const resultDiv = document.getElementById('attendanceResult');
        const workerCountInput = document.querySelector('input[name="labor_costs[worker_count]"]');
        
        if (!dateInput.value) {
            showNotification('Pilih tanggal terlebih dahulu', 'warning');
            return;
        }
        
        resultDiv.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Mengambil data absensi...';
        
        fetch(attendanceCountUrl + '?' + new URLSearchParams({
            date: dateInput.value,
            outlet_id: state.selectedOutlet
        }))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const count = data.data.count;
                resultDiv.innerHTML = `<i class="bx bx-check text-green-600"></i> Ditemukan ${count} karyawan hadir`;
                workerCountInput.value = count;
                calculateLaborCost();
            } else {
                resultDiv.innerHTML = `<i class="bx bx-x text-red-600"></i> ${data.message}`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<i class="bx bx-x text-red-600"></i> Gagal mengambil data absensi';
        });
    };

    // Calculate HPP preview
    window.calculateHppPreview = function() {
        const formData = new FormData(document.getElementById('productionForm'));
        
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
        
        if (!hppPreviewUrl) return;
        
        fetch(hppPreviewUrl, {
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
                const formatted = data.data.formatted;
                document.getElementById('previewMaterialCost').textContent = formatted.material_cost;
                document.getElementById('previewLaborCost').textContent = formatted.labor_cost;
                document.getElementById('previewOperationalCost').textContent = formatted.operational_cost;
                document.getElementById('previewTotalCost').textContent = formatted.total_cost;
                document.getElementById('previewHppPerUnit').textContent = formatted.hpp_per_unit;
            }
        })
        .catch(error => {
            console.error('Error calculating HPP preview:', error);
        });
    };

    // Format currency input
    window.formatCurrencyInput = function(input) {
        const value = parseFloat(input.value) || 0;
        const formatted = formatCurrency(value);
        
        // Update small text if exists
        const smallText = input.nextElementSibling;
        if (smallText && smallText.tagName === 'SMALL') {
            smallText.textContent = formatted;
        }
    };

    // Format currency helper
    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    // Add event listeners for real-time HPP calculation
    document.addEventListener('change', function(e) {
        if (e.target.name && (
            e.target.name.includes('materials[') ||
            e.target.name.includes('labor_costs[') ||
            e.target.name.includes('operational_costs[') ||
            e.target.name === 'target_quantity'
        )) {
            calculateHppPreview();
        }
    });

})();