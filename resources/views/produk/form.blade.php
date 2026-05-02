<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-produk" action="" method="post" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div id="deleted-rabs-container"></div>
            
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>  
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#produk-info" aria-controls="produk-info" role="tab" data-toggle="tab">
                                <i data-feather="info"></i> Informasi Produk
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#produk-images" aria-controls="produk-images" role="tab" data-toggle="tab">
                                <i data-feather="image"></i> Gambar Produk
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#variants" aria-controls="variants" role="tab" data-toggle="tab">
                                <i data-feather="dollar-sign"></i> Varian Produk
                            </a>
                        </li>
                        <!-- Tab RAB (akan muncul jika bukan barang dagang) -->
                        <li role="presentation" class="rab-tab" style="display: none;">
                            <a href="#produk-rab" aria-controls="produk-rab" role="tab" data-toggle="tab">
                                <i data-feather="dollar-sign"></i> RAB
                            </a>
                        </li>
                        
                        <li role="presentation" id="tab-components">
                            <a href="#components" aria-controls="components" role="tab" data-toggle="tab">
                                <i data-feather="package"></i> Komponen Produk
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab Informasi Produk -->
                        <div role="tabpanel" class="tab-pane active" id="produk-info">
                            <div class="form-group row mt-3">
                                <label for="tipe_produk" class="col-lg-2 col-lg-offset-1 control-label">Tipe Produk</label>
                                <div class="col-lg-6">
                                    <select name="tipe_produk" id="tipe_produk" class="form-control" required>
                                        @foreach ($productTypes as $key => $type)
                                            <option value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Nama</label>
                                <div class="col-lg-6">
                                    <input type="text" name="nama_produk" id="nama_produk" class="form-control" required autofocus>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="id_kategori" class="col-lg-2 col-lg-offset-1 control-label">Kategori</label>
                                <div class="col-lg-6">
                                    <select name="id_kategori" id="id_kategori" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($kategori as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            
                            @if($outlets->count() > 1)
                            <div class="form-group row">
                                <label for="id_outlet" class="col-md-2 col-md-offset-1 control-label">Outlet</label>
                                <div class="col-md-9">
                                    <select name="id_outlet" id="id_outlet" class="form-control" required>
                                        <option value="">Pilih Outlet</option>
                                        @foreach ($outlets as $outlet)
                                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            @endif
                            
                            <div class="form-group row">
                                <label for="merk" class="col-lg-2 col-lg-offset-1 control-label">Merk</label>
                                <div class="col-lg-6">
                                    <input type="text" name="merk" id="merk" class="form-control">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="harga_jual" class="col-lg-2 col-lg-offset-1 control-label">Harga Jual</label>
                                <div class="col-lg-6">
                                    <input type="text" name="harga_jual" id="harga_jual" class="form-control" required 
                                        value="{{ old('harga_jual', $produk->harga_jual ?? '') }}">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 col-lg-offset-1 control-label">Diskon</label>
                                <div class="col-lg-6">
                                    <input type="text" name="diskon" id="diskon" class="form-control" value="0">
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="id_satuan" class="col-lg-2 col-lg-offset-1 control-label">Satuan</label>
                                <div class="col-lg-6">
                                    <select name="id_satuan" id="id_satuan" class="form-control" required>
                                        <option value="">Pilih Satuan</option>
                                        @foreach ($satuan as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block with-errors"></span>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="spesifikasi" class="col-lg-2 col-lg-offset-1 control-label">Keterangan</label>
                                <div class="col-lg-6">
                                    <textarea class="form-control" id="spesifikasi" name="spesifikasi" rows="3">{{ old('spesifikasi') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Konten tab Gambar Produk -->
                        <div role="tabpanel" class="tab-pane" id="produk-images">
                            <div class="form-group row mt-3">
                                <label class="col-lg-2 col-lg-offset-1 control-label">Upload Gambar</label>
                                <div class="col-lg-6">
                                    <small class="text-muted d-block">Maksimal 4 gambar. Gambar pertama akan menjadi cover.</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3" id="image-preview-container">
                                @for($i = 0; $i < 4; $i++)
                                <div class="col-md-3 mb-3 image-placeholder" data-index="{{ $i }}">
                                    <input type="file" name="images[]" id="product-image-{{ $i }}" 
                                        class="d-none" accept="image/*" data-index="{{ $i }}">
                                    <div class="card h-100">
                                        <div class="card-body d-flex align-items-center justify-content-center bg-light" 
                                            style="min-height: 150px; cursor: pointer;" 
                                            onclick="triggerFileInput({{ $i }})">
                                            <i data-feather="plus" class="feather-xl text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Tab VARIAN -->
                        <div role="tabpanel" class="tab-pane" id="variants">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Kelola varian produk di sini.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="variant-table">
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
                                        @if(isset($produk) && $produk->variants->count() > 0)
                                            @foreach($produk->variants as $index => $variant)
                                            <tr>
                                                <td>
                                                    <input type="text" name="variants[{{$index}}][nama_varian]" 
                                                        class="form-control" value="{{ $variant->nama_varian }}" required>
                                                </td>
                                                <td>
                                                    <textarea name="variants[{{$index}}][deskripsi]" 
                                                        class="form-control">{{ $variant->deskripsi }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="variants[{{$index}}][harga]" 
                                                        class="form-control variant-price" 
                                                        value="{{ format_uang($variant->harga) }}"
                                                        {{ $variant->is_default ? 'readonly' : '' }}>
                                                </td>
                                                <td class="text-center">
                                                    <input type="radio" name="is_default" value="{{$index}}" 
                                                        {{ $variant->is_default ? 'checked' : '' }}
                                                        class="default-variant-radio">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger remove-variant">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                                <input type="hidden" name="variants[{{$index}}][id]" value="{{ $variant->id }}">
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-variant">
                                    <i class="fa fa-plus"></i> Tambah Varian
                                </button>
                            </div>
                        </div>

                        <!-- Tab RAB -->
                        <div role="tabpanel" class="tab-pane" id="produk-rab">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRabList()">
                                        <i data-feather="plus"></i> Pilih RAB
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <table class="table table-bordered" id="rab-component-table">
                                        <thead>
                                            <tr>
                                                <th width="20%">Nama RAB</th>
                                                <th width="30%">Detail Komponen</th>
                                                <th width="15%" class="text-right">Total Disetujui</th>
                                                <th width="20%">Realisasi Pemakaian</th>
                                                <th width="10%">Status</th>
                                                <th width="5%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan diisi via JavaScript -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-right">Total HPP RAB:</th>
                                                <th id="total-hpp-rab">Rp 0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Pengaturan Produk -->
                        <div role="tabpanel" class="tab-pane" id="produk-settings">
                            <div class="form-group row mt-3">
                                <label for="track_inventory" class="col-lg-2 col-lg-offset-1 control-label">Kelola Stok</label>
                                <div class="col-lg-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="track_inventory" id="track_inventory" value="1" checked>
                                            Lacak persediaan untuk produk ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row inventory-settings">
                                <label for="metode_hpp" class="col-lg-2 col-lg-offset-1 control-label">Metode HPP</label>
                                <div class="col-lg-6">
                                    <select name="metode_hpp" id="metode_hpp" class="form-control">
                                        <option value="FIFO">FIFO (First In First Out)</option>
                                        <option value="LIFO">LIFO (Last In First Out)</option>
                                        <option value="Rata-rata">Rata-rata</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row inventory-settings">
                                <label for="stok_minimum" class="col-lg-2 col-lg-offset-1 control-label">Stok Minimum</label>
                                <div class="col-lg-6">
                                    <input type="number" name="stok_minimum" id="stok_minimum" class="form-control" value="0">
                                </div>
                            </div>
                            
                            <div class="form-group row travel-settings" style="display: none;">
                                <label for="jenis_paket" class="col-lg-2 col-lg-offset-1 control-label">Jenis Paket</label>
                                <div class="col-lg-6">
                                    <select name="jenis_paket" id="jenis_paket" class="form-control">
                                        <option value="Umroh Reguler">Umroh Reguler</option>
                                        <option value="Umroh Plus">Umroh Plus</option>
                                        <option value="Haji Khusus">Haji Khusus</option>
                                        <option value="Haji Reguler">Haji Reguler</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row travel-settings" style="display: none;">
                                <label for="keberangkatan_template_id" class="col-lg-2 col-lg-offset-1 control-label">Template RAB</label>
                                <div class="col-lg-6">
                                    <select name="keberangkatan_template_id" id="keberangkatan_template_id" class="form-control">
                                        <option value="">Pilih Template RAB</option>
                                        @foreach ($rabTemplates as $key => $template)
                                            <option value="{{ $key }}">{{ $template }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-sm btn-default mt-2" onclick="showRabForm()">
                                        <i data-feather="plus"></i> Buat Template Baru
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Komponen Produk -->
                        <div class="tab-pane fade" id="components" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Tambahkan produk lain sebagai komponen bundling.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="component-table">
                                    <thead>
                                        <tr>
                                            <th width="40%">Produk</th>
                                            <th width="15%">Qty</th>
                                            <th width="25%">Harga Normal</th>
                                            <th width="15%">Subtotal</th>
                                            <th width="5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Baris akan ditambahkan via JavaScript -->
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-component">
                                    <i class="fa fa-plus"></i> Tambah Komponen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary">
                        <i data-feather="save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal">
                        <i data-feather="x"></i> Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tambahkan di bagian bawah form.blade.php -->
<div class="modal fade" id="modal-rab-list" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilih Template RAB</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="rab-list-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Nama Template</th>
                            <th>Deskripsi</th>
                            <th>Total Biaya</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Di bagian script produk/form.blade.php
        // Toggle settings berdasarkan tipe produk
        $('#tipe_produk').change(function() {
            const productType = $(this).val();
            
            if (productType === 'paket_travel') {
                $('.inventory-settings').hide();
                $('.travel-settings').show();
                $('.rab-tab').show();
                $('#track_inventory').prop('checked', false);
            } else if (productType === 'barang_dagang') {
                $('.inventory-settings').show();
                $('.travel-settings').hide();
                $('.rab-tab').hide();
                $('#track_inventory').prop('checked', true);
            } else {
                $('.inventory-settings').hide();
                $('.travel-settings').hide();
                $('.rab-tab').hide();
                $('#track_inventory').prop('checked', false);
            }
        }).trigger('change');
        
        // Hitung total HPP dari komponen
        $(document).on('keyup', '.component-price, .component-qty', function() {
            const row = $(this).closest('tr');
            const price = parseFloat(row.find('.component-price').val()) || 0;
            const qty = parseFloat(row.find('.component-qty').val()) || 0;
            const subtotal = price * qty;
            
            row.find('.component-subtotal').val(subtotal);
            calculateTotalHpp();
        });

        // Fungsi untuk RAB
        

        function previewImage(input, index) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const isPrimary = index === 0 ? 'true' : 'false';
                        const placeholder = $(`.image-placeholder[data-index="${index}"]`);
                        
                        placeholder.html(`
                            <input type="file" name="images[]" id="product-image-${index}" 
                                accept="image/*" onchange="previewImage(this, ${index})" multiple>
                            <div class="card h-100 image-preview-item">
                                <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                ${index === 0 ? 
                                    '<span class="badge primary-badge">Cover</span>' : 
                                    '<button type="button" class="btn btn-sm btn-primary set-primary-btn" onclick="setAsPrimary(this, ' + index + ')">' +
                                        '<i data-feather="star"></i>' +
                                    '</button>'
                                }
                                <button type="button" class="btn btn-sm btn-danger delete-btn" onclick="removeImage(this, ' + index + ')">
                                    <i data-feather="trash-2"></i>
                                </button>
                                <input type="hidden" name="uploaded_images[${index}][is_primary]" value="${isPrimary}">
                            </div>
                        `);
                        feather.replace();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
    });

    
    function calculateTotalHpp() {
        let total = 0;
        $('.component-subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total-hpp').text(total.toLocaleString());
    }
    
    function showRabForm() {
        $('#modal-rab').modal('show');
    }

    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
