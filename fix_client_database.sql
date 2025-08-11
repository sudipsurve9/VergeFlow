-- Fix missing columns in vergeflow_client_1 database
USE vergeflow_client_1;

-- Add missing columns to products table
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_featured TINYINT(1) DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1;

-- Update existing products to be active and some to be featured
UPDATE products SET is_active = 1;
UPDATE products SET is_featured = 1 WHERE featured = 1;

-- Add status column to clients table in main database
USE vergeflow_main;
ALTER TABLE clients ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'active';

-- Update existing clients to active status
UPDATE clients SET status = 'active' WHERE status IS NULL;
