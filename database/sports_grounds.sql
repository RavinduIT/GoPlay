-- Sports Grounds Data for GoPlay Platform
-- Simple data with 8 grounds around Colombo for book-ground UI
-- Run this after the main schema.sql

USE goplay_sports_platform;

-- Insert 8 sports facilities around Colombo
INSERT INTO sports_facilities (
    owner_id, name, description, sport_category_id, address, city, state, postal_code, 
    latitude, longitude, hourly_rate, capacity, amenities, images, rules, rating, total_reviews, status
) VALUES

-- Football Grounds
(6, 'Racecourse Football Ground', 
'Premium football turf with floodlights. Perfect for evening matches.',
3, 'Reid Avenue, Colombo 07', 'Colombo', 'Western Province', '00700',
6.9147, 79.8736, 2500.00, 22,
'["floodlights", "changing_rooms", "parking", "refreshments"]',
'["football1.jpg", "football2.jpg"]',
'No metal studs allowed on turf. Teams must bring own balls.',
4.5, 32, 'active'),

(7, 'Royal College Football Field', 
'Well-maintained grass football field in the heart of Colombo.',
3, 'Rajakeeya Mawatha, Colombo 07', 'Colombo', 'Western Province', '00700',
6.9074, 79.8612, 1800.00, 22,
'["changing_rooms", "parking", "refreshments"]',
'["football3.jpg", "football4.jpg"]',
'Proper football boots required. No glass bottles allowed.',
4.3, 28, 'active'),

-- Cricket Grounds
(8, 'SSC Cricket Ground', 
'Historic cricket ground with turf wicket. Perfect for serious cricket.',
4, 'Maitland Place, Colombo 07', 'Colombo', 'Western Province', '00700',
6.9165, 79.8574, 2800.00, 22,
'["turf_wicket", "pavilion", "changing_rooms", "scoreboard"]',
'["cricket1.jpg", "cricket2.jpg"]',
'Traditional cricket ground. Proper whites required for matches.',
4.6, 41, 'active'),

(9, 'Colombo Cricket Club Ground', 
'Premier cricket facility with excellent drainage and maintenance.',
4, 'Maitland Crescent, Colombo 07', 'Colombo', 'Western Province', '00700',
6.9154, 79.8578, 3200.00, 22,
'["premium_wicket", "modern_pavilion", "floodlights", "restaurant"]',
'["cricket3.jpg", "cricket4.jpg"]',
'Premium facility. Match fees include ground maintenance.',
4.8, 55, 'active'),

-- Tennis Courts
(10, 'Colombo Tennis Academy', 
'Professional tennis courts with clay and hard surfaces.',
2, 'Torrington Avenue, Colombo 07', 'Colombo', 'Western Province', '00700',
6.9158, 79.8745, 1200.00, 4,
'["clay_courts", "hard_courts", "coaching", "equipment_rental"]',
'["tennis1.jpg", "tennis2.jpg"]',
'Professional courts. Tennis attire required. Court shoes mandatory.',
4.4, 67, 'active'),

(6, 'Mount Lavinia Tennis Court', 
'Resort tennis court with ocean views. Perfect for recreational play.',
2, 'Hotel Road, Mount Lavinia', 'Mount Lavinia', 'Western Province', '10370',
6.8402, 79.8638, 1500.00, 4,
'["ocean_view", "resort_facilities", "equipment_included", "refreshments"]',
'["tennis3.jpg", "tennis4.jpg"]',
'Resort setting with ocean views. Equipment rental included.',
4.7, 38, 'active'),

-- Basketball Court
(7, 'Colombo Basketball Stadium', 
'Indoor basketball court with wooden floors and air conditioning.',
1, 'Independence Avenue, Colombo 07', 'Colombo', 'Western Province', '00700',
6.9147, 79.8693, 1000.00, 10,
'["indoor_court", "wooden_floor", "air_conditioning", "scoreboard"]',
'["basketball1.jpg", "basketball2.jpg"]',
'Indoor court with AC. Professional standards. Team bookings preferred.',
4.2, 45, 'active'),

-- Swimming Pool
(8, 'Colombo Swimming Club', 
'Olympic-size swimming pool with professional timing systems.',
5, 'Galle Face Court, Colombo 03', 'Colombo', 'Western Province', '00300',
6.9237, 79.8438, 1800.00, 50,
'["olympic_size", "timing_system", "coaching", "poolside_cafe"]',
'["swimming1.jpg", "swimming2.jpg"]',
'Olympic facility. Swimming attire mandatory. Lane bookings available.',
4.9, 78, 'active');

COMMIT;