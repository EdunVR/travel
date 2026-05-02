<div class="modal fade" id="modal-images" tabindex="-1" role="dialog" aria-labelledby="modal-images">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Kelola Gambar Produk</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="product-id">
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="file" id="new-images" class="form-control" multiple accept="image/*">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" onclick="uploadImages()">
                                    <i data-feather="upload"></i> Upload
                                </button>
                            </span>
                        </div>
                        <small class="text-muted">Maksimal ukuran file 2MB per gambar</small>
                    </div>
                </div>
                
                <div class="row" id="image-container">
                    <!-- Gambar akan dimuat via AJAX -->
                    <div class="col-md-12 text-center">
                        <i data-feather="loader" class="fa-spin"></i> Memuat gambar...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i data-feather="x"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
