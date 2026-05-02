<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
<style>
.barcode-simple {
    font-family: 'Libre Barcode 128', cursive !important;
    font-size: 24px !important;
    letter-spacing: 2px !important;
    transform: scale(1.1);
}
</style>

@extends('app')

@section('title')
    Management Sparepart
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Management Sparepart</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm()" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Sparepart</button>
                <a href="{{ route('service.invoice.history') }}" class="btn btn-warning btn-sm" style="margin-right: 10px;">
                        <i class="fa fa-list"></i> History Invoice
                    </a>
                    <button onclick="showExportModal()" class="btn btn-primary btn-sm"><i class="fa fa-file-pdf-o"></i> Export Log PDF</button>
            </div>
            
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-stiped table-bordered" id="table-sparepart">
                        <thead>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Sparepart</th>
                            <th>Merk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status Stok</th>
                            <th>Satuan</th>
                            <th width="15%">Barcode</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Log dan Management -->
<div class="modal fade" id="modal-log" tabindex="-1" role="dialog" aria-labelledby="modal-log">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Management Sparepart - <span id="log-sparepart-name"></span></h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-tambah-stok" data-toggle="tab">Tambah Stok</a></li>
                    <li><a href="#tab-update-harga" data-toggle="tab">Update Harga</a></li>
                    <li><a href="#tab-history" data-toggle="tab">History Perubahan</a></li>
                </ul>
                
                <div class="tab-content" style="padding-top: 15px;">
                    <!-- Tab Tambah Stok -->
                    <div class="tab-pane active" id="tab-tambah-stok">
                        <form id="form-tambah-stok">
                            @csrf
                            <input type="hidden" name="id_sparepart" id="tambah-stok-id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jumlah_tambah">Jumlah Tambah Stok</label>
                                        <input type="number" name="jumlah_tambah" id="jumlah_tambah" class="form-control" required min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stok_sekarang">Stok Sekarang</label>
                                        <input type="text" id="stok_sekarang" class="form-control" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="keterangan_stok">Keterangan (Opsional)</label>
                                <textarea name="keterangan" id="keterangan_stok" class="form-control" rows="2" placeholder="Contoh: Restock dari supplier..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Tambah Stok</button>
                        </form>
                    </div>
                    
                    <!-- Tab Update Harga -->
                    <div class="tab-pane" id="tab-update-harga">
                        <form id="form-update-harga">
                            @csrf
                            <input type="hidden" name="id_sparepart" id="update-harga-id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="harga_sekarang">Harga Sekarang</label>
                                        <input type="text" id="harga_sekarang" class="form-control" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="harga_baru">Harga Baru</label>
                                        <input type="text" name="harga_baru" id="harga_baru" class="form-control" required 
                                            oninput="formatCurrencyRealTime(this)" 
                                            onblur="formatCurrencyFinal(this)">
                                        <small class="text-muted">Contoh: 32.500</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="keterangan_harga">Keterangan (Opsional)</label>
                                <textarea name="keterangan" id="keterangan_harga" class="form-control" rows="2" placeholder="Contoh: Penyesuaian harga supplier..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Harga</button>
                        </form>
                    </div>
                    
                    <!-- Tab History -->
                    <div class="tab-pane" id="tab-history">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="table-log">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Nilai Lama</th>
                                        <th>Nilai Baru</th>
                                        <th>Selisih</th>
                                        <th>User</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="log-body">
                                    <!-- Data log akan diisi di sini -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export Log PDF -->
<div class="modal fade" id="modal-export" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> Export Laporan Log Sparepart</h4>
            </div>
            <div class="modal-body">
                <form id="form-export">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_sparepart">Sparepart (Opsional)</label>
                                <select name="sparepart_id" id="export_sparepart" class="form-control">
                                    <option value="">Semua Sparepart</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_tipe">Tipe Perubahan (Opsional)</label>
                                <select name="tipe_perubahan" id="export_tipe" class="form-control">
                                    <option value="">Semua Tipe</option>
                                    <option value="stok">Stok</option>
                                    <option value="harga">Harga</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_start_date">Tanggal Mulai (Opsional)</label>
                                <input type="date" name="start_date" id="export_start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_end_date">Tanggal Akhir (Opsional)</label>
                                <input type="date" name="end_date" id="export_end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Informasi:</strong> Kosongkan filter untuk menampilkan semua data.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="exportPDF()"><i class="fa fa-download"></i> Export PDF</button>
            </div>
        </div>
    </div>
</div>

@include('sparepart.form')
@include('sparepart.barcode-modal')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('#table-sparepart').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('sparepart.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_sparepart'},
                {data: 'nama_sparepart'},
                {data: 'merk'},
                {data: 'harga_formatted', searchable: false},
                {data: 'stok'},
                {data: 'stok_status', searchable: false, sortable: false},
                {data: 'satuan'},
                {data: 'barcode', searchable: false, sortable: false},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                // Format harga sebelum submit
                const hargaInput = $('#modal-form [name=harga]');
                const hargaValue = parseNumber(hargaInput.val());
                hargaInput.val(hargaValue);
                
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        return;
                    });
            }
        });
    });

    // Function untuk format currency
    function formatCurrency(input) {
        let value = input.value.replace(/\./g, '');
        if (!isNaN(value)) {
            const intValue = parseInt(value) || 0;
            input.value = formatNumber(intValue);
        }
    }

    function formatNumber(number) {
        const num = parseInt(number) || 0;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseNumber(formattedNumber) {
        if (typeof formattedNumber === 'number') {
            return formattedNumber;
        }
        if (typeof formattedNumber !== 'string') {
            return 0;
        }
        const cleanNumber = formattedNumber.toString().replace(/\./g, '').replace(/,/g, '');
        return parseInt(cleanNumber) || 0;
    }

    function addForm() {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Sparepart');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', '{{ route('sparepart.store') }}');
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=kode_sparepart]').val('Sedang digenerate...');
    }

    // Function untuk show log modal
    function showLogModal(id) {
        $('#modal-log').modal('show');
        
        // Reset form
        $('#form-tambah-stok')[0].reset();
        $('#form-update-harga')[0].reset();
        $('#log-body').empty();
        
        // Set ID untuk form
        $('#tambah-stok-id').val(id);
        $('#update-harga-id').val(id);
        
        // Load data sparepart
        $.get(`{{ url('sparepart') }}/${id}/edit`)
            .done((response) => {
                $('#log-sparepart-name').text(response.nama_sparepart + ' (' + response.kode_sparepart + ')');
                $('#stok_sekarang').val(response.stok + ' ' + response.satuan);
                $('#harga_sekarang').val('Rp ' + formatNumber(response.harga));
            })
            .fail((errors) => {
                alert('Tidak dapat memuat data sparepart');
            });
        
        // Load logs
        loadLogs(id);
    }

    // Function untuk load logs
    function loadLogs(id) {
        $.get(`{{ url('sparepart') }}/${id}/logs`)
            .done((response) => {
                if (response.success) {
                    displayLogs(response.logs);
                }
            })
            .fail((errors) => {
                console.error('Gagal memuat logs');
            });
    }

    // Function untuk display logs
    function displayLogs(logs) {
        const tbody = $('#log-body');
        tbody.empty();
        
        if (logs.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center">Tidak ada data log</td></tr>');
            return;
        }
        
        logs.forEach((log) => {
            const tanggal = new Date(log.created_at).toLocaleString('id-ID');
            const user = log.user ? log.user.name : 'System';
            
            // Format nilai berdasarkan tipe
            const formatNilai = (nilai, tipe) => {
                if (tipe === 'harga') {
                    return 'Rp ' + formatNumber(nilai);
                } else {
                    return nilai;
                }
            };
            
            // Format selisih dengan warna
            let selisihClass = '';
            let selisihPrefix = '';
            
            if (log.selisih > 0) {
                selisihClass = 'text-success';
                selisihPrefix = '+';
            } else if (log.selisih < 0) {
                selisihClass = 'text-danger';
            }
            
            const row = `
                <tr>
                    <td>${tanggal}</td>
                    <td>
                        <span class="label ${log.tipe_perubahan === 'stok' ? 'label-info' : 'label-warning'}">
                            ${log.tipe_perubahan.toUpperCase()}
                        </span>
                    </td>
                    <td>${formatNilai(log.nilai_lama, log.tipe_perubahan)}</td>
                    <td>${formatNilai(log.nilai_baru, log.tipe_perubahan)}</td>
                    <td class="${selisihClass}">
                        ${selisihPrefix}${formatNilai(log.selisih, log.tipe_perubahan)}
                    </td>
                    <td>${user}</td>
                    <td>${log.keterangan || '-'}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Form tambah stok
    $('#form-tambah-stok').submit(function(e) {
        e.preventDefault();
        
        const id = $('#tambah-stok-id').val();
        const formData = $(this).serialize();
        
        $.post(`{{ url('sparepart') }}/${id}/tambah-stok`, formData)
            .done((response) => {
                if (response.success) {
                    alert(response.message);
                    $('#form-tambah-stok')[0].reset();
                    table.ajax.reload(); // Reload table utama
                    loadLogs(id); // Reload logs
                    
                    // Update stok sekarang
                    $.get(`{{ url('sparepart') }}/${id}/edit`)
                        .done((response) => {
                            $('#stok_sekarang').val(response.stok + ' ' + response.satuan);
                        });
                } else {
                    alert(response.message);
                }
            })
            .fail((errors) => {
                let errorMessage = 'Terjadi kesalahan';
                if (errors.responseJSON && errors.responseJSON.errors) {
                    errorMessage = Object.values(errors.responseJSON.errors).join(', ');
                }
                alert(errorMessage);
            });
    });

    // Form update harga
    $('#form-update-harga').submit(function(e) {
        e.preventDefault();
        
        const id = $('#update-harga-id').val();
        
        // Ambil nilai dari input dan parse dengan benar
        const hargaBaru = parseNumber($('#harga_baru').val());
        
        // Validasi
        if (hargaBaru <= 0) {
            alert('Harga harus lebih dari 0');
            return;
        }
        
        const formData = {
            _token: $('input[name="_token"]').val(),
            harga_baru: hargaBaru,
            keterangan: $('#keterangan_harga').val()
        };
        
        $.post(`{{ url('sparepart') }}/${id}/update-harga`, formData)
            .done((response) => {
                if (response.success) {
                    alert(response.message);
                    $('#form-update-harga')[0].reset();
                    table.ajax.reload(); // Reload table utama
                    loadLogs(id); // Reload logs
                    
                    // Update harga sekarang
                    $.get(`{{ url('sparepart') }}/${id}/edit`)
                        .done((response) => {
                            $('#harga_sekarang').val('Rp ' + formatNumber(response.harga));
                        });
                } else {
                    alert(response.message);
                }
            })
            .fail((errors) => {
                let errorMessage = 'Terjadi kesalahan';
                if (errors.responseJSON && errors.responseJSON.errors) {
                    errorMessage = Object.values(errors.responseJSON.errors).join(', ');
                } else if (errors.responseJSON && errors.responseJSON.message) {
                    errorMessage = errors.responseJSON.message;
                }
                alert(errorMessage);
            });
    });

    // Update editForm untuk menghilangkan stok dan harga
    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Sparepart');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');

        const id = url.split('/').pop();
        $.get(`{{ url('sparepart') }}/${id}/edit`)
            .done((response) => {
                $('#modal-form [name=kode_sparepart]').val(response.kode_sparepart);
                $('#modal-form [name=nama_sparepart]').val(response.nama_sparepart);
                $('#modal-form [name=merk]').val(response.merk);
                $('#modal-form [name=spesifikasi]').val(response.spesifikasi);
                // HAPUS: harga dan stok
                $('#modal-form [name=stok_minimum]').val(response.stok_minimum);
                $('#modal-form [name=satuan]').val(response.satuan);
                $('#modal-form [name=keterangan]').val(response.keterangan);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function showBarcode(kode, nama) {
        $('#barcode-kode').text(kode);
        $('#barcode-nama').text(nama);
        
        // Generate barcode dan qrcode untuk modal
        const barcodeHTML = '<div class="barcode-simple" style="font-size: 48px; text-align: center;">*' + kode + '*</div>';
        const qrcodeURL = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=' + encodeURIComponent(kode + '|' + nama);
        const qrcodeHTML = '<img src="' + qrcodeURL + '" alt="QR Code" style="width: 150px; height: 150px;">';
        
        $('#barcode-image').html(barcodeHTML);
        $('#qrcode-image').html(qrcodeHTML);
        
        $('#modal-barcode').modal('show');
    }

    // Fungsi untuk print barcode terpilih
    function printBarcode() {
        const checkedBoxes = $('input[name="id[]"]:checked');
        if (checkedBoxes.length < 1) {
            alert('Pilih data yang akan diprint!');
            return;
        }

        const form = $('#form-barcode');
        const ids = checkedBoxes.map(function() {
            return $(this).val();
        }).get();

        // Kosongkan form terlebih dahulu
        form.find('input').remove();
        
        // Tambahkan input untuk setiap ID
        ids.forEach(function(id) {
            form.append('<input type="hidden" name="ids[]" value="' + id + '">');
        });

        // Submit form
        form.submit();
    }

    // Real-time formatting untuk input harga
    function formatCurrencyRealTime(input) {
        // Simpan cursor position
        const cursorPosition = input.selectionStart;
        const originalLength = input.value.length;
        
        // Hapus semua karakter non-digit
        let value = input.value.replace(/[^\d]/g, '');
        
        // Format dengan titik
        if (value.length > 0) {
            const formattedValue = formatNumber(parseInt(value));
            input.value = formattedValue;
            
            // Adjust cursor position
            const newLength = formattedValue.length;
            const lengthDiff = newLength - originalLength;
            const newCursorPosition = cursorPosition + lengthDiff;
            input.setSelectionRange(newCursorPosition, newCursorPosition);
        }
    }

    // Final formatting saat keluar dari input
    function formatCurrencyFinal(input) {
        let value = input.value.replace(/\./g, '');
        if (!isNaN(value) && value !== '') {
            const intValue = parseInt(value) || 0;
            input.value = formatNumber(intValue);
        }
    }

    // Juga update event handler untuk input harga
    $('#harga_baru').on('input', function() {
        formatCurrencyRealTime(this);
    });

    $('#harga_baru').on('blur', function() {
        formatCurrencyFinal(this);
    });

    // Function untuk show export modal
    function showExportModal() {
        $('#modal-export').modal('show');
        $('#form-export')[0].reset();
        loadSparepartsForFilter();
    }

    // Function untuk load spareparts ke filter
    function loadSparepartsForFilter() {
        $.get('{{ route('sparepart.get-for-filter') }}')
            .done((response) => {
                if (response.success) {
                    const select = $('#export_sparepart');
                    select.empty().append('<option value="">Semua Sparepart</option>');
                    
                    response.spareparts.forEach((sparepart) => {
                        select.append(
                            `<option value="${sparepart.id_sparepart}">${sparepart.kode_sparepart} - ${sparepart.nama_sparepart}</option>`
                        );
                    });
                }
            })
            .fail((errors) => {
                console.error('Gagal memuat data sparepart untuk filter');
            });
    }

    // Function untuk export PDF
    function exportPDF() {
        const formData = $('#form-export').serialize();
        const url = `{{ route('sparepart.export-log-pdf') }}?${formData}`;
        
        // Buka di tab baru
        window.open(url, '_blank');
        
        // Tutup modal
        $('#modal-export').modal('hide');
    }

    // Update function showLogModal
    function showLogModal(id) {
        $('#modal-log').modal('show');
        
        // Reset form
        $('#form-tambah-stok')[0].reset();
        $('#form-update-harga')[0].reset();
        $('#log-body').empty();
        
        // Set ID untuk form
        $('#tambah-stok-id').val(id);
        $('#update-harga-id').val(id);
        
        // Load data sparepart
        $.get(`{{ url('sparepart') }}/${id}/edit`)
            .done((response) => {
                $('#log-sparepart-name').text(response.nama_sparepart + ' (' + response.kode_sparepart + ')');
                $('#stok_sekarang').val(response.stok + ' ' + response.satuan);
                $('#harga_sekarang').val('Rp ' + formatNumber(response.harga));
                
                // Tambahkan tombol export khusus untuk sparepart ini
                if (!$('#export-specific-btn').length) {
                    $('.modal-title').append(
                        ` <button type="button" onclick="exportSpecificSparepart(${id})" class="btn btn-xs btn-primary" id="export-specific-btn">
                            <i class="fa fa-file-pdf-o"></i> Export Log
                        </button>`
                    );
                }
            })
            .fail((errors) => {
                alert('Tidak dapat memuat data sparepart');
            });
        
        // Load logs
        loadLogs(id);
    }

    // Function untuk export log sparepart tertentu
    function exportSpecificSparepart(id) {
        const url = `{{ route('sparepart.export-log-pdf') }}?sparepart_id=${id}`;
        window.open(url, '_blank');
    }

    // Juga load spareparts saat page load untuk persiapan
    $(document).ready(function() {
        loadSparepartsForFilter();
    });

</script>
@endpush
