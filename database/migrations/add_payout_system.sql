-- ============================================================
-- GoPlay Payment & Payout System Migration
-- ============================================================
-- Run this file to add shop owner balance tracking, 
-- transaction ledger, and payout request tables.
-- ============================================================

USE goplay_sports_platform;

-- ============================================================
-- 1. Shop Owner Balances (per-seller wallet)
-- ============================================================
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

-- ============================================================
-- 2. Shop Owner Transactions (immutable ledger)
-- ============================================================
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

-- ============================================================
-- 3. Shop Owner Payouts (withdrawal requests)
-- ============================================================
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

-- ============================================================
-- 4. Add payout details to shop_owner_profiles
-- ============================================================
-- These columns store the shop owner's default payout bank details
ALTER TABLE shop_owner_profiles 
    ADD COLUMN IF NOT EXISTS payout_bank_name VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS payout_account_number VARCHAR(50) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS payout_account_holder VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS payout_branch_name VARCHAR(100) DEFAULT NULL;

-- ============================================================
-- 5. Extend notifications type enum
-- ============================================================
ALTER TABLE notifications MODIFY COLUMN type ENUM(
    'booking_confirmation', 'payment_success', 'order_status', 
    'review_request', 'promotional', 'system',
    'new_order', 'order_shipped', 'order_delivered',
    'payout_requested', 'payout_approved', 'payout_rejected',
    'balance_credited'
) NOT NULL;

-- ============================================================
-- 6. Add payout-related settings
-- ============================================================
INSERT IGNORE INTO settings (key_name, value, description, type) VALUES
('min_payout_amount', '10000', 'Minimum balance required for shop owner withdrawal (LKR)', 'number'),
('service_fee_percentage', '5', 'Platform commission percentage on shop sales', 'number'),
('payout_processing_days', '3', 'Estimated business days to process payouts', 'number');

-- ============================================================
-- 7. Create platform_earnings table if not exists
-- ============================================================
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
-- Migration complete!
-- ============================================================
