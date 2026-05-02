<?php
/**
 * Fix Modal Post Depreciation Visibility Issue
 * Mengatasi masalah modal yang tidak muncul meskipun showPostDepreciationModal = true
 */

echo "=== FIX MODAL POST DEPRECIATION VISIBILITY ===\n\n";

$viewFile = 'resources/views/admin/finance/aktiva-tetap/index.blade.php';

// Backup original file
$backupFile = $viewFile . '.backup.' . date('Y-m-d-H-i-s');
if (file_exists($viewFile)) {
    copy($viewFile, $backupFile);
    echo "✅ Backup created: $backupFile\n\n";
}

// Read current content
$content = file_get_contents($viewFile);

// 1. Fix z-index issue - increase z-index to ensure modal is on top
echo "1. FIXING Z-INDEX ISSUE...\n";
$content = str_replace(
    'x-show="showPostDepreciationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"',
    'x-show="showPostDepreciationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" style="display: none;"',
    $content
);

// 2. Add transition for better visibility
echo "2. ADDING TRANSITION EFFECTS...\n";
$content = str_replace(
    'x-show="showPostDepreciationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" style="display: none;"',
    'x-show="showPostDepreciationModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" 
     style="display: none;"',
    $content
);

// 3. Add debug indicator
echo "3. ADDING DEBUG INDICATOR...\n";
$debugIndicator = '
    {{-- Debug Indicator for Modal State --}}
    <div x-show="showPostDepreciationModal" 
         class="fixed top-4 right-4 bg-green-500 text-white px-3 py-2 rounded-lg shadow-lg z-[10000] text-sm font-semibold">
        ✅ Modal Active (ID: <span x-text="selectedDepreciationForPost ? selectedDepreciationForPost.id : \'none\'"></span>)
    </div>';

// Insert debug indicator before the modal
$content = str_replace(
    '{{-- Modal Post Depreciation (Single) --}}',
    $debugIndicator . "\n\n    {{-- Modal Post Depreciation (Single) --}}",
    $content
);

// 4. Add JavaScript debugging function
echo "4. ADDING JAVASCRIPT DEBUG FUNCTION...\n";
$debugJS = '
        // Debug function for modal visibility
        debugModal() {
          console.log("=== MODAL DEBUG ===");
          console.log("showPostDepreciationModal:", this.showPostDepreciationModal);
          console.log("selectedDepreciationForPost:", this.selectedDepreciationForPost);
          console.log("availableBooksForPosting:", this.availableBooksForPosting);
          
          const modal = document.querySelector(\'[x-show="showPostDepreciationModal"]\');
          if (modal) {
            console.log("Modal element found:", modal);
            console.log("Modal computed style:", {
              display: getComputedStyle(modal).display,
              visibility: getComputedStyle(modal).visibility,
              opacity: getComputedStyle(modal).opacity,
              zIndex: getComputedStyle(modal).zIndex
            });
            
            // Force show for debugging
            modal.style.display = "flex";
            modal.style.visibility = "visible";
            modal.style.opacity = "1";
            console.log("Modal forced to show");
          } else {
            console.log("Modal element not found!");
          }
        },

        // Enhanced openPostDepreciationModal with debugging
        async openPostDepreciationModal(id) {
          console.log("🔍 openPostDepreciationModal called with ID:", id);
          console.log("📊 Current depreciationHistory:", this.depreciationHistory);
          
          // Find the depreciation record
          const depreciation = this.depreciationHistory.find(d => d.id == id);
          if (!depreciation) {
            console.error("❌ Depreciation not found for ID:", id);
            alert("Data penyusutan tidak ditemukan");
            return;
          }
          
          console.log("✅ Found depreciation:", depreciation);
          this.selectedDepreciationForPost = depreciation;
          
          // Load available books
          console.log("🔄 Loading available books...");
          await this.loadAvailableBooksForPosting();
          
          console.log("📚 Available books loaded:", this.availableBooksForPosting);
          console.log("🎯 Setting showPostDepreciationModal to true");
          this.showPostDepreciationModal = true;
          
          // Debug after setting
          setTimeout(() => {
            console.log("✅ Modal should be visible now. showPostDepreciationModal:", this.showPostDepreciationModal);
            this.debugModal();
          }, 100);
        },';

// Insert the debug function before the existing openPostDepreciationModal
$content = str_replace(
    'async openPostDepreciationModal(id) {',
    $debugJS . "\n\n        // Original openPostDepreciationModal (replaced by enhanced version above)\n        async openPostDepreciationModalOriginal(id) {",
    $content
);

// 5. Add CSS fixes
echo "5. ADDING CSS FIXES...\n";
$cssFixes = '
<style>
/* Fix for modal visibility issues */
[x-show="showPostDepreciationModal"] {
    display: none !important;
}

[x-show="showPostDepreciationModal"][style*="display: flex"],
[x-show="showPostDepreciationModal"][style*="display: block"] {
    display: flex !important;
    position: fixed !important;
    inset: 0 !important;
    z-index: 9999 !important;
}

/* Ensure modal content is properly positioned */
.modal-post-depreciation {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    z-index: 9999 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    padding: 1rem !important;
}

/* Debug styles */
.debug-modal-indicator {
    position: fixed !important;
    top: 1rem !important;
    right: 1rem !important;
    z-index: 10000 !important;
    background-color: #10b981 !important;
    color: white !important;
    padding: 0.75rem !important;
    border-radius: 0.5rem !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}
</style>';

// Insert CSS before closing head tag
$content = str_replace('</head>', $cssFixes . "\n</head>", $content);

// 6. Add test button for debugging
echo "6. ADDING TEST BUTTON...\n";
$testButton = '
          {{-- Debug Test Button --}}
          <button @click="debugModal()" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 ml-2">
            Debug Modal
          </button>
          
          <button @click="showPostDepreciationModal = !showPostDepreciationModal" class="bg-purple-500 text-white px-3 py-1 rounded text-xs hover:bg-purple-600 ml-2">
            Toggle Modal
          </button>';

// Find a good place to insert test button (after bulk actions)
$content = str_replace(
    '<button @click="bulkDeleteDepreciations()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 disabled:opacity-50" :disabled="selectedDepreciations.length === 0">',
    $testButton . "\n            <button @click=\"bulkDeleteDepreciations()\" class=\"bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 disabled:opacity-50\" :disabled=\"selectedDepreciations.length === 0\">",
    $content
);

// Write the updated content
file_put_contents($viewFile, $content);

echo "✅ Modal visibility fixes applied!\n\n";

echo "CHANGES MADE:\n";
echo "1. ✅ Increased z-index from z-50 to z-[9999]\n";
echo "2. ✅ Added transition effects for better visibility\n";
echo "3. ✅ Added debug indicator to show modal state\n";
echo "4. ✅ Enhanced openPostDepreciationModal with debugging\n";
echo "5. ✅ Added CSS fixes with !important declarations\n";
echo "6. ✅ Added test buttons for debugging\n\n";

echo "TESTING INSTRUCTIONS:\n";
echo "1. Refresh the page\n";
echo "2. Try posting a depreciation - look for green indicator in top-right\n";
echo "3. If indicator appears but modal doesn't, check browser console\n";
echo "4. Use 'Debug Modal' button to force show modal\n";
echo "5. Use 'Toggle Modal' button to test modal visibility\n\n";

echo "If modal still doesn't appear:\n";
echo "1. Check browser console for JavaScript errors\n";
echo "2. Check if there are CSS conflicts with other frameworks\n";
echo "3. Try the test HTML file: test_modal_post_depreciation_debug.html\n\n";

echo "=== FIX COMPLETE ===\n";
?>