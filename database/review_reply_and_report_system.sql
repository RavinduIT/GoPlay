-- ====================================
-- REVIEW REPLY AND REPORT SYSTEM
-- ====================================

USE goplay_sports_platform;

-- Table for Review Replies (Ground owners can reply to reviews)
CREATE TABLE IF NOT EXISTS facility_review_replies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    owner_id INT NOT NULL,
    reply_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES facility_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_review_id (review_id)
);

-- Table for Review Reports (Users can report inappropriate reviews)
CREATE TABLE IF NOT EXISTS facility_review_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason ENUM('spam', 'offensive', 'inappropriate', 'fake', 'other') NOT NULL,
    description TEXT,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (review_id) REFERENCES facility_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_review_id (review_id),
    INDEX idx_status (status),
    -- Prevent duplicate reports from same user for same review
    UNIQUE KEY unique_user_review_report (review_id, reporter_id)
);

-- Add helpful indexes
CREATE INDEX idx_owner_id ON facility_review_replies(owner_id);
CREATE INDEX idx_reporter_id ON facility_review_reports(reporter_id);
CREATE INDEX idx_created_at ON facility_review_replies(created_at);
CREATE INDEX idx_report_created_at ON facility_review_reports(created_at);

-- Display success message
SELECT 'Review Reply and Report System Tables Created Successfully!' as Status;

-- Show table structures
DESCRIBE facility_review_replies;
DESCRIBE facility_review_reports;
