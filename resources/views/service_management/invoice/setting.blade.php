@extends('app')

@section('title')
    Setting Nomor Invoice
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Setting Nomor Invoice</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Setting Nomor Invoice</h3>
            </div>
            <div class="box-body">
                <form id="form-setting">
                    @csrf
                    <div class="form-group">
                        <label for="current_number">Nomor Invoice Saat Ini</label>
                        <input type="text" class="form-control" value="{{ $currentInvoiceNumber }}" readonly>
                        <small class="text-muted">Format: 000/BBN.INV/IX/2025</small>
                    </div>

                    <div class="form-group">
                        <label for="next_number">Nomor Berikutnya Akan Menjadi</label>
                        <input type="text" class="form-control" value="{{ $nextInvoiceNumber }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="starting_number">Set Nomor Mulai *</label>
                        <input type="number" name="starting_number" id="starting_number" class="form-control" 
                               value="{{ $currentNumber + 1 }}" min="1" max="999" required>
                        <small class="text-muted">Masukkan nomor urut (contoh: 25 untuk 025/BBN.INV/IX/2025)</small>
                    </div>

                    <div class="form-group">
                        <label for="year">Tahun</label>
                        <input type="number" name="year" id="year" class="form-control" 
                               value="{{ date('Y') }}" min="2020" max="2030" required>
                    </div>

                    <div class="alert alert-warning">
                        <strong>Peringatan!</strong> Setting nomor mulai akan mempengaruhi semua invoice baru yang dibuat.
                        Pastikan nomor yang diinput sudah benar.
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Setting
                    </button>
                    <a href="{{ route('service.invoice.history') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali ke History
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Informasi Format Invoice</h3>
            </div>
            <div class="box-body">
                <div class="alert alert-info">
                    <strong>Format Nomor Invoice:</strong><br>
                    <code>000/BBN.INV/IX/2025</code>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th width="30%">000</th>
                        <td>Nomor urut (3 digit)</td>
                    </tr>
                    <tr>
                        <th>BBN.INV</th>
                        <td>Kode tetap</td>
                    </tr>
                    <tr>
                        <th>IX</th>
                        <td>Bulan dalam romawi (otomatis)</td>
                    </tr>
                    <tr>
                        <th>2025</th>
                        <td>Tahun (dapat disesuaikan)</td>
                    </tr>
                </table>

                <h5>Contoh:</h5>
                <ul>
                    <li><code>001/BBN.INV/I/2025</code> - Invoice pertama bulan Januari</li>
                    <li><code>025/BBN.INV/IX/2025</code> - Invoice ke-25 bulan September</li>
                    <li><code>125/BBN.INV/XII/2025</code> - Invoice ke-125 bulan Desember</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        // Update preview nomor berikutnya saat input berubah
        $('#starting_number, #year').on('input', function() {
            updatePreview();
        });

        function updatePreview() {
            const number = $('#starting_number').val();
            const year = $('#year').val();
            const romanMonth = getRomanMonth(new Date().getMonth() + 1);
            
            if (number && year) {
                const formattedNumber = number.padStart(3, '0');
                const nextNumber = (parseInt(number) + 1).toString().padStart(3, '0');
                
                $('#next_number').val(`${nextNumber}/BBN.INV/${romanMonth}/${year}`);
            }
        }

        function getRomanMonth(month) {
            const romanNumerals = {
                1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI',
                7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X', 11: 'XI', 12: 'XII'
            };
            return romanNumerals[month] || 'I';
        }

        // Handle form submission
        $('#form-setting').submit(function(e) {
            e.preventDefault();
            
            if (!confirm('Apakah Anda yakin ingin mengubah setting nomor invoice? Tindakan ini akan mempengaruhi semua invoice baru.')) {
                return;
            }

            const formData = $(this).serialize();
            
            $.post('{{ route("service.invoice.update-setting") }}', formData, function(response) {
                if (response.success) {
                    alert('Setting nomor invoice berhasil disimpan');
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan setting: ' + response.message);
                }
            }).fail(function(error) {
                alert('Terjadi kesalahan: ' + error.responseJSON.message);
            });
        });
    });
</script>
@endpush
