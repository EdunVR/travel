@extends('app')

@section('title') Detail RAB Template @endsection

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item"><a href="{{ route('rab_template.index') }}">RAB Template</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box shadow-sm rounded">
            <div class="box-header with-border bg-light p-3 border-bottom">
                <h3 class="box-title mb-0 d-flex align-items-center">
                    <i data-feather="file-text" class="mr-2"></i> Detail RAB Template
                </h3>
            </div>
            <div class="box-body p-4">
                <div class="card mb-4 border-0 bg-light">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 col-lg-3">
                                <label class="font-weight-bold text-muted mb-2 mb-md-0">Nama Template</label>
                            </div>
                            <div class="col-md-8 col-lg-9">
                                <p class="mb-0">{{ $template->nama_template }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 col-lg-3">
                                <label class="font-weight-bold text-muted mb-2 mb-md-0">Deskripsi</label>
                            </div>
                            <div class="col-md-8 col-lg-9">
                                <p class="mb-0">{{ $template->deskripsi }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 col-lg-3">
                                <label class="font-weight-bold text-muted mb-2 mb-md-0">Status</label>
                            </div>
                            <div class="col-md-8 col-lg-9">
                                <span class="badge badge-{{ $template->status == 'Draft' ? 'secondary' : 
                                    ($template->status == 'Disetujui Semua' ? 'success' : 
                                    ($template->status == 'Disetujui Sebagian' ? 'primary' : 
                                    ($template->status == 'Disetujui dengan Revisi' ? 'warning' : 
                                    ($template->status == 'Ditransfer' ? 'info' : 'danger')))) }} py-2 px-3">
                                    <i data-feather="{{ $template->status == 'Draft' ? 'edit-3' : 
                                        ($template->status == 'Disetujui Semua' ? 'check-circle' : 
                                        ($template->status == 'Disetujui Sebagian' ? 'check' : 
                                        ($template->status == 'Disetujui dengan Revisi' ? 'alert-circle' : 
                                        ($template->status == 'Ditransfer' ? 'arrow-right-circle' : 'x-circle')))) }}" 
                                        class="feather-small mr-1"></i>
                                    {{ $template->status }}
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <h6 class="text-muted font-weight-bold mb-2">
                                            <i data-feather="dollar-sign" class="feather-small mr-1"></i> Total Budget
                                        </h6>
                                        <h4 class="mb-0">Rp {{ number_format($template->total_budget, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <h6 class="text-muted font-weight-bold mb-2">
                                            <i data-feather="check-square" class="feather-small mr-1"></i> Total Disetujui
                                        </h6>
                                        <h4 class="mb-0">Rp {{ number_format($template->total_nilai_disetujui, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <h6 class="text-muted font-weight-bold mb-2">
                                            <i data-feather="activity" class="feather-small mr-1"></i> Total Realisasi
                                        </h6>
                                        <h4 class="mb-0">Rp {{ number_format($template->total_realisasi, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('rab_template.update_approval', $template->id_rab) }}" method="POST" enctype="multipart/form-data" id="approval-form">
                    @csrf
                    <div class="form-group mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 font-weight-bold">
                                <i data-feather="list" class="mr-2"></i> Komponen Biaya
                            </h5>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" id="check-all" class="custom-control-input">
                                <label class="custom-control-label" for="check-all">Setujui Semua</label>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="20%">Komponen Biaya</th>
                                        <th width="3%" class="text-center">Jumlah</th>
                                        <th width="8%" class="text-center">Satuan</th>
                                        <th width="12%" class="text-right">Harga Satuan</th>
                                        <th width="12%" class="text-right">Budget</th>
                                        <th width="12%">Nilai Disetujui</th>
                                        <th width="20%">Realisasi Pemakaian</th>
                                        <th width="8%" class="text-center">Disetujui?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($template->details as $detail)
                                    <tr class="rab-item">
                                        <td>
                                            <strong>{{ $detail->nama_komponen }}</strong>
                                            @if($detail->deskripsi)
                                                <p class="text-muted mb-0 small">{{ $detail->deskripsi }}</p>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $detail->jumlah }}</td>
                                        <td class="text-center">{{ $detail->satuan }}</td>
                                        <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($detail->budget, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" 
                                                    name="items[{{ $detail->id_rab_detail }}][nilai_disetujui]" 
                                                    class="form-control nilai-disetujui" 
                                                    value="{{ $detail->nilai_disetujui ? number_format($detail->nilai_disetujui, 0, ',', '.') : '' }}">
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Progress bar -->
                                            <div class="progress mb-2 position-relative" style="height: 20px;">
                                                @php
                                                    $approvedValue = $detail->nilai_disetujui ?? 0;
                                                    $percentage = $approvedValue > 0 ? ($detail->realisasi_pemakaian / $approvedValue) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-success d-flex align-items-center justify-content-center"
                                                    role="progressbar"
                                                    style="width: {{ $percentage }}%;"
                                                    aria-valuenow="{{ $percentage }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    <small class="text-white fw-bold">{{ number_format($percentage, 2) }}%</small>
                                                </div>
                                            </div>

                                            <span class="me-2 fw-semibold">Terpakai: Rp</span>
                                            <span class="me-2 fw-semibold realisasi-text">{{ number_format($detail->realisasi_pemakaian, 0, ',', '.') }}</span>
                                            <span class="me-2 fw-bold"> / </span>
                                            <span class="text-muted sisa-text">Sisa: Rp {{ number_format(($detail->nilai_disetujui ?? 0) - $detail->realisasi_pemakaian, 0, ',', '.') }}</span>

                                            <div class="form-row-action d-flex align-items-center mt-2">
                                                <!-- Tombol Tambah dan History -->
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm btn-add-realisasi me-1" 
                                                        data-detail-id="{{ $detail->id_rab_detail }}">
                                                    <i data-feather="plus" class="feather-xsmall"></i>
                                                </button>

                                                <button type="button" 
                                                        class="btn btn-info btn-sm btn-history-realisasi" 
                                                        data-detail-id="{{ $detail->id_rab_detail }}">
                                                    <i data-feather="clock" class="feather-xsmall"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="items[{{ $detail->id_rab_detail }}][disetujui]" 
                                                    class="custom-control-input approve-check" 
                                                    id="check-{{ $detail->id_rab_detail }}"
                                                    value="1"
                                                    {{ $detail->disetujui ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="check-{{ $detail->id_rab_detail }}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach                                
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <th colspan="4" class="text-right">Total:</th>
                                        <th class="text-right">Rp {{ number_format($template->total_budget, 0, ',', '.') }}</th>
                                        <th class="text-right" id="total-disetujui">Rp {{ number_format($template->total_nilai_disetujui, 0, ',', '.') }}</th>
                                        <th class="text-right" id="total-realisasi">Rp {{ number_format($template->total_realisasi, 0, ',', '.') }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Bukti transfer section... -->
                    <div class="card mb-4 border shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">
                                <i data-feather="credit-card" class="mr-2"></i> Bukti Transfer
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                @if($template->details->whereNotNull('bukti_transfer')->count() > 0)
                                    <div class="mb-3">
                                        <label class="font-weight-bold">Bukti Transfer Saat Ini:</label>
                                        @foreach($template->details->whereNotNull('bukti_transfer') as $detail)
                                            @if($loop->first)
                                            <div class="mt-2 text-center bg-light p-3 rounded">
                                                @php
                                                    $filePath = 'bukti_transfer/'.basename($detail->bukti_transfer);
                                                    $fullPath = storage_path('app/public/'.$filePath);
                                                @endphp
                                                
                                                @if(file_exists($fullPath))
                                                    @if(in_array(pathinfo($detail->bukti_transfer, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ asset('storage/'.$filePath) }}" 
                                                            class="img-fluid mb-2" style="max-height: 200px;">
                                                    @else
                                                        <div class="file-preview">
                                                            <i data-feather="file" class="feather-large"></i>
                                                            <p class="mb-0">{{ basename($detail->bukti_transfer) }}</p>
                                                        </div>
                                                    @endif
                                                    <a href="{{ asset('storage/'.$filePath) }}" 
                                                        target="_blank" class="btn btn-sm btn-info mt-2">
                                                        <i data-feather="eye" class="feather-small mr-1"></i> Lihat Bukti
                                                    </a>
                                                @else
                                                    <p class="text-danger">File tidak ditemukan di: {{ $fullPath }}</p>
                                                    @if(app()->environment('local'))
                                                        <p class="text-muted small">Pastikan storage link sudah dibuat (php artisan storage:link)</p>
                                                    @endif
                                                @endif
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif


                                    <div class="form-group">
                                        <label>Upload Bukti Transfer Baru (opsional):</label>
                                        <div class="custom-file">
                                            <input type="file" name="bukti_transfer" class="custom-file-input" id="bukti_transfer">
                                            <label class="custom-file-label" for="bukti_transfer">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG, PDF (Maks: 2MB)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Sumber Dana:</label>
                                        <select name="sumber_dana" class="form-control">
                                            <option value="">Pilih Sumber Dana</option>
                                            <option value="Tunai" {{ $template->details->first()->sumber_dana == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                            <option value="BSI" {{ $template->details->first()->sumber_dana == 'BSI' ? 'selected' : '' }}>BSI</option>
                                            <option value="BRI" {{ $template->details->first()->sumber_dana == 'BRI' ? 'selected' : '' }}>BRI</option>
                                            <option value="Seabank" {{ $template->details->first()->sumber_dana == 'Seabank' ? 'selected' : '' }}>Seabank</option>
                                            <option value="Mandiri" {{ $template->details->first()->sumber_dana == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                            <option value="BCA" {{ $template->details->first()->sumber_dana == 'BCA' ? 'selected' : '' }}>BCA</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Preview file yang baru diupload -->
                                    <div id="filePreview" class="mt-3" style="display: none;">
                                        <label class="font-weight-bold">Preview:</label>
                                        <div class="preview-container text-center bg-light p-3 rounded">
                                            <img id="imagePreview" class="img-fluid mb-2" style="max-height: 150px; display: none;">
                                            <div id="fileIconPreview" class="file-preview" style="display: none;">
                                                <i data-feather="file" class="feather-large"></i>
                                                <p class="mb-0" id="fileNamePreview"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary mr-2" onclick="window.location.href='{{ route('rab_template.index') }}'">
                            <i data-feather="arrow-left"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="realisasiModal" tabindex="-1" role="dialog" aria-labelledby="realisasiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="realisasiModalLabel">Tambah Realisasi Pemakaian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRealisasi">
                    <input type="hidden" id="detailId">
                    <div class="form-group">
                        <label for="tambahRealisasi">Jumlah yang Ditambahkan</label>
                        <input type="text" class="form-control" id="tambahRealisasi" required>
                    </div>
                    <div class="form-group">
                        <label for="keteranganRealisasi">Keterangan (Wajib)</label>
                        <textarea class="form-control" id="keteranganRealisasi" rows="2" required></textarea>
                        <small class="text-muted">Contoh: Pembayaran hotel malam pertama</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveRealisasi">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">History Realisasi Pemakaian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> Total Realisasi dari History: 
                        <strong id="total-history">Rp 0</strong>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="20%">Tanggal</th>
                                <th width="15%">Jumlah</th>
                                <th>Keterangan</th>
                                <th width="15%">Ditambahkan Oleh</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Data akan diisi via JavaScript -->
                        </tbody>
                    </table>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger mr-auto" id="btnDeleteAll">
                    <i data-feather="trash-2" class="feather-sm"></i> Hapus Semua & Reset
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i data-feather="x" class="feather-sm"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script>
    const baseUrl = window.baseUrl;
$(function() {
    // Format number with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseNumber(value) {
        if (value === null || value === undefined) return 0;
        if (typeof value === 'number') return value;
        
        // Handle berbagai format input
        const str = value.toString().trim();
        if (str === '') return 0;
        
        // Hilangkan semua karakter non-digit kecuali koma dan titik
        const cleaned = str.replace(/[^\d,.-]/g, '')
                        .replace(/\./g, '')  // Hilangkan titik ribuan
                        .replace(',', '.');   // Ubah koma desimal ke titik
        
        const number = parseFloat(cleaned);
        return isNaN(number) ? 0 : number;
    }

    // Format as currency
    function formatCurrency(num) {
        return 'Rp ' + formatNumber(num);
    }

    // Initialize feather icons
    feather.replace({
        width: 18,
        height: 18
    });
    
    // Custom class for smaller feather icons
    $('.feather-small').each(function() {
        $(this).attr('width', 16);
        $(this).attr('height', 16);
    });
    
    $('.feather-xsmall').each(function() {
        $(this).attr('width', 12);
        $(this).attr('height', 12);
    });

    // Update custom file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Pilih file...');
    });

    // Check/uncheck all
    $('#check-all').change(function() {
        $('.approve-check').prop('checked', $(this).prop('checked'));
    });

    // Initialize autoNumeric for currency inputs
    $('.nilai-disetujui').each(function() {
        new AutoNumeric(this, {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            unformatOnSubmit: true,
            modifyValueOnWheel: false
        });
    });

    // Fungsi untuk menghitung total
    function calculateTotals() {
        let totalDisetujui = 0;
        let totalRealisasi = 0;
        
        $('.nilai-disetujui').each(function() {
            totalDisetujui += parseNumber($(this).val());
        });
        
        $('.realisasi-text').each(function() {
            totalRealisasi += parseNumber($(this).text().replace(/\./g, ''));
        });
        
        $('#total-disetujui').text(formatCurrency(totalDisetujui));
        $('#total-realisasi').text(formatCurrency(totalRealisasi));
    }

    // Tambahkan event handler untuk tombol history
    $(document).on('click', '.btn-history-realisasi', function() {
        const detailId = $(this).data('detail-id');
        
        $.get(`${baseUrl}/rab_template/history/${detailId}`, function(response) {
            if (response.success) {
                const historyTableBody = $('#historyTableBody');
                historyTableBody.empty();

                const total = response.data.reduce((sum, item) => sum + parseFloat(item.jumlah), 0);
                
                response.data.forEach(item => {
                    historyTableBody.append(`
                        <tr data-history-id="${item.id}">
                        <td>${item.tanggal}</td>
                        <td class="text-right">Rp ${formatNumber(item.jumlah)}</td>
                        <td>${item.keterangan || '-'}</td>
                        <td>${item.user}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger btn-delete-history" 
                                    data-history-id="${item.id}"
                                    data-jumlah="${item.jumlah}"
                                    title="Hapus">
                                <i data-feather="trash-2" class="feather-sm"></i>
                            </button>
                        </td>
                    </tr>
                    `);
                });
                
                $('#total-history').text('Rp ' + formatNumber(total));
                $('#historyModal').data('detail-id', detailId);
                $('#historyModal').data('current-total', total);
                $('#historyModal').modal('show');
                feather.replace();
            }
        }).fail(function() {
            alert('Gagal memuat history realisasi');
        });
    });

    // Handler untuk tombol delete history
    $(document).on('click', '.btn-delete-history', function() {
        const historyId = $(this).data('history-id');
        const jumlah = parseFloat($(this).data('jumlah'));
        const currentTotal = parseFloat($('#historyModal').data('current-total'));
        
        if (confirm('Apakah Anda yakin ingin menghapus history ini?')) {
            $.ajax({
                url: `${baseUrl}/rab_template/history/${historyId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Hapus baris dari tabel
                        $(`tr[data-history-id="${historyId}"]`).remove();
                        
                        // Update total
                        const newTotal = currentTotal - jumlah;
                        $('#total-history').text('Rp ' + formatNumber(newTotal));
                        $('#historyModal').data('current-total', newTotal);
                        
                        // Update realisasi di tabel utama
                        const detailId = $('#historyModal').data('detail-id');
                        const currentRealisasi = parseNumber($(`button[data-detail-id="${detailId}"]`)
                            .closest('tr')
                            .find('.realisasi-text')
                            .text());
                        
                        const newRealisasi = currentRealisasi - jumlah;
                        $(`button[data-detail-id="${detailId}"]`)
                            .closest('tr')
                            .find('.realisasi-text')
                            .text(formatNumber(newRealisasi));
                        
                        updateRealisasiInfo(detailId, newRealisasi);
                        
                        alert('History berhasil dihapus dan nilai realisasi diperbarui');
                    }
                },
                error: function() {
                    alert('Gagal menghapus history');
                }
            });
        }
    });

    // Handler untuk tombol delete all
    $('#btnDeleteAll').click(function() {
        const detailId = $('#historyModal').data('detail-id');
        if (confirm('Apakah Anda yakin ingin menghapus SEMUA history dan mereset realisasi ke 0?')) {
            $.ajax({
                url: `${baseUrl}/rab_template/history/reset/${detailId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Semua history berhasil dihapus dan realisasi direset');
                        $('#historyModal').modal('hide');
                        // Update tampilan
                        $(`button[data-detail-id="${detailId}"]`)
                            .closest('tr')
                            .find('.realisasi-text')
                            .text('0');
                        updateRealisasiInfo(detailId, 0);
                    }
                },
                error: function() {
                    alert('Gagal menghapus history');
                }
            });
        }
    });

    function updateRealisasiInfo(detailId, newRealisasi) {
        const row = $(`button[data-detail-id="${detailId}"]`).closest('tr');
        
        // Dapatkan nilai disetujui
        const nilaiDisetujuiInput = row.find('input[name*="[nilai_disetujui]"]');
        const nilaiDisetujui = parseNumber(nilaiDisetujuiInput.val());
        
        // Jika newRealisasi tidak diberikan, hitung dari history
        if (typeof newRealisasi === 'undefined') {
            $.get(`${baseUrl}/rab_template/history/sum/${detailId}`, function(response) {
                if (response.success) {
                    const totalRealisasi = response.total;
                    updateUI(row, detailId, nilaiDisetujui, totalRealisasi);
                }
            });
        } else {
            updateUI(row, detailId, nilaiDisetujui, newRealisasi);
        }
    }

    function updateUI(row, detailId, nilaiDisetujui, realisasi) {
        const sisa = nilaiDisetujui - realisasi;
        
        // Update text
        row.find('.realisasi-text').text(formatNumber(realisasi));
        row.find('.sisa-text').text('Sisa: Rp ' + formatNumber(sisa));
        
        // Update progress bar
        const percentage = nilaiDisetujui > 0 ? (realisasi / nilaiDisetujui) * 100 : 0;
        const progressBar = row.find('.progress-bar');
        progressBar.css('width', percentage + '%');
        progressBar.attr('aria-valuenow', percentage);
        progressBar.find('small').text(percentage.toFixed(2) + '%');
        
        // Update warna
        progressBar.removeClass('bg-success bg-warning bg-danger');
        if (percentage > 100) {
            progressBar.addClass('bg-danger');
        } else if (percentage > 80) {
            progressBar.addClass('bg-warning');
        } else {
            progressBar.addClass('bg-success');
        }
        
        calculateTotals();
    }

    function updateProgressBar(row, nilaiDisetujui, realisasi) {
        const percentage = nilaiDisetujui > 0 ? (realisasi / nilaiDisetujui) * 100 : 0;
        const progressBar = row.find('.progress-bar');
        
        progressBar.css('width', percentage + '%');
        progressBar.attr('aria-valuenow', percentage);
        progressBar.find('small').text(percentage.toFixed(2) + '%');
        
        // Update warna
        progressBar.removeClass('bg-success bg-warning bg-danger');
        if (percentage > 100) {
            progressBar.addClass('bg-danger');
        } else if (percentage > 80) {
            progressBar.addClass('bg-warning');
        } else {
            progressBar.addClass('bg-success');
        }
    }

    // Calculate when values change
    $(document).on('input', '.nilai-disetujui, .realisasi-pemakaian', function() {
        const row = $(this).closest('tr');
        updateRealisasiInfo(row);
        calculateTotals();
    });

    // Initialize calculation on load
    $('tr.rab-item').each(function() {
        updateRealisasiInfo($(this));
    });
    calculateTotals();

    // Form submission
    $('#approval-form').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading indicator
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        // Create FormData object to handle file uploads
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reset button
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
                
                // Show success message
                const alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i data-feather="check-circle" class="mr-2"></i> Data berhasil disimpan
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                $('.box-body').prepend(alertHtml);
                feather.replace();
                
                // Check if response contains redirect URL
                if (response.redirect) {
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    // Reload page after short delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                // Reset button
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
                
                // Show error message
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-feather="alert-circle" class="mr-2"></i> ${errorMessage}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                $('.box-body').prepend(alertHtml);
                feather.replace();
                
                console.error(xhr.responseText);
            }
        });
    });
    
    // Add responsive functionality for mobile devices
    function adjustForMobileView() {
        if (window.innerWidth < 768) {
            // Handle table responsive behavior for small screens
            $('.table-responsive table').addClass('table-mobile-responsive');
            
            // Add data-label attributes to TD elements based on TH text
            $('.table-mobile-responsive tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    const headerText = $(this).closest('table').find('thead th').eq(index).text().trim();
                    $(this).attr('data-label', headerText);
                });
            });
        } else {
            $('.table-responsive table').removeClass('table-mobile-responsive');
        }
    }
    
    // Run on page load and window resize
    adjustForMobileView();
    $(window).resize(adjustForMobileView);

    $('.btn-add-realisasi').click(function() {
        const detailId = $(this).data('detail-id');
        $('#detailId').val(detailId);
        $('#tambahRealisasi').val('');
        $('#keteranganRealisasi').val('');
        $('#realisasiModal').modal('show');
    });

    // Simpan realisasi
    $('#saveRealisasi').click(function() {
        const detailId = $('#detailId').val();
        const tambahRealisasi = parseNumber($('#tambahRealisasi').val()) || 0;
        const keterangan = $('#keteranganRealisasi').val();
        
        if (!keterangan) {
            alert('Keterangan wajib diisi');
            return;
        }
        
        // Kirim data ke server
        $.ajax({
            url: `${baseUrl}/rab_template/${detailId}/add-realisasi`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                jumlah: tambahRealisasi,
                keterangan: keterangan
            },
            success: function(response) {
                if (response.success) {
                    // Update tampilan
                    const currentText = $(`button[data-detail-id="${detailId}"]`).closest('tr').find('.realisasi-text').text();
                    const currentValue = parseNumber(currentText.replace(/\./g, ''));
                    const newValue = currentValue + tambahRealisasi;
                    
                    updateRealisasiInfo(detailId, newValue);
                    $('#realisasiModal').modal('hide');
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Gagal menyimpan realisasi');
                console.error(xhr.responseText);
            }
        });
    });

    // Initialize AutoNumeric untuk input modal
    new AutoNumeric('#tambahRealisasi', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        unformatOnSubmit: true,
        modifyValueOnWheel: false
    });

    // Preview file sebelum upload
    $('#bukti_transfer').change(function() {
        const file = this.files[0];
        if (file) {
            $('#filePreview').show();
            const fileType = file.type.split('/')[0];
            const fileName = file.name;
            
            if (fileType === 'image') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                    $('#fileIconPreview').hide();
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
                $('#fileNamePreview').text(fileName);
                $('#fileIconPreview').show();
            }
        } else {
            $('#filePreview').hide();
        }
    });
});
</script>

<style>
/* Custom styles for responsive design */
@media (max-width: 767.98px) {
    .box-title {
        font-size: 1.2rem;
    }
    
    /* Responsive table for mobile */
    .table-mobile-responsive {
        border: 0;
    }
    
    .table-mobile-responsive thead {
        display: none;
    }
    
    .table-mobile-responsive tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    .table-mobile-responsive td {
        display: flex;
        justify-content: space-between;
        text-align: right;
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
        border-top: 0;
    }
    
    .table-mobile-responsive td:last-child {
        border-bottom: 0;
    }
    
    .table-mobile-responsive td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-align: left;
    }
    
    .table-mobile-responsive tfoot {
        display: block;
    }
    
    .table-mobile-responsive tfoot tr {
        background-color: #f8f9fa;
    }
    
    /* Improve form elements on mobile */
    .input-group {
        flex-wrap: nowrap;
    }
    
    .custom-file-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Improve buttons on mobile */
    .form-group .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Adjust spacing on mobile */
    .mb-md-0 {
        margin-bottom: 0.5rem !important;
    }
}

/* Feather icon sizing helper classes */
.feather-small {
    width: 16px;
    height: 16px;
}

.feather-xsmall {
    width: 12px;
    height: 12px;
}

/* Progress bar styling */
.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
    overflow: hidden;
}

/* Card styling */
.card {
    transition: all 0.3s ease;
}

.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

/* Form elements styling */
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::after {
    background-color: #fff;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}

/* Fix for AutoNumeric's handling in responsive design */
.autonumeric-currency {
    text-align: right;
}
.file-preview {
    padding: 20px;
    text-align: center;
}

.file-preview i {
    width: 48px;
    height: 48px;
    color: #6c757d;
}

.feather-large {
    width: 48px;
    height: 48px;
}

/* Tambahkan di bagian CSS Anda */
.btn-delete-history {
    padding: 0.15rem 0.3rem;
    line-height: 1;
}

#btnDeleteAll {
    display: flex;
    align-items: center;
}

#btnDeleteAll i {
    margin-right: 5px;
}

.card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>
@endpush
