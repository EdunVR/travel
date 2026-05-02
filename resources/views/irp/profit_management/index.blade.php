<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Tab styling */
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs .nav-item {
        margin: 0 5px;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.25rem 0.25rem 0 0;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #4e73df;
        background-color: rgba(78, 115, 223, 0.1);
        border-bottom: 3px solid #4e73df;
    }
    
    .nav-tabs .nav-link.active {
        color: #4e73df;
        background-color: transparent;
        border-bottom: 3px solid #4e73df;
    }

    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }

    /* Group card styling */
    .group-card, .card-placeholder {
        height: 30%;
        min-height: 50px; /* Atur tinggi minimal yang sama */
        transition: all 0.3s ease;
    }

    .card-placeholder {
        border: 2px dashed #ccc;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        background-color: #f8f9fa; /* Tambahkan background */
    }

    .card-placeholder:hover {
        border-color: #4e73df;
        background-color: #e9ecef;
    }

    .card-placeholder i {
        color: #6c757d;
        transition: all 0.3s;
    }

    .card-placeholder:hover i {
        color: #4e73df;
        transform: scale(1.1);
    }
    .group-card {
        border-radius: 25px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
        margin-bottom: 50px;
    }
    .group-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .group-card-header {
        border-top-left-radius: 25px !important;
        border-top-right-radius: 25px !important;
        padding: 15px 20px;
    }
    .group-card-body {
        padding: 20px;
    }

    .group-card-header .badge {
        font-size: 1.5rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .group-card-header .feather {
        vertical-align: middle;
        margin-bottom: 2px;
    }

    /* Table styling */
    .table {
        font-size: 1.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .table thead th {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .table tbody td {
        vertical-align: middle;
    }
    
    /* Small screens adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table thead th {
            white-space: nowrap;
        }
    }
    
    /* Tooltip styling */
    .tooltip-inner {
        max-width: 300px;
        white-space: pre-line;
        text-align: left;
    }

    /* Smooth transition for tab content */
    .tab-pane {
        transition: opacity 0.3s ease;
    }

    /* Ukuran tombol dan ikon yang lebih kecil */
    .btn-xs {
        padding: 0.15rem 0.25rem;
        font-size: 0.75rem;
        line-height: 1;
        border-radius: 0.2rem;
    }

    .icon-xs {
        width: 12px;
        height: 12px;
        stroke-width: 2.5px;
    }

    /* Padding minimal untuk tombol */
    .btn.p-1 {
        padding: 0.25rem !important;
    }

    /* Jarak antar tombol */
    .d-flex[style*="gap"] {
        gap: 3px !important;
    }

    /* Tambahan untuk kolom aksi */
    .table td {
        vertical-align: middle !important;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 3px;
        min-width: 110px;
    }

    .action-buttons .btn {
        flex: 1;
        max-width: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0.15rem 0.25rem;
    }
    .action-buttons form {
        display: flex;
        flex: 1;
        max-width: 30px;
    }

    .action-buttons .btn {
        width: 100%;
    }

    .table th, .table td {
        padding: 0.5rem !important;
    }

    /* Khusus kolom aksi */
    .table td:last-child {
        padding: 0.3rem !important;
    }

    /* Tambahkan ke file CSS Anda */
    .investment-amount {
        text-align: right;
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    #remainingQuota.text-danger {
        font-weight: bold;
        color: #dc3545 !important;
    }

    .group-actions {
        margin-top: auto;
        padding-top: 15px;
    }
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
    }
    .badge-status {
        font-size: 0.75rem;
        padding: 0.25em 0.4em;
        font-weight: 500;
        min-width: 80px;
        display: inline-block;
        text-align: center;
    }

    .account-status {
        vertical-align: middle;
        min-width: 120px;
    }

    /* Add these styles to your existing CSS */
    .btn-xs {
        padding: 0.15rem 0.25rem;
        font-size: 0.8rem;
        line-height: 1;
        border-radius: 0.2rem;
    }

    .icon-xs {
        width: 20px;
        height: 20px;
        stroke-width: 2.5px;
    }

    .action-buttons {
        min-width: 120px;  /* Adjust this value as needed */
    }

    /* Make form buttons inline */
    .form-cancel-payment, .form-delete-history {
        display: inline-block;
        margin: 0;
    }

    /* Adjust button spacing */
    .d-flex.gap-2 {
        gap: 0.5rem !important;
    }
</style>

@extends('app')

@section('title', 'Manajemen Bagi Hasil')

@section('content')
<div class="container-fluid">
    <!-- Tab informasi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'group' ? 'active' : '' }}" 
                       href="{{ route('irp.profit-management.index', ['tab' => 'group']) }}">
                       <i data-feather="users" class="icon-sm"></i> Bagi Hasil Kelompok
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'history' ? 'active' : '' }}" 
                       href="{{ route('irp.profit-management.index', ['tab' => 'history']) }}">
                       <i data-feather="clock" class="icon-sm"></i> History Bagi Hasil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'category' ? 'active' : '' }}" 
                       href="{{ route('irp.profit-management.index', ['tab' => 'category']) }}">
                       <i data-feather="grid" class="icon-sm"></i> Bagi Hasil Kategori
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @if($activeTab === 'category')
                <!-- Konten Tab Kategori -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Daftar Pembagian Keuntungan Berdasarkan Kategori</h5>
                    <a href="{{ route('irp.profit-management.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Tambah Pembagian
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="profitTable">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Total Keuntungan</th>
                            <th>Tanggal Pembagian</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Bukti Transfer</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profits as $profit)
                        <tr>
                            <td>{{ $profit->period }}</td>
                            <td class="text-right">{{ format_uang($profit->total_profit) }}</td>
                            <td>{{ $profit->distribution_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $profit->category ? ucfirst($profit->category) : 'Semua Kategori' }}
                            </td>
                            <td>
                                @if($profit->status == 'paid')
                                    <span class="badge badge-success">Sudah Dibayar</span>
                                @elseif($profit->status == 'processed')
                                    <span class="badge badge-warning">Diproses</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($profit->proof_file)
                                    <a href="{{ asset('storage/'.$profit->proof_file) }}" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Lihat
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('irp.profit-management.show', $profit->id) }}" 
                                class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            
            @elseif($activeTab === 'group')
            <div class="row">
                <!-- Loop untuk kelompok yang sudah ada -->
                @foreach($groups as $group)
                <div class="col-md-4 mb-4">
                    <div class="card group-card h-100">
                        <div class="card-header group-card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $group->name }}</h5>
                            <div class="d-flex gap-1">
                                <span class="badge badge-light text-dark" title="Jumlah Investor">
                                    <i data-feather="user" width="15" height="15" class="mr-1"></i>
                                    Jumlah Investor: {{ $group->unique_investors_count }}
                                </span>
                                <span class="badge badge-light text-dark" title="Jumlah Rekening Valid">
                                    <i data-feather="credit-card" width="15" height="15" class="mr-1"></i>
                                    Jumlah Rekening: {{ $group->valid_accounts_count }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body group-card-body">
                            <p>{{ $group->description ?? 'Tidak ada deskripsi' }}</p>
                            @if($group->product)
                                <p><strong>Produk:</strong> {{ $group->product->nama_produk }}</p>
                            @endif
                            @if($group->total_quota)
                                <p><strong>Total Kuota:</strong> {{ format_uang($group->total_quota) }}</p>
                            @endif
                            <p><strong>Total Investasi:</strong> {{ format_uang($group->total_investment_fix) }}</p>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-info btn-detail" 
                                        data-id="{{ $group->id }}">
                                    <i data-feather="eye" class="icon-sm"></i> Detail
                                </button>
                                <button class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $group->id }}">
                                    <i data-feather="edit" class="icon-sm"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete"
                                        data-id="{{ $group->id }}"
                                        data-name="{{ $group->name }}">
                                    <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Placeholder untuk buat baru -->
                <div class="col-md-4 mb-4">
                    <div class="card-placeholder h-100 d-flex align-items-center justify-content-center" 
                        data-toggle="modal" data-target="#createGroupModal">
                        <i data-feather="plus" class="icon-lg"></i>
                    </div>
                </div>
            </div>
            @elseif($activeTab === 'history')
            <!-- Konten Tab History Bagi Hasil Kelompok -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Riwayat Pembagian Keuntungan Kelompok</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="refreshHistoryBtn">
                        <i data-feather="refresh-cw" class="icon-sm"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="historyTable">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Kelompok</th>
                            <th>Periode</th>
                            <th class="text-right">Total Keuntungan</th>
                            <th class="text-right">Investor</th>
                            <th class="text-right">Rekening</th>
                            <th>Tanggal Distribusi</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $index => $history)
                        <tr>
                            <td>{{ $index + $histories->firstItem() }}</td>
                            <td>
                                @if($history->group)
                                    <div class="font-weight-bold">{{ $history->group->name }}</div>
                                    @if($history->group->product)
                                    <small class="text-muted">{{ $history->group->product->nama_produk }}</small>
                                    @endif
                                @else
                                    <span class="text-danger">Kelompok telah dihapus</span>
                                @endif
                            </td>
                            <td>{{ $history->period }}</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($history->total_profit, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $history->distributions->unique('investor_id')->count() }}</td>
                            <td class="text-right">{{ $history->distributions->whereNotNull('account_id')->count() }}</td>
                            <td>{{ $history->distribution_date->format('d/m/Y') }}</td>
                            <td>
                                @if($history->status == 'paid')
                                    <span class="badge badge-success">Dibayar</span>
                                @elseif($history->status == 'processed')
                                    <span class="badge badge-warning">Diproses</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($history->proof_file)
                                    <a href="{{ asset('storage/'.$history->proof_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="file-text" class="icon-sm"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-start gap-2">  <!-- Changed justify-content-center to justify-content-start -->
                                    @if($history->group)
                                    <a href="{{ route('irp.profit-management.show-group-history', $history->id) }}" 
                                    class="btn btn-xs btn-info" title="Detail">  <!-- Changed btn-sm to btn-xs -->
                                        <i data-feather="eye" class="icon-xs"></i>  <!-- Changed icon-sm to icon-xs -->
                                    </a>
                                    @endif
                                    @if(in_array($history->status, ['processed', 'paid']))
                                    <form class="form-cancel-payment d-inline" action="{{ route('irp.profit-management.cancel-payment-group', $history->id) }}" 
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Batalkan">  <!-- Changed btn-sm to btn-xs -->
                                            <i data-feather="x" class="icon-xs"></i>  <!-- Changed icon-sm to icon-xs -->
                                        </button>
                                    </form>
                                    @endif
                                    @if($history->status !== 'paid')
                                    <form class="form-delete-history d-inline" action="{{ route('irp.profit-management.delete-history', $history->id) }}" 
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Hapus">  <!-- Changed btn-sm to btn-xs -->
                                            <i data-feather="trash-2" class="icon-xs"></i>  <!-- Changed icon-sm to icon-xs -->
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    @if($histories->hasPages())
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $histories->firstItem() }} sampai {{ $histories->lastItem() }} dari {{ $histories->total() }} entri
                        </div>
                        <nav>
                            {{ $histories->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Create Group -->
<div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="createGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="createGroupForm" action="{{ route('irp.profit-management.store-group') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">Tambah Kelompok Bagi Hasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label>Nama Kelompok*</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Pilih Produk (Opsional)</label>
                        <select name="product_id" class="form-control" id="productSelect">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id_produk }}" data-price="{{ $product->harga_jual }}">
                                    {{ $product->nama_produk }} ({{ format_uang($product->harga_jual) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Kuota</label>
                        <input type="text" name="total_quota" class="form-control" id="totalQuota">
                    </div>

                    <div class="form-group">
                        <label>Tambahkan Investor</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="investorTable">
                                <thead>
                                    <tr>
                                        <th>Investor</th>
                                        <th>Rekening</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="investorTableBody">
                                    <!-- Rows akan ditambahkan via JS -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <button type="button" class="btn btn-sm btn-primary" id="addInvestorBtn">
                                                <i class="fas fa-plus"></i> Tambah Investor
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="alert alert-info mt-3">
                                <strong>Sisa Kuota:</strong> 
                                <span id="remainingQuota">Rp 0</span> / 
                                <span id="totalQuotaDisplay">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@includeIf('irp.profit_management.edit_group_modal')
@endsection

@push('scripts')
    <script>
        window.baseUrl = @json(url('/'));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

const baseUrl = window.baseUrl;

$(document).ready(function() {
    feather.replace();

    $('.investment-amount').each(function() {
        const rawValue = $(this).attr('data-raw-value') || 0;
        $(this).val(formatRupiah(rawValue));
    });

    // Inisialisasi tab
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const tabName = $(e.target).attr('href').substring(1);
        updateUrlParameter('tab', tabName);
    });

    // Fungsi untuk update URL tanpa reload
    function updateUrlParameter(key, value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set(key, value);
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        window.history.replaceState({}, '', newUrl);
    }

    // Set tab aktif saat pertama kali load
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'category';
    $(`.nav-tabs a[href="#${activeTab}"]`).tab('show');

    // Auto fill total quota
    $('#productSelect').change(function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        if (price) {
            $('#totalQuota').val(formatRupiah(price));
            calculateRemainingQuota();
        }
    });

    const availableInvestors = @json($investors->map(function($investor) {
        return [
            'id' => $investor->id,
            'name' => $investor->name
        ];
    }));

    // Template untuk row investor
    const investorRowTemplate = `
        <tr class="investor-row">
            <td>
                <select class="form-control investor-select-ajax" name="investor_id[]" required style="width: 100%;">
                    <option value="">-- Pilih Investor --</option>
                </select>
            </td>
            <td>
                <select class="form-control account-select" name="account_id[]" required>
                    <option value="">-- Pilih Rekening --</option>
                </select>
            </td>
            <td class="account-status">
                <span class="badge badge-secondary">Belum dipilih</span>
            </td>
            <td>
                <input type="text" class="form-control investment-amount" name="amount[]" readonly data-raw-value="0">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-investor-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

    // Tambahkan row investor baru
    $('#addInvestorBtn').click(function() {
        const newRow = $(investorRowTemplate);
        $('#investorTableBody').append(newRow);
        
        // Inisialisasi Select2 segera setelah row ditambahkan
        initInvestorSelect(newRow.find('.investor-select-ajax'));
        
        // Fokuskan ke input Select2
        newRow.find('.investor-select-ajax').select2('open');
    });

    // Fungsi untuk mengambil data rekening via AJAX
    function getInvestorAccounts(investorId, row, selectedAccountId = null) {
        if (!investorId) {
            row.find('.account-select').empty().append('<option value="">-- Pilih Rekening --</option>').prop('disabled', true);
            row.find('.account-status').html('<span class="badge badge-secondary">Belum dipilih</span>');
            return;
        }

        $.ajax({
            url: `${baseUrl}/api/investor/${investorId}/accounts`,
            method: 'GET',
            data: { page: 1, limit: 20 }, // Menambahkan nilai default untuk page
            dataType: 'json',
            beforeSend: function() {
                row.find('.account-select').empty().append('<option value="">Memuat...</option>');
                row.find('.account-status').html('<span class="badge badge-info">Memuat...</span>');
            },
            success: function(accounts) {
                const accountSelect = row.find('.account-select');
                accountSelect.empty().append('<option value="">-- Pilih Rekening --</option>');
                
                if (accounts.length > 0) {
                    accountSelect.prop('disabled', false);
                    accounts.forEach(account => {
                        const option = new Option(
                            `${account.account_number} (${account.bank_name})`,
                            account.id,
                            false,
                            account.id == selectedAccountId
                        );
                        option.dataset.investment = account.total_investment;
                        option.dataset.status = account.status;
                        accountSelect.append(option);
                    });

                    if (selectedAccountId) {
                        const selectedAccount = accounts.find(a => a.id == selectedAccountId);
                        if (selectedAccount) {
                            updateAccountStatus(row, selectedAccount.status);
                        }
                    } else {
                        updateAccountStatus(row, 'available');
                    }
                } else {
                    accountSelect.append('<option value="">Tidak ada rekening</option>');
                    updateAccountStatus(row, 'no-account');
                }
            },
            error: function(xhr) {
                console.error('Error loading accounts:', xhr.responseText);
                row.find('.account-select').empty().append('<option value="">Error memuat data</option>');
            }
        });
    }

    function updateAccountStatus(row, status) {
        let badgeClass, statusText;
        console.log(status);
        
        switch(status) {
            case 'active':
                badgeClass = 'badge-success';
                statusText = 'Aktif';
                break;
            default:
                badgeClass = 'badge-danger';
                statusText = 'Nonaktif';
        }
        
        row.find('.account-status').html(`<span class="badge ${badgeClass}">${statusText}</span>`);
    }

    // Handle perubahan select investor
    $(document).on('change', '.investor-select-ajax', function() {
        const row = $(this).closest('tr');
        const investorId = $(this).val();
        getInvestorAccounts(investorId, row);
        
        // Set nama investor jika sudah dipilih sebelumnya
        if (investorId) {
            const selectedOption = $(this).find('option:selected');
            const investorName = selectedOption.text();
            row.find('.investor-name').text(investorName);
        }
    });

    // Handle perubahan select rekening
    $(document).on('change', '.account-select', function() {
        const row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const investment = selectedOption.data('investment') || 0;
        const amountInput = row.find('.investment-amount');
        const status = selectedOption.data('status') || 'no-account';

        row.find('.investment-amount')
            .val(formatRupiah(investment))
            .attr('data-raw-value', investment);
        
        row.find('.account-investment').text(formatRupiah(investment));

        updateAccountStatus(row, status);
        calculateRemainingQuota();
    });

    // Fungsi format mata uang (perbaikan untuk handle desimal)
    function formatRupiah(angka) {
        if (!angka) return '0';
        
        // Konversi ke number dan hilangkan desimal jika ada
        const numberValue = typeof angka === 'string' ? 
            parseFloat(angka.replace(/\./g, '').replace(',', '.')) : 
            Number(angka);
        
        // Format dengan titik sebagai pemisah ribuan tanpa desimal
        return Math.floor(numberValue).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Fungsi untuk mengembalikan ke format angka yang bisa dihitung
    function unformatRupiah(rupiah) {
        return parseInt(rupiah.toString().replace(/\./g, "")) || 0;
    }

    // Hapus row investor
    $(document).on('click', '.remove-investor-btn', function() {
        $(this).closest('tr').remove();
        calculateRemainingQuota();
    });

    $('#createGroupForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(form[0]);
        const investors = [];
        
        // Kumpulkan data investor
        $('.investor-row').each(function(index) {
            const $row = $(this);
            investors.push({
                investor_id: $row.find('.investor-select').val(),
                account_id: $row.find('.account-select').val(),
                amount: $row.find('.investment-amount').val()
            });
        });
        
        // Tambahkan investors ke FormData
        formData.append('investors', JSON.stringify(investors));
        
        // Debug data sebelum dikirim
        console.log('Data yang dikirim:', {
            formData: Object.fromEntries(formData),
            investors: investors
        });
        
        // Submit via AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    $('#formErrors').html(response.errors ? response.errors.join('<br>') : 'Terjadi kesalahan').removeClass('d-none');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON) {
                    errorMsg = xhr.responseJSON.message || 
                            (Array.isArray(xhr.responseJSON.errors) ? xhr.responseJSON.errors.join('<br>') : errorMsg);
                }
                $('#formErrors').html(errorMsg).removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.btn-detail', function() {
        const groupId = $(this).data('id');
        window.location.href = `${baseUrl}/irp/profit-management/groups/${groupId}`;
    });

    $(document).on('click', '.btn-edit', function() {
        const groupId = $(this).data('id');
        
        // Kosongkan modal sebelum diisi
        $('#editGroupModal').modal('show');
        $('#editFormErrors').addClass('d-none').empty();
        $('#editInvestorTableBody').empty();
        
        // Ambil data group dari server
        $.get(`${baseUrl}/irp/profit-management/groups/${groupId}/edit`, function(data) {
            // Isi form dengan data group
            $('#groupName').val(data.group.name);
            $('#groupDescription').val(data.group.description);
            $('#groupProduct').val(data.group.product_id);
            const priceWithoutDecimal = Math.floor(data.group.total_quota);
            $('#groupTotalQuota').val(formatRupiah(priceWithoutDecimal || 0));
            
            // Tambahkan total investasi di samping sisa kuota
            const totalInvestment = data.total_investment || 0;
            $('.remaining-quota').html(`
                Sisa Kuota: <span id="editRemainingQuota">${formatRupiah(data.group.total_quota)}</span>
                (Total Investasi: ${formatRupiah(totalInvestment)})
            `);
            
            // Isi tabel investor
            data.group.investors.forEach(investor => {
                addInvestorRowToEditTable({
                    investor_id: investor.investor_id,
                    investor_name: investor.investor.name,
                    account_id: investor.account_id,
                    amount: Math.floor(investor.real_investment || investor.investment_amount) // Fallback ke nilai lama jika tidak ada real investment
                });
            });

            $('#editTotalInvestment').text(formatRupiah(data.total_investment || 0));
            
            // Hitung ulang sisa kuota
            calculateEditRemainingQuota();
            
            // Set form action
            $('#editGroupForm').attr('action', `${baseUrl}/irp/profit-management/groups/${groupId}`);
        }).fail(function(xhr) {
            console.error('Error loading group data:', xhr.responseText);
            $('#editFormErrors').removeClass('d-none').text('Gagal memuat data kelompok');
        });
    });

    function addInvestorRowToEditTable(data) {
        const row = $(`
            <tr class="investor-row" data-investor-id="${data.investor_id}">
                <td>
                    <select class="form-control investor-select-ajax" name="investor_id[]" required style="width: 100%;">
                        ${data.investor_id ? `<option value="${data.investor_id}" selected>${data.investor_name}</option>` : ''}
                    </select>
                </td>
                <td>
                    <select class="form-control account-select" name="account_id[]" required>
                        <option value="">-- Memuat rekening... --</option>
                    </select>
                </td>
                <td class="account-status">
                    <span class="badge badge-info">Memuat...</span>
                </td>
                <td>
                    <input type="text" class="form-control investment-amount" 
                        name="amount[]" value="${formatRupiah(data.amount)}" 
                        data-raw-value="${Math.floor(data.amount)}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-investor-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`);

        $('#editInvestorTableBody').append(row);

        // Inisialisasi Select2
        const selectElement = row.find('.investor-select-ajax');
        initInvestorSelect(selectElement);

        if (data.investor_id) {
            selectElement.val(data.investor_id).trigger('change');
        }

        // Ambil data rekening dan status
        getInvestorAccounts(data.investor_id, row, data.account_id);

        return row;
    }

    $('#addEditInvestorBtn').click(function() {
        const newRow = addInvestorRowToEditTable({
            investor_id: null,
            investor_name: '',
            account_id: null,
            amount: 0
        });
        
        // Fokuskan ke input Select2
        newRow.find('.investor-select-ajax').select2('open');
    });

    // Handle tombol hapus dengan SweetAlert
    $(document).on('click', '.btn-delete', function() {
        const groupId = $(this).data('id');
        const groupName = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Kelompok?',
            text: `Anda yakin ingin menghapus "${groupName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/irp/profit-management/groups/${groupId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Terhapus!',
                                `Kelompok "${groupName}" telah dihapus.`,
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message || 'Gagal menghapus kelompok',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus',
                            'error'
                        );
                    }
                });
            }
        });
    });

    function calculateRemainingQuota() {
        let totalInvested = 0;
        $('.investment-amount').each(function() {
            const amount = parseFloat($(this).val().replace(/\./g, '')) || 0;
            totalInvested += amount;
        });
        
        const totalQuota = parseFloat($('#totalQuota').val().replace(/\./g, '')) || 0;
        const remaining = totalQuota - totalInvested;
        
        $('#remainingQuota').text(formatRupiah(remaining));
        $('#totalQuotaDisplay').text(formatRupiah(totalQuota));
        
        // Warn jika kuota habis
        $('#remainingQuota').toggleClass('text-danger', remaining < 0);
    }

    // Fungsi calculateEditRemainingQuota
    function calculateEditRemainingQuota() {
        let totalInvested = 0;
        $('#editInvestorTableBody .investment-amount').each(function() {
            const amount = unformatRupiah($(this).val());
            totalInvested += amount;
            $(this).attr('data-raw-value', amount);
        });
        
        const totalQuota = unformatRupiah($('#groupTotalQuota').val());
        const remaining = totalQuota - totalInvested;
        
        $('#editRemainingQuota').text(formatRupiah(remaining));
        $('#editTotalQuotaDisplay').text(formatRupiah(totalQuota));
        
        // Warn jika kuota habis
        $('#editRemainingQuota').toggleClass('text-danger', remaining < 0);
    }

    // Panggil saat ada perubahan
    $('#totalQuota, #investorTableBody').on('change keyup', '.investment-amount', calculateRemainingQuota);

    // Format input jumlah otomatis
    $(document).on('focusout', '.investment-amount', function() {
        const value = parseFloat($(this).val()) || 0;
        $(this).val(value.toLocaleString('id-ID'));
    });

    $(document).on('blur', '.investment-amount', function() {
        const value = parseFloat($(this).val().replace(/\./g, '')) || 0;
        $(this).val(value.toLocaleString('id-ID'));
    });

    $('#editGroupForm').on('submit', function(e) {
        e.preventDefault();
    
        const form = $(this);
        const formData = new FormData(form[0]);
        
        // Pastikan amount tanpa desimal
        $('.investment-amount').each(function(index) {
            const rawValue = $(this).attr('data-raw-value');
            formData.set(`amount[${index}]`, rawValue);
        });
        
        formData.append('_method', 'PUT');
        // Kirim data via AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        $('#editGroupModal').modal('hide');
                        location.reload();
                    }
                } else {
                    $('#editFormErrors').removeClass('d-none').html(
                        response.errors ? response.errors.join('<br>') : 'Terjadi kesalahan'
                    );
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON) {
                    errorMsg = xhr.responseJSON.message || 
                            (Array.isArray(xhr.responseJSON.errors) ? xhr.responseJSON.errors.join('<br>') : errorMsg);
                }
                $('#editFormErrors').removeClass('d-none').html(errorMsg);
            }
        });
    });

    // Handle perubahan select investor di modal edit
    $(document).on('change', '#editInvestorTableBody .investor-select', function() {
        const row = $(this).closest('tr');
        const investorId = $(this).val();
        getInvestorAccounts(investorId, row);
    });

    // Handle perubahan select rekening di modal edit
    $(document).on('change', '#editInvestorTableBody .account-select', function() {
        const row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        const investment = selectedOption.data('investment') || 0;
        const amountInput = row.find('.investment-amount');

        amountInput
            .val(formatRupiah(investment))
            .attr('data-raw-value', investment);
        
        calculateEditRemainingQuota();
    });

    // Hapus row investor di modal edit
    $(document).on('click', '#editInvestorTableBody .remove-investor-btn', function() {
        $(this).closest('tr').remove();
        calculateEditRemainingQuota();
    });

    // Auto fill total quota di modal edit
    $('#groupProduct').change(function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        console.log(price);
        if (price) {
            const priceWithoutDecimal = Math.floor(price);
            $('#groupTotalQuota').val(formatRupiah(priceWithoutDecimal));
            $('#groupTotalQuota').attr('data-raw-value', priceWithoutDecimal);
            calculateEditRemainingQuota();
        } else {
            $('#groupTotalQuota').val('0');
            $('#groupTotalQuota').attr('data-raw-value', 0);
            calculateEditRemainingQuota();
        }
    });

    // Hitung sisa kuota saat ada perubahan di modal edit
    $('#groupTotalQuota, #editInvestorTableBody').on('change keyup', '.investment-amount', calculateEditRemainingQuota);

    function initInvestorSelect(element) {
        const $element = $(element);
        
        // Hancurkan Select2 jika sudah diinisialisasi sebelumnya
        if ($element.hasClass('select2-hidden-accessible')) {
            $element.select2('destroy');
        }
        
        $element.select2({
            placeholder: "Cari Investor...",
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: `${baseUrl}/api/investors/search`,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.map(investor => ({
                            id: investor.id,
                            text: investor.name
                        })),
                        pagination: {
                            more: data.current_page < data.last_page
                        }
                    };
                },
                cache: true
            }
        });

        // Untuk kasus edit, jika sudah ada nilai yang dipilih
        if ($element.find('option[selected]').length > 0) {
            const selectedOption = $element.find('option[selected]');
            $element.val(selectedOption.val()).trigger('change');
        }
    }

    $('#createGroupModal').on('shown.bs.modal', function() {
        handleTotalQuotaInput('#totalQuota');
        $('#investorTableBody .investor-select-ajax').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                initInvestorSelect(this);
            }
        });
    });

    // Inisialisasi untuk modal edit
    $('#editGroupModal').on('shown.bs.modal', function() {
        $(this).find('.investor-select-ajax').each(function() {
            initInvestorSelect(this);
        });
    });

    $('#createGroupModal, #editGroupModal').on('hidden.bs.modal', function() {
        $(this).find('.investor-select-ajax').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    });

    // Saat menambahkan row baru, inisialisasi Select2
    $(document).on('shown', '.investor-select-ajax', function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            initInvestorSelect(this);
        }
    });

    // Handler khusus untuk form batal
    $(document).on('submit', 'form.form-cancel-payment', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(form[0]);
        
        Swal.fire({
            title: 'Konfirmasi Pembatalan',
            text: 'Anda yakin ingin membatalkan pembagian keuntungan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Dibatalkan!',
                                'Pembagian keuntungan telah dibatalkan.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message || 'Gagal membatalkan pembagian',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Terjadi kesalahan saat membatalkan',
                            'error'
                        );
                    }
                });
            }
        });
    });
    
    // Handler for delete history form
    $(document).on('submit', 'form.form-delete-history', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(form[0]);
        
        Swal.fire({
            title: 'Hapus History?',
            text: 'Anda yakin ingin menghapus history pembagian ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Terhapus!',
                                'History pembagian telah dihapus.',
                                'success'
                            ).then(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message || 'Gagal menghapus history',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Fix pagination links to maintain the active tab
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const tab = new URLSearchParams(window.location.search).get('tab') || 'history';
        const newUrl = url.includes('?') ? 
            `${url}&tab=${tab}` : 
            `${url}?tab=${tab}`;
        
        window.location.href = newUrl;
    });

    // Refresh button functionality
    $('#refreshHistoryBtn').click(function() {
        window.location.reload();
    });

    function formatNumberInput(input) {
        // Hapus semua karakter non-digit
        let value = input.val().replace(/[^0-9]/g, '');
        
        // Konversi ke number
        const numValue = parseInt(value) || 0;
        
        // Format dengan separator ribuan
        const formattedValue = formatRupiah(numValue);
        
        // Update nilai input
        input.val(formattedValue);
        input.attr('data-raw-value', numValue);
        
        return numValue;
    }

    // Fungsi untuk handle input total quota
    function handleTotalQuotaInput(inputElement, isEditModal = false) {
        const input = $(inputElement);
        
        // Format saat kehilangan fokus
        input.on('blur', function() {
            formatNumberInput(input);
            if (isEditModal) {
                calculateEditRemainingQuota();
            } else {
                calculateRemainingQuota();
            }
        });
        
        // Format saat mengetik (real-time)
        input.on('keyup', function(e) {
            // Format hanya jika bukan tombol navigasi
            if ([37, 38, 39, 40, 8, 46, 13].indexOf(e.keyCode) === -1) {
                formatNumberInput(input);
                if (isEditModal) {
                    calculateEditRemainingQuota();
                } else {
                    calculateRemainingQuota();
                }
            }
        });
    }
});
</script>
@endpush
