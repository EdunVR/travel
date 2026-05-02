@extends('app')

@section('title')
    Buat PO Penjualan Baru
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('po-penjualan.index') }}">PO Penjualan</a></li>
    <li class="active">Buat Baru</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <form id="poForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="no_po">No. PO *</label>
                                <input type="text" name="no_po" id="no_po" class="form-control" 
                                       value="{{ $noPO }}" readonly required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal">Tanggal *</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_outlet">Outlet *</label>
                                <select name="id_outlet" id="id_outlet" class="form-control" required>
                                    <option value="">Pilih Outlet</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">
                                            {{ $outlet->nama_outlet }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_member">Customer *</label>
                                <select name="id_member" id="id_member" class="form-control" required>
                                    <option value="">Pilih Customer</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id_member }}">
                                            {{ $member->nama }} - {{ $member->telepon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="diskon">Diskon (%)</label>
                                <input type="number" name="diskon" id="diskon" class="form-control" 
                                       value="0" min="0" max="100" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tanggal_tempo">Tanggal Tempo</label>
                                <input type="date" name="tanggal_tempo" id="tanggal_tempo" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h4>Detail Items</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="25%">Produk/Ongkir</th>
                                            <th width="15%">Harga</th>
                                            <th width="10%">Jumlah</th>
                                            <th width="10%">Diskon (%)</th>
                                            <th width="15%">Subtotal</th>
                                            <th width="10%">Tipe</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Items will be added here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>Total Harga:</strong></td>
                                            <td><strong id="totalHarga">0</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>Total Ongkir:</strong></td>
                                            <td><strong id="totalOngkir">0</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr class="info">
                                            <td colspan="5" class="text-right"><strong>Total Bayar:</strong></td>
                                            <td><strong id="totalBayar">0</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <button type="button" id="addProduct" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Tambah Produk
                            </button>
                            <button type="button" id="addOngkir" class="btn btn-info btn-sm">
                                <i class="fa fa-truck"></i> Tambah Ongkir
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="diterima">Diterima</label>
                                <input type="number" name="diterima" id="diterima" class="form-control" 
                                       value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="menunggu" selected>Menunggu</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="gagal">Gagal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Simpan PO
                            </button>
                            <a href="{{ route('po-penjualan.index') }}" class="btn btn-default">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilih Produk</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="productTable">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produk as $item)
                            <tr>
                                <td>{{ $item->kode_produk }}</td>
                                <td>{{ $item->nama_produk }}</td>
                                <td>{{ format_uang($item->harga_jual) }}</td>
                                <td>{{ $item->stok }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm select-product" 
                                            data-id="{{ $item->id_produk }}"
                                            data-kode="{{ $item->kode_produk }}"
                                            data-nama="{{ $item->nama_produk }}"
                                            data-harga="{{ $item->harga_jual }}"
                                            data-diskon="{{ $item->diskon }}"
                                            data-stok="{{ $item->stok }}">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemCounter = 0;

$(document).ready(function() {
    // Initialize product table search
    $('#productTable').DataTable({
        "pageLength": 5,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });

    // Add Product Button
    $('#addProduct').click(function() {
        $('#productModal').modal('show');
    });

    // Add Ongkir Button
    $('#addOngkir').click(function() {
        addOngkirRow();
    });

    // Select Product from Modal
    $(document).on('click', '.select-product', function() {
        const productId = $(this).data('id');
        const productKode = $(this).data('kode');
        const productNama = $(this).data('nama');
        const productHarga = $(this).data('harga');
        const productDiskon = $(this).data('diskon');
        const productStok = $(this).data('stok');

        addProductRow(productId, productKode, productNama, productHarga, productDiskon, productStok);
        $('#productModal').modal('hide');
    });

    // Calculate totals when input changes
    $(document).on('input', '.item-jumlah, .item-diskon', function() {
        calculateRowTotal($(this).closest('tr'));
        calculateTotals();
    });

    $(document).on('input', '.item-harga', function() {
        const rawValue = unformatRupiah($(this).val());
        const formatted = formatRupiahInput(rawValue.toString());
        $(this).val(formatted);
        $(this).data('raw-value', rawValue);
        
        calculateRowTotal($(this).closest('tr'));
        calculateTotals();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        renumberItems();
        calculateTotals();
    });

    // Form submission
    $('#poForm').submit(function(e) {
        e.preventDefault();
        savePO();
    });

    // Auto-calculate when diterima changes
    $('#diterima').on('input', function() {
        calculateTotals();
    });

    $(document).on('focus', '.item-harga', function() {
        const rawValue = $(this).data('raw-value') || unformatRupiah($(this).val());
        $(this).val(rawValue.toString().replace('.', ','));
    });

    $(document).on('blur', '.item-harga', function() {
        const rawValue = $(this).data('raw-value') || unformatRupiah($(this).val());
        const formatted = formatRupiahInput(rawValue.toString());
        $(this).val(formatted);
    });
    
});

function savePO() {
    // Validasi minimal ada satu produk (bukan ongkir)
    let hasProduct = false;
    $('.item-row').each(function() {
        if ($(this).data('item-type') === 'produk') {
            hasProduct = true;
        }
    });

    if (!hasProduct) {
        alert('Tambah minimal satu item produk');
        return;
    }

    const formData = new FormData($('#poForm')[0]);
    
    // Convert items array properly dengan nilai tanpa format
    const items = [];
    $('.item-row').each(function(index) {
        const row = $(this);
        
        // Pastikan mengambil nilai raw-value untuk harga - hanya dari satu sumber
        let rawHarga;
        if (row.find('.item-harga').data('raw-value') !== undefined) {
            rawHarga = parseFloat(row.find('.item-harga').data('raw-value'));
        } else {
            rawHarga = unformatRupiah(row.find('.item-harga').val()) || 0;
        }
        
        const item = {
            id_produk: row.find('input[name*="[id_produk]"]').val(),
            is_ongkir: row.find('input[name*="[is_ongkir]"]').val(),
            harga_jual: rawHarga,
            jumlah: parseInt(row.find('.item-jumlah').val()) || 0,
            diskon: parseFloat(row.find('.item-diskon').val()) || 0,
            keterangan: row.find('input[name*="[keterangan]"]').val() || ''
        };
        
        // Debug log untuk troubleshooting
        console.log(`Item ${index}:`, {
            type: row.data('item-type'),
            harga_jual: item.harga_jual,
            rawHarga: rawHarga,
            elementValue: row.find('.item-harga').val(),
            dataRawValue: row.find('.item-harga').data('raw-value')
        });
        
        items.push(item);
    });
    
    // Clear existing items and add new ones
    formData.delete('items');
    items.forEach((item, index) => {
        formData.append(`items[${index}][id_produk]`, item.id_produk);
        formData.append(`items[${index}][is_ongkir]`, item.is_ongkir);
        formData.append(`items[${index}][harga_jual]`, item.harga_jual);
        formData.append(`items[${index}][jumlah]`, item.jumlah);
        formData.append(`items[${index}][diskon]`, item.diskon);
        formData.append(`items[${index}][keterangan]`, item.keterangan);
    });

    // Show loading
    const submitBtn = $('#poForm').find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

    // Tambahkan debug log sebelum submit
    console.log('Data yang akan dikirim:', {
        items: items,
        totalItems: items.length
    });

    $.ajax({
        url: '{{ route("po-penjualan.store") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = response.redirect;
                });
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html(originalText);
            
            let message = 'Terjadi kesalahan saat menyimpan PO';
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).join('\n');
            } else if (xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
            }
            
            Swal.fire('Error!', message, 'error');
        }
    });
}

function addProductRow(productId, productKode, productNama, productHarga, productDiskon, productStok) {
    itemCounter++;
    
    const row = `
        <tr class="item-row" data-item-type="produk">
            <td class="text-center">${itemCounter}</td>
            <td>
                <input type="hidden" name="items[${itemCounter}][id_produk]" value="${productId}">
                <input type="hidden" name="items[${itemCounter}][is_ongkir]" value="0">
                <strong>${productKode}</strong><br>
                ${productNama}
                <small class="text-muted d-block">Stok: ${productStok}</small>
            </td>
            <td>
                <input type="text" name="items[${itemCounter}][harga_jual]" 
                       class="form-control form-control-sm item-harga" 
                       value="${formatRupiahInput(productHarga.toString())}" 
                       data-raw-value="${productHarga}"
                       required>
            </td>
            <td>
                <input type="number" name="items[${itemCounter}][jumlah]" 
                       class="form-control form-control-sm item-jumlah" 
                       value="1" min="1" max="${productStok}" required>
            </td>
            <td>
                <input type="number" name="items[${itemCounter}][diskon]" 
                       class="form-control form-control-sm item-diskon" 
                       value="${productDiskon}" min="0" max="100" step="0.01">
            </td>
            <td>
                <input type="text" name="items[${itemCounter}][subtotal]" 
                       class="form-control form-control-sm item-subtotal" 
                       value="${formatRupiah(productHarga)}" 
                       data-raw-value="${productHarga}"
                       readonly>
            </td>
            <td class="text-center">
                <span class="label label-primary">Produk</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#itemsTable tbody').append(row);
    calculateTotals();
}

function addOngkirRow() {
    itemCounter++;
    
    const row = `
        <tr class="item-row" data-item-type="ongkir">
            <td class="text-center">${itemCounter}</td>
            <td>
                <input type="hidden" name="items[${itemCounter}][id_produk]" value="">
                <input type="hidden" name="items[${itemCounter}][is_ongkir]" value="1">
                <input type="text" name="items[${itemCounter}][keterangan]" 
                       class="form-control form-control-sm" 
                       placeholder="Keterangan ongkir" value="Ongkos Kirim">
            </td>
            <td>
                <input type="text" name="items[${itemCounter}][harga_jual]" 
                       class="form-control form-control-sm item-harga" 
                       value="0" 
                       data-raw-value="0"
                       required>
            </td>
            <td>
                <input type="number" name="items[${itemCounter}][jumlah]" 
                       class="form-control form-control-sm item-jumlah" 
                       value="1" min="1" readonly>
            </td>
            <td>
                <input type="number" name="items[${itemCounter}][diskon]" 
                       class="form-control form-control-sm item-diskon" 
                       value="0" min="0" max="100" step="0.01" readonly>
            </td>
            <td>
                <input type="text" name="items[${itemCounter}][subtotal]" 
                       class="form-control form-control-sm item-subtotal" 
                       value="0" 
                       data-raw-value="0"
                       readonly>
            </td>
            <td class="text-center">
                <span class="label label-info">Ongkir</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#itemsTable tbody').append(row);
    
    // Format input harga setelah row ditambahkan
    const newRow = $('#itemsTable tbody tr:last');
    newRow.find('.item-harga').val(formatRupiahInput('0'));
    
    calculateTotals();
}

function calculateRowTotal(row) {
    const harga = parseFloat(row.find('.item-harga').data('raw-value')) || 
                  unformatRupiah(row.find('.item-harga').val()) || 0;
    const jumlah = parseInt(row.find('.item-jumlah').val()) || 0;
    const diskon = parseFloat(row.find('.item-diskon').val()) || 0;
    
    const subtotal = harga * jumlah;
    const subtotalSetelahDiskon = subtotal - (subtotal * diskon / 100);
    
    // Simpan nilai raw untuk perhitungan
    row.find('.item-subtotal').data('raw-value', subtotalSetelahDiskon);
    
    // Tampilkan dengan format
    row.find('.item-subtotal').val(formatRupiah(subtotalSetelahDiskon));
}

// Update calculateTotals untuk menggunakan nilai raw
function calculateTotals() {
    let totalHarga = 0;
    let totalOngkir = 0;
    
    $('.item-row').each(function() {
        const subtotal = parseFloat($(this).find('.item-subtotal').data('raw-value')) || 0;
        const isOngkir = $(this).data('item-type') === 'ongkir';
        
        if (isOngkir) {
            totalOngkir += subtotal;
        } else {
            totalHarga += subtotal;
        }
    });
    
    const diskonGlobal = parseFloat($('#diskon').val()) || 0;
    const totalSetelahDiskon = totalHarga - (totalHarga * diskonGlobal / 100);
    const totalBayar = totalSetelahDiskon + totalOngkir;
    const diterima = parseFloat($('#diterima').val()) || 0;
    
    // Update display dengan format
    $('#totalHarga').text(formatRupiah(totalHarga));
    $('#totalOngkir').text(formatRupiah(totalOngkir));
    $('#totalBayar').text(formatRupiah(totalBayar));
    
    // Update kembalian jika diperlukan
    if (diterima > 0) {
        const kembalian = diterima - totalBayar;
        $('#kembalian').text(formatRupiah(kembalian));
    }
}

function renumberItems() {
    itemCounter = 0;
    $('.item-row').each(function(index) {
        itemCounter++;
        $(this).find('td:first').text(itemCounter);
        
        // Update input names
        $(this).find('input').each(function() {
            const name = $(this).attr('name').replace(/\[\d+\]/, '[' + itemCounter + ']');
            $(this).attr('name', name);
        });
    });
}

function formatRupiahInput(angka, prefix = '') {
    if (!angka && angka !== 0) return '';
    
    // Hapus semua karakter non-digit kecuali koma untuk desimal
    let number_string = angka.toString().replace(/[^,\d]/g, '');
    
    // Pisahkan bagian desimal jika ada
    const split = number_string.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    // Tambahkan bagian desimal jika ada
    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    
    return prefix === '' ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

function unformatRupiah(rupiah) {
    if (!rupiah) return 0;
    
    // Hapus "Rp. " dan spasi jika ada
    let clean = rupiah.toString().replace(/\s?Rp\s?/g, '');
    
    // Ganti titik (pemisah ribuan) dengan string kosong
    clean = clean.replace(/\./g, '');
    
    // Ganti koma (pemisah desimal) dengan titik
    clean = clean.replace(/,/g, '.');
    
    // Parse ke float
    return parseFloat(clean) || 0;
}

// Format untuk display saja (tanpa input)
function formatRupiah(angka) {
    if (!angka && angka !== 0) return 'Rp 0';
    
    const number_string = Math.floor(angka).toString();
    const sisa = number_string.length % 3;
    let rupiah = number_string.substr(0, sisa);
    const ribuan = number_string.substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    return 'Rp ' + rupiah;
}

</script>
@endpush
