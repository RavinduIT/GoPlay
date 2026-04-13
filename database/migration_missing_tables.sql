-- ===================================================================
-- GoPlay Missing Tables Migration
-- Adds all tables referenced by code but missing from the database
-- ===================================================================

USE goplay_sports_platform;

-- 1. coach_availability
CREATE TABLE IF NOT EXISTS coach_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time TIME NOT NULL DEFAULT '09:00:00',
    end_time TIME NOT NULL DEFAULT '18:00:00',
    slot_duration INT NOT NULL DEFAULT 60,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_coach_day (coach_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. coach_certificates
CREATE TABLE IF NOT EXISTS coach_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255) DEFAULT NULL,
    issue_date DATE DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    credential_id VARCHAR(255) DEFAULT NULL,
    certificate_file VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. coach_achievements
CREATE TABLE IF NOT EXISTS coach_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    date_achieved DATE DEFAULT NULL,
    category ENUM('competition','certification','milestone','award','other') DEFAULT 'other',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. coach_facilities (links coaches to sports facilities)
CREATE TABLE IF NOT EXISTS coach_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    facility_id INT NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    UNIQUE KEY unique_coach_facility (coach_id, facility_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. coach_review_replies
CREATE TABLE IF NOT EXISTS coach_review_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    coach_id INT NOT NULL,
    reply_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES coach_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review_reply (review_id, coach_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Ensure shopping_carts exists (code uses plural)
-- If 'shopping_cart' (singular) exists, rename it; otherwise skip
SET @tbl_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'goplay_sports_platform' AND table_name = 'shopping_cart');
SET @sql = IF(@tbl_exists > 0, 'RENAME TABLE shopping_cart TO shopping_carts', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7. Seed default coach_availability for existing coaches
INSERT IGNORE INTO coach_availability (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
SELECT c.id, d.day_of_week, '09:00:00', '18:00:00', 60, 
       CASE WHEN d.day_of_week IN ('Saturday','Sunday') THEN 0 ELSE 1 END
FROM coaches c
CROSS JOIN (
    SELECT 'Monday' AS day_of_week UNION ALL
    SELECT 'Tuesday' UNION ALL
    SELECT 'Wednesday' UNION ALL
    SELECT 'Thursday' UNION ALL
    SELECT 'Friday' UNION ALL
    SELECT 'Saturday' UNION ALL
    SELECT 'Sunday'
) d;

SELECT 'All missing tables created successfully!' AS status;
