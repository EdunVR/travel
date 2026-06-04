
<div class="print-footer" style="margin-top: 30px; page-break-inside: avoid;">
    
    
    <?php if($companySettings['bank_name'] || $companySettings['bank_account_number']): ?>
    <div class="bank-info" style="margin-bottom: 20px; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background-color: #f8f9fa;">
        <div class="bank-title" style="font-weight: bold; margin-bottom: 8px; color: #2c3e50; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">
            INFORMASI PEMBAYARAN
        </div>
        
        <?php if($companySettings['bank_name']): ?>
        <div style="margin-bottom: 3px;">
            <strong>Bank:</strong> <?php echo e($companySettings['bank_name']); ?>

        </div>
        <?php endif; ?>
        
        <?php if($companySettings['bank_account_number']): ?>
        <div style="margin-bottom: 3px;">
            <strong>No. Rekening:</strong> <span style="font-family: monospace;"><?php echo e($companySettings['bank_account_number']); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if($companySettings['bank_account_name']): ?>
        <div style="margin-bottom: 3px;">
            <strong>Atas Nama:</strong> <?php echo e($companySettings['bank_account_name']); ?>

        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    
    <?php if($companySettings['npwp'] || $companySettings['nib'] || $companySettings['siup'] || $companySettings['tdp']): ?>
    <div class="legal-info" style="margin-bottom: 20px; font-size: 10px; color: #6c757d; text-align: center;">
        <?php if($companySettings['npwp']): ?>
            NPWP: <?php echo e($companySettings['npwp']); ?>

        <?php endif; ?>
        
        <?php if($companySettings['nib']): ?>
            <?php if($companySettings['npwp']): ?> | <?php endif; ?>
            NIB: <?php echo e($companySettings['nib']); ?>

        <?php endif; ?>
        
        <?php if($companySettings['siup']): ?>
            <?php if($companySettings['npwp'] || $companySettings['nib']): ?> | <?php endif; ?>
            SIUP: <?php echo e($companySettings['siup']); ?>

        <?php endif; ?>
        
        <?php if($companySettings['tdp']): ?>
            <?php if($companySettings['npwp'] || $companySettings['nib'] || $companySettings['siup']): ?> | <?php endif; ?>
            TDP: <?php echo e($companySettings['tdp']); ?>

        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    
    <div class="signature-section" style="display: table; width: 100%; margin-top: 40px;">
        <div class="signature-left" style="display: table-cell; width: 50%; vertical-align: top;">
            <?php if(isset($showCustomerSignature) && $showCustomerSignature): ?>
            <div style="text-align: center;">
                <div style="margin-bottom: 60px;">Penerima,</div>
                <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto; padding-top: 5px;">
                    ( ........................... )
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="signature-right" style="display: table-cell; width: 50%; vertical-align: top; text-align: center;">
            <div style="margin-bottom: 10px;">
                <?php echo e($companySettings['company_address'] ? explode(',', $companySettings['company_address'])[0] : 'Jakarta'); ?>, 
                <?php echo e(isset($documentDate) ? $documentDate : date('d F Y')); ?>

            </div>
            <div style="margin-bottom: 60px;">Hormat Kami,</div>
            <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto; padding-top: 5px;">
                <strong><?php echo e(auth()->user()->name ?? 'Admin'); ?></strong>
            </div>
        </div>
    </div>
    
    
    <div class="footer-note" style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d; border-top: 1px dashed #dee2e6; padding-top: 10px;">
        Dokumen ini dicetak secara otomatis oleh sistem <?php echo e($companySettings['company_name']); ?>

        <br>
        Tanggal cetak: <?php echo e(now()->format('d/m/Y H:i:s')); ?>

    </div>
</div>

<style>
    @media print {
        .print-footer {
            page-break-inside: avoid;
        }
    }
</style><?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\print-footer.blade.php ENDPATH**/ ?>