-- SWAD NEPAL Database Schema
-- Import this file into MySQL before running the app:
--   mysql -u root -p < database.sql
-- Or use phpMyAdmin -> Import

CREATE DATABASE IF NOT EXISTS `SWAD-NEPAL Original` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `SWAD-NEPAL Original`;

-- ---------------------------------------------------------------
-- Users (both normal visitors and admin use this table)
-- ---------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------
-- Categories (Momo, Newari, Thakali, Street Food, Dessert, etc.)
-- ---------------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
);

-- ---------------------------------------------------------------
-- Restaurants
-- ---------------------------------------------------------------
CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    address VARCHAR(255) NOT NULL,
    province VARCHAR(100) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    is_demo TINYINT(1) NOT NULL DEFAULT 0,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    price_range ENUM('cheap', 'mid', 'premium') NOT NULL DEFAULT 'cheap',
    category_id INT,
    cover_image VARCHAR(255) DEFAULT NULL,
    added_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------
-- Extra photos per restaurant (gallery)
-- ---------------------------------------------------------------
CREATE TABLE restaurant_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- Reviews
-- ---------------------------------------------------------------
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- Contact messages submitted through the public contact page
-- ---------------------------------------------------------------
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------
-- Seed data
-- ---------------------------------------------------------------

-- Default admin account: email admin@swadnepal.com / password: admin123
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@swadnepal.com', '$2b$12$/R5epNBgAyXFiPq3nGC6W.C.Nynfece.ElzoyW2pNe5iTcRMDmTZK', 'admin');
-- The hash above is a verified bcrypt hash for the password "admin123".
-- Change this password after first login in a real deployment.
-- Alternative: register a normal account through the site, then run:
--   UPDATE users SET role='admin' WHERE email='youremail@example.com';

INSERT INTO categories (name) VALUES
('Newari'), ('Momo'), ('Thakali'), ('Street Food'), ('Dessert'), ('South Indian'), ('Fast Food'), ('Local Thali'), ('Nepali');

INSERT INTO restaurants (name, description, address, province, city, is_demo, latitude, longitude, price_range, category_id, cover_image, added_by) VALUES
('Yeti Momo Corner', 'Small local joint famous for steamed buff and veg momo at unbeatable prices. Always packed with locals at lunchtime.', 'Mahendra Chowk, Biratnagar', 'Koshi Province', 'Biratnagar', 1, NULL, NULL, 'cheap', 2, NULL, 1),
('Newari Bhoj Ghar', 'Authentic Newari khaja set served on a traditional thali - choila, bara, and aila for the adventurous.', 'Traffic Chowk, Biratnagar', 'Koshi Province', 'Biratnagar', 1, NULL, NULL, 'mid', 1, NULL, 1),
('Thakali Kitchen', 'Home-style Thakali daal-bhat set with unlimited refills, run by a family from Mustang.', 'Main Road, Biratnagar', 'Koshi Province', 'Biratnagar', 1, NULL, NULL, 'mid', 3, NULL, 1),
('Station Chatpat Stall', 'Roadside chatpat and panipuri cart with a loyal after-college crowd. Cash only, cheap and fast.', 'Bus Park Area, Biratnagar', 'Koshi Province', 'Biratnagar', 1, NULL, NULL, 'cheap', 4, NULL, 1),
('Mishti Ghar', 'Small Bengali-style sweet shop known for rasbari and local yogurt.', 'Ranibari, Biratnagar', 'Koshi Province', 'Biratnagar', 1, NULL, NULL, 'cheap', 5, NULL, 1);

INSERT INTO restaurants (name, description, address, province, city, is_demo, latitude, longitude, price_range, category_id, cover_image, added_by)
SELECT 'Bajeko sekuwa', 'Nepali cuisine. Approx. price: NPR 300.', 'Main Road', 'Koshi Province', 'Biratnagar', 0, NULL, NULL, 'cheap', categories.id, NULL, NULL
FROM categories
WHERE categories.name = 'Nepali'
    AND NOT EXISTS (SELECT 1 FROM restaurants WHERE name = 'Bajeko sekuwa');

-- ---------------------------------------------------------------
-- Review photos (uploaded alongside a review)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS review_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------
-- Public place suggestions (submitted via suggest_place.php)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS place_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    place_name VARCHAR(150) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100),
    food_type VARCHAR(100),
    why TEXT NOT NULL,
    your_name VARCHAR(100) NOT NULL,
    your_email VARCHAR(150) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
