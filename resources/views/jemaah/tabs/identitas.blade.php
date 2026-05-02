<div class="row mt-3">
    <div class="col-md-12">
        <form id="identitasForm" action="{{ route('jemaah.updateIdentitas', $member->id_member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <h4><i data-feather="user"></i> Identitas Jemaah</h4>
            
            <div class="row">
                <!-- Foto KTP dengan OCR -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Foto KTP</label>
                        <div class="image-upload-container">
                            <img src="{{ $member->jemaahData->first()?->ktp_path ? asset('storage/'.$member->jemaahData->first()->ktp_path) : asset('img/placeholder-ktp.jpg') }}" 
                                class="img-thumbnail img-preview" id="ktp-preview" 
                                style="width: 100%; height: 200px; object-fit: cover;">
                            <input type="file" name="ktp" id="ktp-input" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-sm btn-block btn-primary mt-2" 
                                    onclick="document.getElementById('ktp-input').click()">
                                <i data-feather="upload"></i> Upload
                            </button>
                            <div class="progress mt-2 d-none" id="ktp-progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Foto Passport</label>
                        <div class="image-upload-container">
                            <img src="{{ asset('img/placeholder-passport.jpg') }}" class="img-thumbnail img-preview" id="passport-preview" style="width: 100%; height: 200px; object-fit: cover;">
                            <input type="file" name="passport" id="passport-input" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-sm btn-block btn-primary mt-2" onclick="document.getElementById('passport-input').click()">
                                <i data-feather="upload"></i> Upload
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Foto Visa</label>
                        <div class="image-upload-container">
                            <img src="{{ asset('img/placeholder-visa.jpg') }}" class="img-thumbnail img-preview" id="visa-preview" style="width: 100%; height: 200px; object-fit: cover;">
                            <input type="file" name="visa" id="visa-input" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-sm btn-block btn-primary mt-2" onclick="document.getElementById('visa-input').click()">
                                <i data-feather="upload"></i> Upload
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Pas Foto</label>
                        <div class="image-upload-container">
                            <img src="{{ asset('img/placeholder-photo.jpg') }}" class="img-thumbnail img-preview" id="photo-preview" style="width: 100%; height: 200px; object-fit: cover;">
                            <input type="file" name="photo" id="photo-input" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-sm btn-block btn-primary mt-2" onclick="document.getElementById('photo-input').click()">
                                <i data-feather="upload"></i> Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="{{ $member->jemaahData->first()?->nama_lengkap ?? $member->nama }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin">
                            <option value="L" {{ ($member->jemaahData->first()?->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ ($member->jemaahData->first()?->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status Pernikahan</label>
                        <select class="form-control" name="status_pernikahan">
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Duda">Duda</option>
                            <option value="Janda">Janda</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" value="{{ $member->jemaahData->first()?->tempat_lahir ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" value="{{ $member->jemaahData->first()?->tanggal_lahir ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" class="form-control" name="no_ktp" value="{{ $member->jemaahData->first()?->no_ktp ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" class="form-control" name="no_telepon" value="{{ $member->telepon }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(function() {
    // Fungsi untuk preview gambar
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onloadend = function() {
            preview.src = reader.result;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }
    
    // Event listener untuk setiap input file
    document.getElementById('ktp-input').addEventListener('change', function() {
        previewImage(this, 'ktp-preview');
    });
    
    document.getElementById('passport-input').addEventListener('change', function() {
        previewImage(this, 'passport-preview');
    });
    
    document.getElementById('visa-input').addEventListener('change', function() {
        previewImage(this, 'visa-preview');
    });
    
    document.getElementById('photo-input').addEventListener('change', function() {
        previewImage(this, 'photo-preview');
    });

    // OCR Processing
    $('#ktp-input').change(function(e) {
        if (e.target.files.length > 0) {
            const formData = new FormData();
            formData.append('ktp_image', e.target.files[0]);
            
            $('#ktp-progress').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("jemaah.processKtp") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhr: function() {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('#ktp-progress .progress-bar').css('width', percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        // Update form fields...
                    } else {
                        showError(response.message || 'Gagal memproses KTP');
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan server';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        msg = res.message || msg;
                    } catch(e) {}
                    showError(msg);
                },
                complete: function() {
                    $('#ktp-progress').addClass('d-none');
                }
            });
        }
    });

    function showError(message) {
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
    
    // Form submission
    $('#identitasForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Berhasil', 'Data identitas berhasil disimpan', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });
});
</script>
