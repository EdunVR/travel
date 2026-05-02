<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i data-feather="info"></i> Total HPP Paket: <strong id="total-hpp-value">Rp {{ number_format($totalHpp, 0, ',', '.') }}</strong>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Daftar RAB</h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addRabComponent()">
                <i data-feather="plus"></i> Tambah RAB
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Nama Template</th>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Biaya per Orang</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->rabs as $index => $rab)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $rab->nama_template }}</td>
                        <td>{{ $rab->deskripsi }}</td>
                        <td class="text-right">Rp {{ number_format($rab->pivot->subtotal, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRab({{ $rab->id_rab }})">
                                <i data-feather="trash-2"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada RAB yang ditambahkan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function addRabComponent() {
    $('#modal-rab').modal('show');
    $('#modal-rab .modal-title').text('Tambah RAB');
    $('#modal-rab form')[0].reset();
    $('#modal-rab [name=_method]').val('POST');
    $('#modal-rab [name=product_id]').val('{{ $product->id_produk }}');
}

function removeRab(rabId) {
    if (confirm('Yakin ingin menghapus RAB ini?')) {
        $.post(`/produk/{{ $product->id_produk }}/remove-rab`, {
            _token: '{{ csrf_token() }}',
            id_rab: rabId
        }, function(response) {
            if (response.success) {
                $('#rab-container').load(`/produk/{{ $product->id_produk }}/rabs`);
            }
        });
    }
}
</script>
