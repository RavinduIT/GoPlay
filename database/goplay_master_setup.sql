-- ============================================================
-- GoPlay Sports Platform - MASTER DATABASE SETUP
-- ============================================================
-- Run this SINGLE file to set up the complete database from scratch.
-- It creates all tables, indexes, and seeds necessary data.
--
-- Usage: mysql -u root -p < database/goplay_master_setup.sql
--
-- Default admin login:  admin1@goplay.lk / password123
-- Default user login:   user1@goplay.lk  / password123
-- ============================================================

-- Create and select database
CREATE DATABASE IF NOT EXISTS goplay_sports_platform
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;
USE goplay_sports_platform;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ============================================================
-- SECTION 1: CORE TABLES
-- ============================================================

-- 1.1 Users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    profile_picture VARCHAR(255),
    user_type ENUM('user', 'admin', 'ground_owner', 'coach', 'shop_owner') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1.2 User Addresses
CREATE TABLE IF NOT EXISTS user_addresses (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1.3 Sports Categories
CREATE TABLE IF NOT EXISTS sports_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1.4 Settings
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    description TEXT,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 2: SERVICE PROVIDER TABLES
-- ============================================================

-- 2.1 Coaches
CREATE TABLE IF NOT EXISTS coaches (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2.2 Sports Facilities
CREATE TABLE IF NOT EXISTS sports_facilities (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2.3 Ground Owner Profiles
CREATE TABLE IF NOT EXISTS ground_owner_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    business_name VARCHAR(255),
    business_phone VARCHAR(20),
    business_email VARCHAR(100),
    business_address TEXT,
    profile_completion_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2.4 Shop Owner Profiles
CREATE TABLE IF NOT EXISTS shop_owner_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    shop_name VARCHAR(255),
    business_name VARCHAR(255),
    business_registration_number VARCHAR(100),
    tax_identification_number VARCHAR(100),
    business_type VARCHAR(50) DEFAULT 'sole_proprietor',
    year_established INT,
    number_of_employees INT DEFAULT 0,
    business_phone VARCHAR(20),
    business_email VARCHAR(100),
    shop_address TEXT,
    shop_city VARCHAR(100),
    shop_state VARCHAR(100),
    shop_postal_code VARCHAR(20),
    business_description TEXT,
    product_categories JSON,
    brand_names TEXT,
    website_url VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    twitter VARCHAR(255),
    social_media TEXT,
    business_license_document VARCHAR(255),
    tax_document VARCHAR(255),
    shop_images JSON,
    shop_logo VARCHAR(255),
    shop_banner VARCHAR(255),
    delivery_options JSON,
    operating_hours JSON,
    return_policy TEXT,
    warranty_policy TEXT,
    total_products INT DEFAULT 0,
    total_sales INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    payout_bank_name VARCHAR(100) DEFAULT NULL,
    payout_account_number VARCHAR(50) DEFAULT NULL,
    payout_account_holder VARCHAR(100) DEFAULT NULL,
    payout_branch_name VARCHAR(100) DEFAULT NULL,
    profile_completion_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 3: REVIEWS
-- ============================================================

CREATE TABLE IF NOT EXISTS coach_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL,
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS facility_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL,
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 4: E-COMMERCE
-- ============================================================

-- 4.1 Product Categories (named 'categories' to match application code)
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) UNIQUE,
    parent_id INT NULL,
    description TEXT,
    icon VARCHAR(255),
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.2 Products
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(100) UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    category_id INT NOT NULL,
    shop_owner_id INT DEFAULT NULL,
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
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.3 Product Reviews
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL,
    title VARCHAR(200),
    review_text TEXT,
    images JSON,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.4 Shopping Carts (header)
CREATE TABLE IF NOT EXISTS shopping_carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(255) DEFAULT NULL,
    status ENUM('active','converted','expired','abandoned') DEFAULT 'active',
    expires_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.5 Cart Items
CREATE TABLE IF NOT EXISTS cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES shopping_carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_product (cart_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.6 Cart Summary View
CREATE OR REPLACE VIEW cart_summary AS
SELECT sc.id AS cart_id, sc.user_id, sc.session_id, sc.status, sc.expires_at,
       COALESCE(COUNT(ci.id), 0) AS item_count,
       COALESCE(SUM(ci.quantity), 0) AS total_quantity,
       COALESCE(SUM(ci.quantity * ci.unit_price), 0) AS subtotal
FROM shopping_carts sc LEFT JOIN cart_items ci ON sc.id = ci.cart_id
GROUP BY sc.id, sc.user_id, sc.session_id, sc.status, sc.expires_at;

-- ============================================================
-- SECTION 4B: COACH SUPPORT TABLES
-- ============================================================

-- 4B.1 Coach Availability
CREATE TABLE IF NOT EXISTS coach_availability (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time TIME NOT NULL DEFAULT '09:00:00',
    end_time TIME NOT NULL DEFAULT '18:00:00',
    slot_duration INT NOT NULL DEFAULT 60,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_coach_day (coach_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4B.2 Coach Certificates
CREATE TABLE IF NOT EXISTS coach_certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255) DEFAULT NULL,
    issue_date DATE DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    credential_id VARCHAR(255) DEFAULT NULL,
    certificate_file VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4B.3 Coach Achievements
CREATE TABLE IF NOT EXISTS coach_achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    date_achieved DATE DEFAULT NULL,
    category ENUM('competition','certification','milestone','award','other') DEFAULT 'other',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4B.4 Coach Facilities (links coaches to sports facilities)
CREATE TABLE IF NOT EXISTS coach_facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    coach_id INT NOT NULL,
    facility_id INT NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    UNIQUE KEY unique_coach_facility (coach_id, facility_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4B.5 Coach Review Replies
CREATE TABLE IF NOT EXISTS coach_review_replies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    coach_id INT NOT NULL,
    reply_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES coach_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review_reply (review_id, coach_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 4C: MISCELLANEOUS SUPPORT TABLES
-- ============================================================

-- 4C.1 Shops
CREATE TABLE IF NOT EXISTS shops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    logo VARCHAR(500) DEFAULT NULL,
    banner VARCHAR(500) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    status ENUM('active','inactive','suspended','pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4C.2 Withdrawal Requests
CREATE TABLE IF NOT EXISTS withdrawal_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('bank_transfer','paypal','check') DEFAULT 'bank_transfer',
    bank_name VARCHAR(255) DEFAULT NULL,
    account_number VARCHAR(50) DEFAULT NULL,
    account_holder VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','processing','completed','rejected') DEFAULT 'pending',
    processed_by INT DEFAULT NULL,
    processed_at DATETIME DEFAULT NULL,
    transaction_reference VARCHAR(255) DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4C.3 Admin Audit Log
CREATE TABLE IF NOT EXISTS admin_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action_type VARCHAR(100) NOT NULL,
    target_user_id INT DEFAULT NULL,
    data JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4C.4 User Activities
CREATE TABLE IF NOT EXISTS user_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4C.5 Product Images
CREATE TABLE IF NOT EXISTS product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4C.6 Inventory Logs
CREATE TABLE IF NOT EXISTS inventory_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    type ENUM('add','remove','adjustment','sale','return') NOT NULL,
    quantity INT NOT NULL,
    previous_stock INT DEFAULT NULL,
    new_stock INT DEFAULT NULL,
    reason TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 5: BOOKINGS
-- ============================================================

CREATE TABLE IF NOT EXISTS coach_bookings (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS facility_bookings (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 6: ORDERS & PAYMENTS
-- ============================================================

CREATE TABLE IF NOT EXISTS orders (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 7: NOTIFICATIONS
-- ============================================================

CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM(
        'booking_confirmation', 'payment_success', 'order_status',
        'review_request', 'promotional', 'system',
        'new_order', 'order_shipped', 'order_delivered',
        'payout_requested', 'payout_approved', 'payout_rejected',
        'balance_credited'
    ) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 8: NEWS SYSTEM
-- ============================================================

CREATE TABLE IF NOT EXISTS news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category VARCHAR(100) DEFAULT 'General',
    tags JSON,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    last_viewed_at TIMESTAMP NULL DEFAULT NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 9: PROVIDER APPLICATIONS
-- ============================================================

CREATE TABLE IF NOT EXISTS provider_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    provider_type ENUM('ground_owner', 'coach', 'shop_owner') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    
    -- Common fields
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    nic VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    
    -- Ground Owner specific fields
    facility_name VARCHAR(255),
    facility_address TEXT,
    facility_type VARCHAR(100),
    
    -- Coach specific fields
    sport_specialization VARCHAR(100),
    experience_years INT,
    qualifications TEXT,
    session_rate DECIMAL(10,2),
    bio TEXT,
    specialties JSON,
    
    -- Shop Owner specific fields
    shop_name VARCHAR(255),
    shop_address TEXT,
    shop_city VARCHAR(100),
    shop_postal VARCHAR(20),
    business_type VARCHAR(50),
    business_registration_number VARCHAR(100),
    business_description TEXT,
    product_categories JSON,
    year_established INT,
    number_of_employees INT,
    brand_names TEXT,
    website_url VARCHAR(255),
    social_media TEXT,
    
    -- Documents
    business_registration VARCHAR(255),
    tax_document VARCHAR(255),
    identification_document VARCHAR(255),
    
    -- Review fields
    reviewed_by INT DEFAULT NULL,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    rejection_reason TEXT,
    admin_notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_pa_status (status),
    INDEX idx_pa_type (provider_type),
    INDEX idx_pa_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 10: PROMOTIONS & BANNERS (Admin CRUD)
-- ============================================================

CREATE TABLE IF NOT EXISTS promotions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    link_url VARCHAR(500) DEFAULT NULL,
    link_text VARCHAR(100) DEFAULT 'Learn More',
    position ENUM('hero', 'sidebar', 'footer', 'popup') DEFAULT 'hero',
    bg_color VARCHAR(20) DEFAULT '#3b82f6',
    text_color VARCHAR(20) DEFAULT '#ffffff',
    priority INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    starts_at DATETIME DEFAULT NULL,
    ends_at DATETIME DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 11: CONTACT MESSAGES (Admin CRUD)
-- ============================================================

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    admin_reply TEXT DEFAULT NULL,
    replied_by INT DEFAULT NULL,
    replied_at DATETIME DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- SECTION 12: INDEXES
-- ============================================================

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_type ON users(user_type);
CREATE INDEX IF NOT EXISTS idx_coaches_sport ON coaches(sport_category_id);
CREATE INDEX IF NOT EXISTS idx_coaches_status ON coaches(status);
CREATE INDEX IF NOT EXISTS idx_facilities_sport ON sports_facilities(sport_category_id);
CREATE INDEX IF NOT EXISTS idx_facilities_city ON sports_facilities(city);
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_status ON products(status);
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_payments_order ON payments(order_id);
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_read ON notifications(is_read);
CREATE INDEX IF NOT EXISTS idx_news_status ON news(status);
CREATE INDEX IF NOT EXISTS idx_news_category ON news(category);
CREATE INDEX IF NOT EXISTS idx_news_published ON news(published_at);


-- ============================================================
-- ============================================================
--         SECTION 13: SEED DATA
-- ============================================================
-- ============================================================

-- =====================
-- 13.1 Sports Categories
-- =====================
INSERT IGNORE INTO sports_categories (name, description, icon, is_active) VALUES
('Basketball', 'Basketball courts and facilities', 'fas fa-basketball-ball', TRUE),
('Tennis', 'Tennis courts and coaching', 'fas fa-table-tennis', TRUE),
('Football', 'Football fields and training', 'fas fa-football-ball', TRUE),
('Cricket', 'Cricket grounds and coaching', 'fas fa-baseball-ball', TRUE),
('Swimming', 'Swimming pools and lessons', 'fas fa-swimming-pool', TRUE),
('Badminton', 'Badminton courts and coaching', 'fas fa-shuttlecock', TRUE),
('Volleyball', 'Volleyball courts and training', 'fas fa-volleyball-ball', TRUE),
('Soccer', 'Soccer fields and coaching', 'fas fa-futbol', TRUE);

-- =====================
-- 13.2 Default Settings
-- =====================
INSERT IGNORE INTO settings (key_name, value, description, type) VALUES
('site_name', 'GoPlay Sports Platform', 'Name of the website', 'string'),
('site_description', 'Your ultimate sports hub for booking grounds, coaches, and equipment', 'Site description', 'string'),
('default_currency', 'LKR', 'Default currency code', 'string'),
('tax_rate', '0.084', 'Tax rate (8.4%)', 'number'),
('service_fee_rate', '0.05', 'Service fee rate (5%)', 'number'),
('booking_advance_days', '30', 'Days in advance bookings can be made', 'number'),
('max_booking_duration', '8', 'Maximum booking duration in hours', 'number'),
('admin_email', 'admin@goplay.lk', 'Admin notification email', 'string');

-- =====================
-- 13.3 Users (password: password123 for ALL users)
-- =====================
-- Password hash = argon2id hash of "password123"
INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status) VALUES

-- Admins (5)
('admin1', 'admin1@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'John', 'Smith', '+94771234501', 'admin', 'active'),
('admin2', 'admin2@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Sarah', 'Johnson', '+94771234502', 'admin', 'active'),
('admin3', 'admin3@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Michael', 'Brown', '+94771234503', 'admin', 'active'),
('admin4', 'admin4@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Emma', 'Davis', '+94771234504', 'admin', 'active'),
('admin5', 'admin5@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'David', 'Wilson', '+94771234505', 'admin', 'active'),

-- Ground Owners (5)
('groundowner1', 'groundowner1@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Rajesh', 'Perera', '+94771234511', 'ground_owner', 'active'),
('groundowner2', 'groundowner2@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Nimal', 'Silva', '+94771234512', 'ground_owner', 'active'),
('groundowner3', 'groundowner3@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Kamal', 'Fernando', '+94771234513', 'ground_owner', 'active'),
('groundowner4', 'groundowner4@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Sunil', 'Jayawardena', '+94771234514', 'ground_owner', 'active'),
('groundowner5', 'groundowner5@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Chamara', 'Wickramasinghe', '+94771234515', 'ground_owner', 'active'),

-- Coaches (5)
('coach1', 'coach1@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Lasith', 'Malinga', '+94771234521', 'coach', 'active'),
('coach2', 'coach2@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Angelo', 'Mathews', '+94771234522', 'coach', 'active'),
('coach3', 'coach3@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Thisara', 'Perera', '+94771234523', 'coach', 'active'),
('coach4', 'coach4@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Chaminda', 'Vaas', '+94771234524', 'coach', 'active'),
('coach5', 'coach5@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Muttiah', 'Muralitharan', '+94771234525', 'coach', 'active'),

-- Shop Owners (5)
('shopowner1', 'shopowner1@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Rohan', 'Gunaratne', '+94771234531', 'shop_owner', 'active'),
('shopowner2', 'shopowner2@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Priya', 'Mendis', '+94771234532', 'shop_owner', 'active'),
('shopowner3', 'shopowner3@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Asanka', 'Ratnasinghe', '+94771234533', 'shop_owner', 'active'),
('shopowner4', 'shopowner4@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Kumari', 'Dissanayake', '+94771234534', 'shop_owner', 'active'),
('shopowner5', 'shopowner5@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Ruwan', 'Senanayake', '+94771234535', 'shop_owner', 'active'),

-- Regular Users (5)
('user1', 'user1@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Kavinda', 'Ranasighe', '+94771234541', 'user', 'active'),
('user2', 'user2@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Sanduni', 'Rajapakse', '+94771234542', 'user', 'active'),
('user3', 'user3@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Dilan', 'Wijesinghe', '+94771234543', 'user', 'active'),
('user4', 'user4@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Tharushi', 'Amarasinghe', '+94771234544', 'user', 'active'),
('user5', 'user5@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=1$VkMwb1QuaVBMaU02ZFlBTw$+maiNcsJDspCba38wJLXO57alglLZXRZNSLUlMgecew', 'Janith', 'Kodithuwakku', '+94771234545', 'user', 'active');

-- =====================
-- 13.4 Product Categories
-- =====================
INSERT IGNORE INTO categories (name, slug, description, is_active) VALUES
('Sports Equipment', 'sports-equipment', 'General sports equipment', TRUE),
('Footwear', 'footwear', 'Sports shoes and cleats', TRUE),
('Clothing', 'clothing', 'Sports apparel and jerseys', TRUE),
('Accessories', 'accessories', 'Sports accessories and gear', TRUE),
('Fitness', 'fitness', 'Gym and fitness equipment', TRUE);

-- =====================
-- 13.5 Sample Coaches (linked to coach users, IDs 11-15)
-- =====================
INSERT IGNORE INTO coaches (user_id, sport_category_id, experience_years, hourly_rate, bio, specializations, location, status) VALUES
(11, 4, 15, 5000.00, 'Former international cricket fast bowler. Specializes in fast bowling techniques and death bowling.', '["Fast Bowling", "Death Overs", "Fitness Training"]', 'Colombo, Sri Lanka', 'active'),
(12, 4, 12, 4500.00, 'All-rounder cricket coach. Expert in batting technique and fielding drills.', '["Batting", "Fielding", "Match Strategy"]', 'Kandy, Sri Lanka', 'active'),
(13, 4, 10, 4000.00, 'Power-hitting specialist. Coaches T20 cricket and aggressive batting.', '["Power Hitting", "T20 Cricket", "Batting"]', 'Galle, Sri Lanka', 'active'),
(14, 3, 18, 6000.00, 'Veteran football coach. Expertise in midfield strategy and team tactics.', '["Football Strategy", "Midfield Play", "Youth Training"]', 'Colombo, Sri Lanka', 'active'),
(15, 5, 20, 3500.00, 'Swimming instructor with Olympic training experience. All stroke techniques.', '["Freestyle", "Butterfly", "Backstroke", "Competition Prep"]', 'Negombo, Sri Lanka', 'active');

-- =====================
-- 13.6 Sample Facilities (linked to ground owners, IDs 6-10)
-- =====================
INSERT IGNORE INTO sports_facilities (owner_id, name, description, sport_category_id, address, city, hourly_rate, capacity, status) VALUES
(6, 'Royal Cricket Grounds', 'Premium cricket ground with international standards. Includes practice nets and pavilion.', 4, '45 Maitland Crescent', 'Colombo 07', 8000.00, 22, 'active'),
(7, 'Green Field Football Arena', 'Professional football ground with floodlights. FIFA-approved turf.', 3, '12 Kandy Road', 'Kandy', 6000.00, 30, 'active'),
(8, 'Ace Tennis Club', 'Four synthetic tennis courts with coaching facility.', 2, '78 Galle Face Road', 'Colombo 03', 3000.00, 8, 'active'),
(9, 'Splash Swimming Complex', 'Olympic-size swimming pool with coaching programs.', 5, '23 Beach Road', 'Negombo', 2500.00, 50, 'active'),
(10, 'Elite Badminton Centre', 'Indoor badminton courts with professional lighting.', 6, '56 Peradeniya Road', 'Kandy', 2000.00, 16, 'active');

-- =====================
-- 13.7 Sample News Articles
-- =====================
INSERT IGNORE INTO news (title, slug, content, excerpt, category, author_id, status, views, published_at) VALUES
('Sri Lanka Cricket Season Opens with Exciting Matches', 'sl-cricket-season-opens', 'The 2026 Sri Lanka cricket season has officially begun with exciting matches...', 'The new cricket season promises exciting matches and fresh talent across the island.', 'Cricket', 1, 'published', 450, NOW() - INTERVAL 2 DAY),
('New Football Academy Opens in Colombo', 'new-football-academy-colombo', 'A state-of-the-art football academy has opened its doors in Colombo 07...', 'Young footballers now have access to world-class training facilities in the heart of Colombo.', 'Football', 1, 'published', 320, NOW() - INTERVAL 5 DAY),
('Swimming Championship Results 2026', 'swimming-champ-2026', 'The national swimming championship concluded with record-breaking performances...', 'Record-breaking performances were seen at this years national swimming championships.', 'Swimming', 2, 'published', 280, NOW() - INTERVAL 7 DAY),
('GoPlay Expands to Eastern Province', 'goplay-expands-east', 'GoPlay is excited to announce expansion of sports facility bookings to Eastern Province...', 'More sports facilities are now available for booking in Trincomalee and Batticaloa.', 'General', 1, 'published', 195, NOW() - INTERVAL 10 DAY),
('Top 10 Tennis Tips for Beginners', 'top-10-tennis-tips', 'Whether you are just picking up a racket or looking to improve your game, these tips will help...', 'Essential tennis tips for beginners looking to improve their game quickly.', 'Tennis', 2, 'published', 520, NOW() - INTERVAL 3 DAY),
('Badminton Tournament Draws Record Crowd', 'badminton-tournament-record', 'The annual inter-school badminton tournament attracted over 2000 spectators this year...', 'Over 2000 spectators watched young players compete at the inter-school tournament.', 'Badminton', 1, 'published', 180, NOW() - INTERVAL 12 DAY);

-- =====================
-- 13.8 Sample Promotions
-- =====================
INSERT IGNORE INTO promotions (title, subtitle, link_url, link_text, position, bg_color, text_color, priority, is_active, created_by) VALUES
('Welcome to GoPlay!', 'Book grounds, find coaches, and shop for sports gear all in one place.', '/book-ground', 'Book Now', 'hero', '#3b82f6', '#ffffff', 10, 1, 1),
('Become a Provider', 'Register as a ground owner, coach, or shop owner and start earning today.', '/provider/join', 'Join Now', 'hero', '#10b981', '#ffffff', 8, 1, 1),
('New Sports Gear Available', 'Check out the latest equipment from top brands at great prices.', '/shop', 'Shop Now', 'hero', '#8b5cf6', '#ffffff', 5, 1, 1);

-- =====================
-- 13.9 Sample Contact Messages
-- =====================
INSERT IGNORE INTO contact_messages (name, email, phone, subject, message, status) VALUES
('John Doe', 'john@example.com', '0771234567', 'Booking Question', 'Hi, I wanted to know if I can book a cricket ground for a whole day? What are the rates for a full-day booking?', 'unread'),
('Sarah Silva', 'sarah@example.com', '0779876543', 'Coach Registration', 'I am a certified swimming coach with 8 years of experience. I would like to register as a coach on GoPlay. How do I proceed?', 'unread'),
('Mike Fernando', 'mike@example.com', NULL, 'Payment Issue', 'I made a payment for my order #GP12345 three days ago but it still shows as pending. Can you please help resolve this?', 'read'),
('Kumari Perera', 'kumari@example.com', '0762345678', 'Feedback', 'Great platform! I love how easy it is to book a badminton court. Would be great if you add more courts in Kandy area.', 'replied');

-- =====================
-- 13.10 Sample Provider Applications
-- =====================
INSERT IGNORE INTO provider_applications (user_id, provider_type, status, first_name, last_name, email, phone, city, facility_name, facility_address, created_at) VALUES
(NULL, 'ground_owner', 'pending', 'Amith', 'Bandara', 'amith@example.com', '+94771112233', 'Colombo', 'Bandara Sports Complex', '45 Lotus Road, Colombo 05', NOW() - INTERVAL 2 DAY),
(NULL, 'coach', 'pending', 'Dilani', 'Fernando', 'dilani@example.com', '+94779988776', 'Kandy', NULL, NULL, NOW() - INTERVAL 1 DAY),
(NULL, 'shop_owner', 'pending', 'Nuwan', 'Herath', 'nuwan@example.com', '+94773344556', 'Galle', NULL, NULL, NOW() - INTERVAL 3 DAY);

-- =====================
-- 13.11 Sample Notifications (for admin)
-- =====================
INSERT IGNORE INTO notifications (user_id, type, title, message, is_read) VALUES
(1, 'system', 'New Provider Application', 'Amith Bandara has submitted a ground owner application.', FALSE),
(1, 'system', 'New Provider Application', 'Dilani Fernando has submitted a coach application.', FALSE),
(1, 'system', 'New Contact Message', 'New contact message from John Doe regarding "Booking Question".', FALSE),
(1, 'system', 'Welcome!', 'Welcome to GoPlay Admin Dashboard. Get started by reviewing pending applications.', TRUE);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SECTION 14: PAYOUT SYSTEM TABLES
-- ============================================================

-- 14.1 Shop Owner Balances (per-seller wallet)
CREATE TABLE IF NOT EXISTS shop_owner_balances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_owner_id INT NOT NULL UNIQUE,
    available_balance DECIMAL(12,2) DEFAULT 0.00,
    pending_balance DECIMAL(12,2) DEFAULT 0.00,
    total_earned DECIMAL(12,2) DEFAULT 0.00,
    total_withdrawn DECIMAL(12,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'LKR',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sob_owner (shop_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14.2 Shop Owner Transactions (immutable ledger)
CREATE TABLE IF NOT EXISTS shop_owner_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_owner_id INT NOT NULL,
    order_id INT DEFAULT NULL,
    payout_id INT DEFAULT NULL,
    type ENUM('credit_sale', 'debit_withdrawal', 'debit_refund', 'credit_adjustment') NOT NULL,
    gross_amount DECIMAL(12,2) NOT NULL,
    fee_amount DECIMAL(12,2) DEFAULT 0.00,
    net_amount DECIMAL(12,2) NOT NULL,
    balance_after DECIMAL(12,2) NOT NULL,
    description TEXT,
    status ENUM('pending', 'completed', 'failed', 'reversed') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_sot_owner (shop_owner_id),
    INDEX idx_sot_type (type),
    INDEX idx_sot_created (created_at),
    INDEX idx_sot_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14.3 Shop Owner Payouts (withdrawal requests)
CREATE TABLE IF NOT EXISTS shop_owner_payouts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shop_owner_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('pending', 'processing', 'completed', 'rejected') DEFAULT 'pending',
    payout_method VARCHAR(20) DEFAULT 'bank_transfer',
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    account_holder VARCHAR(100),
    branch_name VARCHAR(100),
    admin_notes TEXT,
    rejection_reason TEXT,
    reviewed_by INT DEFAULT NULL,
    reviewed_at TIMESTAMP NULL,
    transaction_reference VARCHAR(100),
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (shop_owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sop_owner (shop_owner_id),
    INDEX idx_sop_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14.4 Platform Earnings
CREATE TABLE IF NOT EXISTS platform_earnings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT DEFAULT NULL,
    order_id INT DEFAULT NULL,
    booking_id INT DEFAULT NULL,
    booking_type VARCHAR(20) DEFAULT NULL,
    transaction_amount DECIMAL(12,2) NOT NULL,
    service_fee_percentage DECIMAL(5,2) NOT NULL,
    service_fee_amount DECIMAL(12,2) NOT NULL,
    merchant_amount DECIMAL(12,2) NOT NULL,
    merchant_id INT DEFAULT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('pending', 'completed', 'withdrawn', 'refunded') DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (merchant_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_pe_order (order_id),
    INDEX idx_pe_merchant (merchant_id),
    INDEX idx_pe_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 15: ADDITIONAL SEED DATA
-- ============================================================

-- 15.1 Product Categories
INSERT IGNORE INTO categories (id, name, slug, description, icon) VALUES
(1, 'Cricket', 'cricket', 'Cricket equipment, bats, balls, pads and accessories', 'fa-baseball-ball'),
(2, 'Football', 'football', 'Football boots, balls, jerseys and training gear', 'fa-futbol'),
(3, 'Badminton', 'badminton', 'Badminton rackets, shuttlecocks and accessories', 'fa-table-tennis'),
(4, 'Swimming', 'swimming', 'Swimwear, goggles, caps and pool accessories', 'fa-swimmer'),
(5, 'Tennis', 'tennis', 'Tennis rackets, balls, shoes and apparel', 'fa-table-tennis'),
(6, 'Running', 'running', 'Running shoes, apparel and fitness trackers', 'fa-running'),
(7, 'Gym & Fitness', 'gym-fitness', 'Gym equipment, weights, yoga mats and supplements', 'fa-dumbbell'),
(8, 'Basketball', 'basketball', 'Basketballs, hoops, shoes and jerseys', 'fa-basketball-ball');

-- 15.2 Sample Products
INSERT IGNORE INTO products (id, name, sku, description, short_description, category_id, shop_owner_id, brand, price, compare_price, stock_quantity, min_stock_level, images, status, rating, total_reviews) VALUES
(1, 'MRF Genius Grand Edition Cricket Bat', 'PRD-CRICKET-001', 'Premium English willow cricket bat used by international players. Features a thick edge profile for maximum power and a mid-to-low sweet spot for aggressive stroke play. Comes with a premium bat cover.', 'Premium English willow bat with thick edge profile', 1, 5, 'MRF', 45000.00, 55000.00, 15, 3, '["public/images/products/cricket-bat-1.jpg"]', 'active', 4.70, 23),
(2, 'SG Test Opening Cricket Ball (Pack of 3)', 'PRD-CRICKET-002', 'SG Test quality leather cricket ball. Hand-stitched with premium alum-tanned leather. Ideal for competitive cricket matches. Pack of 3 balls.', 'Hand-stitched premium leather cricket balls', 1, 5, 'SG', 3500.00, 4200.00, 50, 10, '["public/images/products/cricket-ball-1.jpg"]', 'active', 4.50, 45),
(3, 'Nike Mercurial Superfly Football Boots', 'PRD-FOOT-001', 'Nike Mercurial Superfly 9 Elite FG football boots with dynamic fit collar and All Conditions Control technology. Designed for speed on firm ground surfaces.', 'Elite speed boots with ACC technology', 2, 5, 'Nike', 32000.00, 38000.00, 8, 2, '["public/images/products/football-boots-1.jpg"]', 'active', 4.80, 18),
(4, 'Adidas Brazuca Official Match Football', 'PRD-FOOT-002', 'Official thermally bonded match football. FIFA Quality Pro certified. Features Adidas proprietary panel technology for perfect roundness and consistent flight.', 'FIFA Quality Pro certified match ball', 2, 5, 'Adidas', 12500.00, 15000.00, 25, 5, '["public/images/products/football-1.jpg"]', 'active', 4.60, 32),
(5, 'Yonex Astrox 88D Pro Badminton Racket', 'PRD-BAD-001', 'Professional level badminton racket with Rotational Generator System for steep angle smashes. Stiff shaft flex with head-heavy balance. Strung with BG65 at 28 lbs.', 'Pro-level racket for powerful smashes', 3, 5, 'Yonex', 28000.00, 32000.00, 12, 3, '["public/images/products/badminton-racket-1.jpg"]', 'active', 4.90, 15),
(6, 'Speedo Fastskin LZR Pure Intent Swimsuit', 'PRD-SWIM-001', 'Competition-level racing swimsuit with ultra-lightweight, water-repellent bonded construction. FINA approved for competition use. Provides maximum compression for reduced drag.', 'FINA-approved competition racing swimsuit', 4, 5, 'Speedo', 18500.00, 22000.00, 10, 2, '["public/images/products/swimsuit-1.jpg"]', 'active', 4.40, 8),
(7, 'Wilson Pro Staff RF97 Tennis Racket', 'PRD-TENNIS-001', 'Roger Federer signature model tennis racket. Features Countervail technology for reduced vibration and muscle fatigue. 97 sq in head size with 16x19 string pattern.', 'RF signature racket with Countervail tech', 5, 5, 'Wilson', 35000.00, 42000.00, 6, 2, '["public/images/products/tennis-racket-1.jpg"]', 'active', 4.75, 12),
(8, 'Nike Air Zoom Pegasus 40 Running Shoes', 'PRD-RUN-001', 'Versatile daily running shoes with responsive Zoom Air cushioning and breathable mesh upper. Suitable for road running and light trail use. Available in multiple colorways.', 'Responsive daily running shoes', 6, 5, 'Nike', 16500.00, 19000.00, 30, 5, '["public/images/products/running-shoes-1.jpg"]', 'active', 4.65, 56),
(9, 'Bowflex SelectTech 552 Adjustable Dumbbells', 'PRD-GYM-001', 'Space-efficient adjustable dumbbells that replace 15 sets of weights. Adjustable from 5 to 52.5 lbs with a quick turn dial system. Includes storage tray.', 'Adjustable dumbbells replacing 15 sets', 7, 5, 'Bowflex', 85000.00, 95000.00, 4, 1, '["public/images/products/dumbbells-1.jpg"]', 'active', 4.55, 19),
(10, 'Spalding NBA Official Game Basketball', 'PRD-BASKET-001', 'Official NBA game ball made with Horween leather. Superior grip and consistent bounce. Used in official NBA games worldwide.', 'Official NBA Horween leather game ball', 8, 5, 'Spalding', 22000.00, 26000.00, 18, 3, '["public/images/products/basketball-1.jpg"]', 'active', 4.70, 27),
(11, 'DSC Intense Shoc Cricket Batting Pads', 'PRD-CRICKET-003', 'Lightweight batting pads with high-density foam for superior protection. Features traditional buckle strapping and reinforced knee roll. Right-hand batsman design.', 'Lightweight pads with superior protection', 1, 5, 'DSC', 8500.00, 10000.00, 20, 5, '["public/images/products/cricket-pads-1.jpg"]', 'active', 4.30, 14),
(12, 'Under Armour Curry 10 Basketball Shoes', 'PRD-BASKET-002', 'Stephen Curry signature basketball shoes with UA Flow cushioning for court feel and traction. Lightweight knit upper with internal heel counter for lockdown support.', 'Curry signature shoes with UA Flow', 8, 5, 'Under Armour', 24500.00, 28000.00, 10, 2, '["public/images/products/basketball-shoes-1.jpg"]', 'active', 4.60, 21);

-- 15.3 Payout System Settings
INSERT IGNORE INTO settings (key_name, value, description, type) VALUES
('min_payout_amount', '10000', 'Minimum balance required for shop owner withdrawal (LKR)', 'number'),
('service_fee_percentage', '5', 'Platform commission percentage on shop sales', 'number'),
('payout_processing_days', '3', 'Estimated business days to process payouts', 'number');

-- ============================================================
-- SETUP COMPLETE!
-- ============================================================
-- Admin login:  admin1@goplay.lk / password123
-- User login:   user1@goplay.lk  / password123
-- Coach login:  coach1@goplay.lk / password123
-- Ground Owner: groundowner1@goplay.lk / password123
-- Shop Owner:   shopowner1@goplay.lk / password123
-- ============================================================
