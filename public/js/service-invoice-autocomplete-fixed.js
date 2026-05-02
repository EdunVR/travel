// Customer Autocomplete - Enhanced with Lock Mechanism
let customerSearchTimeout;
let customerLocked = false; // Flag to prevent clearing selected customer
let customerResults = []; // Store current search results

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("customer-search");
    const dropdown = document.getElementById("customer-dropdown");

    if (!searchInput) {
        console.error("customer-search input not found!");
        return;
    }

    console.log("✅ Customer autocomplete initialized with lock mechanism");

    // Search on input
    searchInput.addEventListener("input", function () {
        handleCustomerInput(this.value.trim());
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

    // Additional event handlers for better customer selection
    searchInput.addEventListener("change", function () {
        checkCustomerSelection();
    });

    searchInput.addEventListener("blur", function () {
        // Small delay to allow click events on dropdown items
        setTimeout(() => {
            checkCustomerSelection();
        }, 100);
    });
});

function handleCustomerInput(searchTerm) {
    // If user is typing and changes the text, unlock customer selection
    if (customerLocked) {
        const currentMatch = customerResults.find(c => c.displayText === document.getElementById("customer-search").value);
        if (!currentMatch || currentMatch.id !== document.getElementById("id_member").value) {
            customerLocked = false;
            console.log('🔓 Customer unlocked due to text change');
        }
    }

    clearTimeout(customerSearchTimeout);

    if (searchTerm.length < 2) {
        document.getElementById("customer-dropdown").classList.add("hidden");
        if (!customerLocked) {
            document.getElementById("id_member").value = '';
            customerResults = [];
        }
        return;
    }

    // Check if current input matches any existing customer
    checkCustomerSelection();

    // If customer is already selected and locked, don't search again
    if (customerLocked && document.getElementById("id_member").value) {
        const currentMatch = customerResults.find(c => c.displayText === searchTerm);
        if (currentMatch && currentMatch.id === document.getElementById("id_member").value) {
            console.log('🔒 Customer already selected and locked, skipping search');
            return;
        }
    }

    customerSearchTimeout = setTimeout(() => {
        searchCustomersAutocomplete(searchTerm);
    }, 300);
}

function checkCustomerSelection() {
    // Check if the current search input matches any customer in results
    if (customerResults.length === 0) {
        return; // No results to check against
    }

    const searchValue = document.getElementById("customer-search").value;
    const selected = customerResults.find(c => c.displayText === searchValue);
    
    if (selected) {
        document.getElementById("id_member").value = selected.id;
        customerLocked = true; // Lock the customer selection
        console.log('✅ Customer auto-selected:', selected);
    } else {
        // Only clear ID if we have results but no match AND customer is not locked
        if (customerResults.length > 0 && !customerLocked) {
            document.getElementById("id_member").value = '';
            console.log('⚠️ Customer not found in current results, clearing ID');
        }
    }
}

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

    // Get current outlet ID
    const outletId =
        document.getElementById("outlet_id")?.value ||
        document.getElementById("outlet-selector")?.value ||
        "1";

    // Use route from Laravel (passed via window.serviceRoutes)
    const baseUrl =
        window.serviceRoutes?.searchCustomers ||
        "/admin/service/search-customers";
    const url = `${baseUrl}?search=${encodeURIComponent(
        searchTerm
    )}&outlet_id=${outletId}`;
    console.log("🔍 Fetching customers from:", url);

    fetch(url, {
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
    })
        .then((response) => {
            console.log(
                "📡 Response status:",
                response.status,
                response.statusText
            );

            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error(
                        "Route not found (404). Check if route exists in routes/web.php"
                    );
                } else if (response.status === 401 || response.status === 419) {
                    throw new Error(
                        "Session expired. Please refresh the page and login again"
                    );
                } else {
                    throw new Error(
                        `HTTP ${response.status}: ${response.statusText}`
                    );
                }
            }

            return response.json();
        })
        .then((data) => {
            console.log("📊 Customer search response:", data);
            loading.classList.add("hidden");

            if (data.success && data.customers && data.customers.length > 0) {
                // Store results for customer selection checking
                customerResults = data.customers.map(customer => {
                    // Generate kode member dengan prefix
                    let kodeMember = "-";
                    if (customer.kode_member) {
                        kodeMember = customer.closing_type_prefix + "-" + customer.kode_member;
                    }
                    
                    return {
                        id: customer.id_member,
                        nama: customer.nama,
                        kode: kodeMember,
                        displayText: `${customer.nama} (${kodeMember})`,
                        closing_type_prefix: customer.closing_type_prefix || 'JP',
                        telepon: customer.telepon
                    };
                });

                displayCustomerDropdown(data.customers);
                results.classList.remove("hidden");
                
                // After updating results, check if current input matches any customer
                checkCustomerSelection();
            } else {
                // Only clear results and ID if customer is not locked
                if (!customerLocked) {
                    customerResults = [];
                    document.getElementById("id_member").value = '';
                    console.log('📭 No customers found, clearing results');
                } else {
                    console.log('🔒 No customers found, but customer is locked - keeping selection');
                }
                empty.classList.remove("hidden");
            }
        })
        .catch((error) => {
            console.error("❌ Error searching customers:", error);
            loading.classList.add("hidden");

            // Only clear on error if customer is not locked
            if (!customerLocked) {
                customerResults = [];
                document.getElementById("id_member").value = '';
            }

            // Show error in dropdown
            results.innerHTML = `
                <div class="px-4 py-3 text-center text-red-600">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                    <p class="mt-2 font-medium">${error.message}</p>
                    <p class="text-sm mt-1 text-gray-600">Check browser console for details</p>
                </div>
            `;
            results.classList.remove("hidden");
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

    console.log("✅ Customer selected:", customerId, customerName, customerKode);

    // Set values
    document.getElementById("id_member").value = customerId;
    searchInput.value = `${customerName} (${customerKode})`;

    // Lock the customer selection
    customerLocked = true;
    console.log("🔒 Customer selection locked");

    // Hide dropdown
    dropdown.classList.add("hidden");

    // Enable mesin dropdown and jenis service
    document.getElementById("id_mesin_customer").disabled = false;
    document.getElementById("jenis_service").disabled = false;

    // Trigger change event to load mesin customer data
    const event = new Event("change");
    document.getElementById("id_member").dispatchEvent(event);
}

// Reset customer lock when form is reset or page loads
function resetCustomerSelection() {
    customerLocked = false;
    customerResults = [];
    document.getElementById("id_member").value = '';
    document.getElementById("customer-search").value = '';
    console.log("🔓 Customer selection reset");
}
