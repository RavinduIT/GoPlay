-- Commission Invoice System
-- Platform charges 10% commission on ground owner earnings

CREATE TABLE IF NOT EXISTS commission_invoices (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number   VARCHAR(30)     NOT NULL UNIQUE,
    owner_id         INT UNSIGNED    NOT NULL,
    period_start     DATE            NOT NULL,
    period_end       DATE            NOT NULL,
    total_earnings   DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
    commission_rate  DECIMAL(5,4)    NOT NULL DEFAULT 0.1000,
    commission_amount DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    status           ENUM('sent','paid','overdue') NOT NULL DEFAULT 'sent',
    due_date         DATE            NOT NULL,
    paid_date        DATE            NULL,
    notes            TEXT            NULL,
    created_by       INT UNSIGNED    NOT NULL,
    created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_owner  (owner_id),
    INDEX idx_status (status),
    INDEX idx_due    (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS commission_invoice_items (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id       INT UNSIGNED    NOT NULL,
    earning_id       INT UNSIGNED    NOT NULL UNIQUE,
    earning_date     DATE            NOT NULL,
    facility_name    VARCHAR(150)    NULL,
    gross_amount     DECIMAL(10,2)   NOT NULL,
    commission_amount DECIMAL(10,2)  NOT NULL,
    INDEX idx_invoice (invoice_id),
    CONSTRAINT fk_cii_invoice FOREIGN KEY (invoice_id)
        REFERENCES commission_invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
