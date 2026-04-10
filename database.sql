CREATE DATABASE IF NOT EXISTS clothing_store;
USE clothing_store;

CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  role ENUM('customer', 'staff') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  description TEXT,
  price DECIMAL(10,2),
  stock INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  total DECIMAL(10,2),
  status ENUM('pending', 'completed') DEFAULT 'completed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  order_id INT,
  product_id INT,
  quantity INT,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- --------------------------------------------------------
-- Sample users
-- Run generate-passwords.php first to get real hashes,
-- then replace the hashes below before importing.
--
-- Staff login:    admin@test.com   / admin123
-- Customer login: customer@test.com / customer123
-- --------------------------------------------------------

INSERT INTO users (name, email, password, role) VALUES
('Admin Staff', 'admin@test.com',
 '$2y$10$PLACEHOLDER_STAFF_HASH_REPLACE_ME_admin123_xxxxx', 'staff'),
('John Smith', 'customer@test.com',
 '$2y$10$PLACEHOLDER_CUSTOMER_HASH_REPLACE_ME_customer123_xx', 'customer');

-- Sample products
INSERT INTO products (name, description, price, stock) VALUES
('Classic White T-Shirt', 'Comfortable everyday cotton t-shirt.', 19.99, 50),
('Black Graphic Tee', 'Slim fit t-shirt with front print.', 24.99, 35),
('Blue Slim Jeans', 'Slim fit blue denim jeans.', 49.99, 25),
('Black Skinny Jeans', 'Stretch skinny jeans in black.', 54.99, 20),
('Leather Jacket', 'Faux leather jacket with zip pockets.', 89.99, 10),
('Denim Jacket', 'Classic denim jacket, button front.', 69.99, 15),
('Grey Pullover Hoodie', 'Warm pullover hoodie with front pocket.', 39.99, 30),
('Navy Zip Hoodie', 'Full zip hoodie, adjustable hood.', 44.99, 22),
('Canvas Belt', 'Durable canvas belt with metal buckle.', 14.99, 60),
('Wool Scarf', 'Soft wool scarf, classic plaid pattern.', 22.99, 4);
