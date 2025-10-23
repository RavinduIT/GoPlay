-- Run this SQL to create the provider_applications table
-- You can run this in phpMyAdmin or MySQL Workbench

CREATE TABLE IF NOT EXISTS provider_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- User Reference (if logged in user)
    user_id INT NULL,

    -- Application Type
    provider_type ENUM('ground_owner', 'coach', 'shop_owner') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',

    -- Common Personal Information
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20),

    -- NIC Document (Common for all)
    nic_document VARCHAR(255),

    -- Ground Owner Specific Fields
    facility_name VARCHAR(255),
    facility_address TEXT,
    facility_city VARCHAR(100),
    facility_postal VARCHAR(20),
    sport_types JSON,
    number_of_courts INT,
    proposed_hourly_rate DECIMAL(10, 2),
    amenities JSON,
    facility_description TEXT,
    business_registration VARCHAR(255),
    ownership_proof VARCHAR(255),
    facility_images JSON,

    -- Coach Specific Fields
    sport_specialization VARCHAR(100),
    experience_years INT,
    session_rate DECIMAL(10, 2),
    qualifications TEXT,
    specialties JSON,
    bio TEXT,
    previous_experience TEXT,
    availability JSON,
    date_of_birth DATE,
    certifications JSON,
    profile_photo VARCHAR(255),

    -- Shop Owner Specific Fields
    shop_name VARCHAR(255),
    business_registration_number VARCHAR(100),
    shop_address TEXT,
    shop_city VARCHAR(100),
    shop_postal VARCHAR(20),
    product_categories JSON,
    business_type VARCHAR(50),
    year_established INT,
    number_of_employees INT,
    business_description TEXT,
    brand_names TEXT,
    website_url VARCHAR(255),
    social_media VARCHAR(255),
    delivery_options JSON,
    tax_document VARCHAR(255),
    shop_images JSON,

    -- Common Additional Documents
    additional_documents JSON,

    -- Admin Review
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    admin_notes TEXT,
    rejection_reason TEXT,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX idx_user_id (user_id),
    INDEX idx_email (email),
    INDEX idx_provider_type (provider_type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),

    -- Foreign Keys (commented out in case the users table doesn't have these columns)
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    -- FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
