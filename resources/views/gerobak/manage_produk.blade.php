<div class="modal fade" id="modal-manage-produk" tabindex="-1" role="dialog" aria-labelledby="modal-manage-produk">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Kelola Produk di Gerobak: <span id="nama-gerobak"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="produk-select">Pilih Produk</label>
                            <select id="produk-select" class="form-control">
                                <option value="">Loading produk...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="stok">Jumlah Stok</label>
                            <input type="number" id="stok" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="visibility: hidden;">Aksi</label><br>
                            <button id="tambah-produk" class="btn btn-primary btn-block" disabled>
                                <i class="fa fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <hr>
                
                <h5>Daftar Produk di Gerobak</h5>
                <table class="table table-striped table-bordered table-produk-gerobak">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="produk-list">
                        <tr>
                            <td colspan="5" class="text-center">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanPerubahan()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script>

// Function untuk membuka modal manage produk
function manageProduk(url) {
    currentGerobakId = url.split('/').pop(); // Ambil ID gerobak dari URL
    const gerobakId = currentGerobakId;
    
    // Tampilkan modal
    $('#modal-manage-produk').modal('show');
    
    // Reset state
    $('#produk-select').html('<option value="">Loading produk...</option>');
    $('#tambah-produk').prop('disabled', true);
    $('#produk-list').html('<tr><td colspan="5" class="text-center">Loading data...</td></tr>');
    
    // Load data gerobak dan produk
    $.get(url)
        .done(function(response) {
            // Set nama gerobak
            $('#nama-gerobak').text(response.gerobak.nama_gerobak);
            
            // Simpan data produk yang tersedia
            availableProduk = response.produk;
            
            // Isi dropdown produk
            populateProdukDropdown(availableProduk);
            
            // Load produk yang sudah ada di gerobak
            loadProdukGerobak(gerobakId);
        })
        .fail(function(error) {
            console.error('Error loading gerobak data:', error);
            alert('Gagal memuat data gerobak');
        });
}

// Function untuk mengisi dropdown produk
function populateProdukDropdown(produkList) {
    const select = $('#produk-select');
    select.empty();
    select.append('<option value="">Pilih Produk</option>');
    
    produkList.forEach(produk => {
        select.append(
            `<option value="${produk.id_produk}" data-harga="${produk.harga_jual}">
                ${produk.kode_produk} - ${produk.nama_produk} (Rp ${formatRupiah(produk.harga_jual)})
            </option>`
        );
    });
    
    $('#tambah-produk').prop('disabled', false);
}

// Function format rupiah
function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Function untuk load produk yang ada di gerobak
function loadProdukGerobak(gerobakId) {
    const url = '{{ route("agen_gerobak.gerobak.get-produk", ":gerobakId") }}'.replace(':gerobakId', gerobakId);
    $.get(url)
        .done(function(response) {
            produkGerobak = response.data;
            renderProdukList();
        })
        .fail(function(error) {
            console.error('Error loading produk gerobak:', error);
            $('#produk-list').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data produk</td></tr>');
        });
}

// Function untuk render daftar produk
function renderProdukList() {
    const tbody = $('#produk-list');
    tbody.empty();
    
    if (produkGerobak.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center">Tidak ada produk</td></tr>');
        return;
    }
    
    produkGerobak.forEach((produk, index) => {
        tbody.append(`
            <tr data-produk-id="${produk.id_produk}">
                <td>${index + 1}</td>
                <td>${produk.kode_produk}</td>
                <td>${produk.nama_produk}</td>
                <td>
                    <input type="number" class="form-control stok-input" 
                           value="${produk.pivot.stok}" 
                           data-produk-id="${produk.id_produk}"
                           min="0">
                </td>
                <td>
                    <button class="btn btn-danger btn-xs" 
                            onclick="hapusProduk(${produk.id_produk})">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                </td>
            </tr>
        `);
    });
}

// Function untuk tambah produk
$('#tambah-produk').on('click', function() {
    const produkId = $('#produk-select').val();
    const stok = $('#stok').val();
    
    if (!produkId) {
        alert('Pilih produk terlebih dahulu');
        return;
    }
    
    if (stok < 1) {
        alert('Stok harus lebih dari 0');
        return;
    }
    
    // Cek apakah produk sudah ada
    const existingIndex = produkGerobak.findIndex(p => p.id_produk == produkId);
    
    if (existingIndex >= 0) {
        // Update stok jika produk sudah ada
        produkGerobak[existingIndex].pivot.stok = parseInt(stok);
    } else {
        // Tambahkan produk baru
        const selectedProduk = availableProduk.find(p => p.id_produk == produkId);
        if (selectedProduk) {
            produkGerobak.push({
                ...selectedProduk,
                pivot: { stok: parseInt(stok) }
            });
        }
    }
    
    renderProdukList();
    
    // Reset form
    $('#produk-select').val('');
    $('#stok').val('0');
});

// Function untuk hapus produk
function hapusProduk(produkId) {
    if (confirm('Yakin ingin menghapus produk ini?')) {
        produkGerobak = produkGerobak.filter(p => p.id_produk != produkId);
        renderProdukList();
    }
}

// Function untuk simpan perubahan
function simpanPerubahan() {
    if (!currentGerobakId) return;
    
    // Kumpulkan data untuk dikirim
    const dataToSend = produkGerobak.map(produk => ({
        id_produk: produk.id_produk,
        stok: produk.pivot.stok
    }));
    
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
     const url = '{{ route("agen_gerobak.gerobak.update-produk", ":gerobakId") }}'.replace(':gerobakId', currentGerobakId);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: csrfToken,
            produk: dataToSend
        },
        success: function(response) {
            alert('Data produk berhasil disimpan');
            $('#modal-manage-produk').modal('hide');
            
            // Refresh tabel gerobak jika ada
            if (typeof tableGerobak !== 'undefined') {
                tableGerobak.ajax.reload();
            }
        },
        error: function(xhr) {
            console.error('Error saving produk:', xhr);
            alert('Gagal menyimpan data produk');
        }
    });
}

// Handle perubahan stok langsung dari input
$(document).on('change', '.stok-input', function() {
    const produkId = $(this).data('produk-id');
    const stok = $(this).val();
    
    const produk = produkGerobak.find(p => p.id_produk == produkId);
    if (produk) {
        produk.pivot.stok = parseInt(stok);
    }
});

// Initialize ketika modal dibuka
$('#modal-manage-produk').on('shown.bs.modal', function() {
    $('#produk-select').focus();
});
</script>
