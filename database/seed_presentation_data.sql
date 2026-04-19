-- =============================================
-- GoPlay Sports Platform - Presentation Seed Data
-- Fills EVERY column of EVERY table with realistic data
-- Compatible with existing seeded users (IDs 1-28)
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- =============================================
-- 1. UPDATE EXISTING USERS (fill empty columns)
-- =============================================
UPDATE users SET phone = '0771234501', date_of_birth = '1990-03-15', last_login_at = NOW() - INTERVAL 1 HOUR WHERE id = 1;
UPDATE users SET phone = '0771234502', date_of_birth = '1988-07-22', last_login_at = NOW() - INTERVAL 2 HOUR WHERE id = 2;
UPDATE users SET phone = '0771234503', date_of_birth = '1985-11-08', last_login_at = NOW() - INTERVAL 3 HOUR WHERE id = 3;
UPDATE users SET phone = '0771234504', date_of_birth = '1992-01-30', last_login_at = NOW() - INTERVAL 5 HOUR WHERE id = 4;
UPDATE users SET phone = '0771234505', date_of_birth = '1987-06-12', last_login_at = NOW() - INTERVAL 8 HOUR WHERE id = 5;

-- Ground owners
UPDATE users SET phone = '0772345601', date_of_birth = '1980-05-20', last_login_at = NOW() - INTERVAL 30 MINUTE WHERE id = 6;
UPDATE users SET phone = '0772345602', date_of_birth = '1982-09-14', last_login_at = NOW() - INTERVAL 45 MINUTE WHERE id = 7;
UPDATE users SET phone = '0772345603', date_of_birth = '1978-12-03', last_login_at = NOW() - INTERVAL 1 HOUR WHERE id = 8;
UPDATE users SET phone = '0772345604', date_of_birth = '1983-04-18', last_login_at = NOW() - INTERVAL 2 HOUR WHERE id = 9;
UPDATE users SET phone = '0772345605', date_of_birth = '1979-08-25', last_login_at = NOW() - INTERVAL 3 HOUR WHERE id = 10;

-- Regular users
UPDATE users SET phone = '0773456701', date_of_birth = '1998-02-14', last_login_at = NOW() - INTERVAL 10 MINUTE WHERE id = 21;
UPDATE users SET phone = '0773456702', date_of_birth = '1999-06-28', last_login_at = NOW() - INTERVAL 20 MINUTE WHERE id = 22;
UPDATE users SET phone = '0773456703', date_of_birth = '1997-10-05', last_login_at = NOW() - INTERVAL 40 MINUTE WHERE id = 23;
UPDATE users SET phone = '0773456704', date_of_birth = '2000-03-17', last_login_at = NOW() - INTERVAL 1 HOUR WHERE id = 24;
UPDATE users SET phone = '0773456705', date_of_birth = '1996-12-09', last_login_at = NOW() - INTERVAL 2 HOUR WHERE id = 25;

-- =============================================
-- 2. USER ADDRESSES
-- =============================================
TRUNCATE TABLE user_addresses;
INSERT INTO user_addresses (user_id, address_type, street_address, city, state, postal_code, country, is_default) VALUES
(21, 'home', '42 Galle Road, Dehiwala', 'Colombo', 'Western', '10350', 'Sri Lanka', 1),
(22, 'home', '15 Kandy Road, Peradeniya', 'Kandy', 'Central', '20400', 'Sri Lanka', 1),
(23, 'home', '88 High Level Road, Nugegoda', 'Colombo', 'Western', '10250', 'Sri Lanka', 1),
(23, 'work', '7 Union Place, Colombo 02', 'Colombo', 'Western', '00200', 'Sri Lanka', 0),
(24, 'home', '33 Temple Road, Maharagama', 'Colombo', 'Western', '10280', 'Sri Lanka', 1),
(25, 'home', '56 Beach Road, Mount Lavinia', 'Colombo', 'Western', '10370', 'Sri Lanka', 1),
(25, 'work', '120 Duplication Road, Colombo 04', 'Colombo', 'Western', '00400', 'Sri Lanka', 0);

-- =============================================
-- 3. USER LOGIN HISTORY
-- =============================================
TRUNCATE TABLE user_logins;
INSERT INTO user_logins (user_id, ip_address, user_agent, login_at) VALUES
(21, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0', NOW() - INTERVAL 10 MINUTE),
(21, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0', NOW() - INTERVAL 1 DAY),
(21, '10.0.0.50', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Safari/605.1', NOW() - INTERVAL 3 DAY),
(22, '192.168.2.55', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) Safari/17.4', NOW() - INTERVAL 20 MINUTE),
(22, '192.168.2.55', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) Safari/17.4', NOW() - INTERVAL 2 DAY),
(23, '172.16.0.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/125.0', NOW() - INTERVAL 40 MINUTE),
(23, '172.16.0.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/125.0', NOW() - INTERVAL 4 DAY),
(24, '192.168.1.200', 'Mozilla/5.0 (Linux; Android 14) Chrome/124.0 Mobile', NOW() - INTERVAL 1 HOUR),
(25, '10.10.10.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/124.0', NOW() - INTERVAL 2 HOUR),
(1, '192.168.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0', NOW() - INTERVAL 1 HOUR),
(6, '192.168.0.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0', NOW() - INTERVAL 30 MINUTE),
(7, '192.168.0.11', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) Safari/17.4', NOW() - INTERVAL 45 MINUTE);

-- =============================================
-- 4. UPDATE SPORTS FACILITIES (fill every column)
-- =============================================
UPDATE sports_facilities SET
  description = 'Premier cricket facility in the heart of Colombo 07. Features a professionally maintained pitch, practice nets, and modern changing rooms. Perfect for club matches and weekend practice.',
  state = 'Western',
  postal_code = '00700',
  latitude = 6.90880000, longitude = 79.86130000,
  capacity = 30,
  amenities = '["Floodlights","Changing Rooms","Practice Nets","Scoreboard","Parking","Canteen","First Aid","WiFi"]',
  rules = 'Players must wear proper cricket whites. No metal spikes allowed. Booking cancellation must be 24 hours in advance.',
  rating = 4.80, total_reviews = 12
WHERE id = 1;

UPDATE sports_facilities SET
  description = 'State-of-the-art football arena with FIFA-quality artificial turf. Night games are spectacular under our premium floodlight system. Full-sized pitch with professional goal posts.',
  state = 'Central',
  postal_code = '20000',
  latitude = 7.29060000, longitude = 80.63450000,
  capacity = 22,
  amenities = '["Artificial Turf","Floodlights","Changing Rooms","Referee Room","Parking","Water Coolers","First Aid"]',
  rules = 'Football boots required (no metal studs). Maximum 11 players per team. Respect facility staff at all times.',
  rating = 4.60, total_reviews = 8
WHERE id = 2;

UPDATE sports_facilities SET
  description = 'Professional tennis courts with Plexicushion surfaces. Offers both singles and doubles courts with net equipment provided. Coaching sessions available on request.',
  state = 'Western',
  postal_code = '00300',
  latitude = 6.91750000, longitude = 79.84900000,
  capacity = 4,
  amenities = '["Hard Courts","Floodlights","Ball Machine","Pro Shop","Parking","Shower Rooms","Coaching"]',
  rules = 'Proper tennis attire required. Court shoes only. No food or drinks on court.',
  rating = 4.50, total_reviews = 6
WHERE id = 3;

UPDATE sports_facilities SET
  description = 'Olympic-standard swimming complex with 50m and 25m pools. Temperature-controlled water and trained lifeguards on duty. Lane booking available for lap swimming.',
  state = 'Western',
  postal_code = '11500',
  latitude = 7.20860000, longitude = 79.83670000,
  capacity = 40,
  amenities = '["50m Pool","25m Pool","Kids Pool","Lifeguards","Changing Rooms","Lockers","Cafe","Parking"]',
  rules = 'Swimming cap mandatory. Shower before entering pool. No diving in shallow areas. Children under 12 must be accompanied by an adult.',
  rating = 4.70, total_reviews = 10
WHERE id = 4;

UPDATE sports_facilities SET
  description = 'Premium badminton centre with 8 international-standard courts. Wooden sprung flooring and regulation lighting. Equipment rental available.',
  state = 'Central',
  postal_code = '20000',
  latitude = 7.29440000, longitude = 80.63600000,
  capacity = 16,
  amenities = '["8 Courts","Wooden Flooring","Equipment Rental","Changing Rooms","Parking","Cafeteria","Air Conditioning"]',
  rules = 'Non-marking shoes required. Rackets available for rent. Book minimum 1 hour.',
  rating = 4.40, total_reviews = 5
WHERE id = 5;

-- =============================================
-- 5. GROUND OWNER PROFILES (fill every column)
-- =============================================
UPDATE ground_owner_profiles SET
  business_name = 'Royal Sports Holdings (Pvt) Ltd',
  business_phone = '0112345678',
  business_email = 'info@royalsports.lk',
  business_address = '42 Maitland Crescent, Colombo 07, Sri Lanka',
  profile_completion_percentage = 100
WHERE user_id = 6;

UPDATE ground_owner_profiles SET
  business_name = 'Green Field Sports Management',
  business_phone = '0812987654',
  business_email = 'bookings@greenfieldkandy.lk',
  business_address = '88 Peradeniya Road, Kandy, Sri Lanka',
  profile_completion_percentage = 95
WHERE user_id = 7;

-- Insert missing ground owner profiles
INSERT INTO ground_owner_profiles (user_id, business_name, business_phone, business_email, business_address, profile_completion_percentage)
VALUES
(8, 'Ace Sports Academy', '0112456789', 'hello@acesports.lk', '15 Temple Road, Colombo 03, Sri Lanka', 90),
(9, 'Splash Aquatics Centre', '0312567890', 'swim@splashaquatics.lk', '7 Beach Road, Negombo, Sri Lanka', 85),
(10, 'Elite Sports Complex', '0812345890', 'contact@elitesports.lk', '22 Ampitiya Road, Kandy, Sri Lanka', 88)
ON DUPLICATE KEY UPDATE business_name = VALUES(business_name);

-- =============================================
-- 6. FACILITY BOOKINGS (realistic past + upcoming)
-- =============================================
TRUNCATE TABLE facility_bookings;
INSERT INTO facility_bookings (user_id, facility_id, booking_date, start_time, end_time, duration_hours, total_amount, special_requests, status, payment_status, created_at) VALUES
-- Completed bookings (past)
(21, 1, CURDATE() - INTERVAL 15 DAY, '08:00:00', '10:00:00', 2.0, 16000.00, 'Need practice nets set up', 'completed', 'paid', NOW() - INTERVAL 15 DAY),
(22, 2, CURDATE() - INTERVAL 14 DAY, '17:00:00', '19:00:00', 2.0, 12000.00, 'Evening game with floodlights', 'completed', 'paid', NOW() - INTERVAL 14 DAY),
(23, 3, CURDATE() - INTERVAL 12 DAY, '09:00:00', '10:00:00', 1.0, 3000.00, NULL, 'completed', 'paid', NOW() - INTERVAL 12 DAY),
(24, 4, CURDATE() - INTERVAL 10 DAY, '06:00:00', '07:00:00', 1.0, 2500.00, 'Lane 3 preferred', 'completed', 'paid', NOW() - INTERVAL 10 DAY),
(25, 5, CURDATE() - INTERVAL 8 DAY, '18:00:00', '20:00:00', 2.0, 4000.00, NULL, 'completed', 'paid', NOW() - INTERVAL 8 DAY),
(21, 2, CURDATE() - INTERVAL 7 DAY, '17:00:00', '19:00:00', 2.0, 12000.00, 'Team practice — 14 players', 'completed', 'paid', NOW() - INTERVAL 7 DAY),
(22, 1, CURDATE() - INTERVAL 5 DAY, '14:00:00', '17:00:00', 3.0, 24000.00, 'Inter-club match, need scoreboard', 'completed', 'paid', NOW() - INTERVAL 5 DAY),
(23, 4, CURDATE() - INTERVAL 3 DAY, '07:00:00', '08:00:00', 1.0, 2500.00, NULL, 'completed', 'paid', NOW() - INTERVAL 3 DAY),
-- Confirmed upcoming bookings
(24, 1, CURDATE() + INTERVAL 1 DAY, '09:00:00', '11:00:00', 2.0, 16000.00, 'Morning practice session', 'confirmed', 'paid', NOW() - INTERVAL 1 DAY),
(25, 2, CURDATE() + INTERVAL 2 DAY, '16:00:00', '18:00:00', 2.0, 12000.00, 'Friendly match — bring cones', 'confirmed', 'paid', NOW() - INTERVAL 12 HOUR),
(21, 3, CURDATE() + INTERVAL 3 DAY, '10:00:00', '11:00:00', 1.0, 3000.00, NULL, 'confirmed', 'paid', NOW() - INTERVAL 6 HOUR),
(22, 5, CURDATE() + INTERVAL 4 DAY, '19:00:00', '21:00:00', 2.0, 4000.00, 'Doubles tournament practice', 'confirmed', 'paid', NOW() - INTERVAL 3 HOUR),
-- Pending bookings
(23, 1, CURDATE() + INTERVAL 5 DAY, '15:00:00', '17:00:00', 2.0, 16000.00, 'Weekend cricket game', 'pending', 'pending', NOW() - INTERVAL 1 HOUR),
(24, 4, CURDATE() + INTERVAL 6 DAY, '06:00:00', '07:30:00', 1.5, 3750.00, 'Early morning swim', 'pending', 'pending', NOW() - INTERVAL 30 MINUTE),
-- Cancelled booking
(25, 3, CURDATE() - INTERVAL 2 DAY, '11:00:00', '12:00:00', 1.0, 3000.00, 'Rain expected', 'cancelled', 'refunded', NOW() - INTERVAL 4 DAY);

-- =============================================
-- 7. COACH BOOKINGS
-- =============================================
TRUNCATE TABLE coach_bookings;
INSERT INTO coach_bookings (user_id, coach_id, booking_date, start_time, end_time, duration_hours, session_type, total_amount, special_requests, status, payment_status, created_at) VALUES
-- Completed
(21, 1, CURDATE() - INTERVAL 14 DAY, '09:00:00', '11:00:00', 2.0, 'individual', 7000.00, 'Focus on batting technique', 'completed', 'paid', NOW() - INTERVAL 14 DAY),
(22, 2, CURDATE() - INTERVAL 12 DAY, '15:00:00', '17:00:00', 2.0, 'group', 10000.00, 'Group of 6 beginners', 'completed', 'paid', NOW() - INTERVAL 12 DAY),
(23, 3, CURDATE() - INTERVAL 10 DAY, '10:00:00', '11:00:00', 1.0, 'individual', 4000.00, 'Serve improvement', 'completed', 'paid', NOW() - INTERVAL 10 DAY),
(24, 4, CURDATE() - INTERVAL 8 DAY, '07:00:00', '08:00:00', 1.0, 'individual', 3500.00, 'Freestyle technique', 'completed', 'paid', NOW() - INTERVAL 8 DAY),
(25, 5, CURDATE() - INTERVAL 6 DAY, '18:00:00', '19:00:00', 1.0, 'assessment', 4500.00, 'Skill assessment for tournament', 'completed', 'paid', NOW() - INTERVAL 6 DAY),
(21, 3, CURDATE() - INTERVAL 4 DAY, '16:00:00', '17:00:00', 1.0, 'individual', 4000.00, 'Backhand drills', 'completed', 'paid', NOW() - INTERVAL 4 DAY),
-- Confirmed upcoming
(22, 1, CURDATE() + INTERVAL 1 DAY, '09:00:00', '11:00:00', 2.0, 'individual', 7000.00, 'Bowling practice', 'confirmed', 'paid', NOW() - INTERVAL 2 DAY),
(23, 5, CURDATE() + INTERVAL 2 DAY, '17:00:00', '18:00:00', 1.0, 'individual', 4500.00, 'Footwork drills', 'confirmed', 'paid', NOW() - INTERVAL 1 DAY),
-- Pending
(24, 2, CURDATE() + INTERVAL 3 DAY, '14:00:00', '16:00:00', 2.0, 'group', 10000.00, 'Youth team training — 8 kids', 'pending', 'pending', NOW() - INTERVAL 6 HOUR),
-- Cancelled
(25, 4, CURDATE() - INTERVAL 1 DAY, '08:00:00', '09:00:00', 1.0, 'individual', 3500.00, NULL, 'cancelled', 'refunded', NOW() - INTERVAL 3 DAY);

-- =============================================
-- 8. UPDATE COACHES (fill every column)
-- =============================================
UPDATE coaches SET
  experience_years = 12, bio = 'Former national team cricketer with 15 years of playing experience. Specializes in batting technique, fielding drills, and mental conditioning for competitive players.',
  specializations = 'Batting Technique, Fielding, Mental Conditioning',
  certifications = 'ICC Level 2 Coach, SLC Advanced Coaching Diploma',
  rating = 4.90, total_reviews = 15, total_sessions = 48,
  location = 'Colombo 07, Western Province'
WHERE id = 1;

UPDATE coaches SET
  experience_years = 8, bio = 'UEFA B licensed football coach with experience training youth academies. Passionate about developing young talent and tactical awareness in grassroots football.',
  specializations = 'Youth Development, Tactical Play, Fitness',
  certifications = 'UEFA B License, AFC C License',
  rating = 4.70, total_reviews = 10, total_sessions = 35,
  location = 'Kandy, Central Province'
WHERE id = 2;

UPDATE coaches SET
  experience_years = 10, bio = 'National-level tennis player turned coach. ITF certified with expertise in serve mechanics, court strategy, and competitive match preparation for all skill levels.',
  specializations = 'Serve Technique, Match Strategy, Junior Development',
  certifications = 'ITF Level 2 Coach, SL Tennis Association Certified',
  rating = 4.60, total_reviews = 8, total_sessions = 42,
  location = 'Colombo 03, Western Province'
WHERE id = 3;

UPDATE coaches SET
  experience_years = 15, bio = 'Former national swimming champion and FINA certified instructor. Specializes in competitive stroke technique, endurance training, and water safety for all ages.',
  specializations = 'Freestyle, Butterfly, Endurance Training, Water Safety',
  certifications = 'FINA Level 2 Coach, Red Cross Lifeguard Instructor',
  rating = 4.80, total_reviews = 12, total_sessions = 55,
  location = 'Negombo, Western Province'
WHERE id = 4;

UPDATE coaches SET
  experience_years = 6, bio = 'Competitive badminton player with BWF coaching certification. Expert in singles and doubles strategy, smash technique, and agility training for tournament players.',
  specializations = 'Singles Strategy, Smash Technique, Agility, Doubles',
  certifications = 'BWF Level 2 Coach, National Sports Institute Diploma',
  rating = 4.50, total_reviews = 6, total_sessions = 28,
  location = 'Kandy, Central Province'
WHERE id = 5;

-- =============================================
-- 9. COACH ACHIEVEMENTS
-- =============================================
TRUNCATE TABLE coach_achievements;
INSERT INTO coach_achievements (coach_id, title, description, date_achieved, category) VALUES
(1, 'National Cricket Coach of the Year 2023', 'Recognized by Sri Lanka Cricket for outstanding coaching contributions at grassroots level.', '2023-11-15', 'award'),
(1, 'Trained 3 Provincial Players', 'Three students selected for Western Province U-19 cricket squad.', '2024-01-20', 'milestone'),
(2, 'AFC Youth Development Award', 'Awarded for exceptional youth football development programs in Central Province.', '2024-03-10', 'award'),
(2, 'Kandy District U-17 Champions', 'Coached team won the Kandy District U-17 Football Championship.', '2023-09-05', 'competition'),
(3, 'ITF Level 2 Certification', 'Successfully completed the International Tennis Federation Level 2 coaching program.', '2022-06-20', 'certification'),
(3, '100th Coaching Session Milestone', 'Completed 100 professional coaching sessions on GoPlay.', '2024-02-14', 'milestone'),
(4, 'National Swimming Championship Gold', 'Won gold in 200m Freestyle at the National Swimming Championships.', '2020-08-12', 'competition'),
(4, 'FINA Coaching Excellence Award', 'Recognized for creating an inclusive swimming program for differently-abled athletes.', '2023-12-01', 'award'),
(5, 'BWF Level 2 Certification', 'Completed Badminton World Federation Level 2 coaching certification in Malaysia.', '2023-05-18', 'certification'),
(5, 'Inter-Club Badminton Champion', 'Led coaching team to victory at the All-Island Inter-Club Championship.', '2024-01-28', 'competition');

-- =============================================
-- 10. COACH CERTIFICATES
-- =============================================
TRUNCATE TABLE coach_certificates;
INSERT INTO coach_certificates (coach_id, title, issuing_organization, issue_date, expiry_date, credential_id) VALUES
(1, 'ICC Level 2 Coaching Certificate', 'International Cricket Council', '2019-03-15', '2025-03-15', 'ICC-LK-2019-0842'),
(1, 'SLC Advanced Coaching Diploma', 'Sri Lanka Cricket', '2020-08-20', '2026-08-20', 'SLC-ADV-2020-156'),
(2, 'UEFA B License', 'Union of European Football Associations', '2021-06-10', '2027-06-10', 'UEFA-B-2021-LK-073'),
(2, 'AFC C License', 'Asian Football Confederation', '2019-11-25', '2025-11-25', 'AFC-C-2019-4521'),
(3, 'ITF Level 2 Coaching Certificate', 'International Tennis Federation', '2022-06-20', '2028-06-20', 'ITF-L2-2022-SL-089'),
(4, 'FINA Level 2 Coaching Certificate', 'World Aquatics (FINA)', '2018-09-14', '2024-09-14', 'FINA-L2-2018-3367'),
(4, 'Red Cross Lifeguard Instructor', 'Sri Lanka Red Cross Society', '2020-02-28', '2026-02-28', 'SLRC-LGI-2020-112'),
(5, 'BWF Level 2 Coaching Certificate', 'Badminton World Federation', '2023-05-18', '2029-05-18', 'BWF-L2-2023-MY-445');

-- =============================================
-- 11. COACH REVIEWS
-- =============================================
TRUNCATE TABLE coach_reviews;
INSERT INTO coach_reviews (coach_id, user_id, booking_id, rating, review_text, created_at) VALUES
(1, 21, 1, 5, 'Incredible cricket coaching! My batting average improved dramatically after just 3 sessions. Highly professional and patient.', NOW() - INTERVAL 13 DAY),
(1, 22, 7, 5, 'Best cricket coach in Colombo. His bowling drills are next-level. Already seeing results in my club matches.', NOW() - INTERVAL 3 DAY),
(2, 22, 2, 5, 'Amazing group session. The kids loved every minute. Tactically brilliant and great with beginners.', NOW() - INTERVAL 11 DAY),
(2, 24, NULL, 4, 'Good coaching style. Very organized sessions. Would appreciate slightly longer warm-ups.', NOW() - INTERVAL 6 DAY),
(3, 23, 3, 5, 'My serve speed increased by 15 km/h! Technical knowledge is outstanding. Worth every rupee.', NOW() - INTERVAL 9 DAY),
(3, 21, 6, 4, 'Great backhand drills session. Very detail-oriented coach. The court was in perfect condition too.', NOW() - INTERVAL 3 DAY),
(4, 24, 4, 5, 'Transformed my freestyle technique. Patient and encouraging — perfect for nervous swimmers.', NOW() - INTERVAL 7 DAY),
(4, 25, NULL, 5, 'Excellent swimming instructor. Safety-conscious and technically brilliant. My daughter loves the lessons!', NOW() - INTERVAL 4 DAY),
(5, 25, 5, 4, 'Solid badminton coaching. Good footwork drills. Would like more focus on doubles strategy.', NOW() - INTERVAL 5 DAY),
(5, 23, 8, 5, 'Fantastic session! Smash technique improved immediately. Very knowledgeable about BWF rules too.', NOW() - INTERVAL 1 DAY);

-- =============================================
-- 12. ORDERS & ORDER ITEMS (Equipment Shop)
-- =============================================
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;
INSERT INTO orders (id, order_number, user_id, order_type, subtotal, tax_amount, shipping_amount, discount_amount, total_amount, currency, status, payment_status, payment_method, shipping_address, billing_address, notes, created_at) VALUES
(1, 'GP-ORD-20260401', 21, 'product', 8500.00, 0.00, 350.00, 0.00, 8850.00, 'LKR', 'delivered', 'paid', 'card',
  '{"street":"42 Galle Road","city":"Colombo","postal":"10350"}',
  '{"street":"42 Galle Road","city":"Colombo","postal":"10350"}',
  'Please deliver before 5 PM', NOW() - INTERVAL 12 DAY),
(2, 'GP-ORD-20260405', 22, 'product', 15200.00, 0.00, 500.00, 1500.00, 14200.00, 'LKR', 'delivered', 'paid', 'card',
  '{"street":"15 Kandy Road","city":"Kandy","postal":"20400"}',
  '{"street":"15 Kandy Road","city":"Kandy","postal":"20400"}',
  NULL, NOW() - INTERVAL 10 DAY),
(3, 'GP-ORD-20260409', 23, 'product', 4500.00, 0.00, 350.00, 0.00, 4850.00, 'LKR', 'delivered', 'paid', 'bank_transfer',
  '{"street":"88 High Level Road","city":"Nugegoda","postal":"10250"}',
  '{"street":"88 High Level Road","city":"Nugegoda","postal":"10250"}',
  'Gift wrap please', NOW() - INTERVAL 8 DAY),
(4, 'GP-ORD-20260412', 24, 'product', 12000.00, 0.00, 0.00, 0.00, 12000.00, 'LKR', 'shipped', 'paid', 'card',
  '{"street":"33 Temple Road","city":"Maharagama","postal":"10280"}',
  '{"street":"33 Temple Road","city":"Maharagama","postal":"10280"}',
  NULL, NOW() - INTERVAL 3 DAY),
(5, 'GP-ORD-20260415', 25, 'product', 6800.00, 0.00, 350.00, 500.00, 6650.00, 'LKR', 'processing', 'paid', 'card',
  '{"street":"56 Beach Road","city":"Mount Lavinia","postal":"10370"}',
  '{"street":"56 Beach Road","city":"Mount Lavinia","postal":"10370"}',
  'Call before delivery', NOW() - INTERVAL 1 DAY),
(6, 'GP-ORD-20260416', 21, 'product', 3200.00, 0.00, 350.00, 0.00, 3550.00, 'LKR', 'pending', 'pending', 'card',
  '{"street":"42 Galle Road","city":"Colombo","postal":"10350"}',
  '{"street":"42 Galle Road","city":"Colombo","postal":"10350"}',
  NULL, NOW() - INTERVAL 2 HOUR),
-- Cancelled order
(7, 'GP-ORD-20260410', 23, 'product', 7500.00, 0.00, 350.00, 0.00, 7850.00, 'LKR', 'cancelled', 'refunded', 'card',
  '{"street":"88 High Level Road","city":"Nugegoda","postal":"10250"}',
  '{"street":"88 High Level Road","city":"Nugegoda","postal":"10250"}',
  'Changed my mind — wrong size', NOW() - INTERVAL 6 DAY);

INSERT INTO order_items (order_id, product_id, item_name, item_description, quantity, unit_price, total_price, selected_size, selected_color) VALUES
(1, 1, 'Professional Cricket Bat', 'English willow, Grade A', 1, 8500.00, 8500.00, NULL, 'Natural'),
(2, 3, 'Football Boots Pro', 'Firm ground, lightweight', 1, 7500.00, 7500.00, '42', 'Black/Gold'),
(2, 5, 'Training Football', 'FIFA approved match ball', 2, 3850.00, 7700.00, 'Size 5', 'White/Blue'),
(3, 7, 'Tennis Racquet Lite', 'Beginner-intermediate level', 1, 4500.00, 4500.00, 'L3', 'Blue/White'),
(4, 9, 'Swimming Goggles Pro', 'Anti-fog, UV protection', 2, 2800.00, 5600.00, NULL, 'Black'),
(4, 10, 'Swim Cap Silicone', 'Premium silicone, durable', 2, 1200.00, 2400.00, NULL, 'Blue'),
(4, 11, 'Kickboard Float', 'High-density foam', 2, 2000.00, 4000.00, NULL, 'Yellow'),
(5, 2, 'Cricket Batting Gloves', 'Premium leather, lightweight', 1, 3500.00, 3500.00, 'L', 'White'),
(5, 4, 'Badminton Racquet Elite', 'Carbon fiber frame', 1, 3300.00, 3300.00, 'G4', 'Red/Black'),
(6, 6, 'Sport Water Bottle 1L', 'BPA-free, insulated', 2, 1600.00, 3200.00, NULL, 'Blue'),
(7, 8, 'Yoga Mat Premium', 'Non-slip, 6mm thick', 1, 7500.00, 7500.00, NULL, 'Purple');

-- =============================================
-- 13. PAYMENTS
-- =============================================
TRUNCATE TABLE payments;
INSERT INTO payments (order_id, transaction_id, payment_method, amount, currency, status, gateway_response, processed_at, created_at) VALUES
(1, 'TXN-PAY-20260401-001', 'credit_card', 8850.00, 'LKR', 'completed', '{"code":"00","message":"Approved","auth_code":"A12345"}', NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 12 DAY),
(2, 'TXN-PAY-20260405-002', 'credit_card', 14200.00, 'LKR', 'completed', '{"code":"00","message":"Approved","auth_code":"B23456"}', NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 10 DAY),
(3, 'TXN-PAY-20260409-003', 'bank_transfer', 4850.00, 'LKR', 'completed', '{"code":"00","message":"Transfer confirmed","ref":"BT-98765"}', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY),
(4, 'TXN-PAY-20260412-004', 'credit_card', 12000.00, 'LKR', 'completed', '{"code":"00","message":"Approved","auth_code":"D45678"}', NOW() - INTERVAL 3 DAY, NOW() - INTERVAL 3 DAY),
(5, 'TXN-PAY-20260415-005', 'debit_card', 6650.00, 'LKR', 'completed', '{"code":"00","message":"Approved","auth_code":"E56789"}', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY),
(6, 'TXN-PAY-20260416-006', 'credit_card', 3550.00, 'LKR', 'pending', NULL, NULL, NOW() - INTERVAL 2 HOUR),
(7, 'TXN-PAY-20260410-007', 'credit_card', 7850.00, 'LKR', 'refunded', '{"code":"00","message":"Refund processed","ref":"RF-11111"}', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 6 DAY);

-- =============================================
-- 14. PRODUCT REVIEWS
-- =============================================
TRUNCATE TABLE product_reviews;
INSERT INTO product_reviews (product_id, user_id, order_id, rating, title, review_text, is_verified_purchase, created_at) VALUES
(1, 21, 1, 5, 'Excellent bat!', 'Perfect weight and balance. Great pickup. Scored a century in my first match with it!', 1, NOW() - INTERVAL 10 DAY),
(3, 22, 2, 5, 'Best boots Ive owned', 'Lightweight and great grip on firm ground. Comfortable from the first wear.', 1, NOW() - INTERVAL 8 DAY),
(5, 22, 2, 4, 'Good match ball', 'Nice feel and good flight. Slightly heavier than expected but great for training.', 1, NOW() - INTERVAL 7 DAY),
(7, 23, 3, 5, 'Perfect for intermediate', 'Great racquet for the price. Good control and power. Recommended for club players.', 1, NOW() - INTERVAL 6 DAY),
(9, 24, 4, 4, 'Anti-fog works well', 'Clear vision underwater. Comfortable fit. Slight pressure on nose bridge after 1 hour.', 1, NOW() - INTERVAL 2 DAY),
(2, 25, 5, 5, 'Premium quality', 'Excellent protection and grip. Very comfortable batting gloves. Worth the investment.', 1, NOW() - INTERVAL 12 HOUR);

-- =============================================
-- 15. SHOP OWNER PROFILES
-- =============================================
-- Get shop owner user IDs
INSERT INTO shop_owner_profiles (user_id, shop_name, business_name, business_registration_number, tax_identification_number, business_type, year_established, number_of_employees, business_phone, business_email, shop_address, shop_city, shop_state, shop_postal_code, business_description, brand_names, return_policy, warranty_policy, total_products, total_sales, average_rating, total_reviews, payout_bank_name, payout_account_number, payout_account_holder, payout_branch_name, profile_completion_percentage)
SELECT id,
  'GoPlay Sports Store',
  'GoPlay Equipment Trading (Pvt) Ltd',
  'PV-2024-88901',
  'TIN-2024-556677',
  'company',
  2022,
  8,
  '0112890123',
  'shop@goplay.lk',
  '55 Duplication Road, Colombo 04',
  'Colombo',
  'Western',
  '00400',
  'Official GoPlay sports equipment store offering premium cricket, football, tennis, swimming, and badminton gear from top international brands.',
  'Kookaburra, Nike, Wilson, Speedo, Yonex',
  'Returns accepted within 14 days of delivery in original packaging. Items must be unused and in original condition.',
  '6-month warranty on manufacturing defects. Does not cover wear and tear from normal use.',
  12, 18, 4.60, 6,
  'Commercial Bank of Ceylon',
  '8012345678',
  'GoPlay Equipment Trading Pvt Ltd',
  'Colombo Main Branch',
  95
FROM users WHERE email = 'admin1@goplay.lk' LIMIT 1
ON DUPLICATE KEY UPDATE shop_name = VALUES(shop_name);

-- =============================================
-- 16. SHOPS
-- =============================================
TRUNCATE TABLE shops;
INSERT INTO shops (user_id, name, description, logo, address, city, state, phone, email, website, rating, total_reviews, status)
SELECT id,
  'GoPlay Sports Store',
  'Your one-stop destination for premium sports equipment. We stock cricket, football, tennis, swimming and badminton gear from leading international brands.',
  NULL,
  '55 Duplication Road, Colombo 04',
  'Colombo',
  'Western',
  '0112890123',
  'shop@goplay.lk',
  'https://goplay.lk/shop',
  4.60, 6, 'active'
FROM users WHERE email = 'admin1@goplay.lk' LIMIT 1;

-- =============================================
-- 17. PLATFORM EARNINGS
-- =============================================
TRUNCATE TABLE platform_earnings;
INSERT INTO platform_earnings (payment_id, order_id, booking_id, booking_type, transaction_amount, service_fee_percentage, service_fee_amount, merchant_amount, merchant_id, currency, status, description, created_at) VALUES
(1, 1, NULL, NULL, 8850.00, 10.00, 885.00, 7965.00, 1, 'LKR', 'completed', 'Product order GP-ORD-20260401', NOW() - INTERVAL 12 DAY),
(2, 2, NULL, NULL, 14200.00, 10.00, 1420.00, 12780.00, 1, 'LKR', 'completed', 'Product order GP-ORD-20260405', NOW() - INTERVAL 10 DAY),
(3, 3, NULL, NULL, 4850.00, 10.00, 485.00, 4365.00, 1, 'LKR', 'completed', 'Product order GP-ORD-20260409', NOW() - INTERVAL 8 DAY),
(4, 4, NULL, NULL, 12000.00, 10.00, 1200.00, 10800.00, 1, 'LKR', 'completed', 'Product order GP-ORD-20260412', NOW() - INTERVAL 3 DAY),
(5, 5, NULL, NULL, 6650.00, 10.00, 665.00, 5985.00, 1, 'LKR', 'completed', 'Product order GP-ORD-20260415', NOW() - INTERVAL 1 DAY),
-- Facility booking earnings
(NULL, NULL, 1, 'facility', 16000.00, 8.00, 1280.00, 14720.00, 6, 'LKR', 'completed', 'Facility booking - Royal Cricket', NOW() - INTERVAL 15 DAY),
(NULL, NULL, 2, 'facility', 12000.00, 8.00, 960.00, 11040.00, 7, 'LKR', 'completed', 'Facility booking - Green Field', NOW() - INTERVAL 14 DAY),
(NULL, NULL, 3, 'facility', 3000.00, 8.00, 240.00, 2760.00, 8, 'LKR', 'completed', 'Facility booking - Ace Tennis', NOW() - INTERVAL 12 DAY),
(NULL, NULL, 4, 'facility', 2500.00, 8.00, 200.00, 2300.00, 9, 'LKR', 'completed', 'Facility booking - Splash Swimming', NOW() - INTERVAL 10 DAY),
(NULL, NULL, 5, 'facility', 4000.00, 8.00, 320.00, 3680.00, 10, 'LKR', 'completed', 'Facility booking - Elite Badminton', NOW() - INTERVAL 8 DAY);

-- =============================================
-- 18. GROUND OWNER NOTIFICATIONS
-- =============================================
TRUNCATE TABLE ground_owner_notifications;
INSERT INTO ground_owner_notifications (owner_id, title, message, notification_type, reference_id, reference_type, is_read, read_at, priority, created_at) VALUES
(6, 'New Booking Received', 'Kavinda R. has booked Royal Cricket Grounds for tomorrow 09:00-11:00.', 'booking', 9, 'facility_booking', 1, NOW() - INTERVAL 1 DAY, 'high', NOW() - INTERVAL 1 DAY),
(6, 'Payment Received', 'Payment of LKR 16,000 confirmed for booking #9 at Royal Cricket Grounds.', 'payment', 1, 'payment', 1, NOW() - INTERVAL 1 DAY, 'medium', NOW() - INTERVAL 1 DAY),
(6, 'New Review Posted', 'Kavinda R. left a 5-star review for Royal Cricket Grounds. "Absolutely world-class..."', 'review', 1, 'facility_review', 0, NULL, 'low', NOW() - INTERVAL 3 DAY),
(7, 'New Booking Received', 'Janith K. has booked Green Field Football Arena for 2 days from now.', 'booking', 10, 'facility_booking', 0, NULL, 'high', NOW() - INTERVAL 12 HOUR),
(7, 'Maintenance Reminder', 'Turf maintenance is due this week. Please schedule during off-peak hours.', 'maintenance', NULL, NULL, 0, NULL, 'medium', NOW() - INTERVAL 2 DAY),
(8, 'Booking Cancelled', 'Janith K. cancelled the booking at Ace Tennis Club for tomorrow. Full refund processed.', 'booking', 15, 'facility_booking', 1, NOW() - INTERVAL 2 DAY, 'high', NOW() - INTERVAL 4 DAY),
(9, 'New Review Posted', 'Tharushi A. left a 5-star review for Splash Swimming Complex.', 'review', 4, 'facility_review', 0, NULL, 'low', NOW() - INTERVAL 10 DAY),
(10, 'System Update', 'GoPlay platform has been updated with new booking features. Check your dashboard for details.', 'system', NULL, NULL, 0, NULL, 'low', NOW() - INTERVAL 5 DAY);

-- =============================================
-- 19. USER ACTIVITIES
-- =============================================
TRUNCATE TABLE user_activities;
INSERT INTO user_activities (user_id, activity_type, description, ip_address, created_at) VALUES
(21, 'booking_created', 'Created facility booking at Royal Cricket Grounds', '192.168.1.100', NOW() - INTERVAL 15 DAY),
(21, 'payment_made', 'Paid LKR 16,000 for facility booking #1', '192.168.1.100', NOW() - INTERVAL 15 DAY),
(21, 'review_posted', 'Posted 5-star review for Royal Cricket Grounds', '192.168.1.100', NOW() - INTERVAL 3 DAY),
(21, 'order_placed', 'Placed order GP-ORD-20260401 for Cricket Bat', '192.168.1.100', NOW() - INTERVAL 12 DAY),
(22, 'booking_created', 'Created facility booking at Green Field Football Arena', '192.168.2.55', NOW() - INTERVAL 14 DAY),
(22, 'coach_booked', 'Booked coaching session with Coach Nimal (Football)', '192.168.2.55', NOW() - INTERVAL 12 DAY),
(22, 'order_placed', 'Placed order GP-ORD-20260405 for Football gear', '192.168.2.55', NOW() - INTERVAL 10 DAY),
(23, 'booking_created', 'Created facility booking at Ace Tennis Club', '172.16.0.88', NOW() - INTERVAL 12 DAY),
(23, 'profile_updated', 'Updated profile information and phone number', '172.16.0.88', NOW() - INTERVAL 8 DAY),
(24, 'booking_created', 'Created facility booking at Splash Swimming Complex', '192.168.1.200', NOW() - INTERVAL 10 DAY),
(24, 'coach_booked', 'Booked swimming coaching session with Coach Sunil', '192.168.1.200', NOW() - INTERVAL 8 DAY),
(25, 'booking_created', 'Created facility booking at Elite Badminton Centre', '10.10.10.25', NOW() - INTERVAL 8 DAY),
(25, 'order_placed', 'Placed order GP-ORD-20260415 for sports gear', '10.10.10.25', NOW() - INTERVAL 1 DAY),
(1, 'admin_login', 'Admin logged into dashboard', '192.168.0.1', NOW() - INTERVAL 1 HOUR),
(1, 'settings_updated', 'Updated platform commission rates', '192.168.0.1', NOW() - INTERVAL 2 DAY);

-- =============================================
-- 20. ADMIN AUDIT LOG
-- =============================================
TRUNCATE TABLE admin_audit_log;
INSERT INTO admin_audit_log (admin_id, action_type, target_user_id, data, ip_address, created_at) VALUES
(1, 'user_approved', 6, '{"action":"Approved ground owner application","facility":"Royal Cricket Grounds"}', '192.168.0.1', NOW() - INTERVAL 30 DAY),
(1, 'user_approved', 7, '{"action":"Approved ground owner application","facility":"Green Field Football Arena"}', '192.168.0.1', NOW() - INTERVAL 28 DAY),
(1, 'settings_changed', NULL, '{"key":"commission_rate","old":"12%","new":"10%","reason":"Competitive pricing"}', '192.168.0.1', NOW() - INTERVAL 14 DAY),
(2, 'user_suspended', 28, '{"action":"Suspended test user account","reason":"Testing suspension workflow"}', '192.168.0.2', NOW() - INTERVAL 10 DAY),
(1, 'promotion_created', NULL, '{"title":"Grand Opening 20% Off","discount":"20%","valid_until":"2026-05-31"}', '192.168.0.1', NOW() - INTERVAL 7 DAY),
(1, 'news_published', NULL, '{"title":"Season Opening Cricket Matches","category":"Cricket"}', '192.168.0.1', NOW() - INTERVAL 5 DAY),
(2, 'user_reactivated', 28, '{"action":"Reactivated test user account"}', '192.168.0.2', NOW() - INTERVAL 3 DAY),
(1, 'payout_processed', 6, '{"amount":"LKR 45,000","bank":"Commercial Bank","ref":"PAY-2026-001"}', '192.168.0.1', NOW() - INTERVAL 2 DAY);

-- =============================================
-- 21. SHOP OWNER BALANCES
-- =============================================
TRUNCATE TABLE shop_owner_balances;
INSERT INTO shop_owner_balances (shop_owner_id, available_balance, pending_balance, total_earned, total_withdrawn, currency)
SELECT id, 28895.00, 5985.00, 41895.00, 7015.00, 'LKR'
FROM users WHERE email = 'admin1@goplay.lk' LIMIT 1;

-- =============================================
-- 22. SHOP OWNER TRANSACTIONS
-- =============================================
TRUNCATE TABLE shop_owner_transactions;

-- =============================================
-- 23. WITHDRAWAL REQUESTS
-- =============================================
TRUNCATE TABLE withdrawal_requests;
INSERT INTO withdrawal_requests (user_id, amount, payment_method, bank_name, account_number, account_holder, branch_code, status, processed_by, processed_at, transaction_reference, notes, created_at) VALUES
(6, 25000.00, 'bank_transfer', 'Commercial Bank of Ceylon', '8012345678', 'Rajesh Perera', 'CMB-COL-001', 'completed', 1, NOW() - INTERVAL 5 DAY, 'WD-2026-0001', 'Monthly facility earnings withdrawal', NOW() - INTERVAL 7 DAY),
(7, 18000.00, 'bank_transfer', 'Sampath Bank', '0056789012', 'Nimal Silva', 'SPB-KDY-002', 'completed', 1, NOW() - INTERVAL 3 DAY, 'WD-2026-0002', 'Bi-weekly payout', NOW() - INTERVAL 5 DAY),
(8, 12000.00, 'bank_transfer', 'Hatton National Bank', '1098765432', 'Kamal Fernando', 'HNB-COL-003', 'processing', NULL, NULL, NULL, 'Pending bank verification', NOW() - INTERVAL 1 DAY),
(6, 20000.00, 'bank_transfer', 'Commercial Bank of Ceylon', '8012345678', 'Rajesh Perera', 'CMB-COL-001', 'pending', NULL, NULL, NULL, 'Second withdrawal request this month', NOW() - INTERVAL 6 HOUR);

-- =============================================
-- 24. INVENTORY LOGS
-- =============================================
TRUNCATE TABLE inventory_logs;
INSERT INTO inventory_logs (product_id, user_id, type, quantity, previous_stock, new_stock, reason, created_at) VALUES
(1, 1, 'add', 20, 0, 20, 'Initial stock load from supplier Kookaburra', NOW() - INTERVAL 30 DAY),
(2, 1, 'add', 30, 0, 30, 'Initial stock load', NOW() - INTERVAL 30 DAY),
(3, 1, 'add', 15, 0, 15, 'Initial stock load from Nike', NOW() - INTERVAL 30 DAY),
(5, 1, 'add', 50, 0, 50, 'Bulk order from Adidas distributor', NOW() - INTERVAL 30 DAY),
(1, NULL, 'sale', -1, 20, 19, 'Sold via order GP-ORD-20260401', NOW() - INTERVAL 12 DAY),
(3, NULL, 'sale', -1, 15, 14, 'Sold via order GP-ORD-20260405', NOW() - INTERVAL 10 DAY),
(5, NULL, 'sale', -2, 50, 48, 'Sold 2 units via order GP-ORD-20260405', NOW() - INTERVAL 10 DAY),
(7, NULL, 'sale', -1, 10, 9, 'Sold via order GP-ORD-20260409', NOW() - INTERVAL 8 DAY),
(9, NULL, 'sale', -2, 25, 23, 'Sold 2 units via order GP-ORD-20260412', NOW() - INTERVAL 3 DAY),
(1, 1, 'add', 10, 19, 29, 'Restocked from supplier — high demand', NOW() - INTERVAL 5 DAY),
(3, 1, 'adjustment', -2, 14, 12, 'Quality inspection — 2 units returned to supplier (defective)', NOW() - INTERVAL 4 DAY);

-- =============================================
-- 25. UPDATE FACILITY REVIEWS (update existing + recalculate)
-- =============================================
-- Add more reviews to make data richer
INSERT INTO facility_reviews (facility_id, user_id, rating, review_text, created_at) VALUES
(1, 25, 5, 'Top-notch cricket ground! The floodlights are brilliant for evening sessions. Staff is very helpful and professional.', NOW() - INTERVAL 6 DAY),
(2, 23, 4, 'Good football facility overall. Turf quality is excellent. Parking could be improved during peak hours.', NOW() - INTERVAL 9 DAY),
(2, 24, 5, 'Love playing here! The atmosphere under floodlights is incredible. Best football experience in Kandy.', NOW() - INTERVAL 4 DAY),
(3, 25, 4, 'Nice tennis courts. Well maintained surface. Coaching staff available on short notice which is a big plus.', NOW() - INTERVAL 5 DAY),
(4, 21, 5, 'Crystal clear water and impeccable hygiene. The 50m pool is Olympic quality. My go-to swimming spot.', NOW() - INTERVAL 2 DAY),
(5, 22, 4, 'Good badminton courts. Air conditioning is a lifesaver. Would appreciate longer evening hours on weekends.', NOW() - INTERVAL 1 DAY);

-- =============================================
-- DONE — Verify row counts
-- =============================================
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'SEED COMPLETE ✓' AS status;
SELECT TABLE_NAME, TABLE_ROWS FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'goplay_sports_platform'
AND TABLE_ROWS > 0
ORDER BY TABLE_ROWS DESC;
