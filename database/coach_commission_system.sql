-- Coach Commission System
-- This version matches the project's live MySQL schema, which uses signed INT keys.
-- Run statements individually if some columns already exist in your environment.

-- 1. Add payment method to coach bookings
ALTER TABLE coach_bookings
    ADD COLUMN payment_method ENUM('online','cash') NULL AFTER payment_status;

-- 2. Coach earnings transaction table
CREATE TABLE IF NOT EXISTS coach_earnings (
    id INT NOT NULL AUTO_INCREMENT,
    coach_id INT NOT NULL,
    booking_id INT DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    earning_date DATE NOT NULL,
    payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'paid',
    payment_method VARCHAR(50) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_coach_date (coach_id, earning_date),
    KEY idx_booking (booking_id),
    KEY idx_payment_status (payment_status),
    CONSTRAINT fk_coach_earnings_coach
        FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    CONSTRAINT fk_coach_earnings_booking
        FOREIGN KEY (booking_id) REFERENCES coach_bookings(id) ON DELETE SET NULL
);

-- 3a. Extend commission invoices for coach providers
ALTER TABLE commission_invoices
    ADD COLUMN provider_type ENUM('ground_owner','coach') NOT NULL DEFAULT 'ground_owner' AFTER id;

-- 3b. Coach reference must match signed coach IDs
ALTER TABLE commission_invoices
    ADD COLUMN coach_id INT NULL AFTER owner_id;

-- 3c. Coach invoices do not always have an owner_id
ALTER TABLE commission_invoices
    MODIFY COLUMN owner_id INT NULL;

-- 3d. Optional foreign key for coach invoices
ALTER TABLE commission_invoices
    ADD CONSTRAINT fk_commission_invoices_coach
        FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE SET NULL;

-- 4a. Coach earnings reference in invoice items
ALTER TABLE commission_invoice_items
    ADD COLUMN coach_earning_id INT NULL AFTER earning_id;

-- 4b. Display label for coach invoice line items
ALTER TABLE commission_invoice_items
    ADD COLUMN provider_name VARCHAR(150) NULL AFTER facility_name;

-- 4c. Ground-owner earning is optional for coach invoice items
ALTER TABLE commission_invoice_items
    MODIFY COLUMN earning_id INT NULL;

-- 4d. Foreign key for coach invoice items
ALTER TABLE commission_invoice_items
    ADD CONSTRAINT fk_commission_items_coach_earning
        FOREIGN KEY (coach_earning_id) REFERENCES coach_earnings(id) ON DELETE CASCADE;

-- 5. Backfill transaction rows for already-paid completed bookings
INSERT INTO coach_earnings (coach_id, booking_id, amount, earning_date, payment_status, payment_method, notes)
SELECT
    cb.coach_id,
    cb.id,
    cb.total_amount,
    cb.booking_date,
    cb.payment_status,
    cb.payment_method,
    'Backfilled from completed coach booking'
FROM coach_bookings cb
LEFT JOIN coach_earnings ce ON ce.booking_id = cb.id
WHERE cb.status = 'completed'
  AND cb.payment_status = 'paid'
  AND ce.id IS NULL;
