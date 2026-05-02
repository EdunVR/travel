/**
 * Travel Management UI Helpers
 * Provides consistent modal, notification, and alert functionality
 */

const TravelUI = {
    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - Type: 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duration in milliseconds (default: 3000)
     */
    showToast(message, type = 'success', duration = 3000) {
        const container = this.getToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `travel-toast travel-toast-${type} travel-toast-enter`;
        
        const iconMap = {
            success: 'bx-check-circle',
            error: 'bx-error-circle',
            warning: 'bx-error',
            info: 'bx-info-circle'
        };
        
        toast.innerHTML = `
            <div class="travel-toast-icon">
                <i class='bx ${iconMap[type]}'></i>
            </div>
            <div class="travel-toast-message">${message}</div>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after duration
        setTimeout(() => {
            toast.classList.remove('travel-toast-enter');
            toast.classList.add('travel-toast-exit');
            setTimeout(() => {
                container.removeChild(toast);
                if (container.children.length === 0) {
                    document.body.removeChild(container);
                }
            }, 300);
        }, duration);
    },
    
    /**
     * Show a notification
     * @param {string} title - The notification title
     * @param {string} message - The notification message
     * @param {string} type - Type: 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duration in milliseconds (0 = no auto-dismiss)
     */
    showNotification(title, message, type = 'info', duration = 5000) {
        const container = this.getNotificationContainer();
        
        const notification = document.createElement('div');
        notification.className = `travel-notification travel-notification-${type} travel-notification-enter`;
        
        const iconMap = {
            success: 'bx-check-circle',
            error: 'bx-error-circle',
            warning: 'bx-error',
            info: 'bx-info-circle'
        };
        
        notification.innerHTML = `
            <div class="travel-notification-icon">
                <i class='bx ${iconMap[type]}'></i>
            </div>
            <div class="travel-notification-content">
                <div class="travel-notification-title">${title}</div>
                <div class="travel-notification-message">${message}</div>
            </div>
            <button class="travel-notification-close" onclick="this.parentElement.remove()">
                <i class='bx bx-x'></i>
            </button>
        `;
        
        container.appendChild(notification);
        
        // Auto remove after duration (if duration > 0)
        if (duration > 0) {
            setTimeout(() => {
                notification.classList.remove('travel-notification-enter');
                notification.classList.add('travel-notification-exit');
                setTimeout(() => {
                    if (notification.parentElement) {
                        container.removeChild(notification);
                        if (container.children.length === 0) {
                            document.body.removeChild(container);
                        }
                    }
                }, 300);
            }, duration);
        }
    },
    
    /**
     * Show an alert
     * @param {string} message - The alert message
     * @param {string} type - Type: 'success', 'error', 'warning', 'info'
     * @param {string} containerId - ID of container to append alert to
     * @param {boolean} dismissible - Whether the alert can be dismissed
     */
    showAlert(message, type = 'info', containerId = null, dismissible = true) {
        const alert = document.createElement('div');
        alert.className = `travel-alert travel-alert-${type}`;
        
        const iconMap = {
            success: 'bx-check-circle',
            error: 'bx-error-circle',
            warning: 'bx-error',
            info: 'bx-info-circle'
        };
        
        alert.innerHTML = `
            <div class="travel-alert-icon">
                <i class='bx ${iconMap[type]}'></i>
            </div>
            <div class="travel-alert-content">
                <div class="travel-alert-message">${message}</div>
            </div>
            ${dismissible ? `
                <button class="travel-alert-close" onclick="this.parentElement.remove()">
                    <i class='bx bx-x'></i>
                </button>
            ` : ''}
        `;
        
        if (containerId) {
            const container = document.getElementById(containerId);
            if (container) {
                container.appendChild(alert);
            }
        }
        
        return alert;
    },
    
    /**
     * Show a confirmation dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {Function} onConfirm - Callback when confirmed
     * @param {Function} onCancel - Callback when cancelled
     * @param {Object} options - Additional options
     */
    showConfirm(title, message, onConfirm, onCancel = null, options = {}) {
        const defaults = {
            confirmText: 'Confirm',
            cancelText: 'Cancel',
            confirmClass: 'bg-primary-600 text-white hover:bg-primary-700',
            cancelClass: 'border border-slate-200 hover:bg-slate-50',
            icon: 'warning', // 'warning', 'danger', 'info'
            iconClass: 'bx-error'
        };
        
        const opts = { ...defaults, ...options };
        
        const overlay = document.createElement('div');
        overlay.className = 'travel-modal-overlay';
        
        const iconClassMap = {
            warning: 'travel-confirm-icon-warning',
            danger: 'travel-confirm-icon-danger',
            info: 'travel-confirm-icon-info'
        };
        
        overlay.innerHTML = `
            <div class="travel-modal travel-confirm-dialog travel-modal-enter">
                <div class="travel-modal-body">
                    <div class="travel-confirm-icon ${iconClassMap[opts.icon]}">
                        <i class='bx ${opts.iconClass}'></i>
                    </div>
                    <div class="travel-confirm-title">${title}</div>
                    <div class="travel-confirm-message">${message}</div>
                    <div class="travel-confirm-actions">
                        <button class="travel-confirm-cancel rounded-xl px-4 py-2 ${opts.cancelClass}">
                            ${opts.cancelText}
                        </button>
                        <button class="travel-confirm-ok rounded-xl px-4 py-2 ${opts.confirmClass}">
                            ${opts.confirmText}
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        const confirmBtn = overlay.querySelector('.travel-confirm-ok');
        const cancelBtn = overlay.querySelector('.travel-confirm-cancel');
        
        const close = () => {
            document.body.removeChild(overlay);
        };
        
        confirmBtn.addEventListener('click', () => {
            if (onConfirm) onConfirm();
            close();
        });
        
        cancelBtn.addEventListener('click', () => {
            if (onCancel) onCancel();
            close();
        });
        
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                if (onCancel) onCancel();
                close();
            }
        });
    },
    
    /**
     * Get or create toast container
     */
    getToastContainer() {
        let container = document.querySelector('.travel-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'travel-toast-container';
            document.body.appendChild(container);
        }
        return container;
    },
    
    /**
     * Get or create notification container
     */
    getNotificationContainer() {
        let container = document.querySelector('.travel-notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'travel-notification-container';
            document.body.appendChild(container);
        }
        return container;
    },
    
    /**
     * Show loading overlay
     * @param {string} message - Loading message
     */
    showLoading(message = 'Loading...') {
        const overlay = document.createElement('div');
        overlay.id = 'travel-loading-overlay';
        overlay.className = 'travel-modal-overlay';
        overlay.innerHTML = `
            <div class="bg-white rounded-2xl p-6 text-center">
                <i class='bx bx-loader-alt bx-spin text-4xl text-primary-600 mb-3'></i>
                <div class="text-slate-700 font-medium">${message}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    },
    
    /**
     * Hide loading overlay
     */
    hideLoading() {
        const overlay = document.getElementById('travel-loading-overlay');
        if (overlay) {
            document.body.removeChild(overlay);
        }
    },
    
    /**
     * Format currency (IDR)
     * @param {number} amount - Amount to format
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },
    
    /**
     * Format date
     * @param {string} date - Date string
     * @param {string} format - Format type: 'short', 'long', 'full'
     */
    formatDate(date, format = 'short') {
        const d = new Date(date);
        const options = {
            short: { year: 'numeric', month: '2-digit', day: '2-digit' },
            long: { year: 'numeric', month: 'long', day: 'numeric' },
            full: { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
        };
        return d.toLocaleDateString('id-ID', options[format]);
    },
    
    /**
     * Debounce function
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in milliseconds
     */
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    /**
     * Copy text to clipboard
     * @param {string} text - Text to copy
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.showToast('Copied to clipboard', 'success');
        } catch (err) {
            this.showToast('Failed to copy', 'error');
        }
    },
    
    /**
     * Validate form
     * @param {HTMLFormElement} form - Form element
     * @returns {boolean} - Whether form is valid
     */
    validateForm(form) {
        const inputs = form.querySelectorAll('[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            this.showToast('Please fill in all required fields', 'error');
        }
        
        return isValid;
    }
};

// Make TravelUI globally available
window.TravelUI = TravelUI;

// Alpine.js magic helper for travel UI
if (window.Alpine) {
    Alpine.magic('travelUI', () => TravelUI);
}
