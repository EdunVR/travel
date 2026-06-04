<!-- Modal Create Kontra Bon -->
<div class="modal fade" id="modal-create-kontrabon" tabindex="-1" role="dialog" aria-labelledby="modal-create-kontrabon-label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="modal-create-kontrabon-label">
                    <i class="bx bx-plus-circle mr-2"></i>
                    Tambah Kontra Bon
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="form-create-kontrabon" action="<?php echo e(route('admin.penjualan.kontrabon.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="start_date_filter" id="start_date_filter">
                <input type="hidden" name="end_date_filter" id="end_date_filter">
                
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php if(count($userOutlets) > 1): ?>
                                <label for="modal_id_outlet">Outlet <span class="text-danger">*</span></label>
                                <select name="id_outlet" id="modal_id_outlet" class="form-control" required>
                                    <option value="">Pilih Outlet</option>
                                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php else: ?>
                                <input type="hidden" name="id_outlet" id="modal_id_outlet" value="<?php echo e($outlets->first()->id_outlet ?? ''); ?>">
                                <label>Outlet</label>
                                <input type="text" class="form-control" value="<?php echo e($outlets->first()->nama_outlet ?? 'No outlet'); ?>" readonly>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="modal_id_member">Customer <span class="text-danger">*</span></label>
                                <select name="id_member" id="modal_id_member" class="form-control" required>
                                    <option value="">Pilih Customer</option>
                                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($member->id_member); ?>" data-saldo="<?php echo e($member->saldo ?? 0); ?>"><?php echo e($member->nama); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="modal_saldo_member">Saldo Member</label>
                                <input type="text" id="modal_saldo_member" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label for="modal_tanggal_jatuh_tempo">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_jatuh_tempo" id="modal_tanggal_jatuh_tempo" class="form-control" required>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Range Tanggal Hutang (Opsional)</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="date" name="start_date" id="modal_start_date" class="form-control" placeholder="Dari">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" name="end_date" id="modal_end_date" class="form-control" placeholder="Sampai">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="modal_pembayaran">Pembayaran <span class="text-danger">*</span></label>
                                <input type="number" name="pembayaran" id="modal_pembayaran" class="form-control" value="0" min="0" step="1000" required placeholder="Masukkan jumlah pembayaran (0 untuk penagihan saja)">
                                <small class="form-text text-muted">
                                    <i class="bx bx-info-circle"></i> Isi 0 untuk penagihan tanpa pembayaran, atau isi jumlah pembayaran
                                </small>
                                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="modalAutoPilihHutang()">
                                    <i class="bx bx-magic-wand"></i> Auto Pilih Hutang
                                </button>
                            </div>

                            <div class="form-group">
                                <label for="modal_masuk_saldo">Masuk Saldo</label>
                                <input type="text" id="modal_masuk_saldo" class="form-control" value="Rp 0" readonly>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="modal_tambahkan_saldo">
                                    <label class="custom-control-label" for="modal_tambahkan_saldo">Tambahkan Saldo ke Pembayaran</label>
                                    <input type="hidden" name="tambahkan_saldo_value" id="modal_tambahkan_saldo_value" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Piutang -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="font-weight-bold mb-3">Daftar Piutang</h6>
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-bordered table-striped table-sm modal-table-piutang">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">Tanggal</th>
                                            <th width="20%">TrxID</th>
                                            <th width="25%">Nominal</th>
                                            <th width="10%">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="modal_check_all">
                                                    <label class="custom-control-label" for="modal_check_all">Pilih</label>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bx bx-info-circle bx-lg mb-2"></i><br>
                                                Pilih customer untuk melihat daftar piutang
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bx bx-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Buat Kontra Bon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Variabel global untuk modal
    let modalPiutangData = [];

    // Event ketika modal dibuka
    $('#modal-create-kontrabon').on('shown.bs.modal', function () {
        console.log('Modal kontra bon opened');
        
        // Set default date untuk jatuh tempo (30 hari dari sekarang)
        const today = new Date();
        const futureDate = new Date(today.getTime() + (30 * 24 * 60 * 60 * 1000));
        const formattedDate = futureDate.toISOString().split('T')[0];
        $('#modal_tanggal_jatuh_tempo').val(formattedDate);
        
        // Reset form
        resetModalForm();
    });

    // Event ketika modal ditutup
    $('#modal-create-kontrabon').on('hidden.bs.modal', function () {
        resetModalForm();
    });

    // Fungsi untuk reset form modal
    function resetModalForm() {
        $('#form-create-kontrabon')[0].reset();
        $('#modal_saldo_member').val('');
        $('#modal_masuk_saldo').val('Rp 0');
        $('#modal_tambahkan_saldo_value').val('0');
        modalPiutangData = [];
        modalClearPiutangTable();
    }

    // Event listener untuk dropdown customer di modal
    $('#modal_id_member').on('change', function() {
        let idMember = $(this).val();
        let saldoMember = $(this).find(':selected').data('saldo') || 0;
        $('#modal_saldo_member').val(saldoMember ? 'Rp ' + number_format(saldoMember, 0, ',', '.') : 'Rp 0');
        
        console.log('Modal - Member selected:', idMember, 'Saldo:', saldoMember);
        
        if (idMember) {
            modalFetchPiutang(idMember);
        } else {
            modalClearPiutangTable();
        }
    });

    // Event listener untuk checkbox tambahkan saldo di modal
    $('#modal_tambahkan_saldo').on('change', function() {
        if (this.checked) {
            $('#modal_tambahkan_saldo_value').val('1');
            let saldoMember = parseFloat($('#modal_id_member').find(':selected').data('saldo')) || 0;
            let pembayaran = parseFloat($('#modal_pembayaran').val()) || 0;
            $('#modal_pembayaran').val(pembayaran + saldoMember);
            modalAutoPilihHutang();
        } else {
            $('#modal_tambahkan_saldo_value').val('0');
            let saldoMember = parseFloat($('#modal_id_member').find(':selected').data('saldo')) || 0;
            let pembayaran = parseFloat($('#modal_pembayaran').val()) || 0;
            $('#modal_pembayaran').val(Math.max(0, pembayaran - saldoMember));
            modalAutoPilihHutang();
        }
    });

    // Event listener untuk range tanggal di modal
    $('#modal_start_date, #modal_end_date').on('change', function() {
        if ($('#modal_id_member').val()) {
            modalApplyDateFilter();
        }
    });

    // Event listener untuk checkbox select all di modal
    $('#modal_check_all').on('click', function () {
        $('input[name="piutang_ids[]"]').prop('checked', this.checked);
        console.log('Modal - Check all toggled:', this.checked);
    });

    // Event listener untuk form submit di modal
    $('#form-create-kontrabon').on('submit', function(e) {
        e.preventDefault();
        
        // Validasi minimal satu piutang dipilih
        let checkedPiutang = $('input[name="piutang_ids[]"]:checked');
        if (checkedPiutang.length === 0) {
            alert('Pilih minimal satu piutang untuk dibayar');
            return false;
        }
        
        let pembayaran = parseFloat($('#modal_pembayaran').val()) || 0;
        
        console.log('Modal - Checked piutang count:', checkedPiutang.length);
        console.log('Modal - Pembayaran:', pembayaran);
        
        $('#start_date_filter').val($('#modal_start_date').val());
        $('#end_date_filter').val($('#modal_end_date').val());
        
        // Submit form via AJAX
        let formData = new FormData(this);
        
        // PENTING: Tambahkan piutang_ids[] secara manual karena checkbox ada di luar form
        // Hapus dulu piutang_ids[] yang mungkin sudah ada
        formData.delete('piutang_ids[]');
        
        // Tambahkan setiap piutang yang dicentang
        checkedPiutang.each(function() {
            formData.append('piutang_ids[]', $(this).val());
            console.log('Modal - Adding piutang_id:', $(this).val());
        });
        
        // Debug: Log all form data
        console.log('Modal - FormData contents:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Disable submit button
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Modal form submitted successfully', response);
                
                // Close modal
                $('#modal-create-kontrabon').modal('hide');
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Kontra bon berhasil dibuat',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(response.message || 'Kontra bon berhasil dibuat');
                }
                
                // Reload DataTables
                if (typeof tablePiutang !== 'undefined') {
                    tablePiutang.ajax.reload();
                }
                if (typeof tableKontraBon !== 'undefined') {
                    tableKontraBon.ajax.reload();
                }
            },
            error: function(xhr) {
                console.error('Modal form submission error:', xhr.responseText);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                } else {
                    alert(errorMessage);
                }
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    function modalFetchPiutang(idMember) {
        console.log('Modal - Fetching piutang for member:', idMember);
        
        // Show loading
        $('.modal-table-piutang tbody').html(`
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm mr-2" role="status"></div>
                    Memuat data piutang...
                </td>
            </tr>
        `);
        
        $.ajax({
            url: `<?php echo e(route('admin.penjualan.kontrabon.get-piutang', ':id')); ?>`.replace(':id', idMember),
            method: 'GET',
            success: function(response) {
                console.log('Modal - Piutang data received:', response);
                modalPiutangData = response;
                modalUpdatePiutangTable(response);
            },
            error: function(xhr) {
                console.error('Modal - Error fetching piutang:', xhr.responseText);
                $('.modal-table-piutang tbody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger py-4">
                            <i class="bx bx-error-circle bx-lg mb-2"></i><br>
                            Gagal memuat data piutang. Silakan coba lagi.
                        </td>
                    </tr>
                `);
            }
        });
    }

    function modalUpdatePiutangTable(data) {
        let tableBody = $('.modal-table-piutang tbody');
        tableBody.empty();
        
        if (data.length === 0) {
            tableBody.append(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bx bx-info-circle bx-lg mb-2"></i><br>
                        Tidak ada piutang yang belum lunas untuk customer ini
                    </td>
                </tr>
            `);
            return;
        }

        data.forEach((item, index) => {
            let row = `<tr>
                <td class="text-center">${index + 1}</td>
                <td>${item.tanggal}</td>
                <td>${item.no_transaksi}</td>
                <td>Rp ${item.piutang}</td>
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="piutang_ids[]" value="${item.id_piutang}" id="piutang_${item.id_piutang}" data-sisa="${item.sisa_piutang}">
                        <label class="custom-control-label" for="piutang_${item.id_piutang}"></label>
                    </div>
                </td>
            </tr>`;
            tableBody.append(row);
        });
        
        console.log('Modal - Table updated with', data.length, 'records');
    }

    function modalClearPiutangTable() {
        $('.modal-table-piutang tbody').html(`
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bx bx-info-circle bx-lg mb-2"></i><br>
                    Pilih customer untuk melihat daftar piutang
                </td>
            </tr>
        `);
    }

    function modalApplyDateFilter() {
        let startDate = $('#modal_start_date').val();
        let endDate = $('#modal_end_date').val();
        
        if (!startDate || !endDate) {
            modalUpdatePiutangTable(modalPiutangData);
            return;
        }

        let filteredData = modalPiutangData.filter(item => {
            let itemDate = new Date(item.tanggal.split('-').reverse().join('-'));
            let start = new Date(startDate);
            let end = new Date(endDate);
            end.setHours(23, 59, 59, 999);
            
            return itemDate >= start && itemDate <= end;
        });

        modalUpdatePiutangTable(filteredData);
        console.log('Modal - Date filter applied:', filteredData.length, 'records');
    }

    // Fungsi untuk auto pilih hutang di modal
    window.modalAutoPilihHutang = function() {
        let pembayaran = parseFloat($('#modal_pembayaran').val()) || 0;
        let totalDipilih = 0;
        let startDate = $('#modal_start_date').val() ? new Date($('#modal_start_date').val()) : null;
        let endDate = $('#modal_end_date').val() ? new Date($('#modal_end_date').val()) : null;

        console.log('Modal - Auto pilih hutang dengan pembayaran:', pembayaran);

        // Reset semua checkbox
        $('input[name="piutang_ids[]"]').prop('checked', false);

        // Loop melalui setiap piutang
        $('input[name="piutang_ids[]"]').each(function () {
            if (totalDipilih < pembayaran) {
                let row = $(this).closest('tr');
                let sisaPiutang = parseFloat($(this).data('sisa')) || 0;
                let tanggalText = row.find('td:eq(1)').text();
                let tanggal = new Date(tanggalText.split('-').reverse().join('-'));
                
                // Filter berdasarkan range tanggal jika ada
                let dalamRange = true;
                if (startDate && endDate) {
                    dalamRange = tanggal >= startDate && tanggal <= endDate;
                }

                if (dalamRange && totalDipilih + sisaPiutang <= pembayaran) {
                    $(this).prop('checked', true);
                    totalDipilih += sisaPiutang;
                }
            }
        });

        // Hitung sisa pembayaran yang masuk ke saldo
        $('#modal_masuk_saldo').val('Rp ' + number_format(pembayaran - totalDipilih, 0, ',', '.'));
        
        console.log('Modal - Total dipilih:', totalDipilih, 'Masuk saldo:', pembayaran - totalDipilih);
    }

    // Helper function untuk format number
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
});
</script><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\kontrabon\modals\create.blade.php ENDPATH**/ ?>