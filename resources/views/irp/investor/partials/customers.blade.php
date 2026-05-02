<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">
    <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#addCustomerModal">
        <i data-feather="plus" class="icon-sm"></i> Tambah Customer
    </button>
    <div class="alert alert-info mb-2 ms-auto">
        <strong>Status:</strong> 
        {{ $investor->customers->where('status', 'paid')->count() }}/{{ $investor->kuota }} kursi terisi
        @if($investor->customers->count() >= $investor->kuota)
            <span class="badge badge-danger ml-2">Kuota Penuh</span>
        @else
            <span class="badge badge-success ml-2">Tersedia</span>
        @endif
    </div>
</div>


<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Customer</th>
                <th>Telepon</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Tanggal Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investor->customers as $customer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $customer->member->nama }}</td>
                <td>{{ $customer->member->telepon }}</td>
                <td class="text-right">{{ format_uang($customer->biaya) }}</td>
                <td>
                    <span class="badge badge-{{ $customer->status == 'paid' ? 'success' : 'warning' }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </td>
                <td>{{ $customer->payment_date ? tanggal_indonesia($customer->payment_date) : '-' }}</td>
                <td>
                    @if($customer->status != 'paid')
                    <form action="{{ route('irp.investor.customer.verify', [$investor->id, $customer->id]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Verifikasi pembayaran?')">
                            <i data-feather="check" class="icon-sm"></i>
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('irp.investor.customer.destroy', [$investor->id, $customer->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus customer ini?')">
                            <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah Customer -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('irp.investor.customer.store', $investor->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Customer</label>
                        <select name="id_member" class="form-control" required>
                            <option value="">Pilih Customer</option>
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->id_member }}">{{ $member->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Biaya</label>
                        <input type="number" name="biaya" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i data-feather="x" class="icon-sm"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="icon-sm"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inisialisasi Feather Icons di modal setelah dibuka
    $('#addCustomerModal').on('shown.bs.modal', function () {
        feather.replace();
    });
</script>
@endpush
