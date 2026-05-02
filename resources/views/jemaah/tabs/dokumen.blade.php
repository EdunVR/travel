<div class="row mt-3">
    <div class="col-md-12">
        <h4><i data-feather="file-text"></i> Dokumen Jemaah</h4>
        
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#ktp" aria-controls="ktp" role="tab" data-toggle="tab">
                    <i data-feather="credit-card"></i> KTP
                </a>
            </li>
            <li role="presentation">
                <a href="#passport" aria-controls="passport" role="tab" data-toggle="tab">
                    <i data-feather="book"></i> Passport
                </a>
            </li>
            <li role="presentation">
                <a href="#visa" aria-controls="visa" role="tab" data-toggle="tab">
                    <i data-feather="file"></i> Visa
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab KTP -->
            <div role="tabpanel" class="tab-pane active" id="ktp">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>File</th>
                                <th>Data Extracted</th>
                                <th>Tanggal Upload</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <a href="#" class="text-primary" data-toggle="modal" data-target="#docPreviewModal" data-doctype="ktp">
                                        <i data-feather="file-text"></i> KTP_1234567890123456.jpg
                                    </a>
                                </td>
                                <td>
                                    <div class="form-group mb-2">
                                        <label class="small mb-0">Nama</label>
                                        <input type="text" class="form-control form-control-sm" value="Ahmad Budiman">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small mb-0">NIK</label>
                                        <input type="text" class="form-control form-control-sm" value="1234567890123456">
                                    </div>
                                </td>
                                <td>15 Jan 2023</td>
                                <td>
                                    <button class="btn btn-sm btn-primary">
                                        <i data-feather="edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        <i data-feather="trash-2"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadDocModal" data-doctype="ktp">
                        <i data-feather="upload"></i> Upload KTP
                    </button>
                </div>
            </div>
            
            <!-- Tab Passport -->
            <div role="tabpanel" class="tab-pane" id="passport">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>File</th>
                                <th>Data Extracted</th>
                                <th>Tanggal Upload</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada dokumen passport</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadDocModal" data-doctype="passport">
                        <i data-feather="upload"></i> Upload Passport
                    </button>
                </div>
            </div>
            
            <!-- Tab Visa -->
            <div role="tabpanel" class="tab-pane" id="visa">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>File</th>
                                <th>Data Extracted</th>
                                <th>Tanggal Upload</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada dokumen visa</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#uploadDocModal" data-doctype="visa">
                        <i data-feather="upload"></i> Upload Visa
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Dokumen -->
<div class="modal fade" id="uploadDocModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen <span id="docTypeTitle"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadDocForm">
                    <div class="form-group">
                        <label>Pilih File</label>
                        <input type="file" class="form-control" name="document" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Maks. 5MB)</small>
                    </div>
                    <div class="form-group">
                        <label>Gunakan OCR</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="useOcr" checked>
                            <label class="custom-control-label" for="useOcr">Ekstrak data secara otomatis</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">
                    <i data-feather="upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Dokumen -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('img/placeholder-ktp.jpg') }}" class="img-fluid" id="docPreviewImage">
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Set dokumen type saat modal upload ditampilkan
        $('#uploadDocModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const docType = button.data('doctype');
            const docTypeTitle = docType.charAt(0).toUpperCase() + docType.slice(1);
            
            $('#docTypeTitle').text(docTypeTitle);
            $('#uploadDocForm').attr('data-doctype', docType);
        });
        
        // Preview dokumen
        $('#docPreviewModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const docType = button.data('doctype');
            let imageUrl = '{{ asset('img/placeholder-') }}' + docType + '.jpg';
            
            $('#docPreviewImage').attr('src', imageUrl);
        });
    });
</script>
