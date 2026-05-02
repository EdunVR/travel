<div class="row mt-3">
    <div class="col-md-12">
        <h4><i data-feather="shopping-bag"></i> Produk yang Diambil</h4>
        
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#keranjang" aria-controls="keranjang" role="tab" data-toggle="tab">
                    <i data-feather="shopping-cart"></i> Keranjang
                    <span class="badge bg-primary" id="cartCount">0</span>
                </a>
            </li>
            <li role="presentation">
                <a href="#proses" aria-controls="proses" role="tab" data-toggle="tab">
                    <i data-feather="clock"></i> Proses
                </a>
            </li>
            <li role="presentation">
                <a href="#selesai" aria-controls="selesai" role="tab" data-toggle="tab">
                    <i data-feather="check-circle"></i> Selesai
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab Keranjang -->
            <div role="tabpanel" class="tab-pane active" id="keranjang">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered" id="cartTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Produk</th>
                                <th width="15%">Harga</th>
                                <th width="10%">Jumlah</th>
                                <th width="15%">Subtotal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data keranjang akan diisi via JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total</th>
                                <th id="cartTotal">Rp 0</th>
                                <th>
                                    <button class="btn btn-sm btn-primary" id="checkoutBtn">
                                        <i data-feather="shopping-cart"></i> Checkout
                                    </button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#productModal">
                        <i data-feather="plus"></i> Tambah Produk
                    </button>
                </div>
            </div>
            
            <!-- Tab Proses dan Selesai tetap sama -->
            <!-- ... -->
        </div>
    </div>
</div>

<!-- Modal Produk -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="productSearch" placeholder="Cari produk...">
                        </div>
                        <div class="list-group" id="productCategories">
                            <a href="#" class="list-group-item list-group-item-action active" data-category="all">
                                Semua Kategori
                            </a>
                            @foreach($categories as $category)
                            <a href="#" class="list-group-item list-group-item-action" data-category="{{ $category->id }}">
                                {{ $category->nama_kategori }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row" id="productGrid">
                            <!-- Produk akan diisi via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Produk -->
<div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailTitle">Detail Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="productCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <!-- Gambar produk akan diisi via JavaScript -->
                            </div>
                            <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 id="productName"></h4>
                        <div class="mb-3">
                            <span class="h5 text-primary" id="productPrice"></span>
                            <span class="text-muted ml-2" id="productDiscount"></span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-group">
                                <label>Varian</label>
                                <select class="form-control" id="productVariant">
                                    <!-- Varian akan diisi via JavaScript -->
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" id="productQty" min="1" value="1">
                            </div>
                            
                            <button class="btn btn-primary btn-block" id="addToCartBtn">
                                <i data-feather="shopping-cart"></i> Tambah ke Keranjang
                            </button>
                        </div>
                        
                        <div class="product-description">
                            <h6>Deskripsi Produk</h6>
                            <div id="productDescription"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    feather.replace();
    let cartItems = JSON.parse(localStorage.getItem('jemaahCart')) || [];
    let currentProduct = null;
    
    // Load produk
    function loadProducts(category = 'all', search = '') {
        $.get('{{ route("api.products") }}', {
            category: category,
            search: search
        }, function(response) {
            const grid = $('#productGrid');
            grid.empty();
            
            if (response.data.length > 0) {
                response.data.forEach(product => {
                    const imageUrl = product.images.length > 0 ? 
                        '{{ asset("storage") }}/' + product.images[0].path : 
                        '{{ asset("img/no-image.png") }}';
                    
                    grid.append(`
                        <div class="col-md-4 mb-4 product-item" data-id="${product.id}" data-category="${product.id_kategori}">
                            <div class="card h-100">
                                <img src="${imageUrl}" class="card-img-top" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">${product.nama_produk}</h5>
                                    <p class="card-text text-primary">Rp ${formatNumber(product.harga_jual)}</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <button class="btn btn-sm btn-primary btn-block view-product" data-id="${product.id}">
                                        <i data-feather="eye"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                });
            } else {
                grid.append('<div class="col-12 text-center py-5"><p>Tidak ada produk ditemukan</p></div>');
            }
            
            feather.replace();
        });
    }
    
    // Load detail produk
    function loadProductDetail(id) {
        $.get('{{ route("api.products") }}/${id}', function(response) {
            currentProduct = response;
            
            $('#productDetailTitle').text(response.nama_produk);
            $('#productName').text(response.nama_produk);
            $('#productPrice').text('Rp ' + formatNumber(response.harga_jual));
            
            if (response.diskon > 0) {
                const discountPrice = response.harga_jual - (response.harga_jual * (response.diskon / 100));
                $('#productDiscount').html(`<del>Rp ${formatNumber(response.harga_jual)}</del> Rp ${formatNumber(discountPrice)}`);
            } else {
                $('#productDiscount').empty();
            }
            
            // Gambar produk
            const carousel = $('#productCarousel .carousel-inner');
            carousel.empty();
            
            if (response.images.length > 0) {
                response.images.forEach((image, index) => {
                    carousel.append(`
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="{{ asset('storage') }}/${image.path}" class="d-block w-100" style="height: 300px; object-fit: contain;">
                        </div>
                    `);
                });
            } else {
                carousel.append(`
                    <div class="carousel-item active">
                        <img src="{{ asset('img/no-image.png') }}" class="d-block w-100" style="height: 300px; object-fit: contain;">
                    </div>
                `);
            }
            
            // Varian produk
            const variantSelect = $('#productVariant');
            variantSelect.empty();
            
            if (response.variants.length > 0) {
                response.variants.forEach(variant => {
                    variantSelect.append(`<option value="${variant.id}" data-price="${variant.harga}">${variant.nama_varian} - Rp ${formatNumber(variant.harga)}</option>`);
                });
            } else {
                variantSelect.append(`<option value="${response.id}" data-price="${response.harga_jual}">Standar - Rp ${formatNumber(response.harga_jual)}</option>`);
            }
            
            // Deskripsi produk
            $('#productDescription').html(response.spesifikasi || 'Tidak ada deskripsi');
            
            $('#productDetailModal').modal('show');
        });
    }
    
    // Update cart display
    function updateCartDisplay() {
        const tbody = $('#cartTable tbody');
        tbody.empty();
        
        let total = 0;
        
        if (cartItems.length > 0) {
            cartItems.forEach((item, index) => {
                const subtotal = item.price * item.qty;
                total += subtotal;
                
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            ${item.name}
                            ${item.variant ? `<br><small class="text-muted">Varian: ${item.variant}</small>` : ''}
                        </td>
                        <td>Rp ${formatNumber(item.price)}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm qty-input" 
                                   data-index="${index}" value="${item.qty}" min="1">
                        </td>
                        <td>Rp ${formatNumber(subtotal)}</td>
                        <td>
                            <button class="btn btn-sm btn-danger remove-from-cart" data-index="${index}">
                                <i data-feather="trash-2"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="6" class="text-center">Keranjang kosong</td></tr>');
        }
        
        $('#cartTotal').text('Rp ' + formatNumber(total));
        $('#cartCount').text(cartItems.length);
        feather.replace();
        
        // Simpan ke localStorage
        localStorage.setItem('jemaahCart', JSON.stringify(cartItems));
    }
    
    // Format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Event listeners
    $('#productSearch').keyup(function() {
        const category = $('#productCategories .active').data('category');
        loadProducts(category, $(this).val());
    });
    
    $('#productCategories a').click(function(e) {
        e.preventDefault();
        $('#productCategories a').removeClass('active');
        $(this).addClass('active');
        loadProducts($(this).data('category'), $('#productSearch').val());
    });
    
    $(document).on('click', '.view-product', function() {
        loadProductDetail($(this).data('id'));
    });
    
    $('#addToCartBtn').click(function() {
        if (currentProduct) {
            const variantSelect = $('#productVariant');
            const variantId = variantSelect.val();
            const variantName = variantSelect.find('option:selected').text().split(' - ')[0];
            const price = parseFloat(variantSelect.find('option:selected').data('price'));
            const qty = parseInt($('#productQty').val());
            
            const cartItem = {
                id: currentProduct.id,
                variantId: variantId,
                name: currentProduct.nama_produk,
                variant: variantName,
                price: price,
                qty: qty
            };
            
            // Cek apakah produk sudah ada di keranjang
            const existingIndex = cartItems.findIndex(item => 
                item.id === cartItem.id && item.variantId === cartItem.variantId);
            
            if (existingIndex >= 0) {
                cartItems[existingIndex].qty += cartItem.qty;
            } else {
                cartItems.push(cartItem);
            }
            
            updateCartDisplay();
            $('#productDetailModal').modal('hide');
            Swal.fire('Berhasil', 'Produk telah ditambahkan ke keranjang', 'success');
        }
    });
    
    $(document).on('click', '.remove-from-cart', function() {
        const index = $(this).data('index');
        cartItems.splice(index, 1);
        updateCartDisplay();
    });
    
    $(document).on('change', '.qty-input', function() {
        const index = $(this).data('index');
        const qty = parseInt($(this).val());
        
        if (qty > 0) {
            cartItems[index].qty = qty;
            updateCartDisplay();
        }
    });
    
    $('#checkoutBtn').click(function() {
        if (cartItems.length > 0) {
            Swal.fire({
                title: 'Checkout Produk',
                text: 'Anda yakin ingin checkout produk yang ada di keranjang?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Checkout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proses checkout
                    $.post('{{ route("jemaah.checkout") }}', {
                        _token: '{{ csrf_token() }}',
                        member_id: '{{ $member->id }}',
                        items: cartItems
                    }, function(response) {
                        if (response.success) {
                            cartItems = [];
                            updateCartDisplay();
                            Swal.fire('Berhasil', 'Produk berhasil di checkout', 'success');
                        }
                    }).fail(function() {
                        Swal.fire('Error', 'Terjadi kesalahan saat checkout', 'error');
                    });
                }
            });
        } else {
            Swal.fire('Peringatan', 'Keranjang belanja kosong', 'warning');
        }
    });
    
    // Load initial data
    loadProducts();
    updateCartDisplay();
});
</script>
