<div class="row mt-3">
    <div class="col-md-12">
        <h4><i data-feather="users"></i> Data Keluarga Jemaah</h4>
        
        <div class="alert alert-info">
            <i data-feather="info"></i> Tambahkan anggota keluarga yang akan ikut dalam perjalanan.
        </div>
        
        <form id="keluargaForm" action="{{ route('jemaah.updateKeluarga', $member->id_member) }}" method="POST">
            @csrf
            @method('PUT')
            
            <ul class="nav nav-tabs" id="keluargaTabs" role="tablist">
                <!-- Tab dinamis akan ditambahkan di sini -->
            </ul>
            
            <div class="tab-content" id="keluargaTabContent">
                <!-- Konten tab dinamis akan ditambahkan di sini -->
            </div>
            
            <input type="hidden" name="keluarga_data" id="keluargaDataInput">
            
            <div class="text-right mt-3">
                <button type="button" class="btn btn-sm btn-primary" id="tambahAnggotaKeluarga">
                    <i data-feather="plus"></i> Tambah Anggota Keluarga
                </button>
                <button type="submit" class="btn btn-sm btn-success">
                    <i data-feather="save"></i> Simpan Semua
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(function() {
    let anggotaCounter = 0;
    const anggotaKeluarga = @json($member->keluarga ?? []);
    
    // Fungsi untuk menambahkan tab anggota keluarga baru
    function addAnggotaKeluargaTab(data = {}, index = null) {
        const tabIndex = index !== null ? index : anggotaCounter++;
        const tabId = `anggota-${tabIndex}`;
        
        // Data default
        data = {
            nama: data.nama || '',
            hubungan: data.hubungan || 'Anak',
            jenis_kelamin: data.jenis_kelamin || 'L',
            tanggal_lahir: data.tanggal_lahir || '',
            no_ktp: data.no_ktp || '',
            no_paspor: data.no_paspor || '',
            ktp_path: data.ktp_path || '',
            passport_path: data.passport_path || '',
            ...data
        };
        
        // Tambahkan tab
        $('#keluargaTabs').append(`
            <li class="nav-item" id="tab-${tabId}">
                <a class="nav-link ${anggotaCounter === 1 ? 'active' : ''}" id="${tabId}-tab" data-toggle="tab" 
                    href="#${tabId}" role="tab" aria-controls="${tabId}" aria-selected="${anggotaCounter === 1 ? 'true' : 'false'}">
                    ${data.nama || 'Anggota ' + (tabIndex + 1)}
                    <button type="button" class="close ml-2" aria-label="Close" style="font-size: 1rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </a>
            </li>
        `);
        
        // Tambahkan konten tab
        $('#keluargaTabContent').append(`
            <div class="tab-pane fade ${anggotaCounter === 1 ? 'show active' : ''}" id="${tabId}" role="tabpanel" aria-labelledby="${tabId}-tab">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" value="${data.nama}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Hubungan Keluarga</label>
                            <select class="form-control" name="hubungan" required>
                                <option value="Suami" ${data.hubungan === 'Suami' ? 'selected' : ''}>Suami</option>
                                <option value="Istri" ${data.hubungan === 'Istri' ? 'selected' : ''}>Istri</option>
                                <option value="Anak" ${data.hubungan === 'Anak' ? 'selected' : ''}>Anak</option>
                                <option value="Orang Tua" ${data.hubungan === 'Orang Tua' ? 'selected' : ''}>Orang Tua</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select class="form-control" name="jenis_kelamin" required>
                                <option value="L" ${data.jenis_kelamin === 'L' ? 'selected' : ''}>Laki-laki</option>
                                <option value="P" ${data.jenis_kelamin === 'P' ? 'selected' : ''}>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Foto KTP</label>
                            <div class="image-upload-container">
                                <img src="${data.ktp_path ? '{{ asset("storage") }}/'+data.ktp_path : '{{ asset("img/placeholder-ktp.jpg") }}'" 
                                     class="img-thumbnail img-preview" id="ktp-preview-${tabId}" 
                                     style="width: 100%; height: 150px; object-fit: cover;">
                                <input type="file" class="d-none ktp-input" data-tab="${tabId}" accept="image/*">
                                <button type="button" class="btn btn-sm btn-block btn-primary mt-2" onclick="$(this).siblings('.ktp-input').click()">
                                    <i data-feather="upload"></i> Upload
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" value="${data.tanggal_lahir}">
                        </div>
                        
                        <div class="form-group">
                            <label>No. KTP</label>
                            <input type="text" class="form-control" name="no_ktp" value="${data.no_ktp}">
                        </div>
                        
                        <div class="form-group">
                            <label>No. Paspor</label>
                            <input type="text" class="form-control" name="no_paspor" value="${data.no_paspor}">
                        </div>
                        
                        <div class="form-group">
                            <label>Foto Passport</label>
                            <div class="image-upload-container">
                                <img src="${data.passport_path ? '{{ asset("storage") }}/'+data.passport_path : '{{ asset("img/placeholder-passport.jpg") }}'" 
                                     class="img-thumbnail img-preview" id="passport-preview-${tabId}" 
                                     style="width: 100%; height: 150px; object-fit: cover;">
                                <input type="file" class="d-none passport-input" data-tab="${tabId}" accept="image/*">
                                <button type="button" class="btn btn-sm btn-block btn-primary mt-2" onclick="$(this).siblings('.passport-input').click()">
                                    <i data-feather="upload"></i> Upload
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group text-right">
                    <button type="button" class="btn btn-danger btn-sm hapus-anggota" data-tab="${tabId}">
                        <i data-feather="trash-2"></i> Hapus Anggota
                    </button>
                </div>
            </div>
        `);
        
        feather.replace();
        
        // Aktifkan tab baru jika bukan edit
        if (index === null) {
            $(`#${tabId}-tab`).tab('show');
        }
    }
    
    // Load data keluarga jika ada
    if (anggotaKeluarga.length > 0) {
        anggotaKeluarga.forEach((anggota, index) => {
            addAnggotaKeluargaTab(anggota, index);
        });
    } else {
        // Tambahkan anggota keluarga pertama saat load
        addAnggotaKeluargaTab();
    }
    
    // Tombol tambah anggota keluarga
    $('#tambahAnggotaKeluarga').click(function() {
        addAnggotaKeluargaTab();
    });
    
    // Hapus anggota keluarga
    $(document).on('click', '.close, .hapus-anggota', function(e) {
        e.stopPropagation();
        const tabId = $(this).data('tab') || $(this).closest('.nav-link').attr('href').replace('#', '');
        
        // Hapus tab dan konten
        $(`#tab-${tabId}`).remove();
        $(`#${tabId}`).remove();
        
        // Aktifkan tab pertama jika ada
        if ($('#keluargaTabs .nav-item').length > 0) {
            $('#keluargaTabs .nav-item:first-child .nav-link').tab('show');
        }
    });
    
    // Upload KTP anggota keluarga
    $(document).on('change', '.ktp-input', function(e) {
        const tabId = $(this).data('tab');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $(`#ktp-preview-${tabId}`).attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
            
            // Proses OCR
            const formData = new FormData();
            formData.append('ktp_image', file);
            
            $.ajax({
                url: '{{ route("jemaah.processKtp") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        const tab = $(`#${tabId}`);
                        tab.find('input[name="nama"]').val(response.data.nama_lengkap || '');
                        tab.find('input[name="no_ktp"]').val(response.data.no_ktp || '');
                        tab.find('input[name="tempat_lahir"]').val(response.data.tempat_lahir || '');
                        tab.find('input[name="tanggal_lahir"]').val(response.data.tanggal_lahir || '');
                    }
                }
            });
        }
    });
    
    // Upload Passport anggota keluarga
    $(document).on('change', '.passport-input', function(e) {
        const tabId = $(this).data('tab');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $(`#passport-preview-${tabId}`).attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Form submission
    $('#keluargaForm').submit(function(e) {
        e.preventDefault();
        
        const keluargaData = [];
        $('#keluargaTabContent .tab-pane').each(function() {
            const tabId = $(this).attr('id');
            const formData = {
                nama: $(this).find('input[name="nama"]').val(),
                hubungan: $(this).find('select[name="hubungan"]').val(),
                jenis_kelamin: $(this).find('select[name="jenis_kelamin"]').val(),
                tanggal_lahir: $(this).find('input[name="tanggal_lahir"]').val(),
                no_ktp: $(this).find('input[name="no_ktp"]').val(),
                no_paspor: $(this).find('input[name="no_paspor"]').val(),
                ktp_path: $(this).find('.ktp-input').data('path') || '',
                passport_path: $(this).find('.passport-input').data('path') || ''
            };
            keluargaData.push(formData);
        });
        
        $('#keluargaDataInput').val(JSON.stringify(keluargaData));
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Berhasil', 'Data keluarga berhasil disimpan', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });
});
</script>
