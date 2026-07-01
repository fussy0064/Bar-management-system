-- Bundara Bar Management System
-- Database schema, normalized to 3NF
-- Target establishment: Dar es Salaam, Tanzania

CREATE DATABASE IF NOT EXISTS bundara_bar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bundara_bar;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    contact_encrypted TEXT,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    cost_price DECIMAL(12,2) NOT NULL,
    selling_price DECIMAL(12,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    cashier_id INT NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('cash', 'mobile_money', 'card') NOT NULL,
    status ENUM('completed', 'cancelled') NOT NULL DEFAULT 'completed',
    created_at DATETIME NOT NULL,
    FOREIGN KEY (cashier_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO roles (role_name) VALUES ('admin'), ('cashier'), ('sysadmin');

INSERT INTO categories (category_name) VALUES
('Beer'), ('Spirits'), ('Wine'), ('Soft Drinks'), ('Food');

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

-- Run database/setup_admin.php once after importing this schema
-- to create the first sysadmin login account.
