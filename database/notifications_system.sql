-- ====================================
-- NOTIFICATIONS SYSTEM SETUP
-- Run this SQL to ensure notification tables exist
-- ====================================

-- User Notifications Table (for regular users)
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('booking_confirmation', 'payment_success', 'order_status', 'review_request', 'promotional', 'system') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ground Owner Notifications Table
CREATE TABLE IF NOT EXISTS ground_owner_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('booking', 'payment', 'review', 'system', 'maintenance') DEFAULT 'system',
    reference_id INT,
    reference_type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_owner_unread (owner_id, is_read),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_read ON notifications(is_read);
CREATE INDEX IF NOT EXISTS idx_notifications_type ON notifications(type);

-- ====================================
-- SAMPLE NOTIFICATIONS DATA
-- ====================================

-- Sample notifications for testing (optional - uncomment to insert)
/*
INSERT INTO notifications (user_id, type, title, message, data, is_read, created_at) VALUES
(1, 'booking_confirmation', 'Booking Confirmed', 'Your booking for Basketball Court A has been confirmed for tomorrow at 3:00 PM.', '{"booking_id": 1, "facility_name": "Basketball Court A"}', FALSE, NOW()),
(1, 'payment_success', 'Payment Successful', 'Your payment of LKR 2,500.00 has been processed successfully.', '{"amount": 2500, "order_number": "12345"}', FALSE, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(1, 'order_status', 'Order Shipped', 'Your order #12345 has been shipped and will arrive in 2-3 business days.', '{"order_number": "12345", "status": "shipped"}', TRUE, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Sample ground owner notifications
INSERT INTO ground_owner_notifications (owner_id, title, message, notification_type, reference_id, is_read, priority, created_at) VALUES
(100, 'New Booking Received', 'You have received a new booking for your Football Field A', 'booking', 1, FALSE, 'high', NOW()),
(100, 'Payment Received', 'Payment of LKR 3,000 received for booking #15', 'payment', 15, FALSE, 'medium', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(100, 'New Review Posted', 'A customer left a 5-star review for your facility', 'review', 1, TRUE, 'medium', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(100, 'Maintenance Reminder', 'Scheduled maintenance for Tennis Court 1 is due next week', 'maintenance', 3, FALSE, 'medium', DATE_SUB(NOW(), INTERVAL 1 DAY));
*/
