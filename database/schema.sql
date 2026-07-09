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
    cost_price TEXT NOT NULL, -- AES-256 encrypted (business-sensitive), decrypted in app via Security::decrypt()
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
    payment_method ENUM('cash','card') NOT NULL,
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

INSERT INTO users (username, password_hash, full_name, contact_encrypted, role_id, status, created_at) VALUES
('happybundara67@gmail.com', '$2y$10$GGYdrWXatzq.Y6dauENUBOx2fF.MVTzFgTvzpNJx9H0zPo/PdPEU.', 'Happy Bundara', 'G/QapwGGhrLOoplI9ID/rqB/5lg+fnEr3TsbACbWSWY=', 3, 'active', NOW());
