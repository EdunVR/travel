// Sparepart Management JavaScript
// Ensure function is available globally BEFORE Alpine.js initializes
console.log("🔧 Loading sparepart.js...");

// Store Alpine component reference globally
let sparepartComponent = null;

// Define sparepartData function globally
window.sparepartData = function sparepartData() {
    return {
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

        async init() {
            // Store component reference
            sparepartComponent = this;

            // Get user role from meta tag or global variable
            const userRoleMeta = document.querySelector(
                'meta[name="user-role"]',
            );
            this.userRole = userRoleMeta
                ? userRoleMeta.content
                : window.userRole || "";

            this.initDataTable();
            await this.loadStats();
            await this.generateKodeSparepart();
        },

        async loadStats() {
            try {
                const response = await fetch(window.sparepartRoutes.data);
                const data = await response.json();

                if (data.data) {
                    this.stats.total = data.recordsTotal || 0;

                    // Count status from data
                    let tersedia = 0;
                    let minimum = 0;
                    let habis = 0;

                    data.data.forEach((item) => {
                        if (item.stok <= 0) {
                            habis++;
                        } else if (item.stok <= item.stok_minimum) {
                            minimum++;
                        } else {
                            tersedia++;
                        }
                    });

                    this.stats.tersedia = tersedia;
                    this.stats.minimum = minimum;
                    this.stats.habis = habis;
                }
            } catch (error) {
                console.error("Error loading stats:", error);
            }
        },

        initDataTable() {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                $("#sparepart-table").DataTable().destroy();
            }

            this.table = $("#sparepart-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: window.sparepartRoutes.data,
                    data: (d) => {
                        d.outlet_id = this.filters.outlet_id;
                        d.start_date = this.filters.start_date;
                        d.end_date = this.filters.end_date;
                    },
                },
                columns: [
                    {
                        data: "checkbox",
                        name: "checkbox",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    { data: "kode_sparepart", name: "kode_sparepart" },
                    { data: "nama_sparepart", name: "nama_sparepart" },
                    { data: "merk", name: "merk" },
                    {
                        data: "harga_formatted",
                        name: "harga",
                        orderable: true,
                        className: "text-right",
                    },
                    { data: "stok", name: "stok", className: "text-center" },
                    {
                        data: "stok_minimum",
                        name: "stok_minimum",
                        className: "text-center",
                    },
                    {
                        data: "stok_status",
                        name: "stok_status",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    {
                        data: "status_badge",
                        name: "is_active",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    {
                        data: "aksi",
                        name: "aksi",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                ],
                order: [[1, "asc"]],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"],
                ],
                language: {
                    processing: "Memuat...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir",
                    },
                },
                dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
            });
        },

        async openAddModal() {
            this.editMode = false;
            this.editId = null;
            this.modalTitle = "Tambah Sparepart";
            this.resetForm();
            await this.generateKodeSparepart();
            this.showModal = true;
        },

        async generateKodeSparepart() {
            if (!this.editMode) {
                try {
                    const response = await fetch(
                        window.sparepartRoutes.generateKode,
                    );
                    const data = await response.json();

                    if (data.success) {
                        this.form.kode_sparepart = data.kode;
                    } else {
                        this.form.kode_sparepart = "SP0001";
                    }
                } catch (error) {
                    console.error("Error generating code:", error);
                    this.form.kode_sparepart = "SP0001";
                }
            }
        },

        openEditModal(id) {
            this.editMode = true;
            this.editId = id;
            this.modalTitle = "Edit Sparepart";
            this.loadSparepartData(id);
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.detailData = null;
        },

        async openAdjustModal(id) {
            try {
                // Load sparepart data
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id),
                );
                const data = await response.json();

                if (data.success) {
                    this.adjustData = data.data;

                    // Load logs
                    const logsResponse = await fetch(
                        window.sparepartRoutes.logs.replace(":id", id),
                    );
                    const logsData = await logsResponse.json();

                    if (logsData.success && logsData.data) {
                        this.adjustLogs = logsData.data;
                        this.filteredAdjustLogs = [...logsData.data];
                        this.updateUniqueKaryawanInLogs();
                    } else {
                        this.adjustLogs = [];
                        this.filteredAdjustLogs = [];
                        this.uniqueKaryawanInLogs = [];
                    }

                    // Reset form
                    this.adjustForm = {
                        tipe: "tambah",
                        kategori: "",
                        jumlah: 0,
                        keterangan: "",
                        id_karyawan: null,
                        karyawan_search: "",
                    };

                    // Reset log filters
                    this.logFilters = {
                        start_date: "",
                        end_date: "",
                        kategori: "",
                        karyawan: "",
                    };

                    this.showAdjustModal = true;
                }
            } catch (error) {
                console.error("Error loading adjust modal:", error);
                alert("Gagal memuat data penyesuaian stok");
            }
        },

        closeAdjustModal() {
            this.showAdjustModal = false;
            this.adjustData = null;
            this.adjustLogs = [];
            this.filteredAdjustLogs = [];
            this.uniqueKaryawanInLogs = [];
            this.showKaryawanDropdown = false;
            this.adjustForm = {
                tipe: "tambah",
                kategori: "",
                jumlah: 0,
                keterangan: "",
                id_karyawan: null,
                karyawan_search: "",
            };
            this.logFilters = {
                start_date: "",
                end_date: "",
                kategori: "",
                karyawan: "",
            };
        },

        async saveAdjustment() {
            if (!this.adjustForm.kategori) {
                alert("Kategori harus dipilih");
                return;
            }

            if (!this.adjustForm.jumlah || this.adjustForm.jumlah <= 0) {
                alert("Jumlah harus lebih dari 0");
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(
                    window.sparepartRoutes.adjust.replace(
                        ":id",
                        this.adjustData.id_sparepart,
                    ),
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                        body: JSON.stringify(this.adjustForm),
                    },
                );

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closeAdjustModal();
                    this.table.ajax.reload(null, false);
                    await this.loadStats();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Error saving adjustment:", error);
                alert("Terjadi kesalahan saat menyimpan penyesuaian");
            } finally {
                this.loading = false;
            }
        },

        async openDetailModal(id) {
            try {
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id),
                );
                const data = await response.json();

                if (data.success) {
                    this.detailData = data.data;
                    this.showDetailModal = true;
                }
            } catch (error) {
                console.error("Error loading detail:", error);
                alert("Gagal memuat detail sparepart");
            }
        },

        resetForm() {
            this.form = {
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
            };
        },

        async loadSparepartData(id) {
            try {
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id),
                );
                const data = await response.json();

                if (data.success) {
                    this.form = {
                        outlet_id: data.data.outlet_id,
                        kode_sparepart: data.data.kode_sparepart,
                        nama_sparepart: data.data.nama_sparepart,
                        merk: data.data.merk || "",
                        spesifikasi: data.data.spesifikasi || "",
                        harga: data.data.harga,
                        stok: data.data.stok,
                        stok_minimum: data.data.stok_minimum,
                        satuan: data.data.satuan,
                        is_active: data.data.is_active ? 1 : 0,
                        keterangan: data.data.keterangan || "",
                    };
                }
            } catch (error) {
                console.error("Error loading sparepart:", error);
                alert("Gagal memuat data sparepart");
            }
        },

        async saveSparepart() {
            this.loading = true;

            try {
                const url = this.editMode
                    ? window.sparepartRoutes.update.replace(":id", this.editId)
                    : window.sparepartRoutes.store;

                const method = this.editMode ? "PUT" : "POST";

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify(this.form),
                });

                // Check if response is JSON
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Non-JSON response received:", text);

                    // Check for common error patterns
                    if (text.includes("CSRF token mismatch")) {
                        alert(
                            "CSRF token mismatch. Silakan refresh halaman dan coba lagi.",
                        );
                        window.location.reload();
                        return;
                    } else if (text.includes("419")) {
                        alert(
                            "Session expired. Silakan refresh halaman dan coba lagi.",
                        );
                        window.location.reload();
                        return;
                    } else if (text.includes("<!DOCTYPE")) {
                        alert(
                            "Terjadi kesalahan server. Silakan coba lagi atau hubungi administrator.",
                        );
                        return;
                    }

                    throw new Error("Server returned non-JSON response");
                }

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closeModal();
                    this.table.ajax.reload(null, false);
                    await this.loadStats();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Error saving sparepart:", error);
                alert(
                    "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.",
                );
            } finally {
                this.loading = false;
            }
        },

        showToast(message, type = "success") {
            alert(message);
        },

        // Filter methods
        applyFilters() {
            this.table.ajax.reload();
        },

        clearFilters() {
            this.filters = {
                start_date: "",
                end_date: "",
                outlet_id: "",
            };
            this.applyFilters();
        },

        // Checkbox methods
        toggleSelectAll() {
            const checkboxes = document.querySelectorAll(".sparepart-checkbox");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = this.selectAll;
            });
            this.updateSelectedItems();
        },

        updateSelectedItems() {
            const checkboxes = document.querySelectorAll(
                ".sparepart-checkbox:checked",
            );
            this.selectedItems = Array.from(checkboxes).map((cb) => cb.value);
            this.selectAll =
                this.selectedItems.length > 0 &&
                this.selectedItems.length ===
                    document.querySelectorAll(".sparepart-checkbox").length;
        },

        // Export methods
        openExportModal() {
            this.exportForm = {
                format: "pdf",
                data_type: this.selectedItems.length > 0 ? "selected" : "all",
                include_history: "no",
                log_start_date: "",
                log_end_date: "",
                log_category: "",
                log_sort: "desc",
            };
            this.showExportModal = true;
        },

        closeExportModal() {
            this.showExportModal = false;
        },

        async processExport() {
            if (
                this.exportForm.data_type === "selected" &&
                this.selectedItems.length === 0
            ) {
                alert("Pilih minimal satu item untuk diexport");
                return;
            }

            this.loading = true;

            try {
                const formData = {
                    ...this.exportForm,
                    ids: this.selectedItems,
                    // Include current table sorting and filtering
                    current_order: this.table ? this.table.order() : [],
                    current_search: this.table ? this.table.search() : "",
                    filters: this.filters,
                };

                if (this.exportForm.format === "pdf") {
                    // For PDF stream, create form and submit to open in new tab
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = window.sparepartRoutes.export;
                    form.target = "_blank";

                    // Add CSRF token
                    const csrfInput = document.createElement("input");
                    csrfInput.type = "hidden";
                    csrfInput.name = "_token";
                    csrfInput.value = document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content;
                    form.appendChild(csrfInput);

                    // Add all form data as hidden inputs
                    Object.keys(formData).forEach((key) => {
                        const input = document.createElement("input");
                        input.type = "hidden";
                        input.name = key;
                        if (Array.isArray(formData[key])) {
                            input.value = JSON.stringify(formData[key]);
                        } else if (typeof formData[key] === "object") {
                            input.value = JSON.stringify(formData[key]);
                        } else {
                            input.value = formData[key];
                        }
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } else {
                    // For Excel, use fetch and download
                    const response = await fetch(
                        window.sparepartRoutes.export,
                        {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                            },
                            body: JSON.stringify(formData),
                        },
                    );

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = `sparepart-export-${new Date()
                        .toISOString()
                        .slice(0, 10)}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                }

                this.closeExportModal();
                alert("Export berhasil");
            } catch (error) {
                console.error("Error exporting:", error);
                alert("Gagal export data");
            } finally {
                this.loading = false;
            }
        },

        // Bulk delete
        async bulkDelete() {
            if (this.selectedItems.length === 0) {
                alert("Pilih minimal satu item untuk dihapus");
                return;
            }

            if (
                !confirm(
                    `Yakin ingin menghapus ${this.selectedItems.length} sparepart?\n\nData yang sudah dihapus tidak dapat dikembalikan.`,
                )
            ) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(
                    window.sparepartRoutes.bulkDelete,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                        body: JSON.stringify({ ids: this.selectedItems }),
                    },
                );

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.selectedItems = [];
                    this.selectAll = false;
                    this.table.ajax.reload(null, false);
                    await this.loadStats();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Error bulk deleting:", error);
                alert("Terjadi kesalahan saat menghapus data");
            } finally {
                this.loading = false;
            }
        },

        // Price adjustment methods
        async openPriceAdjustModal(id) {
            try {
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id),
                );
                const data = await response.json();

                if (data.success) {
                    this.priceAdjustData = data.data;
                    this.priceAdjustForm = {
                        harga_baru: data.data.harga,
                        keterangan: "",
                    };

                    // Load price change logs
                    const logsResponse = await fetch(
                        window.sparepartRoutes.logs.replace(":id", id),
                    );
                    const logsData = await logsResponse.json();

                    if (logsData.success && logsData.data) {
                        // Filter only price change logs
                        this.priceChangeLogs = logsData.data.filter(
                            (log) => log.tipe_perubahan === "harga",
                        );
                    } else {
                        this.priceChangeLogs = [];
                    }

                    this.showPriceAdjustModal = true;
                }
            } catch (error) {
                console.error("Error loading price adjust modal:", error);
                alert("Gagal memuat data penyesuaian harga");
            }
        },

        closePriceAdjustModal() {
            this.showPriceAdjustModal = false;
            this.priceAdjustData = null;
            this.priceChangeLogs = [];
            this.priceAdjustForm = {
                harga_baru: 0,
                keterangan: "",
            };
        },

        async savePriceAdjustment() {
            if (
                !this.priceAdjustForm.harga_baru ||
                this.priceAdjustForm.harga_baru <= 0
            ) {
                alert("Harga baru harus lebih dari 0");
                return;
            }

            if (!this.priceAdjustForm.keterangan) {
                alert("Keterangan harus diisi");
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(
                    window.sparepartRoutes.adjustPrice.replace(
                        ":id",
                        this.priceAdjustData.id_sparepart,
                    ),
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                        body: JSON.stringify(this.priceAdjustForm),
                    },
                );

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closePriceAdjustModal();
                    this.table.ajax.reload(null, false);
                    await this.loadStats();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Error saving price adjustment:", error);
                alert("Terjadi kesalahan saat menyimpan penyesuaian harga");
            } finally {
                this.loading = false;
            }
        },

        // Karyawan search methods
        async searchKaryawan() {
            if (this.adjustForm.karyawan_search.length < 2) {
                this.karyawanList = [];
                this.showKaryawanDropdown = false;
                return;
            }

            try {
                const response = await fetch(
                    `${
                        window.sparepartRoutes.searchKaryawan
                    }?search=${encodeURIComponent(
                        this.adjustForm.karyawan_search,
                    )}`,
                );
                const data = await response.json();

                if (data.success) {
                    this.karyawanList = data.data;
                    this.showKaryawanDropdown = true;
                }
            } catch (error) {
                console.error("Error searching karyawan:", error);
            }
        },

        selectKaryawan(karyawan) {
            this.adjustForm.id_karyawan = karyawan.id;
            this.adjustForm.karyawan_search = karyawan.name;
            this.showKaryawanDropdown = false;
        },

        updateKategoriOptions() {
            this.adjustForm.kategori = "";
            this.adjustForm.keterangan = "";
        },

        updateKeteranganFromKategori() {
            if (this.adjustForm.tipe === "tambah" && this.adjustForm.kategori) {
                if (this.adjustForm.kategori === "service") {
                    this.adjustForm.keterangan = "Pengembalian dari Service";
                } else if (this.adjustForm.kategori === "produksi") {
                    this.adjustForm.keterangan = "Pengembalian dari Produksi";
                } else {
                    this.adjustForm.keterangan = "";
                }
            } else {
                this.adjustForm.keterangan = "";
            }
        },

        toggleHistoryFilters() {
            // Function to show/hide history filters based on selection
        },

        // Log filtering and sorting methods
        filterLogs() {
            let filtered = [...this.adjustLogs];

            if (this.logFilters.start_date) {
                filtered = filtered.filter(
                    (log) =>
                        new Date(log.created_at) >=
                        new Date(this.logFilters.start_date),
                );
            }

            if (this.logFilters.end_date) {
                filtered = filtered.filter(
                    (log) =>
                        new Date(log.created_at) <=
                        new Date(this.logFilters.end_date + " 23:59:59"),
                );
            }

            if (this.logFilters.kategori) {
                filtered = filtered.filter(
                    (log) => log.kategori === this.logFilters.kategori,
                );
            }

            if (this.logFilters.karyawan) {
                filtered = filtered.filter(
                    (log) =>
                        log.karyawan &&
                        log.karyawan.id == this.logFilters.karyawan,
                );
            }

            this.filteredAdjustLogs = filtered;
            this.sortLogs(this.logSortField, false);
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

        sortLogs(field, toggleDirection = true) {
            if (toggleDirection && this.logSortField === field) {
                this.logSortDirection =
                    this.logSortDirection === "asc" ? "desc" : "asc";
            } else {
                this.logSortField = field;
                if (!toggleDirection) {
                    // Keep current direction
                } else {
                    this.logSortDirection = "desc";
                }
            }

            this.filteredAdjustLogs.sort((a, b) => {
                let aVal = a[field];
                let bVal = b[field];

                if (field === "created_at") {
                    aVal = new Date(aVal);
                    bVal = new Date(bVal);
                } else if (typeof aVal === "string") {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }

                if (this.logSortDirection === "asc") {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
        },

        updateUniqueKaryawanInLogs() {
            const karyawanMap = new Map();
            this.adjustLogs.forEach((log) => {
                if (log.karyawan) {
                    karyawanMap.set(log.karyawan.id, log.karyawan);
                }
            });
            this.uniqueKaryawanInLogs = Array.from(karyawanMap.values());
        },
    };
};

// Ensure the function is available globally
if (typeof window !== "undefined") {
    window.sparepartData = window.sparepartData || sparepartData;
}

console.log("✅ sparepartData function defined globally");

// Global functions for DataTables action buttons
function viewDetail(id) {
    if (sparepartComponent && sparepartComponent.openDetailModal) {
        sparepartComponent.openDetailModal(id);
    }
}

function editSparepart(id) {
    if (sparepartComponent && sparepartComponent.openEditModal) {
        sparepartComponent.openEditModal(id);
    }
}

function adjustStok(id) {
    if (sparepartComponent && sparepartComponent.openAdjustModal) {
        sparepartComponent.openAdjustModal(id);
    }
}

function adjustPrice(id) {
    if (sparepartComponent && sparepartComponent.openPriceAdjustModal) {
        sparepartComponent.openPriceAdjustModal(id);
    }
}

function deleteSparepart(id) {
    if (
        !confirm(
            "Yakin ingin menghapus sparepart ini?\n\nData yang sudah dihapus tidak dapat dikembalikan.",
        )
    ) {
        return;
    }

    fetch(window.sparepartRoutes.destroy.replace(":id", id), {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.message);
                const table = $("#sparepart-table").DataTable();
                table.ajax.reload(null, false);

                // Reload stats
                if (sparepartComponent && sparepartComponent.loadStats) {
                    sparepartComponent.loadStats();
                }
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menghapus sparepart");
        });
}

// Event listener for checkbox changes
$(document).on("change", ".sparepart-checkbox", function () {
    if (sparepartComponent && sparepartComponent.updateSelectedItems) {
        sparepartComponent.updateSelectedItems();
    }
});

// Event listener for clicking outside karyawan dropdown
$(document).on("click", function (e) {
    if (sparepartComponent && !$(e.target).closest(".relative").length) {
        sparepartComponent.showKaryawanDropdown = false;
    }
});
