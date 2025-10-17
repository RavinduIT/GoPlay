-- Booking System Database Updates (Final Version)
-- Run this script to ensure the ground booking system works properly

USE goplay_sports_platform;

-- Update existing bookings to have proper status values
UPDATE facility_bookings
SET status = 'confirmed'
WHERE status IS NULL OR status = '';

-- Update payment_status for ground bookings (no payment required)
UPDATE facility_bookings
SET payment_status = 'paid', total_amount = 0.00
WHERE payment_status IS NULL OR payment_status = '';

-- Insert some sample ground bookings for testing
INSERT IGNORE INTO facility_bookings (
    user_id,
    facility_id,
    booking_date,
    start_time,
    end_time,
    duration_hours,
    total_amount,
    special_requests,
    status,
    payment_status
) VALUES
-- Sample bookings for different users and facilities
(21, 1, CURDATE() + INTERVAL 1 DAY, '09:00:00', '11:00:00', 2.0, 0.00, 'Need footballs for team practice', 'confirmed', 'paid'),
(22, 1, CURDATE() + INTERVAL 2 DAY, '15:00:00', '17:00:00', 2.0, 0.00, '', 'confirmed', 'paid'),
(23, 2, CURDATE() + INTERVAL 3 DAY, '18:00:00', '20:00:00', 2.0, 0.00, 'Birthday party event', 'confirmed', 'paid'),
(21, 3, CURDATE() + INTERVAL 4 DAY, '10:00:00', '12:00:00', 2.0, 0.00, 'Corporate team building', 'pending', 'paid'),
(22, 4, CURDATE() + INTERVAL 5 DAY, '16:00:00', '18:00:00', 2.0, 0.00, 'Cricket practice session', 'confirmed', 'paid'),

-- Past bookings for statistics
(21, 1, CURDATE() - INTERVAL 7 DAY, '09:00:00', '11:00:00', 2.0, 0.00, 'Weekly football match', 'completed', 'paid'),
(22, 2, CURDATE() - INTERVAL 5 DAY, '14:00:00', '16:00:00', 2.0, 0.00, '', 'completed', 'paid'),
(23, 3, CURDATE() - INTERVAL 3 DAY, '17:00:00', '19:00:00', 2.0, 0.00, 'Tennis coaching', 'completed', 'paid'),

-- Today's bookings
(21, 1, CURDATE(), '19:00:00', '21:00:00', 2.0, 0.00, 'Evening football game', 'confirmed', 'paid'),
(22, 4, CURDATE(), '08:00:00', '10:00:00', 2.0, 0.00, 'Morning cricket practice', 'confirmed', 'paid');

-- Create a view for easier booking queries with all details
DROP VIEW IF EXISTS booking_details;
CREATE VIEW booking_details AS
SELECT
    fb.*,
    u.first_name,
    u.last_name,
    u.email,
    u.phone,
    sf.name as facility_name,
    sf.address as facility_address,
    CONCAT(sf.city, ', ', sf.state) as facility_location,
    sf.owner_id as facility_owner_id,
    sc.name as sport_name,
    sc.icon as sport_icon,
    owner.first_name as owner_first_name,
    owner.last_name as owner_last_name,
    owner.phone as owner_phone
FROM facility_bookings fb
JOIN users u ON fb.user_id = u.id
JOIN sports_facilities sf ON fb.facility_id = sf.id
LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
LEFT JOIN users owner ON sf.owner_id = owner.id
ORDER BY fb.booking_date DESC, fb.start_time DESC;

COMMIT;