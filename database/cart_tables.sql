-- Cart Tables for GoPlay Sports Platform
-- Run this to add cart functionality to your database

USE goplay_sports_platform;

-- Shopping Cart Table (stores cart sessions)
CREATE TABLE shopping_carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL, -- NULL for guest carts, user_id for logged-in users
    session_id VARCHAR(255) NOT NULL, -- for guest cart persistence
    status ENUM('active', 'converted', 'abandoned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP DEFAULT (DATE_ADD(NOW(), INTERVAL 30 DAY)),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_status (status),
    INDEX idx_expires_at (expires_at)
);

-- Cart Items Table (stores individual products in cart)
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL, -- Store price at time of adding
    total_price DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cart_id) REFERENCES shopping_carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_product (cart_id, product_id), -- Prevent duplicate products in same cart
    INDEX idx_cart_id (cart_id),
    INDEX idx_product_id (product_id)
);

-- Cart Summary View (for easy totals calculation)
CREATE VIEW cart_summary AS
SELECT 
    c.id as cart_id,
    c.user_id,
    c.session_id,
    c.status,
    COUNT(ci.id) as item_count,
    SUM(ci.quantity) as total_quantity,
    SUM(ci.total_price) as subtotal,
    c.created_at,
    c.updated_at,
    c.expires_at
FROM shopping_carts c
LEFT JOIN cart_items ci ON c.id = ci.cart_id
GROUP BY c.id, c.user_id, c.session_id, c.status, c.created_at, c.updated_at, c.expires_at;

-- Sample data for testing
INSERT INTO shopping_carts (user_id, session_id, status) VALUES
(21, 'guest_session_001', 'active'), -- user1's cart
(NULL, 'guest_session_002', 'active'); -- guest cart

INSERT INTO cart_items (cart_id, product_id, quantity, unit_price) VALUES
(1, 1, 2, 3500.00), -- 2x Professional FIFA Football
(1, 3, 1, 15000.00), -- 1x Carbon Fiber Tennis Racket
(2, 2, 3, 999.00); -- 3x Football Training Cones Set (guest cart)

COMMIT;