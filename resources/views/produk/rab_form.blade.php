<div class="modal fade" id="modal-rab" tabindex="-1" role="dialog" aria-labelledby="modal-rab">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Buat Template RAB</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="nama_template" class="col-md-2 control-label">Nama Template</label>
                    <div class="col-md-10">
                        <input type="text" id="nama_template" class="form-control">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="deskripsi_rab" class="col-md-2 control-label">Deskripsi</label>
                    <div class="col-md-10">
                        <textarea id="deskripsi_rab" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary" onclick="addRabItem()">
                            <i data-feather="plus"></i> Tambah Item
                        </button>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table table-bordered" id="rab-item-table">
                            <thead>
                                <tr>
                                    <th width="30%">Nama Komponen</th>
                                    <th width="40%">Deskripsi</th>
                                    <th width="20%">Biaya</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Item RAB akan ditambahkan di sini -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total Biaya per Orang:</th>
                                    <th id="total-rab">0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveRabTemplate()">
                    <i data-feather="save"></i> Simpan
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i data-feather="x"></i> Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function addRabItem() {
        const row = `
        <tr>
            <td>
                <input type="text" class="form-control rab-item-name" required>
            </td>
            <td>
                <textarea class="form-control rab-item-desc" rows="1"></textarea>
            </td>
            <td>
                <input type="number" class="form-control rab-item-cost" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove(); calculateRabTotal();">
                    <i data-feather="trash-2"></i>
                </button>
            </td>
        </tr>`;
        
        $('#rab-item-table tbody').append(row);
        feather.replace();
    }
    
    function calculateRabTotal() {
        let total = 0;
        $('.rab-item-cost').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total-rab').text(total.toLocaleString());
    }
    
    function saveRabTemplate() {
        const namaTemplate = $('#nama_template').val();
        const deskripsi = $('#deskripsi_rab').val();
        
        if (!namaTemplate) {
            alert('Nama template harus diisi');
            return;
        }
        
        const items = [];
        $('#rab-item-table tbody tr').each(function() {
            const name = $(this).find('.rab-item-name').val();
            const cost = $(this).find('.rab-item-cost').val();
            
            if (name && cost) {
                items.push({
                    nama_komponen: name,
                    deskripsi: $(this).find('.rab-item-desc').val(),
                    biaya: cost
                });
            }
        });
        
        if (items.length === 0) {
            alert('Minimal harus ada satu item RAB');
            return;
        }
        
        // Simpan ke database via AJAX
        $.post('{{ route("rab_template.store") }}', {
            _token: '{{ csrf_token() }}',
            nama_template: namaTemplate,
            deskripsi: deskripsi,
            items: items
        }, function(response) {
            if (response.success) {
                // Tambahkan ke dropdown template
                const newOption = new Option(response.data.nama_template, response.data.id_rab, false, false);
                $('#keberangkatan_template_id').append(newOption).val(response.data.id_rab);
                
                // Tutup modal
                $('#modal-rab').modal('hide');
                
                // Reset form
                $('#nama_template, #deskripsi_rab').val('');
                $('#rab-item-table tbody').empty();
                $('#total-rab').text('0');
            }
        }).fail(function(error) {
            alert('Gagal menyimpan template RAB');
            console.error(error);
        });
    }
    
    $(document).on('keyup', '.rab-item-cost', function() {
        calculateRabTotal();
    });
</script>
