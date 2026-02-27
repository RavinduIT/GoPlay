-- ============================================================
-- Coach Booking System - Complete Schema
-- GoPlay Sports Platform
-- Run this on a fresh install AFTER schema.sql
-- ============================================================

USE goplay_sports_platform;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- COACH AVAILABILITY SCHEDULE
-- Coaches define their weekly recurring availability
-- ============================================================
CREATE TABLE IF NOT EXISTS coach_availability (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    coach_id      INT NOT NULL,
    day_of_week   ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time    TIME NOT NULL,
    end_time      TIME NOT NULL,
    slot_duration INT NOT NULL DEFAULT 60   COMMENT 'Minutes per bookable slot',
    is_available  TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    UNIQUE KEY uq_coach_day (coach_id, day_of_week),
    INDEX idx_coach_available (coach_id, is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- COACH BOOKINGS
-- Individual session bookings made by users
-- ============================================================
DROP TABLE IF EXISTS coach_bookings;
CREATE TABLE coach_bookings (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    user_id             INT NOT NULL,
    coach_id            INT NOT NULL,
    booking_date        DATE NOT NULL,
    start_time          TIME NOT NULL,
    end_time            TIME NOT NULL,
    duration            INT NOT NULL DEFAULT 60            COMMENT 'Minutes',
    session_type        ENUM('individual','group','assessment') DEFAULT 'individual',
    session_title       VARCHAR(255)                       DEFAULT NULL,
    total_amount        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_status      ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    status              ENUM('pending','confirmed','completed','cancelled','no_show') DEFAULT 'confirmed',
    special_requests    TEXT                               DEFAULT NULL,
    coach_notes         TEXT                               DEFAULT NULL  COMMENT 'Internal notes by coach',
    cancellation_reason TEXT                               DEFAULT NULL,
    cancelled_by        ENUM('user','coach','admin')       DEFAULT NULL,
    cancelled_at        TIMESTAMP                          NULL DEFAULT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    INDEX idx_coach_date   (coach_id, booking_date),
    INDEX idx_user_status  (user_id,  status),
    INDEX idx_date_status  (booking_date, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED: Default availability for all active coaches
-- Monday–Friday  08:00–18:00  (60-min slots)
-- Saturday       09:00–16:00  (60-min slots)
-- ============================================================
INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT id, 'Monday',    '08:00:00', '18:00:00', 60, 1 FROM coaches WHERE status = 'active';

INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT id, 'Tuesday',   '08:00:00', '18:00:00', 60, 1 FROM coaches WHERE status = 'active';

INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT id, 'Wednesday', '08:00:00', '18:00:00', 60, 1 FROM coaches WHERE status = 'active';

INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT id, 'Thursday',  '08:00:00', '18:00:00', 60, 1 FROM coaches WHERE status = 'active';

INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT id, 'Friday',    '08:00:00', '18:00:00', 60, 1 FROM coaches WHERE status = 'active';

INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT id, 'Saturday',  '09:00:00', '16:00:00', 60, 1 FROM coaches WHERE status = 'active';
