CREATE DATABASE IF NOT EXISTS ice_cream_shop
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ice_cream_shop;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    availability TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(30) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('Pending','Confirmed','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (customer_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'Demo Online Payment',
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('Pending','Successful','Failed','Cancelled') NOT NULL DEFAULT 'Pending',
    transaction_reference VARCHAR(100) DEFAULT NULL,
    payment_datetime TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES
('Vanilla'), ('Chocolate'), ('Strawberry'), ('Mango'), ('Special');

INSERT INTO products (name, description, price, category_id, image, availability) VALUES
('Classic Vanilla', 'Smooth and creamy vanilla ice cream.', 450.00, 1, 'ClassicVanilla.jpg', 1),
('Chocolate Dream', 'Rich chocolate ice cream for chocolate lovers.', 500.00, 2, 'ChocolateDream.jpg', 1),
('Strawberry Bliss', 'Fresh strawberry flavored ice cream.', 480.00, 3, 'StrawberryBliss.jpg', 1),
('Mango Magic', 'Sweet tropical mango ice cream.', 520.00, 4, 'MangoMagic.jpg', 1),
('Royal Sundae', 'A special ice cream treat.', 750.00, 5, 'RoyalSundae.jpg', 1);
