-- Ground Owner Complete Database Setup
-- This script creates all necessary tables and dummy data for ground owner dashboard

USE goplay_sports_platform;

-- ====================================
-- CREATE GROUND OWNER PROFILE TABLE
-- ====================================
CREATE TABLE IF NOT EXISTS ground_owner_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    business_name VARCHAR(200),
    business_registration_number VARCHAR(100),
    business_address TEXT,
    business_phone VARCHAR(20),
    business_email VARCHAR(100),
    bio TEXT,
    years_in_business INT DEFAULT 0,
    total_facilities INT DEFAULT 0,
    website VARCHAR(255),
    facebook VARCHAR(255),
    instagram VARCHAR(255),
    twitter VARCHAR(255),
    bank_name VARCHAR(100),
    bank_account_number VARCHAR(50),
    bank_account_holder VARCHAR(200),
    payment_methods JSON,
    profile_completion_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- CREATE EARNINGS TRACKING TABLE
-- ====================================
CREATE TABLE IF NOT EXISTS ground_owner_earnings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    booking_id INT,
    facility_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    earning_date DATE NOT NULL,
    earning_type ENUM('booking', 'penalty', 'refund', 'adjustment') DEFAULT 'booking',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES facility_bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    INDEX idx_owner_date (owner_id, earning_date),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- CREATE FACILITY AVAILABILITY TABLE
-- ====================================
CREATE TABLE IF NOT EXISTS facility_availability (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    opening_time TIME NOT NULL,
    closing_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    UNIQUE KEY unique_facility_day (facility_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- CREATE FACILITY MAINTENANCE TABLE
-- ====================================
CREATE TABLE IF NOT EXISTS facility_maintenance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    maintenance_type ENUM('routine', 'repair', 'upgrade', 'emergency') DEFAULT 'routine',
    start_date DATE NOT NULL,
    end_date DATE,
    cost DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    performed_by VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    INDEX idx_facility_status (facility_id, status),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================
-- CREATE NOTIFICATIONS TABLE
-- ====================================
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

-- ====================================
-- INSERT DUMMY GROUND OWNERS
-- ====================================

-- Insert ground owner users (if they don't exist)
INSERT IGNORE INTO users (id, username, email, password_hash, first_name, last_name, phone, user_type, status) VALUES
(100, 'rajesh_grounds', 'rajesh@groundowner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rajesh', 'Perera', '+94771234567', 'ground_owner', 'active'),
(101, 'sanduni_sports', 'sanduni@groundowner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sanduni', 'Rajapakse', '+94772345678', 'ground_owner', 'active'),
(102, 'kumar_facilities', 'kumar@groundowner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kumar', 'Silva', '+94773456789', 'ground_owner', 'active');

-- Insert ground owner profiles
INSERT INTO ground_owner_profiles (user_id, business_name, business_registration_number, business_address, business_phone, business_email, bio, years_in_business, total_facilities, website, facebook, instagram, bank_name, bank_account_number, bank_account_holder, payment_methods, profile_completion_percentage) VALUES
(100, 'Perera Sports Complex', 'BR/2018/12345', '123 Galle Road, Colombo 03, Sri Lanka', '+94771234567', 'info@pererasports.lk', 'Leading sports facility provider in Colombo with over 10 years of experience. We offer premium cricket grounds, football fields, and tennis courts with modern amenities and professional maintenance.', 10, 5, 'https://pererasports.lk', 'https://facebook.com/pererasports', 'https://instagram.com/pererasports', 'Commercial Bank of Ceylon', '1234567890', 'Rajesh Perera', '["cash", "card", "bank_transfer", "mobile_payment"]', 95),
(101, 'Rajapakse Sports Arena', 'BR/2019/54321', '456 Kandy Road, Kandy, Sri Lanka', '+94772345678', 'info@rajapaksearena.lk', 'Modern multi-sport facility in Kandy featuring state-of-the-art equipment and well-maintained grounds. Specializing in cricket, football, and badminton facilities with experienced staff.', 5, 3, 'https://rajapaksearena.lk', 'https://facebook.com/rajapaksearena', 'https://instagram.com/rajapaksearena', 'Bank of Ceylon', '9876543210', 'Sanduni Rajapakse', '["cash", "card", "bank_transfer"]', 90),
(102, 'Silva Grounds & Courts', 'BR/2020/67890', '789 Negombo Road, Negombo, Sri Lanka', '+94773456789', 'contact@silvagrounds.lk', 'Family-owned sports facility with a passion for promoting healthy lifestyles. Offering quality football fields, basketball courts, and tennis facilities at affordable rates.', 3, 4, NULL, 'https://facebook.com/silvagrounds', NULL, 'Peoples Bank', '5555666677', 'Kumar Silva', '["cash", "bank_transfer"]', 75);

-- ====================================
-- INSERT DUMMY SPORTS FACILITIES
-- ====================================

INSERT IGNORE INTO sports_facilities (id, owner_id, name, description, sport_category_id, address, city, state, postal_code, hourly_rate, capacity, amenities, images, rating, total_reviews, status) VALUES
-- Rajesh Perera's Facilities
(1, 100, 'Perera Football Field A', 'Premium full-size football field with natural grass, floodlights, and changing rooms. Perfect for matches and training sessions.', 3, '123 Galle Road', 'Colombo', 'Western Province', '00300', 1500.00, 50, '["Parking", "Changing Rooms", "Floodlights", "Seating Area", "Water Fountains", "First Aid"]', '["football-field-1.jpg", "football-field-2.jpg"]', 4.8, 45, 'active'),
(2, 100, 'Perera Cricket Ground', 'International standard cricket ground with well-maintained pitch and practice nets. Equipped with scoreboard and pavilion.', 4, '123 Galle Road', 'Colombo', 'Western Province', '00300', 2000.00, 100, '["Pavilion", "Practice Nets", "Scoreboard", "Changing Rooms", "Parking", "Refreshments"]', '["cricket-ground-1.jpg"]', 4.9, 38, 'active'),
(3, 100, 'Perera Tennis Court 1', 'Professional hard court tennis facility with high-quality playing surface and seating for spectators.', 2, '123 Galle Road', 'Colombo', 'Western Province', '00300', 800.00, 10, '["Professional Surface", "Net Equipment", "Seating", "Lighting", "Water Station"]', '["tennis-court-1.jpg"]', 4.7, 28, 'active'),
(4, 100, 'Perera Badminton Hall', 'Indoor badminton hall with 4 courts, air conditioning, and professional equipment. Suitable for tournaments.', 6, '123 Galle Road', 'Colombo', 'Western Province', '00300', 600.00, 40, '["Air Conditioning", "4 Courts", "Professional Equipment", "Changing Rooms", "Parking", "Seating"]', '["badminton-hall.jpg"]', 4.6, 22, 'active'),
(5, 100, 'Perera Basketball Court', 'Outdoor basketball court with quality flooring and adjustable hoops. Suitable for 3v3 and full court games.', 1, '123 Galle Road', 'Colombo', 'Western Province', '00300', 700.00, 20, '["Quality Flooring", "Adjustable Hoops", "Lighting", "Seating", "Water Station"]', '["basketball-court.jpg"]', 4.5, 18, 'active'),

-- Sanduni Rajapakse's Facilities
(6, 101, 'Rajapakse Football Stadium', 'Modern football stadium with synthetic turf, complete facilities, and 500-person capacity.', 3, '456 Kandy Road', 'Kandy', 'Central Province', '20000', 1800.00, 500, '["Synthetic Turf", "Floodlights", "VIP Boxes", "Changing Rooms", "Cafeteria", "Parking"]', '["stadium-1.jpg"]', 4.9, 52, 'active'),
(7, 101, 'Rajapakse Cricket Academy Ground', 'Academy-standard cricket ground with coaching facilities and modern amenities.', 4, '456 Kandy Road', 'Kandy', 'Central Province', '20000', 1600.00, 80, '["Practice Nets", "Coaching Area", "Pavilion", "Equipment Storage", "Parking"]', '["cricket-academy.jpg"]', 4.7, 31, 'active'),
(8, 101, 'Rajapakse Indoor Badminton Center', '6-court air-conditioned badminton center with professional lighting and equipment.', 6, '456 Kandy Road', 'Kandy', 'Central Province', '20000', 750.00, 60, '["6 Courts", "Air Conditioning", "Professional Lighting", "Equipment Rental", "Cafeteria"]', '["badminton-center.jpg"]', 4.8, 41, 'active'),

-- Kumar Silva's Facilities
(9, 102, 'Silva Multi-Sport Ground', 'Versatile ground suitable for football, rugby, and athletics with basic amenities.', 3, '789 Negombo Road', 'Negombo', 'Western Province', '11500', 1200.00, 60, '["Changing Rooms", "Basic Seating", "Parking", "Water Fountains"]', '["multi-sport.jpg"]', 4.4, 19, 'active'),
(10, 102, 'Silva Tennis Complex', 'Two outdoor tennis courts with quality surfaces and lighting for evening play.', 2, '789 Negombo Road', 'Negombo', 'Western Province', '11500', 650.00, 16, '["2 Courts", "Evening Lighting", "Equipment Available", "Parking"]', '["tennis-complex.jpg"]', 4.5, 15, 'active'),
(11, 102, 'Silva Basketball Arena', 'Covered basketball court with modern facilities and seating area.', 1, '789 Negombo Road', 'Negombo', 'Western Province', '11500', 800.00, 30, '["Covered Court", "Seating Area", "Scoreboard", "Changing Rooms"]', '["basketball-arena.jpg"]', 4.3, 12, 'active'),
(12, 102, 'Silva Volleyball Court', 'Beach volleyball court with sand surface and evening lighting.', 7, '789 Negombo Road', 'Negombo', 'Western Province', '11500', 500.00, 20, '["Beach Sand", "Evening Lights", "Seating", "Parking"]', '["volleyball-court.jpg"]', 4.6, 14, 'active');

-- ====================================
-- INSERT DUMMY BOOKINGS
-- ====================================

INSERT INTO facility_bookings (user_id, facility_id, booking_date, start_time, end_time, duration_hours, total_amount, special_requests, status, payment_status) VALUES
-- Today's Bookings
(21, 1, CURDATE(), '08:00:00', '10:00:00', 2.0, 0.00, 'Morning football practice', 'confirmed', 'paid'),
(22, 2, CURDATE(), '14:00:00', '17:00:00', 3.0, 0.00, 'Cricket match', 'confirmed', 'paid'),
(23, 6, CURDATE(), '18:00:00', '20:00:00', 2.0, 0.00, 'Evening football game', 'confirmed', 'paid'),

-- Tomorrow's Bookings
(21, 3, CURDATE() + INTERVAL 1 DAY, '09:00:00', '10:00:00', 1.0, 0.00, 'Tennis lesson', 'confirmed', 'paid'),
(22, 1, CURDATE() + INTERVAL 1 DAY, '15:00:00', '17:00:00', 2.0, 0.00, 'Team practice', 'pending', 'paid'),
(23, 7, CURDATE() + INTERVAL 1 DAY, '10:00:00', '13:00:00', 3.0, 0.00, 'Cricket coaching', 'confirmed', 'paid'),

-- This Week's Bookings
(21, 4, CURDATE() + INTERVAL 2 DAY, '16:00:00', '18:00:00', 2.0, 0.00, 'Badminton tournament', 'confirmed', 'paid'),
(22, 9, CURDATE() + INTERVAL 3 DAY, '08:00:00', '10:00:00', 2.0, 0.00, 'Rugby practice', 'confirmed', 'paid'),
(23, 10, CURDATE() + INTERVAL 4 DAY, '17:00:00', '19:00:00', 2.0, 0.00, 'Tennis doubles match', 'confirmed', 'paid'),
(21, 5, CURDATE() + INTERVAL 5 DAY, '19:00:00', '21:00:00', 2.0, 0.00, 'Basketball league', 'confirmed', 'paid'),
(22, 8, CURDATE() + INTERVAL 6 DAY, '14:00:00', '16:00:00', 2.0, 0.00, 'Badminton club', 'pending', 'paid'),

-- Past Bookings (Last Month)
(21, 1, CURDATE() - INTERVAL 5 DAY, '10:00:00', '12:00:00', 2.0, 0.00, 'Weekend match', 'completed', 'paid'),
(22, 2, CURDATE() - INTERVAL 7 DAY, '15:00:00', '18:00:00', 3.0, 0.00, 'Cricket practice', 'completed', 'paid'),
(23, 6, CURDATE() - INTERVAL 10 DAY, '18:00:00', '20:00:00', 2.0, 0.00, 'Evening game', 'completed', 'paid'),
(21, 3, CURDATE() - INTERVAL 12 DAY, '09:00:00', '11:00:00', 2.0, 0.00, 'Tennis training', 'completed', 'paid'),
(22, 7, CURDATE() - INTERVAL 15 DAY, '14:00:00', '17:00:00', 3.0, 0.00, 'Academy session', 'completed', 'paid'),
(23, 9, CURDATE() - INTERVAL 18 DAY, '16:00:00', '18:00:00', 2.0, 0.00, 'Team building', 'completed', 'paid'),
(21, 10, CURDATE() - INTERVAL 20 DAY, '10:00:00', '12:00:00', 2.0, 0.00, 'Tennis championship', 'completed', 'paid'),
(22, 4, CURDATE() - INTERVAL 22 DAY, '17:00:00', '19:00:00', 2.0, 0.00, 'Badminton practice', 'completed', 'paid'),
(23, 1, CURDATE() - INTERVAL 25 DAY, '08:00:00', '10:00:00', 2.0, 0.00, 'Morning workout', 'completed', 'paid'),
(21, 6, CURDATE() - INTERVAL 28 DAY, '19:00:00', '21:00:00', 2.0, 0.00, 'Football league', 'completed', 'paid');

-- ====================================
-- INSERT DUMMY EARNINGS
-- ====================================

INSERT INTO ground_owner_earnings (owner_id, booking_id, facility_id, amount, earning_date, earning_type, payment_status, payment_method, notes) VALUES
-- Rajesh Perera (owner_id 100) - Last 30 days earnings
(100, NULL, 1, 3000.00, CURDATE(), 'booking', 'pending', 'card', 'Today booking payment'),
(100, NULL, 2, 6000.00, CURDATE(), 'booking', 'paid', 'bank_transfer', 'Cricket match payment'),
(100, NULL, 3, 800.00, CURDATE() - INTERVAL 1 DAY, 'booking', 'paid', 'cash', 'Tennis court rental'),
(100, NULL, 4, 1200.00, CURDATE() - INTERVAL 2 DAY, 'booking', 'paid', 'card', 'Badminton tournament'),
(100, NULL, 1, 3000.00, CURDATE() - INTERVAL 5 DAY, 'booking', 'paid', 'bank_transfer', 'Football match'),
(100, NULL, 2, 6000.00, CURDATE() - INTERVAL 7 DAY, 'booking', 'paid', 'card', 'Cricket practice'),
(100, NULL, 3, 1600.00, CURDATE() - INTERVAL 12 DAY, 'booking', 'paid', 'cash', 'Tennis training'),
(100, NULL, 5, 1400.00, CURDATE() - INTERVAL 15 DAY, 'booking', 'paid', 'card', 'Basketball game'),
(100, NULL, 1, 3000.00, CURDATE() - INTERVAL 18 DAY, 'booking', 'paid', 'bank_transfer', 'Football practice'),
(100, NULL, 4, 1200.00, CURDATE() - INTERVAL 22 DAY, 'booking', 'paid', 'card', 'Badminton session'),

-- Sanduni Rajapakse (owner_id 101) - Last 30 days earnings
(101, NULL, 6, 3600.00, CURDATE(), 'booking', 'paid', 'bank_transfer', 'Stadium booking'),
(101, NULL, 7, 4800.00, CURDATE() - INTERVAL 1 DAY, 'booking', 'paid', 'card', 'Cricket academy'),
(101, NULL, 8, 1500.00, CURDATE() - INTERVAL 6 DAY, 'booking', 'paid', 'cash', 'Badminton center'),
(101, NULL, 6, 3600.00, CURDATE() - INTERVAL 10 DAY, 'booking', 'paid', 'bank_transfer', 'Football match'),
(101, NULL, 7, 4800.00, CURDATE() - INTERVAL 15 DAY, 'booking', 'paid', 'card', 'Cricket practice'),

-- Kumar Silva (owner_id 102) - Last 30 days earnings
(102, NULL, 9, 2400.00, CURDATE() + INTERVAL 3 DAY, 'booking', 'pending', 'cash', 'Multi-sport booking'),
(102, NULL, 10, 1300.00, CURDATE() + INTERVAL 4 DAY, 'booking', 'pending', 'card', 'Tennis court'),
(102, NULL, 9, 2400.00, CURDATE() - INTERVAL 18 DAY, 'booking', 'paid', 'cash', 'Football practice'),
(102, NULL, 10, 1300.00, CURDATE() - INTERVAL 20 DAY, 'booking', 'paid', 'card', 'Tennis match'),
(102, NULL, 11, 1600.00, CURDATE() - INTERVAL 25 DAY, 'booking', 'paid', 'bank_transfer', 'Basketball league');

-- ====================================
-- INSERT FACILITY AVAILABILITY
-- ====================================

-- Perera Sports Complex availability (all facilities)
INSERT INTO facility_availability (facility_id, day_of_week, opening_time, closing_time, is_available) VALUES
-- Perera Football Field A
(1, 'Monday', '06:00:00', '22:00:00', TRUE),
(1, 'Tuesday', '06:00:00', '22:00:00', TRUE),
(1, 'Wednesday', '06:00:00', '22:00:00', TRUE),
(1, 'Thursday', '06:00:00', '22:00:00', TRUE),
(1, 'Friday', '06:00:00', '22:00:00', TRUE),
(1, 'Saturday', '07:00:00', '23:00:00', TRUE),
(1, 'Sunday', '07:00:00', '23:00:00', TRUE),

-- Perera Cricket Ground
(2, 'Monday', '06:00:00', '20:00:00', TRUE),
(2, 'Tuesday', '06:00:00', '20:00:00', TRUE),
(2, 'Wednesday', '06:00:00', '20:00:00', TRUE),
(2, 'Thursday', '06:00:00', '20:00:00', TRUE),
(2, 'Friday', '06:00:00', '20:00:00', TRUE),
(2, 'Saturday', '07:00:00', '21:00:00', TRUE),
(2, 'Sunday', '07:00:00', '21:00:00', TRUE);

-- ====================================
-- INSERT FACILITY MAINTENANCE RECORDS
-- ====================================

INSERT INTO facility_maintenance (facility_id, title, description, maintenance_type, start_date, end_date, cost, status, performed_by, notes) VALUES
(1, 'Grass Trimming and Field Preparation', 'Regular grass cutting and field marking for matches', 'routine', CURDATE() - INTERVAL 3 DAY, CURDATE() - INTERVAL 3 DAY, 5000.00, 'completed', 'Ground Staff Team', 'Field ready for matches'),
(2, 'Cricket Pitch Rolling', 'Professional pitch rolling and marking', 'routine', CURDATE() - INTERVAL 7 DAY, CURDATE() - INTERVAL 7 DAY, 8000.00, 'completed', 'Professional Groundsman', 'Pitch in excellent condition'),
(3, 'Tennis Court Resurfacing', 'Complete court surface maintenance and line marking', 'repair', CURDATE() - INTERVAL 15 DAY, CURDATE() - INTERVAL 10 DAY, 25000.00, 'completed', 'Court Services Ltd', 'Surface quality improved'),
(4, 'Badminton Net Replacement', 'Replace worn out nets on all courts', 'repair', CURDATE() + INTERVAL 5 DAY, CURDATE() + INTERVAL 5 DAY, 3500.00, 'scheduled', 'Equipment Supplier', 'New nets ordered'),
(6, 'Stadium Floodlight Upgrade', 'Install new LED floodlights for better visibility', 'upgrade', CURDATE() + INTERVAL 10 DAY, CURDATE() + INTERVAL 15 DAY, 150000.00, 'scheduled', 'ElectroSports Solutions', 'Major upgrade planned');

-- ====================================
-- INSERT FACILITY REVIEWS
-- ====================================

INSERT INTO facility_reviews (facility_id, user_id, rating, review_text, created_at) VALUES
-- Reviews for Perera Football Field A
(1, 21, 5, 'Excellent football ground with perfect grass condition. Floodlights are great for evening matches!', CURDATE() - INTERVAL 2 DAY),
(1, 22, 5, 'Top-notch facility! Well maintained and staff is very helpful. Highly recommended.', CURDATE() - INTERVAL 5 DAY),
(1, 23, 4, 'Great field but parking can be tight during peak hours. Otherwise fantastic!', CURDATE() - INTERVAL 10 DAY),

-- Reviews for Perera Cricket Ground
(2, 21, 5, 'Professional cricket ground. Pitch quality is outstanding. Perfect for serious matches.', CURDATE() - INTERVAL 4 DAY),
(2, 22, 5, 'Best cricket facility in Colombo! Practice nets are well maintained.', CURDATE() - INTERVAL 8 DAY),

-- Reviews for Perera Tennis Court
(3, 23, 5, 'Smooth playing surface, excellent bounce. Court lighting is perfect for evening games.', CURDATE() - INTERVAL 6 DAY),
(3, 21, 4, 'Good tennis court, slightly expensive but worth it for the quality.', CURDATE() - INTERVAL 12 DAY),

-- Reviews for Rajapakse Football Stadium
(6, 22, 5, 'Amazing stadium! Synthetic turf is top quality. Facilities are modern and clean.', CURDATE() - INTERVAL 3 DAY),
(6, 23, 5, 'Fantastic venue for tournaments. VIP boxes are great for spectators.', CURDATE() - INTERVAL 9 DAY),

-- Reviews for Silva Multi-Sport Ground
(9, 21, 4, 'Good value for money. Basic but clean facilities. Perfect for casual games.', CURDATE() - INTERVAL 7 DAY);

-- ====================================
-- INSERT NOTIFICATIONS
-- ====================================

INSERT INTO ground_owner_notifications (owner_id, title, message, notification_type, reference_id, is_read, priority, created_at) VALUES
(100, 'New Booking Received', 'You have received a new booking for Perera Football Field A on ' || DATE_FORMAT(CURDATE() + INTERVAL 1 DAY, '%d %M %Y'), 'booking', 1, FALSE, 'high', CURDATE()),
(100, 'Payment Received', 'Payment of LKR 3,000 received for booking #15', 'payment', 15, FALSE, 'medium', CURDATE() - INTERVAL 1 HOUR),
(100, 'New Review Posted', 'Kavinda Ranasighe left a 5-star review for your facility', 'review', 1, TRUE, 'medium', CURDATE() - INTERVAL 3 HOUR),
(100, 'Maintenance Reminder', 'Scheduled maintenance for Tennis Court 1 is due next week', 'maintenance', 3, FALSE, 'medium', CURDATE() - INTERVAL 1 DAY),
(100, 'Booking Cancelled', 'A booking for Cricket Ground has been cancelled by the user', 'booking', 5, TRUE, 'low', CURDATE() - INTERVAL 2 DAY),

(101, 'New Booking Received', 'New stadium booking for this weekend', 'booking', 6, FALSE, 'high', CURDATE()),
(101, 'Monthly Report Ready', 'Your monthly performance report is now available', 'system', NULL, FALSE, 'medium', CURDATE() - INTERVAL 2 HOUR),
(101, 'High Rating Achieved', 'Your average rating has increased to 4.9 stars!', 'system', NULL, TRUE, 'low', CURDATE() - INTERVAL 5 DAY),

(102, 'Payment Pending', 'Payment pending for booking #20', 'payment', 20, FALSE, 'high', CURDATE()),
(102, 'New Facility Approved', 'Your new Volleyball Court has been approved and is now live', 'system', 12, TRUE, 'medium', CURDATE() - INTERVAL 7 DAY);

-- ====================================
-- UPDATE FACILITY RATINGS
-- ====================================

UPDATE sports_facilities sf
SET rating = (
    SELECT COALESCE(AVG(rating), 0)
    FROM facility_reviews fr
    WHERE fr.facility_id = sf.id
),
total_reviews = (
    SELECT COUNT(*)
    FROM facility_reviews fr
    WHERE fr.facility_id = sf.id
);

-- ====================================
-- CREATE USEFUL VIEWS FOR DASHBOARD
-- ====================================

-- View for owner earnings summary
DROP VIEW IF EXISTS v_owner_earnings_summary;
CREATE VIEW v_owner_earnings_summary AS
SELECT
    owner_id,
    DATE_FORMAT(earning_date, '%Y-%m') as month_year,
    SUM(amount) as total_earnings,
    COUNT(*) as total_transactions,
    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as paid_amount,
    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending_amount
FROM ground_owner_earnings
GROUP BY owner_id, DATE_FORMAT(earning_date, '%Y-%m');

-- View for facility performance
DROP VIEW IF EXISTS v_facility_performance;
CREATE VIEW v_facility_performance AS
SELECT
    sf.id as facility_id,
    sf.owner_id,
    sf.name as facility_name,
    COUNT(DISTINCT fb.id) as total_bookings,
    COUNT(DISTINCT CASE WHEN fb.booking_date >= CURDATE() - INTERVAL 30 DAY THEN fb.id END) as bookings_last_30_days,
    sf.rating,
    sf.total_reviews,
    COALESCE(SUM(ge.amount), 0) as total_earnings
FROM sports_facilities sf
LEFT JOIN facility_bookings fb ON sf.id = fb.facility_id
LEFT JOIN ground_owner_earnings ge ON sf.id = ge.facility_id
GROUP BY sf.id, sf.owner_id, sf.name, sf.rating, sf.total_reviews;

COMMIT;

-- Success message
SELECT 'Ground Owner Complete Database Setup Completed Successfully!' as Status;
