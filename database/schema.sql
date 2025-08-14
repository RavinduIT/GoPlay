-- GoPlay Sports Platform Database Creation Script
-- Run this script to create the complete database structure

-- Create Database
CREATE DATABASE IF NOT EXISTS goplay_sports_platform;
USE goplay_sports_platform;

-- ======================
-- USER MANAGEMENT TABLES
-- ======================

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    profile_picture VARCHAR(255),
    user_type ENUM('customer', 'admin', 'coach', 'facility_owner') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Addresses Table
CREATE TABLE user_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_type ENUM('home', 'work', 'billing', 'shipping') DEFAULT 'home',
    street_address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'Sri Lanka',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ======================
-- SPORTS & CATEGORIES
-- ======================

-- Sports Categories Table
CREATE TABLE sports_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Sports Categories
INSERT INTO sports_categories (name, description, icon, is_active) VALUES
('Basketball', 'Basketball courts and facilities', 'fas fa-basketball-ball', TRUE),
('Tennis', 'Tennis courts and coaching', 'fas fa-tennis-ball', TRUE),
('Football', 'Football fields and training', 'fas fa-football-ball', TRUE),
('Cricket', 'Cricket grounds and coaching', 'fas fa-baseball-ball', TRUE),
('Swimming', 'Swimming pools and lessons', 'fas fa-swimming-pool', TRUE),
('Badminton', 'Badminton courts and coaching', 'fas fa-shuttlecock', TRUE),
('Volleyball', 'Volleyball courts and training', 'fas fa-volleyball-ball', TRUE),
('Soccer', 'Soccer fields and coaching', 'fas fa-futbol', TRUE);

-- ======================
-- COACHES MANAGEMENT
-- ======================

-- Coaches Table
CREATE TABLE coaches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    sport_category_id INT NOT NULL,
    experience_years INT DEFAULT 0,
    hourly_rate DECIMAL(10,2) NOT NULL,
    bio TEXT,
    specializations TEXT,
    certifications TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    total_sessions INT DEFAULT 0,
    availability_schedule JSON,
    location VARCHAR(255),
    status ENUM('active', 'inactive', 'busy') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sport_category_id) REFERENCES sports_categories(id)
);

-- Coach Reviews Table
CREATE TABLE coach_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ======================
-- FACILITIES MANAGEMENT
-- ======================

-- Sports Facilities Table
CREATE TABLE sports_facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    sport_category_id INT NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'Sri Lanka',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    hourly_rate DECIMAL(10,2) NOT NULL,
    capacity INT,
    amenities JSON,
    images JSON,
    rules TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sport_category_id) REFERENCES sports_categories(id)
);

-- Facility Reviews Table
CREATE TABLE facility_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ======================
-- PRODUCTS & SHOP
-- ======================

-- Product Categories Table
CREATE TABLE product_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    parent_id INT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id)
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(100) UNIQUE,
    description TEXT,
    category_id INT NOT NULL,
    brand VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2),
    cost_price DECIMAL(10,2),
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 5,
    weight DECIMAL(8,2),
    dimensions VARCHAR(100),
    specifications JSON,
    features JSON,
    images JSON,
    tags JSON,
    status ENUM('active', 'inactive', 'out_of_stock', 'discontinued') DEFAULT 'active',
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    total_sales INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id)
);

-- Product Reviews Table
CREATE TABLE product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    review_text TEXT,
    images JSON,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ======================
-- BOOKINGS SYSTEM
-- ======================

-- Coach Bookings Table
CREATE TABLE coach_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    coach_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_hours DECIMAL(3,1) NOT NULL,
    session_type ENUM('individual', 'group', 'assessment') DEFAULT 'individual',
    total_amount DECIMAL(10,2) NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);

-- Facility Bookings Table
CREATE TABLE facility_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    facility_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_hours DECIMAL(3,1) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE
);

-- ======================
-- E-COMMERCE SYSTEM
-- ======================

-- Shopping Cart Table
CREATE TABLE shopping_cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    order_type ENUM('product', 'service', 'booking') DEFAULT 'product',
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded', 'partial') DEFAULT 'pending',
    payment_method ENUM('card', 'bank_transfer', 'cash', 'paypal', 'other') DEFAULT 'card',
    shipping_address JSON,
    billing_address JSON,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    coach_booking_id INT,
    facility_booking_id INT,
    item_name VARCHAR(200) NOT NULL,
    item_description TEXT,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (coach_booking_id) REFERENCES coach_bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (facility_booking_id) REFERENCES facility_bookings(id) ON DELETE SET NULL
);

-- ======================
-- PAYMENT SYSTEM
-- ======================

-- Payments Table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    transaction_id VARCHAR(255) UNIQUE,
    payment_method ENUM('credit_card', 'debit_card', 'bank_transfer', 'paypal', 'cash', 'other') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    gateway_response JSON,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ======================
-- NOTIFICATIONS SYSTEM
-- ======================

-- Notifications Table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('booking_confirmation', 'payment_success', 'order_status', 'review_request', 'promotional', 'system') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ======================
-- SYSTEM SETTINGS
-- ======================

-- Settings Table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    description TEXT,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Default Settings
INSERT INTO settings (key_name, value, description, type) VALUES
('site_name', 'GoPlay Sports Platform', 'Name of the website', 'string'),
('site_description', 'Your ultimate sports hub for booking grounds, coaches, and equipment', 'Site description', 'string'),
('default_currency', 'LKR', 'Default currency code', 'string'),
('tax_rate', '0.084', 'Tax rate (8.4%)', 'number'),
('service_fee_rate', '0.05', 'Service fee rate (5%)', 'number'),
('booking_advance_days', '30', 'Days in advance bookings can be made', 'number'),
('max_booking_duration', '8', 'Maximum booking duration in hours', 'number');

-- ======================
-- CREATE INDEXES FOR PERFORMANCE
-- ======================

-- User indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_type ON users(user_type);

-- Coach indexes
CREATE INDEX idx_coaches_sport ON coaches(sport_category_id);
CREATE INDEX idx_coaches_rate ON coaches(hourly_rate);
CREATE INDEX idx_coaches_rating ON coaches(rating);
CREATE INDEX idx_coaches_status ON coaches(status);

-- Facility indexes
CREATE INDEX idx_facilities_sport ON sports_facilities(sport_category_id);
CREATE INDEX idx_facilities_city ON sports_facilities(city);
CREATE INDEX idx_facilities_rate ON sports_facilities(hourly_rate);
CREATE INDEX idx_facilities_rating ON sports_facilities(rating);

-- Product indexes
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_brand ON products(brand);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_rating ON products(rating);

-- Booking indexes
CREATE INDEX idx_coach_bookings_date ON coach_bookings(booking_date);
CREATE INDEX idx_coach_bookings_user ON coach_bookings(user_id);
CREATE INDEX idx_coach_bookings_coach ON coach_bookings(coach_id);
CREATE INDEX idx_facility_bookings_date ON facility_bookings(booking_date);
CREATE INDEX idx_facility_bookings_user ON facility_bookings(user_id);
CREATE INDEX idx_facility_bookings_facility ON facility_bookings(facility_id);

-- Order indexes
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_date ON orders(created_at);
CREATE INDEX idx_orders_number ON orders(order_number);

-- Payment indexes
CREATE INDEX idx_payments_order ON payments(order_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_transaction ON payments(transaction_id);

-- Notification indexes
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);
CREATE INDEX idx_notifications_type ON notifications(type);

COMMIT;