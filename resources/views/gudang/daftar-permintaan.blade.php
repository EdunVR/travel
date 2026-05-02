@extends('app')

@section('title')
    Daftar Permintaan Pengiriman
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Permintaan Pengiriman</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Outlet Asal</th>
                            <th>Outlet Tujuan</th>
                            <th>Item</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permintaan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->outletAsal->nama_outlet }}</td>
                                <td>{{ $item->outletTujuan->nama_outlet }}</td>
                                <td>
                                    @if ($item->id_produk)
                                        Produk: {{ $item->produk->nama_produk }}
                                    @elseif ($item->id_bahan)
                                        Bahan: {{ $item->bahan->nama_bahan }}
                                    @elseif ($item->id_inventori)
                                        Inventori: {{ $item->inventori->nama_barang }}
                                    @endif
                                </td>
                                <td>{{ $item->jumlah }}</td>
                                <td>{{ ucfirst($item->status) }}</td>
                                <td>
                                    @if ($item->status === 'menunggu' && in_array($item->id_outlet_tujuan, Auth::user()->akses_outlet ?? []))
                                        <button class="btn btn-sm btn-success" onclick="setujuiPermintaan({{ $item->id_permintaan }})">Setujui</button>
                                        <button class="btn btn-sm btn-danger" onclick="tolakPermintaan({{ $item->id_permintaan }})">Tolak</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Sertakan CSRF token di header AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fungsi untuk menyetujui permintaan
        function setujuiPermintaan(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui permintaan ini?')) {
                $.ajax({
                    url: '{{ route('manajemen-gudang.setujui-permintaan', '') }}/' + id,
                    type: 'POST',
                    success: function(response) {
                        alert(response.message);
                        location.reload(); // Reload halaman setelah menyetujui
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                    }
                });
            }
        }

        function tolakPermintaan(id) {
                if (confirm('Apakah Anda yakin ingin menolak permintaan ini?')) {
                    $.ajax({
                        url: '{{ route('manajemen-gudang.tolak-permintaan', '') }}/' + id,
                        type: 'POST',
                        success: function(response) {
                            alert(response.message);
                            location.reload(); // Reload halaman setelah menolak
                        },
                        error: function(xhr, status, error) {
                            alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                        }
                    });
                }
            }

        // Pindahkan fungsi setujuiPermintaan ke scope global
        window.setujuiPermintaan = setujuiPermintaan;

        // Pindahkan fungsi tolakPermintaan ke scope global
        window.tolakPermintaan = tolakPermintaan;
    });
</script>
@endpush
