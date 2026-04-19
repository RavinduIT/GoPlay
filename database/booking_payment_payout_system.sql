-- ============================================================
-- Booking Payment & Payout System
-- Ground Owner + Coach wallet, payout request tables
-- ============================================================

-- Ground Owner Balances (wallet)
CREATE TABLE IF NOT EXISTS ground_owner_balances (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    owner_id        INT NOT NULL UNIQUE,
    available_balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    pending_balance   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_earned      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_withdrawn   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency          VARCHAR(3)    NOT NULL DEFAULT 'LKR',
    updated_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Ground Owner Payout Requests
CREATE TABLE IF NOT EXISTS ground_owner_payouts (
    id                    INT PRIMARY KEY AUTO_INCREMENT,
    owner_id              INT NOT NULL,
    reviewed_by           INT NULL,
    amount                DECIMAL(12,2) NOT NULL,
    currency              VARCHAR(3)    NOT NULL DEFAULT 'LKR',
    status                ENUM('pending','processing','completed','rejected') NOT NULL DEFAULT 'pending',
    payout_method         VARCHAR(20)   DEFAULT 'bank_transfer',
    bank_name             VARCHAR(100),
    account_number        VARCHAR(50),
    account_holder        VARCHAR(100),
    branch_name           VARCHAR(100),
    admin_notes           TEXT,
    rejection_reason      TEXT,
    transaction_reference VARCHAR(100),
    requested_at          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at           TIMESTAMP     NULL,
    completed_at          TIMESTAMP     NULL,
    FOREIGN KEY (owner_id)    REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

-- Coach Balances (wallet)
CREATE TABLE IF NOT EXISTS coach_balances (
    id                INT PRIMARY KEY AUTO_INCREMENT,
    coach_id          INT NOT NULL UNIQUE,
    available_balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    pending_balance   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_earned      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_withdrawn   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency          VARCHAR(3)    NOT NULL DEFAULT 'LKR',
    updated_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id)
);

-- Coach Payout Requests
CREATE TABLE IF NOT EXISTS coach_payouts (
    id                    INT PRIMARY KEY AUTO_INCREMENT,
    coach_id              INT NOT NULL,
    reviewed_by           INT NULL,
    amount                DECIMAL(12,2) NOT NULL,
    currency              VARCHAR(3)    NOT NULL DEFAULT 'LKR',
    status                ENUM('pending','processing','completed','rejected') NOT NULL DEFAULT 'pending',
    payout_method         VARCHAR(20)   DEFAULT 'bank_transfer',
    bank_name             VARCHAR(100),
    account_number        VARCHAR(50),
    account_holder        VARCHAR(100),
    branch_name           VARCHAR(100),
    admin_notes           TEXT,
    rejection_reason      TEXT,
    transaction_reference VARCHAR(100),
    requested_at          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at           TIMESTAMP     NULL,
    completed_at          TIMESTAMP     NULL,
    FOREIGN KEY (coach_id)    REFERENCES coaches(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);
