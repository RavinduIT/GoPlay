USE goplay_sports_platform;

CREATE TABLE IF NOT EXISTS coach_facilities (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    coach_id    INT NOT NULL,
    facility_id INT NOT NULL,
    is_primary  TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id)    REFERENCES coaches(id)           ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    UNIQUE KEY uq_coach_facility (coach_id, facility_id),
    INDEX idx_coach    (coach_id),
    INDEX idx_facility (facility_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
