@if($images->isEmpty())
    <div class="col-md-12 text-center">
        <i data-feather="image"></i> Belum ada gambar untuk produk ini
    </div>
@else
    @foreach($images as $image)
    <div class="col-md-3 mb-3">
        <div class="card">
            <img src="{{ asset('storage/'.$image->path) }}" class="card-img-top" alt="Product Image" style="height: 120px; object-fit: cover;">
            <div class="card-body text-center">
                @if($image->is_primary)
                    <span class="badge badge-success mb-2">Utama</span>
                @else
                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="setPrimaryImage({{ $image->id_image }})">
                        <i data-feather="star"></i> Jadikan Utama
                    </button>
                @endif
                <button class="btn btn-sm btn-outline-danger" onclick="deleteImage({{ $image->id_image }})">
                    <i data-feather="trash-2"></i> Hapus
                </button>
            </div>
        </div>
    </div>
    @endforeach
@endif
