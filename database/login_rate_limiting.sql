USE goplay_sports_platform;

-- Tracks every login attempt for rate limiting.
-- Indexed on (email, attempted_at) and (ip_address, attempted_at) so the
-- window-count queries used by LoginAttempt::isLockedBy*() are fast.
CREATE TABLE IF NOT EXISTS login_attempts (
    id           INT          PRIMARY KEY AUTO_INCREMENT,
    email        VARCHAR(100) NOT NULL,
    ip_address   VARCHAR(45)  NOT NULL,
    success      TINYINT(1)   NOT NULL DEFAULT 0,
    attempted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_la_email_time (email, attempted_at),
    INDEX idx_la_ip_time    (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
