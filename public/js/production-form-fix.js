
/**
 * Production Form Validation Fix
 * Memperbaiki masalah product_id tidak tersimpan
 */

document.addEventListener('DOMContentLoaded', function() {
    // Fix untuk form produksi
    const productionForm = document.querySelector('form[action*="produksi"]');
    if (productionForm) {
        
        // Pastikan product_id field ada dan terisi
        const productSelect = document.querySelector('#id_produk, select[name="id_produk"]');
        if (productSelect) {
            
            // Add validation before form submit
            productionForm.addEventListener('submit', function(e) {
                const productId = productSelect.value;
                
                if (!productId || productId === '') {
                    e.preventDefault();
                    alert('Silakan pilih produk terlebih dahulu');
                    productSelect.focus();
                    return false;
                }
                
                // Ensure product_id is properly set
                if (!document.querySelector('input[name="id_produk"]')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'id_produk';
                    hiddenInput.value = productId;
                    productionForm.appendChild(hiddenInput);
                }
                
                console.log('Production form submitted with product_id:', productId);
            });
            
            // Add change event to ensure value is captured
            productSelect.addEventListener('change', function() {
                const productId = this.value;
                console.log('Product selected:', productId);
                
                // Update or create hidden input
                let hiddenInput = document.querySelector('input[name="id_produk"][type="hidden"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'id_produk';
                    productionForm.appendChild(hiddenInput);
                }
                hiddenInput.value = productId;
            });
        }
    }
});
