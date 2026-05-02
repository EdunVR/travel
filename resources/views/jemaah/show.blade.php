<style>
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        margin-right: 5px;
        border-radius: 0;
    }
    
    .nav-tabs .nav-link:hover {
        border: none;
        color: #007bff;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff;
        background-color: transparent;
        border: none;
        border-bottom: 2px solid #007bff;
    }
    
    .nav-tabs .nav-link i {
        margin-right: 5px;
    }
    
    .tab-content {
        padding: 1.5rem 0;
    }
    
    /* Image Upload Styles */
    .img-preview {
        border: 2px dashed #ddd;
        cursor: pointer;
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .img-preview:hover {
        border-color: #007bff;
    }
    
    /* Nested Tab Styles */
    #keluargaTabs {
        margin-top: 20px;
    }
    
    #keluargaTabs .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    #keluargaTabs .close {
        font-size: 1.2rem;
        line-height: 1;
        margin-left: 5px;
    }
    
    /* Table Styles */
    .table th {
        white-space: nowrap;
    }
    
    /* Badge Styles */
    .badge {
        font-weight: normal;
        padding: 0.35em 0.65em;
    }
    
    /* Rating Stars */
    .rating i {
        width: 18px;
        height: 18px;
        stroke-width: 1.5px;
    }
</style>

@extends('app')

@section('title')
    Data Jemaah - {{ $member->nama }}
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Data Jemaah - {{ $member->nama }}</h3>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#identitas" aria-controls="identitas" role="tab" data-toggle="tab">
                            <i data-feather="user"></i> Identitas
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#keluarga" aria-controls="keluarga" role="tab" data-toggle="tab">
                            <i data-feather="users"></i> Keluarga
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#produk" aria-controls="produk" role="tab" data-toggle="tab">
                            <i data-feather="shopping-bag"></i> Produk
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#dokumen" aria-controls="dokumen" role="tab" data-toggle="tab">
                            <i data-feather="file-text"></i> Dokumen
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab Identitas -->
                    <div role="tabpanel" class="tab-pane active" id="identitas">
                        @include('jemaah.tabs.identitas')
                    </div>

                    <!-- Tab Keluarga -->
                    <div role="tabpanel" class="tab-pane" id="keluarga">
                        @include('jemaah.tabs.keluarga')
                    </div>

                    <!-- Tab Produk -->
                    <div role="tabpanel" class="tab-pane" id="produk">
                        @include('jemaah.tabs.produk')
                    </div>

                    <!-- Tab Dokumen -->
                    <div role="tabpanel" class="tab-pane" id="dokumen">
                        @include('jemaah.tabs.dokumen')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        feather.replace();
        
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();
        
        // Preview gambar saat diupload
        $('input[type="file"]').change(function(e) {
            const previewId = $(this).attr('id').replace('-input', '-preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    $('#' + previewId).attr('src', event.target.result);
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Simulasi OCR untuk KTP
        $('#ktp-input').change(function(e) {
            if (e.target.files.length > 0) {
                // Simulasi data hasil OCR
                setTimeout(() => {
                    $('input[name="nama_lengkap"]').val('Ahmad Budiman');
                    $('input[name="no_ktp"]').val('1234567890123456');
                    $('input[name="tempat_lahir"]').val('Jakarta');
                    $('input[name="tanggal_lahir"]').val('1990-05-15');
                    $('select[name="jenis_kelamin"]').val('L');
                }, 1000);
            }
        });
    });
    
    function showJemaahData(url) {
        window.location.href = url;
    }
</script>
@endpush
