// Outlet Helper - Global utility for outlet selection
// This file provides common outlet-related functionality across the application

window.OutletHelper = {
    // Get current outlet ID from various sources
    getCurrentOutletId: function () {
        // Try to get from URL parameter first
        const urlParams = new URLSearchParams(window.location.search);
        const outletFromUrl = urlParams.get("outlet_id");
        if (outletFromUrl) {
            return outletFromUrl;
        }

        // Try to get from form input
        const outletInput = document.getElementById("outlet_id");
        if (outletInput && outletInput.value) {
            return outletInput.value;
        }

        // Try to get from outlet selector
        const outletSelector = document.getElementById("outlet-selector");
        if (outletSelector && outletSelector.value) {
            return outletSelector.value;
        }

        // Default fallback
        return "1";
    },

    // Handle outlet change events
    handleOutletChange: function (newOutletId, reloadPage = true) {
        if (reloadPage) {
            // Update URL and reload page
            const url = new URL(window.location);
            url.searchParams.set("outlet_id", newOutletId);
            window.location.href = url.toString();
        } else {
            // Just update form inputs
            const outletInput = document.getElementById("outlet_id");
            if (outletInput) {
                outletInput.value = newOutletId;
            }
        }
    },

    // Get first outlet ID from outlets array
    getFirstOutletId: function (outlets) {
        if (!outlets || !Array.isArray(outlets) || outlets.length === 0) {
            return "1"; // Default fallback
        }
        return outlets[0].id || outlets[0].id_outlet || "1";
    },

    // Initialize outlet selector if present
    initOutletSelector: function () {
        const outletSelector = document.getElementById("outlet-selector");
        if (outletSelector) {
            outletSelector.addEventListener("change", function () {
                OutletHelper.handleOutletChange(this.value, true);
            });
        }
    },
};

// Auto-initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    OutletHelper.initOutletSelector();
});
