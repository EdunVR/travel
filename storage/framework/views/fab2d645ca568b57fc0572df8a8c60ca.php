<style>
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
    .posting-select {
        font-size: 0.8rem;
        padding: 0.25rem;
    }
    .debit-input:disabled,
    .credit-input:disabled {
        background-color: #f8f9fa;
        opacity: 0.7;
    }
    /* Indentasi hierarki */
    .level-1 .account-name { padding-left: 15px; }
    .level-2 .account-name { padding-left: 35px; }
    .level-3 .account-name { padding-left: 55px; }
    .level-4 .account-name { padding-left: 75px; }
    .level-5 .account-name { padding-left: 95px; }
    .level-6 .account-name { padding-left: 115px; }

    /* Indentasi hierarki */
    .level-1 .account-code { padding-left: 15px; }
    .level-2 .account-code { padding-left: 35px; }
    .level-3 .account-code { padding-left: 55px; }
    .level-4 .account-code { padding-left: 75px; }
    .level-5 .account-code { padding-left: 95px; }
    .level-6 .account-code { padding-left: 115px; }
    
    /* Warna background untuk level berbeda */
    .level-1 { background-color: white; }
    .level-2 { background-color: white; }
    .level-3 { background-color: rgba(0,0,0,0.04); }
    .level-4 { background-color: rgba(0,0,0,0.06); }
    .level-5 { background-color: rgba(0,0,0,0.08); }
    .level-6 { background-color: rgba(0,0,0,0.10); }
</style>


<?php $__env->startSection('title', 'Saldo Awal Buku'); ?>
<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Saldo Awal untuk Buku: <?php echo e($book->name); ?>

            </h6>
            <a href="<?php echo e(route('financial.book.list')); ?>" class="btn btn-danger">
                <i data-feather="arrow-left"></i> Kembali ke Daftar Buku
            </a>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Status Saldo:</strong> 
                        <span id="balanceStatus">Loading...</span>
                    </div>
                    <div>
                        <strong>Total Debit:</strong> 
                        <span id="totalDebit" class="font-weight-bold">0</span> | 
                        <strong>Total Kredit:</strong> 
                        <span id="totalCredit" class="font-weight-bold">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
        <form method="POST" action="<?php echo e(route('financial.book.update_balances', $book->id)); ?>" id="balanceForm">
            <?php echo csrf_field(); ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: left">Kode Akun</th>
                        <th style="text-align: left">Nama Akun</th>
                        <th width="150px">Posting</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $level = count(explode('.', $account['code']));
                            $balance = $openingBalances[$account['code']] ?? null;
                            $accountType = $account['type'];
                            
                            // Tentukan default posting
                            $defaultPosting = '';
                            $isCreditAccount = in_array($accountType, ['liability', 'equity', 'revenue']);
                            
                            if ($balance) {
                                $defaultPosting = $balance->debit > 0 ? 'debit' : 'credit';
                            }
                        ?>
                        <tr class="level-<?php echo e($level); ?>">
                            <td class="account-code" style="text-align: left">
                                <span class="d-block text-left pr-2"><?php echo e($account['code']); ?></span>
                            </td>
                            <td class="account-name" style="text-align: left">
                                <span class="d-block text-left pl-2"><?php echo e($account['name']); ?></span>
                            </td>
                            
                            <?php if($level > 2): ?>
                            <td>
                                <select class="form-control posting-select" 
                                        data-account="<?php echo e($account['code']); ?>"
                                        data-type="<?php echo e($accountType); ?>"
                                        data-is-credit="<?php echo e($isCreditAccount ? 'true' : 'false'); ?>">
                                    <option value="">- Pilih -</option>
                                    <option value="debit" <?php echo e($defaultPosting == 'debit' ? 'selected' : ''); ?>>
                                        <?php echo e($isCreditAccount ? 'Mengurangi' : 'Menambah'); ?> <?php echo e(ucfirst($accountType)); ?>

                                    </option>
                                    <option value="credit" <?php echo e($defaultPosting == 'credit' ? 'selected' : ''); ?>>
                                        <?php echo e($isCreditAccount ? 'Menambah' : 'Mengurangi'); ?> <?php echo e(ucfirst($accountType)); ?>

                                    </option>
                                </select>
                            </td>
                            <td>
                                <input type="text" 
                                    name="balances[<?php echo e($account['code']); ?>][debit]" 
                                    value="<?php echo e(isset($balance->debit) ? number_format($balance->debit, 0, ',', '.') : ''); ?>" 
                                    class="form-control debit-input number-input" 
                                    style="text-align: right"
                                    <?php echo e($defaultPosting != 'debit' ? 'disabled' : ''); ?>>
                            </td>
                            <td>
                                <input type="text" 
                                    name="balances[<?php echo e($account['code']); ?>][credit]" 
                                    value="<?php echo e(isset($balance->credit) ? number_format($balance->credit, 0, ',', '.') : ''); ?>" 
                                    class="form-control credit-input number-input" 
                                    style="text-align: right"
                                    <?php echo e($defaultPosting != 'credit' ? 'disabled' : ''); ?>>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">
                <i data-feather="save"></i> Simpan Saldo Awal
            </button>
        </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Fungsi untuk memformat angka dengan separator ribuan
    function formatNumber(input) {
        // Hapus semua karakter non-digit
        let value = input.value.replace(/[^0-9]/g, '');
        
        // Format dengan separator ribuan
        if (value.length > 0) {
            value = parseInt(value, 10).toLocaleString('id-ID');
        }
        
        // Update nilai input
        input.value = value;
    }

    // Fungsi untuk mendapatkan nilai numerik dari input yang diformat
    function getNumericValue(formattedValue) {
        return formattedValue ? parseInt(formattedValue.replace(/[^0-9]/g, ''), 10) : 0;
    }

    // Inisialisasi format number input
    function initializeNumberInputs() {
        document.querySelectorAll('.number-input').forEach(input => {
            // Format saat input berubah
            input.addEventListener('input', function() {
                // Simpan posisi kursor
                const cursorPosition = this.selectionStart;
                const originalLength = this.value.length;
                
                formatNumber(this);
                
                // Kembalikan posisi kursor setelah format
                const newLength = this.value.length;
                const cursorOffset = newLength - originalLength;
                this.setSelectionRange(cursorPosition + cursorOffset, cursorPosition + cursorOffset);
            });
            
            // Format saat pertama kali load
            if (input.value) {
                formatNumber(input);
            }
        });
    }

    // Fungsi untuk menghitung total
    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        
        document.querySelectorAll('.debit-input:not(:disabled)').forEach(input => {
            totalDebit += getNumericValue(input.value);
        });
        
        document.querySelectorAll('.credit-input:not(:disabled)').forEach(input => {
            totalCredit += getNumericValue(input.value);
        });
        
        // Update UI
        document.getElementById('totalDebit').textContent = totalDebit.toLocaleString('id-ID');
        document.getElementById('totalCredit').textContent = totalCredit.toLocaleString('id-ID');
        
        // Check balance
        const diff = Math.abs(totalDebit - totalCredit);
        const statusElement = document.getElementById('balanceStatus');
        
        if (diff < 0.01) {
            statusElement.innerHTML = '<span class="text-success">Balance ✓</span>';
        } else {
            statusElement.innerHTML = `<span class="text-danger">Tidak Balance (Selisih: ${diff.toLocaleString('id-ID')})</span>`;
        }
    }

    // Fungsi untuk mempersiapkan data sebelum submit
    function prepareFormDataBeforeSubmit() {
        document.querySelectorAll('.number-input').forEach(input => {
            const numericValue = getNumericValue(input.value);
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = input.name;
            hiddenInput.value = numericValue;
            input.parentNode.appendChild(hiddenInput);
            input.disabled = true;
        });
        return true;
    }

    // Inisialisasi saat DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        initializeNumberInputs();
        const debitInputs = document.querySelectorAll('.debit-input');
        const creditInputs = document.querySelectorAll('.credit-input');
        calculateTotals();

        debitInputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
        
        creditInputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
        
        // Event listener untuk select posting
        document.querySelectorAll('.posting-select').forEach(select => {
            select.addEventListener('change', function() {
                const accountCode = this.dataset.account;
                const accountType = this.dataset.type;
                const isCreditAccount = this.dataset.isCredit === 'true';
                const debitInput = document.querySelector(`.debit-input[name="balances[${accountCode}][debit]"]`);
                const creditInput = document.querySelector(`.credit-input[name="balances[${accountCode}][credit]"]`);
                const options = select.querySelectorAll('option');

                // Update teks opsi
                options[1].text = isCreditAccount 
                    ? `Mengurangi ${accountType}` 
                    : `Menambah ${accountType}`;
                    
                options[2].text = isCreditAccount 
                    ? `Menambah ${accountType}` 
                    : `Mengurangi ${accountType}`;

                // Reset state
                debitInput.disabled = true;
                creditInput.disabled = true;

                if (this.value === 'debit') {
                    debitInput.disabled = false;
                    if (getNumericValue(debitInput.value) === 0 && getNumericValue(creditInput.value) > 0) {
                        debitInput.value = creditInput.value;
                        creditInput.value = '';
                        formatNumber(debitInput);
                    }
                    debitInput.focus();
                } else if (this.value === 'credit') {
                    creditInput.disabled = false;
                    if (getNumericValue(creditInput.value) === 0 && getNumericValue(debitInput.value) > 0) {
                        creditInput.value = debitInput.value;
                        debitInput.value = '';
                        formatNumber(creditInput);
                    }
                    creditInput.focus();
                } else {
                    debitInput.value = '';
                    creditInput.value = '';
                }

                calculateTotals();
            });
        });

        // Inisialisasi state awal select
        document.querySelectorAll('.posting-select').forEach(select => {
            if (select.value) {
                select.dispatchEvent(new Event('change'));
            }
        });
    });

    // Form submission handler
    document.getElementById('balanceForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Format semua input number sebelum submit
        prepareFormDataBeforeSubmit();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-feather="loader"></i> Menyimpan...';
        feather.replace();

        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await response.json();
            
            if (data.success) {
                window.location.href = data.redirect || window.location.href;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan saat menyimpan data',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data',
                confirmButtonText: 'OK'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            feather.replace();
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\book\opening_balances.blade.php ENDPATH**/ ?>