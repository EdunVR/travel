/**
 * Reusable Alpine.js Components for Finance Module
 * Export/Import functionality and notification system
 */

/**
 * Export Mixin - Provides common export functionality
 * Usage: Spread this into your Alpine component: ...financeExportMixin()
 */
function financeExportMixin() {
    return {
        isExporting: false,
        exportError: null,

        /**
         * Export data to XLSX format
         * @param {string} module - Module name (journal, accounting-book, fixed-assets, general-ledger)
         * @param {Object} filters - Filter parameters to apply
         * @param {string} filename - Optional custom filename
         */
        async exportToXLSX(module, filters = {}, filename = null) {
            this.isExporting = true;
            this.exportError = null;

            try {
                const queryParams = new URLSearchParams(filters).toString();
                const url = `/finance/${module}/export/xlsx${
                    queryParams ? "?" + queryParams : ""
                }`;

                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Export gagal: ${response.statusText}`);
                }

                const blob = await response.blob();
                const downloadFilename =
                    filename || `${module}_export_${new Date().getTime()}.xlsx`;
                this.downloadFile(blob, downloadFilename);

                this.showNotification(
                    "success",
                    "Data berhasil diekspor ke XLSX"
                );
            } catch (error) {
                console.error("Export XLSX error:", error);
                this.exportError = error.message;
                this.showNotification(
                    "error",
                    "Gagal mengekspor data: " + error.message
                );
            } finally {
                this.isExporting = false;
            }
        },

        /**
         * Export data to PDF format
         * @param {string} module - Module name (journal, accounting-book, fixed-assets, general-ledger)
         * @param {Object} filters - Filter parameters to apply
         * @param {string} filename - Optional custom filename
         */
        async exportToPDF(module, filters = {}, filename = null) {
            this.isExporting = true;
            this.exportError = null;

            try {
                const queryParams = new URLSearchParams(filters).toString();
                const url = `/finance/${module}/export/pdf${
                    queryParams ? "?" + queryParams : ""
                }`;

                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/pdf",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Export gagal: ${response.statusText}`);
                }

                const blob = await response.blob();
                const downloadFilename =
                    filename || `${module}_export_${new Date().getTime()}.pdf`;
                this.downloadFile(blob, downloadFilename);

                this.showNotification(
                    "success",
                    "Data berhasil diekspor ke PDF"
                );
            } catch (error) {
                console.error("Export PDF error:", error);
                this.exportError = error.message;
                this.showNotification(
                    "error",
                    "Gagal mengekspor data: " + error.message
                );
            } finally {
                this.isExporting = false;
            }
        },

        /**
         * Helper function to download a blob as a file
         * @param {Blob} blob - The blob to download
         * @param {string} filename - The filename for the download
         */
        downloadFile(blob, filename) {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        },
    };
}

/**
 * Import Mixin - Provides common import functionality
 * Usage: Spread this into your Alpine component: ...financeImportMixin()
 */
function financeImportMixin() {
    return {
        isUploading: false,
        isDragging: false,
        importFile: null,
        uploadProgress: 0,
        importResults: null,
        importError: null,

        /**
         * Handle file selection
         * @param {Event} event - File input change event
         */
        handleFileSelect(event) {
            const file = event.target.files[0];
            this.validateAndSetFile(file);
        },

        /**
         * Handle drag over event
         * @param {Event} event - Drag event
         */
        handleDragOver(event) {
            event.preventDefault();
            this.isDragging = true;
        },

        /**
         * Handle drag leave event
         */
        handleDragLeave() {
            this.isDragging = false;
        },

        /**
         * Handle file drop
         * @param {Event} event - Drop event
         */
        handleFileDrop(event) {
            event.preventDefault();
            this.isDragging = false;

            const file = event.dataTransfer.files[0];
            this.validateAndSetFile(file);
        },

        /**
         * Validate and set the import file
         * @param {File} file - The file to validate
         */
        validateAndSetFile(file) {
            if (!file) {
                return;
            }

            // Validate file type
            const validTypes = [
                "application/vnd.ms-excel",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "text/csv",
            ];

            if (
                !validTypes.includes(file.type) &&
                !file.name.match(/\.(xlsx|xls|csv)$/i)
            ) {
                this.showNotification(
                    "error",
                    "Format file tidak valid. Gunakan file Excel (.xlsx, .xls) atau CSV"
                );
                return;
            }

            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                this.showNotification(
                    "error",
                    "Ukuran file terlalu besar. Maksimal 5MB"
                );
                return;
            }

            this.importFile = file;
            this.importError = null;
            this.importResults = null;
        },

        /**
         * Upload and import the file
         * @param {string} module - Module name (journal, fixed-assets)
         * @param {Object} additionalData - Additional data to send with the import
         */
        async uploadFile(module, additionalData = {}) {
            if (!this.importFile) {
                this.showNotification("error", "Pilih file terlebih dahulu");
                return;
            }

            this.isUploading = true;
            this.uploadProgress = 0;
            this.importError = null;
            this.importResults = null;

            const formData = new FormData();
            formData.append("file", this.importFile);

            // Add additional data
            Object.keys(additionalData).forEach((key) => {
                formData.append(key, additionalData[key]);
            });

            try {
                const xhr = new XMLHttpRequest();

                // Track upload progress
                xhr.upload.addEventListener("progress", (e) => {
                    if (e.lengthComputable) {
                        this.uploadProgress = Math.round(
                            (e.loaded / e.total) * 100
                        );
                    }
                });

                // Handle completion
                const uploadPromise = new Promise((resolve, reject) => {
                    xhr.addEventListener("load", () => {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve(JSON.parse(xhr.responseText));
                        } else {
                            reject(
                                new Error(`Upload gagal: ${xhr.statusText}`)
                            );
                        }
                    });

                    xhr.addEventListener("error", () => {
                        reject(new Error("Terjadi kesalahan saat upload"));
                    });
                });

                // Send request
                xhr.open("POST", `/finance/${module}/import`);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                xhr.setRequestHeader(
                    "X-CSRF-TOKEN",
                    document.querySelector('meta[name="csrf-token"]').content
                );
                xhr.send(formData);

                const result = await uploadPromise;

                if (result.success) {
                    this.importResults = {
                        success: true,
                        imported_count: result.imported_count || 0,
                        skipped_count: result.skipped_count || 0,
                        errors: result.errors || [],
                    };

                    const message =
                        `Berhasil mengimpor ${result.imported_count} data` +
                        (result.skipped_count > 0
                            ? `, ${result.skipped_count} data dilewati`
                            : "");

                    this.showNotification("success", message);

                    // Reset file after successful import
                    this.importFile = null;

                    // Return true to indicate success (caller can reload data)
                    return true;
                } else {
                    this.importError = result.message || "Import gagal";
                    this.importResults = {
                        success: false,
                        errors: result.errors || [],
                    };
                    this.showNotification(
                        "error",
                        result.message || "Import gagal"
                    );
                    return false;
                }
            } catch (error) {
                console.error("Import error:", error);
                this.importError = error.message;
                this.showNotification(
                    "error",
                    "Gagal mengimpor data: " + error.message
                );
                return false;
            } finally {
                this.isUploading = false;
                this.uploadProgress = 0;
            }
        },

        /**
         * Clear import file and results
         */
        clearImport() {
            this.importFile = null;
            this.importResults = null;
            this.importError = null;
            this.uploadProgress = 0;
        },

        /**
         * Download import template
         * @param {string} module - Module name (journal, fixed-assets)
         */
        async downloadTemplate(module) {
            try {
                const response = await fetch(
                    `/finance/${module}/import/template`,
                    {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error("Gagal mengunduh template");
                }

                const blob = await response.blob();
                this.downloadFile(blob, `${module}_import_template.xlsx`);
                this.showNotification("success", "Template berhasil diunduh");
            } catch (error) {
                console.error("Download template error:", error);
                this.showNotification(
                    "error",
                    "Gagal mengunduh template: " + error.message
                );
            }
        },
    };
}

/**
 * Notification System - Toast notifications
 * Usage: Call window.showNotification(type, message) from anywhere
 */
document.addEventListener("alpine:init", () => {
    Alpine.store("notifications", {
        items: [],
        nextId: 1,

        /**
         * Add a notification
         * @param {string} type - Notification type: 'success', 'error', 'info', 'warning'
         * @param {string} message - Notification message
         * @param {number} duration - Auto-dismiss duration in ms (0 = no auto-dismiss)
         */
        add(type, message, duration = 5000) {
            const id = this.nextId++;
            const notification = {
                id,
                type,
                message,
                visible: true,
            };

            this.items.push(notification);

            // Auto-dismiss after duration
            if (duration > 0) {
                setTimeout(() => {
                    this.remove(id);
                }, duration);
            }

            return id;
        },

        /**
         * Remove a notification
         * @param {number} id - Notification ID
         */
        remove(id) {
            const index = this.items.findIndex((item) => item.id === id);
            if (index !== -1) {
                this.items[index].visible = false;
                // Remove from array after animation
                setTimeout(() => {
                    this.items.splice(index, 1);
                }, 300);
            }
        },

        /**
         * Clear all notifications
         */
        clear() {
            this.items = [];
        },
    });
});

/**
 * Global notification helper function
 * @param {string} type - Notification type: 'success', 'error', 'info', 'warning'
 * @param {string} message - Notification message
 * @param {number} duration - Auto-dismiss duration in ms (default: 5000)
 */
window.showNotification = function (type, message, duration = 5000) {
    if (window.Alpine && Alpine.store("notifications")) {
        return Alpine.store("notifications").add(type, message, duration);
    } else {
        console.warn("Alpine.js notifications store not available");
        console.log(`[${type.toUpperCase()}] ${message}`);
    }
};
