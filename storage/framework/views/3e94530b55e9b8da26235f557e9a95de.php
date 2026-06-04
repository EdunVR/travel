

<?php $__env->startSection('title'); ?>
    Setting COA Invoice Penjualan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Setting COA Invoice Penjualan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Setting Chart of Account (COA) untuk Invoice Penjualan</h3>
            </div>
            <div class="box-body">
                <form id="form-coa-setting">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accounting_book_id">Buku Akuntansi *</label>
                                <select name="accounting_book_id" id="accounting_book_id" class="form-control" required>
                                    <option value="">Pilih Buku Akuntansi</option>
                                    <?php $__currentLoopData = $accountingBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($book->id); ?>" <?php echo e($setting && $setting->accounting_book_id == $book->id ? 'selected' : ''); ?>>
                                            <?php echo e($book->name); ?> - <?php echo e($book->start_date->format('d/m/Y')); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Akun-Akun yang Terlibat (Opsional)</h4>
                            <p class="text-muted">Kosongkan jika tidak ingin membuat jurnal otomatis untuk akun tertentu</p>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="25%">Keterangan</th>
                                            <th width="35%">Akun COA</th>
                                            <th width="20%">Posisi Default</th>
                                            <th width="20%">Tipe Akun</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $coaFields = [
                                                'akun_piutang_usaha' => [
                                                    'label' => 'Piutang Usaha',
                                                    'description' => 'Untuk mencatat piutang dari customer',
                                                    'position' => 'Debit',
                                                    'type' => 'asset'
                                                ],
                                                'akun_pendapatan_penjualan' => [
                                                    'label' => 'Pendapatan Penjualan',
                                                    'description' => 'Untuk mencatat pendapatan dari penjualan',
                                                    'position' => 'Kredit',
                                                    'type' => 'revenue'
                                                ],
                                                'akun_kas' => [
                                                    'label' => 'Kas',
                                                    'description' => 'Untuk pembayaran cash',
                                                    'position' => 'Debit',
                                                    'type' => 'asset'
                                                ],
                                                'akun_bank' => [
                                                    'label' => 'Bank',
                                                    'description' => 'Untuk pembayaran transfer',
                                                    'position' => 'Debit',
                                                    'type' => 'asset'
                                                ],
                                                'akun_hpp' => [
                                                    'label' => 'Harga Pokok Penjualan (HPP)',
                                                    'description' => 'Untuk mencatat biaya HPP',
                                                    'position' => 'Debit',
                                                    'type' => 'expense'
                                                ],
                                                'akun_persediaan' => [
                                                    'label' => 'Persediaan',
                                                    'description' => 'Untuk mencatat persediaan keluar',
                                                    'position' => 'Kredit',
                                                    'type' => 'asset'
                                                ]
                                            ];
                                        ?>

                                        <?php $__currentLoopData = $coaFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldName => $fieldConfig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($fieldConfig['label']); ?></strong>
                                                <br><small class="text-muted"><?php echo e($fieldConfig['description']); ?></small>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           class="form-control account-display" 
                                                           id="<?php echo e($fieldName); ?>_display"
                                                           value="<?php echo e($setting ? $getAccountDisplayValue($setting->$fieldName) : ''); ?>"
                                                           placeholder="Pilih akun..."
                                                           readonly
                                                           style="background-color: #fff; cursor: pointer;">
                                                    <input type="hidden" 
                                                           name="<?php echo e($fieldName); ?>" 
                                                           id="<?php echo e($fieldName); ?>"
                                                           value="<?php echo e($setting ? $setting->$fieldName : ''); ?>">
                                                    <span class="input-group-btn">
                                                        <button type="button" 
                                                                class="btn btn-info btn-pick-account" 
                                                                data-field="<?php echo e($fieldName); ?>"
                                                                data-type="<?php echo e($fieldConfig['type']); ?>">
                                                            <i class="fa fa-search"></i> Pilih
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-default btn-clear-account" 
                                                                data-field="<?php echo e($fieldName); ?>">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </td>
                                            <td><?php echo e($fieldConfig['position']); ?></td>
                                            <td><?php echo e($accountTypes[$fieldConfig['type']] ?? $fieldConfig['type']); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Preview Jurnal Otomatis</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status Invoice</label>
                                        <select id="preview-status" class="form-control">
                                            <option value="menunggu">Menunggu Pembayaran</option>
                                            <option value="lunas">Lunas</option>
                                            <option value="gagal">Gagal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Invoice</label>
                                        <input type="text" id="preview-total" class="form-control" value="1.000.000">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" id="btn-preview" class="btn btn-info btn-block">
                                            <i class="fa fa-eye"></i> Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="preview-result" style="display: none;">
                                <div class="alert alert-info">
                                    <strong>Keterangan:</strong> <span id="preview-description"></span>
                                    <br><strong>Total:</strong> Rp <span id="preview-total-display"></span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="15%">Kode Akun</th>
                                                <th width="45%">Nama Akun</th>
                                                <th width="20%" class="text-right">Debit</th>
                                                <th width="20%" class="text-right">Kredit</th>
                                            </tr>
                                        </thead>
                                        <tbody id="preview-entries">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                                <td class="text-right"><strong id="preview-total-debit">0</strong></td>
                                                <td class="text-right"><strong id="preview-total-credit">0</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div id="preview-warning" class="alert alert-warning" style="display: none;"></div>
                                <div id="preview-success" class="alert alert-success" style="display: none;">
                                    <i class="fa fa-check"></i> Jurnal balanced dan siap dibuat.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan Setting COA
                            </button>
                            <a href="<?php echo e(route('sales.invoice.index')); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Kembali ke Invoice
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Pilih Akun -->
<div class="modal fade" id="modal-accounts" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pilih Akun</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Filter Tipe Akun</label>
                            <select id="filter-account-type" class="form-control">
                                <option value="asset">Aset</option>
                                <option value="liability">Kewajiban</option>
                                <option value="equity">Ekuitas</option>
                                <option value="revenue">Pendapatan</option>
                                <option value="expense">Beban</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" id="search-account" class="form-control" placeholder="Cari akun...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-accounts">
                        <thead>
                            <tr>
                                <th width="20%">Kode Akun</th>
                                <th width="60%">Nama Akun</th>
                                <th width="20%">Tipe</th>
                            </tr>
                        </thead>
                        <tbody id="accounts-list">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        let currentField = '';
        let currentType = 'asset';
        let allAccounts = [];

        // Load accounts by type
        function loadAccounts(type) {
            $.get('<?php echo e(route("sales.coa.get-accounts")); ?>', { type: type }, function(response) {
                if (response.success) {
                    allAccounts = response.accounts;
                    displayAccounts(allAccounts);
                }
            });
        }

        // Display accounts in table
        function displayAccounts(accounts) {
            const tbody = $('#accounts-list');
            tbody.empty();

            if (accounts.length === 0) {
                tbody.append('<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            accounts.forEach(function(account) {
                const row = `
                    <tr style="cursor: pointer;" onclick="selectAccount('${account.code}', '${account.name.replace(/'/g, "\\'")}')">
                        <td>${account.code}</td>
                        <td>${account.name}</td>
                        <td>${getTypeLabel(account.type)}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Get type label
        function getTypeLabel(type) {
            const types = {
                'asset': 'Aset',
                'liability': 'Kewajiban',
                'equity': 'Ekuitas',
                'revenue': 'Pendapatan',
                'expense': 'Beban'
            };
            return types[type] || type;
        }

        // Global function untuk select account
        window.selectAccount = function(code, name) {
            $('#' + currentField).val(code);
            $('#' + currentField + '_display').val(code + ' - ' + name);
            $('#modal-accounts').modal('hide');
        }

        // Button pick account click
        $('.btn-pick-account').click(function() {
            currentField = $(this).data('field');
            currentType = $(this).data('type');
            
            // Set filter sesuai type
            $('#filter-account-type').val(currentType);
            
            // Load accounts
            loadAccounts(currentType);
            
            $('#modal-accounts').modal('show');
        });

        // Clear account
        $('.btn-clear-account').click(function() {
            const field = $(this).data('field');
            $('#' + field).val('');
            $('#' + field + '_display').val('');
        });

        // Filter account type change
        $('#filter-account-type').change(function() {
            const type = $(this).val();
            loadAccounts(type);
        });

        // Search accounts
        $('#search-account').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const filteredAccounts = allAccounts.filter(function(account) {
                return account.code.toLowerCase().includes(searchTerm) || 
                       account.name.toLowerCase().includes(searchTerm);
            });
            displayAccounts(filteredAccounts);
        });

        // Preview journal
        $('#btn-preview').click(function() {
            const status = $('#preview-status').val();
            let total = $('#preview-total').val().replace(/\./g, '');
            
            if (!total || isNaN(total)) {
                alert('Masukkan total yang valid');
                return;
            }

            total = parseInt(total);

            $.get('<?php echo e(route("sales.coa.setting.preview")); ?>', {
                status: status,
                total: total
            }, function(response) {
                console.log('Preview response:', response);
                if (response.success) {
                    showPreview(response.preview);
                } else {
                    alert('Gagal memuat preview: ' + response.message);
                }
            }).fail(function(xhr) {
                console.error('Preview error:', xhr);
                alert('Terjadi kesalahan saat memuat preview: ' + xhr.responseJSON?.message || xhr.statusText);
            });
        });

        function showPreview(preview) {
            console.log('Showing preview:', preview);
            
            $('#preview-description').text(preview.description);
            $('#preview-total-display').text(formatNumber(preview.total));
            $('#preview-entries').empty();
            
            let totalDebit = 0;
            let totalCredit = 0;
            let hasEntries = false;

            if (preview.entries && preview.entries.length > 0) {
                preview.entries.forEach(function(entry) {
                    if (entry.account_code && entry.account_name) {
                        hasEntries = true;
                        const debit = entry.debit || 0;
                        const credit = entry.credit || 0;
                        
                        $('#preview-entries').append(
                            '<tr>' +
                            '<td>' + entry.account_code + '</td>' +
                            '<td>' + entry.account_name + '</td>' +
                            '<td class="text-right">' + (debit > 0 ? 'Rp ' + formatNumber(debit) : '-') + '</td>' +
                            '<td class="text-right">' + (credit > 0 ? 'Rp ' + formatNumber(credit) : '-') + '</td>' +
                            '</tr>'
                        );
                        
                        totalDebit += parseFloat(debit);
                        totalCredit += parseFloat(credit);
                    }
                });
            }

            if (hasEntries) {
                $('#preview-total-debit').text('Rp ' + formatNumber(totalDebit));
                $('#preview-total-credit').text('Rp ' + formatNumber(totalCredit));
                $('#preview-result').show();
                
                // Show warning/success based on balance
                if (totalDebit === totalCredit) {
                    $('#preview-success').show();
                    $('#preview-warning').hide();
                } else {
                    $('#preview-success').hide();
                    $('#preview-warning').show().html(
                        '<i class="fa fa-warning"></i> Jurnal tidak balance! Debit: Rp ' + formatNumber(totalDebit) + ' ≠ Kredit: Rp ' + formatNumber(totalCredit)
                    );
                }
            } else {
                $('#preview-result').show();
                $('#preview-entries').html('<tr><td colspan="4" class="text-center">Tidak ada jurnal yang akan dibuat</td></tr>');
                $('#preview-total-debit').text('Rp 0');
                $('#preview-total-credit').text('Rp 0');
                $('#preview-success').hide();
                $('#preview-warning').show().html(
                    '<i class="fa fa-info-circle"></i> Tidak ada jurnal yang akan dibuat karena akun yang diperlukan belum diatur.'
                );
            }
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Format total input
        $('#preview-total').on('blur', function() {
            let value = $(this).val().replace(/\./g, '');
            if (!isNaN(value) && value !== '') {
                $(this).val(formatNumber(parseInt(value)));
            }
        });

        // Handle form submission
        $('#form-coa-setting').submit(function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            console.log('Form data:', formData);
            
            $.post('<?php echo e(route("sales.coa.setting.update")); ?>', formData)
                .done(function(response) {
                    if (response.success) {
                        alert('Setting COA berhasil disimpan');
                        // Refresh preview if open
                        if ($('#preview-result').is(':visible')) {
                            $('#btn-preview').click();
                        }
                    } else {
                        alert('Gagal menyimpan setting: ' + response.message);
                    }
                })
                .fail(function(xhr) {
                    console.error('Save error:', xhr);
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMessage = 'Error Validasi:\n';
                        for (let field in xhr.responseJSON.errors) {
                            errorMessage += '• ' + xhr.responseJSON.errors[field][0] + '\n';
                        }
                        alert(errorMessage);
                    } else {
                        alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText));
                    }
                });
        });

        // Load initial accounts
        loadAccounts('asset');
        
        // Load initial preview if setting exists
        <?php if($setting): ?>
        setTimeout(function() {
            $('#btn-preview').click();
        }, 500);
        <?php endif; ?>
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\sales_management\invoice\coa_setting.blade.php ENDPATH**/ ?>