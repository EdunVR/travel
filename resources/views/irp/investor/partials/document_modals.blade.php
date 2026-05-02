<!-- Modal Upload Dokumen -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadDocumentForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Dokumen*</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Dokumen*</label>
                        <select name="type" class="form-control" required>
                            <option value="KTP">KTP</option>
                            <option value="NPWP">NPWP</option>
                            <option value="AKAD">Akad Perjanjian</option>
                            <option value="KONTRAK">Perpanjangan Kontrak</option>
                            <option value="LAINNYA">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File Dokumen*</label>
                        <input type="file" name="document" class="form-control-file" required>
                        <small class="form-text text-muted">
                            Format: PDF, JPG, PNG (Maks. 2MB)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Buat Dokumen Baru -->
<div class="modal fade" id="createDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Dokumen Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createDocumentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jenis Dokumen*</label>
                        <select name="type" class="form-control" required>
                            <option value="AKAD">Akad Perjanjian</option>
                            <option value="KONTRAK">Perpanjangan Kontrak</option>
                            <option value="LAINNYA">Dokumen Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Judul Dokumen*</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Template Dokumen</label>
                        <select class="form-control" id="documentTemplate">
                            <option value="">Pilih Template</option>
                            <option value="akad_template">Template Akad Standar</option>
                            <option value="kontrak_template">Template Perpanjangan Kontrak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Isi Dokumen*</label>
                        <textarea name="content" class="form-control" rows="10" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tanda Tangan</label>
                        <input type="text" name="signature" class="form-control" 
                               placeholder="Nama yang bertanda tangan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Dokumen -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- Isi mirip dengan create modal, disesuaikan untuk edit -->
</div>
