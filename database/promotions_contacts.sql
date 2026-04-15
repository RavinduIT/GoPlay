-- Promotions/Banners Table
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    link_url VARCHAR(500) DEFAULT NULL,
    link_text VARCHAR(100) DEFAULT 'Learn More',
    position ENUM('hero', 'sidebar', 'footer', 'popup') DEFAULT 'hero',
    bg_color VARCHAR(20) DEFAULT '#3b82f6',
    text_color VARCHAR(20) DEFAULT '#ffffff',
    priority INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    starts_at DATETIME DEFAULT NULL,
    ends_at DATETIME DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample promotions
INSERT INTO promotions (title, subtitle, image_url, link_url, position, bg_color, priority, is_active) VALUES
('Welcome to GoPlay', 'Book grounds, find coaches, and shop for sports gear all in one place!', NULL, '/provider/join', 'hero', '#3b82f6', 10, 1),
('Become a Provider', 'Register as a ground owner, coach, or shop owner and start earning today.', NULL, '/provider/join', 'hero', '#10b981', 5, 1),
('New Sports Gear Available', 'Check out the latest equipment from top brands.', NULL, '/shop', 'sidebar', '#8b5cf6', 3, 1);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    admin_reply TEXT DEFAULT NULL,
    replied_by INT DEFAULT NULL,
    replied_at DATETIME DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample contact messages
INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES
('John Doe', 'john@example.com', '0771234567', 'Booking Question', 'Hi, I wanted to know if I can book a cricket ground for a whole day? What are the rates?', 'unread'),
('Sarah Silva', 'sarah@example.com', '0779876543', 'Coach Registration', 'I am a certified swimming coach and would like to register on GoPlay. How do I proceed?', 'unread'),
('Mike Fernando', 'mike@example.com', NULL, 'Payment Issue', 'I made a payment but it is showing as pending for 3 days. Can you please look into this?', 'read');
