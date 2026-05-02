/**
 * Emergency Fix untuk Sparepart Alpine.js
 * Improved version with better error handling
 */

console.log("🚨 Loading emergency fix for sparepart...");

// Check if sparepartData is available - only create if missing
if (
    typeof sparepartData === "undefined" &&
    typeof window.sparepartData === "undefined"
) {
    console.log("⚠️ sparepartData not found, creating emergency version...");

    // Create emergency sparepartData function
    window.sparepartData = function () {
        return {
            // Basic data structure
            table: null,
            search: "",
            stats: { total: 0, tersedia: 0, minimum: 0, habis: 0 },
            showModal: false,
            showDetailModal: false,
            showAdjustModal: false,
            showExportModal: false,
            showPriceAdjustModal: false,
            modalTitle: "Tambah Sparepart",
            editMode: false,
            editId: null,
            loading: false,
            detailData: null,
            adjustData: null,
            adjustLogs: [],
            filteredAdjustLogs: [],
            priceAdjustData: null,
            priceChangeLogs: [],
            userRole: "",
            selectedItems: [],
            selectAll: false,
            showKaryawanDropdown: false,
            karyawanList: [],
            uniqueKaryawanInLogs: [],
            filters: {
                start_date: "",
                end_date: "",
                outlet_id: "",
            },
            logFilters: {
                start_date: "",
                end_date: "",
                kategori: "",
                karyawan: "",
            },
            logSortField: "created_at",
            logSortDirection: "desc",
            adjustForm: {
                tipe: "tambah",
                kategori: "",
                jumlah: 0,
                keterangan: "",
                id_karyawan: null,
                karyawan_search: "",
            },
            priceAdjustForm: {
                harga_baru: 0,
                keterangan: "",
            },
            exportForm: {
                format: "pdf",
                data_type: "all",
                include_history: "no",
                log_start_date: "",
                log_end_date: "",
                log_category: "",
                log_sort: "desc",
            },
            form: {
                outlet_id: "",
                kode_sparepart: "",
                nama_sparepart: "",
                merk: "",
                spesifikasi: "",
                harga: 0,
                stok: 0,
                stok_minimum: 0,
                satuan: "",
                is_active: 1,
                keterangan: "",
            },

            // Basic methods
            async init() {
                console.log("🚨 Emergency sparepartData initialized");
                await this.initDataTable();
                await this.loadStats();
                await this.generateKodeSparepart();
            },

            async initDataTable() {
                console.log("Initializing DataTable...");

                try {
                    if (typeof window.DataTableManager !== "undefined") {
                        this.table = await window.DataTableManager.init(
                            "#sparepart-table",
                            {
                                processing: true,
                                serverSide: false,
                                data: [],
                                columns: [
                                    {
                                        data: "checkbox",
                                        orderable: false,
                                        searchable: false,
                                        className: "text-center",
                                    },
                                    {
                                        data: "DT_RowIndex",
                                        orderable: false,
                                        searchable: false,
                                        className: "text-center",
                                    },
                                    { data: "kode_sparepart" },
                                    { data: "nama_sparepart" },
                                    { data: "merk" },
                                    {
                                        data: "harga_formatted",
                                        className: "text-right",
                                    },
                                    { data: "stok", className: "text-center" },
                                    {
                                        data: "stok_minimum",
                                        className: "text-center",
                                    },
                                    {
                                        data: "stok_status",
                                        className: "text-center",
                                    },
                                    {
                                        data: "status_badge",
                                        className: "text-center",
                                    },
                                    {
                                        data: "aksi",
                                        orderable: false,
                                        searchable: false,
                                        className: "text-center",
                                    },
                                ],
                                language: {
                                    processing: "Memuat...",
                                    search: "Cari:",
                                    lengthMenu: "Tampilkan _MENU_ data",
                                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                    infoEmpty:
                                        "Menampilkan 0 sampai 0 dari 0 data",
                                    zeroRecords:
                                        "Tidak ada data yang ditemukan",
                                    emptyTable: "Tidak ada data tersedia",
                                    paginate: {
                                        first: "Pertama",
                                        previous: "Sebelumnya",
                                        next: "Selanjutnya",
                                        last: "Terakhir",
                                    },
                                },
                            },
                        );
                    } else {
                        console.warn(
                            "DataTableManager not available, using basic initialization",
                        );
                        this.table = $("#sparepart-table").DataTable({
                            processing: true,
                            data: [],
                        });
                    }
                } catch (error) {
                    console.error("DataTable initialization error:", error);
                }
            },

            async loadStats() {
                this.stats = { total: 0, tersedia: 0, minimum: 0, habis: 0 };
            },

            openAddModal() {
                this.showModal = true;
                this.editMode = false;
                this.modalTitle = "Tambah Sparepart";
            },

            openEditModal(id) {
                this.showModal = true;
                this.editMode = true;
                this.editId = id;
                this.modalTitle = "Edit Sparepart";
            },

            openDetailModal(id) {
                this.showDetailModal = true;
            },

            openAdjustModal(id) {
                this.showAdjustModal = true;
            },

            openExportModal() {
                this.showExportModal = true;
            },

            openPriceAdjustModal(id) {
                this.showPriceAdjustModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            closeDetailModal() {
                this.showDetailModal = false;
            },

            closeAdjustModal() {
                this.showAdjustModal = false;
            },

            closeExportModal() {
                this.showExportModal = false;
            },

            closePriceAdjustModal() {
                this.showPriceAdjustModal = false;
            },

            applyFilters() {
                if (this.table && this.table.ajax) {
                    this.table.ajax.reload();
                }
            },

            clearFilters() {
                this.filters = {
                    start_date: "",
                    end_date: "",
                    outlet_id: "",
                };
                this.applyFilters();
            },

            toggleSelectAll() {
                const checkboxes = document.querySelectorAll(
                    ".sparepart-checkbox",
                );
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = this.selectAll;
                });
                this.updateSelectedItems();
            },

            updateSelectedItems() {
                const checkboxes = document.querySelectorAll(
                    ".sparepart-checkbox:checked",
                );
                this.selectedItems = Array.from(checkboxes).map(
                    (cb) => cb.value,
                );
                this.selectAll =
                    this.selectedItems.length > 0 &&
                    this.selectedItems.length ===
                        document.querySelectorAll(".sparepart-checkbox").length;
            },

            async saveSparepart() {
                this.loading = true;
                console.log("Saving sparepart...");

                try {
                    // Basic save logic - would need actual implementation
                    await new Promise((resolve) => setTimeout(resolve, 1000));
                    alert("Fitur save sedang dalam perbaikan");
                } catch (error) {
                    console.error("Save error:", error);
                    alert("Terjadi kesalahan saat menyimpan");
                } finally {
                    this.loading = false;
                }
            },

            async generateKodeSparepart() {
                this.form.kode_sparepart =
                    "SP" + Date.now().toString().slice(-4);
            },

            async bulkDelete() {
                if (this.selectedItems.length === 0) {
                    alert("Pilih minimal satu item untuk dihapus");
                    return;
                }

                if (
                    confirm(
                        `Yakin ingin menghapus ${this.selectedItems.length} sparepart?`,
                    )
                ) {
                    alert("Fitur bulk delete sedang dalam perbaikan");
                }
            },

            async processExport() {
                this.loading = true;
                try {
                    await new Promise((resolve) => setTimeout(resolve, 1000));
                    alert("Fitur export sedang dalam perbaikan");
                } finally {
                    this.loading = false;
                }
            },

            async saveAdjustment() {
                this.loading = true;
                try {
                    await new Promise((resolve) => setTimeout(resolve, 1000));
                    alert("Fitur adjustment sedang dalam perbaikan");
                } finally {
                    this.loading = false;
                }
            },

            async savePriceAdjustment() {
                this.loading = true;
                try {
                    await new Promise((resolve) => setTimeout(resolve, 1000));
                    alert("Fitur price adjustment sedang dalam perbaikan");
                } finally {
                    this.loading = false;
                }
            },

            // Additional helper methods
            updateKategoriOptions() {
                this.adjustForm.kategori = "";
            },

            updateKeteranganFromKategori() {
                // Update keterangan based on kategori
            },

            toggleHistoryFilters() {
                // Toggle history filters
            },

            filterLogs() {
                this.filteredAdjustLogs = [...this.adjustLogs];
            },

            clearLogFilters() {
                this.logFilters = {
                    start_date: "",
                    end_date: "",
                    kategori: "",
                    karyawan: "",
                };
                this.filterLogs();
            },

            sortLogs(field) {
                // Sort logs by field
            },

            updateUniqueKaryawanInLogs() {
                this.uniqueKaryawanInLogs = [];
            },

            async searchKaryawan() {
                this.karyawanList = [];
            },

            selectKaryawan(karyawan) {
                this.adjustForm.id_karyawan = karyawan.id;
                this.adjustForm.karyawan_search = karyawan.name;
                this.showKaryawanDropdown = false;
            },
        };
    };

    console.log("✅ Emergency sparepartData function created");
} else {
    console.log(
        "✅ sparepartData function already exists, skipping emergency creation",
    );
}

// Ensure formatCurrency is available
if (typeof window.formatCurrency === "undefined") {
    window.formatCurrency = function (amount) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };
    console.log("✅ Emergency formatCurrency function created");
}

// Global functions for DataTable action buttons
if (typeof window.viewDetail === "undefined") {
    window.viewDetail = function (id) {
        console.log("View detail:", id);
        alert("Fitur view detail sedang dalam perbaikan");
    };
}

if (typeof window.editSparepart === "undefined") {
    window.editSparepart = function (id) {
        console.log("Edit sparepart:", id);
        alert("Fitur edit sedang dalam perbaikan");
    };
}

if (typeof window.adjustStok === "undefined") {
    window.adjustStok = function (id) {
        console.log("Adjust stok:", id);
        alert("Fitur adjust stok sedang dalam perbaikan");
    };
}

if (typeof window.adjustPrice === "undefined") {
    window.adjustPrice = function (id) {
        console.log("Adjust price:", id);
        alert("Fitur adjust price sedang dalam perbaikan");
    };
}

if (typeof window.deleteSparepart === "undefined") {
    window.deleteSparepart = function (id) {
        if (confirm("Yakin ingin menghapus sparepart ini?")) {
            console.log("Delete sparepart:", id);
            alert("Fitur delete sedang dalam perbaikan");
        }
    };
}

console.log("🚨 Emergency fix loaded successfully");
