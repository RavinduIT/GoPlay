-- Create shop_owner_profiles table
-- This table stores detailed information for shop owners linked to users table
-- Similar to ground_owner_profiles structure

USE goplay_sports_platform;

CREATE TABLE IF NOT EXISTS shop_owner_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Link to users table
    user_id INT NOT NULL UNIQUE,
    
    -- Business Information
    shop_name VARCHAR(255),
    business_name VARCHAR(255),
    business_registration_number VARCHAR(100),
    tax_identification_number VARCHAR(100),
    business_type ENUM('sole_proprietor', 'partnership', 'private_limited', 'public_limited') DEFAULT 'sole_proprietor',
    year_established YEAR,
    number_of_employees INT DEFAULT 0,
    
    -- Contact Information
    business_phone VARCHAR(20),
    business_email VARCHAR(255),
    shop_address TEXT,
    shop_city VARCHAR(100),
    shop_state VARCHAR(100),
    shop_postal_code VARCHAR(20),
    
    -- Business Details
    business_description TEXT,
    product_categories JSON COMMENT 'Array of product categories the shop sells',
    brand_names TEXT COMMENT 'Comma-separated list of brands',
    
    -- Online Presence
    website_url VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    twitter VARCHAR(255),
    
    -- Images and Documents
    shop_logo VARCHAR(255),
    shop_banner VARCHAR(255) COMMENT 'Banner/cover image for shop',
    business_license_document VARCHAR(255),
    tax_document VARCHAR(255),
    shop_images JSON COMMENT 'Array of shop/store images',
    
    -- Bank Details for Payments
    bank_name VARCHAR(100),
    bank_account_number VARCHAR(50),
    bank_account_holder VARCHAR(255),
    bank_branch VARCHAR(100),
    bank_swift_code VARCHAR(20),
    
    -- Delivery & Operations
    delivery_options JSON COMMENT 'Delivery methods: {"home_delivery": true, "pickup": true}',
    operating_hours JSON COMMENT 'Store operating hours',
    return_policy TEXT,
    warranty_policy TEXT,
    
    -- Statistics
    total_products INT DEFAULT 0,
    total_sales INT DEFAULT 0,
    total_revenue DECIMAL(12, 2) DEFAULT 0.00,
    average_rating DECIMAL(3, 2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    
    -- Profile Completion
    profile_completion_percentage INT DEFAULT 10,
    
    -- Status
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_shop_name (shop_name),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add helpful comment
ALTER TABLE shop_owner_profiles 
COMMENT = 'Stores detailed profile information for shop owners including business details, documents, and bank information';
