-- Admin Earnings & Payment System Schema
-- Run this after your existing schema.sql

USE goplay_sports_platform;

-- Platform Earnings Table - Tracks all service fees collected
CREATE TABLE IF NOT EXISTS platform_earnings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    order_id INT NULL,
    booking_id INT NULL,
    booking_type ENUM('facility', 'coach', 'product') NULL,
    transaction_amount DECIMAL(10,2) NOT NULL,
    service_fee_percentage DECIMAL(5,2) NOT NULL,
    service_fee_amount DECIMAL(10,2) NOT NULL,
    merchant_amount DECIMAL(10,2) NOT NULL,
    merchant_id INT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('pending', 'completed', 'withdrawn', 'cancelled') DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (merchant_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_booking_type (booking_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Withdrawal Requests Table - Track admin withdrawal requests
CREATE TABLE IF NOT EXISTS withdrawal_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    withdrawal_method ENUM('bank_transfer', 'check', 'paypal', 'other') DEFAULT 'bank_transfer',
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    account_holder VARCHAR(100),
    paypal_email VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'processing', 'completed', 'rejected', 'cancelled') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    processed_by INT NULL,
    rejection_reason TEXT,
    transaction_reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_admin (admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Platform Settings Table Update - Add service fee settings
INSERT INTO settings (key_name, value, description, type) VALUES
('service_fee_percentage', '5.00', 'Platform service fee percentage (default 5%)', 'number'),
('min_withdrawal_amount', '1000.00', 'Minimum withdrawal amount in LKR', 'number'),
('withdrawal_processing_time', '3', 'Withdrawal processing time in business days', 'number'),
('enable_auto_withdrawal', 'false', 'Enable automatic withdrawal processing', 'boolean')
ON DUPLICATE KEY UPDATE value=value;

-- Update payments table to track service fees
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS service_fee_amount DECIMAL(10,2) DEFAULT 0.00 AFTER amount,
ADD COLUMN IF NOT EXISTS merchant_amount DECIMAL(10,2) DEFAULT 0.00 AFTER service_fee_amount;

-- Create view for earnings summary
CREATE OR REPLACE VIEW earnings_summary AS
SELECT 
    DATE(created_at) as date,
    COUNT(*) as transaction_count,
    SUM(transaction_amount) as total_transactions,
    SUM(service_fee_amount) as total_service_fees,
    SUM(merchant_amount) as total_merchant_payments,
    booking_type,
    status
FROM platform_earnings
GROUP BY DATE(created_at), booking_type, status;

-- Create view for withdrawal summary
CREATE OR REPLACE VIEW withdrawal_summary AS
SELECT 
    admin_id,
    COUNT(*) as total_requests,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_withdrawn,
    SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount,
    SUM(CASE WHEN status = 'processing' THEN amount ELSE 0 END) as processing_amount
FROM withdrawal_requests
GROUP BY admin_id;

-- Insert sample data for testing
INSERT INTO platform_earnings (
    payment_id, order_id, booking_id, booking_type,
    transaction_amount, service_fee_percentage, service_fee_amount, 
    merchant_amount, merchant_id, status, description
) 
SELECT 
    p.id,
    p.order_id,
    NULL,
    'product',
    p.amount,
    5.00,
    ROUND(p.amount * 0.05, 2),
    ROUND(p.amount * 0.95, 2),
    o.user_id,
    CASE p.status 
        WHEN 'completed' THEN 'completed'
        WHEN 'pending' THEN 'pending'
        ELSE 'cancelled'
    END,
    CONCAT('Service fee from order #', o.order_number)
FROM payments p
LEFT JOIN orders o ON p.order_id = o.id
WHERE p.id NOT IN (SELECT payment_id FROM platform_earnings)
LIMIT 100;

-- Create indexes for better performance
CREATE INDEX idx_platform_earnings_merchant ON platform_earnings(merchant_id);
CREATE INDEX idx_platform_earnings_date ON platform_earnings(created_at);
CREATE INDEX idx_withdrawal_requests_date ON withdrawal_requests(requested_at);

COMMIT;