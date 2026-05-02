<style>
    .badge {
        margin-left: 5px;
        padding: 5px 10px;
        border-radius: 50%;
        background-color: #dc3545; /* Warna merah */
        color: white;
    }
</style>

@extends('app')

@section('title')
    Kepegawaian & Rekrutmen
@endsection

@push('css')
    <style>
        .tab-content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .nav-tabs {
            border-bottom: 1px solid #ddd;
        }
        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-radius: 4px 4px 0 0;
            margin-right: 5px;
            padding: 10px 20px;
            color: #333;
            background-color: #f8f9fa;
        }
        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #ddd #ddd #fff;
            color: #007bff;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .btn-icon {
            padding: 5px 10px;
            margin: 2px;
        }
        .text-center.py-5 {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 40px;
        }
        .feather-lg {
            width: 48px;
            height: 48px;
        }
        .card.shadow-sm {
            border-radius: 12px;
        }
        .list-unstyled li {
            margin-bottom: 10px;
        }
        .card-body.text-center {
            padding: 40px;
        }
        .card.border-0.shadow-sm {
            transition: transform 0.2s;
        }
        .card.border-0.shadow-sm:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@section('content')
<link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <a href="{{ route('hrm.recruitment.create') }}" class="btn btn-primary pull-left">
                    <i data-feather="plus"></i> Tambah Rekrutmen
                </a>
            </div>
            <div class="box-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="recruitmentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="waiting-tab" data-toggle="tab" href="#waiting" role="tab" aria-controls="waiting" aria-selected="true">Menunggu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="accepted-tab" data-toggle="tab" href="#accepted" role="tab" aria-controls="accepted" aria-selected="false">Diterima</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">Ditolak</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="recruitmentTabsContent">
                    <!-- Tab Menunggu -->
                    <div class="tab-pane fade" id="waiting" role="tabpanel" aria-labelledby="waiting-tab">
                    @if($recruitments->where('status', 'menunggu')->isEmpty())
                        <div class="text-center py-5">
                            <i data-feather="inbox" class="feather-lg text-muted"></i>
                            <h5 class="mt-3">Tidak ada data rekrutmen yang menunggu.</h5>
                            <p class="text-muted">Silakan tambahkan data rekrutmen baru.</p>
                        </div>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Department</th>
                                    <th>Jobdesk</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recruitments->where('status', 'menunggu') as $recruitment)
                                <tr>
                                    <td>{{ $recruitment->name }}</td>
                                    <td>{{ $recruitment->position }}</td>
                                    <td>{{ $recruitment->department }}</td>
                                    <td>
                                        <ul>
                                            @if($recruitment->jobdesk)
                                                @foreach(json_decode($recruitment->jobdesk) as $job)
                                                    <li>{{ $job }}</li>
                                                @endforeach
                                            @else
                                                <li>Tidak ada jobdesk.</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ ucfirst($recruitment->status) }}</span>
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('hrm.recruitment.edit', $recruitment->id) }}" class="btn btn-warning btn-xs btn-flat" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="{{ route('hrm.recruitment.destroy', $recruitment->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs btn-flat" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>

                    <!-- Tab Diterima -->
                    <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
                    @if($recruitments->where('status', 'diterima')->isEmpty())
                        <div class="text-center py-5">
                            <i data-feather="check-circle" class="feather-lg text-success"></i>
                            <h5 class="mt-3">Tidak ada data rekrutmen yang diterima.</h5>
                            <p class="text-muted">Silakan periksa kembali data rekrutmen.</p>
                        </div>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Department</th>
                                    <th>Jobdesk</th>
                                    <th>Gaji Pokok</th>
                                    <th>Harga Per Jam</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recruitments->where('status', 'diterima') as $recruitment)
                                <tr>
                                    <td>{{ $recruitment->name }}</td>
                                    <td>{{ $recruitment->position }}</td>
                                    <td>{{ $recruitment->department }}</td>
                                    <td>
                                        <ul>
                                            @if($recruitment->jobdesk)
                                                @foreach(json_decode($recruitment->jobdesk) as $job)
                                                    <li>{{ $job }}</li>
                                                @endforeach
                                            @else
                                                <li>Tidak ada jobdesk.</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>{{ format_uang($recruitment->salary) }}</td>
                                    <td>{{ format_uang($recruitment->hourly_rate) }} / Jam</td>
                                    <td>
                                        <span class="badge badge-success">{{ ucfirst($recruitment->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('hrm.recruitment.edit', $recruitment->id) }}" class="btn btn-warning btn-xs btn-flat" title="Edit">
                                            <i data-feather="edit"></i>
                                        </a>
                                        <form action="{{ route('hrm.recruitment.destroy', $recruitment->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs btn-flat" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-info btn-xs btn-flat" title="Cetak Kontrak" onclick="openPrintContractModal({{ $recruitment->id }})">
                                            <i data-feather="printer"></i> Cetak Kontrak
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    </div>

                    <!-- Tab Ditolak -->
                    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    @if($recruitments->where('status', 'ditolak')->isEmpty())
                        <div class="text-center py-5">
                            <i data-feather="x-circle" class="feather-lg text-danger"></i>
                            <h5 class="mt-3">Tidak ada data rekrutmen yang ditolak.</h5>
                            <p class="text-muted">Silakan periksa kembali data rekrutmen.</p>
                        </div>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Department</th>
                                    <th>Jobdesk</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recruitments->where('status', 'ditolak') as $recruitment)
                                <tr>
                                    <td>{{ $recruitment->name }}</td>
                                    <td>{{ $recruitment->position }}</td>
                                    <td>{{ $recruitment->department }}</td>
                                    <td>
                                        <ul>
                                            @if($recruitment->jobdesk)
                                                @foreach(json_decode($recruitment->jobdesk) as $job)
                                                    <li>{{ $job }}</li>
                                                @endforeach
                                            @else
                                                <li>Tidak ada jobdesk.</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger">{{ ucfirst($recruitment->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('hrm.recruitment.edit', $recruitment->id) }}" class="btn btn-warning btn-xs btn-flat" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('hrm.recruitment.destroy', $recruitment->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs btn-flat" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- UI Animasi dan Informasi -->
<div class="row mt-4">
    <div class="col-md-12 mx-auto"> <!-- Pusatkan konten -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <!-- Animasi Lottie -->
                <lottie-player
                    src="https://assets10.lottiefiles.com/packages/lf20_vybwn7df.json"
                    background="transparent"
                    speed="1"
                    style="width: 200px; height: 200px; margin: 0 auto;"
                    loop
                    autoplay>
                </lottie-player>

                <!-- Judul dan Deskripsi -->
                <h4 class="mt-3">Selamat Datang di Manajemen Rekrutmen</h4>
                <p class="text-muted">
                    Gunakan tab di atas untuk melihat daftar rekrutmen berdasarkan status.
                </p>

                <!-- Petunjuk Penggunaan -->
                <div class="mt-4">
                    <h5 class="mb-3">Petunjuk Penggunaan:</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <i data-feather="edit" class="feather-lg text-primary mb-3"></i>
                                    <h6>Edit Data</h6>
                                    <p class="text-muted small">Klik tombol <strong>Edit</strong> untuk mengubah data rekrutmen.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <i data-feather="trash-2" class="feather-lg text-danger mb-3"></i>
                                    <h6>Hapus Data</h6>
                                    <p class="text-muted small">Klik tombol <strong>Hapus</strong> untuk menghapus data rekrutmen.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <i data-feather="printer" class="feather-lg text-info mb-3"></i>
                                    <h6>Cetak Kontrak</h6>
                                    <p class="text-muted small">Klik tombol <strong>Cetak Kontrak</strong> untuk mencetak kontrak kerja.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk memilih Pihak Pertama -->
<div class="modal fade" id="selectManagerModal" tabindex="-1" role="dialog" aria-labelledby="selectManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectManagerModalLabel">Pilih Pihak Pertama</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select id="manager-select" class="form-control">
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}">{{ $manager->name }} - {{ $manager->position }} ({{ $manager->department }})</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="printContract()">Cetak Kontrak</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<script>
    feather.replace();
    const printContractUrlTemplate = @json(route('hrm.recruitment.print-contract', ['id' => ':recruitmentId']) . '?manager_id=:managerId');
    // Fungsi untuk membuka modal cetak kontrak
    function openPrintContractModal(recruitmentId) {
        $('#selectManagerModal').modal('show');
        $('#selectManagerModal').data('recruitment-id', recruitmentId);
    }

    // Fungsi untuk mencetak kontrak
    function printContract() {
        const recruitmentId = $('#selectManagerModal').data('recruitment-id');
        const managerId = $('#manager-select').val();
        const finalUrl = printContractUrlTemplate
            .replace(':recruitmentId', recruitmentId)
            .replace(':managerId', managerId);

        window.location.href = finalUrl;
    }
</script>
@endpush
