-- GoPlay Sports Platform Sample Data
-- Run this after creating the database structure

USE goplay_sports_platform;

-- ======================
-- SAMPLE USERS DATA
-- ======================

-- Insert sample users (password_hash would be properly hashed in real app)
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status) VALUES
('admin', 'admin@goplay.com', '$2y$10$example_hashed_password', 'Admin', 'User', '+94701234567', 'admin', 'active'),
('john_doe', 'john@email.com', '$2y$10$example_hashed_password', 'John', 'Doe', '+94701234568', 'customer', 'active'),
('jane_smith', 'jane@email.com', '$2y$10$example_hashed_password', 'Jane', 'Smith', '+94701234569', 'customer', 'active'),
('coach_michael', 'michael@goplay.com', '$2y$10$example_hashed_password', 'Michael', 'Johnson', '+94701234570', 'coach', 'active'),
('coach_sarah', 'sarah@goplay.com', '$2y$10$example_hashed_password', 'Sarah', 'Williams', '+94701234571', 'coach', 'active'),
('owner_david', 'david@goplay.com', '$2y$10$example_hashed_password', 'David', 'Brown', '+94701234572', 'facility_owner', 'active');

-- Insert sample addresses
INSERT INTO user_addresses (user_id, address_type, street_address, city, postal_code, is_default) VALUES
(2, 'home', '123 Main Street', 'Colombo', '00100', TRUE),
(3, 'home', '456 Queen Street', 'Kandy', '20000', TRUE),
(4, 'home', '789 King Road', 'Colombo', '00300', TRUE),
(5, 'home', '321 Park Lane', 'Galle', '80000', TRUE);

-- ======================
-- SAMPLE COACHES DATA
-- ======================

-- Insert sample coaches
INSERT INTO coaches (user_id, sport_category_id, experience_years, hourly_rate, bio, specializations, rating, total_reviews, status) VALUES
(4, 1, 8, 75.00, 'Professional basketball coach with 8 years of experience. Specialized in shooting techniques and defensive strategies.', 'Shooting, Defense, Team Strategy', 4.9, 156, 'active'),
(5, 2, 12, 85.00, 'Certified tennis instructor with over 12 years of experience. Former professional player with expertise in technique and mental game.', 'Technique, Mental Game, Tournament Preparation', 4.8, 203, 'active');

-- ======================
-- SAMPLE FACILITIES DATA
-- ======================

-- Insert sample sports facilities
INSERT INTO sports_facilities (owner_id, name, description, sport_category_id, address, city, postal_code, hourly_rate, capacity, rating, total_reviews, status) VALUES
(6, 'Elite Basketball Courts', 'Premium indoor basketball courts with professional flooring and lighting', 1, '15 Sports Complex Road', 'Colombo', '00700', 50.00, 20, 4.7, 89, 'active'),
(6, 'Royal Tennis Club', 'Professional tennis courts with clay and hard court surfaces', 2, '25 Tennis Avenue', 'Colombo', '00300', 60.00, 8, 4.6, 124, 'active'),
(6, 'Champions Football Ground', 'Full-size football field with natural grass and floodlights', 3, '100 Stadium Road', 'Kandy', '20000', 80.00, 50, 4.8, 67, 'active');

-- ======================
-- SAMPLE PRODUCTS DATA
-- ======================

-- Insert product categories
INSERT INTO product_categories (name, description, is_active) VALUES
('Rackets', 'Tennis, Badminton, and other racket sports equipment', TRUE),
('Balls', 'Sports balls for various games', TRUE),
('Footwear', 'Sports shoes and athletic footwear', TRUE),
('Apparel', 'Sports clothing and uniforms', TRUE),
('Accessories', 'Sports accessories and gear', TRUE);

-- Insert sample products (matching your existing JSON data)
INSERT INTO products (name, sku, description, category_id, brand, price, stock_quantity, specifications, features, images, status, rating, total_reviews) VALUES
('Professional Tennis Racket', 'TNS-001', 'High-performance tennis racket used by professional players. Perfect balance of power and control.', 1, 'Wilson', 199.99, 25, '{"weight": "315g", "headSize": "97 sq in", "stringPattern": "16x19", "balance": "31.5cm"}', '["Professional", "Tournament", "Wilson"]', '["tennis_racket_1.jpg", "tennis_racket_2.jpg"]', 'active', 4.8, 142),

('Premium Basketball Shoes', 'BSK-002', 'Latest basketball shoes with advanced cushioning and support technology.', 3, 'Nike', 179.99, 48, '{"sizes": ["8", "9", "10", "11", "12"], "colors": ["Black/Red", "White/Blue"], "material": "Synthetic Leather"}', '["Basketball", "Nike", "Shoes"]', '["basketball_shoes_1.jpg", "basketball_shoes_2.jpg"]', 'active', 4.7, 89),

('FIFA Official Soccer Ball', 'SOC-003', 'Official FIFA World Cup soccer ball with superior flight characteristics.', 2, 'Adidas', 49.99, 67, '{"size": "5", "material": "Synthetic Leather", "bladder": "Butyl", "panels": "6"}', '["Soccer", "Football", "FIFA", "Adidas", "Official"]', '["soccer_ball_1.jpg", "soccer_ball_2.jpg"]', 'active', 4.9, 234),

('Badminton Racket Set', 'BAD-004', 'Professional badminton racket set with 2 rackets and shuttlecocks included.', 1, 'Yonex', 129.99, 32, '{"weight": "83g", "flexibility": "Medium", "balance": "Head Light", "includes": "2 Rackets, 6 Shuttlecocks, Case"}', '["Badminton", "Yonex", "Racket", "Set"]', '["badminton_set_1.jpg", "badminton_set_2.jpg"]', 'active', 4.6, 76),

('Swimming Goggles Pro', 'SWM-005', 'Professional swimming goggles with anti-fog technology and UV protection.', 5, 'Speedo', 39.99, 85, '{"lensType": "Mirrored", "uvProtection": "100%", "antiFog": "Yes", "adjustableStrap": "Yes"}', '["Swimming", "Goggles", "Speedo", "Professional"]', '["swimming_goggles_1.jpg", "swimming_goggles_2.jpg"]', 'active', 4.5, 156),

('Volleyball Official Size', 'VOL-006', 'Official volleyball used in international competitions with superior grip.', 2, 'Mikasa', 69.99, 43, '{"size": "Official", "material": "Synthetic Leather", "circumference": "65-67cm", "weight": "260-280g"}', '["Volleyball", "Mikasa", "Official", "Competition"]', '["volleyball_1.jpg"]', 'active', 4.8, 98),

('Cricket Bat Professional', 'CRI-007', 'Professional cricket bat made from premium English willow wood.', 1, 'Gray Nicolls', 189.99, 0, '{"wood": "English Willow", "weight": "2lb 8oz", "handle": "Oval", "grade": "Grade 1"}', '["Cricket", "Bat", "Professional", "Willow"]', '["cricket_bat_1.jpg", "cricket_bat_2.jpg"]', 'out_of_stock', 4.7, 45),

('Table Tennis Paddle Set', 'TT-008', 'Professional table tennis paddle set with premium rubber and carbon blade.', 1, 'Butterfly', 159.99, 19, '{"blade": "5-ply wood + 2 ALC", "rubber": "Tenergy 05", "handle": "Flared", "includes": "2 Paddles, 6 Balls, Case"}', '["Table Tennis", "Butterfly", "Professional", "Set"]', '["table_tennis_set_1.jpg", "table_tennis_set_2.jpg"]', 'active', 4.9, 67);

-- ======================
-- SAMPLE BOOKINGS DATA
-- ======================

-- Insert sample coach bookings
INSERT INTO coach_bookings (user_id, coach_id, booking_date, start_time, end_time, duration_hours, session_type, total_amount, status, payment_status) VALUES
(2, 1, '2024-03-15', '14:00:00', '16:00:00', 2.0, 'individual', 150.00, 'confirmed', 'paid'),
(3, 2, '2024-03-16', '10:00:00', '11:30:00', 1.5, 'individual', 127.50, 'confirmed', 'paid'),
(2, 1, '2024-03-20', '15:00:00', '16:00:00', 1.0, 'assessment', 75.00, 'pending', 'pending');

-- Insert sample facility bookings
INSERT INTO facility_bookings (user_id, facility_id, booking_date, start_time, end_time, duration_hours, total_amount, status, payment_status) VALUES
(2, 1, '2024-03-18', '18:00:00', '20:00:00', 2.0, 100.00, 'confirmed', 'paid'),
(3, 2, '2024-03-19', '16:00:00', '18:00:00', 2.0, 120.00, 'confirmed', 'paid');

-- ======================
-- SAMPLE ORDERS DATA
-- ======================

-- Insert sample orders
INSERT INTO orders (order_number, user_id, order_type, subtotal, tax_amount, total_amount, status, payment_status, payment_method) VALUES
('GP-2024-001', 2, 'product', 249.98, 20.99, 270.97, 'delivered', 'paid', 'card'),
('GP-2024-002', 3, 'product', 129.99, 10.92, 140.91, 'processing', 'paid', 'paypal'),
('GP-2024-003', 2, 'service', 150.00, 12.60, 162.60, 'completed', 'paid', 'card');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, item_name, item_description, quantity, unit_price, total_price) VALUES
(1, 1, 'Professional Tennis Racket', 'Wilson Pro Staff 97', 1, 199.99, 199.99),
(1, 5, 'Swimming Goggles Pro', 'Speedo Fastskin3', 1, 49.99, 49.99),
(2, 4, 'Badminton Racket Set', 'Yonex Arcsaber 11 Set', 1, 129.99, 129.99);

-- ======================
-- SAMPLE REVIEWS DATA
-- ======================

-- Insert sample coach reviews
INSERT INTO coach_reviews (coach_id, user_id, booking_id, rating, review_text) VALUES
(1, 2, 1, 5, 'Excellent coach! Michael really helped improve my shooting technique. Highly recommend!'),
(2, 3, 2, 5, 'Sarah is amazing! Her teaching style is perfect and she really understands the mental aspect of tennis.');

-- Insert sample facility reviews
INSERT INTO facility_reviews (facility_id, user_id, booking_id, rating, review_text) VALUES
(1, 2, 1, 5, 'Great facilities! Clean courts with excellent lighting. Perfect for evening games.'),
(2, 3, 2, 4, 'Nice tennis courts but could use some maintenance on court 2. Overall good experience.');

-- Insert sample product reviews
INSERT INTO product_reviews (product_id, user_id, order_id, rating, title, review_text, is_verified_purchase) VALUES
(1, 2, 1, 5, 'Excellent racket!', 'This Wilson racket is exactly what I was looking for. Great balance and control. Worth every penny!', TRUE),
(5, 2, 1, 4, 'Good goggles', 'Anti-fog works well but strap could be more comfortable for long sessions.', TRUE),
(4, 3, 2, 5, 'Perfect set for beginners', 'Great value for money. Comes with everything you need to start playing badminton seriously.', TRUE);

-- ======================
-- SAMPLE NOTIFICATIONS
-- ======================

-- Insert sample notifications
INSERT INTO notifications (user_id, type, title, message, is_read) VALUES
(2, 'booking_confirmation', 'Booking Confirmed', 'Your basketball coaching session with Michael Johnson has been confirmed for March 15, 2024.', TRUE),
(2, 'payment_success', 'Payment Successful', 'Your payment of $162.60 for coaching session has been processed successfully.', TRUE),
(3, 'order_status', 'Order Shipped', 'Your order GP-2024-002 has been shipped and will arrive in 2-3 business days.', FALSE),
(2, 'review_request', 'Review Your Experience', 'How was your recent coaching session with Michael Johnson? Please share your feedback.', FALSE);

COMMIT;