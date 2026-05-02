<div class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#uploadDocumentModal">
                <i class="fas fa-upload"></i> Upload Dokumen
            </button>
            <button class="btn btn-success" data-toggle="modal" data-target="#createDocumentModal">
                <i class="fas fa-file-alt"></i> Buat Dokumen Baru
            </button>
        </div>
        <div class="col-md-6">
            <div class="float-right">
                <select class="form-control form-control-sm" id="documentTypeFilter">
                    <option value="">Semua Jenis</option>
                    <option value="KTP">KTP</option>
                    <option value="NPWP">NPWP</option>
                    <option value="AKAD">Akad Perjanjian</option>
                    <option value="KONTRAK">Perpanjangan Kontrak</option>
                    <option value="LAINNYA">Lainnya</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="documentsTable">
        <thead>
            <tr>
                <th>Judul Dokumen</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>File</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $document)
            <tr data-type="{{ $document->type }}">
                <td>{{ $document->title }}</td>
                <td>
                    @if($document->type == 'AKAD')
                        <span class="badge badge-primary">Akad Perjanjian</span>
                    @elseif($document->type == 'KONTRAK')
                        <span class="badge badge-info">Perpanjangan Kontrak</span>
                    @else
                        <span class="badge badge-secondary">{{ $document->type }}</span>
                    @endif
                </td>
                <td>{{ $document->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('irp.investor.document.show', [$investor->id, $document->id]) }}" 
                       target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning edit-document" 
                            data-id="{{ $document->id }}"
                            data-toggle="modal" 
                            data-target="#editDocumentModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-document" 
                            data-id="{{ $document->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('irp.investor.partials.document_modals')

@push('scripts')
<script>
    $(document).ready(function() {
        // Filter dokumen
        $('#documentTypeFilter').change(function() {
            const type = $(this).val();
            if (type) {
                $('#documentsTable tbody tr').hide();
                $(`#documentsTable tbody tr[data-type="${type}"]`).show();
            } else {
                $('#documentsTable tbody tr').show();
            }
        });

        // Handle upload dokumen
        $('#uploadDocumentForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(form[0]);
            
            Swal.fire({
                title: 'Mengupload Dokumen',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('irp.investor.document.store', $investor->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        });

        // Handle buat dokumen baru
        $('#createDocumentForm').submit(function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Membuat Dokumen',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('irp.investor.document.create.custom', $investor->id) }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    }).then(() => {
                        window.open(response.document_url, '_blank');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        });

        // Load template dokumen
        $('#documentTemplate').change(function() {
            const template = $(this).val();
            if (template === 'akad_template') {
                $('textarea[name="content"]').val(`Pihak-pihak:\n\n1. Nama: {{ $investor->name }}\n   Alamat: {{ $investor->address }}\n\n2. Nama: [Nama Perusahaan]\n   Alamat: [Alamat Perusahaan]\n\nDengan ini menyatakan sepakat untuk melakukan kerjasama investasi dengan ketentuan sebagai berikut:\n\n1. Jumlah Investasi: [Jumlah]\n2. Jangka Waktu: [Waktu]\n3. Bagi Hasil: [Persentase]%\n\nDan ketentuan-ketentuan lainnya sebagaimana diatur dalam perjanjian ini.`);
            } else if (template === 'kontrak_template') {
                $('textarea[name="content"]').val(`Perjanjian Perpanjangan Kontrak\n\nDengan ini kedua belah pihak sepakat untuk memperpanjang kontrak investasi yang telah dibuat sebelumnya pada tanggal [Tanggal Awal Kontrak] dengan ketentuan:\n\n1. Masa perpanjangan: [Jangka Waktu]\n2. Syarat dan ketentuan lainnya mengikuti perjanjian awal.\n\nDemikian perjanjian ini dibuat untuk dipatuhi bersama.`);
            }
        });
    });
</script>
@endpush
