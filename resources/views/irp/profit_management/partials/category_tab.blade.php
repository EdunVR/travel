<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Daftar Pembagian Keuntungan Berdasarkan Kategori</h5>
    <a href="{{ route('irp.profit-management.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> Tambah Pembagian
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="profitTable">
        <thead>
            <tr>
                <th>Periode</th>
                <th>Total Keuntungan</th>
                <th>Tanggal Pembagian</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Bukti Transfer</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profits as $profit)
            <tr>
                <td>{{ $profit->period }}</td>
                <td class="text-right">{{ format_uang($profit->total_profit) }}</td>
                <td>{{ $profit->distribution_date->format('d/m/Y') }}</td>
                <td>
                    {{ $profit->category ? ucfirst($profit->category) : 'Semua Kategori' }}
                </td>
                <td>
                    @if($profit->status == 'paid')
                        <span class="badge badge-success">Sudah Dibayar</span>
                    @elseif($profit->status == 'processed')
                        <span class="badge badge-warning">Diproses</span>
                    @else
                        <span class="badge badge-secondary">Draft</span>
                    @endif
                </td>
                <td>
                    @if($profit->proof_file)
                        <a href="{{ asset('storage/'.$profit->proof_file) }}" target="_blank">
                            <i class="fas fa-file-pdf"></i> Lihat
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="{{ route('irp.profit-management.show', $profit->id) }}" 
                    class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
