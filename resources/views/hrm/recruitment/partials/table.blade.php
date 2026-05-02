<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th style="width: 15%;">Nama</th>
                <th style="width: 15%;">Posisi</th>
                <th style="width: 15%;">Department</th>
                <th style="width: 20%;">Jobdesk</th>
                <th style="width: 10%;">Fingerprint ID</th>
                <th style="width: 10%;">Status Sidik Jari</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recruitments as $recruitment)
            <tr>
                <td>{{ $recruitment->name }}</td>
                <td>{{ $recruitment->position }}</td>
                <td>{{ $recruitment->department }}</td>
                <td>
                    <ul>
                        @if($recruitment->jobdesk)
                            @foreach(json_decode($recruitment->jobdesk) as $job)
                                <li>{{ $job }}</li>
                            @endforeach
                        @else
                            <li>Tidak ada jobdesk.</li>
                        @endif
                    </ul>
                </td>
                <td>{{ $recruitment->fingerprint_id ?? 'Belum terdaftar' }}</td>
                <td>{{ $recruitment->is_registered_fingerprint ? 'Terdaftar' : 'Belum terdaftar' }}</td>
                <td>
                    <span class="badge 
                        @if($recruitment->status == 'menunggu') badge-warning
                        @elseif($recruitment->status == 'diterima') badge-success
                        @else badge-danger
                        @endif">
                        {{ ucfirst($recruitment->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('hrm.recruitment.edit', $recruitment->id) }}" class="btn btn-icon btn-warning" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('hrm.recruitment.destroy', $recruitment->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                    @if($recruitment->status == 'diterima')
                        <button class="btn btn-icon btn-info mt-1" title="Cetak Kontrak" onclick="openPrintContractModal({{ $recruitment->id }})">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
