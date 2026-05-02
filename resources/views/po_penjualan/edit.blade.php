@extends('app')

@section('title')
    Update Status PO Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('po-penjualan.index') }}">PO Penjualan</a></li>
    <li class="active">Update Status</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Update Status PO Penjualan</h3>
            </div>
            <div class="box-body">
                <!-- Informasi PO -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Informasi PO</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <td width="40%"><strong>No. PO</strong></td>
                                <td>{{ $poPenjualan->no_po }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>{{ tanggal_indonesia($poPenjualan->tanggal, false) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Customer</strong></td>
                                <td>{{ $poPenjualan->member->nama ?? 'Customer Umum' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Bayar</strong></td>
                                <td>{{ format_uang($poPenjualan->bayar) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Diterima</strong></td>
                                <td>{{ format_uang($poPenjualan->diterima) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Saat Ini</strong></td>
                                <td>
                                    @if($poPenjualan->status == 'menunggu')
                                        <span class="label label-warning">Menunggu</span>
                                    @elseif($poPenjualan->status == 'lunas')
                                        <span class="label label-success">Lunas</span>
                                    @else
                                        <span class="label label-danger">Gagal</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Form Update Status -->
                <form id="statusForm" action="{{ route('po-penjualan.update-status', $poPenjualan->id_po_penjualan) }}" method="POST">
                    @csrf
                    @method('POST')
                    
                    <div class="form-group">
                        <label for="status">Status Baru *</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="menunggu" {{ $poPenjualan->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="lunas" {{ $poPenjualan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="gagal" {{ $poPenjualan->status == 'gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>

                    <div class="form-group" id="diterimaField" style="display: none;">
                        <label for="diterima">Jumlah Diterima</label>
                        <input type="number" name="diterima" id="diterima" class="form-control" 
                               value="{{ $poPenjualan->diterima }}" min="0" step="0.01">
                        <small class="text-muted">Kosongkan jika tidak ada perubahan</small>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" 
                                  placeholder="Alasan perubahan status..."></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Status
                        </button>
                        <a href="{{ route('po-penjualan.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Items -->
    <div class="col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Items</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($poPenjualan->details as $detail)
                            <tr>
                                <td>
                                    @if($detail->tipe_item == 'ongkir')
                                        <strong>ONGKIR</strong><br>
                                        <small>{{ $detail->keterangan }}</small>
                                    @else
                                        <strong>{{ $detail->produk->kode_produk ?? '' }}</strong><br>
                                        <small>{{ $detail->produk->nama_produk ?? 'Produk' }}</small>
                                    @endif
                                </td>
                                <td class="text-right">{{ format_uang($detail->harga_jual) }}</td>
                                <td class="text-center">{{ $detail->jumlah }}</td>
                                <td class="text-right">{{ format_uang($detail->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td class="text-right"><strong>{{ format_uang($poPenjualan->bayar) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tampilkan field diterima saat status lunas
    $('#status').change(function() {
        if ($(this).val() === 'lunas') {
            $('#diterimaField').show();
        } else {
            $('#diterimaField').hide();
        }
    });

    // Trigger change saat load
    $('#status').trigger('change');

    // Form submission
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route('po-penjualan.index') }}';
                    });
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalText);
                
                let message = 'Terjadi kesalahan saat update status';
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire('Error!', message, 'error');
            }
        });
    });
});
</script>
@endpush
