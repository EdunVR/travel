/**
 * Button Loading Helper
 * Usage: showButtonLoading(button), hideButtonLoading(button)
 */

function showButtonLoading(button) {
    const btn = $(button);
    if (btn.data('loading')) return; // Already loading
    
    btn.data('loading', true);
    btn.data('original-html', btn.html());
    btn.data('original-disabled', btn.prop('disabled'));
    
    btn.prop('disabled', true);
    
    // Detect if button has icon
    const hasIcon = btn.find('i.fas, i.far, i.fab').length > 0;
    
    if (hasIcon) {
        btn.find('i').attr('class', 'fas fa-spinner fa-spin');
    } else {
        btn.html('<i class="fas fa-spinner fa-spin"></i> ' + btn.text());
    }
}

function hideButtonLoading(button) {
    const btn = $(button);
    if (!btn.data('loading')) return; // Not loading
    
    btn.data('loading', false);
    btn.html(btn.data('original-html'));
    btn.prop('disabled', btn.data('original-disabled') || false);
    
    btn.removeData('loading');
    btn.removeData('original-html');
    btn.removeData('original-disabled');
}

// Auto-apply to forms with data-loading attribute
$(document).ready(function() {
    $('form[data-loading]').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading(submitBtn);
    });
    
    // Auto-apply to buttons with data-loading attribute
    $('button[data-loading], a[data-loading]').on('click', function() {
        showButtonLoading(this);
    });
});
