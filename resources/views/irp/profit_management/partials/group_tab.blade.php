<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Manajemen Bagi Hasil Kelompok</h5>
    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createGroupModal">
        <i class="fas fa-plus"></i> Tambah Kelompok
    </button>
</div>

<div class="row">
    @if($groups->isEmpty())
        <div class="col-md-4">
            <div class="card-placeholder" data-toggle="modal" data-target="#createGroupModal">
                <i class="fas fa-plus"></i>
            </div>
        </div>
    @else
        @foreach($groups as $group)
            <div class="col-md-4">
                <div class="card group-card">
                    <div class="card-header group-card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $group->name }}</h5>
                    </div>
                    <div class="card-body group-card-body">
                        <p>{{ $group->description ?? 'Tidak ada deskripsi' }}</p>
                        
                        @if($group->product)
                            <p><strong>Produk:</strong> {{ $group->product->nama_produk }}</p>
                        @endif
                        
                        @if($group->total_quota)
                            <p><strong>Total Kuota:</strong> {{ format_uang($group->total_quota) }}</p>
                        @endif
                        
                        <p><strong>Total Investasi:</strong> {{ format_uang($group->total_investment) }}</p>
                        
                        <div class="mt-3">
                            <button class="btn btn-sm btn-info">Detail</button>
                            <button class="btn btn-sm btn-warning">Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
