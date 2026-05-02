/**
 * Service Mesin Customer JavaScript
 * Handles mesin customer management functionality
 */

// Global function for Alpine.js
window.mesinCrud = function() {
    return {
        mesinList: [],
        outlets: [],
        ongkirList: [],
        produkList: [],
        customerResults: [],
        searchTimeout: null,
        customerLocked: false, // Flag to prevent clearing selected customer
        loading: false,
        saving: false,
        deleting: false,
        
        search: '',
        outletFilter: '',
        
        showForm: false,
        form: {
            id: null,
            id_member: '',
            id_ongkir: '',
            produk: []
        },
        customerSearch: '',
        errors: {},
        
        toDelete: null,
        
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init() {
            console.log('✅ Mesin CRUD initialized');
            await this.fetchOutlets();
            
            // Set default outlet filter to first available outlet (remove "ALL" option)
            if (this.outlets.length > 0) {
                this.outletFilter = this.outlets[0].id;
                console.log('Default outlet set to:', this.outletFilter);
            }
            
            await Promise.all([
                this.fetchData(),
                this.fetchOngkir(),
                this.fetchProduk()
            ]);
        },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.search,
                    outlet_id: this.outletFilter || ''
                });

                const response = await fetch(`${window.baseUrl}/admin/service/mesin/data?${params}`);
                const result = await response.json();
                
                this.mesinList = result.data.map(item => ({
                    id: item.id,
                    kode_mesin: item.kode_mesin,
                    customer_name: item.customer_name || item.member_name || '-',
                    daerah: item.daerah || item.ongkir_daerah || '-',
                    ongkir_harga: item.ongkir_harga || 0,
                    produk: item.produk || []
                }));
            } catch (error) {
                console.error('Error fetching data:', error);
                this.showToastMessage('Gagal memuat data', 'error');
            } finally {
                this.loading = false;
            }
        },

        async fetchOutlets() {
            try {
                const response = await fetch(`${window.baseUrl}/admin/inventaris/bahan/outlets`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                
                const data = await response.json();
                console.log('Raw outlet data:', data); // Debug log
                
                // Handle different data formats
                if (Array.isArray(data)) {
                    // If data is already an array
                    this.outlets = data.map(item => ({
                        id: item.id || item.id_outlet,
                        name: item.name || item.nama || item.nama_outlet
                    }));
                } else if (typeof data === 'object' && data !== null) {
                    // If data is an object, convert to array
                    this.outlets = Object.entries(data).map(([id, name]) => ({
                        id: id,
                        name: typeof name === 'string' ? name : (name.name || name.nama || `Outlet ${id}`)
                    }));
                } else {
                    throw new Error('Unexpected data format');
                }
                
                console.log('Processed outlets:', this.outlets); // Debug log
            } catch (error) {
                console.error('Error fetching outlets:', error);
                // Fallback to default outlets if API fails
                this.outlets = [
                    { id: '1', name: 'PBU' },
                    { id: '3', name: 'Dahana' }
                ];
            }
        },

        async fetchOngkir() {
            try {
                let outletId = this.outletFilter || null;
                
                // Get outlet ID with fallback logic
                if (!outletId) {
                    if (window.OutletHelper && typeof window.OutletHelper.getFirstOutletId === 'function') {
                        outletId = window.OutletHelper.getFirstOutletId(this.outlets);
                    } else {
                        outletId = this.outlets[0]?.id || this.outlets[0]?.id_outlet || '1';
                    }
                }
                
                const response = await fetch(`${window.baseUrl}/admin/service/ongkir/data?outlet_id=${outletId}`);
                const result = await response.json();
                this.ongkirList = result.data.map(item => ({
                    id: item.id_ongkir,
                    daerah: item.daerah,
                    harga: new Intl.NumberFormat('id-ID').format(item.harga)
                }));
            } catch (error) {
                console.error('Error fetching ongkir:', error);
            }
        },

        async fetchProduk() {
            try {
                let outletId = this.outletFilter || null;
                
                // Get outlet ID with fallback logic
                if (!outletId) {
                    if (window.OutletHelper && typeof window.OutletHelper.getFirstOutletId === 'function') {
                        outletId = window.OutletHelper.getFirstOutletId(this.outlets);
                    } else {
                        outletId = this.outlets[0]?.id || this.outlets[0]?.id_outlet || '1';
                    }
                }
                
                const response = await fetch(`${window.baseUrl}/admin/service/mesin/produk/list?outlet_id=${outletId}`);
                const data = await response.json();
                if (data.success) {
                    this.produkList = data.data;
                }
            } catch (error) {
                console.error('Error fetching produk:', error);
            }
        },

        handleCustomerInput() {
            // If user is typing and changes the text, unlock customer selection
            if (this.customerLocked) {
                const currentMatch = this.customerResults.find(c => c.text === this.customerSearch);
                if (!currentMatch || currentMatch.id !== this.form.id_member) {
                    this.customerLocked = false;
                    console.log('Customer unlocked due to text change');
                }
            }
            
            // Clear customer ID when user types
            if (this.customerSearch.length < 1) {
                this.form.id_member = '';
                this.customerResults = [];
                this.customerLocked = false;
                return;
            }
            
            // Check if current input matches any existing customer
            this.checkCustomerSelection();
            
            // If customer is already selected and input matches, don't search again
            if (this.form.id_member && this.customerResults.length > 0 && this.customerLocked) {
                const currentMatch = this.customerResults.find(c => c.text === this.customerSearch);
                if (currentMatch && currentMatch.id === this.form.id_member) {
                    console.log('Customer already selected and locked, skipping search');
                    return;
                }
            }
            
            // Debounce search
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.searchCustomers();
            }, 300);
        },

        checkCustomerSelection() {
            // Check if the current customerSearch matches any customer in results
            if (this.customerResults.length === 0) {
                return; // No results to check against
            }
            
            const selected = this.customerResults.find(c => c.text === this.customerSearch);
            if (selected) {
                this.form.id_member = selected.id;
                this.customerLocked = true; // Lock the customer selection
                console.log('Customer auto-selected:', selected);
            } else {
                // Only clear ID if we have results but no match AND customer is not locked
                if (this.customerResults.length > 0 && !this.customerLocked) {
                    this.form.id_member = '';
                    console.log('Customer not found in current results, clearing ID');
                }
            }
        },

        async searchCustomers() {
            try {
                // Get current outlet ID for search
                let outletId = this.outletFilter || null;
                
                // Get outlet ID with fallback logic
                if (!outletId) {
                    if (window.OutletHelper && typeof window.OutletHelper.getFirstOutletId === 'function') {
                        outletId = window.OutletHelper.getFirstOutletId(this.outlets);
                    } else {
                        outletId = this.outlets[0]?.id || this.outlets[0]?.id_outlet || '1';
                    }
                }
                
                const params = new URLSearchParams({
                    q: this.customerSearch,
                    outlet_id: outletId
                });
                
                const response = await fetch(`${window.baseUrl}/admin/service/search-customers?${params}`);
                const data = await response.json();
                
                console.log('Customer search response:', data); // Debug log
                
                if (data.success && data.customers) {
                    // Transform customers to expected format for datalist
                    this.customerResults = data.customers.map(customer => ({
                        id: customer.id_member, // ✅ Fixed: Use id_member instead of id
                        text: `${customer.nama} - ${customer.telepon || 'No Phone'}`,
                        nama: customer.nama,
                        telepon: customer.telepon,
                        closing_type_prefix: customer.closing_type_prefix || 'JP'
                    }));
                    
                    // After updating results, check if current input matches any customer
                    this.checkCustomerSelection();
                } else {
                    // Only clear results and ID if customer is not locked
                    if (!this.customerLocked) {
                        this.customerResults = [];
                        this.form.id_member = '';
                        console.log('No customers found, clearing results');
                    } else {
                        console.log('No customers found, but customer is locked - keeping selection');
                    }
                }
            } catch (error) {
                console.error('Error searching customers:', error);
                if (!this.customerLocked) {
                    this.customerResults = [];
                    this.form.id_member = '';
                }
            }
        },

        selectCustomerFromList() {
            // When user selects from datalist, find and set the customer ID
            const selected = this.customerResults.find(c => c.text === this.customerSearch);
            if (selected) {
                this.form.id_member = selected.id;
                console.log('Customer selected:', selected); // Debug log
            } else {
                // If not found in results, clear the ID
                this.form.id_member = '';
                console.log('Customer not found in results, clearing ID'); // Debug log
            }
        },

        openCreate() {
            this.form = {
                id: null,
                id_member: '',
                id_ongkir: '',
                produk: [{
                    id_produk: '',
                    jumlah: 1,
                    biaya_service: 0,
                    closing_type: 'jual_putus'
                }]
            };
            this.customerSearch = '';
            this.customerResults = [];
            this.customerLocked = false; // Reset customer lock
            this.errors = {};
            this.showForm = true;
        },

        async openEdit(item) {
            try {
                const response = await fetch(`${window.baseUrl}/admin/service/mesin/${item.id}`);
                const data = await response.json();
                
                this.form = {
                    id: data.id,
                    id_member: data.id_member,
                    id_ongkir: data.id_ongkir,
                    produk: data.produk.map(p => ({
                        id_produk: p.id_produk,
                        jumlah: p.pivot.jumlah,
                        biaya_service: p.pivot.biaya_service,
                        closing_type: p.pivot.closing_type
                    }))
                };
                this.customerSearch = data.member.nama;
                this.errors = {};
                this.showForm = true;
            } catch (error) {
                console.error('Error loading mesin:', error);
                this.showToastMessage('Gagal memuat data', 'error');
            }
        },

        closeForm() {
            this.showForm = false;
            this.errors = {};
        },

        addProdukRow() {
            this.form.produk.push({
                id_produk: '',
                jumlah: 1,
                biaya_service: 0,
                closing_type: 'jual_putus'
            });
        },

        removeProdukRow(index) {
            if (this.form.produk.length > 1) {
                this.form.produk.splice(index, 1);
            } else {
                this.showToastMessage('Minimal harus ada 1 produk', 'error');
            }
        },

        async submitForm() {
            console.log('Submit form - Customer ID:', this.form.id_member);
            console.log('Submit form - Customer Search:', this.customerSearch);
            console.log('Submit form - Customer Results:', this.customerResults);
            
            // Validate customer selection
            if (!this.form.id_member || !this.customerSearch) {
                this.showToastMessage('Pilih customer terlebih dahulu dari daftar. ID: ' + this.form.id_member + ', Search: ' + this.customerSearch, 'error');
                return;
            }

            if (this.form.produk.length === 0) {
                this.showToastMessage('Minimal harus ada 1 produk', 'error');
                return;
            }

            this.saving = true;
            this.errors = {};

            try {
                const url = this.form.id 
                    ? `${window.baseUrl}/admin/service/mesin/${this.form.id}`
                    : `${window.baseUrl}/admin/service/mesin`;
                
                const method = this.form.id ? 'PUT' : 'POST';

                const formData = {
                    id_member: this.form.id_member,
                    id_ongkir: this.form.id_ongkir,
                    produk: this.form.produk.map(p => p.id_produk),
                    jumlah_produk: this.form.produk.map(p => p.jumlah),
                    biaya_service_produk: this.form.produk.map(p => p.biaya_service),
                    closing_type_produk: this.form.produk.map(p => p.closing_type)
                };

                console.log('Form data to submit:', formData);
                console.log('Form produk array:', this.form.produk);

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
                    this.closeForm();
                    await this.fetchData();
                } else {
                    if (result.errors) {
                        this.errors = result.errors;
                    }
                    this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Error saving data:', error);
                this.showToastMessage('Gagal menyimpan data', 'error');
            } finally {
                this.saving = false;
            }
        },

        confirmDelete(item) {
            this.toDelete = item;
        },

        async deleteNow() {
            if (!this.toDelete) return;
            
            this.deleting = true;
            try {
                const response = await fetch(`${window.baseUrl}/admin/service/mesin/${this.toDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
                    this.toDelete = null;
                    await this.fetchData();
                } else {
                    this.showToastMessage(result.message || 'Gagal menghapus data', 'error');
                }
            } catch (error) {
                console.error('Error deleting data:', error);
                this.showToastMessage('Gagal menghapus data', 'error');
            } finally {
                this.deleting = false;
            }
        },

        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },

        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
    };
};

console.log('✅ mesin.js loaded successfully');