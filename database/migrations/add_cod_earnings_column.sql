-- ============================================================
-- Migration: Add cod_earnings column to shop_owner_balances
-- COD earnings are tracked separately and are NOT withdrawable.
-- ============================================================

USE goplay_sports_platform;

ALTER TABLE shop_owner_balances
    ADD COLUMN IF NOT EXISTS cod_earnings DECIMAL(12,2) NOT NULL DEFAULT 0.00
    AFTER pending_balance;
