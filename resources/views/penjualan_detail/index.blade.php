@extends('app')

@section('title')
    Transaksi Penjualan
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-penjualan tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }

    .table-produk input.jumlah {
        width: 70px; /* Sesuaikan lebar input */
        text-align: center;
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjaualn</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                    
                <form class="form-produk">
                    @csrf
                    @if($outlets->count() > 1)
                    <div class="form-group row">
                                <label for="id_outlet" class="col-lg-2 control-label">Pilih Outlet</label>
                                <div class="col-lg-5">
                                    <select name="id_outlet" id="id_outlet" class="form-control">
                                        <option value="">Pilih Outlet Terlebih Dahulu!</option>
                                        @foreach ($outlets as $outlet)
                                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                    @endif
                    <div class="form-group row">
                        <label for="nama_member" class="col-lg-2 control-label">Customer</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nama_member" placeholder="Cari Customer..." value="{{ !empty($memberSelected->nama) ? $memberSelected->nama : '' }}">
                                <span class="input-group-btn">
                                    <button onclick="tampilMember()" class="btn btn-info btn-flat" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <!-- Hasil pencarian Customer -->
                            <div id="hasil-pencarian-member" class="list-group" style="display: none;">
                                <!-- Hasil pencarian akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_produk" class="col-lg-2">Nama Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="hidden" name="hpp" id="hpp">
                                <input type="hidden" name="stok" id="stok">
                                <input type="hidden" name="id_hpp" id="id_hpp">
                                <input type="hidden" name="jumlah" id="jumlah" value="0">
                                <input type="text" class="form-control" name="nama_produk" id="nama_produk" placeholder="Cari Produk...">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <!-- Hasil pencarian Produk -->
                            <div id="hasil-pencarian-produk" class="list-group" style="display: none;">
                                <!-- Hasil pencarian akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                            @csrf
                            <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="piutangHidden" id="piutangHidden">
                            <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="piutang" class="col-lg-2 control-label">Piutang</label>
                                <div class="col-lg-8">
                                    <input type="text" id="piutang" class="form-control" value="{{ $memberSelected->piutang ?? 0 }}" readonly>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="bayarDenganPiutang"> Bayar dengan Piutang
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="ingatkanPiutang"> Ingatkan Piutang Customer
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Diskon</label>
                                <div class="col-lg-8">
                                    <input type="number" name="diskon" id="diskon" class="form-control" 
                                        value="{{ ! empty($memberSelected->id_member) ? $diskon : 0 }}" 
                                        >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bayarrp" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" id="bayarrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Diterima</label>
                                <div class="col-lg-6">
                                    <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}">
                                </div>
                                <div class="col-lg-2">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="lunas"> Lunas
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" class="col-lg-2 control-label">Kembali</label>
                                <div class="col-lg-8">
                                    <input type="text" id="kembali" name="kembali" class="form-control" value="0" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan_detail.detail')
@includeIf('penjualan_detail.produk')
@includeIf('penjualan_detail.member')
@includeIf('penjualan_detail.edit_jumlah')
@includeIf('penjualan_detail.due_date_modal')
@endsection

@push('scripts')
<script>
    let table, table2, table3;
    let tipeMember = null;
    let selectedProductIds = [];

    $(function () {
        // $('body').addClass('sidebar-collapse');

        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaksi.data', $id_penjualan) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'diskon'},
                {data: 'subtotal'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#diskon').val());
            setTimeout(() => {
                $('#diterima').trigger('input');
            }, 300);
        });
        table2 = $('.table-produk').DataTable();

        table3 = $('.table-detail').DataTable({
                    processing: true,
                    bSort: false,
                    dom: 'Brt',
                    destroy: true,
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'tanggal'},
                        {data: 'harga_jual'},
                        {data: 'hpp'},
                        {data: 'stok'},
                        {data: 'aksi', searchable: false, sortable: false},
                    ]
                });

        const allMembers = @json($member); 
        const allProduk = @json($produk); 

        let members = allMembers;
        let produk = allProduk; 

        function filterDataByOutlet(id_outlet) {
            if (id_outlet) {
                // Filter data berdasarkan outlet
                members = allMembers.filter(member => member.id_outlet == id_outlet);
                produk = allProduk.filter(produk => produk.id_outlet == id_outlet);
            } else {
                // Jika tidak ada outlet yang dipilih, gunakan semua data
                members = allMembers;
                produk = allProduk;
            }
        }
        
        $('#id_outlet').on('change', function () {
            var id_outlet = $(this).val();
            filterDataByOutlet(id_outlet); 
            
            $.ajax({
                url: '{{ route('transaksi.index') }}',
                type: 'GET',
                data: {
                    id_outlet: id_outlet
                },
                success: function(response) {
                    // Reload modal member dengan data yang baru
                    $('#modal-member').html($(response).find('#modal-member').html());
                    $('#modal-produk').html($(response).find('#modal-produk').html());
                },
                error: function(xhr, status, error) {
                    console.log("Gagal memuat data member:", error);
                }
            });
        });

        const initialOutlet = $('#id_outlet').val();
        filterDataByOutlet(initialOutlet);

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            if (jumlah < 1) {
                $(this).val(1);
                alert('Jumlah tidak boleh kurang dari 1');
                return;
            }
            if (jumlah > 10000) {
                $(this).val(10000);
                alert('Jumlah tidak boleh lebih dari 10000');
                return;
            }

            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });

        $(document).on('input', '#diskon', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('#diterima').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($('#diskon').val(), $(this).val());
        });

        $('#lunas').on('change', function () {
            if ($(this).is(':checked')) {
                const bayarrp = $('#bayarrp').val().replace(/[^\d]/g, '') || 0;
                $('#diterima').val(bayarrp).trigger('input');
            } else {
                // Jika checkbox tidak dicentang, reset nilai diterima
                $('#diterima').val(0).trigger('input');
            }
        });

        $('.btn-simpan').on('click', function () {
            var isChecked = $('#bayarDenganPiutang').is(':checked') ? 'true' : 'false';
            var isCheckedIngatkan = $('#ingatkanPiutang').is(':checked') ? 'true' : 'false';
            var piutang = $('#piutangHidden').val();
            var isLunas = $('#lunas').is(':checked');

            if($('#id_member').val() == "") {
                return $('.form-penjualan').submit();
            }

            if (!isLunas) {
                $('#modal-due-date').modal('show');
                
                // Handle due date submission
                $('#btn-save-due-date').off('click').on('click', function() {
                    const tanggalTempo = $('#tanggal_tempo').val();
                    if (!tanggalTempo) {
                        alert('Silakan pilih tanggal jatuh tempo');
                        return;
                    }

                    // Proceed with saving after selecting due date
                    saveTransaction(isChecked, isCheckedIngatkan, piutang, tanggalTempo);
                    $('#modal-due-date').modal('hide');
                });
            } else {
                // For cash transactions, proceed directly
                saveTransaction(isChecked, isCheckedIngatkan, piutang);
            }
            
        });

        $('#bayarDenganPiutang').change(function() {
            var bayar = parseFloat($('#bayar').val()) || 0;
            var piutang = parseFloat($('#piutang').val()) || 0;
            var diterima = parseFloat($('#diterima').val()) || 0;
            var isChecked = "true";
            if ($(this).is(':checked')) {
                // Tambahkan piutang ke bayar
                //$('#bayarrp').val(bayar + piutang);
                //session('isChecked') === 'true';
                isChecked = "true";
            } else {
                //session('isChecked') === 'false';
                isChecked = "false";
                // Kembalikan bayar ke nilai semula (tanpa piutang)
                //$('#bayarrp').val(bayar);
            }
            
            loadForm($('#diskon').val(), diterima, isChecked);
        });

        

        $('#nama_member').on('input', function() {
            const keyword = $(this).val().toLowerCase(); // Ambil nilai dari input
            const hasilPencarian = $('#hasil-pencarian-member');
            hasilPencarian.empty(); // Kosongkan hasil sebelumnya

            if (keyword.length >= 2) { // Mulai pencarian setelah 2 karakter
                const filteredMembers = members.filter(member => 
                    member.nama.toLowerCase().includes(keyword) || 
                    member.telepon.toLowerCase().includes(keyword)
                );

                if (filteredMembers.length > 0) {
                    filteredMembers.forEach(member => {
                        hasilPencarian.append(`
                            <a href="#" class="list-group-item list-group-item-action"
                                onclick="pilihMember('${member.id_member}', '${member.nama}', '${member.piutang}', '${member.id_tipe}')">
                                ${member.nama} - ${member.telepon}
                            </a>
                        `);
                    });
                    hasilPencarian.show(); // Tampilkan hasil pencarian
                } else {
                    hasilPencarian.hide(); // Sembunyikan jika tidak ada hasil
                }
            } else {
                hasilPencarian.hide(); // Sembunyikan jika input kurang dari 2 karakter
            }
        });

        $('#nama_produk').on('input', function() {
            const keyword = $(this).val().toLowerCase();
            const hasilPencarian = $('#hasil-pencarian-produk');
            hasilPencarian.empty();

            if (keyword.length >= 2) {
                const filteredProduk = produk.filter(produk => 
                    produk.nama_produk.toLowerCase().includes(keyword)
                );

                if (filteredProduk.length > 0) {
                    filteredProduk.forEach(produk => {
                        hasilPencarian.append(`
                            <a href="#" class="list-group-item list-group-item-action"
                                onclick="pilihProduk('${produk.id_produk}', '${produk.nama_produk}', '${produk.hpp_produk_sum_stok}')">
                                ${produk.nama_produk} - Stok: ${produk.hpp_produk_sum_stok}
                            </a>
                        `);
                    });
                    hasilPencarian.show();
                } else {
                    hasilPencarian.hide();
                }
            } else {
                hasilPencarian.hide();
            }
        });
        

    });

    function tampilProduk() {
        $('#modal-produk').modal('show');

    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihHarga(url, id) {
                $('#id_produk').val(id);
                hideProduk();
                $('#modal-detail').modal('show');
                table3.ajax.url(url);
                table3.ajax.reload();
            }

            // function pilihBahan(id, nama, harga_beli, stok, id_harga_bahan) {
            //     $('#id_bahan').val(id);
            //     $('#id_harga_bahan').val(id_harga_bahan);
            //     $('#nama_bahan').val(nama);
            //     $('#harga_beli').val(harga_beli);
            //     $('#stok').val(stok);
            //     $('#modal-detail').modal('hide');
            //     tambahBahan();
            // }

    function pilihProduk(id, kode, stok) {
        $('#hasil-pencarian-produk').hide();

        stok = parseInt(stok) || 0;
        let jumlah = parseInt($(`input[data-id="${id}"]`).val()) || 0;  

        $('#id_produk').val(id);
        //$('#id_hpp').val(0);
        $('#kode_produk').val(kode);
        $('#stok').val(stok);
        $('#jumlah').val(jumlah);

        console.log("Stok: ", stok);
        console.log("Jumlah: ", jumlah);
        // Validasi stok
        if (stok < jumlah) {
            console.log(stok, "<", jumlah);
            alert('Produk tidak dapat ditambahkan karena stok tidak mencukupi XX');
            return; // Menghentikan eksekusi jika stok 0
        }

        if (!selectedProductIds.includes(id)) {
            selectedProductIds.push(id);
        }

        console.log("Produk dipilih:", selectedProductIds);

        tambahProduk();
    }

    function tambahProduk() {
        const id_member = $('#id_member').val();
        const jumlah = $('#jumlah').val();

        $.post('{{ route('transaksi.store') }}', {
            ...$('.form-produk').serializeArray().reduce((obj, item) => {
                obj[item.name] = item.value;
                return obj;
            }, {}),
            id_member: id_member,
            jumlah: jumlah
        })
            .done(response => {
                $('#kode_produk').focus();
                table.ajax.reload(() => loadForm($('#diskon').val()));

                hideProduk();
                loadProduk();
            })
            .fail(errors => {
                console.log(errors);
                alert('Tidak dapat menyimpan data');
                return;
            });
    }

    function loadProduk() {
        $.ajax({
            url: '{{ route('transaksi.index') }}',
            type: 'GET',
            success: function(response) {
                $('#modal-produk').html($(response).find('#modal-produk').html());
            },
            error: function(xhr, status, error) {
                console.log("Gagal memuat data produk:", error);
            }
        });
    }

    function tampilMember() {
        $('#modal-member').modal('show');
    }

    function pilihMember(id, nama, piutang, tipe_id) {
        $('#hasil-pencarian-member').hide();

        $('#id_member').val(id);
        $('#nama_member').val(nama);
        $('#piutang').val(piutang);
        $('#piutangHidden').val(piutang);
        tipeMember = tipe_id;

        var id_member = $('#id_member').val();
        var produkIds = [];

        $.ajax({
            url: '{{ route('transaksi.index') }}', // Ganti dengan route yang sesuai
            type: 'GET', // Atau POST jika diperlukan
            data: {
                id_tipe: tipe_id
            },
            success: function(response) {
                $('#modal-produk').html($(response).find('#modal-produk').html());
                console.log("Data produk berhasil diperbarui.");
            },
            error: function(xhr, status, error) {
                console.log("Gagal mengirim data ke index:", error);
            }
        });
        
        $.ajax({
            url: '{{ route('hapus.produk') }}',
            type: 'POST',
            data: {
                _token: $('[name=csrf-token]').attr('content'), // CSRF token
                id_penjualan: $('#id_penjualan').val() // ID transaksi saat ini
            },
            success: function(response) {
                console.log("Semua produk terhapus dari database.");
                
                // Reset produk terpilih di frontend
                selectedProductIds = [];
                $('.table-penjualan tbody').empty(); // Hapus tampilan tabel

                console.log("Produk telah direset setelah memilih member.");
            },
            error: function(xhr, status, error) {
                console.log("Gagal menghapus produk:", error);
            }
        });

        

        loadForm($('#diskon').val());
        $('#diterima').val(0).focus().select();

        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }


    function deleteData(url, id) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    '_token': $('[name=csrf-token]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    table.ajax.reload(); // Reload tabel setelah menghapus
                    loadProduk();
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 404) {
                        alert('Data tidak ditemukan');
                    } else {
                        alert('Gagal menghapus data: ' + error);
                    }
                }
            });
        }
    }

    function loadForm(diskon = 0, diterima = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        var piutang = parseFloat($('#piutangHidden').val()) || 0; // Ambil nilai piutang
        var isChecked = $('#bayarDenganPiutang').is(':checked') ? 'true' : 'false';
        var isCheckedIngatkan = $('#ingatkanPiutang').is(':checked') ? 'true' : 'false';
        var total = parseFloat($('.total').text()) || 0;
        //var isChecked = $('#bayarDenganPiutang').is(':checked');

        $.get(`{{ url('/transaksi/loadform') }}/${diskon}/${total}/${diterima}/${piutang}/${isChecked}/${isCheckedIngatkan}`)
            .done(response => {
                $('#totalrp').val(response.totalrp);
                $('#bayar').val(response.bayar);
                $('#piutang').val(response.piutang);
                $('#bayarrp').val(response.bayarrp);
                $('.tampil-bayar').text('Bayar: ' + response.bayarrp);
                $('.tampil-terbilang').text(response.terbilang);

                $('#kembali').val(response.kembalirp);
                if ($('#diterima').val() != 0) {
                    $('.tampil-bayar').text('Kembali: ' + response.kembalirp);
                    $('.tampil-terbilang').text(response.kembali_terbilang);
                }
            })
            .fail(errors => {
                console.log(errors);
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    // Fungsi untuk menampilkan modal edit jumlah
    function editJumlah(id_penjualan_detail) {
        // Ambil data jumlah saat ini
        const jumlahSaatIni = $(`input[data-id="${id_penjualan_detail}"]`).val();

        // Isi form edit jumlah
        $('#edit_id_penjualan_detail').val(id_penjualan_detail);
        $('#edit_jumlah').attr('placeholder', jumlahSaatIni).val('');

        // Tampilkan modal
        $('#modal-edit-jumlah').modal('show');
    }

    // Fungsi untuk menyimpan perubahan jumlah
    function simpanEditJumlah() {
        const id_penjualan_detail = $('#edit_id_penjualan_detail').val();
        const jumlah = $('#edit_jumlah').val();

        if (jumlah < 1) {
            alert('Jumlah tidak boleh kurang dari 1');
            return;
        }

        $.ajax({
            url: '{{ route('transaksi.updateJumlah') }}', // Route untuk update jumlah
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_penjualan_detail: id_penjualan_detail,
                jumlah: jumlah
            },
            success: function(response) {
                if (response.success) {
                    // Perbarui jumlah di tabel
                    $(`input[data-id="${id_penjualan_detail}"]`).val(jumlah);

                    // Perbarui subtotal dan total
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                    loadProduk();

                    // Sembunyikan modal
                    $('#modal-edit-jumlah').modal('hide');
                } else {
                    alert('Gagal memperbarui jumlah');
                }
            },
            error: function(xhr, status, error) {
                console.log("Gagal menyimpan perubahan:", error);
                alert('Terjadi kesalahan saat menyimpan perubahan');
            }
        });
    }

    function saveTransaction(isChecked, isCheckedIngatkan, piutang, tanggalTempo = null) {
        // Prepare form data
        let formData = {
            '_token': $('[name=csrf-token]').attr('content'),
            'id_member': $('#id_member').val(),
            'bayar': $('#bayar').val(),
            'diterima': $('#diterima').val(),
            'isChecked': isChecked,
            'isCheckedIngatkan': isCheckedIngatkan,
            'piutang': piutang,
            'is_bon': $('#lunas').is(':checked') ? 0 : 1
        };

        // Add due date if provided
        if (tanggalTempo) {
            formData.tanggal_tempo = tanggalTempo;
        }

        // Update piutang and submit form
        $.post('{{ route('transaksi.updatePiutang') }}', formData)
            .done(response => {
                // Add hidden fields for form submission
                if (!formData.is_bon) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'is_bon',
                        value: 0
                    }).appendTo('.form-penjualan');
                } else {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'is_bon',
                        value: 1
                    }).appendTo('.form-penjualan');
                    
                    if (tanggalTempo) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'tanggal_tempo',
                            value: tanggalTempo
                        }).appendTo('.form-penjualan');
                    }
                }

                $('.form-penjualan').submit();
                alert(response);
            })
            .fail(errors => {
                console.log(errors);
                alert('Gagal memperbarui piutang');
            });
    }

</script>
@endpush
