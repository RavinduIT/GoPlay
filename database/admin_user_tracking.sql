-- Admin User Management and Tracking Tables
-- Run this SQL file to add login tracking and admin logging functionality

USE goplay_sports_platform;

-- User Logins Table (for tracking login history)
CREATE TABLE IF NOT EXISTS user_logins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    last_login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login_ip VARCHAR(45),
    user_agent TEXT,
    login_method ENUM('password', 'social', 'token') DEFAULT 'password',
    device_type ENUM('desktop', 'mobile', 'tablet', 'unknown') DEFAULT 'unknown',
    browser VARCHAR(100),
    platform VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_logins_user_id (user_id),
    INDEX idx_user_logins_date (last_login_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Logs Table (for tracking admin actions)
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action_type ENUM(
        'user_create', 
        'user_update', 
        'user_delete', 
        'role_change', 
        'status_change', 
        'password_reset',
        'permission_change',
        'system_setting',
        'data_export',
        'bulk_action'
    ) NOT NULL,
    target_user_id INT NULL,
    action_data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_admin_logs_admin (admin_id),
    INDEX idx_admin_logs_action (action_type),
    INDEX idx_admin_logs_date (created_at),
    INDEX idx_admin_logs_target (target_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Sessions Table (for active session management)
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_sessions_user (user_id),
    INDEX idx_user_sessions_token (session_token),
    INDEX idx_user_sessions_active (is_active, expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Activity Log (for detailed activity tracking)
CREATE TABLE IF NOT EXISTS user_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    activity_type ENUM(
        'login',
        'logout',
        'profile_update',
        'password_change',
        'booking_created',
        'booking_cancelled',
        'order_placed',
        'order_cancelled',
        'review_posted',
        'payment_made',
        'other'
    ) NOT NULL,
    activity_description TEXT,
    metadata JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_activity_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Statistics View (for quick stats access)
CREATE OR REPLACE VIEW user_statistics AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.user_type,
    u.status,
    u.created_at as joined_date,
    ul.last_login_at,
    ul.last_login_ip,
    COUNT(DISTINCT cb.id) as coach_booking_count,
    COUNT(DISTINCT fb.id) as facility_booking_count,
    COUNT(DISTINCT o.id) as order_count,
    COALESCE(SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END), 0) as total_spent,
    COUNT(DISTINCT CASE WHEN DATE(cb.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN cb.id END) as recent_bookings_30d,
    COUNT(DISTINCT CASE WHEN DATE(o.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN o.id END) as recent_orders_30d
FROM users u
LEFT JOIN user_logins ul ON u.id = ul.user_id AND ul.id = (
    SELECT id FROM user_logins WHERE user_id = u.id ORDER BY last_login_at DESC LIMIT 1
)
LEFT JOIN coach_bookings cb ON u.id = cb.user_id
LEFT JOIN facility_bookings fb ON u.id = fb.user_id
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY u.id, u.username, u.email, u.user_type, u.status, u.created_at, ul.last_login_at, ul.last_login_ip;

-- Insert some sample admin logs (optional)
INSERT INTO admin_logs (admin_id, action_type, target_user_id, action_data, ip_address) 
SELECT 
    (SELECT id FROM users WHERE user_type = 'admin' LIMIT 1),
    'system_setting',
    NULL,
    '{"action": "initial_setup", "description": "Admin user management system initialized"}',
    '127.0.0.1'
WHERE EXISTS (SELECT 1 FROM users WHERE user_type = 'admin');

-- Create stored procedure for recording login
DELIMITER $

CREATE PROCEDURE IF NOT EXISTS record_user_login(
    IN p_user_id INT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    DECLARE v_browser VARCHAR(100);
    DECLARE v_platform VARCHAR(100);
    DECLARE v_device_type VARCHAR(20);
    
    -- Simple browser detection (can be enhanced)
    SET v_browser = CASE
        WHEN p_user_agent LIKE '%Chrome%' THEN 'Chrome'
        WHEN p_user_agent LIKE '%Firefox%' THEN 'Firefox'
        WHEN p_user_agent LIKE '%Safari%' THEN 'Safari'
        WHEN p_user_agent LIKE '%Edge%' THEN 'Edge'
        ELSE 'Other'
    END;
    
    -- Simple platform detection
    SET v_platform = CASE
        WHEN p_user_agent LIKE '%Windows%' THEN 'Windows'
        WHEN p_user_agent LIKE '%Mac%' THEN 'MacOS'
        WHEN p_user_agent LIKE '%Linux%' THEN 'Linux'
        WHEN p_user_agent LIKE '%Android%' THEN 'Android'
        WHEN p_user_agent LIKE '%iOS%' THEN 'iOS'
        ELSE 'Unknown'
    END;
    
    -- Simple device detection
    SET v_device_type = CASE
        WHEN p_user_agent LIKE '%Mobile%' THEN 'mobile'
        WHEN p_user_agent LIKE '%Tablet%' THEN 'tablet'
        ELSE 'desktop'
    END;
    
    -- Insert login record
    INSERT INTO user_logins (
        user_id, 
        last_login_at, 
        last_login_ip, 
        user_agent,
        browser,
        platform,
        device_type
    ) VALUES (
        p_user_id,
        NOW(),
        p_ip_address,
        p_user_agent,
        v_browser,
        v_platform,
        v_device_type
    );
    
    -- Log activity
    INSERT INTO user_activity_log (user_id, activity_type, activity_description, ip_address)
    VALUES (p_user_id, 'login', CONCAT('User logged in from ', v_device_type, ' device'), p_ip_address);
END$

DELIMITER ;

-- Create stored procedure for admin action logging
DELIMITER $

CREATE PROCEDURE IF NOT EXISTS log_admin_action(
    IN p_admin_id INT,
    IN p_action_type VARCHAR(50),
    IN p_target_user_id INT,
    IN p_action_data JSON,
    IN p_ip_address VARCHAR(45)
)
BEGIN
    INSERT INTO admin_logs (
        admin_id,
        action_type,
        target_user_id,
        action_data,
        ip_address,
        user_agent
    ) VALUES (
        p_admin_id,
        p_action_type,
        p_target_user_id,
        p_action_data,
        p_ip_address,
        NULL
    );
END$

DELIMITER ;

-- Indexes for performance optimization
CREATE INDEX IF NOT EXISTS idx_users_type_status ON users(user_type, status);
CREATE INDEX IF NOT EXISTS idx_users_created ON users(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_users_email_username ON users(email, username);

-- Clean up old login records (keep last 100 per user)
DELIMITER $

CREATE PROCEDURE IF NOT EXISTS cleanup_old_logins()
BEGIN
    DELETE ul1 FROM user_logins ul1
    INNER JOIN (
        SELECT user_id, id
        FROM (
            SELECT user_id, id,
                   ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY last_login_at DESC) as rn
            FROM user_logins
        ) ranked
        WHERE rn > 100
    ) ul2 ON ul1.id = ul2.id;
END$

DELIMITER ;

-- Create event to auto-cleanup (runs daily)
-- Note: Make sure the event scheduler is enabled: SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS cleanup_old_data
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    -- Cleanup old login records (keep last 100 per user)
    CALL cleanup_old_logins();
    
    -- Cleanup expired sessions
    DELETE FROM user_sessions 
    WHERE expires_at < NOW() OR (is_active = FALSE AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY));
    
    -- Cleanup old activity logs (keep last 6 months)
    DELETE FROM user_activity_log 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
END;

-- Grant necessary permissions (adjust as needed)
-- GRANT SELECT, INSERT ON user_logins TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT ON admin_logs TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON user_sessions TO 'your_app_user'@'localhost';
-- GRANT SELECT, INSERT ON user_activity_log TO 'your_app_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE record_user_login TO 'your_app_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE log_admin_action TO 'your_app_user'@'localhost';

-- Commit changes
COMMIT;

-- Display summary
SELECT 'Admin user management tables created successfully!' as Status;
SELECT TABLE_NAME, TABLE_ROWS 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'goplay_sports_platform' 
AND TABLE_NAME IN ('user_logins', 'admin_logs', 'user_sessions', 'user_activity_log');