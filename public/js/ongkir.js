/**
 * Service Ongkir JavaScript
 * Handles ongkir (shipping cost) management functionality
 */

// Global function for Alpine.js
window.ongkirCrud = function() {
    return {
        ongkirList: [],
        outlets: [],
        loading: false,
        saving: false,
        deleting: false,
        
        search: '',
        outletFilter: 'ALL',
        
        showForm: false,
        form: {
            id: null,
            id_outlet: '',
            daerah: '',
            harga: 0
        },
        errors: {},
        
        toDelete: null,
        
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init() {
            console.log('✅ Ongkir CRUD initialized');
            await Promise.all([
                this.fetchOutlets(),
                this.fetchData()
            ]);
        },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.search,
                    outlet_id: this.outletFilter !== 'ALL' ? this.outletFilter : ''
                });

                const response = await fetch(`${window.baseUrl}/admin/service/ongkir/data?${params}`);
                const result = await response.json();
                
                this.ongkirList = result.data.map(item => ({
                    id: item.id_ongkir,
                    id_outlet: item.id_outlet,
                    daerah: item.daerah,
                    harga: item.harga,
                    harga_formatted: new Intl.NumberFormat('id-ID').format(item.harga)
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
                    // Fallback if data format is unexpected
                    this.outlets = [
                        { id: '1', name: 'PBU' },
                        { id: '3', name: 'Dahana' }
                    ];
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

        openCreate() {
            this.form = {
                id: null,
                id_outlet: this.outletFilter !== 'ALL' ? this.outletFilter : (this.outlets[0]?.id || ''),
                daerah: '',
                harga: 0
            };
            this.errors = {};
            this.showForm = true;
        },

        async openEdit(item) {
            try {
                const response = await fetch(`${window.baseUrl}/admin/service/ongkir/${item.id}`);
                const data = await response.json();
                
                this.form = {
                    id: data.id_ongkir,
                    id_outlet: data.id_outlet,
                    daerah: data.daerah,
                    harga: data.harga
                };
                this.errors = {};
                this.showForm = true;
            } catch (error) {
                console.error('Error loading ongkir:', error);
                this.showToastMessage('Gagal memuat data', 'error');
            }
        },

        closeForm() {
            this.showForm = false;
            this.errors = {};
        },

        async submitForm() {
            this.saving = true;
            this.errors = {};

            try {
                const url = this.form.id 
                    ? `${window.baseUrl}/admin/service/ongkir/${this.form.id}`
                    : `${window.baseUrl}/admin/service/ongkir`;
                
                const method = this.form.id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
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
                const response = await fetch(`${window.baseUrl}/admin/service/ongkir/${this.toDelete.id}`, {
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
        }
    };
};

console.log('✅ ongkir.js loaded successfully');