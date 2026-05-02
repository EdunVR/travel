/**
 * Travel Management System - Validation Error Display Helper
 * Provides consistent validation error display across all forms
 */

const TravelValidation = {
    /**
     * Display validation errors on form fields
     * @param {Object} errors - Laravel validation errors object
     * @param {String} formSelector - Optional form selector to scope errors
     */
    displayErrors: function(errors, formSelector = null) {
        // Clear existing errors first
        this.clearErrors(formSelector);

        // Display each error
        Object.keys(errors).forEach(field => {
            const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
            this.showFieldError(field, messages[0], formSelector);
        });
    },

    /**
     * Show error for a specific field
     * @param {String} fieldName - Name of the field
     * @param {String} message - Error message
     * @param {String} formSelector - Optional form selector
     */
    showFieldError: function(fieldName, message, formSelector = null) {
        const scope = formSelector ? $(formSelector) : $(document);
        const field = scope.find(`[name="${fieldName}"], #${fieldName}`);
        
        if (field.length === 0) {
            console.warn(`Field not found: ${fieldName}`);
            return;
        }

        // Add invalid class
        field.addClass('is-invalid');
        
        // Remove any existing feedback
        field.siblings('.invalid-feedback').remove();
        
        // Add error message
        field.after(`<div class="invalid-feedback d-block">${message}</div>`);
        
        // Scroll to first error if this is the first one
        if (scope.find('.is-invalid').length === 1) {
            $('html, body').animate({
                scrollTop: field.offset().top - 100
            }, 300);
        }
    },

    /**
     * Clear all validation errors
     * @param {String} formSelector - Optional form selector to scope clearing
     */
    clearErrors: function(formSelector = null) {
        const scope = formSelector ? $(formSelector) : $(document);
        scope.find('.is-invalid').removeClass('is-invalid');
        scope.find('.invalid-feedback').remove();
        scope.find('.alert-danger').remove();
    },

    /**
     * Display warning message (non-blocking validation)
     * @param {String} message - Warning message
     * @param {String} containerSelector - Container to show warning in
     */
    showWarning: function(message, containerSelector = '.modal-body') {
        const container = $(containerSelector);
        
        // Remove existing warnings
        container.find('.alert-warning.validation-warning').remove();
        
        // Add warning alert
        const warningHtml = `
            <div class="alert alert-warning alert-dismissible fade show validation-warning" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Peringatan:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        container.prepend(warningHtml);
    },

    /**
     * Display general error message
     * @param {String} message - Error message
     * @param {String} containerSelector - Container to show error in
     */
    showError: function(message, containerSelector = '.modal-body') {
        const container = $(containerSelector);
        
        // Remove existing errors
        container.find('.alert-danger.validation-error').remove();
        
        // Add error alert
        const errorHtml = `
            <div class="alert alert-danger alert-dismissible fade show validation-error" role="alert">
                <i class="fas fa-times-circle mr-2"></i>
                <strong>Error:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        container.prepend(errorHtml);
        
        // Scroll to error
        $('html, body').animate({
            scrollTop: container.offset().top - 100
        }, 300);
    },

    /**
     * Display success message
     * @param {String} message - Success message
     * @param {String} containerSelector - Container to show message in
     */
    showSuccess: function(message, containerSelector = '.modal-body') {
        const container = $(containerSelector);
        
        const successHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        container.prepend(successHtml);
    },

    /**
     * Handle AJAX error response
     * @param {Object} xhr - jQuery XHR object
     * @param {String} formSelector - Optional form selector
     */
    handleAjaxError: function(xhr, formSelector = null) {
        if (xhr.status === 422) {
            // Validation errors
            const response = xhr.responseJSON;
            if (response && response.errors) {
                this.displayErrors(response.errors, formSelector);
            }
            if (response && response.message) {
                this.showError(response.message, formSelector || '.modal-body');
            }
        } else if (xhr.status === 400) {
            // Business rule violation
            const response = xhr.responseJSON;
            if (response && response.message) {
                this.showError(response.message, formSelector || '.modal-body');
            }
        } else if (xhr.status === 403) {
            // Authorization error
            this.showError('Anda tidak memiliki izin untuk melakukan tindakan ini.', formSelector || '.modal-body');
        } else if (xhr.status === 404) {
            // Not found
            this.showError('Data tidak ditemukan.', formSelector || '.modal-body');
        } else {
            // Server error
            this.showError('Terjadi kesalahan pada server. Silakan coba lagi nanti.', formSelector || '.modal-body');
        }
    },

    /**
     * Validate field on blur
     * @param {String} fieldSelector - Field selector
     * @param {Function} validationFn - Validation function that returns {valid: boolean, message: string}
     */
    validateOnBlur: function(fieldSelector, validationFn) {
        $(document).on('blur', fieldSelector, function() {
            const field = $(this);
            const value = field.val();
            const result = validationFn(value, field);
            
            if (!result.valid) {
                TravelValidation.showFieldError(field.attr('name') || field.attr('id'), result.message);
            } else {
                field.removeClass('is-invalid');
                field.siblings('.invalid-feedback').remove();
            }
        });
    },

    /**
     * Clear errors on input
     * @param {String} fieldSelector - Field selector
     */
    clearOnInput: function(fieldSelector) {
        $(document).on('input change', fieldSelector, function() {
            const field = $(this);
            field.removeClass('is-invalid');
            field.siblings('.invalid-feedback').remove();
        });
    },

    /**
     * Display validation summary
     * @param {Array} errors - Array of error objects with {field, message}
     * @param {String} containerSelector - Container to show summary in
     */
    showValidationSummary: function(errors, containerSelector = '.modal-body') {
        const container = $(containerSelector);
        
        let errorList = '<ul class="mb-0">';
        errors.forEach(error => {
            errorList += `<li>${error.message || error}</li>`;
        });
        errorList += '</ul>';
        
        const summaryHtml = `
            <div class="alert alert-danger alert-dismissible fade show validation-summary" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle mr-2"></i>Validasi Gagal</h5>
                <p class="mb-2">Silakan perbaiki kesalahan berikut:</p>
                ${errorList}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        container.prepend(summaryHtml);
    },

    /**
     * Show inline warning indicator
     * @param {String} fieldSelector - Field selector
     * @param {String} message - Warning message
     */
    showFieldWarning: function(fieldSelector, message) {
        const field = $(fieldSelector);
        
        // Remove existing warning
        field.siblings('.text-warning').remove();
        field.removeClass('border-warning');
        
        // Add warning styling
        field.addClass('border-warning');
        field.after(`<small class="text-warning d-block mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>${message}</small>`);
    },

    /**
     * Initialize real-time validation for common fields
     */
    initializeCommonValidations: function() {
        // Clear errors on input
        this.clearOnInput('input, select, textarea');
        
        // NIK validation (16 digits)
        this.validateOnBlur('input[name="ktp_nik"], input[name="nik"]', function(value) {
            if (!value) return {valid: true};
            if (!/^\d{16}$/.test(value)) {
                return {valid: false, message: 'NIK KTP harus terdiri dari 16 digit angka'};
            }
            return {valid: true};
        });
        
        // Phone number validation
        this.validateOnBlur('input[name*="phone"], input[name*="telepon"], input[name*="hp"]', function(value) {
            if (!value) return {valid: true};
            if (!/^[0-9+\-\s()]{8,20}$/.test(value)) {
                return {valid: false, message: 'Format nomor telepon tidak valid'};
            }
            return {valid: true};
        });
        
        // Email validation
        this.validateOnBlur('input[type="email"]', function(value) {
            if (!value) return {valid: true};
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return {valid: false, message: 'Format email tidak valid'};
            }
            return {valid: true};
        });
        
        // Positive number validation
        this.validateOnBlur('input[type="number"][min="0"]', function(value, field) {
            if (!value) return {valid: true};
            const num = parseFloat(value);
            if (isNaN(num) || num < 0) {
                return {valid: false, message: 'Nilai harus berupa angka positif'};
            }
            return {valid: true};
        });
        
        // Date validation (future dates)
        this.validateOnBlur('input[name*="departure"], input[name*="keberangkatan"]', function(value) {
            if (!value) return {valid: true};
            const selectedDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate <= today) {
                return {valid: false, message: 'Tanggal keberangkatan harus di masa depan'};
            }
            return {valid: true};
        });
    }
};

// Initialize on document ready
$(document).ready(function() {
    TravelValidation.initializeCommonValidations();
});

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TravelValidation;
}
