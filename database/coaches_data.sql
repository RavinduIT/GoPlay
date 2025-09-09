-- Coaches Sample Data
-- This file populates the coaches table with sample data based on the existing UI coaches

USE goplay_sports_platform;

-- First, let's insert the coach data into the coaches table
-- Note: The coach users are already inserted in schema.sql (user IDs 11-15)

INSERT INTO coaches (
    user_id, 
    sport_category_id, 
    experience_years, 
    hourly_rate, 
    bio, 
    specializations, 
    certifications, 
    rating, 
    total_reviews, 
    total_sessions,
    location,
    status
) VALUES
-- Coach 1: Lasith Malinga (Cricket Coach - user_id 11)
(11, 4, 15, 3500.00, 
 'Former international cricket player with extensive coaching experience in cricket fundamentals and advanced techniques.',
 'Fast Bowling, Cricket Fundamentals, Match Strategy, Youth Development',
 'Level 3 Cricket Coach, Sports Psychology',
 4.9, 156, 230, 'Colombo', 'active'),

-- Coach 2: Angelo Mathews (Cricket Coach - user_id 12) - But we'll make him Tennis for variety
(12, 2, 8, 4000.00,
 'Professional tennis coach specializing in junior development and competitive training programs.',
 'Technique, Strategy, Match Play, Fitness',
 'ITF Certified, Youth Development',
 4.8, 89, 145, 'Kandy', 'active'),

-- Coach 3: Thisara Perera (Football Coach - user_id 13)
(13, 3, 10, 2800.00,
 'Former professional footballer now dedicated to developing the next generation of players.',
 'Technical Skills, Tactical Awareness, Physical Conditioning',
 'UEFA B License, Strength & Conditioning',
 4.7, 203, 187, 'Galle', 'active'),

-- Coach 4: Chaminda Vaas (Swimming Coach - user_id 14)
(14, 5, 12, 3200.00,
 'Olympic swimmer turned coach, specializing in competitive swimming and stroke technique.',
 'Stroke Technique, Endurance, Competition Prep',
 'Swim Coach Level 2, Water Safety',
 4.9, 92, 156, 'Colombo', 'active'),

-- Coach 5: Muttiah Muralitharan (Basketball Coach - user_id 15)
(15, 1, 7, 3000.00,
 'Basketball coach with focus on fundamental skills and team play development.',
 'Shooting, Defense, Team Strategy, Youth Development',
 'FIBA Certified, Youth Basketball',
 4.6, 134, 98, 'Negombo', 'active');

-- Insert additional coaches to make the system more realistic
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, user_type, status) VALUES
('coach6', 'coach6@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=3$SGVsbG9Xb3JsZA$nqsz3OBGzR7w8JiZXHnZxw8JGq1UPc9kB3zK9fP1wN4', 'Chamini', 'De Silva', '+94771234526', 'coach', 'active'),
('coach7', 'coach7@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=3$SGVsbG9Xb3JsZA$nqsz3OBGzR7w8JiZXHnZxw8JGq1UPc9kB3zK9fP1wN4', 'Ruwan', 'Kalpage', '+94771234527', 'coach', 'active'),
('coach8', 'coach8@goplay.lk', '$argon2id$v=19$m=65536,t=4,p=3$SGVsbG9Xb3JsZA$nqsz3OBGzR7w8JiZXHnZxw8JGq1UPc9kB3zK9fP1wN4', 'Sanduni', 'Perera', '+94771234528', 'coach', 'active');

-- Insert additional coach records
INSERT INTO coaches (
    user_id, 
    sport_category_id, 
    experience_years, 
    hourly_rate, 
    bio, 
    specializations, 
    certifications, 
    rating, 
    total_reviews, 
    total_sessions,
    location,
    status
) VALUES
-- Coach 6: Chamini De Silva (Badminton Coach)
(16, 6, 4, 2500.00,
 'Former national badminton player with expertise in competitive training and technique refinement.',
 'Technique, Footwork, Game Strategy, Mental Preparation',
 'BWF Certified, Sports Nutrition',
 4.8, 67, 89, 'Kandy', 'active'),

-- Coach 7: Ruwan Kalpage (Volleyball Coach)  
(17, 7, 6, 2700.00,
 'Professional volleyball coach with experience in team dynamics and competitive play.',
 'Spiking, Serving, Team Coordination, Defense',
 'FIVB Level 2, Team Psychology',
 4.5, 98, 112, 'Matara', 'active'),

-- Coach 8: Sanduni Perera (Soccer Coach)
(18, 8, 5, 3100.00,
 'Former national soccer player focused on youth development and technical skills.',
 'Ball Control, Passing, Shooting, Tactical Awareness',
 'FIFA D License, Youth Development',
 4.7, 123, 167, 'Kurunegala', 'active');

-- Insert some coach reviews for realistic data
INSERT INTO coach_reviews (coach_id, user_id, rating, review_text, created_at) VALUES
-- Reviews for Lasith Malinga (coach_id: 1)
(1, 21, 5, 'Excellent coaching! Malinga sir helped me improve my bowling technique significantly.', '2024-12-20 10:30:00'),
(1, 22, 5, 'Amazing experience. Very professional and knows his cricket well.', '2024-12-18 14:20:00'),
(1, 23, 4, 'Good coach but sessions are quite intense. Worth the investment.', '2024-12-15 16:45:00'),

-- Reviews for Angelo Mathews (coach_id: 2) 
(2, 21, 5, 'Great tennis coach with excellent knowledge and patience. Highly recommended!', '2024-12-19 09:15:00'),
(2, 24, 4, 'Very technical approach to tennis. Improved my game significantly.', '2024-12-17 11:30:00'),

-- Reviews for Thisara Perera (coach_id: 3)
(3, 22, 5, 'Best football coach I have worked with. Great tactical knowledge.', '2024-12-21 13:20:00'),
(3, 25, 4, 'Professional and dedicated. Helped improve my ball control.', '2024-12-16 15:10:00'),

-- Reviews for Chaminda Vaas (coach_id: 4)
(4, 23, 5, 'Outstanding swimming coach. Technique improvement was remarkable.', '2024-12-20 08:45:00'),
(4, 21, 5, 'Very patient and explains techniques clearly. Highly recommend.', '2024-12-14 17:30:00');

COMMIT;