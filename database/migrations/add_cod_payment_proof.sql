-- ============================================================
-- Migration: Add cod_payment_proof column to orders table
-- Stores the file path of the uploaded payment receipt for COD orders.
-- ============================================================

USE goplay_sports_platform;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS cod_payment_proof VARCHAR(500) DEFAULT NULL
    AFTER notes;
