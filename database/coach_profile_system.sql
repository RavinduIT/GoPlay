-- ============================================================
-- Coach Profile System – Certificates & Achievements
-- GoPlay Sports Platform
-- Run AFTER coach_booking_system.sql
-- ============================================================

USE goplay_sports_platform;

-- ── Certificates ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS coach_certificates (
    id                   INT PRIMARY KEY AUTO_INCREMENT,
    coach_id             INT NOT NULL,
    title                VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255)  DEFAULT NULL,
    issue_date           DATE          DEFAULT NULL,
    expiry_date          DATE          DEFAULT NULL,
    credential_id        VARCHAR(100)  DEFAULT NULL,
    created_at           TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    INDEX idx_coach_cert (coach_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Achievements ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS coach_achievements (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    coach_id      INT NOT NULL,
    title         VARCHAR(255) NOT NULL,
    description   TEXT         DEFAULT NULL,
    date_achieved DATE         DEFAULT NULL,
    category      ENUM('award','competition','certification','milestone','other') DEFAULT 'other',
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    INDEX idx_coach_ach (coach_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed: sample certificates for coach id 1 ─────────────────
INSERT IGNORE INTO coach_certificates (id, coach_id, title, issuing_organization, issue_date, expiry_date, credential_id)
VALUES
(1, 1, 'Level 3 Cricket Coaching Certificate', 'Sri Lanka Cricket Board', '2019-06-15', NULL, 'SLC-L3-2019-001'),
(2, 1, 'Sports First Aid & Safety',            'Sri Lanka Red Cross',    '2020-03-10', '2025-03-10', 'RC-2020-445');

-- ── Seed: sample achievements for coach id 1 ─────────────────
INSERT IGNORE INTO coach_achievements (id, coach_id, title, description, date_achieved, category)
VALUES
(1, 1, 'Best Youth Coach Award', 'Awarded by the Provincial Cricket Association for outstanding contribution to youth cricket development.', '2021-09-01', 'award'),
(2, 1, 'International Playing Career', 'Represented Sri Lanka National Cricket Team in international matches across three formats.', '2019-12-01', 'milestone');
