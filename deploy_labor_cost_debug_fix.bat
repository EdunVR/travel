@echo off
echo 🚀 Deploying Labor Cost Debug Fix
echo ================================

echo.
echo 📋 Changes Applied:
echo - Enhanced labor cost calculation in ProductionController
echo - Added debugging to calculateLaborCost() JavaScript function  
echo - Added debugging to HPP preview data collection
echo - Added debugging to response handling and UI updates

echo.
echo 🔧 Files Modified:
echo - app/Http/Controllers/ProductionController.php
echo - public/js/production.js

echo.
echo ✅ Deployment completed!
echo.
echo 📋 Testing Instructions:
echo 1. Open the production page in browser
echo 2. Open browser developer tools (F12)
echo 3. Go to Console tab
echo 4. Create a new production
echo 5. Fill in labor cost fields (worker count and cost per worker)
echo 6. Watch console for debug messages:
echo    - "🔧 calculateLaborCost() called"
echo    - "💰 Labor cost calculation"
echo    - "🚀 Calling calculateHppPreview()"
echo    - "💰 Labor costs in HPP preview"
echo    - "💰 Labor cost in response"
echo    - "🎯 Updating UI elements"
echo 7. Check if labor cost appears in the HPP preview section

echo.
echo 🐛 If issues persist, check:
echo - Network tab for failed requests
echo - Console for JavaScript errors
echo - Verify labor cost form fields have correct names
echo - Check if previewLaborCost element exists in DOM

pause