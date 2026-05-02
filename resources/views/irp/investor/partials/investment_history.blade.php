<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @if($investor->investments->count() > 0)
                @foreach($investor->investments as $investment)
                <tr>
                    <td>{{ tanggal_indonesia($investment->date) }}</td>
                    <td>{{ ucfirst($investment->type) }}</td>
                    <td class="text-right">{{ format_uang($investment->amount) }}</td>
                    <td>{{ $investment->notes ?? '-' }}</td>
                    <td>
                        @if($investment->document)
                            <a href="{{ asset('storage/'.$investment->document) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-download"></i>
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">Belum ada riwayat investasi</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
