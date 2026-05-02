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
                        <label for="customer" class="col-lg-2 col-lg-offset-1 control-label">Customer</label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="customer_display" placeholder="Pilih Customer..." readonly>
                                <input type="hidden" name="customer_id" id="customer_id">
                                <input type="hidden" name="customer_type" id="customer_type" value="member">
                                <span class="input-group-btn">
                                    <button onclick="tampilCustomer()" class="btn btn-info btn-flat" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_ongkir" class="col-lg-2 col-lg-offset-1 control-label">Ongkos Kirim</label>
                        <div class="col-lg-6">
                            <select name="id_ongkir" id="id_ongkir" class="form-control" required>
                                <option value="">Pilih Ongkos Kirim</option>
                                @foreach ($ongkosKirim as $ongkir)
                                    <option value="{{ $ongkir->id_ongkir }}">{{ $ongkir->daerah }} - Rp {{ number_format($ongkir->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Form Pencarian Produk -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-lg-offset-1 control-label">Tambah Produk</label>
                        <div class="col-lg-6">
                            <button type="button" onclick="tampilProduk()" class="btn btn-info btn-flat">
                                <i class="fa fa-search"></i> Pilih Produk
                            </button>
                        </div>
                    </div>
                    
                    <!-- Tabel Produk Terpilih -->
                    <div class="form-group row">
                        <div class="col-lg-10 col-lg-offset-1">
                            <table class="table table-bordered table-striped" id="table-produk-terpilih">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th width="20%">Harga Normal</th>
                                        <th width="25%">Harga Khusus</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Produk terpilih akan ditampilkan di sini -->
                                </tbody>
                            </table>
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

<!-- Modal Customer untuk Customer Price -->
<div class="modal fade" id="modal-customer-customer-price" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pilih Customer</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" id="search-customer-customer-price" class="form-control" placeholder="Cari customer...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customer-list-customer-price">
                            <!-- Data customer akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div id="customer-pagination-customer-price" class="text-center" style="display: none;">
                    <nav>
                        <ul class="pagination pagination-sm" id="pagination-links-customer-price">
                            <!-- Pagination links akan diisi oleh JavaScript -->
                        </ul>
                    </nav>
                    <div class="text-muted" id="pagination-info-customer-price"></div>
                </div>
            </div>
        </div>
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
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th width="20%">Harga Normal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @foreach ($produks as $index => $produk)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $produk->kode_produk }}</td>
                            <td>{{ $produk->nama_produk }}</td>
                            <td class="text-right">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs btn-flat" 
                                        onclick="pilihProduk('{{ $produk->id_produk }}', '{{ $produk->nama_produk }}', {{ $produk->harga_jual }})">
                                    <i class="fa fa-check-circle"></i> Pilih
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
