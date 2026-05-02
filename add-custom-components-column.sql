-- Add custom_components columns to hpp_calculations table
-- Run this SQL in phpMyAdmin or MySQL client

ALTER TABLE `hpp_calculations` 
ADD COLUMN `custom_components` JSON NULL AFTER `contingency`,
ADD COLUMN `component_payment_status` JSON NULL AFTER `custom_components`,
ADD COLUMN `component_hutang_amount` JSON NULL AFTER `component_payment_status`;

-- Verify the columns were added
DESCRIBE `hpp_calculations`;
