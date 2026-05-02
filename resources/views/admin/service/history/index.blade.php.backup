<x-layouts.admin title="Service / History Invoice">
    <div x-data="historyData()" x-init="init()" class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold">History Invoice Service</h1>
                <p class="text-slate-600 text-sm">Riwayat semua invoice service</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <select x-model="outletFilter" x-on:change="changeOutlet()" class="px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200">
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $selectedOutlet ? 'selected' : '' }}>
                            {{ $outlet->nama_outlet }}
                        </option>
                    @endforeach
                </select>
                <button x-on:click="exportData()" class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-4 py-2 hover:bg-green-700">
                    <i class='bx bx-export text-lg'></i> Export Excel
                </button>
                <a href="{{ route('admin.service.invoice.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                    <i class='bx bx-plus-circle text-lg'></i> Buat Invoice Baru
                </a>
            </div>
        </div>

        <!-- Status Tabs -->
        <div>
            <div class="flex gap-2 p-1 bg-slate-100 rounded-xl overflow-x-auto">
                <button x-on:click="changeTab('terkini')" :class="currentStatus === 'terkini' ? 'tab-active' : 'tab-inactive'" class="tab-button">
                    <i class='bx bx-list-ul'></i> Semua (<span x-text="counts.terkini">0</span>)
                </button>
                <button x-on:click="changeTab('menunggu')" :class="currentStatus === 'menunggu' ? 'tab-active' : 'tab-inactive'" class="tab-button">
                    <i class='bx bx-time'></i> Menunggu (<span x-text="counts.menunggu">0</span>)
                </button>
                <button x-on:click="changeTab('lunas')" :class="currentStatus === 'lunas' ? 'tab-active' : 'tab-inactive'" class="tab-button">
                    <i class='bx bx-check-circle'></i> Lunas (<span x-text="counts.lunas">0</span>)
                </button>
                <button x-on:click="changeTab('gagal')" :class="currentStatus === 'gagal' ? 'tab-active' : 'tab-inactive'" class="tab-button">
                    <i class='bx bx-x-circle'></i> Gagal (<span x-text="counts.gagal">0</span>)
                </button>
                <button x-on:click="changeTab('service-berikutnya')" :class="currentStatus === 'service-berikutnya' ? 'tab-active' : 'tab-inactive'" class="tab-button">
                    <i class='bx bx-calendar'></i> Service Berikutnya (<span x-text="counts.service_berikutnya">0</span>)
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-card">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <label class="block mb-2 text-sm text-slate-600">Tanggal Mulai</label>
                    <input type="date" x-model="startDate" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200">
                </div>
                <div>
                    <label class="block mb-2 text-sm text-slate-600">Tanggal Akhir</label>
                    <input type="date" x-model="endDate" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200">
                </div>
                <div class="flex items-end">
                    <button x-on:click="fetchData()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-white bg-primary-600 rounded-xl hover:bg-primary-700">
                        <i class='bx bx-filter-alt'></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <div class="inline-flex items-center gap-2 text-slate-600">
                <i class='bx bx-loader-alt bx-spin text-xl'></i>
                <span>Memuat data...</span>
            </div>
        </div>

        <!-- Data Table -->
        <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left w-12">No</th>
                            <th class="px-4 py-3 text-left">No Invoice</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-left">Jenis Service</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Sisa Waktu</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(invoice, index) in invoices" :key="invoice.id">
                            <tr class="border-t border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3" x-text="index + 1"></td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-primary-600" x-text="invoice.no_invoice"></span>
                                </td>
                                <td class="px-4 py-3" x-text="invoice.tanggal_formatted"></td>
                                <td class="px-4 py-3" x-text="invoice.customer_name"></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs" x-text="invoice.jenis_service"></span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium" x-text="invoice.total_formatted"></td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="{
                                        'bg-yellow-50 text-yellow-700 border-yellow-200': invoice.status === 'menunggu',
                                        'bg-green-50 text-green-700 border-green-200': invoice.status === 'lunas',
                                        'bg-red-50 text-red-700 border-red-200': invoice.status === 'gagal'
                                    }" class="px-2 py-1 rounded-lg border text-xs font-medium" x-text="invoice.status.toUpperCase()"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span x-show="invoice.sisa_hari !== null" :class="{
                                        'text-red-600': invoice.sisa_hari < 0,
                                        'text-yellow-600': invoice.sisa_hari >= 0 && invoice.sisa_hari <= 3,
                                        'text-green-600': invoice.sisa_hari > 3
                                    }" class="text-xs font-medium" x-text="invoice.sisa_hari_text"></span>
                                    <span x-show="invoice.sisa_hari === null" class="text-xs text-slate-400">-</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button x-on:click="viewPdf(invoice.id)" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50" title="Lihat PDF">
                                            <i class='bx bx-printer text-lg'></i>
                                        </button>
                                        <button x-show="invoice.status === 'menunggu'" x-on:click="updateStatus(invoice.id, 'lunas')" class="p-1.5 rounded-lg border border-green-200 text-green-700 hover:bg-green-50" title="Tandai Lunas">
                                            <i class='bx bx-check text-lg'></i>
                                        </button>
                                        <button x-show="invoice.status === 'menunggu'" x-on:click="updateStatus(invoice.id, 'gagal')" class="p-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50" title="Tandai Gagal">
                                            <i class='bx bx-x text-lg'></i>
                                        </button>
                                        <button x-on:click="confirmDelete(invoice)" class="p-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50" title="Hapus">
                                            <i class='bx bx-trash text-lg'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="invoices.length === 0">
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada data invoice</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal PDF -->
        <div x-show="showPdfModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40" x-on:click="showPdfModal = false"></div>
            <div class="relative z-50 w-full max-w-6xl h-[90vh] bg-white rounded-2xl shadow-float overflow-hidden flex flex-col">
                <div class="flex items-center justify-between p-4 border-b border-slate-200">
                    <h3 class="font-semibold">Preview Invoice</h3>
                    <button x-on:click="showPdfModal = false" class="p-2 hover:bg-slate-100 rounded-lg">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>
                <iframe :src="pdfUrl" class="w-full flex-1"></iframe>
            </div>
        </div>

        <!-- Modal Update Status -->
        <div x-show="showStatusModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40" x-on:click="showStatusModal = false"></div>
            <div class="relative z-50 w-full max-w-md bg-white rounded-2xl shadow-float overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-lg font-semibold">Update Status Invoice</h3>
                </div>
                
                <div class="px-5 py-4">
                    <div x-show="statusForm.status === 'lunas'" class="space-y-3">
                        <div>
                            <label class="block mb-2 text-sm text-slate-600">Jenis Pembayaran *</label>
                            <select x-model="statusForm.jenis_pembayaran" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm text-slate-600">Penerima *</label>
                            <input type="text" x-model="statusForm.penerima" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200">
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm text-slate-600">Tanggal Pembayaran *</label>
                            <input type="datetime-local" x-model="statusForm.tanggal_pembayaran" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200">
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm text-slate-600">Catatan Pembayaran</label>
                            <textarea x-model="statusForm.catatan_pembayaran" rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-200"></textarea>
                        </div>
                    </div>
                    
                    <div x-show="statusForm.status === 'gagal'">
                        <p class="text-sm text-slate-600">Apakah Anda yakin ingin menandai invoice ini sebagai gagal?</p>
                    </div>
                </div>
                
                <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button x-on:click="showStatusModal = false" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">
                        Batal
                    </button>
                    <button x-on:click="submitStatus()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="saving" class="inline-flex items-center gap-2">
                            <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
                        </span>
                        <span x-show="!saving">Simpan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Delete -->
        <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-on:click.outside="toDelete = null" class="relative z-50 w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden">
                <div class="px-5 py-4">
                    <div class="font-semibold">Hapus Invoice?</div>
                    <p class="text-slate-600 mt-1">Data akan dihapus secara permanen dari database.</p>
                    <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-medium" x-text="toDelete?.no_invoice"></div>
                        <div class="text-xs text-slate-500 mt-1" x-text="'Customer: ' + (toDelete?.customer_name || '-')"></div>
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button x-on:click="toDelete = null" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
                    <button x-on:click="deleteNow()" :disabled="deleting" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
                        <span x-show="deleting" class="inline-flex items-center gap-2">
                            <i class='bx bx-loader-alt bx-spin'></i> Menghapus...
                        </span>
                        <span x-show="!deleting">Hapus</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
            <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" 
                 class="px-4 py-3 rounded-xl border shadow-lg max-w-sm">
                <div class="flex items-center gap-2">
                    <i :class="toastType === 'success' ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'"></i>
                    <span x-text="toastMessage"></span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function historyData() {
            return {
                invoices: [],
                loading: false,
                saving: false,
                deleting: false,
                
                currentStatus: '{{ $status }}',
                outletFilter: {{ $selectedOutlet }},
                startDate: '',
                endDate: '',
                
                counts: {
                    terkini: 0,
                    menunggu: 0,
                    lunas: 0,
                    gagal: 0,
                    service_berikutnya: 0
                },
                
                showPdfModal: false,
                pdfUrl: '',
                
                showStatusModal: false,
                statusForm: {
                    invoice_id: null,
                    status: '',
                    jenis_pembayaran: 'cash',
                    penerima: '',
                    tanggal_pembayaran: '',
                    catatan_pembayaran: ''
                },
                
                toDelete: null,
                
                showToast: false,
                toastMessage: '',
                toastType: 'success',

                async init() {
                    await Promise.all([
                        this.fetchData(),
                        this.loadStatusCounts()
                    ]);
                },

                async fetchData() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            outlet_id: this.outletFilter,
                            status: this.currentStatus,
                            start_date: this.startDate || '',
                            end_date: this.endDate || ''
                        });

                        const response = await fetch(`{{ route('admin.service.history.data') }}?${params}`);
                        const result = await response.json();
                        
                        this.invoices = result.data.map(item => ({
                            id: item.id_service_invoice,
                            no_invoice: item.no_invoice,
                            tanggal_formatted: item.tanggal_formatted,
                            customer_name: item.customer_display || item.member?.nama || '-',
                            jenis_service: item.jenis_service,
                            total_formatted: item.total_formatted,
                            status: item.status,
                            sisa_hari: item.sisa_hari,
                            sisa_hari_text: item.sisa_hari_text || '-'
                        }));
                    } catch (error) {
                        console.error('Error fetching data:', error);
                        this.showToastMessage('Gagal memuat data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async loadStatusCounts() {
                    try {
                        const response = await fetch(`{{ route('admin.service.status-counts') }}?outlet_id=${this.outletFilter}`);
                        const data = await response.json();
                        
                        this.counts.menunggu = data.menunggu || 0;
                        this.counts.lunas = data.lunas || 0;
                        this.counts.gagal = data.gagal || 0;
                        this.counts.service_berikutnya = data.service_berikutnya || 0;
                        this.counts.terkini = (data.menunggu || 0) + (data.lunas || 0) + (data.gagal || 0);
                    } catch (error) {
                        console.error('Error loading counts:', error);
                    }
                },

                changeTab(status) {
                    this.currentStatus = status;
                    this.fetchData();
                },

                changeOutlet() {
                    window.location.href = `{{ route('admin.service.history.index') }}?outlet_id=${this.outletFilter}&status=${this.currentStatus}`;
                },

                viewPdf(invoiceId) {
                    this.pdfUrl = `{{ url('/admin/service/invoice') }}/${invoiceId}/print`;
                    this.showPdfModal = true;
                },

                updateStatus(invoiceId, status) {
                    this.statusForm = {
                        invoice_id: invoiceId,
                        status: status,
                        jenis_pembayaran: 'cash',
                        penerima: '',
                        tanggal_pembayaran: new Date().toISOString().slice(0, 16),
                        catatan_pembayaran: ''
                    };
                    this.showStatusModal = true;
                },

                async submitStatus() {
                    this.saving = true;
                    try {
                        const response = await fetch(`{{ url('/admin/service/invoice/status') }}/${this.statusForm.invoice_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.statusForm)
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToastMessage('Status berhasil diupdate!', 'success');
                            this.showStatusModal = false;
                            await Promise.all([
                                this.fetchData(),
                                this.loadStatusCounts()
                            ]);
                        } else {
                            this.showToastMessage(result.message || 'Gagal update status', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showToastMessage('Terjadi kesalahan', 'error');
                    } finally {
                        this.saving = false;
                    }
                },

                confirmDelete(invoice) {
                    this.toDelete = invoice;
                },

                async deleteNow() {
                    if (!this.toDelete) return;
                    
                    this.deleting = true;
                    try {
                        const response = await fetch(`{{ url('/admin/service/invoice') }}/${this.toDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToastMessage('Invoice berhasil dihapus!', 'success');
                            this.toDelete = null;
                            await Promise.all([
                                this.fetchData(),
                                this.loadStatusCounts()
                            ]);
                        } else {
                            this.showToastMessage(result.message || 'Gagal menghapus invoice', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showToastMessage('Terjadi kesalahan', 'error');
                    } finally {
                        this.deleting = false;
                    }
                },

                exportData() {
                    const params = new URLSearchParams({
                        outlet_id: this.outletFilter,
                        status: this.currentStatus,
                        start_date: this.startDate || '',
                        end_date: this.endDate || ''
                    });
                    
                    window.location.href = `{{ route('admin.service.history.export') }}?${params}`;
                },

                showToastMessage(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                }
            }
        }
    </script>
    @endpush

    @push('styles')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .tab-button {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tab-active {
            background-color: white;
            color: #2563EB;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .tab-inactive {
            background-color: transparent;
            color: #64748b;
        }
        .tab-button:hover {
            background-color: #f1f5f9;
        }
    </style>
    @endpush
</x-layouts.admin>
