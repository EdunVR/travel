/**
 * HPP Custom Components Fix - Version 2.0
 * This file handles custom components properly without accumulation
 */

window.hppCustomComponentsV2 = {
    version: '2.0.0',
    
    /**
     * Build payload with custom components as separate array
     */
    buildPayload(hppForm, hppExtraComponents, selectedPackage) {
        const extraMap = {};
        const payStatusMap = {};
        const hutangMap = {};
        const customComponents = []; // Array untuk custom components dengan label
        
        const knownKeys = [
            'transportation_cost',
            'meal_cost',
            'visa_cost',
            'guide_cost',
            'insurance_cost',
            'operational_overhead',
            'contingency'
        ];
        
        // Initialize known keys
        knownKeys.forEach(k => extraMap[k] = 0);
        
        // Process all components
        hppExtraComponents.forEach(c => {
            if (knownKeys.includes(c.id)) {
                // Known component - add to extraMap
                extraMap[c.id] = (extraMap[c.id] || 0) + (parseFloat(c.value) || 0);
                payStatusMap[c.id] = 'hutang';
                hutangMap[c.id] = (parseFloat(c.value) || 0) * (selectedPackage?.capacity || 1);
            } else if (c.id.startsWith('custom_')) {
                // Custom component - add to customComponents array with label
                customComponents.push({
                    id: c.id,
                    label: c.label || 'Biaya Lainnya',
                    value: parseFloat(c.value) || 0,
                    payment_status: 'hutang',
                    hutang_amount: (parseFloat(c.value) || 0) * (selectedPackage?.capacity || 1),
                });
            }
        });
        
        const payload = {
            flight_cost: hppForm.flight_cost || 0,
            hotel_cost: hppForm.hotel_cost || 0,
            ...extraMap,
            custom_components: customComponents, // IMPORTANT: Send as array
            component_payment_status: payStatusMap,
            component_hutang_amount: hutangMap,
        };
        
        console.log('=== HPP PAYLOAD V2 ===');
        console.log('Custom components count:', customComponents.length);
        console.log('Custom components:', customComponents);
        console.log('======================');
        
        return payload;
    }
};

console.log('✓ HPP Custom Components V2 loaded successfully');
