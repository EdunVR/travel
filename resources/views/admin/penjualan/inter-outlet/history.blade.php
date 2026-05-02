<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Penjualan Antar Outlet</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Optimized for iframe modal */
        body {
            margin: 0;
            padding: 8px;
            font-family: system-ui, -apple-system, sans-serif;
        }
        
        /* DataTable height fix */
        .dataTables_scrollBody {
            height: 400px !important;
            overflow-y: auto !important;
        }
        
        .dataTables_wrapper {
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="bg-gradient-to-br from-slate-50 to-slate-100">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="text-lg font-bold text-slate-900">Riwayat Penjualan Antar Outlet</h1>
            <p class="text-slate-600 text-sm">Kelola dan pantau transaksi penjualan antar outlet</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-4">
            <div class="p-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Outlet</label>
                        <select id="outlet-filter" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="all">Semua Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $outletId ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
                        <select id="status-filter" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="all">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" id="start-date" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Akhir</label>
                        <input type="date" id="end-date" class="w-full px-2 py-1.5 text-sm border border-slate-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div class="flex items-center gap-2 mt-3">
                    <button onclick="applyFilters()" class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 transition-colors">
                        <i class="bx bx-search mr-1"></i>Filter
                    </button>
                    <button onclick="resetFilters()" class="px-3 py-1.5 text-sm bg-slate-600 text-white rounded hover:bg-slate-700 transition-colors">
                        <i class="bx bx-refresh mr-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="p-3">
                <table id="history-table" class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-2 px-2 font-medium text-slate-700">No</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">No. Transaksi</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Tanggal</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Outlet Asal</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Outlet Tujuan</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Total</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Status</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Items</th>
                            <th class="text-left py-2 px-2 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Modal -->
        <div id="detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Detail Transaksi</h2>
                    <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
                        <i class="bx bx-x text-2xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div id="detail-content">
                        <!-- Detail content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let historyTable;

        $(document).ready(function() {
            // Initialize DataTable with simple configuration
            historyTable = $('#history-table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: '400px',
                scrollCollapse: true,
                ajax: {
                    url: '{{ route('admin.penjualan.inter-outlet.history.data') }}',
                    data: function(d) {
                        d.outlet_id = $('#outlet-filter').val();
                        d.status = $('#status-filter').val();
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
                    { data: 'no_transaksi', name: 'no_transaksi', width: '120px' },
                    { data: 'tanggal_formatted', name: 'tanggal', width: '90px' },
                    { data: 'outlet_asal_name', name: 'outlet_asal_name', width: '100px' },
                    { data: 'outlet_tujuan_name', name: 'outlet_tujuan_name', width: '100px' },
                    { data: 'total_formatted', name: 'total', width: '90px' },
                    { data: 'status_badge', name: 'status', width: '70px' },
                    { data: 'items_count', name: 'items_count', orderable: false, width: '60px' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '100px' }
                ],
                order: [[2, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    processing: '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600"></div> Memuat...</div>'
                }
            });
        });

        function applyFilters() {
            historyTable.ajax.reload();
        }

        function resetFilters() {
            $('#outlet-filter').val('all');
            $('#status-filter').val('all');
            $('#start-date').val('');
            $('#end-date').val('');
            historyTable.ajax.reload();
        }

        function viewDetail(id) {
            fetch(`{{ route('admin.penjualan.inter-outlet.show', '') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showDetailModal(data.data);
                    } else {
                        alert('Gagal memuat detail transaksi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat detail');
                });
        }

        function showDetailModal(transaction) {
            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('detail-content');
            
            let itemsHtml = '';
            transaction.items.forEach(item => {
                itemsHtml += `
                    <tr class="border-b border-slate-100">
                        <td class="py-2">${item.produk ? item.produk.nama_produk : 'Produk tidak ditemukan'}</td>
                        <td class="py-2">${item.produk ? item.produk.kode_produk : '-'}</td>
                        <td class="py-2">${parseFloat(item.kuantitas)} ${item.produk ? item.produk.satuan?.nama_satuan || 'pcs' : 'pcs'}</td>
                        <td class="py-2">${formatCurrency(item.harga)}</td>
                        <td class="py-2">${formatCurrency(item.subtotal)}</td>
                    </tr>
                `;
            });

            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Transaksi</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">No. Transaksi:</span>
                                <span class="font-medium">${transaction.no_transaksi}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Tanggal:</span>
                                <span class="font-medium">${new Date(transaction.tanggal).toLocaleDateString('id-ID')}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Status:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium ${getStatusClass(transaction.status)}">${transaction.status.toUpperCase()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">User:</span>
                                <span class="font-medium">${transaction.user ? transaction.user.name : '-'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Outlet</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Outlet Asal:</span>
                                <span class="font-medium">${transaction.outlet_asal ? transaction.outlet_asal.nama_outlet : '-'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Outlet Tujuan:</span>
                                <span class="font-medium">${transaction.outlet_tujuan ? transaction.outlet_tujuan.nama_outlet : '-'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Detail Items</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left py-2 font-medium text-slate-700">Produk</th>
                                    <th class="text-left py-2 font-medium text-slate-700">SKU</th>
                                    <th class="text-left py-2 font-medium text-slate-700">Qty</th>
                                    <th class="text-left py-2 font-medium text-slate-700">Harga</th>
                                    <th class="text-left py-2 font-medium text-slate-700">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-slate-600">Subtotal:</span>
                        <span class="font-medium">${formatCurrency(transaction.subtotal)}</span>
                    </div>
                    ${transaction.total_diskon > 0 ? `
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-slate-600">Diskon:</span>
                            <span class="font-medium text-red-600">-${formatCurrency(transaction.total_diskon)}</span>
                        </div>
                    ` : ''}
                    ${transaction.ppn > 0 ? `
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-slate-600">PPN:</span>
                            <span class="font-medium">${formatCurrency(transaction.ppn)}</span>
                        </div>
                    ` : ''}
                    <div class="flex justify-between items-center text-lg font-semibold border-t border-slate-200 pt-2">
                        <span>Total:</span>
                        <span class="text-primary-600">${formatCurrency(transaction.total)}</span>
                    </div>
                </div>

                ${transaction.catatan ? `
                    <div class="mt-4 p-4 bg-slate-50 rounded-lg">
                        <h4 class="font-medium text-slate-900 mb-2">Catatan:</h4>
                        <p class="text-slate-600">${transaction.catatan}</p>
                    </div>
                ` : ''}
            `;
            
            modal.classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }

        function approveTransaction(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui transaksi ini?')) {
                fetch(`{{ route('admin.penjualan.inter-outlet.approve', '') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Transaksi berhasil disetujui');
                        historyTable.ajax.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyetujui transaksi');
                });
            }
        }

        function getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    </script>
</body>
</html>