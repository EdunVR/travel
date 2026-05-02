<style>
    .text-left {
        text-align: left !important;
    }

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

    /* Style untuk input harga */
    .input-group-text {
        background-color: #f8f9fa;
    }

    .tab-content {
        padding: 15px 0;
    }

    /* Style untuk tabel */
    .table th {
        white-space: nowrap;
    }

    /* Style untuk modal */
    .modal-lg {
        max-width: 900px;
    }

    /* Style untuk form */
    .form-group {
        margin-bottom: 15px;
    }

    /* Style untuk feather icon */
    .feather {
        width: 16px;
        height: 16px;
        vertical-align: text-bottom;
    }

    .img-thumbnail {
        max-height: 50px;
        object-fit: cover;
    }

    /* Style untuk card gambar */
    .card-img-top {
        height: 120px;
        object-fit: cover;
    }

    /* Style untuk badge */
    .badge {
        font-size: 0.8em;
        font-weight: normal;
    }

    /* Style untuk label tipe produk */
    .label {
        display: inline-block;
        padding: 0.3em 0.6em;
        font-size: 0.8em;
        font-weight: normal;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25em;
    }

    .label-primary {
        background-color: #007bff;
    }

    .label-success {
        background-color: #28a745;
    }

    .label-info {
        background-color: #17a2b8;
    }

    .label-warning {
        background-color: #ffc107;
        color: #212529;
    }

    /* Style untuk preview gambar */
    .image-preview-item {
        position: relative;
    }

    .image-preview-item .card {
        height: 100%;
    }

    .image-preview-item img {
        height: 120px;
        object-fit: cover;
    }

    /* Style untuk tabel RAB */
    #rab-component-table th {
        white-space: nowrap;
    }

    /* Style untuk modal RAB */
    #modal-rab .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Style untuk tombol aksi */
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    .image-placeholder {
        cursor: pointer;
    }

    .image-placeholder .card {
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }

    .image-placeholder .card:hover {
        border-color: #007bff;
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

    .image-preview-item {
        position: relative;
        overflow: hidden;
    }

    .image-preview-item:hover .set-primary-btn {
        opacity: 1;
    }

    .image-preview-item .card-img-top {
        width: 100%;
        height: 150px;
        object-fit: cover;
        object-position: center;
    }

    .progress {
        margin-bottom: 5px;
        height: 20px;
    }

    .progress-bar {
        line-height: 20px;
        font-size: 12px;
    }

    /* Style untuk tabel varian */
    #variant-table,
    #variant-table-create {
        margin-bottom: 0;
    }

    #variant-table tbody tr,
    #variant-table-create tbody tr {
        background-color: #fff;
    }

    .variant-price {
        text-align: right;
    }

    .remove-variant {
        padding: 0.25rem 0.5rem;
    }

    /* Style untuk varian default */
    .variant-price[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    /* Style untuk tabel konfirmasi */
    #confirm-variants td,
    #confirm-variants th {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    #confirm-variants thead th {
        border-bottom: 2px solid #dee2e6;
    }

    /* Style untuk tabel komponen */
    #component-table th {
        white-space: nowrap;
    }

    /* Style untuk harga coret */
    .component-subtotal span {
        color: #6c757d;
        opacity: 0.8;
    }

    /* Style untuk select2 */
    .select2-container {
        width: 100% !important;
    }

    .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
    }

    .select2-selection__rendered {
        line-height: 36px !important;
    }

    .select2-selection__arrow {
        height: 36px !important;
    }

    /* Style untuk komponen yang akan dihapus */
    .deleted-component {
        display: none;
    }

    /* Style untuk tabel komponen */
    #component-table tr td {
        vertical-align: middle;
    }

    #component-table .original-price {
        font-weight: bold;
    }
    /* Style untuk tabel produk */
    .table-produk td:nth-child(5) { /* Kolom nama produk */
        text-align: left !important;
        padding-left: 15px !important;
        font-weight: 500;
    }

    .table-produk td:nth-child(9) { /* Kolom harga jual */
        text-align: right !important;
        padding-right: 15px !important;
        font-weight: bold;
        color: #2a6496;
    }

    /* Hover effect untuk harga */
    .table-produk td:nth-child(9):hover {
        color: #d35400;
        text-decoration: underline;
    }
    /* Efek zoom pada gambar */
    .zoom-hover {
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .zoom-hover:hover {
        transform: scale(1.5);
        z-index: 100;
        position: relative;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    /* Style untuk modal gambar */
    #imageModal .modal-content {
        background-color: white;
        color: green;
    }

    #imageModal .close {
        color: white;
        opacity: 1;
        text-shadow: none;
    }

    #imageModal .modal-body {
        padding: 0;
    }

    #imageModal .modal-footer {
        border-top: none;
    }
    #imageModal {
    z-index: 99999 !important; /* Nilai tinggi untuk memastikan di depan */
    }

    #imageModal .modal-backdrop {
        z-index: 99998 !important; /* Sedikit lebih rendah dari modal */
    }

    
</style>

@extends('app')

@section('title') Produk @endsection

@section('breadcrumb')
@parent
<li class="active">Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row">
                    @if($outlets->count() > 1)
                        <div class="col-md-3">
                            <label for="id_outlet">Pilih Outlet</label>
                            <select name="id_outlet" id="id_outlet" class="form-control">
                                <option value="">Semua Outlet</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label for="product_type">Filter Tipe Produk</label>
                        <select name="product_type" id="product_type" class="form-control">
                            <option value="">Semua Tipe</option>
                            @foreach($productTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="btn-group mt-3">
                    <a href="#" onclick="addForm('{{ route('produk.store') }}')"
                        class="btn btn-success btn-sm btn-flat">
                        <i data-feather="plus-circle"></i> Tambah
                    </a>
                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')"
                        class="btn btn-danger btn-sm btn-flat">
                        <i data-feather="trash-2"></i> Hapus
                    </button>
                    <button onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')"
                        class="btn btn-info btn-sm btn-flat">
                        <i data-feather="printer"></i> Cetak Barcode
                    </button>
                    <a href="{{ route('rab_template.index') }}"
                        class="btn btn-primary btn-sm btn-flat">
                        <i data-feather="dollar-sign"></i> List RAB
                    </a>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-produk">
                    <thead>
                        <th width="5%">
                            <input type="checkbox" name="select_all" id="select_all">
                        </th>
                        <th width="5%">No</th>
                        <th>Gambar</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Kategori</th>
                        <th>Outlet</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th width="15%"><i data-feather="settings"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal RAB -->
<div class="modal fade" id="modal-rabs" tabindex="-1" role="dialog" aria-labelledby="modal-rabs">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Kelola RAB Produk</h4>
            </div>
            <div class="modal-body" id="rab-container">
                <!-- Konten RAB akan dimuat via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk gambar fullscreen -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 80vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@includeIf('produk.detail')
    @includeIf('produk.form')
        @includeIf('produk.form_hpp')
            @includeIf('produk.rab_form')
                @includeIf('produk.images_form')
                    @includeIf('produk.create-modal')
                        @endsection

                        @push('styles')
                            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                        @endpush

                        @push('scripts')
                            <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
                            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                            <script>
                                const baseUrl = window.baseUrl;
                                let currentProductId = null;
                                let rabData = [];

                                function showRabs(id) {
                                    $('#modal-rabs').modal('show');
                                    $('#rab-container').load('${baseUrl}/produk/' + id + '/rabs');
                                }

                                let table, table1, tableRab;

                                $(function () {
                                    feather.replace();

                                    if (AutoNumeric.getAutoNumericElement('#harga_jual')) {
                                        AutoNumeric.getAutoNumericElement('#harga_jual').remove();
                                    }

                                    new AutoNumeric('#harga_jual', {
                                        digitGroupSeparator: '.',
                                        decimalCharacter: ',',
                                        decimalPlaces: 0,
                                        unformatOnSubmit: true,
                                        modifyValueOnWheel: false
                                    });

                                    // Inisialisasi AutoNumeric untuk diskon
                                    new AutoNumeric('#diskon', {
                                        digitGroupSeparator: '.',
                                        decimalCharacter: ',',
                                        decimalPlaces: 0,
                                        unformatOnSubmit: true,
                                        modifyValueOnWheel: false
                                    });

                                    $('#modal-form').on('shown.bs.modal', function () {
                                        const hargaJual = $('#harga_jual').val();
                                        if (hargaJual) {
                                            AutoNumeric.getAutoNumericElement('#harga_jual').set(
                                                hargaJual);
                                        }
                                        // Jika ada varian yang sudah ada, tampilkan
                                        if (window.produkData && produkData.variants && produkData
                                            .variants.length > 0) {
                                            $('#variant-table tbody').empty();
                                            produkData.variants.forEach((variant, index) => {
                                                addVariantRow('variant-table', index, {
                                                    nama_varian: variant.nama_varian,
                                                    deskripsi: variant.deskripsi,
                                                    harga: variant.harga,
                                                    is_default: variant.is_default,
                                                    id: variant.id
                                                });
                                            });
                                        }
                                        if (currentProductId) {
                                            loadProductComponents(currentProductId);
                                        }
                                    });

                                    $('#product_type').on('change', function () {
                                        table.ajax.reload();
                                    });
                                    table = $('.table-produk').DataTable({
                                        responsive: true,
                                        processing: true,
                                        serverSide: true,
                                        autoWidth: false,
                                        ajax: {
                                            url: '{{ route('produk.data') }}',
                                            data: function (d) {
                                                d.id_outlet = $('#id_outlet').val();
                                                d.product_type = $('#product_type').val();
                                            }
                                        },
                                        columns: [{
                                                data: 'select_all',
                                                searchable: false,
                                                sortable: false
                                            },
                                            {
                                                data: 'DT_RowIndex',
                                                searchable: false,
                                                sortable: false
                                            },
                                            {
                                                data: 'gambar',
                                                searchable: false,
                                                sortable: false,
                                                render: function(data, type, full, meta) {
                                                    if (data) {
                                                        const imgUrl = `{{ asset('storage') }}/${data}`;
                                                        return `
                                                            <a href="#" class="show-image" data-img="${imgUrl}">
                                                                <img src="${imgUrl}" alt="Product Image" 
                                                                    class="img-thumbnail zoom-hover" width="50">
                                                            </a>`;
                                                    }
                                                    return `<img src="{{ asset('img/no-image.png') }}" alt="No Image" class="img-thumbnail" width="50">`;
                                                }
                                            },
                                            {
                                                data: 'kode_produk'
                                            },
                                            {
                                                data: 'nama_produk'
                                            },
                                            {
                                                data: 'tipe_produk'
                                            },
                                            {
                                                data: 'nama_kategori'
                                            },
                                            {
                                                data: 'nama_outlet'
                                            },
                                            {
                                                data: 'harga_jual'
                                            },
                                            {
                                                data: 'stok'
                                            },
                                            {
                                                data: 'aksi',
                                                searchable: false,
                                                sortable: false
                                            },
                                        ],
                                        columnDefs: [{
                                                className: 'text-center',
                                                targets: [0, 1, 2, 8, 9]
                                            },
                                            {
                                                className: 'text-right',
                                                targets: [8]
                                            }
                                        ],
                                        drawCallback: function () {
                                            feather.replace();
                                            
                                            // Tambahkan hover effect manual untuk mobile
                                            $('.zoom-hover').on('touchstart', function() {
                                                $(this).css('transform', 'scale(1.5)');
                                            }).on('touchend', function() {
                                                $(this).css('transform', '');
                                            });

                                            $(document).on('click', '.show-image', function(e) {
                                                e.preventDefault();
                                                const imgUrl = $(this).data('img');
                                                $('#modalImage').attr('src', imgUrl);
                                                $('#imageModal').modal('show');
                                            });

                                            // Untuk gambar di form edit/create
                                            $(document).on('click', '.image-preview-item img', function() {
                                                const imgUrl = $(this).attr('src');
                                                $('#modalImage').attr('src', imgUrl);
                                                $('#imageModal').modal('show');
                                            });
                                        }
                                    });

                                    table1 = $('.table-detail').DataTable({
                                        processing: true,
                                        bSort: false,
                                        bPaginate: true,
                                        destroy: true,
                                        responsive: true,
                                        autoWidth: false,
                                        columns: [{
                                                data: 'DT_RowIndex',
                                                searchable: false,
                                                sortable: false
                                            },
                                            {
                                                data: 'tanggal'
                                            },
                                            {
                                                data: 'hpp'
                                            },
                                            {
                                                data: 'stok'
                                            },
                                            {
                                                data: 'aksi',
                                                name: 'aksi',
                                                searchable: false,
                                                sortable: false
                                            },
                                        ]
                                    });

                                    tableRab = $('.table-rab').DataTable({
                                        processing: true,
                                        bSort: false,
                                        bPaginate: true,
                                        destroy: true,
                                        responsive: true,
                                        autoWidth: false,
                                        columns: [{
                                                data: 'DT_RowIndex',
                                                searchable: false,
                                                sortable: false
                                            },
                                            {
                                                data: 'tanggal'
                                            },
                                            {
                                                data: 'hpp'
                                            },
                                            {
                                                data: 'stok'
                                            },
                                            {
                                                data: 'aksi',
                                                name: 'aksi',
                                                searchable: false,
                                                sortable: false
                                            },
                                        ]
                                    });

                                    $('#id_outlet').on('change', function () {
                                        table.ajax.reload();
                                    });

                                    // Fungsi untuk mengumpulkan semua gambar sebelum submit
                                    function prepareImageUploads() {
                                        const formData = new FormData($('#modal-form form')[0]);

                                        // Hapus semua input images[] yang ada
                                        $('input[name="images[]"]').remove();

                                        // Tambahkan kembali hanya yang memiliki file
                                        $('.image-preview-item').each(function (index) {
                                            const input = $(this).prev('input[type="file"]');
                                            if (input[0] && input[0].files[0]) {
                                                formData.append('images[]', input[0].files[0]);
                                            }
                                        });

                                        return formData;
                                    }

                                    function collectImageFiles() {
                                        const imageFiles = [];
                                        $('input[type="file"][name="images[]"]').each(function () {
                                            if (this.files && this.files[0]) {
                                                imageFiles.push(this.files[0]);
                                            }
                                        });
                                        return imageFiles;
                                    }

                                    $(document).on('submit', '#modal-form form', function (e) {
                                        e.preventDefault();

                                        if (!currentProductId || isNaN(currentProductId)) {
                                            alert('Invalid product ID');
                                            return;
                                        }

                                        // Unformat harga_jual if using AutoNumeric
                                        if (AutoNumeric.getAutoNumericElement('#harga_jual')) {
                                            AutoNumeric.getAutoNumericElement('#harga_jual')
                                                .formUnformat();
                                        }

                                        // Unformat semua input numerik
                                        $('.variant-price, .component-subtotal-value').each(function() {
                                            if (AutoNumeric.getAutoNumericElement(this)) {
                                                AutoNumeric.getAutoNumericElement(this).formUnformat();
                                            }
                                        });

                                        const formData = new FormData(this);

                                        const deletedImages = [];
                                        $('input[name="deleted_images[]"]').each(function () {
                                            deletedImages.push($(this).val());
                                            formData.append('deleted_images[]', $(this).val());
                                        });

                                        // Kirim data komponen yang dihapus
                                        $('.deleted-component').each(function() {
                                            formData.append('deleted_components[]', $(this).val());
                                        });


                                        $.ajax({
                                            url: `${baseUrl}/produk/${currentProductId}`,
                                            type: 'POST', // Tetap POST karena FormData tidak support PUT
                                            data: formData,
                                            contentType: false,
                                            processData: false,
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                                    .attr('content'),
                                                'X-HTTP-Method-Override': 'PUT' // Override method ke PUT
                                            },
                                            success: function (response) {
                                                $('#modal-form').modal('hide');
                                                table.ajax.reload();
                                            },
                                            error: function (xhr) {
                                                console.error('Error:', xhr.responseText);
                                                if (xhr.status === 422) {
                                                    const errors = xhr.responseJSON.errors;
                                                    let errorMsg = '';
                                                    for (const field in errors) {
                                                        errorMsg += `${errors[field][0]}\n`;
                                                    }
                                                    showAlert('Validasi Gagal', errorMsg, 'error');
                                                } else {
                                                    showAlert(
                                                        'Error saving. Check console for details.');
                                                }
                                            }
                                        });
                                    });

                                    $('#modal-form-hpp').on('hidden.bs.modal', function () {
                                        // Reset form saat modal ditutup
                                        $('#modal-form-hpp form')[0].reset();
                                        $('#modal-form-hpp [name=_method]').val(
                                        ''); // Pastikan _method kosong
                                        $('#modal-form-hpp form').attr('action', ''); // Reset action
                                    });

                                    $('#modal-form-hpp form').validator().on('submit', function (e) {
                                        if (!e.isDefaultPrevented()) {
                                            e.preventDefault();
                                            var url = $('#modal-form-hpp form').attr('action');
                                            var method = $('#modal-form-hpp [name=_method]').val() ||
                                                'POST'; // Default ke POST jika _method kosong



                                            $.ajax({
                                                url: url,
                                                type: method,
                                                data: $('#modal-form-hpp form').serialize(),
                                                success: function (response) {
                                                    $('#modal-form-hpp').modal('hide');
                                                    table1.ajax
                                                .reload(); // Reload tabel detail HPP
                                                    table.ajax.reload();
                                                },
                                                error: function (errors) {
                                                    alert('Tidak dapat menyimpan data');
                                                    console.log(errors);
                                                }
                                            });
                                        }
                                    });


                                    $('[name=select_all]').on('click', function () {
                                        $(':checkbox').prop('checked', this.checked);
                                    });

                                    $(document).on('input', '.realisasi-input', function () {
                                        const rabId = $(this).data('rab-id');
                                        const totalDisetujui = parseFloat($(this).data(
                                            'total-disetujui')) || 0;
                                        const realisasi = parseNumber($(this).val());
                                        const sisa = totalDisetujui - realisasi;
                                        const percentage = totalDisetujui > 0 ?
                                            Math.round((realisasi / totalDisetujui) * 100) :
                                            0;

                                        // Update progress bar
                                        const row = $(this).closest('tr');
                                        const progressBar = row.find('.progress-bar');
                                        progressBar.css('width', percentage + '%');
                                        progressBar.attr('aria-valuenow', percentage);
                                        progressBar.text(percentage + '%');

                                        // Update class progress bar
                                        progressBar.removeClass('bg-success bg-warning bg-danger');
                                        if (percentage > 100) {
                                            progressBar.addClass('bg-danger');
                                        } else if (percentage > 80) {
                                            progressBar.addClass('bg-warning');
                                        } else {
                                            progressBar.addClass('bg-success');
                                        }

                                        // Update tampilan sisa
                                        row.find('small.text-muted').html(`
                        Sisa: Rp ${formatNumber(sisa)} (${Math.max(0, 100 - percentage)}%)
                    `);

                                        // Update data di rabData
                                        const rab = rabData.find(r => r.id_rab == rabId);
                                        if (rab) {
                                            rab.total_realisasi = realisasi;
                                        }
                                    });




                                });

                                function parseNumber(formattedNumber) {
                                    if (typeof formattedNumber === 'number') return formattedNumber;
                                    if (!formattedNumber) return 0;

                                    // Hapus semua karakter non-digit kecuali koma (untuk desimal)
                                    const numberString = formattedNumber.toString()
                                        .replace(/[^\d,]/g, '') // Hapus semua karakter non-digit dan non-koma
                                        .replace(',', '.'); // Ubah koma menjadi titik untuk desimal

                                    return parseFloat(numberString) || 0;
                                }

                                function showDetail(url) {
                                    $('#modal-detail').modal('show');
                                    table1.ajax.url(url);
                                    table1.ajax.reload();

                                    var productId = url.split('/').pop();
                                    $('#id_produk').val(productId);
                                }

                                function addForm(url) {
                                    $('#modal-create').modal('show');
                                    $('#form-create')[0].reset();
                                    $('#form-create').attr('action', url);

                                    // Reset steps
                                    currentStepCreate = 1;
                                    $('#modal-create .step-pane').removeClass('active');
                                    $('#modal-create .step').removeClass('active completed');
                                    $('#modal-create .step-pane[data-step="1"]').addClass('active');
                                    $('#modal-create .step[data-step="1"]').addClass('active');
                                    updateStepButtonsCreate();

        
                                    feather.replace();
                                }

                                function addFormHpp() {
                                    $('#modal-form-hpp').modal('show');
                                    $('#modal-form-hpp .modal-title').text('Tambah HPP & Stok Manual');

                                    // Reset form dan set action ke route storeHPP
                                    $('#modal-form-hpp form')[0].reset();
                                    $('#modal-form-hpp form').attr('action',
                                        '{{ route('produk.store_hpp') }}');
                                    $('#modal-form-hpp [name=_method]').val(''); // Pastikan _method kosong untuk POST
                                    $('#modal-form-hpp [name=hpp]').focus();
                                }

                                function editForm(url) {
                                    // Ensure URL is clean (remove any encoded characters)
                                    const cleanUrl = url.replace(/%7B/g, '{').replace(/%7D/g, '}');

                                    $('#modal-form').modal('show');
                                    $('#modal-form .modal-title').text('Edit Produk');
                                    $('#modal-form form')[0].reset();
                                    $('#modal-form form').attr('action', cleanUrl);
                                    $('#modal-form [name=_method]').val('put');

                                    $.get(cleanUrl)
                                        .done((response) => {
                                            currentProductId = response.produk.id_produk;

                                            $('#modal-form [name=tipe_produk]').val(response.produk.tipe_produk)
                                                .trigger('change');
                                            $('#modal-form [name=nama_produk]').val(response.produk.nama_produk);
                                            $('#modal-form [name=id_kategori]').val(response.produk.id_kategori);
                                            $('#modal-form [name=merk]').val(response.produk.merk);
                                            $('#modal-form [name=harga_jual]').val(response.produk.harga_jual);
                                            $('#modal-form [name=diskon]').val(response.produk.diskon);
                                            $('#modal-form [name=id_satuan]').val(response.produk.id_satuan);
                                            $('#modal-form [name=id_outlet]').val(response.produk.id_outlet);
                                            $('#modal-form [name=spesifikasi]').val(response.produk.spesifikasi);
                                            $('#modal-form [name=metode_hpp]').val(response.produk.metode_hpp);
                                            $('#modal-form [name=stok_minimum]').val(response.produk.stok_minimum);
                                            $('#modal-form [name=jenis_paket]').val(response.produk.jenis_paket);
                                            $('#modal-form [name=keberangkatan_template_id]').val(response.produk
                                                .keberangkatan_template_id);
                                            $('#modal-form [name=track_inventory]').prop('checked', response.produk
                                                .track_inventory);
                                            loadProductImages(response.produk.id_produk);
                                            loadSelectedRabs(response.produk.rabs);
                                            window.availableRabs = response.availableRabs;

                                            // $.get(`${baseUrl}/produk/${response.produk.id_produk}/rabs`, function(rabs) {
                                            //     rabData = rabs; // Simpan data RAB ke variabel rabData
                                            //     calculateTotalHppRab();
                                            // });
                                        })
                                        .fail((errors) => {
                                            alert('Failed to load data');
                                            console.error(errors);
                                        });
                                }
                                
                                // Di fungsi loadProductImages
                                function loadProductImages(productId) {
                                    $.get(`${baseUrl}/produk/${productId}/images`)
                                        .done((images) => {
                                            const container = $('#image-preview-container');
                                            container.empty();

                                            images.forEach((image, index) => {
                                                if (index >= 4) return;
                                                
                                                const imgUrl = `{{ asset('storage') }}/${image.path}`;
                                                container.append(`
                                                    <div class="col-md-3 mb-3 image-placeholder" data-index="${index}">
                                                        <input type="file" name="images[]" class="d-none" accept="image/*" data-index="${index}">
                                                        <div class="card h-100 image-preview-item">
                                                            <img src="${imgUrl}" class="card-img-top zoom-image" 
                                                                style="height: 150px; object-fit: cover; cursor: pointer;">
                                                            ${image.is_primary ? 
                                                                '<span class="badge bg-primary primary-badge">Cover</span>' : 
                                                                '<button type="button" class="btn btn-sm btn-primary set-primary-btn" onclick="setAsPrimary(' + index + ')">' +
                                                                    '<i data-feather="star"></i>' +
                                                                '</button>'
                                                            }
                                                            <button type="button" class="btn btn-sm btn-danger delete-btn" onclick="removeImage(${index}, ${image.id_image})">
                                                                <i data-feather="trash-2"></i>
                                                            </button>
                                                            <input type="hidden" name="existing_images[${index}][id]" value="${image.id_image}">
                                                            <input type="hidden" name="uploaded_images[${index}][is_primary]" value="${image.is_primary}">
                                                        </div>
                                                    </div>
                                                `);
                                            });

                                            // Isi placeholder yang kosong
                                            for (let i = images.length; i < 4; i++) {
                                                container.append(`
                                                    <div class="col-md-3 mb-3 image-placeholder" data-index="${i}">
                                                        <input type="file" name="images[]" class="d-none" accept="image/*" data-index="${i}">
                                                        <div class="card h-100" onclick="triggerFileInput(${i})">
                                                            <div class="card-body d-flex align-items-center justify-content-center bg-light" 
                                                                style="min-height: 150px; cursor: pointer;">
                                                                <i data-feather="plus" class="feather-xl text-muted"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `);
                                            }

                                            feather.replace();
                                        })
                                        .fail((error) => {
                                            console.error('Gagal memuat gambar:', error);
                                        });
                                }

                                function editForm_hpp(url) {
                                    $('#modal-form-hpp').modal('show');
                                    $('#modal-form-hpp .modal-title').text('Edit HPP dan Stok');

                                    // Reset form dan set action ke route updateHPP
                                    $('#modal-form-hpp form')[0].reset();
                                    $('#modal-form-hpp [name=_method]').val('PUT'); // Set _method ke PUT untuk update
                                    $('#modal-form-hpp [name=hpp]').focus();

                                    // Ambil data dari URL dan isi form
                                    $.get(url)
                                        .done((response) => {
                                            $('#modal-form-hpp [name=id_hpp]').val(response.id_hpp); // Isi id_hpp
                                            $('#modal-form-hpp [name=hpp]').val(response.hpp);
                                            $('#modal-form-hpp [name=stok]').val(response.stok);
                                            $('#id_produk').val(response.id_produk);

                                            $('#modal-form-hpp form').attr('action',
                                                "{{ route('produk.update_hpp', '') }}/" +
                                                response.id_hpp);
                                            table.ajax.reload();
                                        })
                                        .fail((errors) => {
                                            console.log(errors);
                                            alert('Tidak dapat menampilkan data');
                                        });
                                }

                                function deleteData(url) {
                                    showConfirm('Hapus Data', 'Yakin ingin menghapus data terpilih?', 'Ya, Hapus!')
                                        .then((result) => {
                                            if (result.isConfirmed) {
                                                $.post(url, {
                                                    '_token': $('[name=csrf-token]').attr('content'),
                                                    '_method': 'delete'
                                                })
                                                .done((response) => {
                                                    showAlert('Berhasil', 'Data berhasil dihapus', 'success');
                                                    table.ajax.reload();
                                                })
                                                .fail((errors) => {
                                                    showAlert('Gagal', 'Tidak dapat menghapus data', 'error');
                                                });
                                            }
                                        });
                                }


                                function deleteData_hpp(url) {
                                    if (confirm('Yakin ingin menghapus data?')) {
                                        $.post(url, {
                                                '_token': $('[name=csrf-token]').attr('content'),
                                                '_method': 'delete'
                                            })
                                            .done((response) => {

                                                table.ajax.reload();
                                                table1.ajax.reload();
                                            })
                                            .fail((errors) => {
                                                alert('Tidak dapat menghapus data');
                                                return;
                                            })
                                    }
                                }

                                function deleteSelected(url) {
                                    if ($('input:checked').length > 1) {
                                        showConfirm('Hapus Data', 'Yakin ingin menghapus data terpilih?', 'Ya, Hapus!')
                                            .then((result) => {
                                                if (result.isConfirmed) {
                                                    $.post(url, $('.form-produk').serialize())
                                                        .done((response) => {
                                                            showAlert('Berhasil', 'Data berhasil dihapus', 'success');
                                                            table.ajax.reload();
                                                        })
                                                        .fail((errors) => {
                                                            showAlert('Gagal', 'Tidak dapat menghapus data', 'error');
                                                        });
                                                }
                                            });
                                    } else {
                                        showAlert('Peringatan', 'Pilih data yang akan dihapus', 'warning');
                                    }
                                }

                                function cetakBarcode(url) {
                                    if ($('input:checked').length < 1) {
                                        showAlert('Peringatan', 'Pilih data yang akan dicetak', 'warning');
                                        return;
                                    } else if ($('input:checked').length < 3) {
                                        showAlert('Peringatan', 'Pilih minimal 3 data untuk dicetak', 'warning');
                                        return;
                                    } else {
                                        $('.form-produk')
                                            .attr('target', '_blank')
                                            .attr('action', url)
                                            .submit();
                                    }
                                }

                                function showImages(id) {
                                    $('#modal-images').modal('show');
                                    $('#product-id').val(id);

                                    // Load images via AJAX
                                    $.get(`${baseUrl}/produk/${id}/images`, function (response) {
                                        $('#image-container').html(response);
                                        feather.replace();
                                    });
                                }

                                function uploadImages() {
                                    const productId = $('#product-id').val();
                                    const formData = new FormData();
                                    const files = $('#new-images')[0].files;

                                    for (let i = 0; i < files.length; i++) {
                                        formData.append('images[]', files[i]);
                                    }

                                    $.ajax({
                                        url: `${baseUrl}/produk/${productId}/upload-images`,
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                            $('#new-images').val('');
                                            showImages(productId); // Reload images
                                        },
                                        error: function (xhr) {
                                            showAlert('Gagal mengupload gambar');
                                            console.log(xhr.responseText);
                                        }
                                    });
                                }

                                function deleteImage(imageId) {
                                    if (confirm('Yakin ingin menghapus gambar ini?')) {
                                        $.post(`${baseUrl}/produk/images/${imageId}/delete`, {
                                                '_token': $('[name=csrf-token]').attr('content'),
                                                '_method': 'DELETE'
                                            })
                                            .done((response) => {
                                                showImages($('#product-id').val()); // Reload images
                                            })
                                            .fail((errors) => {
                                                showAlert('Tidak dapat menghapus gambar');
                                                return;
                                            });
                                    }
                                }

                                function triggerFileInput(index) {
                                    const fileInput = document.getElementById(`product-image-${index}`);
                                    if (fileInput) {
                                        fileInput.click();
                                    }
                                }

                                function removeImage(index, imageId = null) {
                                    const placeholder = $(`.image-placeholder[data-index="${index}"]`);
                                    const form = $('#modal-form form');

                                    // 1. Hapus semua input terkait gambar ini
                                    placeholder.find('input[name^="existing_images"], input[name^="uploaded_images"]')
                                        .remove();

                                    // 2. Jika gambar existing, tandai untuk dihapus
                                    if (imageId) {
                                        // Cek apakah sudah ada input deleted_images dengan value yang sama
                                        let exists = false;
                                        $('input[name="deleted_images[]"]').each(function () {
                                            if ($(this).val() == imageId) exists = true;
                                        });

                                        if (!exists) {
                                            // Tambahkan input deleted_images langsung ke form (bukan di placeholder)
                                            form.append(
                                                `<input type="hidden" name="deleted_images[]" value="${imageId}" class="deleted-image-marker">`
                                                );
                                        }
                                    }

                                    // 3. Reset file input dan preview
                                    placeholder.html(`
                    <input type="file" name="images[]" class="d-none" accept="image/*" data-index="${index}">
                    <div class="card h-100" onclick="triggerFileInput(${index})">
                        <div class="card-body d-flex align-items-center justify-content-center bg-light" 
                            style="min-height: 150px; cursor: pointer;">
                            <i data-feather="plus" class="feather-xl text-muted"></i>
                        </div>
                    </div>
                `);

                                    feather.replace();
                                }

                                // Fungsi untuk set gambar sebagai primary
                                function setAsPrimary(index) {
                                    const container = $('#image-preview-container');

                                    // Reset semua primary
                                    container.find('input[name^="uploaded_images"]').val('false');
                                    container.find('.primary-badge').remove();

                                    // Set primary baru
                                    const placeholder = container.find(`.image-placeholder[data-index="${index}"]`);
                                    const primaryInput = placeholder.find('input[name^="uploaded_images"]');

                                    if (primaryInput.length) {
                                        primaryInput.val('true');
                                    } else {
                                        placeholder.append(
                                            `<input type="hidden" name="uploaded_images[${index}][is_primary]" value="true">`
                                            );
                                    }

                                    // Tambahkan badge jika ini preview gambar
                                    if (placeholder.find('.image-preview-item').length) {
                                        placeholder.find('.card').prepend(
                                            '<span class="badge bg-primary primary-badge">Cover</span>');
                                    }

                                    feather.replace();
                                }

                                $(document).on('change', 'input[type="file"][name="images[]"]', function(e) {
                                    const index = $(this).data('index');
                                    const file = this.files && this.files[0] ? this.files[0] : null;
                                    const placeholder = $(`.image-placeholder[data-index="${index}"]`);

                                    if (!file) return;

                                    // Validasi file
                                    const validation = validateFile(file, index);
                                    if (!validation.isValid) {
                                        let errorMessage = `Masalah dengan gambar ${validation.fileIndex}:\n`;
                                        errorMessage += validation.messages.join('\n');
                                        errorMessage += '\n\nTips untuk upload sukses:\n';
                                        errorMessage += '• Gunakan nama file tanpa spasi dan tanda "-"\n';
                                        errorMessage += '• Gunakan underscore "_" sebagai pengganti spasi\n';
                                        errorMessage += '• Pastikan ukuran file < 2MB\n';
                                        errorMessage += '• Format file harus JPEG, PNG, atau GIF';

                                        showAlert('Validasi File Gagal', errorMessage, 'warning');
                                        $(this).val(''); // Reset input file
                                        return;
                                    }

                                    const reader = new FileReader();

                                    reader.onload = function(event) {
                                        // Hapus semua input yang mungkin ada sebelumnya
                                        placeholder.find('input[name^="existing_images"], input[name^="uploaded_images"]').remove();

                                        // Bangun preview baru
                                        placeholder.html(`
                                            <input type="file" name="images[]" class="d-none" accept="image/*" data-index="${index}">
                                            <div class="card h-100 image-preview-item">
                                                <img src="${event.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                <button type="button" class="btn btn-sm btn-primary set-primary-btn" onclick="setAsPrimary(${index})">
                                                    <i data-feather="star"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" onclick="removeImage(${index})">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                                <input type="hidden" name="uploaded_images[${index}][is_primary]" value="false">
                                            </div>
                                        `);

                                        // Re-attach the file
                                        const newFileInput = placeholder.find('input[type="file"]');
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(file);
                                        newFileInput[0].files = dataTransfer.files;

                                        // Setelah preview dibuat, cek apakah file benar-benar terattach
                                        setTimeout(() => {
                                            if (!newFileInput[0].files.length) {
                                                showAlert('Peringatan', 
                                                    `Gambar ${index + 1} tidak terattach dengan benar. Harap upload ulang.`, 
                                                    'warning');
                                                removeImage(index);
                                            }
                                        }, 500);

                                        feather.replace();
                                    };
                                    reader.readAsDataURL(file);
                                });

                                function showRabList() {
                                    $('#modal-rab-list').modal('show');
                                    const tableBody = $('#rab-list-table tbody');
                                    tableBody.empty();

                                    // Filter RAB yang belum dipilih
                                    const selectedRabIds = rabData.map(r => r.id_rab);
                                    const availableRabsToShow = window.availableRabs.filter(rab =>
                                        !selectedRabIds.includes(rab.id_rab)
                                    );

                                    if (availableRabsToShow.length > 0) {
                                        availableRabsToShow.forEach((rab, index) => {
                                            tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${escapeHtml(rab.nama_template)}</td>
                                <td>${escapeHtml(rab.deskripsi || '')}</td>
                                <td class="text-right">Rp ${formatNumber(rab.total_nilai_disetujui || 0)}</td>
                                <td>
                                    <span class="badge badge-${getStatusClass(rab.status || 'Draft')}">
                                        ${rab.status || 'Draft'}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                        onclick="selectRab(${JSON.stringify(rab).replace(/"/g, '&quot;')})">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                        `);
                                        });
                                    } else {
                                        tableBody.append(
                                            '<tr><td colspan="6" class="text-center">Tidak ada RAB tersedia</td></tr>'
                                            );
                                    }

                                    feather.replace();
                                }

                                function escapeHtml(unsafe) {
                                    if (typeof unsafe !== 'string') return unsafe;
                                    return unsafe
                                        .replace(/&/g, "&amp;")
                                        .replace(/</g, "&lt;")
                                        .replace(/>/g, "&gt;")
                                        .replace(/"/g, "&quot;")
                                        .replace(/'/g, "&#039;");
                                }



                                // Fungsi format rupiah
                                function formatRupiah(angka) {
                                    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }

                                function loadSelectedRabs(rabs) {
                                    const tbody = $('#rab-component-table tbody');
                                    tbody.empty();

                                    let totalHpp = 0;

                                    rabs.forEach(rab => {
                                        // Pastikan status ada
                                        const status = rab.status || 'Draft';

                                        // Hitung total nilai disetujui
                                        const totalDisetujui = rab.details.reduce((sum, detail) =>
                                            sum + (parseFloat(detail.nilai_disetujui) || 0), 0);

                                        // Hitung total realisasi
                                        const totalRealisasi = rab.details.reduce((sum, detail) =>
                                            sum + (parseFloat(detail.realisasi_pemakaian) || 0), 0);

                                        // Hitung sisa
                                        const sisa = Math.round(totalDisetujui - totalRealisasi) || 0;

                                        // Hitung persentase
                                        const percentage = totalDisetujui > 0 ?
                                            Math.round((totalRealisasi / totalDisetujui) * 100) :
                                            0;

                                        // Tambahkan ke total HPP
                                        totalHpp += totalDisetujui;

                                        // Tambahkan row ke tabel
                                        tbody.append(`
                        <tr data-id="${rab.id_rab}">
                            <td>${escapeHtml(rab.nama_template)}</td>
                            <td>
                                <ul class="list-unstyled">
                                    ${rab.details.map(detail => `
                                        <li>• ${escapeHtml(detail.nama_komponen)}: 
                                        ${detail.jumlah} ${escapeHtml(detail.satuan || '')}</li>
                                    `).join('')}
                                </ul>
                            </td>
                            <td class="text-right">Rp ${formatNumber(totalDisetujui)}</td>
                            <td>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar ${getProgressBarClass(percentage)}" 
                                        style="width: ${percentage}%">
                                        ${percentage}%
                                    </div>
                                </div>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="realisasi[${rab.id_rab}]" 
                                        class="form-control realisasi-input" 
                                        value="${totalRealisasi}"
                                        data-rab-id="${rab.id_rab}"
                                        data-total-disetujui="${totalDisetujui}">
                                </div>
                                <small class="text-muted">
                                    Sisa: Rp ${formatNumber(sisa)} (${Math.max(0, 100 - percentage)}%)
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-${getStatusClass(status)}">
                                    ${status}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="removeRab($(this).closest('tr'))">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </td>
                            <input type="hidden" name="rabs[]" value="${rab.id_rab}">
                        </tr>
                    `);
                                    });

                                    // Update total HPP
                                    $('#total-hpp-rab').text('Rp ' + formatNumber(totalHpp));

                                    // Inisialisasi AutoNumeric untuk input realisasi
                                    initRealisasiInputs();
                                    feather.replace();
                                }

                                // Fungsi untuk menghitung total HPP realtime
                                function calculateTotalHppRab() {
                                    let total = 0;

                                    $('#rab-component-table tbody tr').each(function () {
                                        const rabId = $(this).data('id');
                                        const rab = rabData.find(r => r.id_rab == rabId);

                                        if (rab) {
                                            total += parseFloat(rab.total_nilai_disetujui) || 0;
                                        }
                                    });

                                    $('#total-hpp-rab').text('Rp ' + formatNumber(total));
                                }

                                function selectRab(rab) {
                                    try {
                                        const status = rab.status || 'Draft';
                                        // Cek apakah RAB sudah ada
                                        if ($('#rab-component-table tbody tr[data-id="' + rab.id_rab + '"]').length >
                                            0) {
                                            alert('RAB ini sudah ditambahkan');
                                            return;
                                        }

                                        // Pastikan data RAB valid
                                        if (!rab || !rab.id_rab || !rab.details) {
                                            console.error('Data RAB tidak valid:', rab);
                                            alert('Data RAB tidak valid');
                                            return;
                                        }

                                        // Hitung total nilai disetujui dan realisasi
                                        const totalNilaiDisetujui = rab.details.reduce((sum, detail) => {
                                            return sum + (parseFloat(detail.nilai_disetujui) || 0);
                                        }, 0);

                                        const totalRealisasi = rab.details.reduce((sum, detail) => {
                                            return sum + (parseFloat(detail.realisasi_pemakaian) || 0);
                                        }, 0);

                                        // Buat objek RAB yang akan ditambahkan
                                        const newRab = {
                                            id_rab: rab.id_rab,
                                            nama_template: rab.nama_template,
                                            total_nilai_disetujui: totalNilaiDisetujui,
                                            total_realisasi: totalRealisasi,
                                            details: rab.details,
                                            status: rab.status || 'Draft'
                                        };

                                        // Tambahkan ke rabData jika belum ada
                                        if (!rabData.some(r => r.id_rab == rab.id_rab)) {
                                            rabData.push(newRab);
                                        }

                                        // Hitung persentase realisasi
                                        const percentage = totalNilaiDisetujui > 0 ?
                                            Math.round((totalRealisasi / totalNilaiDisetujui) * 100) :
                                            0;

                                        // Buat row baru untuk tabel
                                        const newRow = `
                    <tr data-id="${rab.id_rab}">
                        <td>${escapeHtml(rab.nama_template)}</td>
                        <td>
                            <ul class="list-unstyled">
                                ${rab.details.map(detail => `
                                    <li>• ${escapeHtml(detail.nama_komponen)}: 
                                    ${detail.jumlah} ${escapeHtml(detail.satuan || '')} 
                                    (Rp ${formatNumber(detail.nilai_disetujui || 0)})</li>
                                `).join('')}
                            </ul>
                        </td>
                        <td>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar ${getProgressBarClass(percentage)}" 
                                    style="width: ${percentage}%">
                                    ${percentage}%
                                </div>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="realisasi[${rab.id_rab}]" 
                                    class="form-control realisasi-input" 
                                    value="${totalRealisasi}"
                                    data-rab-id="${rab.id_rab}">
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-${getStatusClass(newRab.status)}">
                                ${newRab.status}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" 
                                onclick="removeRab($(this).closest('tr'))">
                                <i data-feather="trash-2"></i>
                            </button>
                        </td>
                        <input type="hidden" name="rabs[]" value="${rab.id_rab}">
                    </tr>`;

                                        $('#rab-component-table tbody').append(newRow);

                                        initRealisasiInputs();

                                        // Hitung ulang total HPP
                                        calculateTotalHppRab();

                                        feather.replace();
                                    } catch (error) {
                                        console.error('Error in selectRab:', error);
                                        alert('Terjadi kesalahan saat menambahkan RAB');
                                    }
                                }

                                // Helper functions
                                function getStatusClass(status) {
                                    const statusMap = {
                                        'Ditransfer': 'info',
                                        'Disetujui Semua': 'success',
                                        'Disetujui dengan Revisi': 'warning',
                                        'Disetujui Sebagian': 'primary',
                                        'Draft': 'secondary'
                                    };
                                    return statusMap[status] || 'secondary';
                                }

                                function getProgressBarClass(percentage) {
                                    if (percentage > 100) return 'bg-danger';
                                    if (percentage > 80) return 'bg-warning';
                                    return 'bg-success';
                                }

                                function refreshAvailableRabs() {
                                    $.get(`${baseUrl}/rab_template/list`, function (response) {
                                        if (response.success) {
                                            window.availableRabs = response.data;
                                            if ($('#modal-rab-list').is(':visible')) {
                                                showRabList();
                                            }
                                        }
                                    });
                                }

                                // Fungsi untuk menghapus RAB
                                function removeRab(row) {
                                    if (!confirm('Apakah Anda yakin ingin menghapus RAB ini?')) {
                                        return;
                                    }

                                    const rabId = row.data('id');

                                    // Tambahkan input tersembunyi untuk menandai RAB yang dihapus
                                    $('#deleted-rabs-container').append(
                                        `<input type="hidden" name="deleted_rabs[]" value="${rabId}">`
                                    );

                                    // Hapus dari array data
                                    rabData = rabData.filter(r => r.id_rab != rabId);

                                    // Hapus dari tampilan
                                    row.remove();

                                    // Hitung ulang total HPP
                                    calculateTotalHppRab();

                                    refreshAvailableRabs();
                                }

                                function formatNumber(num) {
                                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }

                                function initRealisasiInputs() {
                                    $('.realisasi-input').each(function () {
                                        // Cek apakah sudah diinisialisasi
                                        if (!$(this).hasClass('autoNumeric')) {
                                            new AutoNumeric(this, {
                                                digitGroupSeparator: '.',
                                                decimalCharacter: ',',
                                                decimalPlaces: 0,
                                                unformatOnSubmit: true,
                                                modifyValueOnWheel: false
                                            });
                                        }
                                    });
                                }

                                

                                // Fungsi untuk hapus gambar (create)
                                function removeImageCreate(index) {
                                    const placeholder = $(`#modal-create .image-placeholder[data-index="${index}"]`);
                                    const form = $('#form-create');

                                    // 1. Hapus file input yang ada
                                    placeholder.find('input[type="file"]').remove();

                                    // 2. Hapus preview gambar
                                    placeholder.empty();

                                    // 3. Buat ulang struktur placeholder
                                    placeholder.html(`
                        <input type="file" name="images[]" id="create_product-image-${index}" 
                            style="display: none;" accept="image/*" data-index="${index}">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center justify-content-center bg-light" 
                                style="min-height: 150px; cursor: pointer;">
                                <i data-feather="plus" class="feather-xl text-muted"></i>
                            </div>
                        </div>
                    `);

                                    // 4. Pasang event handler untuk klik placeholder
                                    placeholder.find('.card').on('click', function () {
                                        $(`#create_product-image-${index}`).click();
                                    });

                                    // 5. Hapus data gambar dari form jika ada
                                    $(`#form-create input[name="uploaded_images[${index}][is_primary]"]`).remove();

                                    // 6. Update feather icons
                                    feather.replace();
                                }

                                // Fungsi untuk set gambar utama (create)
                                function setAsPrimaryCreate(index) {
                                    const container = $('#modal-create #create_image-preview-container');

                                    // Reset semua primary
                                    container.find('.primary-badge').remove();
                                    container.find('input[name^="uploaded_images"]').val('false');

                                    // Set primary baru
                                    const placeholder = container.find(`.image-placeholder[data-index="${index}"]`);

                                    // Tambahkan badge primary
                                    placeholder.find('.card').prepend(
                                        '<span class="badge bg-primary primary-badge">Cover</span>');

                                    // Update input is_primary
                                    let primaryInput = placeholder.find('input[name^="uploaded_images"]');
                                    if (primaryInput.length) {
                                        primaryInput.val('true');
                                    } else {
                                        placeholder.append(
                                            `<input type="hidden" name="uploaded_images[${index}][is_primary]" value="true">`
                                            );
                                    }

                                    feather.replace();
                                }

                                // Di dalam event handler change file input untuk create
                                $(document).on('change', '#modal-create input[type="file"][name="images[]"]', function(e) {
                                    const index = $(this).data('index');
                                    const file = this.files[0];
                                    const placeholder = $(`#modal-create .image-placeholder[data-index="${index}"]`);

                                    if (!file) return;

                                    const reader = new FileReader();

                                    reader.onload = function(event) {
                                        placeholder.html(`
                                            <input type="file" name="images[]" id="create_product-image-${index}" 
                                                class="d-none" accept="image/*" data-index="${index}">
                                            <div class="card h-100 image-preview-item">
                                                <img src="${event.target.result}" 
                                                    class="card-img-top zoom-image" 
                                                    style="height: 150px; object-fit: cover; cursor: pointer;"
                                                    data-img="${event.target.result}">
                                                ${index === 0 ? 
                                                    '<span class="badge bg-primary primary-badge">Cover</span>' : 
                                                    '<button type="button" class="btn btn-sm btn-primary set-primary-btn">' +
                                                        '<i data-feather="star"></i>' +
                                                    '</button>'
                                                }
                                                <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                                <input type="hidden" name="uploaded_images[${index}][is_primary]" value="${index === 0 ? 'true' : 'false'}">
                                            </div>
                                        `);

                                        // Re-attach the file
                                        const newFileInput = placeholder.find('input[type="file"]');
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(file);
                                        newFileInput[0].files = dataTransfer.files;

                                        // Pasang event handler
                                        placeholder.find('.delete-btn').on('click', function() {
                                            removeImageCreate(index);
                                        });

                                        placeholder.find('.set-primary-btn').on('click', function() {
                                            setAsPrimaryCreate(index);
                                        });

                                        // Tambahkan event click untuk zoom gambar
                                        placeholder.find('.zoom-image').on('click', function(e) {
                                            e.preventDefault();
                                            const imgUrl = $(this).data('img') || $(this).attr('src');
                                            $('#modalImage').attr('src', imgUrl);
                                            $('#imageModal').modal('show');
                                        });

                                        feather.replace();
                                    };
                                    reader.readAsDataURL(file);
                                });

                                // Submit form create
                                $('#form-create').on('submit', function(e) {
                                    e.preventDefault();
                                    
                                    const form = this; // Simpan referensi form yang benar
                                    
                                    // Cek masalah file input
                                    const {hasIssue, issueMessages} = checkFileInputs();
                                    
                                    if (hasIssue) {
                                        showConfirm('Masalah Upload Gambar', 
                                        issueMessages.join('\n') + '\n\nJika pada Gambar ada keterangan "No File Chosen" maka perbaiki dengan cara hapus gambar dan upload ulang sampai keterangan tersebut hilang!', 
                                        'Lanjutkan Simpan', 'Perbaiki Upload')
                                        .then((result) => {
                                            if (result.isConfirmed) {
                                                proceedWithSubmit(form); // Kirim form sebagai parameter
                                            }
                                        });
                                    } else {
                                        proceedWithSubmit(form); // Kirim form sebagai parameter
                                    }
                                });

                                function proceedWithSubmit(formElement) {
                                    // Unformat numeric inputs
                                    if (AutoNumeric.getAutoNumericElement('#create_harga_jual')) {
                                        AutoNumeric.getAutoNumericElement('#create_harga_jual').formUnformat();
                                    }

                                    if (AutoNumeric.getAutoNumericElement('#create_diskon')) {
                                        AutoNumeric.getAutoNumericElement('#create_diskon').formUnformat();
                                    }

                                    // Unformat harga varian
                                    $('.variant-price').each(function() {
                                        if (AutoNumeric.getAutoNumericElement(this)) {
                                            AutoNumeric.getAutoNumericElement(this).formUnformat();
                                        }
                                    });

                                    const formData = new FormData(formElement); // Gunakan formElement yang diterima

                                    $.ajax({
                                        url: $(formElement).attr('action'),
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        success: function(response) {
                                            $('#modal-create').modal('hide');
                                            table.ajax.reload();
                                            showAlert('Berhasil', 'Produk berhasil ditambahkan', 'success');
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr.responseText);
                                            if (xhr.status === 422) {
                                                const errors = xhr.responseJSON.errors;
                                                let errorMsg = '';
                                                for (const field in errors) {
                                                    errorMsg += `${errors[field][0]}\n`;
                                                }
                                                showAlert('Validasi Gagal', errorMsg, 'error');
                                            } else {
                                                showAlert('Error', 'Terjadi kesalahan. Silakan cek konsol untuk detail.', 'error');
                                            }
                                        }
                                    });
                                }


                                let currentStepCreate = 1;
                                const totalStepsCreate = 4;

                                // Inisialisasi AutoNumeric khusus untuk modal create
                                $('#modal-create').on('shown.bs.modal', function () {
                                    $('#variant-table-create tbody').empty();
                                    $('#create_image-preview-container').empty();

                                    for (let i = 0; i < 4; i++) {
                                        $('#create_image-preview-container').append(`
                            <div class="col-md-3 mb-3 image-placeholder" data-index="${i}">
                                <input type="file" name="images[]" id="create_product-image-${i}" 
                                    style="display: none;" accept="image/*" data-index="${i}">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center justify-content-center bg-light" 
                                        style="min-height: 150px; cursor: pointer;">
                                        <i data-feather="plus" class="feather-xl text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        `);

                                        // Pasang event handler untuk klik placeholder
                                        $(`#modal-create .image-placeholder[data-index="${i}"] .card`).on(
                                            'click',
                                            function () {
                                                $(`#create_product-image-${i}`).click();
                                                console.log('Placeholder clicked', i);
                                            });
                                    }

                                    feather.replace();
                                    // Reset step
                                    currentStepCreate = 1;
                                    updateStepButtonsCreate();

                                    // Hancurkan instance AutoNumeric yang mungkin sudah ada
                                    if (AutoNumeric.getAutoNumericElement('#create_harga_jual')) {
                                        AutoNumeric.getAutoNumericElement('#create_harga_jual').remove();
                                    }
                                    if (AutoNumeric.getAutoNumericElement('#create_diskon')) {
                                        AutoNumeric.getAutoNumericElement('#create_diskon').remove();
                                    }

                                    // Inisialisasi baru
                                    new AutoNumeric('#create_harga_jual', {
                                        digitGroupSeparator: '.',
                                        decimalCharacter: ',',
                                        decimalPlaces: 0,
                                        unformatOnSubmit: true,
                                        modifyValueOnWheel: false
                                    });

                                    new AutoNumeric('#create_diskon', {
                                        digitGroupSeparator: '.',
                                        decimalCharacter: ',',
                                        decimalPlaces: 0,
                                        unformatOnSubmit: true,
                                        modifyValueOnWheel: false
                                    });
                                });

                                $(document).on('click', '#modal-create .next-step', function () {
                                    if (validateStepCreate(currentStepCreate)) {
                                        if (currentStepCreate === 3) { // Sebelum pindah ke step 4 (konfirmasi)
                                            updateConfirmationDataCreate();
                                        }

                                        $('#modal-create .step-pane[data-step="' + currentStepCreate + '"]')
                                            .removeClass('active');
                                        $('#modal-create .step[data-step="' + currentStepCreate + '"]')
                                            .removeClass('active').addClass('completed');

                                        currentStepCreate++;

                                        $('#modal-create .step-pane[data-step="' + currentStepCreate + '"]')
                                            .addClass('active');
                                        $('#modal-create .step[data-step="' + currentStepCreate + '"]')
                                            .addClass('active');

                                        updateStepButtonsCreate();
                                    }
                                });

                                $(document).on('click', '#modal-create .prev-step', function () {
                                    $('#modal-create .step-pane[data-step="' + currentStepCreate + '"]')
                                        .removeClass('active');
                                    $('#modal-create .step[data-step="' + currentStepCreate + '"]').removeClass(
                                        'active');

                                    currentStepCreate--;

                                    $('#modal-create .step-pane[data-step="' + currentStepCreate + '"]')
                                        .addClass('active');
                                    $('#modal-create .step[data-step="' + currentStepCreate + '"]').addClass(
                                        'active').removeClass('completed');

                                    updateStepButtonsCreate();
                                });

                                // Update tombol step untuk modal create
                                function updateStepButtonsCreate() {
                                    $('#modal-create .prev-step').prop('disabled', currentStepCreate === 1);

                                    if (currentStepCreate === totalStepsCreate) {
                                        $('#modal-create .next-step').hide();
                                        $('#modal-create .btn-submit').show();
                                    } else {
                                        $('#modal-create .next-step').show();
                                        $('#modal-create .btn-submit').hide();
                                    }
                                }

                                // Validasi step untuk modal create
                                function validateStepCreate(step) {
                                if (step === 1) {
                                    const requiredFields = [
                                        {id: '#create_tipe_produk', name: 'Tipe Produk'},
                                        {id: '#create_nama_produk', name: 'Nama Produk'},
                                        {id: '#create_id_kategori', name: 'Kategori'},
                                        {id: '#create_id_satuan', name: 'Satuan'},
                                        {id: '#create_harga_jual', name: 'Harga Jual'}
                                    ];

                                    let isValid = true;
                                    let errorFields = [];

                                    requiredFields.forEach(field => {
                                        const element = $(field.id);
                                        let value = element.val();

                                        // Handle AutoNumeric field
                                        if (field.id === '#create_harga_jual' || field.id === '#create_diskon') {
                                            if (AutoNumeric.getAutoNumericElement(field.id)) {
                                                value = AutoNumeric.getAutoNumericElement(field.id).getNumber();
                                            }
                                        }

                                        if (!value) {
                                            element.addClass('is-invalid');
                                            errorFields.push(field.name);
                                            isValid = false;
                                        } else {
                                            element.removeClass('is-invalid');
                                        }
                                    });

                                    if (!isValid) {
                                        showAlert('Peringatan', 'Harap lengkapi field yang wajib diisi:\n- ' + errorFields.join('\n- '), 'warning');
                                        return false;
                                    }
                                }

                                return true;
                            }

                                // Update data konfirmasi untuk modal create
                                function updateConfirmationDataCreate() {
                                    // Basic info
                                    $('#modal-create #confirm-tipe-produk').text($(
                                        '#create_tipe_produk option:selected').text());
                                    $('#modal-create #confirm-nama-produk').text($('#create_nama_produk').val());
                                    $('#modal-create #confirm-kategori').text($('#create_id_kategori option:selected')
                                        .text());

                                    // Handle outlet jika ada
                                    if ($('#create_id_outlet').length) {
                                        $('#modal-create #confirm-outlet').text($('#create_id_outlet option:selected')
                                            .text());
                                    } else {
                                        $('#modal-create #confirm-outlet').text('-');
                                    }

                                    $('#modal-create #confirm-merk').text($('#create_merk').val() || '-');

                                    // Format harga jual
                                    let hargaJual = '0';
                                    if (AutoNumeric.getAutoNumericElement('#create_harga_jual')) {
                                        hargaJual = AutoNumeric.getAutoNumericElement('#create_harga_jual')
                                            .getFormatted();
                                    }
                                    $('#modal-create #confirm-harga-jual').text('Rp ' + hargaJual);

                                    // Format diskon
                                    let diskon = '0';
                                    if (AutoNumeric.getAutoNumericElement('#create_diskon')) {
                                        diskon = AutoNumeric.getAutoNumericElement('#create_diskon').getFormatted();
                                    }
                                    $('#modal-create #confirm-diskon').text('Rp ' + diskon);

                                    $('#modal-create #confirm-satuan').text($('#create_id_satuan option:selected')
                                    .text());
                                    $('#modal-create #confirm-spesifikasi').text($('#create_spesifikasi').val() || '-');

                                    // Images
                                    const confirmImagesContainer = $('#modal-create #confirm-images');
                                    confirmImagesContainer.empty();

                                    let hasImages = false;
                                    let imageIndex = 1;

                                    $('#modal-create .image-preview-item').each(function () {
                                        const imgSrc = $(this).find('img').attr('src');
                                        if (imgSrc) {
                                            hasImages = true;
                                            confirmImagesContainer.append(`
                                <div class="col-md-3 mb-3">
                                    <img src="${imgSrc}" class="img-thumbnail" style="height: 120px; object-fit: cover;">
                                    <p class="small text-muted mb-0 text-center">Gambar ${imageIndex++}</p>
                                </div>
                            `);
                                        }
                                    });

                                    // Juga cek dari file input
                                    $('#modal-create input[type="file"][name="images[]"]').each(function (index) {
                                        if (this.files && this.files[0] && !hasImages) {
                                            const reader = new FileReader();
                                            reader.onload = function (e) {
                                                confirmImagesContainer.append(`
                                    <div class="col-md-3 mb-3">
                                        <img src="${e.target.result}" class="img-thumbnail" style="height: 120px; object-fit: cover;">
                                        <p class="small text-muted mb-0 text-center">Gambar ${index + 1}</p>
                                    </div>
                                `);
                                            };
                                            reader.readAsDataURL(this.files[0]);
                                            hasImages = true;
                                        }
                                    });

                                    if (!hasImages) {
                                        confirmImagesContainer.html(
                                            '<div class="col-12"><p class="text-muted">Tidak ada gambar yang diupload</p></div>'
                                            );
                                    }

                                    // Update konfirmasi varian
                                    const confirmVariants = $('#confirm-variants tbody');
                                    confirmVariants.empty();

                                    $('#variant-table-create tbody tr').each(function() {
                                        const nama = $(this).find('input[name*="[nama_varian]"]').val();
                                        const deskripsi = $(this).find('textarea[name*="[deskripsi]"]').val();
                                        const harga = $(this).find('input[name*="[harga]"]').val();
                                        const isDefault = $(this).find('.default-variant-radio').is(':checked');

                                        // Unformat harga jika menggunakan AutoNumeric
                                        let formattedHarga = harga;
                                        const priceInput = $(this).find('input[name*="[harga]"]')[0];
                                        if (priceInput && AutoNumeric.getAutoNumericElement(priceInput)) {
                                            formattedHarga = AutoNumeric.getAutoNumericElement(priceInput).getFormatted();
                                        }

                                        confirmVariants.append(`
                                            <tr>
                                                <td>${nama || '-'}</td>
                                                <td>${deskripsi || '-'}</td>
                                                <td>${formattedHarga ? 'Rp ' + formattedHarga : '-'}</td>
                                                <td>${isDefault ? '<span class="badge bg-primary">Default</span>' : ''}</td>
                                            </tr>
                                        `);
                                    });

                                    if (confirmVariants.find('tr').length === 0) {
                                        confirmVariants.append(
                                            '<tr><td colspan="4" class="text-muted">Tidak ada varian</td></tr>'
                                        );
                                    }

                                }
                                // Fungsi untuk menambahkan baris varian baru
                                function addVariantRow(tableId, index = null, data = {}) {
                                        const isEdit = tableId === 'variant-table';
                                        const tbody = $(`#${tableId} tbody`);
                                        const newIndex = index !== null ? index : tbody.find('tr').length;

                                        // Format harga jika ada
                                        const formattedHarga = data.harga ? formatNumber(data.harga) : '';

                                        const row = `
                                            <tr>
                                                <td>
                                                    <input type="text" name="variants[${newIndex}][nama_varian]" 
                                                        class="form-control" value="${data.nama_varian || ''}" required>
                                                </td>
                                                <td>
                                                    <textarea name="variants[${newIndex}][deskripsi]" 
                                                        class="form-control">${data.deskripsi || ''}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="variants[${newIndex}][harga]" 
                                                        class="form-control variant-price" 
                                                        value="${formattedHarga}"
                                                        ${data.is_default ? 'readonly' : ''}>
                                                </td>
                                                <td class="text-center">
                                                    <input type="radio" name="is_default" value="${newIndex}" 
                                                        ${data.is_default ? 'checked' : ''}
                                                        class="default-variant-radio">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger remove-variant">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </td>
                                                ${isEdit && data.id ? `<input type="hidden" name="variants[${newIndex}][id]" value="${data.id}">` : ''}
                                            </tr>`;

                                        tbody.append(row);

                                        // Inisialisasi AutoNumeric untuk harga
                                        const priceInput = $(`#${tableId} tbody tr:last-child .variant-price`);
                                        new AutoNumeric(priceInput[0], {
                                            digitGroupSeparator: '.',
                                            decimalCharacter: ',',
                                            decimalPlaces: 0,
                                            unformatOnSubmit: true,
                                            modifyValueOnWheel: false
                                        });

                                        // Jika default, set harga dari produk utama
                                        if (data.is_default) {
                                            const basePrice = tableId === 'variant-table-create' ?
                                                AutoNumeric.getAutoNumericElement('#create_harga_jual').getNumber() :
                                                AutoNumeric.getAutoNumericElement('#harga_jual').getNumber();

                                            AutoNumeric.getAutoNumericElement(priceInput[0]).set(basePrice);
                                            priceInput.prop('readonly', true);
                                            priceInput.addClass('bg-light');
                                        }

                                        feather.replace();
                                    }

                                    // Event handler untuk tombol tambah varian (create)
                                    $(document).on('click', '#add-variant-create', function () {
                                        console.log('add-variant-create clicked');
                                        addVariantRow('variant-table-create');
                                    });

                                    // Event handler untuk tombol tambah varian (edit)
                                    $(document).on('click', '#add-variant', function () {
                                        addVariantRow('variant-table');
                                    });

                                    // Event handler untuk menghapus varian
                                    $(document).on('click', '.remove-variant', function () {
                                        $(this).closest('tr').remove();
                                        reindexVariantRows();
                                    });

                                    // Event handler untuk radio button default yang lebih aman
                                    $(document).on('change', '.default-variant-radio', function() {
                                        const tableId = $(this).closest('table').attr('id');
                                        const rowIndex = $(this).val();
                                        
                                        // Dapatkan harga dasar dengan pengecekan null
                                        let basePrice;
                                        try {
                                            if (tableId === 'variant-table-create') {
                                                const createHarga = AutoNumeric.getAutoNumericElement('#create_harga_jual');
                                                basePrice = createHarga ? createHarga.getNumber() : 0;
                                            } else {
                                                const editHarga = AutoNumeric.getAutoNumericElement('#harga_jual');
                                                basePrice = editHarga ? editHarga.getNumber() : 0;
                                            }
                                        } catch (e) {
                                            console.error('Error getting base price:', e);
                                            basePrice = 0;
                                        }
                                        
                                        // Update semua input harga
                                        $(`#${tableId} tbody tr`).each(function(index) {
                                            const priceInput = $(this).find('.variant-price')[0];
                                            const isDefault = $(this).find('.default-variant-radio').is(':checked');
                                            
                                            if (!priceInput) return;
                                            
                                            const autoNumericInstance = AutoNumeric.getAutoNumericElement(priceInput);
                                            if (!autoNumericInstance) return;
                                            
                                            if (isDefault) {
                                                autoNumericInstance.set(basePrice);
                                                $(priceInput).prop('readonly', true).addClass('bg-light');
                                            } else {
                                                $(priceInput).prop('readonly', false).removeClass('bg-light');
                                            }
                                        });
                                    });

                                    // Fungsi untuk mengindeks ulang baris varian
                                    function reindexVariantRows() {
                                        $('table[id^="variant-table"] tbody tr').each(function (index) {
                                            $(this).find('input, textarea').each(function () {
                                                const name = $(this).attr('name');
                                                if (name) {
                                                    $(this).attr('name', name.replace(/\[\d+\]/,
                                                        `[${index}]`));
                                                }
                                            });
                                            $(this).find('.default-variant-radio').val(index);
                                        });
                                    }

                                    // Update konfirmasi varian
                                    const confirmVariants = $('#confirm-variants tbody');
                                    confirmVariants.empty();

                                    $('#variant-table-create tbody tr').each(function () {
                                        const nama = $(this).find('input[name*="[nama_varian]"]').val();
                                        const deskripsi = $(this).find('textarea[name*="[deskripsi]"]').val();
                                        const harga = $(this).find('input[name*="[harga]"]').val();
                                        const isDefault = $(this).find('.default-variant-radio').is(':checked');

                                        confirmVariants.append(`
                            <tr>
                                <td>${nama || '-'}</td>
                                <td>${deskripsi || '-'}</td>
                                <td>${harga ? 'Rp ' + formatNumber(harga) : '-'}</td>
                                <td>${isDefault ? '<span class="badge bg-primary">Default</span>' : ''}</td>
                            </tr>
                        `);
                                    });

                                    if (confirmVariants.find('tr').length === 0) {
                                        confirmVariants.append(
                                            '<tr><td colspan="4" class="text-muted">Tidak ada varian</td></tr>');
                                    }
                                    
                                    function loadVariants(productId) {
                                        if (!productId) return;
                                        productData = null;

                                        $.get(`${baseUrl}/produk/${productId}/variants`, function(variants) {
                                            const tbody = $('#variant-table tbody');
                                            tbody.empty();

                                            if (variants.length > 0) {
                                                variants.forEach((variant, index) => {
                                                    addVariantRow('variant-table', index, {
                                                        nama_varian: variant.nama_varian,
                                                        deskripsi: variant.deskripsi,
                                                        harga: variant.harga,
                                                        is_default: variant.is_default,
                                                        id: variant.id
                                                    });
                                                });
                                            } else {
                                                // Tambahkan baris kosong jika tidak ada varian
                                                addDefaultVariantRow(productData);
                                            }
                                        }).fail(function(error) {
                                            console.error('Gagal memuat varian:', error);
                                            alert('Gagal memuat data varian');
                                        });
                                    }

                                    function addDefaultVariantRow(productData) {
                                        const defaultData = {
                                            nama_varian: productData?.nama_produk || 'Produk Utama',
                                            deskripsi: 'Tanpa varian',
                                            harga: productData?.harga_jual || 0,
                                            is_default: true
                                        };
                                        addVariantRow('variant-table', 0, defaultData);
                                    }

                                    // Event handler saat tab varian diklik
                                    $('#variants-tab').on('click', function() {
                                        if (currentProductId) {
                                            loadVariants(currentProductId);
                                        }
                                    });

                                    // Inisialisasi saat modal edit dibuka
                                    $('#modal-form').on('shown.bs.modal', function() {
                                        // Load varian pertama kali
                                        if (currentProductId) {
                                            loadVariants(currentProductId);
                                        }
                                    });

                                    // Fungsi untuk menambahkan baris komponen
                                    function addComponentRow(index = null, data = {}) {
                                        const tbody = $('#component-table tbody');
                                        const newIndex = index !== null ? index : tbody.find('tr').length;
                                        
                                        const row = `
                                            <tr>
                                                <td>
                                                    <select class="form-control component-product-select" 
                                                        name="components[${newIndex}][product_id]" 
                                                        data-index="${newIndex}" required>
                                                        ${data.product_id ? `<option value="${data.product_id}" selected>${data.product_text}</option>` : '<option value="">Pilih Produk</option>'}
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="components[${newIndex}][qty]" 
                                                        class="form-control component-qty" min="1" value="${data.qty || 1}" required>
                                                </td>
                                                <td>
                                                    <span class="original-price" data-index="${newIndex}">
                                                        ${data.original_price ? 'Rp ' + formatNumber(data.original_price) : '-'}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="component-subtotal" data-index="${newIndex}">
                                                        ${data.subtotal ? '<span style="text-decoration: line-through;">Rp ' + formatNumber(data.subtotal) + '</span>' : '-'}
                                                    </span>
                                                    <input type="hidden" class="component-subtotal-value" 
                                                        name="components[${newIndex}][subtotal]" value="${data.subtotal || 0}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger remove-component">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                    ${data.id ? `<input type="hidden" name="components[${newIndex}][id]" value="${data.id}">` : ''}
                                                </td>
                                            </tr>`;
                                        
                                        tbody.append(row);
                                        feather.replace();
                                        
                                        // Inisialisasi Select2 hanya jika belum ada data produk
                                        if (!data.product_id) {
                                            const select = $(`select.component-product-select[data-index="${newIndex}"]`);
                                            select.select2({
                                                ajax: {
                                                url: `${baseUrl}/api/produk/search`,
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        q: params.term,
                                                        page: params.page
                                                    };
                                                },
                                                processResults: function(data, params) {
                                                    return {
                                                        results: data.data.map(product => ({
                                                            id: product.id_produk,
                                                            text: product.kode_produk + ' - ' + product.nama_produk,
                                                            harga: product.harga_jual
                                                        })),
                                                        pagination: {
                                                            more: data.next_page_url ? true : false
                                                        }
                                                    };
                                                },
                                                cache: true
                                            },
                                            minimumInputLength: 1,
                                            templateResult: formatProduct,
                                            templateSelection: formatProductSelection
                                            });
                                            
                                            select.on('select2:select', function(e) {
                                                updateComponentPrice($(this).data('index'), e.params.data.harga);
                                            });
                                        }
                                        
                                        $(`input[name="components[${newIndex}][qty]"]`).on('change', function() {
                                            const rowIndex = $(this).closest('tr').index();
                                            const price = parseFloat($(this).closest('tr').find('.original-price').data('price')) || 0;
                                            updateComponentPrice(rowIndex, price);
                                        });
                                        
                                        // Jika ada data, set harga asli
                                        if (data.original_price) {
                                            $(`.original-price[data-index="${newIndex}"]`).data('price', data.original_price);
                                        }
                                    }

                                    // Format tampilan Select2
                                    function formatProduct(product) {
                                        if (!product.id) return product.text;
                                        
                                        return $(
                                            `<div>${product.text} <small class="text-muted float-right">Rp ${formatNumber(product.harga)}</small></div>`
                                        );
                                    }

                                    function formatProductSelection(product) {
                                        if (!product.id) return product.text;
                                        
                                        return product.text;
                                    }

                                    // Update harga komponen
                                    function updateComponentPrice(index, price) {
                                        const qty = $(`input[name="components[${index}][qty]"]`).val() || 1;
                                        const subtotal = price * qty;
                                        
                                        $(`.original-price[data-index="${index}"]`)
                                            .html('Rp ' + formatNumber(price))
                                            .data('price', price);
                                            
                                        $(`.component-subtotal[data-index="${index}"]`).html(
                                            `<span style="text-decoration: line-through;">Rp ${formatNumber(subtotal)}</span>`
                                        );
                                        $(`.component-subtotal-value[data-index="${index}"]`).val(subtotal);
                                    }

                                    // Event handler untuk tombol tambah komponen
                                    $(document).on('click', '#add-component', function() {
                                        addComponentRow();
                                    });

                                    // Event handler untuk hapus komponen
                                    $(document).on('click', '.remove-component', function() {
                                        const row = $(this).closest('tr');
                                        const componentId = row.find('input[name*="[id]"]').val();
                                        
                                        if (componentId) {
                                            // Jika komponen sudah ada di database, tambahkan marker untuk dihapus
                                            $('#modal-form form').append(
                                                `<input type="hidden" class="deleted-component" name="deleted_components[]" value="${componentId}">`
                                            );
                                        }
                                        
                                        row.remove();
                                    });


                                    function loadProductComponents(productId) {
                                        if (!productId) return;

                                        $.get(`${baseUrl}/produk/${productId}/components`, function(components) {
                                            const tbody = $('#component-table tbody');
                                            tbody.empty();

                                            if (components.length > 0) {
                                                components.forEach((component, index) => {
                                                    addComponentRow(index, {
                                                        product_id: component.component_id,
                                                        product_text: component.component.kode_produk + ' - ' + component.component.nama_produk,
                                                        qty: component.qty,
                                                        original_price: component.component.harga_jual,
                                                        subtotal: component.subtotal,
                                                        id: component.id
                                                    });
                                                });
                                            }
                                        }).fail(function(error) {
                                            console.error('Gagal memuat komponen:', error);
                                        });
                                    }

                                    // Fungsi untuk menampilkan alert biasa
                                    function showAlert(title, text, icon = 'success') {
                                        Swal.fire({
                                            title: title,
                                            text: text,
                                            icon: icon,
                                            confirmButtonText: 'OK'
                                        });
                                    }

                                    // Fungsi untuk konfirmasi
                                    function showConfirm(title, text, confirmButtonText = 'Ya', cancelButtonText = 'Tidak') {
                                        return Swal.fire({
                                            title: title,
                                            text: text,
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: confirmButtonText,
                                            cancelButtonText: cancelButtonText
                                        });
                                    }

                                    function checkFileInputs() {
                                        let hasIssue = false;
                                        let issueMessages = [];
                                        
                                        // Cek semua input file
                                        $('input[type="file"][name="images[]"]').each(function(index) {
                                            const fileInput = $(this)[0];
                                            const placeholder = $(this).closest('.image-placeholder');
                                            
                                            // Jika input file ada tetapi tidak ada file yang dipilih DAN ada preview gambar
                                            if (fileInput && !fileInput.files.length && placeholder.find('img').length) {
                                                hasIssue = true;
                                                issueMessages.push(`Gambar ${index + 1} mungkin tidak akan tersimpan. Harap upload ulang.`);
                                                
                                                // Tambahkan border merah sebagai indikator visual
                                                placeholder.find('.card').css('border', '2px solid red');
                                            } else {
                                                // Reset border jika tidak ada masalah
                                                placeholder.find('.card').css('border', '');
                                            }
                                        });
                                        
                                        return {hasIssue, issueMessages};
                                    }

                                    function validateFile(file, index) {
                                        const issues = [];
                                        
                                        // Validasi nama file
                                        if (file.name.includes('-')) {
                                            issues.push(`• Nama file "${file.name}" mengandung karakter "-" (disarankan menggunakan underscore "_")`);
                                        }
                                        
                                        if (file.name.includes(' ')) {
                                            issues.push(`• Nama file "${file.name}" mengandung spasi (disarankan menggunakan underscore "_")`);
                                        }
                                        
                                        if (file.name.length > 50) {
                                            issues.push(`• Nama file "${file.name}" terlalu panjang (max 50 karakter)`);
                                        }
                                        
                                        // Validasi ukuran file
                                        const maxSize = 2 * 1024 * 1024; // 2MB
                                        if (file.size > maxSize) {
                                            issues.push(`• Ukuran file "${file.name}" terlalu besar (max 2MB)`);
                                        }
                                        
                                        // Validasi tipe file
                                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                                        if (!allowedTypes.includes(file.type)) {
                                            issues.push(`• Tipe file "${file.name}" tidak didukung (hanya JPEG, PNG, GIF)`);
                                        }
                                        
                                        return {
                                            isValid: issues.length === 0,
                                            messages: issues,
                                            fileIndex: index + 1
                                        };
                                    }
                            </script>
                        @endpush
