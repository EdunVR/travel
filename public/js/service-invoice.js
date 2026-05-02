// Service Invoice JavaScript
let itemIndex = 0;
let currentClosingType = "";
let currentJenisService = "";
const baseUrl = window.location.origin;

// Helper Functions
function parseNumber(formattedNumber) {
    if (typeof formattedNumber === "number") {
        return formattedNumber;
    }
    if (typeof formattedNumber !== "string") {
        return 0;
    }
    const cleanNumber = formattedNumber.toString().replace(/\./g, "");
    return parseInt(cleanNumber) || 0;
}

function formatNumber(number) {
    const num = parseInt(number) || 0;
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatCurrency(input) {
    let value = input.value.replace(/\./g, "");
    if (!isNaN(value)) {
        const intValue = parseInt(value) || 0;
        input.value = formatNumber(intValue);
    }
}

// Add Item Row Function
function addItemRow(item) {
    const rowCount = document.querySelectorAll("#items-container tr").length;

    const harga = parseInt(item.harga) || 0;
    const diskon = parseInt(item.diskon) || 0;
    const subtotal = parseInt(item.subtotal) || 0;

    const isOngkosKirim = item.tipe === "ongkir";
    const isSparepart = item.tipe === "sparepart";
    const jenisKendaraan = item.jenis_kendaraan || "";
    const kodeSparepart = item.kode_sparepart || "";

    const row = document.createElement("tr");
    row.innerHTML = `
        <td class="p-2 border">${rowCount + 1}</td>
        <td class="p-2 border">
            <input type="text" name="items[deskripsi][]" class="w-full px-2 py-1 border rounded" value="${
                item.deskripsi || ""
            }" required>
        </td>
        <td class="p-2 border">
            ${
                isOngkosKirim
                    ? `<select name="items[jenis_kendaraan][]" class="w-full px-2 py-1 border rounded jenis-kendaraan" required>
                    <option value="">Pilih Kendaraan</option>
                    <option value="mobil" ${
                        jenisKendaraan === "mobil" ? "selected" : ""
                    }>Mobil</option>
                    <option value="motor" ${
                        jenisKendaraan === "motor" ? "selected" : ""
                    }>Motor</option>
                </select>
                <input type="hidden" name="items[keterangan][]" value="Menggunakan ${
                    jenisKendaraan || "mobil"
                }">`
                    : `<input type="text" name="items[keterangan][]" class="w-full px-2 py-1 border rounded" value="${
                          item.keterangan || ""
                      }">`
            }
            <input type="hidden" name="items[is_sparepart][]" value="${
                isSparepart ? "1" : "0"
            }">
            <input type="hidden" name="items[kode_sparepart][]" value="${
                kodeSparepart || ""
            }">
            <input type="hidden" name="items[id_sparepart][]" value="${
                item.id_sparepart || ""
            }">
        </td>
        <td class="p-2 border">
            <input type="number" name="items[kuantitas][]" class="w-full px-2 py-1 text-center border rounded quantity" value="${
                parseInt(item.kuantitas) || 1
            }" min="1" required>
        </td>
        <td class="p-2 border">
            <input type="text" name="items[satuan][]" class="w-full px-2 py-1 border rounded" value="${
                item.satuan || ""
            }">
        </td>
        <td class="p-2 border">
            <input type="text" name="items[harga][]" class="w-full px-2 py-1 text-right border rounded price" value="${formatNumber(
                harga
            )}" required>
        </td>
        <td class="p-2 border">
            <input type="text" name="items[diskon][]" class="w-full px-2 py-1 text-right border rounded discount" value="${formatNumber(
                diskon
            )}" placeholder="0">
        </td>
        <td class="p-2 border">
            <input type="text" name="items[subtotal][]" class="w-full px-2 py-1 text-right border rounded subtotal" value="${formatNumber(
                subtotal
            )}" readonly>
        </td>
        <td class="p-2 text-center border">
            <input type="hidden" name="items[tipe][]" value="${
                item.tipe || "lainnya"
            }">
            <input type="hidden" name="items[id_produk][]" value="${
                item.id_produk || ""
            }">
            <button type="button" class="px-2 py-1 text-white bg-red-600 rounded hover:bg-red-700 remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    document.getElementById("items-container").appendChild(row);

    // Event handlers
    row.querySelectorAll(".quantity, .price, .discount").forEach((input) => {
        input.addEventListener("input", function () {
            updateRowTotal(row);
        });
    });

    row.querySelectorAll(".price, .discount").forEach((input) => {
        input.addEventListener("blur", function () {
            formatCurrency(this);
        });
    });

    row.querySelector(".remove-row").addEventListener("click", function () {
        row.remove();
        updateTotal();
        renumberRows();
    });

    // Handle jenis kendaraan change
    if (isOngkosKirim) {
        const jenisSelect = row.querySelector(".jenis-kendaraan");
        const keteranganInput = row.querySelector(
            'input[name="items[keterangan][]"]'
        );

        jenisSelect.addEventListener("change", function () {
            const jenis = this.value;
            keteranganInput.value = jenis
                ? "Menggunakan " + jenis
                : "Menggunakan mobil";
        });
    }

    updateTotal();
}

function updateRowTotal(row) {
    let quantity = parseInt(row.querySelector(".quantity").value) || 0;
    let price = parseNumber(row.querySelector(".price").value);
    let discount = parseNumber(row.querySelector(".discount").value) || 0;

    let hargaSetelahDiskon = price - discount;
    if (hargaSetelahDiskon < 0) hargaSetelahDiskon = 0;

    let subtotal = quantity * hargaSetelahDiskon;

    row.querySelector(".subtotal").value = formatNumber(subtotal);
    updateTotal();
}

function updateTotal() {
    let subtotal = 0;

    document.querySelectorAll(".subtotal").forEach((input) => {
        subtotal += parseNumber(input.value);
    });

    const diskon = parseNumber(document.getElementById("diskon").value);
    const total = Math.max(0, subtotal - diskon);

    document.getElementById("subtotal-display").textContent =
        "Rp " + formatNumber(subtotal);
    document.getElementById("total-display").textContent =
        "Rp " + formatNumber(total);
    document.getElementById("total_setelah_diskon").value = total;
}

function renumberRows() {
    document.querySelectorAll("#items-container tr").forEach((row, index) => {
        row.querySelector("td:first-child").textContent = index + 1;
    });
}

// Search Sparepart Functions
function searchSpareparts(searchTerm = "") {
    const url = window.serviceRoutes.searchSparepart + `?search=${searchTerm}`;
    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            // Handle both formats: direct array or {success: true, data: array}
            const spareparts = data.data || data;
            displaySparepartResults(spareparts);
        })
        .catch((error) => {
            console.error("Error searching spareparts:", error);
            alert("Gagal mencari sparepart");
        });
}

function displaySparepartResults(spareparts) {
    const tbody = document.getElementById("sparepart-search-results");
    tbody.innerHTML = "";

    spareparts.forEach((sparepart, index) => {
        const stokStatus =
            sparepart.stok > 0
                ? `<span class="px-2 py-1 text-xs text-white bg-green-600 rounded">${sparepart.stok} ${sparepart.satuan}</span>`
                : `<span class="px-2 py-1 text-xs text-white bg-red-600 rounded">Habis</span>`;

        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="p-2 border">${index + 1}</td>
            <td class="p-2 border"><strong>${
                sparepart.kode_sparepart
            }</strong></td>
            <td class="p-2 border">${sparepart.nama_sparepart}</td>
            <td class="p-2 border">${sparepart.merk || "-"}</td>
            <td class="p-2 border text-right">Rp ${formatNumber(
                sparepart.harga
            )}</td>
            <td class="p-2 border text-center">${stokStatus}</td>
            <td class="p-2 border text-center">
                <button type="button" class="px-3 py-1 text-white bg-blue-600 rounded hover:bg-blue-700 select-sparepart" 
                        data-sparepart-id="${sparepart.id_sparepart}"
                        ${sparepart.stok == 0 ? "disabled" : ""}>
                    <i class="fas fa-check"></i> Pilih
                </button>
            </td>
        `;
        tbody.appendChild(row);

        row.querySelector(".select-sparepart")?.addEventListener(
            "click",
            function () {
                selectSparepart(sparepart.id_sparepart);
            }
        );
    });
}

function selectSparepart(sparepartId) {
    const url = window.serviceRoutes.sparepartDetail.replace(
        ":id",
        sparepartId
    );
    fetch(url)
        .then((response) => response.json())
        .then((result) => {
            // Handle both formats: direct object or {success: true, data: object}
            const sparepart = result.data || result;

            addItemRow({
                id_sparepart: sparepart.id_sparepart,
                deskripsi: sparepart.nama_sparepart,
                keterangan: sparepart.merk ? `Merk: ${sparepart.merk}` : "",
                kuantitas: 1,
                satuan: sparepart.satuan,
                harga: sparepart.harga,
                subtotal: sparepart.harga,
                tipe: "sparepart",
                is_sparepart: true,
                kode_sparepart: sparepart.kode_sparepart,
            });

            // Close modal
            document
                .getElementById("modal-pilih-sparepart")
                .classList.add("hidden");
            document.getElementById("search-sparepart").value = "";
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Gagal memuat detail sparepart");
        });
}

// Search Customer Functions
function searchCustomers(searchTerm = "") {
    const loadingElement = document.getElementById("customer-search-loading");
    const emptyElement = document.getElementById("customer-search-empty");
    const resultsElement = document.getElementById("customer-search-results");
    const tableElement = resultsElement.closest("table");

    // Show loading, hide others
    loadingElement.classList.remove("hidden");
    emptyElement.classList.add("hidden");
    tableElement.classList.add("hidden");

    fetch(`/service-management/invoice/search-customers?search=${searchTerm}`)
        .then((response) => response.json())
        .then((data) => {
            loadingElement.classList.add("hidden");

            if (data.success && data.customers && data.customers.length > 0) {
                displayCustomerResults(data.customers);
                tableElement.classList.remove("hidden");
            } else {
                emptyElement.classList.remove("hidden");
                tableElement.classList.add("hidden");
            }
        })
        .catch((error) => {
            console.error("Error searching customers:", error);
            loadingElement.classList.add("hidden");
            emptyElement.classList.remove("hidden");
            tableElement.classList.add("hidden");
        });
}

function displayCustomerResults(customers) {
    const tbody = document.getElementById("customer-search-results");
    tbody.innerHTML = "";

    customers.forEach((customer, index) => {
        // Generate kode member dengan prefix
        let kodeMember = "-";
        if (customer.kode_member) {
            kodeMember =
                customer.closing_type_prefix + "-" + customer.kode_member;
        }

        // Determine badge color based on closing type
        let badgeClass = "bg-blue-600";
        let badgeText = "Jual Putus";

        if (customer.closing_type_prefix === "D") {
            badgeClass = "bg-green-600";
            badgeText = "Deposit";
        } else if (customer.closing_type_prefix === "JD") {
            badgeClass = "bg-yellow-600";
            badgeText = "Mixed";
        }

        const row = document.createElement("tr");
        row.className = "hover:bg-gray-50 cursor-pointer";
        row.innerHTML = `
            <td class="p-2 border">${index + 1}</td>
            <td class="p-2 border">
                <div>
                    <strong>${customer.nama}</strong>
                    <br>
                    <small class="text-gray-500">Kode: ${kodeMember}</small>
                    <span class="px-2 py-1 ml-2 text-xs text-white rounded ${badgeClass}">${badgeText}</span>
                </div>
            </td>
            <td class="p-2 border">${customer.alamat || "-"}</td>
            <td class="p-2 border">${customer.telepon || "-"}</td>
            <td class="p-2 text-center border">
                <button type="button" class="px-3 py-1 text-white bg-blue-600 rounded hover:bg-blue-700 select-customer">
                    <i class="fas fa-check"></i> Pilih
                </button>
            </td>
        `;

        // Add click event for entire row
        row.addEventListener("click", function () {
            selectCustomer(customer.id_member, customer.nama, kodeMember);
        });

        // Add click event for select button
        row.querySelector(".select-customer").addEventListener(
            "click",
            function (e) {
                e.stopPropagation();
                selectCustomer(customer.id_member, customer.nama, kodeMember);
            }
        );

        tbody.appendChild(row);
    });
}

function selectCustomer(customerId, customerName, customerKode) {
    document.getElementById("id_member").value = customerId;
    document.getElementById(
        "customer-display"
    ).value = `${customerName} (${customerKode})`;
    document.getElementById("modal-cari-customer").classList.add("hidden");
    document.getElementById("search-customer").value = "";

    // Enable mesin dropdown and load data
    document.getElementById("id_mesin_customer").disabled = false;
    document.getElementById("jenis_service").disabled = false;

    // Trigger change event to load mesin customer
    const event = new Event("change");
    document.getElementById("id_member").dispatchEvent(event);
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
    // Outlet change handler
    document
        .getElementById("outlet-selector")
        ?.addEventListener("change", function () {
            window.location.href =
                window.location.pathname + "?outlet_id=" + this.value;
        });

    // Customer change handler
    document
        .getElementById("id_member")
        ?.addEventListener("change", function () {
            const memberId = this.value;
            const outletId = document.getElementById("outlet_id").value;
            const mesinSelect = document.getElementById("id_mesin_customer");

            if (!memberId) {
                mesinSelect.innerHTML =
                    '<option value="">Pilih Customer terlebih dahulu</option>';
                mesinSelect.disabled = true;
                document.getElementById("jenis_service").disabled = true;
                return;
            }

            // Use route from Laravel (passed via window.serviceRoutes)
            const routeUrl =
                window.serviceRoutes?.getMesinByMember ||
                "/get-mesin-customer-grouped/:id";
            const url = routeUrl.replace(":id", memberId);
            fetch(url)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then((data) => {
                    mesinSelect.innerHTML =
                        '<option value="">Tidak ada mesin (Service umum)</option>';

                    if (data && data.length > 0) {
                        data.forEach((mesin) => {
                            const option = document.createElement("option");
                            // Use 'id' as primary key (from model)
                            const mesinId = mesin.id || mesin.id_mesin_customer;
                            if (
                                !mesinId ||
                                mesinId === null ||
                                mesinId === "null"
                            ) {
                                console.error(
                                    "Mesin customer tidak memiliki ID yang valid:",
                                    mesin
                                );
                                return; // Skip this mesin
                            }
                            option.value = mesinId;
                            option.dataset.closingType =
                                mesin.closing_type || "";

                            // Handle produk data (could be 'produk' or 'produks')
                            const produkData =
                                mesin.produk || mesin.produks || [];
                            option.dataset.produks = JSON.stringify(produkData);

                            // Handle ongkos kirim data (could be ongkos_kirim or ongkir)
                            const ongkirData =
                                mesin.ongkos_kirim || mesin.ongkir || {};
                            option.dataset.ongkosKirim =
                                JSON.stringify(ongkirData);

                            // Build option text
                            const daerah =
                                ongkirData.daerah ||
                                ongkirData.nama_daerah ||
                                mesin.daerah ||
                                "Unknown";
                            const produkNames =
                                produkData
                                    .map((p) => p.nama_produk)
                                    .join(", ") || "no products";
                            option.textContent = `${daerah} - ${produkNames}`;

                            mesinSelect.appendChild(option);
                        });
                        mesinSelect.disabled = false;
                    } else {
                        mesinSelect.innerHTML =
                            '<option value="">Tidak ada mesin (Service umum)</option>';
                        mesinSelect.disabled = false;
                    }

                    // Always enable jenis service after customer is selected
                    document.getElementById("jenis_service").disabled = false;
                })
                .catch((error) => {
                    console.error("Error:", error);
                    mesinSelect.innerHTML =
                        '<option value="">Error loading data</option>';
                    mesinSelect.disabled = true;
                    alert(
                        "Gagal memuat data mesin customer. Silakan coba lagi."
                    );
                });
        });

    // Mesin customer change handler
    document
        .getElementById("id_mesin_customer")
        ?.addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];

            if (!selectedOption.value) {
                // No mesin selected - reset data but keep service enabled
                currentClosingType = "";
                document.getElementById("jenis_service").disabled = false;
                return;
            }

            currentClosingType = selectedOption.dataset.closingType;
            const produks = JSON.parse(selectedOption.dataset.produks || "[]");
            const ongkosKirim = JSON.parse(
                selectedOption.dataset.ongkosKirim || "{}"
            );

            document.getElementById("jenis_service").disabled = false;
        });

    // Jenis service change handler
    document
        .getElementById("jenis_service")
        ?.addEventListener("change", function () {
            currentJenisService = this.value;

            // Show/hide teknisi section
            const teknisiSection = document.getElementById("teknisi-section");
            if (this.value === "Service") {
                teknisiSection.classList.remove("hidden");
            } else {
                teknisiSection.classList.add("hidden");
                // Reset teknisi values when hidden
                document.getElementById("jumlah_teknisi").value = 0;
                document.getElementById("jumlah_jam").value = 0;
                document.getElementById("biaya_teknisi").value = 0;
                removeTeknisiItem();
            }

            const selectedOption =
                document.getElementById("id_mesin_customer").options[
                    document.getElementById("id_mesin_customer").selectedIndex
                ];

            let produks = [];
            let ongkosKirim = {};

            // Only get mesin data if a mesin is selected
            if (selectedOption && selectedOption.value) {
                produks = JSON.parse(selectedOption.dataset.produks || "[]");
                ongkosKirim = JSON.parse(
                    selectedOption.dataset.ongkosKirim || "{}"
                );
            }

            // Clear existing items
            document.getElementById("items-container").innerHTML = "";

            // Add items based on service type
            if (currentJenisService === "Maintenance" && produks.length > 0) {
                produks.forEach((produk) => {
                    const biayaService = produk.pivot
                        ? produk.pivot.biaya_service
                        : 0;
                    const jumlah = produk.pivot ? produk.pivot.jumlah || 1 : 1;
                    const subtotal = biayaService * jumlah;
                    const satuan = produk.satuan
                        ? produk.satuan.nama_satuan
                        : "Unit";

                    addItemRow({
                        id_produk: produk.id_produk,
                        deskripsi: produk.nama_produk,
                        keterangan: "Biaya Maintenance",
                        kuantitas: jumlah,
                        satuan: satuan,
                        harga: biayaService,
                        subtotal: subtotal,
                        tipe: "produk",
                        is_sparepart: false,
                    });
                });
            }

            // Add ongkir if available
            if (ongkosKirim && ongkosKirim.harga) {
                addItemRow({
                    deskripsi: "Transport - " + ongkosKirim.daerah,
                    keterangan: "Menggunakan mobil",
                    kuantitas: 1,
                    satuan: "Trip",
                    harga: ongkosKirim.harga,
                    subtotal: ongkosKirim.harga,
                    tipe: "ongkir",
                    jenis_kendaraan: "mobil",
                    is_sparepart: false,
                });
            }
        });

    // Add sparepart button
    document
        .getElementById("add-sparepart")
        ?.addEventListener("click", function () {
            document
                .getElementById("modal-pilih-sparepart")
                .classList.remove("hidden");
            searchSpareparts("");
        });

    // Add item button
    document.getElementById("add-item")?.addEventListener("click", function () {
        addItemRow({
            deskripsi: "",
            keterangan: "",
            kuantitas: 1,
            satuan: "Unit",
            harga: 0,
            subtotal: 0,
            tipe: "lainnya",
            is_sparepart: false,
        });
    });

    // Button cari customer
    document
        .getElementById("btn-cari-customer")
        ?.addEventListener("click", function () {
            document
                .getElementById("modal-cari-customer")
                .classList.remove("hidden");
            searchCustomers("");
        });

    // Search customer input
    let customerSearchTimeout;
    document
        .getElementById("search-customer")
        ?.addEventListener("input", function () {
            clearTimeout(customerSearchTimeout);
            const searchTerm = this.value;

            customerSearchTimeout = setTimeout(() => {
                searchCustomers(searchTerm);
            }, 500);
        });

    // Diskon change handler
    document.getElementById("diskon")?.addEventListener("input", function () {
        formatCurrency(this);
        updateTotal();
    });

    // Search sparepart
    document
        .getElementById("search-sparepart")
        ?.addEventListener("input", function () {
            searchSpareparts(this.value);
        });

    // Form submit
    document
        .getElementById("invoice-form")
        ?.addEventListener("submit", function (e) {
            e.preventDefault();

            // Get submit button and disable it
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<i class="mr-2 fas fa-spinner fa-spin"></i>Menyimpan...';
            submitBtn.classList.add("opacity-75", "cursor-not-allowed");

            // Validate required fields before submit
            const mesinCustomerValue =
                document.getElementById("id_mesin_customer").value;

            // Mesin customer is now optional - only validate if customer is selected
            const customerId = document.getElementById("id_member").value;
            if (!customerId) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.classList.remove("opacity-75", "cursor-not-allowed");
                alert("Silakan pilih Customer terlebih dahulu!");
                return;
            }

            const formData = {
                outlet_id: document.getElementById("outlet_id").value || null,
                tanggal: document.getElementById("tanggal").value,
                id_member: document.getElementById("id_member").value,
                id_mesin_customer: mesinCustomerValue
                    ? parseInt(mesinCustomerValue)
                    : null,
                jenis_service: document.getElementById("jenis_service").value,
                keterangan_service:
                    document.getElementById("keterangan_service").value || "",
                tanggal_mulai_service: document.getElementById(
                    "tanggal_mulai_service"
                ).value,
                tanggal_selesai_service: document.getElementById(
                    "tanggal_selesai_service"
                ).value,
                tanggal_service_berikutnya:
                    document.getElementById("tanggal_service_berikutnya")
                        .value || null,
                is_garansi: document.getElementById("is_garansi").checked
                    ? 1
                    : 0,
                jumlah_teknisi:
                    parseInt(document.getElementById("jumlah_teknisi").value) ||
                    0,
                jumlah_jam:
                    parseFloat(document.getElementById("jumlah_jam").value) ||
                    0,
                biaya_teknisi:
                    parseNumber(
                        document.getElementById("biaya_teknisi").value
                    ) || 0,
                diskon:
                    parseNumber(document.getElementById("diskon").value) || 0,
                total_setelah_diskon:
                    parseNumber(
                        document.getElementById("total_setelah_diskon").value
                    ) || 0,
                items: [],
            };

            // Debug log
            console.log("Form data before submit:", formData);

            document.querySelectorAll("#items-container tr").forEach((row) => {
                const item = {
                    id_produk:
                        row.querySelector('input[name="items[id_produk][]"]')
                            .value || null,
                    id_sparepart:
                        row.querySelector('input[name="items[id_sparepart][]"]')
                            .value || null,
                    deskripsi: row.querySelector(
                        'input[name="items[deskripsi][]"]'
                    ).value,
                    keterangan: row.querySelector(
                        'input[name="items[keterangan][]"]'
                    ).value,
                    kuantitas:
                        parseInt(
                            row.querySelector(
                                'input[name="items[kuantitas][]"]'
                            ).value
                        ) || 1,
                    satuan:
                        row.querySelector('input[name="items[satuan][]"]')
                            .value || "",
                    diskon: parseNumber(
                        row.querySelector('input[name="items[diskon][]"]').value
                    ),
                    harga: parseNumber(
                        row.querySelector('input[name="items[harga][]"]').value
                    ),
                    subtotal: parseNumber(
                        row.querySelector('input[name="items[subtotal][]"]')
                            .value
                    ),
                    tipe: row.querySelector('input[name="items[tipe][]"]')
                        .value,
                    is_sparepart:
                        row.querySelector('input[name="items[is_sparepart][]"]')
                            .value === "1",
                    jenis_kendaraan:
                        row.querySelector(
                            'select[name="items[jenis_kendaraan][]"]'
                        )?.value || null,
                    kode_sparepart:
                        row.querySelector(
                            'input[name="items[kode_sparepart][]"]'
                        ).value || null,
                };

                formData.items.push(item);
            });

            // Send AJAX request
            fetch(this.action, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify(formData),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((err) => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then((result) => {
                    if (result.success) {
                        alert("Invoice berhasil dibuat!");
                        window.location.href =
                            result.redirect || "/admin/service/history";
                    } else {
                        // Reset button state on error
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;
                        submitBtn.classList.remove(
                            "opacity-75",
                            "cursor-not-allowed"
                        );

                        // Show detailed error
                        let errorMsg = result.message || "Unknown error";
                        if (result.errors) {
                            console.error("Validation errors:", result.errors);
                            errorMsg += "\n\nDetail error:\n";
                            for (let field in result.errors) {
                                errorMsg += `- ${field}: ${result.errors[
                                    field
                                ].join(", ")}\n`;
                            }
                        }
                        alert("Gagal membuat invoice: " + errorMsg);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);

                    // Reset button state on error
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                    submitBtn.classList.remove(
                        "opacity-75",
                        "cursor-not-allowed"
                    );

                    // Show detailed error
                    let errorMsg = "Terjadi kesalahan saat menyimpan invoice";
                    if (error.message) {
                        errorMsg += "\n\n" + error.message;
                    }
                    if (error.errors) {
                        console.error("Validation errors:", error.errors);
                        errorMsg += "\n\nDetail error:\n";
                        for (let field in error.errors) {
                            errorMsg += `- ${field}: ${error.errors[field].join(
                                ", "
                            )}\n`;
                        }
                    }
                    alert(errorMsg);
                });
        });

    // Teknisi calculation event listeners
    document
        .getElementById("jumlah_teknisi")
        ?.addEventListener("input", calculateTeknisiCost);
    document
        .getElementById("jumlah_jam")
        ?.addEventListener("input", calculateTeknisiCost);

    // Set minimum date for tanggal_service_berikutnya based on tanggal_selesai_service
    document
        .getElementById("tanggal_selesai_service")
        ?.addEventListener("change", function () {
            const tanggalSelesai = this.value;
            const serviceBerikutnyaInput = document.getElementById(
                "tanggal_service_berikutnya"
            );
            if (tanggalSelesai && serviceBerikutnyaInput) {
                serviceBerikutnyaInput.setAttribute("min", tanggalSelesai);
            }
        });

    // Set initial min date on page load
    const tanggalSelesaiAwal = document.getElementById(
        "tanggal_selesai_service"
    )?.value;
    if (tanggalSelesaiAwal) {
        const serviceBerikutnyaInput = document.getElementById(
            "tanggal_service_berikutnya"
        );
        if (serviceBerikutnyaInput) {
            serviceBerikutnyaInput.setAttribute("min", tanggalSelesaiAwal);
        }
    }

    // Trigger on page load to show if "Service" is selected
    const jenisServiceSelect = document.getElementById("jenis_service");
    if (jenisServiceSelect) {
        // Trigger change event to initialize form state
        const event = new Event("change");
        jenisServiceSelect.dispatchEvent(event);
    }
});

// Teknisi Functions
function calculateTeknisiCost() {
    const jumlahTeknisi =
        parseInt(document.getElementById("jumlah_teknisi").value) || 0;
    const jumlahJam =
        parseFloat(document.getElementById("jumlah_jam").value) || 0;
    const biayaPerJam = 25000; // Rp 25.000 per jam

    const biayaTeknisi = jumlahTeknisi * jumlahJam * biayaPerJam;
    document.getElementById("biaya_teknisi").value = formatNumber(biayaTeknisi);

    // Update or add teknisi item
    updateTeknisiItem(biayaTeknisi, jumlahTeknisi, jumlahJam);
}

function updateTeknisiItem(biayaTeknisi, jumlahTeknisi, jumlahJam) {
    // Find existing teknisi row
    let teknisiRow = null;
    document.querySelectorAll("#items-container tr").forEach((row) => {
        const tipe = row.querySelector('input[name="items[tipe][]"]')?.value;
        if (tipe === "teknisi") {
            teknisiRow = row;
        }
    });

    if (biayaTeknisi > 0) {
        if (teknisiRow) {
            // Update existing row
            teknisiRow.querySelector(".price").value =
                formatNumber(biayaTeknisi);
            teknisiRow.querySelector(".subtotal").value =
                formatNumber(biayaTeknisi);
            // Keterangan dikosongkan
            teknisiRow.querySelector(
                'input[name="items[keterangan][]"]'
            ).value = "";
        } else {
            // Add new row
            addItemRow({
                deskripsi: "Biaya Teknisi",
                keterangan: "", // Dikosongkan sesuai requirement
                kuantitas: 1,
                satuan: "Paket",
                harga: biayaTeknisi,
                diskon: 0,
                subtotal: biayaTeknisi,
                tipe: "teknisi",
                is_sparepart: false,
            });
        }
    } else if (teknisiRow) {
        // Remove if biaya = 0
        teknisiRow.remove();
        renumberRows();
    }

    updateTotal();
}

function removeTeknisiItem() {
    document.querySelectorAll("#items-container tr").forEach((row) => {
        const tipe = row.querySelector('input[name="items[tipe][]"]')?.value;
        if (tipe === "teknisi") {
            row.remove();
        }
    });
    renumberRows();
    updateTotal();
}

// Invoice Settings Functions
document
    .getElementById("btn-invoice-settings")
    ?.addEventListener("click", function () {
        loadInvoiceSettings();
        document
            .getElementById("modal-invoice-settings")
            .classList.remove("hidden");
    });

function loadInvoiceSettings() {
    const url =
        window.serviceRoutes?.getInvoiceSettings ||
        "/admin/service/invoice/settings";

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                document.getElementById("invoice_prefix").value =
                    data.settings.prefix || "BBN.INV";
                document.getElementById("last_number").value =
                    data.settings.last_number || 0;
                document.getElementById("counter_year").value =
                    data.settings.year || new Date().getFullYear();
                updateInvoicePreview();
            }
        })
        .catch((error) => {
            console.error("Error loading settings:", error);
            // Set default values
            document.getElementById("invoice_prefix").value = "BBN.INV";
            document.getElementById("last_number").value = 0;
            document.getElementById("counter_year").value =
                new Date().getFullYear();
            updateInvoicePreview();
        });
}

function updateInvoicePreview() {
    const prefix = document.getElementById("invoice_prefix").value || "BBN.INV";
    const lastNumber =
        parseInt(document.getElementById("last_number").value) || 0;
    const year =
        document.getElementById("counter_year").value ||
        new Date().getFullYear();
    const nextNumber = String(lastNumber + 1).padStart(3, "0");
    const month = getRomanMonth(new Date().getMonth() + 1);

    document.getElementById(
        "invoice-preview"
    ).textContent = `${nextNumber}/${prefix}/${month}/${year}`;
}

function getRomanMonth(month) {
    const romanNumerals = {
        1: "I",
        2: "II",
        3: "III",
        4: "IV",
        5: "V",
        6: "VI",
        7: "VII",
        8: "VIII",
        9: "IX",
        10: "X",
        11: "XI",
        12: "XII",
    };
    return romanNumerals[month] || "I";
}

// Update preview on input change
document
    .getElementById("invoice_prefix")
    ?.addEventListener("input", updateInvoicePreview);
document
    .getElementById("last_number")
    ?.addEventListener("input", updateInvoicePreview);
document
    .getElementById("counter_year")
    ?.addEventListener("input", updateInvoicePreview);

// Form submit
document
    .getElementById("form-invoice-settings")
    ?.addEventListener("submit", function (e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnContent = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<i class="mr-2 fas fa-spinner fa-spin"></i>Menyimpan...';

        const formData = {
            prefix: document.getElementById("invoice_prefix").value,
            last_number: parseInt(document.getElementById("last_number").value),
            year: parseInt(document.getElementById("counter_year").value),
        };

        const url =
            window.serviceRoutes?.saveInvoiceSettings ||
            "/admin/service/invoice/settings";

        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify(formData),
        })
            .then((response) => response.json())
            .then((result) => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;

                if (result.success) {
                    alert("Setting nomor invoice berhasil disimpan!");
                    document
                        .getElementById("modal-invoice-settings")
                        .classList.add("hidden");
                } else {
                    alert(
                        "Gagal menyimpan setting: " +
                            (result.message || "Unknown error")
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
                alert("Terjadi kesalahan saat menyimpan setting");
            });
    });
