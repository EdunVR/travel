@extends('app')

@section('title')
    Harga Khusus Customer
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Harga Khusus Customer</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('sales.customer-price.store') }}')" class="btn btn-success btn-xs btn-flat">
                    <i class="fa fa-plus-circle"></i> Tambah
                </button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered" id="table-customer-price">
                    <thead>
                        <th width="5%">No</th>
                        <th>Customer</th>
                        <th>Tipe</th>
                        <th>Daerah Ongkir</th>
                        <th>Produk & Harga Khusus</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('sales_management.customer_price.form')
@endsection

@push('scripts')
<script>
    let table, tableProduk;
    let selectedProdukIds = [];
    let selectedProdukNames = {};
    let selectedProdukHargaNormal = {};
    let selectedProdukHarga = {};
    
    // VARIABLES PAGINATION UNTUK CUSTOMER PRICE
    let currentPageCustomerPrice = 1;
    let totalPagesCustomerPrice = 1;
    let currentSearchCustomerPrice = '';

    $(function () {
        table = $('#table-customer-price').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('sales.customer-price.index') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'customer_name'},
                {data: 'customer_type'},
                {data: 'ongkos_kirim.daerah'},
                {data: 'produk', searchable: false},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
    if (! e.preventDefault()) {
        if (selectedProdukIds.length === 0) {
            alert('Pilih minimal satu produk');
            return false;
        }

        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', $('#modal-form [name=_method]').val() || 'POST');
        formData.append('customer_type', $('#customer_type').val());
        formData.append('customer_id', $('#customer_id').val());
        formData.append('id_ongkir', $('#id_ongkir').val());

        selectedProdukIds.forEach((id) => {
            formData.append('produk[]', id);
            // Parse formatted number sebelum dikirim
            const hargaKhususValue = $('#harga_khusus_' + id).val();
            const hargaKhususNumeric = parseNumber(hargaKhususValue);
            formData.append('harga_khusus_produk[]', hargaKhususNumeric);
        });

        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }

        $.ajax({
            url: $('#modal-form form').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modal-form').modal('hide');
                table.ajax.reload();
                resetForm();
                alert('Data berhasil disimpan');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Error Validasi:\n';
                    for (const field in errors) {
                        errorMessage += `• ${field}: ${errors[field].join(', ')}\n`;
                    }
                    alert(errorMessage);
                } else {
                    alert('Tidak dapat menyimpan data: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            }
        });
    }
});
    });

    function resetForm() {
        selectedProdukIds = [];
        selectedProdukNames = {};
        selectedProdukHargaNormal = {};
        selectedProdukHarga = {};
        $('#table-produk-terpilih tbody').empty();
        $('#customer_type').val('member');
        $('#customer_id').val('');
        $('#customer_display').val('');
        $('#id_ongkir').val('');
    }

    function addForm(url) {
        resetForm();
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Harga Khusus Customer');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
    }

    function editForm(url) {
    resetForm();
    $('#modal-form').modal('show');
    $('#modal-form .modal-title').text('Edit Harga Khusus Customer');
    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('put');

    // Extract ID dari URL untuk GET request
    const id = url.split('/').pop();
    const editUrl = '{{ route("sales.customer-price.edit", ":id") }}'.replace(':id', id);

    $.get(editUrl)
        .done((response) => {
            if (response.success) {
                $('#customer_type').val(response.customer_type);
                $('#customer_id').val(response.customer_id);
                $('#customer_display').val(response.customer.nama);
                $('#id_ongkir').val(response.id_ongkir);
                
                // Clear existing products
                selectedProdukIds = [];
                selectedProdukNames = {};
                selectedProdukHargaNormal = {};
                selectedProdukHarga = {};
                $('#table-produk-terpilih tbody').empty();
                
                // Add products from response
                if (response.produk && response.produk.length > 0) {
                    response.produk.forEach(produk => {
                        const hargaKhusus = produk.pivot ? produk.pivot.harga_khusus : 0;
                        tambahProdukKeTabel(
                            produk.id_produk, 
                            produk.nama_produk, 
                            produk.harga_jual, 
                            hargaKhusus
                        );
                    });
                }
            } else {
                alert('Gagal memuat data: ' + (response.message || 'Unknown error'));
            }
        })
        .fail((xhr, status, error) => {
            console.error('Edit error:', xhr);
            alert('Tidak dapat menampilkan data. Error: ' + (xhr.responseJSON?.message || error));
        });
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

    // ========== FUNGSI CUSTOMER DENGAN PAGINATION ==========
    
    // Function untuk load customers dengan pagination (CUSTOMER PRICE)
    function loadCustomersCustomerPrice(search = '', page = 1) {
        console.log('Loading customers for customer price:', search, 'page:', page);
        
        currentSearchCustomerPrice = search;
        currentPageCustomerPrice = page;
        
        $.get('{{ route("sales.customer-price.get-customers") }}', { 
            search: search,
            page: page 
        }, function(response) {
            console.log('Customers response (customer price):', response);
            
            if (response.success) {
                const tbody = $('#customer-list-customer-price');
                tbody.empty();
                
                if (response.customers.length > 0) {
                    response.customers.forEach(customer => {
                        const namaEscaped = customer.nama ? customer.nama.replace(/'/g, "\\'") : 'N/A';
                        const telepon = customer.telepon || '-';
                        const alamat = customer.alamat || '-';
                        
                        tbody.append(`
                            <tr>
                                <td>${customer.nama || 'N/A'}</td>
                                <td>${telepon}</td>
                                <td>${alamat}</td>
                                <td>${customer.type === 'member' ? 'Member' : 'Prospek'}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-xs" 
                                            onclick="pilihCustomerCustomerPrice('${customer.type}', '${customer.id}', '${namaEscaped}')">
                                        <i class="fa fa-check"></i> Pilih
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    // Update pagination
                    updatePaginationCustomerPrice(response.pagination);
                    
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center">Tidak ada data customer</td></tr>');
                    $('#customer-pagination-customer-price').hide();
                }
            } else {
                $('#customer-list-customer-price').html('<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>');
                $('#customer-pagination-customer-price').hide();
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading customers (customer price):', error);
            $('#customer-list-customer-price').html('<tr><td colspan="5" class="text-center">Error loading data: ' + error + '</td></tr>');
            $('#customer-pagination-customer-price').hide();
        });
    }

    // Function untuk update pagination UI (CUSTOMER PRICE)
    function updatePaginationCustomerPrice(pagination) {
        const paginationContainer = $('#customer-pagination-customer-price');
        const paginationLinks = $('#pagination-links-customer-price');
        const paginationInfo = $('#pagination-info-customer-price');
        
        if (pagination.total > pagination.per_page) {
            paginationContainer.show();
            
            // Update pagination info
            paginationInfo.html(
                `Menampilkan ${pagination.from} - ${pagination.to} dari ${pagination.total} customer`
            );
            
            // Update pagination links
            paginationLinks.empty();
            
            // Previous button
            const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
            paginationLinks.append(`
                <li class="${prevDisabled}">
                    <a href="#" onclick="loadCustomersCustomerPrice(currentSearchCustomerPrice, ${pagination.current_page - 1}); return false;">
                        <span>&laquo;</span>
                    </a>
                </li>
            `);
            
            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                const active = i === pagination.current_page ? 'active' : '';
                paginationLinks.append(`
                    <li class="${active}">
                        <a href="#" onclick="loadCustomersCustomerPrice(currentSearchCustomerPrice, ${i}); return false;">${i}</a>
                    </li>
                `);
            }
            
            // Next button
            const nextDisabled = pagination.current_page === pagination.last_page ? 'disabled' : '';
            paginationLinks.append(`
                <li class="${nextDisabled}">
                    <a href="#" onclick="loadCustomersCustomerPrice(currentSearchCustomerPrice, ${pagination.current_page + 1}); return false;">
                        <span>&raquo;</span>
                    </a>
                </li>
            `);
            
        } else {
            paginationContainer.hide();
            if (pagination.total > 0) {
                paginationInfo.html(`Menampilkan semua ${pagination.total} customer`);
                paginationContainer.show();
            }
        }
    }

    // Load customers saat modal dibuka (CUSTOMER PRICE)
    function tampilCustomer() {
        $('#modal-customer-customer-price').modal('show');
        $('#search-customer-customer-price').val('');
        currentSearchCustomerPrice = '';
        currentPageCustomerPrice = 1;
        loadCustomersCustomerPrice('', 1);
    }

    // Search customer dengan delay (CUSTOMER PRICE)
    let searchTimeoutCustomerPrice;
    $('#search-customer-customer-price').on('input', function() {
        const searchTerm = $(this).val();
        clearTimeout(searchTimeoutCustomerPrice);
        searchTimeoutCustomerPrice = setTimeout(() => {
            currentPageCustomerPrice = 1;
            loadCustomersCustomerPrice(searchTerm, 1);
        }, 500);
    });

    // Global function untuk pilih customer (CUSTOMER PRICE)
    window.pilihCustomerCustomerPrice = function(type, id, nama) {
        $('#customer_id').val(id);
        $('#customer_display').val(nama);
        $('#customer_type').val(type);
        $('#modal-customer-customer-price').modal('hide');
        
        // Reset search dan pagination
        $('#search-customer-customer-price').val('');
        currentSearchCustomerPrice = '';
        currentPageCustomerPrice = 1;
    }

    // ========== FUNGSI PRODUK ==========
    
    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihProduk(id, nama, hargaNormal) {
        tambahProdukKeTabel(id, nama, hargaNormal);
        hideProduk();
    }

    function tambahProdukKeTabel(id, nama, hargaNormal = 0, hargaKhusus = 0) {
        console.log('Produk dipilih:', id, nama, hargaNormal, hargaKhusus);
    if (!selectedProdukIds.includes(id.toString())) {
        selectedProdukIds.push(id.toString());
        selectedProdukNames[id] = nama;
        selectedProdukHargaNormal[id] = hargaNormal;
        selectedProdukHarga[id] = hargaKhusus;
        
        $('#table-produk-terpilih tbody').append(`
            <tr id="produk-${id}">
                <td>${nama}</td>
                <td class="text-right">Rp ${formatNumber(hargaNormal)}</td>
                <td>
                    <input type="text" class="form-control harga-khusus-input" 
                        id="harga_khusus_${id}" 
                        value="${formatNumber(hargaKhusus)}" 
                        oninput="formatCurrency(this)"
                        onblur="updateHargaKhusus('${id}', this.value)"
                        placeholder="Harga Khusus">
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
    return new Intl.NumberFormat('id-ID').format(number);
}

// Function untuk format currency input
function formatCurrency(input) {
    let value = input.value.replace(/\./g, '');
    if (!isNaN(value) && value !== '') {
        input.value = formatNumber(parseInt(value));
    }
}

// Function untuk update harga khusus
function updateHargaKhusus(id, value) {
    const numericValue = parseInt(value.replace(/\./g, '')) || 0;
    selectedProdukHarga[id] = numericValue;
}

// Function untuk parse number dari formatted string
function parseNumber(formattedNumber) {
    if (typeof formattedNumber === 'number') {
        return formattedNumber;
    }
    if (typeof formattedNumber !== 'string') {
        return 0;
    }
    const cleanNumber = formattedNumber.toString().replace(/\./g, '');
    return parseInt(cleanNumber) || 0;
}

    function hapusProduk(id) {
        const index = selectedProdukIds.indexOf(id.toString());
        if (index > -1) {
            selectedProdukIds.splice(index, 1);
            delete selectedProdukNames[id];
            delete selectedProdukHarga[id];
            $(`#produk-${id}`).remove();
        }
    }
</script>
@endpush
