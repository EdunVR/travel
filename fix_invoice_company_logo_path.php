<?php

echo "=== FIXING INVOICE COMPANY LOGO PATH ===\n\n";

// Fix sales invoice
$salesInvoicePath = 'resources/views/admin/penjualan/invoice/print.blade.php';
$salesContent = file_get_contents($salesInvoicePath);

// Replace logo_url with company_logo
$salesContent = str_replace(
    "\$companySettings['logo_url']",
    "\$companySettings['company_logo']",
    $salesContent
);

file_put_contents($salesInvoicePath, $salesContent);
echo "✓ Fixed sales invoice logo path\n";

// Fix service invoice
$serviceInvoicePath = 'resources/views/admin/service/invoice/print.blade.php';
$serviceContent = file_get_contents($serviceInvoicePath);

// Replace logo_url with company_logo
$serviceContent = str_replace(
    "\$companySettings['logo_url']",
    "\$companySettings['company_logo']",
    $serviceContent
);

file_put_contents($serviceInvoicePath, $serviceContent);
echo "✓ Fixed service invoice logo path\n";

echo "\n=== CHANGES APPLIED ===\n";
echo "- Changed \$companySettings['logo_url'] to \$companySettings['company_logo']\n";
echo "- Updated both sales and service invoice templates\n";
echo "- Logo paths now match database structure\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Clear view cache: php artisan view:clear\n";
echo "2. Test invoice printing\n";
echo "3. Verify company logo appears\n";