@extends('app')

@section('title')
Tipe Customer
@endsection

@section('breadcrumb')
@parent
<li class="active">Tipe Custoer</li>
@endsection

@section('content')
<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                @if($outlets->count() > 1)
                <div class="form-group">
                    <label for="id_outlet">Pilih Outlet</label>
                    <select name="id_outlet" id="id_outlet" class="form-control">
                        <option value="">Semua Outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <button onclick="addForm('{{ route('tipe.store') }}')"
                    class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tipe</th>
                        <th>Outlet</th>
                        <th>Produk dan Diskon</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                    <!-- <tbody>
                        @foreach ($tipe as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_tipe }}</td>
                                <td>
                                    <ul>
                                        @foreach ($item->produkTipe as $produkTipe)
                                            <li>{{ $produkTipe->produk->nama_produk }} - {{ $produkTipe->diskon }}%</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="editForm('{{ route('tipe.update', $item->id_tipe) }}')" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                                        <button onclick="deleteData('{{ route('tipe.destroy', $item->id_tipe) }}')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody> -->
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

@includeif('tipe.form')
    @endsection

    <script>
        console.log('Route URL:', '{{ route('tipe.data') }}');
    </script>
    @push('scripts')
        <script>
            let table;
            let produkCount = 0;
            $(function () {
                table = $('.table').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: {
                        url: '{{ route('tipe.data') }}',
                        data: function (d) {
                            d.id_outlet = $('#id_outlet').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'nama_tipe'
                        },
                        { data: 'nama_outlet' },
                        {
                            data: 'produk_diskon',
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
                    console.log('Outlet ID terpilih: ', $(this).val());
                    table.ajax.reload();
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#modal-form form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        alert('Silakan periksa kembali input Anda. Pastikan semua field yang diperlukan telah diisi dengan benar.');
                    } else {
                        e.preventDefault(); // Mencegah pengiriman form default

                        // Ambil nilai dari input atau placeholder
                        const formData = $('#modal-form form').serializeArray();
                        const produkRows = document.querySelectorAll('#produk-container .form-group.row');

                        produkRows.forEach((row, index) => {
                            const diskonInput = row.querySelector('input[name="diskon[]"]');
                            const hargaJualInput = row.querySelector('input[name="harga_jual[]"]');

                            // Jika input kosong, ambil nilai dari placeholder
                            if (diskonInput.value === '') {
                                diskonInput.value = diskonInput.getAttribute('placeholder');
                            }
                            if (hargaJualInput.value === '') {
                                hargaJualInput.value = hargaJualInput.getAttribute('placeholder');
                            }
                        });

                        $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                            .done((response) => {
                                $('#modal-form').modal('hide'); // Menutup modal
                                table.ajax.reload(); // Reload tabel
                                alert('Data berhasil disimpan!');
                                console.log(response); // Tampilkan pesan sukses
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menyimpan data: ' + errors.responseText); // Tampilkan pesan kesalahan
                                return;
                            });
                    }
                });

                document.getElementById('add-produk').addEventListener('click', function() {
                    const userOutlets = @json($userOutlets ?? []);
                    let idOutlet;
    
                    // Jika user hanya memiliki 1 outlet, gunakan outlet pertama
                    if (userOutlets.length === 1) {
                        idOutlet = userOutlets[0];
                    } 
                    // Jika user memiliki lebih dari 1 outlet, ambil dari form
                    else if (userOutlets.length > 1) {
                        idOutlet = $('#modal-form [name=id_outlet]').val();
                        
                        // Validasi jika outlet belum dipilih
                        if (!idOutlet) {
                            alert('Pilih outlet terlebih dahulu!');
                            return;
                        }
                        
                        // Validasi jika outlet yang dipilih tidak ada di akses user
                        if (!userOutlets.includes(parseInt(idOutlet))) {
                            alert('Anda tidak memiliki akses ke outlet ini!');
                            return;
                        }
                    }
                    // Jika tidak ada outlet
                    else {
                        alert('User tidak memiliki akses ke outlet manapun!');
                        return;
                    }
                    
                    addProdukRow(idOutlet, '', '', '');
                    //addProdukRow();
                });
            });

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Tipe');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama_tipe]').focus();

                $('#modal-form [name=id_outlet]').prop('readonly', false);

                document.getElementById('produk-container').innerHTML = '';
                produkCount = 0;
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Tipe');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama_tipe]').focus();

                $('#modal-form [name=id_outlet]').prop('readonly', true);

                document.getElementById('produk-container').innerHTML = '';
                produkCount = 0;

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama_tipe]').val(response.nama_tipe);
                        $('#modal-form [name=id_outlet]').val(response.id_outlet);

                        // Clear existing produk rows
                        document.getElementById('produk-container').innerHTML = '';
                        produkCount = 0; // Reset produk count

                        if (Array.isArray(response.produk_tipe)) {
                            response.produk_tipe.forEach(item => {
                                const diskon = item.diskon === 0 ? '' : item.diskon;
                                const hargaJual = item.harga_jual === 0 ? '' : item.harga_jual;
                                addProdukRow(response.id_outlet, item.id_produk, diskon, hargaJual);
                            });
                        } else {
                            console.error('produkTipe is not an array or is undefined');
                        }
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        console.log(errors);
                        return;
                    });
            }

            function removeProduk(id) {
                const produkRow = document.querySelector(`div[data-id='${id}']`);
                if (produkRow) {
                    produkRow.remove(); // Hapus elemen produk
                }
            }

            function addProdukRow(id_outlet = null, produkId = '', diskon = '', hargaJual = '') {
                const container = document.getElementById('produk-container');
                const newRow = document.createElement('div');
                newRow.classList.add('form-group', 'row', 'align-items-center', 'mb-3');
                newRow.setAttribute('data-id', produkCount);

                let options = '';
                @foreach ($produk as $item)
                    if ('{{ $item->id_outlet }}' == id_outlet) {
                        options += `<option value="{{ $item->id_produk }}" ${produkId == '{{ $item->id_produk }}' ? 'selected' : ''}>
                                        {{ $item->nama_produk }}
                                    </option>`;
                    }
                @endforeach

                newRow.innerHTML = `
                    <div class="col-md-5">
                        <label for="produk" class="control-label">Produk</label>
                        <select name="produk[]" class="form-control" required>
                            <option value="">Pilih Produk</option>
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="diskon" class="control-label">Diskon (%)</label>
                        <input type="number" name="diskon[]" class="form-control" min="0" max="100" placeholder="${diskon === '' ? '0' : diskon}" value="${diskon}">
                    </div>
                    <div class="col-md-3">
                        <label for="harga_jual" class="control-label">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual[]" class="form-control" min="0" placeholder="${hargaJual === '' ? '0' : hargaJual}" value="${hargaJual}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-block remove-produk" onclick="removeProduk(${produkCount})">Hapus</button>
                    </div>
                `;
                container.appendChild(newRow);
                produkCount++;
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            console.log(response);
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            alert('Tidak dapat menghapus data');
                            return;
                        })
                }
            }
        </script>
    @endpush
