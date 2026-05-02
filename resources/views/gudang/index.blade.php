<style>
    .badge {
        margin-left: 5px;
        padding: 5px 10px;
        border-radius: 50%;
        background-color: #dc3545; /* Warna merah */
        color: white;
    }

</style>
@extends('app')

@section('title')
    Transfer Gudang
@endsection

@push('css')
    <style>
        .outlet-container {
            display: flex;
            justify-content: space-between;
        }
        .outlet-column {
            width: 48%;
        }
        .item-list {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .item-list h4 {
            margin-top: 0;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
            <a href="{{ route('manajemen-gudang.daftar-permintaan') }}" class="btn btn-info">
                Lihat Daftar Permintaan 
                <span class="badge bg-danger">{{ \App\Models\PermintaanPengiriman::where('status', 'menunggu')->count() }}</span>
            </a>
            </div>
            <div class="box-body">
                <div class="outlet-container">
                    <!-- Kolom Kanan: Outlet Tujuan -->
                    <div class="outlet-column">
                        <h4>Outlet Pengirim</h4>
                        <select id="outlet-asal" class="form-control">
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                        <div id="item-list-asal" class="item-list">
                            <!-- Daftar produk, bahan, atau inventori dari outlet asal -->
                        </div>
                    </div>
                    
                    <!-- Kolom Kiri: Outlet Asal -->
                    <div class="outlet-column">
                        <h4>Outlet Penerima</h4>
                        <select id="outlet-tujuan" class="form-control">
                            @foreach ($outlet_all as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                        <div id="item-list-tujuan" class="item-list">
                            <!-- Daftar produk, bahan, atau inventori dari outlet tujuan -->
                        </div>
                    </div>

                    
                </div>

            </div>
        </div>
    </div>
    
</div>

@includeif('gudang.jumlah')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
            window.setujuiPermintaan = setujuiPermintaan;
            window.tolakPermintaan = tolakPermintaan;
            // Fungsi untuk memuat daftar item berdasarkan outlet
            function loadItems(outletId, targetElement, target) {
                $.ajax({
                    url: '{{ route('manajemen-gudang.get-items') }}',
                    type: 'GET',
                    data: {
                        id_outlet: outletId,
                        target: target
                    },
                    success: function(response) {
                        $(targetElement).html(response);
                    },
                    error: function(xhr, status, error) {
                        console.log("Gagal memuat data:", error);
                    }
                });
            }

            $('#outlet-asal').on('change', function() {
                const outletId = $(this).val();
                loadItems(outletId, '#item-list-asal', 'asal');

                $('#outlet-tujuan option').prop('disabled', false); // Reset semua opsi
                $('#outlet-tujuan option[value="' + outletId + '"]').prop('disabled', true);
            });

            $('#outlet-tujuan').on('change', function() {
                const outletId = $(this).val();
                loadItems(outletId, '#item-list-tujuan', 'tujuan');
            });

            loadItems($('#outlet-asal').val(), '#item-list-asal', 'asal');
            loadItems($('#outlet-tujuan').val(), '#item-list-tujuan', 'tujuan');

            function pilihItem(jenis, id, nama) {
                // Tampilkan modal untuk mengisi jumlah
                $('#modal-jumlah').modal('show');
                $('#modal-jumlah').data('jenis', jenis);
                $('#modal-jumlah').data('id', id);
                $('#modal-jumlah').data('nama', nama);
                $('#modal-jumlah').find('.modal-title').text(`Pilih Jumlah untuk ${nama}`);
            }

            $('#btn-submit-jumlah').on('click', function() {
                const jenis = $('#modal-jumlah').data('jenis');
                const id = $('#modal-jumlah').data('id');
                const jumlah = $('#jumlah').val();

                if (!jumlah || jumlah < 1) {
                    alert('Jumlah harus diisi dan minimal 1.');
                    return;
                }

                const data = {
                    id_outlet_asal: $('#outlet-asal').val(),
                    id_outlet_tujuan: $('#outlet-tujuan').val(),
                    jumlah: jumlah,
                };

                if (jenis === 'produk') {
                    data.id_produk = id;
                } else if (jenis === 'bahan') {
                    data.id_bahan = id;
                } else if (jenis === 'inventori') {
                    data.id_inventori = id;
                }

                // Kirim permintaan pengiriman
                $.ajax({
                    url: '{{ route('manajemen-gudang.buat-permintaan') }}',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        alert(response.message);
                        $('#modal-jumlah').modal('hide');
                        const outletId = $('#outlet-tujuan').val();
                        loadItems(outletId, '#item-list-tujuan', 'tujuan');
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                    }
                });
            });
        });

        function pilihItem(jenis, id, nama) {
            // Tampilkan modal untuk mengisi jumlah
            $('#modal-jumlah').modal('show');
            $('#modal-jumlah').data('jenis', jenis);
            $('#modal-jumlah').data('id', id);
            $('#modal-jumlah').find('.modal-title').text(`Pilih Jumlah untuk ${nama}`);
        }
    </script>
@endpush
