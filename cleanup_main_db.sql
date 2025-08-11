-- Remove client-specific tables from vergeflow_main database
USE vergeflow_main;

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Remove client-specific tables that should only exist in client databases
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `coupon_usages`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `order_status_histories`;
DROP TABLE IF EXISTS `password_resets`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Show remaining tables
SHOW TABLES;
