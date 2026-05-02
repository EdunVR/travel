<div class="modal fade" id="modal-produk" tabindex="-1" aria-labelledby="modal-produk" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-glass">
            <div class="modal-header bg-gradient-pastel-primary">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i data-feather="plus-circle" class="icon-lg mr-2"></i>Tambah Produksi Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i data-feather="x"></i></span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <form id="form-produksi" method="POST" action="{{ route('produksi.store') }}">
                    @csrf
                    <input type="hidden" name="id_outlet_produksi" id="id_outlet_produksi" value="{{ $id_outlet }}">
                    
                    <!-- Product Selection Card -->
                    <div class="card card-pastel mb-4 animate-fade-in">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="id_produk" class="font-weight-bold text-primary mb-2">
                                            <i data-feather="package" class="icon-sm mr-2"></i>Pilih Produk
                                        </label>
                                        <select name="id_produk" id="id_produk" class="form-control form-control-lg select2-custom" required>
                                            <option value="">Pilih Produk...</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id_produk }}">{{ $produk->nama_produk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="jumlah" class="font-weight-bold text-primary mb-2">
                                            <i data-feather="hash" class="icon-sm mr-2"></i>Jumlah Produksi
                                        </label>
                                        <input type="number" name="jumlah" id="jumlah" class="form-control form-control-lg input-pastel" min="1" required placeholder="Masukkan jumlah...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HPP Calculation Display -->
                    <div class="card card-pastel-info mb-4 animate-slide-up" id="hpp-display" style="display: none;">
                        <div class="card-body py-3">
                            <div class="row text-center">
                                <div class="col-md-4 border-right">
                                    <small class="text-muted d-block">
                                        <i data-feather="dollar-sign" class="icon-xs mr-1"></i>Total Biaya Bahan
                                    </small>
                                    <h5 class="mb-0 text-primary font-weight-bold mt-1" id="total-biaya">Rp 0</h5>
                                </div>
                                <div class="col-md-4 border-right">
                                    <small class="text-muted d-block">
                                        <i data-feather="target" class="icon-xs mr-1"></i>Jumlah Produksi
                                    </small>
                                    <h5 class="mb-0 text-info font-weight-bold mt-1" id="jumlah-produksi-display">0 unit</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">
                                        <i data-feather="trending-up" class="icon-xs mr-1"></i>HPP per Unit
                                    </small>
                                    <h5 class="mb-0 text-success font-weight-bold mt-1" id="hpp-unit">Rp 0</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ingredients Selection -->
                    <div class="card card-pastel-primary mb-4 animate-fade-in">
                        <div class="card-header bg-pastel-primary">
                            <h6 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="list" class="icon-sm mr-2"></i>Daftar Bahan Baku
                                <button type="button" class="btn btn-sm btn-pastel-info float-right btn-hover-glow" onclick="showCalculator()">
                                    <i data-feather="calculator" class="icon-xs mr-1"></i>Kalkulator Konversi
                                </button>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="alert alert-pastel-info m-3">
                                <div class="d-flex align-items-center">
                                    <i data-feather="info" class="icon-sm mr-2"></i>
                                    <small>Klik pada bahan untuk menambahkannya ke produksi</small>
                                </div>
                            </div>
                            
                            <div class="row m-0">
                                <!-- Available Ingredients -->
                                <div class="col-md-6 border-right p-3">
                                    <h6 class="font-weight-bold text-muted mb-3">
                                        <i data-feather="archive" class="icon-sm mr-2"></i>Bahan Tersedia
                                    </h6>
                                    <div id="available-bahans" class="available-container">
                                        @foreach($bahans as $bahan)
                                            @php
                                                $stokTotal = $bahan->hargaBahan->sum('stok');
                                                $isDisabled = $stokTotal <= 0;
                                                $totalValue = 0;
                                                $totalStock = 0;
                                                foreach ($bahan->hargaBahan as $harga) {
                                                    if ($harga->stok > 0) {
                                                        $totalValue += $harga->harga_beli * $harga->stok;
                                                        $totalStock += $harga->stok;
                                                    }
                                                }
                                                $avgPrice = $totalStock > 0 ? $totalValue / $totalStock : 0;
                                            @endphp
                                            <div class="bahan-item card card-pastel mb-2 {{ $isDisabled ? 'bg-light' : 'cursor-pointer btn-hover-lift' }}" 
                                                 data-bahan-id="{{ $bahan->id_bahan }}"
                                                 data-bahan-name="{{ $bahan->nama_bahan }}"
                                                 data-stok="{{ $stokTotal }}"
                                                 data-satuan="{{ $bahan->satuan->nama_satuan ?? '-' }}"
                                                 data-harga-rata="{{ $avgPrice }}"
                                                 onclick="{{ !$isDisabled ? "addBahanToProduction('{$bahan->id_bahan}')" : '' }}">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 {{ $isDisabled ? 'text-muted' : 'text-dark' }}">
                                                                {{ $bahan->nama_bahan }}
                                                            </h6>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    <i data-feather="database" class="icon-xs mr-1"></i>
                                                                    <span class="font-weight-bold">{{ $stokTotal }}</span> 
                                                                    {{ $bahan->satuan->nama_satuan ?? '-' }}
                                                                </small>
                                                                <small class="text-success font-weight-bold">
                                                                    <i data-feather="dollar-sign" class="icon-xs mr-1"></i>
                                                                    ~{{ format_uang($avgPrice) }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        @if(!$isDisabled)
                                                        <div class="text-success ml-2 btn-hover-grow">
                                                            <i data-feather="plus-circle"></i>
                                                        </div>
                                                        @else
                                                        <div class="text-muted ml-2">
                                                            <i data-feather="x-circle"></i>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Production Area -->
                                <div class="col-md-6 p-3">
                                    <h6 class="font-weight-bold text-success mb-3">
                                        <i data-feather="check-circle" class="icon-sm mr-2"></i>Bahan Diproduksi
                                    </h6>
                                    <div id="production-area" class="production-container border rounded p-2 min-h-200 bg-pastel-success">
                                        <div class="text-center text-muted py-5" id="empty-production">
                                            <i data-feather="arrow-left" class="icon-lg mb-3"></i>
                                            <p>Klik bahan di sebelah kiri untuk menambahkannya</p>
                                        </div>
                                        <div id="selected-bahans" class="selected-container">
                                            <!-- Selected ingredients will appear here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Fields for Selected Ingredients -->
                            <div id="bahan-inputs" class="p-3 border-top bg-white" style="display: none;">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i data-feather="edit-3" class="icon-sm mr-2"></i>Detail Jumlah Bahan
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-bordered">
                                        <thead class="bg-pastel-primary">
                                            <tr>
                                                <th width="25%" class="border-0">
                                                    <i data-feather="package" class="icon-xs mr-1"></i>Nama Bahan
                                                </th>
                                                <th width="15%" class="text-center border-0">
                                                    <i data-feather="database" class="icon-xs mr-1"></i>Stok
                                                </th>
                                                <th width="15%" class="text-center border-0">
                                                    <i data-feather="dollar-sign" class="icon-xs mr-1"></i>Harga/Unit
                                                </th>
                                                <th width="20%" class="text-center border-0">
                                                    <i data-feather="edit-2" class="icon-xs mr-1"></i>Jumlah
                                                </th>
                                                <th width="15%" class="text-center border-0">
                                                    <i data-feather="bar-chart-2" class="icon-xs mr-1"></i>Subtotal
                                                </th>
                                                <th width="10%" class="text-center border-0">
                                                    <i data-feather="trash-2" class="icon-xs mr-1"></i>Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bahan-input-container">
                                            <!-- Input rows will be generated here -->
                                        </tbody>
                                        <tfoot id="bahan-total-footer" style="display: none;">
                                            <tr class="table-pastel-info font-weight-bold">
                                                <td colspan="4" class="text-right border-0">Total Biaya Bahan:</td>
                                                <td class="text-center border-0" id="total-all-bahan">Rp 0</td>
                                                <td class="border-0"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-pastel-success btn-lg px-5 btn-hover-glow">
                            <i data-feather="save" class="icon-sm mr-2"></i>Simpan Produksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Calculator Modal -->
<div class="modal fade" id="modal-calculator" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content modal-glass">
            <div class="modal-header bg-pastel-info">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i data-feather="calculator" class="icon-sm mr-2"></i>Kalkulator Konversi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i data-feather="x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold text-muted">Nilai</label>
                    <input type="number" id="calc-value" class="form-control input-pastel" value="1" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted">Dari Satuan</label>
                    <select id="calc-from" class="form-control input-pastel">
                        <option value="gram">Gram</option>
                        <option value="kg">Kilogram</option>
                        <option value="ml">Mililiter</option>
                        <option value="liter">Liter</option>
                        <option value="pcs">Pieces</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted">Ke Satuan</label>
                    <select id="calc-to" class="form-control input-pastel">
                        <option value="kg">Kilogram</option>
                        <option value="gram">Gram</option>
                        <option value="liter">Liter</option>
                        <option value="ml">Mililiter</option>
                        <option value="pcs">Pieces</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted">Hasil</label>
                    <input type="text" id="calc-result" class="form-control bg-light border-0 font-weight-bold text-success" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-pastel-primary btn-hover-grow" onclick="calculateConversion()">
                    <i data-feather="refresh-cw" class="icon-xs mr-1"></i>Hitung
                </button>
                <button type="button" class="btn btn-pastel-secondary" data-dismiss="modal">
                    <i data-feather="x" class="icon-xs mr-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-glass {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 20px;
}

.bg-gradient-pastel-primary {
    background: linear-gradient(135deg, #a8c0ff 0%, #b6fbff 100%) !important;
    border-radius: 20px 20px 0 0 !important;
}

.bg-pastel-primary {
    background: linear-gradient(135deg, #a8c0ff 0%, #b6fbff 100%) !important;
}

.bg-pastel-info {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%) !important;
}

.bg-pastel-success {
    background: linear-gradient(135deg, #a8e6cf 0%, #dcedc1 100%) !important;
}

.card-pastel {
    border: none;
    border-radius: 15px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-pastel:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-pastel-primary {
    border: none;
    border-radius: 15px;
    background: linear-gradient(135deg, #e0f7fa 0%, #bbdefb 100%);
    box-shadow: 0 4px 15px rgba(33,150,243,0.2);
}

.card-pastel-info {
    border: none;
    border-radius: 15px;
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    box-shadow: 0 4px 15px rgba(76,175,80,0.2);
}

.alert-pastel-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: none;
    border-radius: 10px;
    color: #1565c0;
}

.input-pastel {
    border: none;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.input-pastel:focus {
    background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);
    box-shadow: 0 0 0 3px rgba(33,150,243,0.2);
}

.select2-custom .select2-selection {
    border: none !important;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
    border-radius: 10px !important;
    padding: 0.75rem 1rem !important;
    height: auto !important;
}

.btn-hover-glow:hover {
    box-shadow: 0 0 20px rgba(33,150,243,0.5);
    transform: translateY(-2px);
}

.btn-hover-lift:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.btn-hover-grow:hover {
    transform: scale(1.05);
}

.available-container, .production-container {
    min-height: 300px;
    max-height: 400px;
    overflow-y: auto;
}

.min-h-200 {
    min-height: 200px;
}

.bahan-item {
    transition: all 0.3s ease;
    border: none;
    border-radius: 10px;
}

.bahan-item.cursor-pointer {
    cursor: pointer;
}

.bahan-item.cursor-pointer:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.table-pastel-info {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
}

.border-0 {
    border: none !important;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease;
}

.animate-slide-up {
    animation: slideUp 0.8s ease;
}

.fade-in-row {
    animation: fadeInUp 0.5s ease;
}

/* Custom scrollbar */
.available-container::-webkit-scrollbar,
.production-container::-webkit-scrollbar {
    width: 6px;
}

.available-container::-webkit-scrollbar-track,
.production-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.available-container::-webkit-scrollbar-thumb,
.production-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #a8c0ff 0%, #b6fbff 100%);
    border-radius: 10px;
}

.available-container::-webkit-scrollbar-thumb:hover,
.production-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>


<script>
let selectedBahans = {};

$(document).ready(function() {
    // Initialize Select2 with custom class
    $('.select2-custom').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Produk'
    });

    // Initialize Feather Icons
    feather.replace();

    // Event listeners
    $('#jumlah').on('input', calculateHPP);
});

function addBahanToProduction(bahanId) {
    const bahanItem = $(`[data-bahan-id="${bahanId}"]`);
    const bahanName = bahanItem.data('bahan-name');
    const stokTotal = bahanItem.data('stok');
    const satuan = bahanItem.data('satuan');
    const hargaRata = bahanItem.data('harga-rata');

    // Check if already selected
    if (selectedBahans[bahanId]) {
        return;
    }

    // Add to selected bahans object
    selectedBahans[bahanId] = {
        id: bahanId,
        name: bahanName,
        stok: stokTotal,
        satuan: satuan,
        jumlah: 1,
        harga_rata: hargaRata,
        harga_fifo: 0,
        subtotal: 0,
        loading: true
    };

    updateProductionDisplay();
    updateInputFields();
    // Calculate harga FIFO untuk bahan ini
    calculateHargaFifo(bahanId, 1);
}

function removeBahan(bahanId) {
    delete selectedBahans[bahanId];
    updateProductionDisplay();
    updateInputFields();
    calculateHPP();
}

function updateProductionDisplay() {
    const selectedContainer = $('#selected-bahans');
    const emptyProduction = $('#empty-production');
    
    selectedContainer.empty();
    
    if (Object.keys(selectedBahans).length === 0) {
        emptyProduction.show();
        $('#bahan-inputs').hide();
        $('#hpp-display').hide();
    } else {
        emptyProduction.hide();
        $('#bahan-inputs').show();
        
        Object.values(selectedBahans).forEach(bahan => {
            const subtotalDisplay = bahan.loading ? 
                '<small class="loading-harga">menghitung...</small>' : 
                formatRupiah(bahan.subtotal);
                
            selectedContainer.append(`
                <div class="bahan-item card mb-2 bg-light" data-bahan-id="${bahan.id}">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="font-weight-bold">${bahan.name}</span>
                                <small class="text-muted d-block">${bahan.jumlah} ${bahan.satuan}</small>
                                <small class="text-success d-block">${subtotalDisplay}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBahan('${bahan.id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
        });
    }
}

function updateInputFields() {
    const inputContainer = $('#bahan-input-container');
    const totalFooter = $('#bahan-total-footer');
    
    inputContainer.empty();
    
    if (Object.keys(selectedBahans).length === 0) {
        totalFooter.hide();
        return;
    }
    
    Object.values(selectedBahans).forEach(bahan => {
        const hargaDisplay = bahan.loading ? 
            '<small class="loading-harga">menghitung...</small>' : 
            formatRupiah(bahan.harga_fifo);
            
        const subtotalDisplay = bahan.loading ? 
            '<small class="loading-harga">menghitung...</small>' : 
            formatRupiah(bahan.subtotal);
        
        inputContainer.append(`
            <tr class="bahan-input-row" id="input-row-${bahan.id}">
                <td>
                    <label class="mb-0 font-weight-bold">${bahan.name}</label>
                    <small class="text-muted d-block">Satuan: ${bahan.satuan}</small>
                </td>
                <td class="text-center">
                    <span class="badge badge-info">${bahan.stok} ${bahan.satuan}</span>
                </td>
                <td class="text-center">
                    ${hargaDisplay}
                </td>
                <td>
                    <input type="number" 
                           name="bahan[${bahan.id}][jumlah]" 
                           class="form-control form-control-sm text-center bahan-jumlah" 
                           data-bahan-id="${bahan.id}"
                           value="${bahan.jumlah}"
                           min="1" 
                           max="${bahan.stok}" 
                           required
                           onchange="updateBahanJumlah('${bahan.id}', this.value)">
                    <input type="hidden" name="bahan[${bahan.id}][checked]" value="1">
                </td>
                <td class="text-center font-weight-bold">
                    ${subtotalDisplay}
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBahan('${bahan.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    
    totalFooter.show();
    updateTotalBahan();
}

function calculateHargaFifo(bahanId, jumlah) {
    $.ajax({
        url: '{{ route("produksi.getHargaFifo") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id_bahan: bahanId,
            jumlah: jumlah
        },
        success: function(response) {
            if (response.success) {
                selectedBahans[bahanId].harga_fifo = response.harga_rata;
                selectedBahans[bahanId].subtotal = response.harga_total;
                selectedBahans[bahanId].loading = false;
                
                updateProductionDisplay();
                updateInputFields();
                calculateHPP();
            } else {
                alert(response.message);
                removeBahan(bahanId);
            }
        },
        error: function() {
            alert('Error menghitung harga bahan');
            removeBahan(bahanId);
        }
    });
}

function updateBahanJumlah(bahanId, jumlah) {
    const newJumlah = parseInt(jumlah) || 1;
    
    if (newJumlah > selectedBahans[bahanId].stok) {
        alert('Jumlah melebihi stok tersedia!');
        $(`#input-row-${bahanId} .bahan-jumlah`).val(selectedBahans[bahanId].stok);
        return;
    }
    
    selectedBahans[bahanId].jumlah = newJumlah;
    selectedBahans[bahanId].loading = true;
    
    updateProductionDisplay();
    updateInputFields();
    
    // Recalculate harga FIFO dengan jumlah baru
    calculateHargaFifo(bahanId, newJumlah);
}

function updateTotalBahan() {
    let totalAll = 0;
    Object.values(selectedBahans).forEach(bahan => {
        if (!bahan.loading) {
            totalAll += bahan.subtotal;
        }
    });
    $('#total-all-bahan').text(formatRupiah(totalAll));
}

function calculateHPP() {
    const jumlahProduksi = parseInt($('#jumlah').val()) || 0;
    let totalBiaya = 0;

    // Calculate total from all bahan
    Object.values(selectedBahans).forEach(bahan => {
        if (!bahan.loading) {
            totalBiaya += bahan.subtotal;
        }
    });

    const hppUnit = jumlahProduksi > 0 ? totalBiaya / jumlahProduksi : 0;

    // Update display
    $('#total-biaya').text(formatRupiah(totalBiaya));
    $('#jumlah-produksi-display').text(`${jumlahProduksi} unit`);
    $('#hpp-unit').text(formatRupiah(hppUnit));
    
    // Show HPP display if there are selected bahans and production quantity
    if (Object.keys(selectedBahans).length > 0 && jumlahProduksi > 0) {
        $('#hpp-display').show();
    } else {
        $('#hpp-display').hide();
    }
}

function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function showCalculator() {
    $('#modal-calculator').modal('show');
}

function calculateConversion() {
    const value = parseFloat($('#calc-value').val()) || 0;
    const from = $('#calc-from').val();
    const to = $('#calc-to').val();
    
    let result = value;
    
    // Conversion factors
    const conversions = {
        'gram_kg': value / 1000,
        'kg_gram': value * 1000,
        'ml_liter': value / 1000,
        'liter_ml': value * 1000,
        'gram_ml': value, // approximate for water
        'ml_gram': value, // approximate for water
        'kg_liter': value, // approximate for water
        'liter_kg': value  // approximate for water
    };
    
    if (from === 'gram' && to === 'kg') result = conversions.gram_kg;
    else if (from === 'kg' && to === 'gram') result = conversions.kg_gram;
    else if (from === 'ml' && to === 'liter') result = conversions.ml_liter;
    else if (from === 'liter' && to === 'ml') result = conversions.liter_ml;
    else if (from === 'gram' && to === 'ml') result = conversions.gram_ml;
    else if (from === 'ml' && to === 'gram') result = conversions.ml_gram;
    else if (from === 'kg' && to === 'liter') result = conversions.kg_liter;
    else if (from === 'liter' && to === 'kg') result = conversions.liter_kg;
    
    $('#calc-result').val(result.toFixed(4));
}
</script>
