-- ============================================================
-- Fix notifications.type column
-- The ENUM was missing booking_cancelled and coach_booking,
-- causing silent insert failures when ground owners decline
-- bookings or when coach booking notifications are created.
-- Change to VARCHAR(50) so any type string can be stored.
-- ============================================================

ALTER TABLE notifications
    MODIFY COLUMN type VARCHAR(50) NOT NULL;
