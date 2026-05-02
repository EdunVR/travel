@extends('app')

@section('title')
    Input Laporan Manual - {{ $agen->nama }}
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Input Laporan Penjualan Manual - {{ $agen->nama }}</h3>
                <a href="{{ route('agen_laporan.index', $agen->id_member) }}" class="btn btn-default btn-xs">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="box-body">
                <form id="form-laporan">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tanggal">Tanggal Penjualan</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_gerobak">Pilih Gerobak *</label>
                                <select name="id_gerobak" id="id_gerobak" class="form-control" required>
                                    <option value="">Pilih Gerobak</option>
                                    @foreach($gerobaks as $gerobak)
                                        <option value="{{ $gerobak->id_gerobak }}">{{ $gerobak->nama_gerobak }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="produk_select">Pilih Produk</label>
                                <select id="produk_select" class="form-control" disabled>
                                    <option value="">Pilih gerobak terlebih dahulu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="visibility: hidden;">Tambah</label><br>
                                <button type="button" id="btn-tambah-produk" class="btn btn-primary" disabled>
                                    <i class="fa fa-plus"></i> Tambah Produk
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Keterangan penjualan">
                            </div>
                        </div>
                    </div>

                    <h4>Data Produk yang Dijual</h4>
                    <div class="alert alert-info">
                        <strong>Info:</strong> Hanya produk yang tersedia di gerobak yang dapat dipilih.
                    </div>
                    
                    <table class="table table-bordered" id="tabel-produk">
                        <thead>
                            <tr class="bg-primary">
                                <th width="5%">No</th>
                                <th width="20%">Kode Produk</th>
                                <th width="25%">Nama Produk</th>
                                <th width="10%">Stok Tersedia</th>
                                <th width="15%">Harga Jual (Rp)</th>
                                <th width="10%">Jumlah</th>
                                <th width="15%">Subtotal (Rp)</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="empty-row">
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada produk ditambahkan. Pilih gerobak dan tambahkan produk.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-success">
                                <th colspan="6" class="text-right">Total:</th>
                                <th id="total-harga">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="form-group" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Laporan
                        </button>
                        <a href="{{ route('agen_laporan.index', $agen->id_member) }}" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.Produk-row {
    background-color: #f9f9f9;
}
.Produk-row:hover {
    background-color: #f0f0f0;
}
</style>
@endpush

@push('scripts')
<script>
let produkList = [];
let rowCount = 0;

$(document).ready(function() {
    // When gerobak is selected
    $('#id_gerobak').on('change', function() {
        const gerobakId = $(this).val();
        
        if (gerobakId) {
            // Enable produk select and load produk
            $('#produk_select').prop('disabled', false);
            $('#btn-tambah-produk').prop('disabled', false);
            
            // Load produk from gerobak
            loadProdukByGerobak(gerobakId);
        } else {
            $('#produk_select').prop('disabled', true);
            $('#btn-tambah-produk').prop('disabled', true);
            $('#produk_select').html('<option value="">Pilih gerobak terlebih dahulu</option>');
        }
    });

    // Add produk to table
    $('#btn-tambah-produk').on('click', function() {
        const produkId = $('#produk_select').val();
        
        if (!produkId) {
            alert('Pilih produk terlebih dahulu');
            return;
        }
        
        // Check if produk already added
        if ($(`input[name="produk[${produkId}][id_produk]"]`).length > 0) {
            alert('Produk ini sudah ditambahkan');
            return;
        }
        
        // Find produk data
        const produkData = produkList.find(p => p.id_produk == produkId);
        
        if (produkData) {
            addProdukRow(produkData);
        }
    });

    // Calculate totals
    $(document).on('change', 'input[name*="[harga_jual]"], input[name*="[jumlah]"]', function() {
        calculateSubtotal($(this).closest('tr'));
        calculateTotal();
    });

    // Remove row
    $(document).on('click', '.btn-remove', function() {
        $(this).closest('tr').remove();
        calculateTotal();
        
        // Show empty message if no rows
        if ($('#tabel-produk tbody tr.produk-row').length === 0) {
            $('#empty-row').show();
        }
    });
});

function loadProdukByGerobak(gerobakId) {
    $.get('{{ url("agen_laporan/get-produk") }}/' + gerobakId)
        .done(function(response) {
            if (response.success) {
                produkList = response.produk;
                
                // Populate produk select
                $('#produk_select').html('<option value="">Pilih Produk</option>');
                response.produk.forEach(function(produk) {
                    $('#produk_select').append(
                        `<option value="${produk.id_produk}">${produk.kode_produk} - ${produk.nama_produk} (Stok: ${produk.stok})</option>`
                    );
                });
            } else {
                alert(response.message);
            }
        })
        .fail(function(xhr) {
            alert('Gagal memuat produk dari gerobak');
            console.error(xhr.responseText);
        });
}

function addProdukRow(produkData) {
    rowCount++;
    
    // Hide empty row message
    $('#empty-row').hide();
    
    const newRow = `
        <tr class="produk-row" data-produk-id="${produkData.id_produk}">
            <td class="text-center">${rowCount}</td>
            <td>
                ${produkData.kode_produk}
                <input type="hidden" name="produk[${produkData.id_produk}][id_produk]" value="${produkData.id_produk}">
            </td>
            <td>${produkData.nama_produk}</td>
            <td class="text-center">${produkData.stok}</td>
            <td>
                <input type="number" name="produk[${produkData.id_produk}][harga_jual]" 
                       class="form-control harga-jual" value="${produkData.harga_default}" 
                       min="1" required>
            </td>
            <td>
                <input type="number" name="produk[${produkData.id_produk}][jumlah]" 
                       class="form-control jumlah" value="0" 
                       min="1" max="${produkData.stok}" required>
            </td>
            <td>
                <input type="text" class="form-control subtotal" value="Rp 0" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs btn-remove">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#tabel-produk tbody').append(newRow);
}

function calculateSubtotal(row) {
    const harga = parseInt(row.find('.harga-jual').val()) || 0;
    const jumlah = parseInt(row.find('.jumlah').val()) || 0;
    const subtotal = harga * jumlah;
    
    row.find('.subtotal').val('Rp ' + subtotal.toLocaleString('id-ID'));
}

function calculateTotal() {
    let total = 0;
    
    $('.produk-row').each(function() {
        const harga = parseInt($(this).find('.harga-jual').val()) || 0;
        const jumlah = parseInt($(this).find('.jumlah').val()) || 0;
        total += harga * jumlah;
    });
    
    $('#total-harga').text('Rp ' + total.toLocaleString('id-ID'));
}

$('#form-laporan').on('submit', function(e) {
    e.preventDefault();
    
    // Validasi minimal ada 1 produk dengan jumlah > 0
    let hasSales = false;
    $('input[name*="[jumlah]"]').each(function() {
        if (parseInt($(this).val()) > 0) {
            hasSales = true;
            return false;
        }
    });
    
    if (!hasSales) {
        alert('Minimal harus ada 1 produk dengan jumlah terjual lebih dari 0');
        return;
    }
    
    // Validasi stok tidak boleh melebihi stok tersedia
    let stockValid = true;
    $('.produk-row').each(function() {
        const maxStock = parseInt($(this).find('td:eq(3)').text());
        const jumlah = parseInt($(this).find('.jumlah').val()) || 0;
        
        if (jumlah > maxStock) {
            stockValid = false;
            alert('Jumlah melebihi stok tersedia untuk produk: ' + $(this).find('td:eq(2)').text());
            return false;
        }
    });
    
    if (!stockValid) return;
    
    // Tampilkan loading
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
    
    $.ajax({
        url: '{{ route("agen_laporan.store", $agen->id_member) }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                alert(response.message);
                window.location.href = '{{ route("agen_laporan.index", $agen->id_member) }}';
            } else {
                alert(response.message);
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Laporan');
            }
        },
        error: function(xhr) {
            alert('Gagal menyimpan laporan: ' + (xhr.responseJSON?.message || xhr.responseText));
            submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Laporan');
        }
    });
});
</script>
@endpush
