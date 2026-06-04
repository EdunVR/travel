<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Setting COA Payroll']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Setting COA Payroll']); ?>
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Setting COA Payroll</h1>
                <p class="text-sm text-slate-600 mt-1">Konfigurasi akun untuk jurnal payroll otomatis</p>
            </div>
            <a href="<?php echo e(route('sdm.payroll.index')); ?>" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 flex items-center gap-2">
                <i class='bx bx-arrow-back'></i>
                <span>Kembali</span>
            </a>
        </div>

        
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class='bx bx-info-circle text-2xl text-blue-600'></i>
                <div>
                    <h3 class="font-semibold text-blue-900">Informasi Jurnal Otomatis</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Saat payroll di-approve, sistem akan membuat jurnal untuk mencatat beban gaji dan hutang gaji.
                        Saat payroll dibayar, sistem akan membuat jurnal untuk mencatat pembayaran dari kas/bank.
                        <br><strong>Catatan:</strong> Hanya akun tanpa sub-akun yang akan ditampilkan untuk mencegah kesalahan jurnal.
                    </p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-card p-6 border border-slate-200">
            <form id="coaSettingForm">
                <div class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
                        <select id="outlet_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required onchange="loadAccountsAndSettings()">
                            <option value="">Pilih Outlet</option>
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Pilih outlet untuk memuat akun yang sesuai</p>
                    </div>

                    <div id="accountsSection" style="display: none;">
                        <hr>

                        
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4">Akun Beban (Expense) - Debit saat Approve</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Beban Gaji Pokok <span class="text-red-500">*</span></label>
                                    <select id="salary_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                        <option value="">Pilih Akun</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Beban Lembur</label>
                                    <select id="overtime_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                        <option value="">Pilih Akun</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Beban Bonus</label>
                                    <select id="bonus_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                        <option value="">Pilih Akun</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Beban Tunjangan</label>
                                    <select id="allowance_expense_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                        <option value="">Pilih Akun</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4">Akun Hutang (Liability) - Credit saat Approve</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Hutang Gaji <span class="text-red-500">*</span></label>
                                    <select id="salary_payable_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                        <option value="">Pilih Akun</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Hutang Pajak <span class="text-red-500">*</span></label>
                                    <select id="tax_payable_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                        <option value="">Pilih Akun</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4">Akun Aset (Asset)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Piutang Pinjaman Karyawan</label>
                                    <select id="loan_receivable_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                        <option value="">Pilih Akun</option>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Debit saat approve (potongan pinjaman)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Kas/Bank <span class="text-red-500">*</span></label>
                                    <select id="cash_account_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                        <option value="">Pilih Akun</option>
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Credit saat pay (pembayaran gaji)</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        
                        <div class="flex justify-end gap-3">
                            <a href="<?php echo e(route('sdm.payroll.index')); ?>" class="px-6 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                Simpan Setting
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        
        <div id="loadingIndicator" class="bg-white rounded-xl shadow-card p-6 border border-slate-200 text-center" style="display: none;">
            <i class='bx bx-loader-alt bx-spin text-3xl text-primary-600'></i>
            <p class="mt-2 text-slate-600">Memuat akun...</p>
        </div>

        
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Alur Jurnal Otomatis</h3>
            
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-lg border border-slate-200">
                    <h4 class="font-semibold text-slate-900 mb-2">1. Saat Approve Payroll:</h4>
                    <div class="text-sm text-slate-700 space-y-1">
                        <p><strong>Debit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Beban Gaji Pokok</li>
                            <li>Beban Lembur (jika ada)</li>
                            <li>Beban Bonus (jika ada)</li>
                            <li>Beban Tunjangan (jika ada)</li>
                            <li>Piutang Pinjaman Karyawan (jika ada potongan pinjaman)</li>
                        </ul>
                        <p class="mt-2"><strong>Credit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Hutang Pajak (jika ada)</li>
                            <li>Hutang Gaji (net salary)</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200">
                    <h4 class="font-semibold text-slate-900 mb-2">2. Saat Pay Payroll:</h4>
                    <div class="text-sm text-slate-700 space-y-1">
                        <p><strong>Debit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Hutang Gaji</li>
                        </ul>
                        <p class="mt-2"><strong>Credit:</strong></p>
                        <ul class="list-disc list-inside ml-4">
                            <li>Kas/Bank</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAccounts = {
            expense: [],
            liability: [],
            asset: []
        };

        $(document).ready(function() {
            // Auto-load settings if only one outlet
            <?php if(count($outlets) === 1): ?>
                $('#outlet_id').val('<?php echo e($outlets[0]->id_outlet); ?>');
                loadAccountsAndSettings();
            <?php endif; ?>
            
            // Load settings if outlet is already selected (from URL or previous selection)
            const selectedOutlet = $('#outlet_id').val();
            if (selectedOutlet) {
                loadAccountsAndSettings();
            }
        });

        async function loadAccountsAndSettings() {
            const outletId = $('#outlet_id').val();
            console.log('Loading accounts and settings for outlet:', outletId);
            
            if (!outletId) {
                console.log('No outlet selected, hiding accounts section');
                $('#accountsSection').hide();
                clearAllFields();
                return;
            }

            // Show loading indicator
            $('#loadingIndicator').show();
            $('#accountsSection').hide();

            try {
                // Load accounts first
                await loadAccounts(outletId);
                
                // Then load existing settings
                await loadSettings(outletId);
                
                // Show accounts section
                $('#accountsSection').show();
                
            } catch (error) {
                console.error('Error loading accounts and settings:', error);
                alert('Gagal memuat data akun');
            } finally {
                $('#loadingIndicator').hide();
            }
        }

        async function loadAccounts(outletId) {
            try {
                const url = `<?php echo e(route('sdm.payroll.coa.accounts')); ?>?outlet_id=${outletId}`;
                console.log('Fetching accounts from URL:', url);
                
                const response = await fetch(url);
                const result = await response.json();
                
                console.log('Accounts API Response:', result);

                if (result.success && result.data) {
                    currentAccounts = result.data;
                    
                    // Populate expense accounts
                    populateSelect('salary_expense_account_id', currentAccounts.expense);
                    populateSelect('overtime_expense_account_id', currentAccounts.expense);
                    populateSelect('bonus_expense_account_id', currentAccounts.expense);
                    populateSelect('allowance_expense_account_id', currentAccounts.expense);
                    
                    // Populate liability accounts
                    populateSelect('salary_payable_account_id', currentAccounts.liability);
                    populateSelect('tax_payable_account_id', currentAccounts.liability);
                    
                    // Populate asset accounts
                    populateSelect('loan_receivable_account_id', currentAccounts.asset);
                    populateSelect('cash_account_id', currentAccounts.asset);
                    
                    console.log('Account selects populated successfully');
                } else {
                    console.log('No accounts found for this outlet');
                    clearAllFields();
                    alert('Tidak ada akun ditemukan untuk outlet ini. Pastikan akun sudah dibuat di Chart of Accounts.');
                }
            } catch (error) {
                console.error('Error loading accounts:', error);
                throw error;
            }
        }

        function populateSelect(selectId, accounts) {
            const select = $(`#${selectId}`);
            select.html('<option value="">Pilih Akun</option>');
            
            accounts.forEach(account => {
                select.append(`<option value="${account.id}">${account.code} - ${account.name}</option>`);
            });
        }

        async function loadSettings(outletId) {
            try {
                const url = `<?php echo e(route('sdm.payroll.coa.settings')); ?>?outlet_id=${outletId}`;
                console.log('Fetching settings from URL:', url);
                
                const response = await fetch(url);
                const result = await response.json();
                
                console.log('Settings API Response:', result);

                if (result.success && result.data) {
                    const data = result.data;
                    console.log('Setting form values with data:', data);
                    
                    $('#salary_expense_account_id').val(data.salary_expense_account_id || '');
                    $('#overtime_expense_account_id').val(data.overtime_expense_account_id || '');
                    $('#bonus_expense_account_id').val(data.bonus_expense_account_id || '');
                    $('#allowance_expense_account_id').val(data.allowance_expense_account_id || '');
                    $('#tax_payable_account_id').val(data.tax_payable_account_id || '');
                    $('#loan_receivable_account_id').val(data.loan_receivable_account_id || '');
                    $('#salary_payable_account_id').val(data.salary_payable_account_id || '');
                    $('#cash_account_id').val(data.cash_account_id || '');
                    
                    console.log('Form values set successfully');
                } else {
                    console.log('No settings found for this outlet, clearing form');
                    clearFormFields();
                }
            } catch (error) {
                console.error('Error loading settings:', error);
                throw error;
            }
        }

        function clearAllFields() {
            // Clear all select options
            const selects = [
                'salary_expense_account_id', 'overtime_expense_account_id', 
                'bonus_expense_account_id', 'allowance_expense_account_id',
                'tax_payable_account_id', 'loan_receivable_account_id',
                'salary_payable_account_id', 'cash_account_id'
            ];
            
            selects.forEach(selectId => {
                $(`#${selectId}`).html('<option value="">Pilih Akun</option>');
            });
        }

        function clearFormFields() {
            // Clear form values but keep options
            const selects = [
                'salary_expense_account_id', 'overtime_expense_account_id', 
                'bonus_expense_account_id', 'allowance_expense_account_id',
                'tax_payable_account_id', 'loan_receivable_account_id',
                'salary_payable_account_id', 'cash_account_id'
            ];
            
            selects.forEach(selectId => {
                $(`#${selectId}`).val('');
            });
        }

        function showSuccessIndicator() {
            // Add green border to form temporarily
            const form = $('#coaSettingForm');
            form.addClass('border-2 border-green-500');
            setTimeout(() => {
                form.removeClass('border-2 border-green-500');
            }, 2000);
        }

        function showToast(message, type = 'success') {
            // Create toast element if not exists
            if (!$('#toast-container').length) {
                $('body').append(`
                    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
                `);
            }

            const toastId = 'toast-' + Date.now();
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };

            const toast = $(`
                <div id="${toastId}" class="transform translate-x-full transition-transform duration-300 ${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg max-w-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">${message}</span>
                        <button onclick="$('#${toastId}').addClass('translate-x-full').delay(300).fadeOut()" class="ml-2 text-white hover:text-gray-200">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                </div>
            `);

            $('#toast-container').append(toast);
            
            // Show toast
            setTimeout(() => {
                toast.removeClass('translate-x-full');
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(() => {
                toast.addClass('translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }

        $('#coaSettingForm').on('submit', async function(e) {
            e.preventDefault();

            const data = {
                outlet_id: $('#outlet_id').val(),
                salary_expense_account_id: $('#salary_expense_account_id').val(),
                overtime_expense_account_id: $('#overtime_expense_account_id').val() || null,
                bonus_expense_account_id: $('#bonus_expense_account_id').val() || null,
                allowance_expense_account_id: $('#allowance_expense_account_id').val() || null,
                tax_payable_account_id: $('#tax_payable_account_id').val(),
                loan_receivable_account_id: $('#loan_receivable_account_id').val() || null,
                salary_payable_account_id: $('#salary_payable_account_id').val(),
                cash_account_id: $('#cash_account_id').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            };

            console.log('Submitting COA settings:', data);

            try {
                const response = await fetch('<?php echo e(route('sdm.payroll.coa.store')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log('Save response:', result);

                if (result.success) {
                    showToast(result.message, 'success');
                    console.log('Reloading settings after save...');
                    // Reload settings to show saved data
                    await loadSettings($('#outlet_id').val());
                    // Show success indicator
                    showSuccessIndicator();
                } else {
                    showToast(result.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                showToast('Gagal menyimpan setting', 'error');
            }
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\payroll\coa-settings.blade.php ENDPATH**/ ?>