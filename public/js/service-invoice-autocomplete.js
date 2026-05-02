// Customer Autocomplete
let customerSearchTimeout;

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("customer-search");
    const dropdown = document.getElementById("customer-dropdown");

    if (!searchInput) return;

    // Search on input
    searchInput.addEventListener("input", function () {
        clearTimeout(customerSearchTimeout);
        const searchTerm = this.value.trim();

        if (searchTerm.length < 2) {
            dropdown.classList.add("hidden");
            return;
        }

        customerSearchTimeout = setTimeout(() => {
            searchCustomersAutocomplete(searchTerm);
        }, 300);
    });

    // Hide dropdown when clicking outside
    document.addEventListener("click", function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add("hidden");
        }
    });

    // Show dropdown when focus if has value
    searchInput.addEventListener("focus", function () {
        if (this.value.trim().length >= 2) {
            dropdown.classList.remove("hidden");
        }
    });
});

function searchCustomersAutocomplete(searchTerm) {
    const dropdown = document.getElementById("customer-dropdown");
    const loading = document.getElementById("customer-loading");
    const results = document.getElementById("customer-results");
    const empty = document.getElementById("customer-empty");

    // Show dropdown and loading
    dropdown.classList.remove("hidden");
    loading.classList.remove("hidden");
    results.classList.add("hidden");
    empty.classList.add("hidden");

    fetch(`/service-management/invoice/search-customers?search=${searchTerm}`)
        .then((response) => response.json())
        .then((data) => {
            loading.classList.add("hidden");

            if (data.success && data.customers && data.customers.length > 0) {
                displayCustomerDropdown(data.customers);
                results.classList.remove("hidden");
            } else {
                empty.classList.remove("hidden");
            }
        })
        .catch((error) => {
            console.error("Error searching customers:", error);
            loading.classList.add("hidden");
            empty.classList.remove("hidden");
        });
}

function displayCustomerDropdown(customers) {
    const results = document.getElementById("customer-results");
    results.innerHTML = "";

    customers.forEach((customer) => {
        // Generate kode member dengan prefix
        let kodeMember = "-";
        if (customer.kode_member) {
            kodeMember =
                customer.closing_type_prefix + "-" + customer.kode_member;
        }

        // Determine badge color based on closing type
        let badgeClass = "bg-blue-100 text-blue-800";
        let badgeText = "JP";

        if (customer.closing_type_prefix === "D") {
            badgeClass = "bg-green-100 text-green-800";
            badgeText = "D";
        } else if (customer.closing_type_prefix === "JD") {
            badgeClass = "bg-yellow-100 text-yellow-800";
            badgeText = "JD";
        }

        const item = document.createElement("div");
        item.className =
            "px-4 py-3 cursor-pointer hover:bg-gray-50 border-b border-gray-100 last:border-0";
        item.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="font-medium text-gray-900">${
                        customer.nama
                    }</div>
                    <div class="text-sm text-gray-500">
                        <span>Kode: ${kodeMember}</span>
                        ${customer.telepon ? ` • ${customer.telepon}` : ""}
                    </div>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded ${badgeClass}">${badgeText}</span>
            </div>
        `;

        item.addEventListener("click", function () {
            selectCustomerAutocomplete(
                customer.id_member,
                customer.nama,
                kodeMember
            );
        });

        results.appendChild(item);
    });
}

function selectCustomerAutocomplete(customerId, customerName, customerKode) {
    const searchInput = document.getElementById("customer-search");
    const dropdown = document.getElementById("customer-dropdown");

    // Set values
    document.getElementById("id_member").value = customerId;
    searchInput.value = `${customerName} (${customerKode})`;

    // Hide dropdown
    dropdown.classList.add("hidden");

    // Enable mesin dropdown and load data
    document.getElementById("id_mesin_customer").disabled = false;
    document.getElementById("jenis_service").disabled = false;

    // Trigger change event to load mesin customer
    const event = new Event("change");
    document.getElementById("id_member").dispatchEvent(event);
}
