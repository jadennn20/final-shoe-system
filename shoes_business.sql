-- Shoe Store Management System upgrade
-- Run this once in phpMyAdmin. It preserves existing brands and shoes.
CREATE DATABASE IF NOT EXISTS shoe_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shoe_store;

-- Existing parent and child tables (created only if they do not exist).
CREATE TABLE IF NOT EXISTS brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS shoes (
    shoe_id INT AUTO_INCREMENT PRIMARY KEY,
    shoe_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    shoe_size INT NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    brand_id INT NOT NULL,
    CONSTRAINT fk_shoes_brand FOREIGN KEY (brand_id)
      REFERENCES brands(brand_id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- New USER ENTITY. Passwords are stored as hashes, never plain text.
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    address VARCHAR(255) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Add user-tracking columns to existing parent and child records.
-- Existing records keep NULL added_by because they were created before user accounts existed.
ALTER TABLE brands
    ADD COLUMN added_by INT NULL,
    ADD COLUMN updated_by INT NULL,
    ADD COLUMN last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE shoes
    ADD COLUMN added_by INT NULL,
    ADD COLUMN updated_by INT NULL,
    ADD COLUMN last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE brands
    ADD CONSTRAINT fk_brands_added_by FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    ADD CONSTRAINT fk_brands_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE RESTRICT;

ALTER TABLE shoes
    ADD CONSTRAINT fk_shoes_added_by FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    ADD CONSTRAINT fk_shoes_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE RESTRICT;

-- Audit log records CREATE, UPDATE, and DELETE actions, including deleted records.
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_type ENUM('CREATE','UPDATE','DELETE') NOT NULL,
    entity_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Sample brands/products are inserted only if absent.
INSERT IGNORE INTO brands (brand_name) VALUES ('Nike'), ('Adidas'), ('Converse');
INSERT INTO shoes (shoe_name, price, shoe_size, stock_quantity, brand_id)
SELECT 'Air Runner',2499.00,9,12,brand_id FROM brands b
WHERE b.brand_name='Nike' AND NOT EXISTS
(SELECT 1 FROM shoes s WHERE s.shoe_name='Air Runner' AND s.brand_id=b.brand_id);
INSERT INTO shoes (shoe_name, price, shoe_size, stock_quantity, brand_id)
SELECT 'Street Classic',1899.00,8,7,brand_id FROM brands b
WHERE b.brand_name='Adidas' AND NOT EXISTS
(SELECT 1 FROM shoes s WHERE s.shoe_name='Street Classic' AND s.brand_id=b.brand_id);
