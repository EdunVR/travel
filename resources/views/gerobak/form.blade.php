<div class="modal fade" id="modal-form-gerobak" tabindex="-1" role="dialog" aria-labelledby="modal-form-gerobak">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form method="post" class="form-horizontal">
                    @csrf
                    @method('post')
                    
                    <div class="form-group">
                        <label for="nama_gerobak" class="col-lg-4 control-label">Nama Gerobak</label>
                        <div class="col-lg-8">
                            <input type="text" name="nama_gerobak" id="nama_gerobak" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_outletz" class="col-lg-4 control-label">Outlet</label>
                        <div class="col-lg-8">
                            <select name="id_outletz" id="id_outletz" class="form-control" required>
                                <option value="">Pilih Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status" class="col-lg-4 control-label">Status</label>
                        <div class="col-lg-8">
                            <select name="status" id="status" class="form-control" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Function untuk handle form submission
function handleGerobakFormSubmit(e) {
    e.preventDefault();
    
    const form = this;
    const url = form.getAttribute('action');
    const method = form.querySelector('[name="_method"]')?.value || 'POST';
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
    submitBtn.disabled = true;
    
    // Dapatkan nilai form secara manual (bukan menggunakan FormData)
    const formData = new URLSearchParams();
    
    // Tambahkan field form secara manual
    formData.append('nama_gerobak', $('#nama_gerobak').val());
    formData.append('id_outlet', $('#id_outletz').val());
    formData.append('status', $('#status').val());

    
    // Untuk method PUT, tambahkan _method field
    if (method.toUpperCase() === 'PUT') {
        formData.append('_method', 'PUT');
    }
    
    // Tambahkan CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) {
        formData.append('_token', csrfToken);
    }
    
    $.ajax({
        url: url,
        type: 'POST', // Selalu gunakan POST, method sebenarnya diatur via _method
        data: formData.toString(),
        processData: false,
        contentType: 'application/x-www-form-urlencoded',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (response.success) {
                // Tutup modal
                $('#modal-form-gerobak').modal('hide');
                
                // Show success message
                alert(response.message);
                
                // Refresh tabel gerobak
                if (typeof tableGerobak !== 'undefined') {
                    tableGerobak.ajax.reload(null, false);
                }
            }
        },
        error: function(xhr) {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (xhr.status === 419) {
                // CSRF token mismatch - reload page untuk mendapatkan token baru
                alert('Session expired. Please refresh the page and try again.');
                window.location.reload();
            } else if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                for (const field in errors) {
                    errorMsg += `${errors[field][0]}\n`;
                }
                alert('Validasi Gagal: ' + errorMsg);
            } else {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                console.error('Ajax error:', xhr);
            }
        }
    });
}

// Attach event handler ke form
$(document).ready(function() {
    $('#modal-form-gerobak form').off('submit').on('submit', handleGerobakFormSubmit);
});

// Handle modal hidden event untuk reset form
$('#modal-form-gerobak').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    // Reset method ke POST
    $(this).find('[name="_method"]').val('post');
});
</script>
