@extends('settings.coa.index')

@section('coa-content')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-shopping-cart"></i> Setting Akun PO Penjualan
        </h3>
    </div>
    <div class="box-body">
        <form id="poPenjualanForm" action="{{ route('settings.coa.update-po-penjualan') }}" method="POST">
            @csrf
            
            <!-- Section: Buku Akuntansi -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Buku Akuntansi</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="accounting_book_id">Buku Akuntansi *</label>
                                        <select name="accounting_book_id" id="accounting_book_id" class="form-control select2" required>
                                            <option value="">Pilih Buku Akuntansi</option>
                                            @foreach($accountingBooks as $book)
                                                <option value="{{ $book->id }}" 
                                                    {{ $settings->accounting_book_id == $book->id ? 'selected' : '' }}>
                                                    {{ $book->name }} - {{ $book->currency }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Buku akuntansi yang akan digunakan untuk transaksi PO Penjualan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Akun untuk Status Menunggu -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fa fa-clock-o"></i> Akun untuk Status MENUNGGU
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akun_piutang_po">Akun Piutang PO *</label>
                                        <select name="akun_piutang_po" id="akun_piutang_po" class="form-control select2" required>
                                            <option value="">Pilih Akun Piutang</option>
                                            @php
                                                $assetAccounts = $allAccounts->where('type', 'asset')->where('is_active', true)->sortBy('code');
                                            @endphp
                                            @foreach($assetAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_piutang_po == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat piutang dari PO Penjualan (Tipe: Aset)</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="akun_uang_muka_po">Akun Uang Muka PO *</label>
                                        <select name="akun_uang_muka_po" id="akun_uang_muka_po" class="form-control select2" required>
                                            <option value="">Pilih Akun Uang Muka</option>
                                            @php
                                                $liabilityAccounts = $allAccounts->where('type', 'liability')->where('is_active', true)->sortBy('code');
                                            @endphp
                                            @foreach($liabilityAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_uang_muka_po == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat uang muka (Tipe: Kewajiban)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akun_pendapatan_diterima_dimuka">Akun Pendapatan Diterima Dimuka *</label>
                                        <select name="akun_pendapatan_diterima_dimuka" id="akun_pendapatan_diterima_dimuka" class="form-control select2" required>
                                            <option value="">Pilih Akun Pendapatan Diterima Dimuka</option>
                                            @foreach($liabilityAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_pendapatan_diterima_dimuka == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat pendapatan diterima dimuka (Tipe: Kewajiban)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Akun untuk Status Lunas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fa fa-check-circle"></i> Akun untuk Status LUNAS
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akun_pendapatan_po">Akun Pendapatan PO *</label>
                                        <select name="akun_pendapatan_po" id="akun_pendapatan_po" class="form-control select2" required>
                                            <option value="">Pilih Akun Pendapatan</option>
                                            @php
                                                $revenueAccounts = $allAccounts->where('type', 'revenue')->where('is_active', true)->sortBy('code');
                                            @endphp
                                            @foreach($revenueAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_pendapatan_po == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat pendapatan dari PO Penjualan (Tipe: Pendapatan)</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="akun_ongkir_po">Akun Pendapatan Ongkir *</label>
                                        <select name="akun_ongkir_po" id="akun_ongkir_po" class="form-control select2" required>
                                            <option value="">Pilih Akun Pendapatan Ongkir</option>
                                            @foreach($revenueAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_ongkir_po == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat pendapatan ongkos kirim (Tipe: Pendapatan)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akun_diskon_penjualan">Akun Diskon Penjualan *</label>
                                        <select name="akun_diskon_penjualan" id="akun_diskon_penjualan" class="form-control select2" required>
                                            <option value="">Pilih Akun Diskon Penjualan</option>
                                            @php
                                                $expenseAccounts = $allAccounts->where('type', 'expense')->where('is_active', true)->sortBy('code');
                                            @endphp
                                            @foreach($expenseAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_diskon_penjualan == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat diskon penjualan (Tipe: Beban)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Akun untuk HPP -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fa fa-cubes"></i> Akun untuk Harga Pokok Penjualan (HPP)
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akun_hpp_po">Akun HPP PO *</label>
                                        <select name="akun_hpp_po" id="akun_hpp_po" class="form-control select2" required>
                                            <option value="">Pilih Akun HPP</option>
                                            @foreach($expenseAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_hpp_po == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat Harga Pokok Penjualan (Tipe: Beban)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="akun_persediaan_po">Akun Persediaan PO *</label>
                                        <select name="akun_persediaan_po" id="akun_persediaan_po" class="form-control select2" required>
                                            <option value="">Pilih Akun Persediaan</option>
                                            @foreach($assetAccounts as $account)
                                                <option value="{{ $account['code'] }}" 
                                                    {{ $settings->akun_persediaan_po == $account['code'] ? 'selected' : '' }}
                                                    data-name="{{ $account['name'] }}">
                                                    {{ $account['code'] }} - {{ $account['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Akun untuk mencatat persediaan barang (Tipe: Aset)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>Informasi:</strong> 
                        <ul>
                            <li><strong>Status MENUNGGU:</strong> PO sudah dibuat tetapi belum lunas pembayarannya</li>
                            <li><strong>Status LUNAS:</strong> PO sudah dilunasi pembayarannya</li>
                            <li><strong>HPP:</strong> Dicatat saat status berubah menjadi LUNAS</li>
                            <li>Pastikan akun-akun yang dipilih sesuai dengan tipe yang direkomendasikan</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="button" id="validateBtn" class="btn btn-info">
                        <i class="fa fa-check"></i> Validasi Akun
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Setting
                    </button>
                    <a href="{{ route('settings.coa.index') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Current Settings Summary -->
@if($settings->akun_piutang_po)
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-info-circle"></i> Setting Saat Ini
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title">Buku Akuntansi</h4>
                    </div>
                    <div class="panel-body">
                        @if($settings->accountingBook)
                            <strong>{{ $settings->accountingBook->name }}</strong><br>
                            <small class="text-muted">{{ $settings->accountingBook->currency }} - {{ $settings->accountingBook->description }}</small>
                        @else
                            <span class="text-danger">Belum dipilih</span>
                        @endif
                    </div>
                </div>
                
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h4 class="panel-title">Status Menunggu</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td width="40%"><strong>Piutang PO:</strong></td>
                                <td>{{ $settings->akun_piutang_po }} - {{ $accountNames['akun_piutang_po'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Uang Muka:</strong></td>
                                <td>{{ $settings->akun_uang_muka_po }} - {{ $accountNames['akun_uang_muka_po'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pendapatan Diterima Dimuka:</strong></td>
                                <td>{{ $settings->akun_pendapatan_diterima_dimuka }} - {{ $accountNames['akun_pendapatan_diterima_dimuka'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">Status Lunas</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td width="40%"><strong>Pendapatan PO:</strong></td>
                                <td>{{ $settings->akun_pendapatan_po }} - {{ $accountNames['akun_pendapatan_po'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pendapatan Ongkir:</strong></td>
                                <td>{{ $settings->akun_ongkir_po }} - {{ $accountNames['akun_ongkir_po'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Diskon Penjualan:</strong></td>
                                <td>{{ $settings->akun_diskon_penjualan }} - {{ $accountNames['akun_diskon_penjualan'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">Harga Pokok Penjualan</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td width="40%"><strong>HPP PO:</strong></td>
                                <td>{{ $settings->akun_hpp_po }} - {{ $accountNames['akun_hpp_po'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Persediaan:</strong></td>
                                <td>{{ $settings->akun_persediaan_po }} - {{ $accountNames['akun_persediaan_po'] ?? 'Tidak ditemukan' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Preview Jurnal -->
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-eye"></i> Preview Entri Jurnal PO Penjualan
        </h3>
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tabMenunggu" data-toggle="tab">Status Menunggu</a></li>
                <li><a href="#tabLunas" data-toggle="tab">Status Lunas</a></li>
                <li><a href="#tabHpp" data-toggle="tab">Jurnal HPP</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tabMenunggu">
                    <div class="alert alert-warning">
                        <strong>Status Menunggu:</strong> PO dibuat dengan pembayaran uang muka Rp 500.000 dari total Rp 2.000.000
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>Akun</th>
                                    <th>Kode</th>
                                    <th>Tipe</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="previewJurnalMenunggu">
                                <tr>
                                    <td colspan="6" class="text-center">Pilih akun untuk melihat preview jurnal</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tabLunas">
                    <div class="alert alert-success">
                        <strong>Status Lunas:</strong> PO dilunasi dengan pembayaran penuh Rp 2.000.000
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>Akun</th>
                                    <th>Kode</th>
                                    <th>Tipe</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="previewJurnalLunas">
                                <tr>
                                    <td colspan="6" class="text-center">Pilih akun untuk melihat preview jurnal</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tabHpp">
                    <div class="alert alert-info">
                        <strong>Jurnal HPP:</strong> Dicatat saat status berubah menjadi LUNAS (HPP: Rp 1.200.000)
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>Akun</th>
                                    <th>Kode</th>
                                    <th>Tipe</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="previewJurnalHpp">
                                <tr>
                                    <td colspan="6" class="text-center">Pilih akun untuk melihat preview jurnal</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
.panel {
    margin-bottom: 20px;
}
.panel-heading {
    padding: 10px 15px;
}
.panel-body {
    padding: 15px;
}
.nav-tabs-custom {
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Pilih akun",
        allowClear: true
    });

    // Validasi akun sebelum submit
    $('#validateBtn').click(function() {
        const formData = {
            akun_piutang_po: $('#akun_piutang_po').val(),
            akun_pendapatan_po: $('#akun_pendapatan_po').val(),
            akun_hpp_po: $('#akun_hpp_po').val(),
            akun_uang_muka_po: $('#akun_uang_muka_po').val(),
            akun_pendapatan_diterima_dimuka: $('#akun_pendapatan_diterima_dimuka').val()
        };

        $.ajax({
            url: '{{ route("settings.coa.validate-accounts") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Validasi Berhasil',
                        text: response.message,
                        timer: 2000
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Terjadi kesalahan validasi:<br><ul>';
                    
                    errors.forEach(error => {
                        errorMessage += `<li>${error}</li>`;
                    });
                    
                    errorMessage += '</ul>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errorMessage
                    });
                }
            }
        });
    });

    // Update preview jurnal saat akun berubah
    $('.select2').change(function() {
        updateJurnalPreview();
    });

    function updateJurnalPreview() {
        updateJurnalMenunggu();
        updateJurnalLunas();
        updateJurnalHpp();
    }

    function updateJurnalMenunggu() {
        const piutang = $('#akun_piutang_po option:selected');
        const uangMuka = $('#akun_uang_muka_po option:selected');
        const pendapatanDimuka = $('#akun_pendapatan_diterima_dimuka option:selected');

        let html = '';

        if (piutang.val() && uangMuka.val() && pendapatanDimuka.val()) {
            html += `
                <tr>
                    <td>${piutang.text()}</td>
                    <td>${piutang.val()}</td>
                    <td><span class="label label-primary">Aset</span></td>
                    <td>1.500.000</td>
                    <td>0</td>
                    <td>Piutang PO</td>
                </tr>
                <tr>
                    <td>${uangMuka.text()}</td>
                    <td>${uangMuka.val()}</td>
                    <td><span class="label label-warning">Kewajiban</span></td>
                    <td>500.000</td>
                    <td>0</td>
                    <td>Uang muka diterima</td>
                </tr>
                <tr>
                    <td>${pendapatanDimuka.text()}</td>
                    <td>${pendapatanDimuka.val()}</td>
                    <td><span class="label label-warning">Kewajiban</span></td>
                    <td>0</td>
                    <td>500.000</td>
                    <td>Pendapatan diterima dimuka</td>
                </tr>
                <tr class="info">
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong>2.000.000</strong></td>
                    <td><strong>2.000.000</strong></td>
                    <td></td>
                </tr>
            `;
        }

        $('#previewJurnalMenunggu').html(html || '<tr><td colspan="6" class="text-center">Pilih akun untuk melihat preview jurnal</td></tr>');
    }

    function updateJurnalLunas() {
        const pendapatan = $('#akun_pendapatan_po option:selected');
        const ongkir = $('#akun_ongkir_po option:selected');
        const diskon = $('#akun_diskon_penjualan option:selected');
        const uangMuka = $('#akun_uang_muka_po option:selected');
        const pendapatanDimuka = $('#akun_pendapatan_diterima_dimuka option:selected');

        let html = '';

        if (pendapatan.val() && ongkir.val() && diskon.val() && uangMuka.val() && pendapatanDimuka.val()) {
            html += `
                <tr>
                    <td>Kas/Bank</td>
                    <td>1.01.01</td>
                    <td><span class="label label-primary">Aset</span></td>
                    <td>1.500.000</td>
                    <td>0</td>
                    <td>Pelunasan PO</td>
                </tr>
                <tr>
                    <td>${uangMuka.text()}</td>
                    <td>${uangMuka.val()}</td>
                    <td><span class="label label-warning">Kewajiban</span></td>
                    <td>0</td>
                    <td>500.000</td>
                    <td>Penyelesaian uang muka</td>
                </tr>
                <tr>
                    <td>${pendapatanDimuka.text()}</td>
                    <td>${pendapatanDimuka.val()}</td>
                    <td><span class="label label-warning">Kewajiban</span></td>
                    <td>500.000</td>
                    <td>0</td>
                    <td>Realisasi pendapatan dimuka</td>
                </tr>
                <tr>
                    <td>${pendapatan.text()}</td>
                    <td>${pendapatan.val()}</td>
                    <td><span class="label label-success">Pendapatan</span></td>
                    <td>0</td>
                    <td>1.200.000</td>
                    <td>Pendapatan PO</td>
                </tr>
                <tr>
                    <td>${ongkir.text()}</td>
                    <td>${ongkir.val()}</td>
                    <td><span class="label label-success">Pendapatan</span></td>
                    <td>0</td>
                    <td>300.000</td>
                    <td>Pendapatan ongkir</td>
                </tr>
                <tr>
                    <td>${diskon.text()}</td>
                    <td>${diskon.val()}</td>
                    <td><span class="label label-danger">Beban</span></td>
                    <td>100.000</td>
                    <td>0</td>
                    <td>Diskon penjualan</td>
                </tr>
                <tr class="info">
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong>2.100.000</strong></td>
                    <td><strong>2.100.000</strong></td>
                    <td></td>
                </tr>
            `;
        }

        $('#previewJurnalLunas').html(html || '<tr><td colspan="6" class="text-center">Pilih akun untuk melihat preview jurnal</td></tr>');
    }

    function updateJurnalHpp() {
        const hpp = $('#akun_hpp_po option:selected');
        const persediaan = $('#akun_persediaan_po option:selected');

        let html = '';

        if (hpp.val() && persediaan.val()) {
            html += `
                <tr>
                    <td>${hpp.text()}</td>
                    <td>${hpp.val()}</td>
                    <td><span class="label label-danger">Beban</span></td>
                    <td>1.200.000</td>
                    <td>0</td>
                    <td>HPP PO</td>
                </tr>
                <tr>
                    <td>${persediaan.text()}</td>
                    <td>${persediaan.val()}</td>
                    <td><span class="label label-primary">Aset</span></td>
                    <td>0</td>
                    <td>1.200.000</td>
                    <td>Persediaan keluar</td>
                </tr>
                <tr class="info">
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong>1.200.000</strong></td>
                    <td><strong>1.200.000</strong></td>
                    <td></td>
                </tr>
            `;
        }

        $('#previewJurnalHpp').html(html || '<tr><td colspan="6" class="text-center">Pilih akun untuk melihat preview jurnal</td></tr>');
    }

    // Initial preview
    updateJurnalPreview();
});
</script>
@endpush
