
-- Setup company settings for logo testing
INSERT INTO company_settings (outlet_id, company_name, company_logo, is_active, created_at, updated_at) 
VALUES (1, 'Test Company', 'logos/test-logo.png', 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE 
    company_logo = 'logos/test-logo.png', 
    company_name = 'Test Company',
    updated_at = NOW();

-- Verify the record
SELECT id, outlet_id, company_name, company_logo FROM company_settings WHERE outlet_id = 1;
