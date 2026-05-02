<style>
    /* Perlebar modal */
    .modal-xl {
        max-width: 1200px; /* Sesuaikan dengan kebutuhan */
    }

    /* Pastikan modal memenuhi lebar layar */
    @media (min-width: 1200px) {
        .modal-xl {
            width: 90%; /* Sesuaikan dengan kebutuhan */
        }
    }

    /* Perbaiki tata letak form */
    .modal-body .form-group {
        margin-bottom: 15px;
    }

    .modal-body .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .modal-body .form-group input,
    .modal-body .form-group select {
        width: 100%;
    }
</style>
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"> <!-- Ubah modal-lg menjadi modal-xl -->
        <form id="form-tipe" action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-form-label">Form Tipe</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="nama_tipe" class="col-lg-2 control-label">Nama Tipe</label>
                        <div class="col-lg-10">
                            <input type="text" name="nama_tipe" id="nama_tipe" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    @if($outlets->count() > 1)
                    <div class="form-group row">
                        <label for="id_outlet" class="col-lg-2 control-label">Outlet</label>
                        <div class="col-lg-10">
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
                    <div id="produk-container">
                        <!-- Produk, Diskon, dan Harga Jual akan ditambahkan di sini -->
                    </div>
                    <button type="button" id="add-produk" class="btn btn-primary">Tambah Produk</button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
