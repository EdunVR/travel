<?php

echo "=== FIXING COMPANY LOGO KEY FINAL ===\n\n";

// Fix sales invoice - revert back to logo_url
$salesInvoicePath = 'resources/views/admin/penjualan/invoice/print.blade.php';
$salesContent = file_get_contents($salesInvoicePath);

// Replace company_logo back to logo_url (since model has accessor)
$salesContent = str_replace(
    "\$companySettings['company_logo']",
    "\$companySettings['logo_url']",
    $salesContent
);

file_put_contents($salesInvoicePath, $salesContent);
echo "✓ Fixed sales invoice to use logo_url (with accessor)\n";

// Fix service invoice - revert back to logo_url
$serviceInvoicePath = 'resources/views/admin/service/invoice/print.blade.php';
$serviceContent = file_get_contents($serviceInvoicePath);

// Replace company_logo back to logo_url (since model has accessor)
$serviceContent = str_replace(
    "\$companySettings['company_logo']",
    "\$companySettings['logo_url']",
    $serviceContent
);

file_put_contents($serviceInvoicePath, $serviceContent);
echo "✓ Fixed service invoice to use logo_url (with accessor)\n";

echo "\n=== EXPLANATION ===\n";
echo "- CompanySetting model has getLogoUrlAttribute() accessor\n";
echo "- Accessor reads from 'company_logo' database column\n";
echo "- But exposes it as 'logo_url' attribute in arrays\n";
echo "- Templates should use \$companySettings['logo_url']\n";
echo "- This maps to company_logo column via accessor\n";

echo "\n=== CHANGES APPLIED ===\n";
echo "- Reverted \$companySettings['company_logo'] back to \$companySettings['logo_url']\n";
echo "- Updated both sales and service invoice templates\n";
echo "- Now uses correct accessor key that exists in controller arrays\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Clear view cache: php artisan view:clear\n";
echo "2. Test invoice printing\n";
echo "3. Verify company logo appears\n";