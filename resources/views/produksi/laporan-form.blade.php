<div class="modal fade" id="modal-laporan" tabindex="-1" aria-labelledby="modal-laporan" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content modal-glass">
            <div class="modal-header bg-gradient-pastel-primary">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i data-feather="printer" class="icon-lg mr-2"></i>Cetak Laporan Produksi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i data-feather="x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-laporan-preview" action="{{ route('produksi.generateLaporan') }}" method="GET" target="_blank">
                    <form id="form-laporan-download" action="{{ route('produksi.downloadLaporan') }}" method="GET">
                    <div class="card card-pastel mb-4">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i data-feather="filter" class="icon-sm mr-2"></i>Filter Laporan
                            </h6>
                            
                            @if($outlets->count() > 1)
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted">Outlet</label>
                                <select name="id_outlet" class="form-control input-pastel">
                                    <option value="">Semua Outlet</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted">Periode Tanggal</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="date" name="start_date" class="form-control input-pastel" 
                                               placeholder="Dari Tanggal">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" name="end_date" class="form-control input-pastel" 
                                               placeholder="Sampai Tanggal">
                                    </div>
                                </div>
                                <small class="text-muted">Kosongkan untuk semua tanggal</small>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-muted">Tipe Laporan</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-pastel-info active btn-hover-grow">
                                        <input type="radio" name="report_type" value="preview" checked> 
                                        <i data-feather="eye" class="icon-xs mr-1"></i> Preview
                                    </label>
                                    <label class="btn btn-pastel-success btn-hover-grow">
                                        <input type="radio" name="report_type" value="download"> 
                                        <i data-feather="download" class="icon-xs mr-1"></i> Download
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-pastel-info">
                        <div class="d-flex align-items-center">
                            <i data-feather="info" class="icon-sm mr-2"></i>
                            <div>
                                <small class="d-block"><strong>Preview:</strong> Tampilkan PDF di browser baru</small>
                                <small class="d-block"><strong>Download:</strong> Unduh file PDF langsung</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-pastel-secondary btn-hover-grow" data-dismiss="modal">
                    <i data-feather="x" class="icon-xs mr-1"></i>Batal
                </button>
                <button type="button" class="btn btn-pastel-primary btn-hover-glow" onclick="generateLaporan()">
                    <i data-feather="file-text" class="icon-xs mr-1"></i>Buat Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-group-toggle .btn {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.btn-group-toggle .btn.active {
    border-color: #007bff;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}

.btn-group-toggle .btn:not(.active):hover {
    transform: translateY(-2px);
}
</style>

<script>
function generateLaporan() {
    // Validasi tanggal
    const startDate = $('input[name="start_date"]').val();
    const endDate = $('input[name="end_date"]').val();
    
    if ((startDate && !endDate) || (!startDate && endDate)) {
        Swal.fire({
            title: 'Perhatian!',
            text: 'Harap pilih kedua tanggal atau kosongkan keduanya',
            icon: 'warning',
            background: 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)',
            borderRadius: '15px',
            customClass: {
                confirmButton: 'btn-pastel-primary'
            }
        });
        return;
    }

    if (startDate && endDate && startDate > endDate) {
        Swal.fire({
            title: 'Perhatian!',
            text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir',
            icon: 'warning',
            background: 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)',
            borderRadius: '15px',
            customClass: {
                confirmButton: 'btn-pastel-primary'
            }
        });
        return;
    }

    const reportType = $('input[name="report_type"]:checked').val();
    
    // Show loading notification
    Swal.fire({
        title: 'Membuat Laporan...',
        text: 'Laporan PDF sedang dipersiapkan',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        background: 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)',
        borderRadius: '15px'
    });

    // Submit form berdasarkan tipe
    if (reportType === 'preview') {
        $('#form-laporan-preview').submit();
    } else {
        $('#form-laporan-download').submit();
    }
    
    $('#modal-laporan').modal('hide');
    
    // Close loading after a delay (for preview)
    if (reportType === 'preview') {
        setTimeout(() => {
            Swal.close();
        }, 2000);
    }
}

function showLaporanForm() {
    $('#modal-laporan').modal('show');
    feather.replace();
    
    // Reset form state
    $('input[name="report_type"][value="preview"]').prop('checked', true);
    $('.btn-group-toggle .btn').removeClass('active');
    $('.btn-group-toggle .btn:first').addClass('active');
}

// Handle button group toggle
$(document).on('click', '.btn-group-toggle .btn', function() {
    $('.btn-group-toggle .btn').removeClass('active');
    $(this).addClass('active');
    $(this).find('input').prop('checked', true);
});
</script>
