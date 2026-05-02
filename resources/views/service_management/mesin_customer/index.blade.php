@extends('app')

@section('title')
    Mesin Customer
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Mesin Customer</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('service.mesin-customer.store') }}')" class="btn btn-success btn-xs btn-flat">
                    <i class="fa fa-plus-circle"></i> Tambah
                </button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered" id="table-mesin-customer">
                    <thead>
                        <th width="5%">No</th>
                        <th>Customer</th>
                        <th>Daerah Ongkir</th>
                        <th>Produk</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('service_management.mesin_customer.form')
@endsection

@push('scripts')
<script>
    let table, tableProduk, tableMember;
    let selectedProdukIds = [];
    let selectedProdukNames = {};
    let selectedProdukBiaya = {};
    let selectedProdukClosingTypes = {};
    let selectedProdukJumlah = {};

    $(function () {
        table = $('#table-mesin-customer').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('service.mesin-customer.index') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'member.nama'},
                {data: 'ongkos_kirim.daerah'},
                {data: 'produk'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        // Inisialisasi tabel produk untuk modal
        tableProduk = $('.table-produk').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('produk.data') }}', // Pastikan route ini ada
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual', render: function(data) {
                    return 'Rp ' + data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }},
                {data: 'aksi', searchable: false, sortable: false, 
                 render: function(data, type, row) {
                    return '<button type="button" class="btn btn-primary btn-xs btn-flat" onclick="pilihProduk(\'' + row.id_produk + '\', \'' + row.nama_produk + '\')"><i class="fa fa-check-circle"></i> Pilih</button>';
                 }},
            ]
        });

        // Inisialisasi tabel member untuk modal
        tableMember = $('.table-member').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('member.data') }}', // Pastikan route ini ada
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama'},
                {data: 'telepon'},
                {data: 'alamat'},
                {data: 'aksi', searchable: false, sortable: false,
                 render: function(data, type, row) {
                    return '<button type="button" class="btn btn-primary btn-xs btn-flat" onclick="pilihMember(\'' + row.id_member + '\', \'' + row.nama + '\')"><i class="fa fa-check-circle"></i> Pilih</button>';
                 }},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                // Validasi sebelum submit
                if (selectedProdukIds.length === 0) {
                    alert('Pilih minimal satu produk');
                    return false;
                }

                // Dapatkan CSRF token dari meta tag
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                // Buat FormData manual
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', $('#modal-form [name=_method]').val() || 'POST');
                formData.append('id_member', $('#id_member').val());
                formData.append('id_ongkir', $('#id_ongkir').val());

                // Tambahkan array data untuk produk, biaya service, dan closing type
                selectedProdukIds.forEach((id) => {
                    formData.append('produk[]', id);
                    const biayaService = $('#biaya_service_' + id).val() || 0;
                    const closingType = $('#closing_type_' + id).val() || 'jual_putus';
                    const jumlah = $('#jumlah_' + id).val() || 1;
                    formData.append('biaya_service_produk[]', biayaService);
                    formData.append('closing_type_produk[]', closingType);
                    formData.append('jumlah_produk[]', jumlah);
                });

                // Debug: Log data yang akan dikirim
                console.log('Data yang dikirim:');
                console.log('Method:', $('#modal-form [name=_method]').val() || 'POST');
                console.log('CSRF Token:', csrfToken);
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                $.ajax({
                    url: $('#modal-form form').attr('action'),
                    type: 'POST', // Selalu gunakan POST, _method akan handle PUT/PATCH
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                        resetForm();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Validation Error:\n';
                            for (const field in errors) {
                                errorMessage += field + ': ' + errors[field][0] + '\n';
                            }
                            alert(errorMessage);
                            
                            // Debug: Tampilkan data yang menyebabkan error
                            console.log('Error details:', errors);
                        } else if (xhr.status === 419) {
                            // CSRF token mismatch
                            alert('Session expired. Please refresh the page and try again.');
                            location.reload();
                        } else {
                            alert('Tidak dapat menyimpan data: ' + xhr.statusText);
                            console.log('Error response:', xhr.responseJSON);
                        }
                    }
                });
            }
        });

        // Pencarian real-time produk
        $('#nama_produk').on('input', function() {
            const keyword = $(this).val().toLowerCase();
            const hasilPencarian = $('#hasil-pencarian-produk');
            hasilPencarian.empty();

            if (keyword.length >= 2) {
                $.get('{{ route('produk.cari') }}', { keyword: keyword })
                    .done(function(response) {
                        if (response.length > 0) {
                            response.forEach(produk => {
                                hasilPencarian.append(`
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="pilihProduk('${produk.id_produk}', '${produk.nama_produk}')">
                                        ${produk.nama_produk} - ${produk.kode_produk}
                                    </a>
                                `);
                            });
                            hasilPencarian.show();
                        } else {
                            hasilPencarian.hide();
                        }
                    })
                    .fail(function() {
                        hasilPencarian.hide();
                    });
            } else {
                hasilPencarian.hide();
            }
        });

        // Pencarian real-time member
        $('#nama_member').on('input', function() {
            const keyword = $(this).val().toLowerCase();
            const hasilPencarian = $('#hasil-pencarian-member');
            hasilPencarian.empty();

            if (keyword.length >= 2) {
                $.get('{{ route('member.cari') }}', { keyword: keyword })
                    .done(function(response) {
                        if (response.length > 0) {
                            response.forEach(member => {
                                hasilPencarian.append(`
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="pilihMember('${member.id_member}', '${member.nama}')">
                                        ${member.nama} - ${member.telepon}
                                    </a>
                                `);
                            });
                            hasilPencarian.show();
                        } else {
                            hasilPencarian.hide();
                        }
                    })
                    .fail(function() {
                        hasilPencarian.hide();
                    });
            } else {
                hasilPencarian.hide();
            }
        });
    });

    function resetForm() {
        selectedProdukIds = [];
        selectedProdukNames = {};
        selectedProdukBiaya = {};
        selectedProdukClosingTypes = {};
        $('#table-produk-terpilih tbody').empty();
        $('#id_member').val('');
        $('#nama_member').val('');
        $('#id_ongkir').val('');
        
        // Reset semua input fields
        $('#modal-form form')[0].reset();
    }

    function addForm(url) {
        resetForm();
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Mesin Customer');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
    }

    function editForm(url) {
        resetForm();
        
        // Extract ID from URL untuk membuat route edit
        var id = url.split('/').pop();
        var editUrl = '{{ route("service.mesin-customer.edit", ":id") }}'.replace(':id', id);
        
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Mesin Customer');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');

        // Dapatkan CSRF token untuk request GET
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: editUrl,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                $('#id_member').val(response.id_member);
                $('#nama_member').val(response.member.nama);
                $('#id_ongkir').val(response.id_ongkir);
                
                // Isi produk terpilih dengan biaya service dan closing type
                response.produk.forEach(produk => {
                    const biayaService = produk.pivot.biaya_service || 0;
                    const closingType = produk.pivot.closing_type || 'jual_putus';
                    const jumlah = produk.pivot.jumlah || 1;
                    tambahProdukKeTabel(produk.id_produk, produk.nama_produk, biayaService, closingType, jumlah);
                });
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    alert('Session expired. Please refresh the page.');
                    location.reload();
                } else {
                    alert('Tidak dapat menampilkan data');
                }
            }
        });
    }

    function tampilProduk() {
        $('#modal-produk').modal('show');
        tableProduk.ajax.reload();
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function tampilMember() {
        $('#modal-member').modal('show');
        tableMember.ajax.reload();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }

    function pilihProduk(id, nama) {
        tambahProdukKeTabel(id, nama);
        hideProduk();
        $('#nama_produk').val('');
        $('#hasil-pencarian-produk').hide();
    }

    function pilihMember(id, nama) {
        $('#id_member').val(id);
        $('#nama_member').val(nama);
        hideMember();
        $('#hasil-pencarian-member').hide();
    }

    function tambahProdukKeTabel(id, nama, biayaService = 0, closingType = 'jual_putus', jumlah = 1) {
        if (!selectedProdukIds.includes(id.toString())) {
            selectedProdukIds.push(id.toString());
            selectedProdukNames[id] = nama;
            selectedProdukBiaya[id] = biayaService;
            selectedProdukClosingTypes[id] = closingType;
            selectedProdukJumlah[id] = jumlah;
            
            $('#table-produk-terpilih tbody').append(`
                <tr id="produk-${id}">
                    <td>${nama}</td>
                    <td>
                        <input type="number" class="form-control jumlah-input" 
                            id="jumlah_${id}" 
                            value="${jumlah}" 
                            min="1" 
                            onchange="updateJumlah('${id}', this.value)"
                            placeholder="Jumlah">
                    </td>
                    <td>
                        <input type="number" class="form-control biaya-service-input" 
                            id="biaya_service_${id}" 
                            value="${biayaService}" 
                            min="0" 
                            onchange="updateBiayaService('${id}', this.value)"
                            placeholder="Biaya Service">
                    </td>
                    <td>
                        <select class="form-control closing-type-input" 
                                id="closing_type_${id}" 
                                onchange="updateClosingType('${id}', this.value)">
                            <option value="jual_putus" ${closingType === 'jual_putus' ? 'selected' : ''}>Jual Putus</option>
                            <option value="deposit" ${closingType === 'deposit' ? 'selected' : ''}>Deposit</option>
                        </select>
                    </td>
                    <td>
                        <span class="label label-info" id="subtotal_${id}">
                            Rp ${formatNumber(biayaService * jumlah)}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs" onclick="hapusProduk('${id}')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            `);
        }
    }

    function formatNumber(number) {
        const num = parseInt(number) || 0;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateJumlah(id, jumlah) {
        selectedProdukJumlah[id] = parseInt(jumlah) || 1;
        updateSubtotal(id);
    }

    function updateSubtotal(id) {
        const biayaService = parseInt($('#biaya_service_' + id).val()) || 0;
        const jumlah = parseInt($('#jumlah_' + id).val()) || 1;
        const subtotal = biayaService * jumlah;
        
        $('#subtotal_' + id).text('Rp ' + formatNumber(subtotal));
    }

    function updateClosingType(id, value) {
        selectedProdukClosingTypes[id] = value;
    }

    function updateBiayaService(id, biayaService) {
        selectedProdukBiaya[id] = parseInt(biayaService) || 0;
        updateSubtotal(id);
    }

    function hapusProduk(id) {
        const index = selectedProdukIds.indexOf(id.toString());
        if (index > -1) {
            selectedProdukIds.splice(index, 1);
            delete selectedProdukNames[id];
            delete selectedProdukBiaya[id];
            $(`#produk-${id}`).remove();
            
            $('#produk_ids').val(JSON.stringify(selectedProdukIds));
        }
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
</script>
@endpush
