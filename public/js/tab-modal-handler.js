/**
 * ========================================
 * TAB MODAL HANDLER
 * Menangani modal agar hanya menutupi tab yang aktif
 * ========================================
 */

(function () {
    "use strict";

    // Wait for DOM to be ready
    document.addEventListener("DOMContentLoaded", function () {
        initializeTabModalHandler();
    });

    function initializeTabModalHandler() {
        // Override Bootstrap modal show method
        if (typeof $.fn.modal !== "undefined") {
            const originalShow = $.fn.modal.Constructor.prototype.show;

            $.fn.modal.Constructor.prototype.show = function () {
                // Call original show
                originalShow.apply(this, arguments);

                // Get active tab
                const activeTabContent = document.querySelector(
                    '.tab-content-wrapper[x-show="activeTab === tab.id"]:not([style*="display: none"])'
                );

                if (activeTabContent) {
                    // Move modal and backdrop to active tab
                    const modal = this._element;
                    const backdrop = document.querySelector(".modal-backdrop");

                    if (modal && !activeTabContent.contains(modal)) {
                        // Create modal container in tab if not exists
                        let modalContainer = activeTabContent.querySelector(
                            ".tab-modal-container"
                        );
                        if (!modalContainer) {
                            modalContainer = document.createElement("div");
                            modalContainer.className = "tab-modal-container";
                            activeTabContent.appendChild(modalContainer);
                        }

                        // Move modal to tab container
                        modalContainer.appendChild(modal);

                        // Move backdrop to tab container
                        if (backdrop) {
                            modalContainer.appendChild(backdrop);
                        }
                    }
                }
            };
        }

        // Handle modal events
        $(document).on("show.bs.modal", ".modal", function (e) {
            const modal = $(this);
            const activeTab = getActiveTab();

            if (activeTab) {
                // Add data attribute to track which tab owns this modal
                modal.attr("data-tab-owner", activeTab.id);
            }
        });

        $(document).on("hidden.bs.modal", ".modal", function (e) {
            // Clean up modal backdrop
            $(".modal-backdrop").remove();
            $("body").removeClass("modal-open");
        });

        // Prevent modal from opening in wrong tab
        $(document).on("show.bs.modal", ".modal", function (e) {
            const modal = $(this);
            const modalTabOwner = modal.attr("data-tab-owner");
            const activeTab = getActiveTab();

            if (modalTabOwner && activeTab && modalTabOwner !== activeTab.id) {
                // Modal belongs to different tab, prevent opening
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }

    function getActiveTab() {
        // Get active tab from Alpine.js data
        const mainArea = document.querySelector('[x-data*="tabSystem"]');
        if (mainArea && mainArea.__x) {
            const data = mainArea.__x.$data;
            const activeTabId = data.activeTab;
            return data.tabs.find((t) => t.id === activeTabId);
        }
        return null;
    }

    // Global function to open modal in current tab
    window.openModalInTab = function (modalId) {
        const modal = $(modalId);
        const activeTab = getActiveTab();

        if (activeTab) {
            modal.attr("data-tab-owner", activeTab.id);
        }

        modal.modal("show");
    };

    // Global function to reload current tab
    window.reloadCurrentTab = function () {
        const mainArea = document.querySelector('[x-data*="tabSystem"]');
        if (mainArea && mainArea.__x) {
            const component = mainArea.__x.$data;
            if (typeof component.reloadTab === "function") {
                component.reloadTab();
            }
        }
    };

    // Global function to open URL in active tab (updated behavior)
    window.openInNewTab = function (url, title = "Halaman Baru") {
        const mainArea = document.querySelector('[x-data*="tabSystem"]');
        if (mainArea && mainArea.__x) {
            const component = mainArea.__x.$data;
            if (typeof component.loadInActiveTab === "function") {
                component.loadInActiveTab(url, title);
            }
        }
    };

    // Global function to create new empty tab
    window.createNewTab = function () {
        const mainArea = document.querySelector('[x-data*="tabSystem"]');
        if (mainArea && mainArea.__x) {
            const component = mainArea.__x.$data;
            if (typeof component.createNewTab === "function") {
                component.createNewTab();
            }
        }
    };

    // Handle form submissions in tabs
    $(document).on("submit", "form[data-tab-submit]", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr("action");
        const method = form.attr("method") || "POST";
        const data = form.serialize();

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function (response) {
                // Show success message
                if (response.message) {
                    showNotification(response.message, "success");
                }

                // Reload current tab if needed
                if (form.attr("data-reload-on-success") === "true") {
                    setTimeout(() => {
                        reloadCurrentTab();
                    }, 1000);
                }

                // Close modal if form is in modal
                const modal = form.closest(".modal");
                if (modal.length) {
                    modal.modal("hide");
                }
            },
            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message || "Terjadi kesalahan";
                showNotification(message, "error");
            },
        });
    });

    // Notification helper
    function showNotification(message, type = "info") {
        // Use existing notification system if available
        if (typeof window.showNotification === "function") {
            window.showNotification(message, type);
            return;
        }

        // Fallback to alert
        alert(message);
    }

    // Handle DataTable reloads in tabs
    window.reloadDataTableInTab = function (tableId) {
        const table = $(tableId).DataTable();
        if (table) {
            table.ajax.reload(null, false); // false = keep current page
        }
    };

    // Prevent tab switching when modal is open
    $(document).on("show.bs.modal", ".modal", function () {
        // Disable tab switching
        const tabButtons = document.querySelectorAll(
            '[x-data*="tabSystem"] button[\\@click*="switchTab"]'
        );
        tabButtons.forEach((btn) => {
            btn.setAttribute("data-modal-open", "true");
            btn.style.pointerEvents = "none";
            btn.style.opacity = "0.5";
        });
    });

    $(document).on("hidden.bs.modal", ".modal", function () {
        // Re-enable tab switching
        const tabButtons = document.querySelectorAll(
            '[x-data*="tabSystem"] button[data-modal-open="true"]'
        );
        tabButtons.forEach((btn) => {
            btn.removeAttribute("data-modal-open");
            btn.style.pointerEvents = "";
            btn.style.opacity = "";
        });
    });
})();
