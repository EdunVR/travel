<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="id_member" class="col-lg-2 col-lg-offset-1 control-label">Customer</label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nama_member" placeholder="Cari Customer..." readonly>
                                <span class="input-group-btn">
                                    <button onclick="tampilMember()" class="btn btn-info btn-flat" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <input type="hidden" name="id_member" id="id_member">
                            <!-- Hasil pencarian Customer -->
                            <div id="hasil-pencarian-member" class="list-group" style="display: none;">
                                <!-- Hasil pencarian akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_ongkir" class="col-lg-2 col-lg-offset-1 control-label">Ongkos Kirim</label>
                        <div class="col-lg-6">
                            <select name="id_ongkir" id="id_ongkir" class="form-control" required>
                                <option value="">Pilih Ongkos Kirim</option>
                                @foreach ($ongkosKirim as $ongkir)
                                    <option value="{{ $ongkir->id_ongkir }}">{{ $ongkir->daerah }} - {{ number_format($ongkir->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Form Pencarian Produk -->
                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2 col-lg-offset-1 control-label">Tambah Produk</label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nama_produk" placeholder="Cari Produk..." readonly>
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <input type="hidden" id="id_produk">
                            <!-- Hasil pencarian Produk -->
                            <div id="hasil-pencarian-produk" class="list-group" style="display: none;">
                                <!-- Hasil pencarian akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabel Produk Terpilih -->
                    <div class="form-group row">
                        <div class="col-lg-8 col-lg-offset-3">
                            <table class="table table-bordered table-striped" id="table-produk-terpilih">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Biaya Service (/mesin)</th>
                                        <th width="20%">Closing Type</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Produk terpilih akan ditampilkan di sini -->
                                </tbody>
                            </table>
                            @if(isset($selectedProdukIds) && is_array($selectedProdukIds))
                                @foreach($selectedProdukIds as $produkId)
                                    <input type="hidden" name="produk[]" value="{{ $produkId }}">
                                    <input type="hidden" name="biaya_service_produk[]" value="{{ $selectedProdukBiaya[$produkId] ?? 0 }}">
                                    <input type="hidden" name="closing_type_produk[]" value="{{ $selectedProdukClosingTypes[$produkId] ?? 'jual_putus' }}">
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Produk -->
<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog" aria-labelledby="modal-produk">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Produk</h4>
            </div>
            <div class="modal-body">
                <table class="table table-stiped table-bordered table-produk">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Member -->
<div class="modal fade" id="modal-member" tabindex="-1" role="dialog" aria-labelledby="modal-member">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Customer</h4>
            </div>
            <div class="modal-body">
                <table class="table table-stiped table-bordered table-member">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
