-- ======================
-- NEWS TRACKING UPDATES
-- ======================

USE goplay_sports_platform;

-- Create news table if it doesn't exist
CREATE TABLE IF NOT EXISTS news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category VARCHAR(100) DEFAULT 'General',
    tags JSON,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    last_viewed_at TIMESTAMP NULL DEFAULT NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_news_status (status),
    INDEX idx_news_category (category),
    INDEX idx_news_published (published_at),
    INDEX idx_news_slug (slug),
    INDEX idx_news_views_published (views DESC, published_at DESC),
    INDEX idx_news_last_viewed (last_viewed_at),
    INDEX idx_news_trending (published_at DESC, views DESC)
);

-- Update existing news articles to set views to random values if they are NULL or 0
-- This makes the data more realistic for testing
UPDATE news SET views = FLOOR(RAND() * 1500) + 100 WHERE views = 0 OR views IS NULL;

-- Add indexes for better performance on views and analytics queries (will be created with table above)

-- Create analytics tracking table for detailed user engagement
CREATE TABLE IF NOT EXISTS news_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    news_id INT NOT NULL,
    user_id INT NULL,
    session_id VARCHAR(255) NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referer VARCHAR(255),
    time_spent_seconds INT DEFAULT 0,
    scroll_percentage DECIMAL(5,2) DEFAULT 0.00,
    device_type ENUM('desktop', 'mobile', 'tablet') DEFAULT 'desktop',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_analytics_news_date (news_id, created_at),
    INDEX idx_analytics_user (user_id),
    INDEX idx_analytics_session (session_id)
);

-- Create a view for popular news with time-based weighting
CREATE OR REPLACE VIEW popular_news AS
SELECT 
    n.*,
    COALESCE(n.views, 0) as view_count,
    CASE 
        WHEN n.published_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN COALESCE(n.views, 0) * 1.5
        WHEN n.published_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN COALESCE(n.views, 0) * 1.2
        ELSE COALESCE(n.views, 0)
    END as weighted_popularity_score
FROM news n
WHERE n.status = 'published' 
AND n.published_at <= NOW()
ORDER BY weighted_popularity_score DESC;

-- Insert additional sample news data with varied view counts and recent dates
INSERT IGNORE INTO news (title, slug, content, excerpt, featured_image, category, tags, author_id, status, published_at, views) VALUES

('Local Football Championship Final This Weekend', 
 'local-football-championship-final-weekend',
 'The much-anticipated local football championship final is set to take place this weekend at the Central Sports Stadium. Two powerhouse teams, City Lions and Valley Eagles, will clash in what promises to be an electrifying match.\n\nBoth teams have shown exceptional performance throughout the season. City Lions, led by captain Marcus Johnson, have maintained an undefeated streak in their last 8 matches. Their offensive strategy and quick ball movement have been the key to their success.\n\nValley Eagles, on the other hand, have demonstrated remarkable defensive capabilities. Their goalkeeper, Sarah Martinez, has kept 12 clean sheets this season, making her one of the most reliable players in the league.\n\nThe match is scheduled for Saturday at 3:00 PM, with gates opening at 2:00 PM. Tickets are still available at the stadium box office and online. Expected attendance is over 15,000 spectators.\n\nBoth teams have been training intensively for this final showdown. City Lions coach Mike Thompson stated, "We have prepared extensively for this match. Our players are in peak condition and ready to give their absolute best."\n\nValley Eagles coach Jennifer Adams echoed similar sentiments, "This final represents everything we have worked for this season. We are confident in our abilities and excited to compete at the highest level."',
 'City Lions face Valley Eagles in this weekend\'s championship final at Central Sports Stadium.',
 '/public/assets/images/news/football-championship.jpg',
 'Football',
 '["Championship", "Final", "City Lions", "Valley Eagles", "Local Football"]',
 11, 'published', '2024-09-08 10:30:00', 342),

('Tennis Academy Opens New Training Facility',
 'tennis-academy-opens-new-training-facility',
 'The prestigious Coastal Tennis Academy has officially opened its state-of-the-art training facility, featuring six indoor courts, advanced ball machines, and video analysis technology.\n\nThe new facility spans 25,000 square feet and includes modern amenities such as a fitness center, physiotherapy room, and player lounge. The academy aims to provide world-class training opportunities for players of all skill levels.\n\nDuring the opening ceremony, academy director Robert Chen highlighted the facility\'s commitment to developing tennis talent in the region. "This new facility represents our dedication to excellence in tennis education and training," he said.\n\nThe facility features courts with three different surface types: hard court, clay court, and grass court simulators. This variety allows players to experience and adapt to different playing conditions they might encounter in tournaments.\n\nProfessional coaching staff includes former ATP and WTA players who bring decades of competitive experience. The academy offers programs for juniors, adults, and competitive players looking to improve their rankings.\n\nRegistration for programs is now open, with special introductory rates available for the first month. The academy also plans to host regional tournaments throughout the year.',
 'Coastal Tennis Academy unveils new 25,000 sq ft training facility with six indoor courts and modern amenities.',
 '/public/assets/images/news/tennis-academy.jpg',
 'Tennis',
 '["Tennis", "Academy", "Training", "Facility", "Coaching"]',
 12, 'published', '2024-09-08 08:15:00', 156),

('Basketball League MVP Awards Announced',
 'basketball-league-mvp-awards-announced',
 'The Regional Basketball League has announced its Most Valuable Player awards for the 2024 season, with Thunder Hawks point guard Alex Rodriguez taking home the top honor.\n\nRodriguez averaged 28.5 points, 8.2 assists, and 6.1 rebounds per game throughout the regular season, leading the Thunder Hawks to a league-best 24-6 record. His clutch performances in critical games earned him recognition from coaches and fans alike.\n\n"Alex has been exceptional this season," said Thunder Hawks coach Maria Santos. "His leadership on and off the court has been instrumental in our team\'s success. This award is well-deserved."\n\nThe Defensive Player of the Year award went to Storm Eagles center David Kim, who averaged 2.8 blocks and 11.3 rebounds per game. Kim\'s presence in the paint was a game-changer for the Eagles throughout the season.\n\nRookie of the Year honors were claimed by Lightning Bolts forward Jessica Walsh, who made an immediate impact after joining from college basketball. Walsh averaged 18.2 points per game and was named to the All-Star team.\n\nThe awards ceremony will take place next Friday at the Grand Sports Hall, with all award recipients and their families invited to attend.',
 'Thunder Hawks\' Alex Rodriguez wins Regional Basketball League MVP award for outstanding 2024 season performance.',
 '/public/assets/images/news/basketball-mvp.jpg',
 'Basketball',
 '["Basketball", "MVP", "Awards", "Rodriguez", "Thunder Hawks"]',
 13, 'published', '2024-09-07 16:20:00', 287),

('Swimming Pool Renovation Project Completed',
 'swimming-pool-renovation-project-completed',
 'The city\'s main aquatic center has completed its comprehensive renovation project, reopening with Olympic-standard facilities and enhanced accessibility features.\n\nThe six-month renovation included installation of a new filtration system, LED underwater lighting, and updated starting blocks that meet international competition standards. The project cost $2.3 million and was funded through a combination of city budget allocation and community fundraising efforts.\n\nNew features include a dedicated warm-up pool, expanded seating capacity for 800 spectators, and improved locker room facilities. The center now also offers full accessibility compliance with wheelchair-accessible pool lifts and specialized changing areas.\n\nAquatic center director Lisa Park expressed excitement about the improvements: "These upgrades will allow us to host regional and national swimming competitions while continuing to serve our community swimming programs."\n\nThe renovation also included environmental improvements such as solar heating panels and water recycling systems that will reduce operational costs by an estimated 30%.\n\nSwimming programs for all ages will resume next week, with registration now open online. The center plans to host its first major competition in November with the Regional Masters Swimming Championship.',
 'City aquatic center reopens after $2.3M renovation with Olympic-standard facilities and enhanced accessibility.',
 '/public/assets/images/news/swimming-renovation.jpg',
 'Swimming',
 '["Swimming", "Renovation", "Aquatic Center", "Olympic Standard", "Accessibility"]',
 14, 'published', '2024-09-06 11:45:00', 198),

('Cricket Club Youth Development Program Launch',
 'cricket-club-youth-development-program-launch',
 'The Metropolitan Cricket Club has launched an ambitious youth development program aimed at nurturing the next generation of cricket talent in the region.\n\nThe program will provide structured coaching for players aged 8-18, with specialized training sessions focusing on batting, bowling, fielding, and mental preparation. Professional coaches with international playing experience will lead the sessions.\n\n"We believe in identifying and developing young talent early," said club president James Morrison. "This program will provide pathways for promising players to progress from grassroots level to potential professional careers."\n\nThe program includes weekly training sessions, competitive matches against other clubs, and educational workshops covering nutrition, fitness, and sports psychology. Scholarships are available for talented players who demonstrate financial need.\n\nRegistration for the program is open until the end of the month, with sessions beginning in October. The club has partnered with local schools to identify potential participants and ensure the program reaches deserving young athletes.\n\nThe youth development initiative is part of the club\'s broader community engagement strategy, which also includes coaching clinics in underserved areas and equipment donation programs.',
 'Metropolitan Cricket Club launches comprehensive youth development program for players aged 8-18.',
 '/public/assets/images/news/cricket-youth.jpg',
 'Cricket',
 '["Cricket", "Youth", "Development", "Program", "Coaching"]',
 11, 'published', '2024-09-05 14:30:00', 224),

('Sports Medicine Conference Highlights Latest Research',
 'sports-medicine-conference-highlights-research',
 'The Annual International Sports Medicine Conference concluded yesterday with groundbreaking presentations on injury prevention and athletic performance enhancement.\n\nOver 500 medical professionals, coaches, and researchers gathered to discuss the latest developments in sports science. Key topics included concussion management protocols, rehabilitation techniques for ACL injuries, and nutrition strategies for endurance athletes.\n\nDr. Sarah Williams from the Sports Research Institute presented findings on a new treatment protocol that reduces ACL rehabilitation time by 25%. "Early intervention with specific movement patterns shows remarkable promise in accelerating recovery," she explained.\n\nAnother highlight was research on sleep optimization for athletic performance. Studies showed that athletes following structured sleep protocols improved their performance metrics by an average of 12%.\n\nThe conference also featured workshops on mental health support for athletes, with emphasis on creating supportive environments that prioritize both physical and psychological wellbeing.\n\nNext year\'s conference is scheduled to take place in March, with a focus on youth sports development and long-term athlete health.',
 'International Sports Medicine Conference showcases breakthrough research in injury prevention and performance enhancement.',
 '/public/assets/images/news/sports-medicine.jpg',
 'General Sports',
 '["Sports Medicine", "Research", "Conference", "Injury Prevention", "Performance"]',
 12, 'published', '2024-09-04 09:20:00', 167);

-- Update some existing articles with recent last_viewed_at timestamps
UPDATE news SET last_viewed_at = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 24) HOUR) WHERE views > 500;
UPDATE news SET last_viewed_at = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 7) DAY) WHERE views BETWEEN 200 AND 500;

-- Insert some sample analytics data (optional - for testing)
INSERT IGNORE INTO news_analytics (news_id, session_id, ip_address, time_spent_seconds, scroll_percentage, device_type) 
SELECT 
    id,
    CONCAT('session_', FLOOR(RAND() * 10000)),
    CONCAT('192.168.1.', FLOOR(RAND() * 254) + 1),
    FLOOR(RAND() * 300) + 30,
    ROUND(RAND() * 100, 2),
    CASE FLOOR(RAND() * 3)
        WHEN 0 THEN 'desktop'
        WHEN 1 THEN 'mobile'
        ELSE 'tablet'
    END
FROM news 
WHERE status = 'published' 
LIMIT 20;

COMMIT;