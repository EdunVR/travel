/**
 * Tab System Readiness Test
 *
 * Paste this into browser console to check if tab system is working
 */

console.log("🔍 Tab System Diagnostic Test");
console.log("================================\n");

// 1. Check global flags
console.log("1️⃣ Global Flags:");
console.log("   TAB_SYSTEM_ACTIVE:", window.TAB_SYSTEM_ACTIVE);
console.log("   TAB_SYSTEM_READY:", window.TAB_SYSTEM_READY);
console.log(
    "   TAB_SYSTEM_COMPONENT:",
    window.TAB_SYSTEM_COMPONENT ? "✅ Available" : "❌ Not found"
);
console.log("");

// 2. Check Alpine.js
console.log("2️⃣ Alpine.js:");
console.log(
    "   Alpine loaded:",
    typeof Alpine !== "undefined" ? "✅ Yes" : "❌ No"
);
if (typeof Alpine !== "undefined") {
    const mainArea = document.querySelector('[x-data*="tabSystem"]');
    console.log("   Main area found:", mainArea ? "✅ Yes" : "❌ No");
    if (mainArea) {
        console.log(
            "   Alpine data:",
            mainArea.__x ? "✅ Available" : "❌ Not initialized"
        );
        if (mainArea.__x) {
            const data = mainArea.__x.$data;
            console.log("   Tabs count:", data.tabs?.length || 0);
            console.log("   Active tab:", data.activeTab);
            console.log(
                "   loadInActiveTab:",
                typeof data.loadInActiveTab === "function"
                    ? "✅ Available"
                    : "❌ Not found"
            );
        }
    }
}
console.log("");

// 3. Check sidebar
console.log("3️⃣ Sidebar:");
const sidebar = document.querySelector("aside");
console.log("   Sidebar found:", sidebar ? "✅ Yes" : "❌ No");
if (sidebar) {
    const links = sidebar.querySelectorAll("a[href]");
    console.log("   Total links:", links.length);

    let normalLinks = 0;
    let hashLinks = 0;
    let modifiedLinks = 0;

    links.forEach((link) => {
        const href = link.getAttribute("href");
        const originalHref = link.getAttribute("data-original-href");

        if (originalHref) {
            modifiedLinks++;
        } else if (href === "#") {
            hashLinks++;
        } else {
            normalLinks++;
        }
    });

    console.log("   Normal links:", normalLinks);
    console.log("   Hash links:", hashLinks);
    console.log("   Modified links:", modifiedLinks);
}
console.log("");

// 4. Test loadInActiveTab
console.log("4️⃣ Function Test:");
if (
    window.TAB_SYSTEM_COMPONENT &&
    typeof window.TAB_SYSTEM_COMPONENT.loadInActiveTab === "function"
) {
    console.log("   ✅ loadInActiveTab is callable");
    console.log("   To test, run:");
    console.log(
        '   window.TAB_SYSTEM_COMPONENT.loadInActiveTab("/test-url", "Test Page")'
    );
} else {
    console.log("   ❌ loadInActiveTab not available");
}
console.log("");

// 5. Check for errors
console.log("5️⃣ Common Issues:");
const issues = [];

if (!window.TAB_SYSTEM_ACTIVE) {
    issues.push(
        "❌ TAB_SYSTEM_ACTIVE not set - Alpine may not have initialized"
    );
}

if (!window.TAB_SYSTEM_READY) {
    issues.push("❌ TAB_SYSTEM_READY not set - Initialization incomplete");
}

if (!window.TAB_SYSTEM_COMPONENT) {
    issues.push(
        "❌ TAB_SYSTEM_COMPONENT not set - Component not exposed globally"
    );
}

const mainArea = document.querySelector('[x-data*="tabSystem"]');
if (!mainArea) {
    issues.push('❌ Main area with x-data="tabSystem()" not found');
} else if (!mainArea.__x) {
    issues.push("❌ Alpine.js not initialized on main area");
}

if (issues.length === 0) {
    console.log("   ✅ No issues detected - System should be working!");
} else {
    issues.forEach((issue) => console.log("   " + issue));
}
console.log("");

// 6. Summary
console.log("================================");
if (window.TAB_SYSTEM_READY && window.TAB_SYSTEM_COMPONENT) {
    console.log("✅ TAB SYSTEM IS READY");
    console.log("You can now click sidebar links to test navigation");
} else {
    console.log("❌ TAB SYSTEM NOT READY");
    console.log("Check the issues above and refresh the page");
}
console.log("================================");

// 7. Provide helper functions
window.testTabNavigation = function (url, title) {
    if (window.TAB_SYSTEM_COMPONENT) {
        console.log("🧪 Testing navigation to:", url);
        window.TAB_SYSTEM_COMPONENT.loadInActiveTab(url, title || "Test Page");
    } else {
        console.error("❌ Tab system not available");
    }
};

window.inspectTabs = function () {
    if (window.TAB_SYSTEM_COMPONENT) {
        console.log("📊 Current Tabs:");
        window.TAB_SYSTEM_COMPONENT.tabs.forEach((tab, index) => {
            console.log(`   ${index + 1}. ${tab.title} (${tab.type})`);
            console.log(`      ID: ${tab.id}`);
            console.log(`      URL: ${tab.url || "none"}`);
            console.log(
                `      Active: ${
                    tab.id === window.TAB_SYSTEM_COMPONENT.activeTab
                        ? "✅"
                        : "❌"
                }`
            );
            console.log("");
        });
    } else {
        console.error("❌ Tab system not available");
    }
};

console.log("\n💡 Helper functions available:");
console.log("   testTabNavigation(url, title) - Test loading a URL");
console.log("   inspectTabs() - Show all current tabs");
console.log("\nExample:");
console.log('   testTabNavigation("/admin/dashboard", "Dashboard")');
console.log("   inspectTabs()");
