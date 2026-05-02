<style>
    @media (max-width: 768px) {
    /* Modal adjustments for mobile */
    .modal-dialog {
        margin: 10px;
        width: auto;
        max-width: 100%;
    }
    
    .modal-content {
        border-radius: 8px;
        max-height: 90vh;
        overflow: hidden;
    }
    
    .modal-body {
        padding: 15px;
        max-height: 60vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 10px 15px;
    }
    
    .modal-footer {
        padding: 10px 15px;
    }
    
    /* Table responsive dalam modal */
    .modal .table-responsive {
        display: block !important;
        max-height: 50vh;
        overflow-y: auto;
    }
    
    .modal table {
        font-size: 12px;
    }
    
    .modal table th,
    .modal table td {
        padding: 6px 4px;
        white-space: nowrap;
    }
    
    /* Input group dalam modal */
    .modal .input-group {
        flex-wrap: nowrap;
    }
    
    .modal .input-group .form-control {
        font-size: 14px;
    }
    
    /* Loading dan empty states */
    #customer-search-loading,
    #sparepart-search-loading,
    #customer-search-empty,
    #sparepart-search-empty {
        padding: 15px;
        font-size: 14px;
    }
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1040;
    width: 100vw;
    height: 100vh;
    background-color: #000;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100%;
    height: 100%;
    overflow: hidden;
    outline: 0;
}

    @media (min-width: 769px) {
        .desktop-btn, 
        .desktop-title,
        .desktop-actions {
            display: block !important;
        }
        
        .mobile-action-buttons,
        .mobile-title,
        .mobile-only,
        .mobile-items-list,
        .mobile-summary,
        .mobile-actions {
            display: none !important;
        }
        
        .table-responsive {
            display: block !important;
        }
    }
</style>

@extends('app')

@section('title')
    Buat Invoice Service
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Buat Invoice Service</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="mobile-action-buttons" style="display: none; margin-bottom: 15px;">
                    <a href="{{ route('service.invoice.history') }}" class="btn btn-info btn-block btn-sm" style="margin-bottom: 10px;">
                        <i class="fa fa-arrow-left"></i> Kembali ke History
                    </a>
                </div>
                
                <a href="{{ route('service.invoice.history') }}" class="btn btn-success btn-sm desktop-btn" style="margin-right: 10px;">
                    <i class="fa fa-list"></i> History Invoice
                </a>
                
                <form id="invoice-form">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_member">Customer</label>
                                <div class="input-group">
                                    <input type="text" id="customer-display" class="form-control" placeholder="Pilih customer..." readonly>
                                    <input type="hidden" name="id_member" id="id_member">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary" id="btn-cari-customer">
                                            <i class="fa fa-search"></i> Cari
                                        </button>
                                    </span>
                                </div>
                                <small class="text-muted">Klik tombol cari untuk memilih customer</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_mesin_customer">Mesin Customer</label>
                                <select name="id_mesin_customer" id="id_mesin_customer" class="form-control" required disabled>
                                    <option value="">Pilih Customer Terlebih Dahulu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jenis_service">Jenis Service</label>
                                <select name="jenis_service" id="jenis_service" class="form-control" required disabled>
                                    <option value="">Pilih Jenis Service</option>
                                    <option value="Service">Service</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Pembelian Sparepart">Pembelian Sparepart</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="keterangan_service">Keterangan Service</label>
                                <textarea name="keterangan_service" id="keterangan_service" class="form-control" rows="2" placeholder="Tambahkan keterangan detail tentang service yang dilakukan..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal">Tanggal Invoice</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_mulai_service">Tanggal Mulai Service</label>
                                <input type="date" name="tanggal_mulai_service" id="tanggal_mulai_service" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_selesai_service">Tanggal Selesai Service</label>
                                <input type="date" name="tanggal_selesai_service" id="tanggal_selesai_service" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 25px;">
                                    <label>
                                        <input type="checkbox" name="is_garansi" id="is_garansi" value="1"> 
                                        <strong>Garansi</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_service_berikutnya">Tanggal Service Berikutnya (Opsional)</label>
                                <input type="date" name="tanggal_service_berikutnya" id="tanggal_service_berikutnya" class="form-control">
                                <small class="text-muted">Isi jika ada jadwal service berikutnya</small>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Action Buttons -->
                    <div class="mobile-only" style="display: none; margin-bottom: 15px;">
                        <div class="btn-group-mobile">
                            <button type="button" class="btn btn-info btn-mobile" id="add-sparepart-mobile">
                                <i class="fa fa-cog"></i> Sparepart
                            </button>
                            <button type="button" class="btn btn-info btn-mobile" id="add-item-mobile">
                                <i class="fa fa-plus"></i> Item Baru
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h4 class="desktop-title">Detail Invoice</h4>
                            <h4 class="mobile-title" style="display: none;">Detail Invoice</h4>
                            
                            <!-- Desktop Action Buttons -->
                            <div class="desktop-actions" style="margin-bottom: 15px;">
                                <button type="button" class="btn btn-info" id="add-sparepart">
                                    <i class="fa fa-cog"></i> Tambah Sparepart
                                </button>
                                <button type="button" class="btn btn-info" id="add-item">
                                    <i class="fa fa-cog"></i> Tambah Item (FreeText)
                                </button>
                            </div>
                            
                            <!-- Desktop Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="invoice-items">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Deskripsi</th>
                                            <th width="15%">Keterangan</th>
                                            <th width="8%">Kuantitas</th>
                                            <th width="8%">Satuan</th>
                                            <th width="12%">Harga</th>
                                            <th width="10%">Diskon</th>
                                            <th width="12%">Subtotal</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Items will be added automatically based on mesin customer selection -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" rowspan="4">
                                                <div class="form-group" style="margin-bottom: 5px;">
                                                    <label for="diskon" style="margin-bottom: 0;">Diskon (Rp)</label>
                                                    <input type="text" name="diskon" id="diskon" class="form-control" value="0" onblur="formatCurrency(this)">
                                                </div>
                                            </td>
                                            <td class="text-right"><strong>Subtotal</strong></td>
                                            <td><strong id="subtotal-amount">0</strong></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><strong>Diskon</strong></td>
                                            <td><strong id="diskon-amount">0</strong></td>
                                            <td></td>
                                        </tr>
                                        <tr class="total-row">
                                            <td class="text-right"><strong>TOTAL</strong></td>
                                            <td><strong id="total-amount">0</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <!-- Mobile Items List -->
                            <div class="mobile-items-list" id="mobile-items-list" style="display: none;">
                                <!-- Items will be displayed here on mobile -->
                            </div>
                            
                            <!-- Mobile Summary -->
                            <div class="mobile-summary" id="mobile-summary" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 15px;">
                                <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span>Subtotal:</span>
                                    <span id="mobile-subtotal">0</span>
                                </div>
                                <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span>Diskon:</span>
                                    <span id="mobile-diskon">0</span>
                                </div>
                                <div class="summary-row" style="display: flex; justify-content: space-between; font-weight: bold; font-size: 16px; padding-top: 8px; border-top: 1px solid #dee2e6;">
                                    <span>TOTAL:</span>
                                    <span id="mobile-total">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3" id="teknisi-section" style="display: none;">
                        <div class="col-md-12">
                            <h4>Biaya Teknisi</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="jumlah_teknisi">Jumlah Teknisi</label>
                                        <input type="number" name="jumlah_teknisi" id="jumlah_teknisi" class="form-control" min="0" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="jumlah_jam">Jumlah Jam</label>
                                        <input type="number" name="jumlah_jam" id="jumlah_jam" class="form-control" min="0" value="0" step="0.5">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="biaya_teknisi">Biaya per Jam</label>
                                        <input type="text" class="form-control" value="Rp 25.000" readonly>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="biaya_teknisi" id="biaya_teknisi" value="0">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="desktop-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Simpan Invoice
                                </button>
                                <button type="button" class="btn btn-default" onclick="resetForm()">
                                    <i class="fa fa-refresh"></i> Reset Form
                                </button>
                            </div>
                            
                            <div class="mobile-actions" style="display: none;">
                                <button type="submit" class="btn btn-success btn-block" style="margin-bottom: 10px;">
                                    <i class="fa fa-save"></i> Simpan Invoice
                                </button>
                                <button type="button" class="btn btn-default btn-block" onclick="resetForm()">
                                    <i class="fa fa-refresh"></i> Reset Form
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pencarian Customer -->
<div class="modal fade" id="modal-cari-customer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-search"></i> Pencarian Customer
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" id="search-customer" class="form-control" placeholder="Cari berdasarkan nama, alamat, atau telepon...">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" id="btn-search-customer">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-customer-search">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Nama Customer</th>
                                <th width="30%">Alamat</th>
                                <th width="15%">Telepon</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customer-search-results">
                            <!-- Results will be populated here -->
                        </tbody>
                    </table>
                </div>

                <div id="customer-search-loading" style="display: none; text-align: center; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Mencari customer...</p>
                </div>

                <div id="customer-search-empty" style="display: none; text-align: center; padding: 20px;">
                    <i class="fa fa-info-circle fa-2x" style="color: #6c757d;"></i>
                    <p>Tidak ada customer ditemukan</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Sparepart -->
<div class="modal fade" id="modal-pilih-sparepart" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-cog"></i> Pilih Sparepart
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" id="search-sparepart" class="form-control" placeholder="Cari sparepart berdasarkan kode, nama, atau merk...">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" id="btn-search-sparepart">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Kode</th>
                                <th>Nama Sparepart</th>
                                <th>Merk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sparepart-search-results">
                            <!-- Results will be populated here -->
                        </tbody>
                    </table>
                </div>

                <div id="sparepart-search-loading" style="display: none; text-align: center; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Mencari sparepart...</p>
                </div>

                <div id="sparepart-search-empty" style="display: none; text-align: center; padding: 20px;">
                    <i class="fa fa-info-circle fa-2x" style="color: #6c757d;"></i>
                    <p>Tidak ada sparepart ditemukan</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Item Mobile -->
<div class="modal fade" id="modal-edit-item-mobile" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-edit"></i> Edit Item
                </h4>
            </div>
            <div class="modal-body">
                <form id="form-edit-item-mobile">
                    <input type="hidden" id="edit-item-index">
                    
                    <div class="form-group">
                        <label for="edit-deskripsi">Deskripsi</label>
                        <input type="text" id="edit-deskripsi" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-keterangan">Keterangan</label>
                        <input type="text" id="edit-keterangan" class="form-control">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-kuantitas">Kuantitas</label>
                                <input type="number" id="edit-kuantitas" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-satuan">Satuan</label>
                                <input type="text" id="edit-satuan" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-harga">Harga</label>
                                <input type="text" id="edit-harga" class="form-control" onblur="formatCurrency(this)" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-diskon">Diskon</label>
                                <input type="text" id="edit-diskon" class="form-control" onblur="formatCurrency(this)" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-tipe">Tipe Item</label>
                        <select id="edit-tipe" class="form-control">
                            <option value="lainnya">Lainnya</option>
                            <option value="produk">Produk</option>
                            <option value="ongkir">Ongkos Kirim</option>
                            <option value="sparepart">Sparepart</option>
                            <option value="teknisi">Teknisi</option>
                        </select>
                    </div>
                    
                    <div id="edit-jenis-kendaraan-container" style="display: none;">
                        <div class="form-group">
                            <label for="edit-jenis-kendaraan">Jenis Kendaraan</label>
                            <select id="edit-jenis-kendaraan" class="form-control">
                                <option value="mobil">Mobil</option>
                                <option value="motor">Motor</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-edit-item">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    function searchCustomers(searchTerm = '') {
        const loadingElement = $('#customer-search-loading');
        const emptyElement = $('#customer-search-empty');
        const resultsElement = $('#customer-search-results');

        // Show loading, hide others
        loadingElement.show();
        emptyElement.hide();
        resultsElement.hide();


        $.ajax({
            url: `${baseUrl}/service-management/invoice/search-customers`,
            type: 'GET',
            data: {
                search: searchTerm
            },
            success: function(response) {
                loadingElement.hide();

                if (response.success && response.customers.length > 0) {
                    displayCustomerResults(response.customers);
                    resultsElement.show();
                } else {
                    emptyElement.show();
                    resultsElement.hide();
                }
            },
            error: function(error) {
                loadingElement.hide();
                emptyElement.show();
                console.error('Error searching customers:', error);
                alert('Terjadi kesalahan saat mencari customer');
            }
        });
    }

    function checkScreenSize() {
        if ($(window).width() <= 768) {
            $('.desktop-btn, .desktop-title, .desktop-actions').hide();
            $('.mobile-action-buttons, .mobile-title, .mobile-only, .mobile-actions').show();
            $('.table-responsive').hide();
            $('.mobile-items-list').show();
            renderMobileItems();
        } else {
            $('.desktop-btn, .desktop-title, .desktop-actions').show();
            $('.mobile-action-buttons, .mobile-title, .mobile-only, .mobile-actions').hide();
            $('.table-responsive').show();
            $('.mobile-items-list').hide();
        }
    }

    function renderMobileItems() {
        const container = $('#mobile-items-list');
        container.empty();
        
        let hasItems = false;
        
        $('#invoice-items tbody tr').each(function(index) {
            hasItems = true;
            const row = $(this);
            const deskripsi = row.find('input[name="items[deskripsi][]"]').val();
            const keterangan = row.find('input[name="items[keterangan][]"]').val() || 
                             (row.find('select[name="items[jenis_kendaraan][]"]').length ? 
                              'Menggunakan ' + row.find('select[name="items[jenis_kendaraan][]"]').val() : '-');
            const kuantitas = row.find('input[name="items[kuantitas][]"]').val();
            const satuan = row.find('input[name="items[satuan][]"]').val();
            const harga = row.find('input[name="items[harga][]"]').val();
            const diskon = row.find('input[name="items[diskon][]"]').val() || '0';
            const subtotal = row.find('input[name="items[subtotal][]"]').val();
            const tipe = row.find('input[name="items[tipe][]"]').val();
            const isOngkosKirim = tipe === 'ongkir';
            
            const typeBadge = getTypeBadge(tipe);
            
            const itemHtml = `
                <div class="item-card" data-index="${index}">
                    <div class="item-header">
                        <div class="item-title">${deskripsi}</div>
                        <div class="item-type">${typeBadge}</div>
                    </div>
                    
                    <div class="item-detail-row">
                        <div class="item-detail-label">Keterangan</div>
                        <div class="item-detail-value">${keterangan}</div>
                    </div>
                    
                    <div class="item-detail-row">
                        <div class="item-detail-label">Qty</div>
                        <div class="item-detail-value">${kuantitas} ${satuan}</div>
                    </div>
                    
                    <div class="item-detail-row">
                        <div class="item-detail-label">Harga</div>
                        <div class="item-detail-value">${harga}</div>
                    </div>
                    
                    <div class="item-detail-row">
                        <div class="item-detail-label">Diskon</div>
                        <div class="item-detail-value">${diskon}</div>
                    </div>
                    
                    <div class="item-detail-row" style="font-weight: bold;">
                        <div class="item-detail-label">Subtotal</div>
                        <div class="item-detail-value">${subtotal}</div>
                    </div>
                    
                    <div class="item-actions">
                        <button type="button" class="btn btn-warning btn-mobile-sm" onclick="editMobileItem(${index})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-mobile-sm" onclick="removeMobileItem(${index})">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            `;
            
            container.append(itemHtml);
        });
        
        if (!hasItems) {
            container.html('<div class="text-center" style="padding: 20px; color: #666;">Belum ada items. Tambah item menggunakan tombol di atas.</div>');
        }
        
        // Show/hide summary
        if (hasItems) {
            $('#mobile-summary').show();
            updateMobileSummary();
        } else {
            $('#mobile-summary').hide();
        }
    }

    function getTypeBadge(tipe) {
        const types = {
            'produk': 'Produk',
            'ongkir': 'Ongkir',
            'sparepart': 'Sparepart',
            'teknisi': 'Teknisi',
            'lainnya': 'Lainnya'
        };
        return types[tipe] || tipe;
    }

    function updateMobileSummary() {
        const subtotal = $('#subtotal-amount').text();
        const diskon = $('#diskon-amount').text();
        const total = $('#total-amount').text();
        
        $('#mobile-subtotal').text(subtotal);
        $('#mobile-diskon').text(diskon);
        $('#mobile-total').text(total);
    }

    // Function to edit item in mobile
    function editMobileItem(index) {
        const row = $('#invoice-items tbody tr').eq(index);
        
        // Get current values
        const deskripsi = row.find('input[name="items[deskripsi][]"]').val();
        const keterangan = row.find('input[name="items[keterangan][]"]').val();
        const kuantitas = row.find('input[name="items[kuantitas][]"]').val();
        const satuan = row.find('input[name="items[satuan][]"]').val();
        const harga = row.find('input[name="items[harga][]"]').val();
        const diskon = row.find('input[name="items[diskon][]"]').val() || '0';
        const tipe = row.find('input[name="items[tipe][]"]').val();
        const isOngkosKirim = tipe === 'ongkir';
        const jenisKendaraan = isOngkosKirim ? row.find('select[name="items[jenis_kendaraan][]"]').val() : '';
        
        // Set values in modal
        $('#edit-item-index').val(index);
        $('#edit-deskripsi').val(deskripsi);
        $('#edit-keterangan').val(keterangan);
        $('#edit-kuantitas').val(kuantitas);
        $('#edit-satuan').val(satuan);
        $('#edit-harga').val(harga);
        $('#edit-diskon').val(diskon);
        $('#edit-tipe').val(tipe);
        
        // Show/hide jenis kendaraan field
        if (isOngkosKirim) {
            $('#edit-jenis-kendaraan-container').show();
            $('#edit-jenis-kendaraan').val(jenisKendaraan || 'mobil');
        } else {
            $('#edit-jenis-kendaraan-container').hide();
        }
        
        // Show modal
        $('#modal-edit-item-mobile').modal('show');
    }

    function removeMobileItem(index) {
        if (confirm('Hapus item ini?')) {
            $('#invoice-items tbody tr').eq(index).remove();
            updateTotal();
            renumberRows();
            renderMobileItems();
        }
    }

    $('#btn-save-edit-item').click(function() {
        const index = $('#edit-item-index').val();
        const row = $('#invoice-items tbody tr').eq(index);
        
        if (row.length) {
            // Update values in the table row
            row.find('input[name="items[deskripsi][]"]').val($('#edit-deskripsi').val());
            row.find('input[name="items[keterangan][]"]').val($('#edit-keterangan').val());
            row.find('input[name="items[kuantitas][]"]').val($('#edit-kuantitas').val());
            row.find('input[name="items[satuan][]"]').val($('#edit-satuan').val());
            row.find('input[name="items[harga][]"]').val($('#edit-harga').val());
            row.find('input[name="items[diskon][]"]').val($('#edit-diskon').val());
            row.find('input[name="items[tipe][]"]').val($('#edit-tipe').val());
            
            // Handle ongkos kirim specific fields
            if ($('#edit-tipe').val() === 'ongkir') {
                if (!row.find('select[name="items[jenis_kendaraan][]"]').length) {
                    // Convert to ongkos kirim row
                    const keteranganCell = row.find('td').eq(2);
                    keteranganCell.html(`
                        <select name="items[jenis_kendaraan][]" class="form-control jenis-kendaraan" required>
                            <option value="mobil">Mobil</option>
                            <option value="motor">Motor</option>
                        </select>
                        <input type="hidden" name="items[keterangan][]" value="Menggunakan ${$('#edit-jenis-kendaraan').val()}">
                    `);
                    row.addClass('ongkir-row');
                }
                row.find('select[name="items[jenis_kendaraan][]"]').val($('#edit-jenis-kendaraan').val());
                row.find('input[name="items[keterangan][]"]').val('Menggunakan ' + $('#edit-jenis-kendaraan').val());
            } else {
                // Remove ongkos kirim specific fields if type changed
                if (row.find('select[name="items[jenis_kendaraan][]"]').length) {
                    const keteranganCell = row.find('td').eq(2);
                    keteranganCell.html('<input type="text" name="items[keterangan][]" class="form-control" value="' + $('#edit-keterangan').val() + '">');
                    row.removeClass('ongkir-row');
                }
            }
            
            // Update row total
            updateRowTotal(row);
            
            // Close modal and refresh mobile view
            $('#modal-edit-item-mobile').modal('hide');
            renderMobileItems();
        }
    });

    // Handle tipe change in edit modal
    $('#edit-tipe').change(function() {
        if ($(this).val() === 'ongkir') {
            $('#edit-jenis-kendaraan-container').show();
        } else {
            $('#edit-jenis-kendaraan-container').hide();
        }
    });

    function resetForm() {
        if (confirm('Reset semua data form?')) {
            $('#invoice-form')[0].reset();
            $('#invoice-items tbody').empty();
            $('#id_mesin_customer').prop('disabled', true);
            $('#jenis_service').prop('disabled', true);
            $('#customer-display').val('');
            $('#id_member').val('');
            $('#teknisi-section').hide();
            updateTotal();
            renderMobileItems();
            $('#mobile-customer-info').hide();
        }
    }

    // Update customer selection for mobile
    function selectCustomer(customerId, customerName, customerKode) {
        $('#id_member').val(customerId);
        $('#customer-display').val(`${customerName} (${customerKode})`);
        $('#modal-cari-customer').modal('hide');
        $('#search-customer').val('');
        $('#id_mesin_customer').prop('disabled', false);
        loadMesinCustomer(customerId);
        $('#jenis_service').prop('disabled', false);

        // Mobile specific
        if ($(window).width() <= 768) {
            $('#mobile-customer-info').html(`
                <div class="customer-selected">
                    <div class="customer-info">
                        <i class="fa fa-check-circle"></i> Customer Dipilih: ${customerName}
                    </div>
                    <small>Kode: ${customerKode}</small>
                </div>
            `).show();
        }
    }

// Function to display customer search results
function displayCustomerResults(customers) {
    const tbody = $('#customer-search-results');
    tbody.empty();

    customers.forEach((customer, index) => {
        // Generate kode member dengan prefix
        let kodeMember = '-';
        if (customer.kode_member) {
            kodeMember = customer.closing_type_prefix + '-' + customer.kode_member;
        }
        
        // Determine badge color based on closing type
        let badgeClass = 'label-primary';
        let badgeText = 'Jual Putus';
        
        if (customer.closing_type_prefix === 'D') {
            badgeClass = 'label-success';
            badgeText = 'Deposit';
        } else if (customer.closing_type_prefix === 'JD') {
            badgeClass = 'label-warning';
            badgeText = 'Mixed';
        }

        const row = `
            <tr class="customer-row" data-customer-id="${customer.id_member}" data-customer-name="${customer.nama}" data-customer-kode="${kodeMember}">
                <td>${index + 1}</td>
                <td>
                    <div>
                        <strong>${customer.nama}</strong>
                        <br>
                        <small class="text-muted">Kode: ${kodeMember}</small>
                        <span class="label ${badgeClass}" style="margin-left: 5px; font-size: 10px;">${badgeText}</span>
                    </div>
                </td>
                <td>${customer.alamat || '-'}</td>
                <td>${customer.telepon || '-'}</td>
                <td>
                    <button type="button" class="btn btn-xs btn-primary select-customer" 
                            data-customer-id="${customer.id_member}" 
                            data-customer-name="${customer.nama}"
                            data-customer-kode="${kodeMember}">
                        <i class="fa fa-check"></i> Pilih
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Add click event for entire row
    $('.customer-row').click(function() {
        const customerId = $(this).data('customer-id');
        const customerName = $(this).data('customer-name');
        const customerKode = $(this).data('customer-kode');
        selectCustomer(customerId, customerName, customerKode);
    });

    // Add click event for select button
    $('.select-customer').click(function(e) {
        e.stopPropagation(); // Prevent row click event
        const customerId = $(this).data('customer-id');
        const customerName = $(this).data('customer-name');
        const customerKode = $(this).data('customer-kode');
        selectCustomer(customerId, customerName, customerKode);
    });
}

// Update customer selection for mobile
    function selectCustomer(customerId, customerName, customerKode) {
        $('#id_member').val(customerId);
        $('#customer-display').val(`${customerName} (${customerKode})`);
        $('#modal-cari-customer').modal('hide');
        $('#search-customer').val('');
        $('#id_mesin_customer').prop('disabled', false);
        loadMesinCustomer(customerId);
        $('#jenis_service').prop('disabled', false);

        // Mobile specific
        if ($(window).width() <= 768) {
            $('#mobile-customer-info').html(`
                <div class="customer-selected">
                    <div class="customer-info">
                        <i class="fa fa-check-circle"></i> Customer Dipilih: ${customerName}
                    </div>
                    <small>Kode: ${customerKode}</small>
                </div>
            `).show();
        }
    }

// Function to load mesin customer (existing function)
function loadMesinCustomer(memberId) {
    if (memberId) {
        $.get(`${baseUrl}/service-management/get-mesin-customer-grouped/` + memberId, function(mesinCustomers) {
            $('#id_mesin_customer').empty().append('<option value="">Pilih Mesin Customer</option>');
            
            if (mesinCustomers && mesinCustomers.length > 0) {
                $.each(mesinCustomers, function(index, mc) {
                    $('#id_mesin_customer').append(
                        '<option value="' + mc.id_mesin_customer + '" ' +
                        'data-closing-type="' + mc.closing_type + '" ' +
                        'data-produks=\'' + JSON.stringify(mc.produks) + '\' ' +
                        'data-ongkos-kirim=\'' + JSON.stringify(mc.ongkos_kirim) + '\'>' +
                        mc.label +
                        '</option>'
                    );
                });
                
                $('#id_mesin_customer').prop('disabled', false);
            } else {
                $('#id_mesin_customer').append('<option value="">Tidak ada mesin customer</option>');
                $('#id_mesin_customer').prop('disabled', true);
            }
        }).fail(function(xhr) {
            console.error('Error loading mesin customer:', xhr.responseText);
            $('#id_mesin_customer').empty().append('<option value="">Error loading data</option>');
            $('#id_mesin_customer').prop('disabled', true);
        });
    } else {
        $('#id_mesin_customer').empty().append('<option value="">Pilih Customer Terlebih Dahulu</option>');
        $('#id_mesin_customer').prop('disabled', true);
    }
}

function parseNumber(formattedNumber) {
            if (typeof formattedNumber === 'number') {
                return formattedNumber;
            }
            if (typeof formattedNumber !== 'string') {
                return 0;
            }
            const cleanNumber = formattedNumber.toString().replace(/\./g, '');
            return parseInt(cleanNumber) || 0;
        }

        function formatNumber(number) {
            const num = parseInt(number) || 0;
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatCurrency(input) {
            let value = input.value.replace(/\./g, '');
            if (!isNaN(value)) {
                const intValue = parseInt(value) || 0;
                input.value = formatNumber(intValue);
            }
        }

    $(document).ready(function() {
        let currentClosingType = '';
        let currentJenisService = '';
        let customerSearchTimeout;

        checkScreenSize();
        $(window).resize(checkScreenSize);
        
        $('#mobile-customer-info').hide();
        
        $('#add-item-mobile').click(function() {
            addItemRow({
                deskripsi: 'Item Baru',
                keterangan: '',
                kuantitas: 1,
                satuan: 'pcs',
                harga: 0,
                subtotal: 0,
                tipe: 'lainnya',
                is_sparepart: false,
                jenis_kendaraan: ''
            });
            // Auto-edit the new item on mobile
            if ($(window).width() <= 768) {
                setTimeout(() => {
                    const newIndex = $('#invoice-items tbody tr').length - 1;
                    editMobileItem(newIndex);
                }, 100);
            }
        });
        
        $('#add-sparepart-mobile').click(function() {
            ensureModalDisplay('#modal-pilih-sparepart');
            searchSpareparts('');
        });
        
        // Update mobile view when items change
        $(document).on('input', '.quantity, .price, .discount', function() {
            setTimeout(() => {
                if ($(window).width() <= 768) {
                    renderMobileItems();
                }
            }, 100);
        });
        
        // Update mobile view when items are added/removed
        $(document).on('click', '.remove-row', function() {
            setTimeout(() => {
                if ($(window).width() <= 768) {
                    renderMobileItems();
                }
            }, 100);
        });

        function ensureModalDisplay(modalId) {
            const modal = $(modalId);
            const modalDialog = modal.find('.modal-dialog');
            
            // Reset any inline styles that might be causing issues
            modalDialog.css({
                'margin': '',
                'width': '',
                'max-width': ''
            });
            
            // Force show the modal
            modal.modal('show');
            
            // Additional mobile adjustments after modal is shown
            setTimeout(() => {
                if ($(window).width() <= 768) {
                    modalDialog.css({
                        'margin': '10px',
                        'max-width': 'calc(100% - 20px)'
                    });
                }
            }, 50);
        }

        $('#btn-cari-customer').click(function() {
            //$('#modal-cari-customer').modal('show');
            ensureModalDisplay('#modal-cari-customer');
            searchCustomers('');
        });

        $('#search-customer').on('input', function() {
            clearTimeout(customerSearchTimeout);
            const searchTerm = $(this).val();
            
            customerSearchTimeout = setTimeout(() => {
                searchCustomers(searchTerm);
            }, 500);
        });

        // Manual search button
        $('#btn-search-customer').click(function() {
            const searchTerm = $('#search-customer').val();
            searchCustomers(searchTerm);
        });

        // Enter key to search
        $('#search-customer').keypress(function(e) {
            if (e.which === 13) {
                const searchTerm = $(this).val();
                searchCustomers(searchTerm);
            }
        });
        
        // Handle member change - tampilkan mesin customer per closing type
        $('#id_member').change(function() {
            var memberId = $(this).val();
            // Reset dan disable dropdown lainnya
            $('#id_mesin_customer').empty().append('<option value="">Pilih Customer Terlebih Dahulu</option>').prop('disabled', true);
            $('#jenis_service').val('').prop('disabled', true);
            $('#preview-invoice').prop('disabled', true);
            
            if (memberId) {
                $.get('{{ route("service.get-mesin-customer-grouped", "") }}/' + memberId, function(mesinCustomers) {
                    $('#id_mesin_customer').empty().append('<option value="">Pilih Mesin Customer</option>');
                    
                    if (mesinCustomers && mesinCustomers.length > 0) {
                        $.each(mesinCustomers, function(index, mc) {
                            $('#id_mesin_customer').append(
                                '<option value="' + mc.id_mesin_customer + '" ' +
                                'data-closing-type="' + mc.closing_type + '" ' +
                                'data-produks=\'' + JSON.stringify(mc.produks) + '\' ' +
                                'data-ongkos-kirim=\'' + JSON.stringify(mc.ongkos_kirim) + '\'>' +
                                mc.label +
                                '</option>'
                            );
                        });
                        
                        $('#id_mesin_customer').prop('disabled', false);
                    } else {
                        $('#id_mesin_customer').append('<option value="">Tidak ada mesin customer</option>');
                        $('#id_mesin_customer').prop('disabled', true);
                    }
                }).fail(function(xhr) {
                    console.error('Error loading mesin customer:', xhr.responseText);
                    $('#id_mesin_customer').empty().append('<option value="">Error loading data</option>');
                    $('#id_mesin_customer').prop('disabled', true);
                });
            } else {
                $('#id_mesin_customer').empty().append('<option value="">Pilih Customer Terlebih Dahulu</option>');
                $('#id_mesin_customer').prop('disabled', true);
            }
        });

        // Handle mesin customer change
        $('#id_mesin_customer').change(function() {
            var selectedOption = $(this).find('option:selected');
            currentClosingType = selectedOption.data('closing-type');
            var produks = selectedOption.data('produks');
            var ongkosKirim = selectedOption.data('ongkos-kirim');
            
            console.log('Selected data:', { currentClosingType, produks, ongkosKirim, currentJenisService });
            
            if (selectedOption.val()) {
                // Enable jenis service dropdown
                $('#jenis_service').prop('disabled', false);
                $('#preview-invoice').prop('disabled', false);
                
                // Reset dan update items berdasarkan mesin customer yang dipilih
                //updateInvoiceItems(currentClosingType, produks, ongkosKirim);
            } else {
                // Disable jenis service jika mesin customer tidak dipilih
                $('#jenis_service').val('').prop('disabled', true);
                $('#preview-invoice').prop('disabled', true);
                // Clear items
                $('#invoice-items tbody').empty();
                updateTotal();
            }
        });

        $('#jenis_service').change(function() {
            currentJenisService = $(this).val();
            
            if (currentJenisService === 'Service') {
                // Reset semua harga produk menjadi 0
                $('#invoice-items tbody tr').each(function() {
                    const row = $(this);
                    const tipe = row.find('input[name="items[tipe][]"]').val();
                    
                    if (tipe === 'produk') {
                        row.find('.price').val(formatNumber(0));
                        row.find('.quantity').val(1);
                        updateRowTotal(row);
                    }
                });
                
                // Update keterangan untuk produk
                $('#invoice-items tbody tr').each(function() {
                    const row = $(this);
                    const tipe = row.find('input[name="items[tipe][]"]').val();
                    const deskripsi = row.find('input[name="items[deskripsi][]"]').val();
                    
                    if (tipe === 'produk' && deskripsi.includes('Service')) {
                        row.find('input[name="items[keterangan][]"]').val('Service Gratis - Jual Putus');
                    }
                });
            }
            
            updateInvoiceItemsBasedOnServiceType();
        });

        function updateInvoiceItemsBasedOnServiceType() {
            var selectedOption = $('#id_mesin_customer').find('option:selected');
            if (!selectedOption.val()) return;
            
            var closingType = selectedOption.data('closing-type');
            var produks = selectedOption.data('produks');
            var ongkosKirim = selectedOption.data('ongkos-kirim');
            
            updateInvoiceItems(closingType, produks, ongkosKirim);
        }

        function updateInvoiceItems(closingType, produks, ongkosKirim) {
            // Clear existing items
            $('#invoice-items tbody').empty();
            
            // Handle berdasarkan jenis service
            if (currentJenisService === 'Maintenance') {
                // Maintenance: tampilkan produk items
                $('#teknisi-section').hide();
                removeTeknisiItem();
                
                if (produks && Array.isArray(produks)) {
                    $.each(produks, function(index, produk) {
                        const biayaService = produk.pivot ? produk.pivot.biaya_service : 0;
                        const jumlah = produk.pivot ? (produk.pivot.jumlah || 1) : 1;
                        const subtotal = biayaService * jumlah;
                        const satuan = produk.satuan ? produk.satuan.nama_satuan : 'Unit';
                        
                        addItemRow({
                            id_produk: produk.id_produk,
                            deskripsi: produk.nama_produk,
                            keterangan: 'Biaya Maintenance',
                            kuantitas: jumlah > 1 ? jumlah : 1,
                            satuan: satuan,
                            harga: biayaService,
                            subtotal: subtotal,
                            tipe: 'produk',
                            is_sparepart: false
                        });
                    });
                }

                // Always add ongkir item jika ada (untuk semua jenis service)
                if (ongkosKirim && ongkosKirim.harga) {
                    addItemRow({
                        deskripsi: 'Transport - ' + ongkosKirim.daerah,
                        keterangan: 'Menggunakan mobil',
                        kuantitas: 1,
                        satuan: 'Trip',
                        harga: ongkosKirim.harga,
                        subtotal: ongkosKirim.harga,
                        tipe: 'ongkir',
                        jenis_kendaraan: 'mobil',
                        is_sparepart: false
                    });
                }
                
            } else if (currentJenisService === 'Service') {
                
                if (produks && Array.isArray(produks)) {
                    $.each(produks, function(index, produk) {
                        const satuan = produk.satuan ? produk.satuan.nama_satuan : 'Unit';
                        const keterangan = closingType === 'jual_putus' ? 'Service Gratis - Jual Putus' : 'Biaya Service';
                        
                        addItemRow({
                            deskripsi: 'Transport - ' + ongkosKirim.daerah,
                            keterangan: 'Menggunakan mobil',
                            kuantitas: 1,
                            satuan: 'Trip',
                            harga: 0,
                            subtotal: 0,
                            tipe: 'ongkir',
                            jenis_kendaraan: 'mobil',
                            is_sparepart: false
                        });
                    });
                }
                $('#teknisi-section').show();
                removeProdukItems();
                
            } else if (currentJenisService === 'Pembelian Sparepart' || currentJenisService === 'Lainnya') {
                $('#teknisi-section').show();
                // Kosongkan produk items
                removeProdukItems();
            }
            
            
            
            updateTotal();
        }

        // Helper function untuk ucfirst
        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
        
        // Remove produk items
        function removeProdukItems() {
            $('#invoice-items tbody tr').each(function() {
                if ($(this).find('input[name="items[tipe][]"]').val() === 'produk') {
                    $(this).remove();
                }
            });
        }
        
        // Calculate teknisi cost
        $('#jumlah_teknisi, #jumlah_jam').on('input', function() {
            calculateTeknisiCost();
        });
        
        // Add custom item
        $('#add-item').click(function() {
            addItemRow({
                deskripsi: '',
                keterangan: '',
                kuantitas: 1,
                satuan: '',
                harga: 0,
                subtotal: 0,
                tipe: 'lainnya',
                is_sparepart: false,
                jenis_kendaraan: ''
            });
        });
        

        $('#tanggal_selesai_service').change(function() {
            const tanggalSelesai = $(this).val();
            if (tanggalSelesai) {
                $('#tanggal_service_berikutnya').attr('min', tanggalSelesai);
            }
        });

        // Juga set min date saat page load
        const tanggalSelesaiAwal = $('#tanggal_selesai_service').val();
        if (tanggalSelesaiAwal) {
            $('#tanggal_service_berikutnya').attr('min', tanggalSelesaiAwal);
        }
        
        // Handle form submission
        $('#invoice-form').submit(function(e) {
            e.preventDefault();

             const totalData = updateTotalWithDiscount();
            
            // Parse semua nilai formatted number sebelum submit
            const formData = {
                _token: $('input[name=_token]').val(),
                tanggal: $('#tanggal').val(),
                tanggal_mulai_service: $('#tanggal_mulai_service').val(),
                tanggal_selesai_service: $('#tanggal_selesai_service').val(),
                tanggal_service_berikutnya: $('#tanggal_service_berikutnya').val(),
                id_member: $('#id_member').val(),
                id_mesin_customer: $('#id_mesin_customer').val(),
                jenis_service: $('#jenis_service').val(),
                keterangan_service: $('#keterangan_service').val(),
                is_garansi: $('#is_garansi').is(':checked') ? '1' : '0',
                diskon: totalData.diskon,
                total_setelah_diskon: totalData.total_setelah_diskon,
                jumlah_teknisi: $('#jumlah_teknisi').val() || 0,
                jumlah_jam: $('#jumlah_jam').val() || 0,
                biaya_teknisi: parseNumber($('#biaya_teknisi').val()) || 0,
                items: []
            };
            
            $('#invoice-items tbody tr').each(function() {
                var row = $(this);
                
                // Convert string boolean to actual boolean
                const isSparepartValue = row.find('input[name="items[is_sparepart][]"]').val();
                const isSparepart = isSparepartValue === '1';
                
                const item = {
                    id_produk: row.find('input[name="items[id_produk][]"]').val() || null,
                    id_sparepart: row.find('input[name="items[id_sparepart][]"]').val() || null,
                    deskripsi: row.find('input[name="items[deskripsi][]"]').val(),
                    keterangan: row.find('input[name="items[keterangan][]"]').val(),
                    kuantitas: parseInt(row.find('input[name="items[kuantitas][]"]').val()) || 1,
                    satuan: row.find('input[name="items[satuan][]"]').val() || '',
                    diskon: parseNumber(row.find('input[name="items[diskon][]"]').val()),
                    harga: parseNumber(row.find('input[name="items[harga][]"]').val()),
                    subtotal: parseNumber(row.find('input[name="items[subtotal][]"]').val()),
                    tipe: row.find('input[name="items[tipe][]"]').val(),
                    is_sparepart: isSparepart,
                    jenis_kendaraan: row.find('select[name="items[jenis_kendaraan][]"]').length ? 
                                row.find('select[name="items[jenis_kendaraan][]"]').val() : 
                                null,
                    kode_sparepart: row.find('input[name="items[kode_sparepart][]"]').val() || null,
                };
                
                formData.items.push(item);
            });
            
            //console.log('Data yang dikirim ke server:', JSON.stringify(formData, null, 2));
            
            // Show confirmation with SweetAlert
            Swal.fire({
                title: 'Konfirmasi Simpan Invoice',
                html: `
                    <div style="text-align: left; font-size: 14px;">
                        <p><strong>Apakah data invoice sudah sesuai?</strong></p>
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;">
                            <p><strong>Customer:</strong> ${$('#id_member option:selected').text()}</p>
                            <p><strong>Jenis Service:</strong> ${$('#jenis_service').val()}</p>
                            <p><strong>Tanggal:</strong> ${$('#tanggal').val()}</p>
                            <p><strong>Total Item:</strong> ${formData.items.length} items</p>
                            <p><strong>Total:</strong> Rp ${formatNumber(totalData.total_setelah_diskon)}</p>
                        </div>
                        <p style="color: #666; font-size: 12px;">Pastikan semua data sudah benar sebelum menyimpan.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan Invoice',
                cancelButtonText: 'Periksa Kembali',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with submission
                    submitInvoiceForm(formData);
                } else {
                    // User cancelled, focus on first field for review
                    $('#id_member').focus();
                }
            });
        });

        function calculateTotal() {
            let total = 0;
            $('#invoice-items tbody tr').each(function() {
                const subtotal = parseNumber($(this).find('.subtotal').val());
                total += subtotal;
            });
            return total;
        }

        // Function untuk submit form setelah konfirmasi
        function submitInvoiceForm(formData) {
            // Show loading indicator
            const submitBtn = $('#invoice-form').find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
            
            $.ajax({
                url: '{{ route("service.invoice.store") }}',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function(response) {
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Invoice berhasil dibuat',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("service.invoice.history") }}';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal membuat invoice: ' + (response.message || 'Unknown error'),
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(error) {
                    submitBtn.prop('disabled', false).html(originalText);
                    console.error('Error response detail:', error);
                    
                    let errorMessage = 'Terjadi kesalahan';
                    if (error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    }
                    if (error.responseJSON && error.responseJSON.errors) {
                        const errors = error.responseJSON.errors;
                        errorMessage = 'Error Validasi:\n';
                        for (const key in errors) {
                            errorMessage += `• ${key}: ${errors[key].join(', ')}\n`;
                        }
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        html: `<div style="text-align: left; font-size: 14px;">${errorMessage.replace(/\n/g, '<br>')}</div>`,
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK',
                        width: '500px'
                    });
                }
            });
        }
        
        // Preview invoice function
        $('#preview-invoice').click(function() {
            const mesinCustomerId = $('#id_mesin_customer').val();
            const jenisService = $('#jenis_service').val();
            
            if (mesinCustomerId && jenisService) {
                // Kumpulkan data untuk preview
                const formData = {
                    tanggal: $('#tanggal').val(),
                    id_member: $('#id_member').val(),
                    id_mesin_customer: mesinCustomerId,
                    jenis_service: jenisService,
                    jumlah_teknisi: $('#jumlah_teknisi').val() || 0,
                    jumlah_jam: $('#jumlah_jam').val() || 0,
                    biaya_teknisi: parseNumber($('#biaya_teknisi').val()) || 0,
                    items: [],
                    is_preview: true
                };
                
                // Ambil data member untuk preview
                const selectedMember = $('#id_member option:selected');
                if (selectedMember.length) {
                    formData.member_nama = selectedMember.text();
                    // Anda bisa menambahkan data member lainnya jika diperlukan
                }
                
                $('#invoice-items tbody tr').each(function() {
                    var row = $(this);
                    formData.items.push({
                        id_produk: row.find('input[name="items[id_produk][]"]').val(),
                        deskripsi: row.find('input[name="items[deskripsi][]"]').val(),
                        keterangan: row.find('input[name="items[keterangan][]"]').val(),
                        kuantitas: row.find('input[name="items[kuantitas][]"]').val(),
                        satuan: row.find('input[name="items[satuan][]"]').val(),
                        harga: parseNumber(row.find('input[name="items[harga][]"]').val()),
                        subtotal: parseNumber(row.find('input[name="items[subtotal][]"]').val()),
                        tipe: row.find('input[name="items[tipe][]"]').val()
                    });
                });
                
                // Encode data untuk URL
                const encodedData = encodeURIComponent(JSON.stringify(formData));
                
                // Buka preview dalam tab baru
                const previewUrl = '{{ route("service.invoice.preview.temp") }}?data=' + encodedData + '&preview=true&timestamp=' + new Date().getTime();
                window.open(previewUrl, '_blank');
                
            } else {
                alert('Pilih mesin customer dan jenis service terlebih dahulu');
            }
        });


        // Auto format input harga
        $(document).on('input', '.price, .discount', function() {
            var value = parseNumber($(this).val());
            $(this).val(formatNumber(value));
        });

        $(document).on('change', '.jenis-kendaraan', function() {
            const row = $(this).closest('tr');
            const deskripsi = row.find('input[name="items[deskripsi][]"]').val();
            
            // Jika ini ongkos kirim, update keterangan berdasarkan jenis kendaraan
            if (deskripsi.includes('Transport')) {
                const jenisKendaraan = $(this).val();
                const keterangan = jenisKendaraan ? 'Pengiriman dengan ' + jenisKendaraan : 'Pengiriman';
                row.find('input[name="items[keterangan][]"]').val(keterangan);
            }
        });

        // function checkScreenSize() {
        //     if ($(window).width() <= 768) {
        //         $('.desktop-btn').hide();
        //         $('.mobile-action-buttons').show();
        //         $('.form-section').show();
        //     } else {
        //         $('.desktop-btn').show();
        //         $('.mobile-action-buttons').hide();
        //         $('.form-section').hide();
        //     }
        // }

        
    });

// Renumber rows after removal
        function renumberRows() {
            $('#invoice-items tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        function calculateTeknisiCost() {
            const jumlahTeknisi = parseInt($('#jumlah_teknisi').val()) || 0;
            const jumlahJam = parseInt($('#jumlah_jam').val()) || 0;
            const biayaPerJam = 25000;
            
            const biayaTeknisi = jumlahTeknisi * jumlahJam * biayaPerJam;
            $('#biaya_teknisi').val(formatNumber(biayaTeknisi));
            
            updateTeknisiItem(biayaTeknisi);
        }

        function updateTeknisiItem(biayaTeknisi) {
            let teknisiRow = null;
            $('#invoice-items tbody tr').each(function() {
                if ($(this).find('input[name="items[tipe][]"]').val() === 'teknisi') {
                    teknisiRow = $(this);
                    return false;
                }
            });
            
            if (biayaTeknisi > 0) {
                if (teknisiRow) {
                    teknisiRow.find('.price').val(formatNumber(biayaTeknisi));
                    teknisiRow.find('.subtotal').val(formatNumber(biayaTeknisi));
                } else {
                    addItemRow({
                        deskripsi: 'Biaya Teknisi',
                        keterangan: $('#jumlah_teknisi').val() + ' orang x ' + $('#jumlah_jam').val() + ' jam',
                        kuantitas: 1,
                        satuan: 'Paket',
                        harga: biayaTeknisi,
                        subtotal: biayaTeknisi,
                        tipe: 'teknisi'
                    });
                }
            } else if (teknisiRow) {
                teknisiRow.remove();
            }
            
            updateTotal();
        }

        function removeTeknisiItem() {
            $('#invoice-items tbody tr').each(function() {
                if ($(this).find('input[name="items[tipe][]"]').val() === 'teknisi') {
                    $(this).remove();
                }
            });
        }

function updateTotalWithDiscount() {
    let subtotal = 0;
    $('#invoice-items tbody tr').each(function() {
        let rowSubtotal = parseNumber($(this).find('.subtotal').val());
        subtotal += rowSubtotal;
    });

    const diskon = parseNumber($('#diskon').val());
    const totalSebelumDiskon = subtotal;
    const totalSetelahDiskon = Math.max(0, totalSebelumDiskon - diskon);

    // Update display
    $('#subtotal-amount').text(formatNumber(subtotal));
    $('#diskon-amount').text(formatNumber(diskon));
    $('#total-sebelum-diskon').text(formatNumber(totalSebelumDiskon));
    $('#total-amount').text(formatNumber(totalSetelahDiskon));

    return {
        subtotal: subtotal,
        diskon: diskon,
        total_sebelum_diskon: totalSebelumDiskon,
        total_setelah_diskon: totalSetelahDiskon
    };
}

// Update function updateTotal()
function updateTotal() {
    updateTotalWithDiscount();
}

// Event untuk diskon
$('#diskon').on('input', function() {
    formatCurrency(this);
    updateTotalWithDiscount();
});

// Function untuk mencari sparepart
function searchSpareparts(searchTerm = '') {
    const loadingElement = $('#sparepart-search-loading');
    const emptyElement = $('#sparepart-search-empty');
    const resultsElement = $('#sparepart-search-results');

    loadingElement.show();
    emptyElement.hide();
    resultsElement.hide();

    $.ajax({
        url: `${baseUrl}/sparepart/search`,
        type: 'GET',
        data: {
            search: searchTerm
        },
        success: function(response) {
            loadingElement.hide();

            if (response.length > 0) {
                displaySparepartResults(response);
                resultsElement.show();
            } else {
                emptyElement.show();
                resultsElement.hide();
            }
        },
        error: function(error) {
            loadingElement.hide();
            emptyElement.show();
            console.error('Error searching spareparts:', error);
        }
    });
}

// Function untuk menampilkan hasil pencarian sparepart
function displaySparepartResults(spareparts) {
    const tbody = $('#sparepart-search-results');
    tbody.empty();

    spareparts.forEach((sparepart, index) => {
        const stokStatus = sparepart.stok > 0 ? 
            `<span class="label label-success">${sparepart.stok} ${sparepart.satuan}</span>` :
            `<span class="label label-danger">Habis</span>`;
        
        const row = `
            <tr class="sparepart-row" data-sparepart-id="${sparepart.id_sparepart}">
                <td>${index + 1}</td>
                <td><strong>${sparepart.kode_sparepart}</strong></td>
                <td>${sparepart.nama_sparepart}</td>
                <td>${sparepart.merk || '-'}</td>
                <td>Rp ${formatNumber(sparepart.harga)}</td>
                <td>${stokStatus}</td>
                <td>
                    <button type="button" class="btn btn-xs btn-primary select-sparepart" 
                            data-sparepart-id="${sparepart.id_sparepart}"
                            ${sparepart.stok == 0 ? 'disabled' : ''}>
                        <i class="fa fa-check"></i> Pilih
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Add click event for select button
    $('.select-sparepart').click(function(e) {
        e.stopPropagation();
        const sparepartId = $(this).data('sparepart-id');
        selectSparepart(sparepartId);
    });
}

function selectSparepart(sparepartId) {
    $.get(`{{ url('sparepart') }}/${sparepartId}/detail`, function(sparepart) {
        // Tambahkan sebagai item di invoice
        addItemRow({
            id_sparepart: sparepart.id_sparepart, // PASTIKAN INI ADA
            deskripsi: sparepart.nama_sparepart,
            keterangan: sparepart.merk ? `Merk: ${sparepart.merk}` : '',
            kuantitas: 1,
            satuan: sparepart.satuan,
            harga: sparepart.harga,
            subtotal: sparepart.harga,
            tipe: 'sparepart',
            is_sparepart: true,
            kode_sparepart: sparepart.kode_sparepart // PASTIKAN INI ADA
        });
        
        // Tutup modal
        $('#modal-pilih-sparepart').modal('hide');
        $('#search-sparepart').val('');
        
    }).fail(function(error) {
        alert('Gagal memuat detail sparepart');
    });
}

// Update tombol tambah sparepart
$('#add-sparepart').click(function() {
    $('#modal-pilih-sparepart').modal('show');
    searchSpareparts(''); // Load semua sparepart awal
});

// Real-time search untuk sparepart
$('#search-sparepart').on('input', function() {
    const searchTerm = $(this).val();
    searchSpareparts(searchTerm);
});

// Manual search button untuk sparepart
$('#btn-search-sparepart').click(function() {
    const searchTerm = $('#search-sparepart').val();
    searchSpareparts(searchTerm);
});

function addItemRow(item) {
            var rowCount = $('#invoice-items tbody tr').length;
            
            const harga = parseInt(item.harga) || 0;
            const diskon = parseInt(item.diskon) || 0;
            const subtotal = parseInt(item.subtotal) || 0;
            
            // Determine if this is ongkos kirim (show jenis kendaraan dropdown)
            const isOngkosKirim = item.tipe === 'ongkir';
            const isSparepart = item.tipe === 'sparepart';
            const jenisKendaraan = item.jenis_kendaraan || '';
            const kodeSparepart = item.kode_sparepart || '';
            
            var row = '<tr>' +
                '<td>' + (rowCount + 1) + '</td>' +
                '<td><input type="text" name="items[deskripsi][]" class="form-control" value="' + (item.deskripsi || '') + '" required></td>' +
                '<td>' +
                    (isOngkosKirim ? 
                        '<select name="items[jenis_kendaraan][]" class="form-control jenis-kendaraan" required>' +
                            '<option value="">Pilih Kendaraan</option>' +
                            '<option value="mobil" ' + (jenisKendaraan === 'mobil' ? 'selected' : '') + '>Mobil</option>' +
                            '<option value="motor" ' + (jenisKendaraan === 'motor' ? 'selected' : '') + '>Motor</option>' +
                        '</select>' +
                        '<input type="hidden" name="items[keterangan][]" value="Menggunakan ' + (jenisKendaraan || 'mobil') + '">' :
                        '<input type="text" name="items[keterangan][]" class="form-control" value="' + (item.keterangan || '') + '">'
                    ) +
                    '<input type="hidden" name="items[is_sparepart][]" value="' + (isSparepart ? '1' : '0') + '">' +
                    '<input type="hidden" name="items[kode_sparepart][]" value="' + (kodeSparepart || '') + '">' +
                    '<input type="hidden" name="items[id_sparepart][]" value="' + (item.id_sparepart || '') + '">' +
                '</td>' +
                '<td><input type="number" name="items[kuantitas][]" class="form-control quantity" value="' + (parseInt(item.kuantitas) || 1) + '" min="1" required></td>' +
                '<td><input type="text" name="items[satuan][]" class="form-control" value="' + (item.satuan || '') + '"></td>' +
                '<td><input type="text" name="items[harga][]" class="form-control price" value="' + formatNumber(harga) + '" required></td>' +
                '<td><input type="text" name="items[diskon][]" class="form-control discount" value="' + formatNumber(diskon) + '" placeholder="0"></td>' + 
                '<td><input type="text" name="items[subtotal][]" class="form-control subtotal" value="' + formatNumber(subtotal) + '" readonly></td>' +
                '<td>' +
                    '<input type="hidden" name="items[tipe][]" value="' + (item.tipe || 'lainnya') + '">' +
                    '<input type="hidden" name="items[id_produk][]" value="' + (item.id_produk || '') + '">' +
                    '<button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-trash"></i></button>' +
                '</td>' +
                '</tr>';
            
            $('#invoice-items tbody').append(row);
            
            var newRow = $('#invoice-items tbody tr:last');
            
            // Add class berdasarkan tipe
            if (item.tipe === 'ongkir') {
                newRow.addClass('ongkir-row');
            } else if (item.tipe === 'sparepart') {
                newRow.addClass('sparepart-row');
                // Tampilkan kode sparepart di deskripsi
                // if (kodeSparepart) {
                //     newRow.find('input[name="items[deskripsi][]"]').after('<small class="text-muted">Kode: ' + kodeSparepart + '</small>');
                // }
            } else if (item.tipe === 'teknisi') {
                newRow.addClass('teknisi-row');
            }
            
            // Auto-set keterangan untuk ongkos kirim berdasarkan jenis kendaraan
            if (isOngkosKirim) {
                const jenisSelect = newRow.find('.jenis-kendaraan');
                const keteranganInput = newRow.find('input[name="items[keterangan][]"]');
                
                // Set initial value
                const selectedJenis = jenisSelect.val();
                if (selectedJenis) {
                    keteranganInput.val('Menggunakan ' + selectedJenis);
                } else {
                    keteranganInput.val('Menggunakan mobil');
                    jenisSelect.val('mobil');
                }
                
                // Update on change
                jenisSelect.change(function() {
                    const jenis = $(this).val();
                    keteranganInput.val(jenis ? 'Menggunakan ' + jenis : 'Menggunakan mobil');
                });
            }
            
            // Event handlers
            newRow.find('.quantity, .price, .discount').on('input', function() {
                updateRowTotal(newRow);
            });
            
            newRow.find('.price, .discount').on('blur', function() {
                formatCurrency(this);
            });
            
            newRow.find('.remove-row').click(function() {
                $(this).closest('tr').remove();
                updateTotal();
                renumberRows();
                if ($(window).width() <= 768) {
                    renderMobileItems();
                }
            });
            
            updateTotal();

            if ($(window).width() <= 768) {
                renderMobileItems();
            }
        }

        function updateRowTotal(row) {
            let quantity = parseInt(row.find('.quantity').val()) || 0;
            let price = parseNumber(row.find('.price').val());
            let discount = parseNumber(row.find('.discount').val()) || 0;

            let hargaSetelahDiskon = price - discount;
            if (hargaSetelahDiskon < 0) hargaSetelahDiskon = 0;
            
            let subtotal = quantity * hargaSetelahDiskon;
            
            row.find('.subtotal').val(formatNumber(subtotal));
            updateTotal();
        }
</script>
@endpush
