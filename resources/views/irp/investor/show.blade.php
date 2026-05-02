@extends('app')

@section('title', 'Detail Investor')

@section('content')
<div class="container-fluid">
    <!-- Konten Investor -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Investor</h6>
            <div>
                <a href="{{ route('irp.investor.edit', $investor->id) }}" class="btn btn-sm btn-warning">
                    Edit
                </a>
                <a href="{{ route('irp.investor.index') }}" class="btn btn-sm btn-danger">
                    Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Konten detail investor -->
            <div class="row">
            <div class="col-md-3 text-center">
                @php
                    $defaultPhoto = $investor->jenis_kelamin === 'Perempuan'
                                    ? asset('img/investor_user_perempuan.png')
                                    : asset('img/investor_user.png');

                    $photo = $investor->photo 
                                ? asset('storage/' . $investor->photo) 
                                : $defaultPhoto;
                @endphp

                <img src="{{ $photo }}" alt="Foto Profil" class="img-thumbnail mb-3" style="max-width: 200px;">

                <h4>{{ $investor->name }}</h4>

                <span class="badge badge-{{ $investor->jenis_kelamin == 'Laki-laki' ? 'success' : 'secondary' }}">
                    {{ ucfirst($investor->jenis_kelamin) ?? 'Tidak Diketahui' }}
                </span>

                <span class="badge badge-{{ $investor->status == 'active' ? 'success' : 'secondary' }}">
                    {{ ucfirst($investor->status) }}
                </span>
            </div>


                <div class="col-md-9">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informasi Dasar</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th>Bergabung Pada</th>
                                    <td>{{ tanggal_indonesia($investor->join_date) }}</td>
                                </tr>
                                <tr>
                                    <th width="40%">Email</th>
                                    <td>{{ $investor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td>{{ $investor->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>
                                        <span class="badge badge-{{ $investor->category == 'internal' ? 'primary' : 'success' }}">
                                            {{ ucfirst($investor->category) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="40%">Bank</th>
                                    <td>{{ $investor->bank }}</td>
                                </tr>
                                <tr>
                                    <th width="40%">Rekening</th>
                                    <td>{{ $investor->rekening }}</td>
                                </tr>
                                
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Informasi Investasi</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Total Investasi</th>
                                    <td class="text-right">{{ format_uang($investor->total_investment) }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Rekening</th>
                                    <td class="text-right">{{ $investor->accounts->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Total Saldo Bagi Hasil</th>
                                    <td class="text-right">
                                        {{ format_uang($investor->accounts->sum('profit_balance') - $investor->accounts->sum('saldo_tertahan')) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Saldo Tertahan</th>
                                    <td class="text-right">
                                        {{ format_uang($investor->accounts->sum('saldo_tertahan')) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($investor->address)
                    <div class="row">
                        <div class="col-12">
                            <h5>Alamat</h5>
                            <p>{{ $investor->address }}</p>
                        </div>
                    </div>
                    @endif

                    @if($investor->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Catatan</h5>
                            <p>{{ $investor->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab informasi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'accounts' ? 'active' : '' }}" id="accounts-tab" data-toggle="tab" href="#accounts" role="tab"><i data-feather="credit-card" class="icon-sm"></i>Rekening Investasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'profit' ? 'active' : '' }}" id="profit-tab" data-toggle="tab" href="#profit" role="tab"><i data-feather="dollar-sign" class="icon-sm"></i>Bagi Hasil & Investasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'customers' ? 'active' : '' }}" id="customers-tab" data-toggle="tab" href="#customers" role="tab"><i data-feather="users" class="icon-sm"></i>Customer</a>
                </li>  
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'documents' ? 'active' : '' }}" id="documents-tab" data-toggle="tab" href="#documents" role="tab"><i data-feather="file-text" class="icon-sm"></i>Dokumen</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="investorTabsContent">
                <!-- Tab Rekening Investasi -->
                <div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
                    @include('irp.investor.partials.accounts', [
                        'accounts' => $accounts,
                        'investor' => $investor
                    ])
                </div>
                
                <!-- Tab Bagi Hasil -->
                <div class="tab-pane fade" id="profit" role="tabpanel" aria-labelledby="profit-tab">
                    @include('irp.investor.partials.profit_sharing', [
                        'accounts' => $accounts,
                        'investor' => $investor
                    ])
                </div>

                <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">
                    @include('irp.investor.partials.customers')
                </div>
                
                <!-- Tab Dokumen -->
                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                    @include('irp.investor.partials.documents')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Improve table appearance */
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
    }

    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
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
    /* Tambahkan di bagian style */
    .table td {
        vertical-align: middle !important; /* Pastikan konten sel selalu di tengah vertikal */
    }

    .action-buttons {
        display: flex;
        justify-content: center; /* Untuk rata tengah horizontal */
        align-items: center;    /* Untuk rata tengah vertikal */
        gap: 3px;
        min-width: 110px;       /* Lebar minimum kolom aksi */
    }

    .action-buttons .btn {
        flex: 1;                /* Membuat tombol melebar sama rata */
        max-width: 30px;        /* Lebar maksimum tombol */
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
        padding: 0.5rem !important; /* Padding sel tabel */
    }

    /* Khusus kolom aksi */
    .table td:last-child {
        padding: 0.3rem !important; /* Padding lebih kecil untuk kolom aksi */
    }
</style>

@push('scripts')
<script>
// Handle saat halaman pertama kali load
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();

    // Set tab aktif dari URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'accounts';
    
    // Aktifkan tab yang sesuai
    $(`.nav-tabs a[href="#${activeTab}"]`).tab('show');
    
    // Tangani perubahan tab
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
});
</script>
@endpush
@endsection
