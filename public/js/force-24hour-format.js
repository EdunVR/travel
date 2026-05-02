/**
 * FORCE 24-HOUR FORMAT - GLOBAL JAVASCRIPT
 * This script aggressively enforces 24-hour format on all time inputs
 */

(function() {
    'use strict';
    
    console.log('🕐 Loading AGGRESSIVE 24-hour format enforcement...');
    
    // Configuration
    const CONFIG = {
        debug: true,
        retryAttempts: 5,
        retryDelay: 200,
        observerDelay: 50
    };
    
    // Log function
    function log(message, ...args) {
        if (CONFIG.debug) {
            console.log(`[24H-FORMAT] ${message}`, ...args);
        }
    }
    
    // Function to aggressively enforce 24-hour format
    function enforce24HourFormat() {
        const timeInputs = document.querySelectorAll('input[type="time"]');
        log(`Found ${timeInputs.length} time inputs to process`);
        
        timeInputs.forEach((input, index) => {
            try {
                // Skip if already processed
                if (input.hasAttribute('data-24hour-enforced')) {
                    return;
                }
                
                log(`Processing time input ${index + 1}:`, input);
                
                // Mark as processed
                input.setAttribute('data-24hour-enforced', 'true');
                
                // Force attributes
                input.setAttribute('step', '1');
                input.setAttribute('pattern', '[0-9]{2}:[0-9]{2}');
                input.setAttribute('data-format', '24');
                input.setAttribute('data-24hour', 'true');
                input.setAttribute('min', '00:00');
                input.setAttribute('max', '23:59');
                
                // Remove AM/PM related attributes
                const ampmAttributes = [
                    'data-12hour', 'data-ampm', 'data-meridiem', 
                    'data-am-pm', 'data-time-format'
                ];
                ampmAttributes.forEach(attr => {
                    if (input.hasAttribute(attr)) {
                        input.removeAttribute(attr);
                        log(`Removed attribute: ${attr}`);
                    }
                });
                
                // Force CSS properties
                const styles = {
                    '-webkit-appearance': 'none',
                    '-moz-appearance': 'textfield',
                    'appearance': 'none'
                };
                
                Object.entries(styles).forEach(([property, value]) => {
                    input.style.setProperty(property, value, 'important');
                });
                
                // Add validation event listener
                if (!input.hasAttribute('data-24hour-validated')) {
                    input.setAttribute('data-24hour-validated', 'true');
                    
                    input.addEventListener('input', function(e) {
                        const value = this.value;
                        log(`Time input changed: ${value}`);
                        
                        if (value && !value.match(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
                            this.setCustomValidity('Format harus HH:MM (24 jam)');
                            log(`Invalid time format: ${value}`);
                        } else {
                            this.setCustomValidity('');
                            log(`Valid time format: ${value}`);
                        }
                    });
                    
                    input.addEventListener('focus', function() {
                        log('Time input focused, re-enforcing 24-hour format');
                        this.setAttribute('data-format', '24');
                        this.setAttribute('data-24hour', 'true');
                        
                        // Force hide AM/PM elements after focus
                        setTimeout(() => hideAmPmElements(this), 10);
                    });
                    
                    input.addEventListener('click', function() {
                        log('Time input clicked, re-enforcing 24-hour format');
                        setTimeout(() => hideAmPmElements(this), 10);
                    });
                }
                
                // Hide AM/PM elements immediately
                hideAmPmElements(input);
                
                log(`✅ Successfully processed time input ${index + 1}`);
                
            } catch (error) {
                log(`❌ Error processing time input ${index + 1}:`, error);
            }
        });
        
        return timeInputs.length;
    }
    
    // Function to aggressively hide AM/PM elements
    function hideAmPmElements(input) {
        try {
            // Get the shadow root if it exists (for some browsers)
            const shadowRoot = input.shadowRoot;
            if (shadowRoot) {
                const ampmElements = shadowRoot.querySelectorAll('*[class*="ampm"], *[class*="meridiem"]');
                ampmElements.forEach(el => {
                    el.style.display = 'none';
                    el.style.visibility = 'hidden';
                });
            }
            
            // Hide any sibling elements that might contain AM/PM
            const parent = input.parentElement;
            if (parent) {
                const siblings = parent.querySelectorAll('*');
                siblings.forEach(sibling => {
                    const text = sibling.textContent || sibling.innerText || '';
                    if (text.includes('AM') || text.includes('PM')) {
                        sibling.style.display = 'none';
                        log('Hidden AM/PM sibling element:', sibling);
                    }
                });
            }
            
        } catch (error) {
            log('Error hiding AM/PM elements:', error);
        }
    }
    
    // Function to run enforcement with retries
    function runEnforcementWithRetries(attempt = 1) {
        const processed = enforce24HourFormat();
        log(`Enforcement attempt ${attempt}: processed ${processed} inputs`);
        
        if (attempt < CONFIG.retryAttempts) {
            setTimeout(() => {
                runEnforcementWithRetries(attempt + 1);
            }, CONFIG.retryDelay * attempt);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            log('DOM loaded, starting enforcement');
            runEnforcementWithRetries();
        });
    } else {
        log('DOM already loaded, starting enforcement immediately');
        runEnforcementWithRetries();
    }
    
    // Run after page load
    window.addEventListener('load', function() {
        log('Page loaded, running additional enforcement');
        setTimeout(() => runEnforcementWithRetries(), 100);
    });
    
    // Watch for dynamically added time inputs
    const observer = new MutationObserver(function(mutations) {
        let hasNewTimeInputs = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'INPUT' && node.type === 'time') {
                            hasNewTimeInputs = true;
                            log('New time input detected directly:', node);
                        } else if (node.querySelectorAll) {
                            const timeInputs = node.querySelectorAll('input[type="time"]');
                            if (timeInputs.length > 0) {
                                hasNewTimeInputs = true;
                                log(`New time inputs detected in container: ${timeInputs.length}`);
                            }
                        }
                    }
                });
            } else if (mutation.type === 'attributes' && 
                       mutation.target.tagName === 'INPUT' && 
                       mutation.attributeName === 'type' && 
                       mutation.target.type === 'time') {
                hasNewTimeInputs = true;
                log('Input type changed to time:', mutation.target);
            }
        });
        
        if (hasNewTimeInputs) {
            log('🔄 New time inputs detected, re-running enforcement');
            setTimeout(() => {
                runEnforcementWithRetries();
            }, CONFIG.observerDelay);
        }
    });
    
    // Start observing
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['type', 'class', 'id']
        });
        log('✅ MutationObserver started');
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['type', 'class', 'id']
            });
            log('✅ MutationObserver started after DOM ready');
        });
    }
    
    // Additional enforcement for Alpine.js
    document.addEventListener('alpine:init', function() {
        log('🏔️ Alpine.js initialized, running enforcement');
        setTimeout(() => runEnforcementWithRetries(), 100);
    });
    
    document.addEventListener('alpine:initialized', function() {
        log('🏔️ Alpine.js fully initialized, running enforcement');
        setTimeout(() => runEnforcementWithRetries(), 100);
    });
    
    // Intercept setAttribute to prevent 12-hour format (only for time inputs)
    const originalSetAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function(name, value) {
        // Skip override for non-time elements to avoid Alpine.js conflicts
        if (!this.type || this.type !== "time" || this.tagName !== "INPUT") {
            return originalSetAttribute.call(this, name, value);
        }
        // Only intercept for time inputs to avoid conflicts with other functionality
        if (this.type === 'time' && this.tagName === 'INPUT') {
            const blockedAttributes = ['data-12hour', 'data-ampm', 'data-meridiem'];
            if (blockedAttributes.includes(name)) {
                log(`🚫 Blocked attempt to set ${name}="${value}" on time input`);
                return;
            }
        }
        
        // For all other elements and attributes, use original method
        try {
            return originalSetAttribute.call(this, name, value);
        } catch (error) {
            // If there's an error, log it but don't break other functionality
            console.warn('setAttribute override error:', error);
            return;
        }
    };
    
    // Global function to manually trigger enforcement
    window.enforce24HourFormat = function() {
        log('Manual enforcement triggered');
        runEnforcementWithRetries();
    };
    
    log('✅ AGGRESSIVE 24-hour format enforcement initialized');
    
})();