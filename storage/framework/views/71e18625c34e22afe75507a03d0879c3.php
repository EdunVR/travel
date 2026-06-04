
<div class="print-header" style="display: table; width: 100%; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px;">
    <div class="header-left" style="display: table-cell; width: 70%; vertical-align: top;">
        <?php if($companySettings['logo_url']): ?>
        <img src="<?php echo e($companySettings['logo_url']); ?>" 
             alt="Company Logo" 
             style="width: 80px; height: auto; float: left; margin-right: 15px; margin-top: 5px;">
        <?php endif; ?>
        
        <div class="company-info" style="overflow: hidden;">
            <div class="company-name" style="font-weight: bold; font-size: 18px; margin-bottom: 5px; color: #2c3e50;">
                <?php echo e($companySettings['company_name']); ?>

            </div>
            
            <?php if($companySettings['company_code']): ?>
            <div class="company-code" style="font-size: 12px; color: #7f8c8d; margin-bottom: 3px;">
                Kode: <?php echo e($companySettings['company_code']); ?>

            </div>
            <?php endif; ?>
            
            <?php if($companySettings['company_address']): ?>
            <div class="company-address" style="font-size: 12px; line-height: 1.4; color: #34495e; margin-bottom: 3px;">
                <?php echo $companySettings['formatted_address']; ?>

            </div>
            <?php endif; ?>
            
            <div class="company-contact" style="font-size: 12px; color: #34495e;">
                <?php if($companySettings['company_phone']): ?>
                    <span>Telp: <?php echo e($companySettings['company_phone']); ?></span>
                <?php endif; ?>
                
                <?php if($companySettings['company_email']): ?>
                    <?php if($companySettings['company_phone']): ?> | <?php endif; ?>
                    <span>Email: <?php echo e($companySettings['company_email']); ?></span>
                <?php endif; ?>
                
                <?php if($companySettings['company_website']): ?>
                    <?php if($companySettings['company_phone'] || $companySettings['company_email']): ?> | <?php endif; ?>
                    <span><?php echo e($companySettings['company_website']); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="header-right" style="display: table-cell; width: 30%; vertical-align: top; text-align: right;">
        <div class="document-info">
            <?php if(isset($documentTitle)): ?>
            <div class="document-title" style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: #e74c3c;">
                <?php echo e($documentTitle); ?>

            </div>
            <?php endif; ?>
            
            <?php if(isset($documentNumber)): ?>
            <div class="document-number" style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">
                No: <?php echo e($documentNumber); ?>

            </div>
            <?php endif; ?>
            
            <?php if(isset($documentDate)): ?>
            <div class="document-date" style="font-size: 12px; color: #7f8c8d;">
                Tanggal: <?php echo e($documentDate); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    @media print {
        .print-header {
            page-break-inside: avoid;
        }
        
        .print-header img {
            max-width: 80px !important;
            height: auto !important;
        }
    }
</style><?php /**PATH C:\xampp\htdocs\hm\resources\views\partials\print-header.blade.php ENDPATH**/ ?>