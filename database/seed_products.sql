-- Seed products categorized correctly
-- Categories: 1 = Beer, 2 = Spirits, 3 = Wine, 4 = Soft Drinks, 5 = Food

USE bundara_bar;

INSERT INTO products (product_name, category_id, cost_price, selling_price, stock_quantity, unit, created_at) VALUES
('Heineken', 1, 3500.00, 5000.00, 100, 'bottle', NOW()),
('Brutal', 1, 3000.00, 4000.00, 100, 'bottle', NOW()),
('Savannah', 1, 4000.00, 5500.00, 100, 'bottle', NOW()),
('Kilimanjaro', 1, 2000.00, 3000.00, 200, 'bottle', NOW()),
('Serengeti', 1, 2000.00, 3000.00, 200, 'bottle', NOW()),
('Balimi', 1, 1800.00, 2500.00, 150, 'bottle', NOW()),
('Windowk', 1, 3000.00, 4500.00, 100, 'bottle', NOW()),
('Flying fish', 1, 2500.00, 3500.00, 120, 'bottle', NOW()),
('Four cousins', 3, 12000.00, 18000.00, 50, 'bottle', NOW()),
('Amarula', 2, 35000.00, 50000.00, 20, 'bottle', NOW()),
('Values', 2, 8000.00, 12000.00, 40, 'bottle', NOW()),
('Kvant', 2, 7000.00, 10000.00, 50, 'bottle', NOW()),
('Hanson choice', 2, 9000.00, 14000.00, 30, 'bottle', NOW()),
('John walker', 2, 45000.00, 65000.00, 15, 'bottle', NOW()),
('Jagger master', 2, 40000.00, 60000.00, 20, 'bottle', NOW()),
('Symne of ice', 2, 3000.00, 4500.00, 80, 'bottle', NOW()),
-- Food Available in Dar es Salaam Tz
('Chips Mayai', 5, 2000.00, 3000.00, 50, 'plate', NOW()),
('Beef Mishkaki', 5, 1200.00, 2000.00, 100, 'skewer', NOW()),
('Nyama Choma', 5, 10000.00, 15000.00, 30, 'kg', NOW()),
('Kuku Choma', 5, 4500.00, 7000.00, 40, 'portion', NOW()),
('Ndizi Kaanga', 5, 1000.00, 2000.00, 50, 'plate', NOW()),
('Ugali Nyama Choma', 5, 5000.00, 8000.00, 30, 'plate', NOW()),
('Samaki Choma', 5, 8000.00, 12000.00, 20, 'piece', NOW()),
('Pilau Kuku', 5, 6000.00, 9000.00, 25, 'plate', NOW());
