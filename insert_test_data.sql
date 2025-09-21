-- Insert test data into vergeflow_client_1 database

USE vergeflow_client_1;

-- Clear existing data
DELETE FROM orders;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM users WHERE role = 'user';

-- Insert categories
INSERT INTO categories (id, name, slug, description, is_active, client_id, created_at, updated_at) VALUES
(1, 'Classic Cars', 'classic-cars', 'Vintage and classic car models', 1, 1, NOW(), NOW()),
(2, 'Sports Cars', 'sports-cars', 'High-performance sports cars', 1, 1, NOW(), NOW()),
(3, 'Muscle Cars', 'muscle-cars', 'American muscle cars', 1, 1, NOW(), NOW()),
(4, 'Fantasy Cars', 'fantasy-cars', 'Fantasy and concept cars', 1, 1, NOW(), NOW());

-- Insert products
INSERT INTO products (id, name, slug, description, price, sale_price, sku, stock_quantity, category_id, is_featured, status, is_active, image, client_id, created_at, updated_at) VALUES
(1, '1967 Camaro SS', '1967-camaro-ss', 'Classic American muscle car with racing stripes and powerful V8 engine.', 299.99, 249.99, 'HW-CAM-67-SS', 25, 1, 1, 'active', 1, 'hotwheels-1.jpg', 1, NOW(), NOW()),
(2, '1969 Dodge Charger R/T', '1969-dodge-charger-rt', 'Legendary muscle car featured in movies and TV shows.', 349.99, NULL, 'HW-CHR-69-RT', 18, 3, 1, 'active', 1, 'hotwheels-2.jpg', 1, NOW(), NOW()),
(3, 'Lamborghini Aventador', 'lamborghini-aventador', 'Italian supercar with scissor doors and V12 engine.', 799.99, 699.99, 'HW-LAM-AVE', 12, 2, 1, 'active', 1, 'hotwheels-3.jpg', 1, NOW(), NOW()),
(4, 'Twin Mill III', 'twin-mill-iii', 'Futuristic fantasy car with dual engines and unique design.', 149.99, 129.99, 'HW-TM3', 35, 4, 1, 'active', 1, 'hotwheels-4.jpg', 1, NOW(), NOW()),
(5, 'Ferrari 488 GTB', 'ferrari-488-gtb', 'Italian sports car with twin-turbo V8 engine.', 649.99, NULL, 'HW-FER-488', 15, 2, 0, 'active', 1, 'hotwheels-5.jpg', 1, NOW(), NOW());

-- Insert test users
INSERT INTO users (id, name, email, email_verified_at, password, role, client_id, created_at, updated_at) VALUES
(1, 'John Customer', 'john@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW()),
(2, 'Jane Smith', 'jane@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW()),
(3, 'Mike Johnson', 'mike@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1, NOW(), NOW());

-- Insert sample orders
INSERT INTO orders (id, user_id, order_number, status, payment_status, total_amount, shipping_address, billing_address, payment_method, client_id, created_at, updated_at) VALUES
(1, 1, 'ORD-001', 'completed', 'paid', 249.99, '123 Main St, City, State 12345', '123 Main St, City, State 12345', 'stripe', 1, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 2, 'ORD-002', 'pending', 'pending', 699.99, '456 Oak Ave, City, State 67890', '456 Oak Ave, City, State 67890', 'cod', 1, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 3, 'ORD-003', 'completed', 'paid', 349.99, '789 Pine St, City, State 11111', '789 Pine St, City, State 11111', 'stripe', 1, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 1, 'ORD-004', 'processing', 'paid', 129.99, '123 Main St, City, State 12345', '123 Main St, City, State 12345', 'paypal', 1, NOW(), NOW());

-- Verify data insertion
SELECT 'Categories' as table_name, COUNT(*) as count FROM categories
UNION ALL
SELECT 'Products', COUNT(*) FROM products
UNION ALL
SELECT 'Users (customers)', COUNT(*) FROM users WHERE role = 'user'
UNION ALL
SELECT 'Orders', COUNT(*) FROM orders
UNION ALL
SELECT 'Revenue (paid orders)', SUM(total_amount) FROM orders WHERE payment_status = 'paid';
