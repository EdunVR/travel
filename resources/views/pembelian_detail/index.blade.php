@extends('app')

@section('title')
Transaksi Pembelian
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

        .table-pembelian tbody tr:last-child {
            display: none;
        }

        <blade media|(max-width%3A%20768px)%20%7B>.tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
        }
    </style>
@endpush

@section('breadcrumb')
@parent
<li class="active">Transaksi Pembelian</li>
@endsection

@section('content')
<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <table>
                    <tr>
                        <td>Outlet</td>
                        <td>: {{ $supplier->outlet->nama_outlet }} </td>
                    </tr>
                    <tr>
                        <td>Supplier</td>
                        <td>: {{ $supplier->nama }}</td>
                    </tr>
                    <tr>
                        <td>Telepon</td>
                        <td>: {{ $supplier->telepon }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: {{ $supplier->alamat }}</td>
                    </tr>
                    <tr>
                        <td>Hutang</td>
                        <td>: <span class="label label-danger"> {{ format_uang($supplier->hutang) }} </span></td>
                    </tr> 
                </table>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <form class="form-bahan">
                    @csrf
                    <div class="form-group row">
                        <label for="nama_bahan" class="col-lg-2">Nama Bahan</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_pembelian" id="id_pembelian" value="{{ $id_pembelian }}">
                                <input type="hidden" name="id_bahan" id="id_bahan">
                                <input type="hidden" name="harga_beli" id="harga_beli">
                                <input type="hidden" name="stok" id="stok">
                                <input type="hidden" name="id_harga_bahan" id="id_harga_bahan">
                                <input type="hidden" name="jumlah" id="jumlah" value="0">
                                <input type="text" class="form-control" name="nama_bahan" id="nama_bahan" placeholder="Cari Bahan...">
                                <span class="input-group-btn">
                                    <button onclick="tampilBahan()" class="btn btn-info btn-flat" type="button"><i
                                            class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                            <!-- Hasil pencarian Produk -->
                            <div id="hasil-pencarian-bahan" class="list-group" style="display: none;">
                                <!-- Hasil pencarian akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-stiped table-bordered table-pembelian">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama Bahan</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('pembelian.store') }}" class="form-pembelian"
                            method="post">
                            @csrf
                            <input type="hidden" name="id_pembelian" value="{{ $id_pembelian }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="id_supplier" id="id_supplier" value="{{ $supplier->id_supplier }}">
                            <input type="hidden" name="hutangHidden" id="hutangHidden" value="{{ $supplier->hutang }}">
                            <input type="hidden" name="hutangBaru" id="hutangBaru">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Diskon</label>
                                <div class="col-lg-8">
                                    <input type="number" name="diskon" id="diskon" class="form-control"
                                       min="0" value="{{ $diskon }}" max="100">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bayarrp" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" id="bayarrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="hutang" class="col-lg-5 control-label">Berhutang ke supplier</label>
                                <div class="col-lg-5">
                                    <input type="checkbox" id="hutang" name="hutang">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bayarHutang" class="col-lg-5 control-label">Bayar dengan Hutang</label>
                                <div class="col-lg-5">
                                    <input type="checkbox" id="bayarHutang" name="bayarHutang">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i
                        class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>

    </div>
    <!-- /.box -->
</div>
<!-- /.col -->
</div>

@includeif('pembelian_detail.detail')
@includeif('pembelian_detail.bahan')
@includeif('pembelian_detail.edit_jumlah')
@includeif('pembelian_detail.form_harga')
    @endsection

    @push('scripts')
        <script>
            let baseUrl = "{{ url('/') }}"; // Base URL Laravel

            function getHargaBeliUrl(idBahan) {
                return "{{ route('getHargaBeli', ':id') }}".replace(':id', idBahan);
            }

            let table, table2, table3;
            $(function () {
                table = $('.table-pembelian').DataTable({
                        responsive: true,
                        processing: true,
                        serverSide: true,
                        autoWidth: false,
                        ajax: '{{ route('pembelian_detail.data', $id_pembelian) }}',
                        columns: [{
                                data: 'DT_RowIndex',
                                searchable: false,
                                sortable: false
                            },
                            {
                                data: 'nama_bahan'
                            },
                            {
                                data: 'harga_beli'
                            },
                            {
                                data: 'jumlah',
                            },
                            {
                                data: 'subtotal'
                            },
                            {
                                data: 'aksi',
                                name: 'aksi',
                                searchable: false,
                                sortable: false
                            },
                        ],
                        dom: 'Brt'

                    })
                    .on('draw.dt', function () {
                        loadForm($('#diskon').val());
                    });

                table2 = $('.table-bahan').DataTable();

                table3 = $('.table-detail').DataTable({
                    processing: true,
                    bSort: false,
                    dom: 'Brt',
                    destroy: true,
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'tanggal'},
                        {data: 'harga_beli'},
                        {data: 'stok'},
                        {data: 'aksi', searchable: false, sortable: false},
                    ]
                });

                const bahan = @json($bahan); 

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

                    $.post(`{{ url('/pembelian_detail') }}/${id}`, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'put',
                            'jumlah': jumlah
                        })
                        .done(response => {
                            $(this).on('mouseout', function () {
                                // table.ajax.reload(() => loadForm($('#diskon').val()));
                                table.ajax.reload();
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

                $('.btn-simpan').on('click', function () {
                    var isChecked = $('#hutang').is(':checked') ? 'true' : 'false';
                    var isBayarHutang = $('#bayarHutang').is(':checked') ? 'true' : 'false';
                    var hutang = $('#hutangBaru').val();
                    var hutangLama = $('#hutangHidden').val();
                    // Setelah transaksi disimpan, perbarui piutang
                    $.post('{{ route('pembelian_detail.updateHutang') }}', {
                        '_token': $('[name=csrf-token]').attr('content'),
                        'id_supplier': $('#id_supplier').val(),
                        'bayar': $('#bayar').val(), // Pastikan ini diisi dengan nilai yang benar
                        'isChecked': isChecked,
                        'hutang': hutang,
                        'isBayarHutang': isBayarHutang,
                        'hutangLama': hutangLama
                    })
                    .done(response => {
                        $('.form-pembelian').submit();
                        alert(response); // Tampilkan pesan sukses
                    })
                    .fail(errors => {
                        console.log(errors);
                        alert('Gagal memperbarui piutang');
                    });
                });

                $('#nama_bahan').on('input', function() {
                    const keyword = $(this).val().toLowerCase();
                    const hasilPencarian = $('#hasil-pencarian-bahan');
                    hasilPencarian.empty();

                    if (keyword.length >= 2) {
                        const filteredBahan = bahan.filter(bahan => 
                            bahan.nama_bahan.toLowerCase().includes(keyword)
                        );

                        console.log(filteredBahan);

                        if (filteredBahan.length > 0) {
                            filteredBahan.forEach(bahan => {
                                hasilPencarian.append(`
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="pilihHarga('${getHargaBeliUrl(bahan.id_bahan)}', '${bahan.id_bahan}')">
                                        ${bahan.nama_bahan} - Stok: ${bahan.stok}
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

            $('#hutang').change(function() {
                var bayar = parseFloat($('#bayar').val()) || 0;

                var isChecked = "true";
                if ($(this).is(':checked')) {
                    $('#hutangBaru').val(bayar);
                    isChecked = "true";
                } else {
                    isChecked = "false";
                    $('#hutangBaru').val(0);
                }

                loadForm($('#diskon').val());
            });

            $('#bayarHutang').change(function() {
                var bayar = parseFloat($('#bayar').val()) || 0;

                var isBayarHutang = "true";
                if ($(this).is(':checked')) {
                    isBayarHutang = "true";
                } else {
                    isBayarHutang = "false";
                }

                loadForm($('#diskon').val());
            });

            $('#modal-form-harga form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault();
                        $.post($('#modal-form-harga form').attr('action'), $('#modal-form-harga form').serialize())
                            .done((response) => {
                                $('#modal-form-harga').modal('hide');
                                $table3.ajax.reload(() => loadForm($('#diskon').val()));
                                $('#modal-detail').html($(response).find('#modal-detail').html());
                                loadBahan();
                                // table1.ajax.reload();
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })

            function tampilBahan() {
                $('#modal-bahan').modal('show');
            }

            function loadBahan() {
                $.ajax({
                    url: '{{ route('pembelian_detail.index') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#modal-bahan').html($(response).find('#modal-bahan').html());
                    },
                    error: function(xhr, status, error) {
                        console.log("Gagal memuat data produk:", error);
                    }
                });
            }

            function hideBahan() {
                $('#modal-bahan').modal('hide');
            }

            function pilihHarga(url, id) {
                $('#id_bahan').val(id);
                const jumlah = $(`input[data-id="${id}"]`).val() || 0;
                $('#jumlah').val(jumlah);
                hideBahan();
                $('#modal-detail').modal('show');
                table3.ajax.url(url);
                table3.ajax.reload();
            }

            function pilihBahan(id, nama, harga_beli, stok, id_harga_bahan) {
                $('#id_bahan').val(id);
                $('#id_harga_bahan').val(id_harga_bahan);
                $('#nama_bahan').val(nama);
                $('#harga_beli').val(harga_beli);
                $('#stok').val(stok);
                $('#modal-detail').modal('hide');
                tambahBahan();
            }

            function tambahBahan() {
                const jumlah = $('#jumlah').val();
                console.log(jumlah);
                $.post('{{ route('pembelian_detail.store') }}', {
                    ...$('.form-bahan').serializeArray().reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {}),
                    jumlah: jumlah
                })
                .done(response => {
                    $('#nama_bahan').focus();
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                    $('#modal-jumlah').modal('hide');
                })
                .fail(errors => {
                    console.log(errors);
                    alert('Tidak dapat menyimpan data');
                    return;
                });
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data Terpilih?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            //console.log(response);
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            console.log(errors);
                            alert('Tidak dapat menghapus data');
                            return;
                        })
                }
            }

            function loadForm(diskon = 0) {
                $('#total').val($('.total').text());
                $('#total_item').val($('.total_item').text());

                var hutang = parseFloat($('#hutangHidden').val()) || 0;
                var isChecked = $('#hutang').is(':checked') ? 'true' : 'false';
                var isBayarHutang = $('#bayarHutang').is(':checked') ? 'true' : 'false';

                $.get(
                        `{{ url('/pembelian_detail/loadform') }}/${diskon}/${$('.total').text()}/${isChecked}/${isBayarHutang}/${hutang}`
                    )
                    .done(response => {
                        $('#totalrp').val(response.totalrp);
                        $('#bayarrp').val(response.bayarrp);
                        $('#bayar').val(response.bayar);
                        $('.tampil-bayar').text(response.bayarrp);
                        $('.tampil-terbilang').text(response.terbilang);
                    })
                    .fail(errors => {
                        alert('Tidak dapat menampilkan data');
                        return;
                    })
            }

            function tambahHargaBahan() {
                let harga_beli = $('#harga_beli_baru').val();
                let id_bahan = $('#id_bahan').val(); // Ambil ID bahan dari input

                if (!harga_beli || harga_beli <= 0) {
                    alert('Masukkan harga beli yang valid!');
                    return;
                }

                $.post('{{ route('simpanHargaBahan') }}', {
                    _token: '{{ csrf_token() }}',
                    id_bahan: id_bahan,
                    harga_beli: harga_beli,
                    stok: 0
                })
                .done(response => {
                    alert('Harga berhasil ditambahkan!');
                    $('#harga_beli_baru').val(''); // Kosongkan input
                    table3.ajax.reload(); // Reload tabel harga bahan
                })
                .fail(errors => {
                    console.log(errors);
                    alert('Gagal menambahkan harga!');
                });
            }

            // Fungsi untuk menampilkan modal edit jumlah
            function editJumlah(id_pembelian_detail) {
                // Ambil data jumlah saat ini
                const jumlahSaatIni = $(`input[data-id="${id_pembelian_detail}"]`).val();

                // Isi form edit jumlah
                $('#edit_id_pembelian_detail').val(id_pembelian_detail);
                $('#edit_jumlah').attr('placeholder', jumlahSaatIni).val('');

                // Tampilkan modal
                $('#modal-edit-jumlah').modal('show');
            }

            // Fungsi untuk menyimpan perubahan jumlah
            function simpanEditJumlah() {
                const id_pembelian_detail = $('#edit_id_pembelian_detail').val();
                const jumlah = $('#edit_jumlah').val();

                if (jumlah < 1) {
                    alert('Jumlah tidak boleh kurang dari 1');
                    return;
                }

                $.ajax({
                    url: '{{ route('pembelian_detail.updateJumlah') }}', // Route untuk update jumlah
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_pembelian_detail: id_pembelian_detail,
                        jumlah: jumlah
                    },
                    success: function(response) {
                        if (response.success) {
                            // Perbarui jumlah di tabel
                            $(`input[data-id="${id_pembelian_detail}"]`).val(jumlah);

                            // Perbarui subtotal dan total
                            table.ajax.reload(() => loadForm($('#diskon').val()));
                            //$('#modal-bahan').html($(response).find('#modal-bahan').html());
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

            function editForm_harga(url) {
                $('#modal-form-harga').modal('show');
                $('#modal-form-harga .modal-title').text('Edit Harga dan Stok');
                
                // Reset nilai input dalam modal
                $('#modal-form-harga input').val('');
                $('#modal-form-harga [name=_method]').val('put');  // Set method PUT
                $('#modal-form-harga [name=harga_beli]').focus();  // Fokuskan elemen harga_bahan
                
                // Ambil data dari URL dan masukkan ke input modal
                $.get(url)
                    .done((response) => {
                        // Isi input modal dengan data yang diterima dari server
                        $('#modal-form-harga [name=harga_beli]').val(response.harga_beli);
                        $('#modal-form-harga [name=stok]').val(response.stok);
                        
                        // Handle pengiriman data menggunakan AJAX, tanpa form
                        $('#modal-form-harga .btn-primary').on('click', function(e) {
                            e.preventDefault();  // Prevent default form submission
                            
                            var dataX = {
                                _method: 'PUT',  // Menyatakan request adalah PUT
                                harga_beli: $('#modal-form-harga [name=harga_beli]').val(),
                                stok: $('#modal-form-harga [name=stok]').val()
                            };
                            
                            $.ajax({
                                url: "{{ url('pembelian_detail/bahan_harga') }}/" + response.id,
                                method: 'PUT',
                                data: dataX,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // Menambahkan CSRF token ke header
                                },
                                success: function(response) {
                                    alert('Data berhasil disimpan');
                                    $('#modal-form-harga').modal('hide');
                                    table3.ajax.reload(() => loadForm($('#diskon').val()));
                                    $('#modal-detail').html($(response).find('#modal-detail').html());
                                    loadBahan();
                                },
                                error: function(errors) {
                                    console.log(errors);
                                    alert('Tidak dapat menyimpan data');
                                }
                            });
                        });
                    })
                    .fail((errors) => {
                        console.log(errors);
                        alert('Tidak dapat menampilkan data');
                    });
            }

            function deleteData_harga(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            console.log(response);
                            table3.ajax.reload(() => loadForm($('#diskon').val()));
                            $('#modal-detail').html($(response).find('#modal-detail').html());
                            loadBahan();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                            return;
                        })
                }
            }
        </script>
    @endpush
