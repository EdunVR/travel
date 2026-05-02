<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <div class="form-group row">
        <label for="nama_template" class="col-sm-2 col-form-label">Nama Template</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="nama_template" 
                name="nama_template" value="{{ $template->nama_template ?? old('nama_template') }}" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="deskripsi" class="col-sm-2 col-form-label">Deskripsi</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="deskripsi" name="deskripsi" 
                rows="3">{{ $template->deskripsi ?? old('deskripsi') }}</textarea>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="komponen_utama" name="komponen_utama" 
                    value="1" {{ (isset($template) && $template->komponen_utama) ? 'checked' : '' }}>
                <label class="custom-control-label" for="komponen_utama">
                    Komponen Utama (Semua komponen akan otomatis divalidasi saat disimpan)
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Produk Terkait (Opsional)</label>
        <div class="col-sm-10">
            <select name="products[]" class="form-control select2" multiple="multiple">
                @foreach($products as $product)
                <option value="{{ $product->id_produk }}" 
                    {{ in_array($product->id_produk, old('products', $template->products->pluck('id_produk')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ $product->nama_produk }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Komponen Biaya</label>
        <div class="col-sm-10">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="20%">Komponen Biaya</th>
                        <th width="10%">Jumlah</th>
                        <th width="10%">Satuan</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Budget</th>
                        <th width="20%">Deskripsi</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rab-items">
                    @if(isset($template) && $template->details->count() > 0)
                        @foreach($template->details as $index => $detail)
                        <tr class="rab-item">
                            <td>
                                <input type="text" name="items[{{ $index }}][nama_komponen]" 
                                    class="form-control" value="{{ $detail->nama_komponen }}" required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][jumlah]" 
                                    class="form-control jumlah" value="{{ $detail->jumlah }}" required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][satuan]" 
                                    class="form-control" value="{{ $detail->satuan }}">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][harga_satuan]" 
                                    class="form-control harga-satuan" value="{{ number_format($detail->harga_satuan, 0, ',', '.') }}" required>
                            </td>
                            <td class="text-right budget">Rp {{ number_format($detail->budget, 0, ',', '.') }}</td>
                            <td>
                                <textarea name="items[{{ $index }}][deskripsi]" 
                                    class="form-control" rows="1">{{ $detail->deskripsi }}</textarea>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="rab-item">
                            <td>
                                <input type="text" name="items[0][nama_komponen]" 
                                    class="form-control" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][jumlah]" 
                                    class="form-control jumlah" value="1" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][satuan]" 
                                    class="form-control" value="pcs">
                            </td>
                            <td>
                                <input type="text" name="items[0][harga_satuan]" 
                                    class="form-control harga-satuan" value="0" required>
                            </td>
                            <td class="text-right budget">Rp 0</td>
                            <td>
                                <textarea name="items[0][deskripsi]" 
                                    class="form-control" rows="1"></textarea>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total Biaya:</th>
                        <th id="total-biaya" class="text-right">
                            Rp {{ isset($template) ? number_format($template->total_biaya_per_orang, 0, ',', '.') : '0' }}
                        </th>
                        <th colspan="2">
                            <button type="button" id="add-rab-item" class="btn btn-primary btn-sm">
                                <i data-feather="plus"></i> Tambah
                            </button>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('rab_template.index') }}" class="btn btn-default">Batal</a>
        </div>
    </div>
</form>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script>
$(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Pilih produk terkait",
        allowClear: true
    });

    // Format number with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Parse formatted number
    function parseNumber(str) {
        return parseFloat(str.replace(/\./g, '')) || 0;
    }

    // Format as currency
    function formatCurrency(num) {
        return 'Rp ' + formatNumber(num);
    }

    // Initialize AutoNumeric for an element
    function initAutoNumeric(element) {
        new AutoNumeric(element, {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            unformatOnSubmit: true,
            modifyValueOnWheel: false,
            emptyInputBehavior: 'zero'
        });
    }

    // Calculate budget for a row
    function calculateRowBudget(row) {
        const jumlah = parseFloat(row.find('.jumlah').val()) || 0;
        const hargaSatuan = parseNumber(row.find('.harga-satuan').val()) || 0;
        const budget = jumlah * hargaSatuan;
        row.find('.budget').text(formatCurrency(budget));
        return budget;
    }

    // Calculate total budget
    function calculateTotal() {
        let total = 0;
        $('.rab-item').each(function() {
            total += calculateRowBudget($(this));
        });
        $('#total-biaya').text(formatCurrency(total));
        $('input[name="total_biaya_per_orang"]').val(total);
    }

    // Initialize all AutoNumeric inputs
    function initAllAutoNumeric() {
        $('.harga-satuan').each(function() {
            if (!$(this).hasClass('autoNumeric')) {
                initAutoNumeric(this);
            }
        });
    }

    // Calculate when values change
    $(document).on('input', '.jumlah, .harga-satuan', function() {
        calculateRowBudget($(this).closest('.rab-item'));
        calculateTotal();
    });

    // Add new item
    $('#add-rab-item').click(function() {
        const itemCount = $('.rab-item').length;
        const newItem = `
        <tr class="rab-item">
            <td>
                <input type="text" name="items[${itemCount}][nama_komponen]" 
                    class="form-control" required>
            </td>
            <td>
                <input type="text" name="items[${itemCount}][jumlah]" 
                    class="form-control jumlah" value="1" required>
            </td>
            <td>
                <input type="text" name="items[${itemCount}][satuan]" 
                    class="form-control" value="pcs">
            </td>
            <td>
                <input type="text" name="items[${itemCount}][harga_satuan]" 
                    class="form-control harga-satuan" value="0" required>
            </td>
            <td class="text-right budget">Rp 0</td>
            <td>
                <textarea name="items[${itemCount}][deskripsi]" 
                    class="form-control" rows="1"></textarea>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i data-feather="trash-2"></i>
                </button>
            </td>
        </tr>`;
        
        const $newRow = $(newItem);
        $('#rab-items').append($newRow);
        
        // Initialize AutoNumeric for the new input
        initAutoNumeric($newRow.find('.harga-satuan')[0]);
        
        // Initialize feather icons for the new row
        feather.replace({
            'aria-hidden': 'true'
        });
        
        // Trigger calculation
        calculateTotal();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.rab-item').remove();
        calculateTotal();
    });

    // Initialize everything on load
    initAllAutoNumeric();
    calculateTotal();
    feather.replace();

    $('form').on('submit', function(e) {
        // Jika checkbox dicentang, set semua nilai disetujui
        if ($('#komponen_utama').is(':checked')) {
            $('.rab-item').each(function(index) {
                const budget = parseNumber($(this).find('.budget').text().replace('Rp ', ''));
                $(this).find('input[name="items['+index+'][nilai_disetujui]"]').val(budget);
                $(this).find('input[name="items['+index+'][disetujui]"]').prop('checked', true);
            });
        }
    });

    
});
</script>
@endpush
