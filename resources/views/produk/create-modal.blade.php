<style>
    /* Step Indicator */
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
    }
    
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e0e0e0;
        z-index: 1;
    }
    
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    
    .step-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e0e0e0;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .step-label {
        font-size: 12px;
        color: #9e9e9e;
        text-align: center;
    }
    
    .step.active .step-icon {
        background-color: #007bff;
    }
    
    .step.active .step-label {
        color: #007bff;
        font-weight: bold;
    }
    
    .step.completed .step-icon {
        background-color: #28a745;
    }
    
    .step.completed .step-label {
        color: #28a745;
    }
    
    /* Step Content */
    .step-content {
        position: relative;
        min-height: 300px;
    }
    
    .step-pane {
        display: none;
    }
    
    .step-pane.active {
        display: block;
    }
    
    /* Image Upload */
    .image-placeholder {
        cursor: pointer;
    }
    
    .image-placeholder .card {
        transition: all 0.3s ease;
    }
    
    .image-placeholder .card:hover {
        border-color: #007bff;
    }
    
    .image-preview-item {
        position: relative;
    }
    
    .image-preview-item .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .image-preview-item:hover .delete-btn {
        opacity: 1;
    }
    
    .primary-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        z-index: 10;
    }
    
    .set-primary-btn {
        position: absolute;
        bottom: 5px;
        left: 5px;
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .image-preview-item .card-img-top {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
</style>

<div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="modal-create">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-create" action="{{ route('produk.store') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Tambah Produk Baru</h4>
                </div>
                
                <div class="modal-body">
                    <!-- Step Indicator -->
                    <div class="steps mb-4">
                        <div class="step-indicator">
                            <div class="step active" data-step="1">
                                <div class="step-icon">1</div>
                                <div class="step-label">Informasi Produk</div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-icon">2</div>
                                <div class="step-label">Gambar Produk</div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-icon">3</div>
                                <div class="step-label">Varian Produk</div>
                            </div>
                            <div class="step" data-step="4">
                                <div class="step-icon">4</div>
                                <div class="step-label">Konfirmasi</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step Content -->
                    <div class="step-content">
                        <!-- Step 1: Informasi Produk -->
                        <div class="step-pane active" data-step="1">
                            <div class="form-group row">
                                <label for="create_tipe_produk" class="col-lg-3 control-label">Tipe Produk</label>
                                <div class="col-lg-9">
                                    <select name="tipe_produk" id="create_tipe_produk" class="form-control" required>
                                        @foreach ($productTypes as $key => $type)
                                            <option value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="create_nama_produk" class="col-lg-3 control-label">Nama Produk</label>
                                <div class="col-lg-9">
                                    <input type="text" name="nama_produk" id="create_nama_produk" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="create_id_kategori" class="col-lg-3 control-label">Kategori</label>
                                <div class="col-lg-9">
                                    <select name="id_kategori" id="create_id_kategori" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($kategori as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            @if($outlets->count() > 1)
                            <div class="form-group row">
                                <label for="create_id_outlet" class="col-lg-3 control-label">Outlet</label>
                                <div class="col-lg-9">
                                    <select name="id_outlet" id="create_id_outlet" class="form-control" required>
                                        <option value="">Pilih Outlet</option>
                                        @foreach ($outlets as $outlet)
                                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            
                            <div class="form-group row">
                                <label for="create_merk" class="col-lg-3 control-label">Merk</label>
                                <div class="col-lg-9">
                                    <input type="text" name="merk" id="create_merk" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="create_harga_jual" class="col-lg-3 control-label">Harga Jual</label>
                                <div class="col-lg-9">
                                    <input type="text" name="harga_jual" id="create_harga_jual" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="create_diskon" class="col-lg-3 control-label">Diskon</label>
                                <div class="col-lg-9">
                                    <input type="text" name="diskon" id="create_diskon" class="form-control" value="0">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="create_id_satuan" class="col-lg-3 control-label">Satuan</label>
                                <div class="col-lg-9">
                                    <select name="id_satuan" id="create_id_satuan" class="form-control" required>
                                        <option value="">Pilih Satuan</option>
                                        @foreach ($satuan as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="create_spesifikasi" class="col-lg-3 control-label">Keterangan</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" id="create_spesifikasi" name="spesifikasi" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 2: Gambar Produk -->
                        <div class="step-pane" data-step="2">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Maksimal 4 gambar. Gambar pertama akan menjadi cover.
                            </div>
                            
                            <div class="row" id="create_image-preview-container">
                                
                            </div>
                        </div>

                        <!-- Step 3: Varian Produk -->
                        <div class="step-pane" data-step="3">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Tambahkan varian produk jika diperlukan. Jika tidak ada varian, biarkan kosong.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="variant-table-create">
                                    <thead>
                                        <tr>
                                            <th width="25%">Nama Varian</th>
                                            <th width="35%">Deskripsi</th>
                                            <th width="20%">Harga</th>
                                            <th width="15%">Default</th>
                                            <th width="5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Baris akan ditambahkan via JavaScript -->
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-variant-create">
                                    <i class="fa fa-plus"></i> Tambah Varian
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Konfirmasi -->
                        <div class="step-pane" data-step="4">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Informasi Produk</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="30%">Tipe Produk</th>
                                            <td id="confirm-tipe-produk"></td>
                                        </tr>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <td id="confirm-nama-produk"></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td id="confirm-kategori"></td>
                                        </tr>
                                        <tr>
                                            <th>Outlet</th>
                                            <td id="confirm-outlet"></td>
                                        </tr>
                                        <tr>
                                            <th>Merk</th>
                                            <td id="confirm-merk"></td>
                                        </tr>
                                        <tr>
                                            <th>Harga Jual</th>
                                            <td id="confirm-harga-jual"></td>
                                        </tr>
                                        <tr>
                                            <th>Diskon</th>
                                            <td id="confirm-diskon"></td>
                                        </tr>
                                        <tr>
                                            <th>Satuan</th>
                                            <td id="confirm-satuan"></td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td id="confirm-spesifikasi"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Gambar Produk</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="confirm-images">
                                        <div class="col-12">
                                            <p class="text-muted">Tidak ada gambar yang diupload</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Varian Produk</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless" id="confirm-variants">
                                            <thead>
                                                <tr>
                                                    <th width="25%">Nama Varian</th>
                                                    <th width="35%">Deskripsi</th>
                                                    <th width="20%">Harga</th>
                                                    <th width="20%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data varian akan diisi via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default prev-step" disabled>
                        <i class="fa fa-arrow-left"></i> Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary next-step">
                        Selanjutnya <i class="fa fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-success btn-submit" style="display: none;">
                        <i class="fa fa-check"></i> Simpan Produk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

