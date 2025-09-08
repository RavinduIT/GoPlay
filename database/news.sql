-- Add this to your schema.sql file after the existing tables

-- ======================
-- NEWS SYSTEM
-- ======================

-- News Categories Table
CREATE TABLE news_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#3B82F6',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default News Categories
INSERT INTO news_categories (name, slug, description, color) VALUES
('Cricket', 'cricket', 'Cricket news and updates', '#10B981'),
('Football', 'football', 'Football news and match reports', '#F59E0B'),
('Basketball', 'basketball', 'Basketball games and tournaments', '#8B5CF6'),
('Tennis', 'tennis', 'Tennis tournaments and player news', '#EF4444'),
('Swimming', 'swimming', 'Swimming competitions and records', '#06B6D4'),
('General Sports', 'general-sports', 'General sports news and updates', '#6B7280');

-- News Table
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category VARCHAR(100) DEFAULT 'General Sports',
    tags JSON,
    author_id INT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    views INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- News Comments Table
CREATE TABLE news_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    news_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT NULL,
    comment TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES news_comments(id) ON DELETE CASCADE
);

-- News Indexes for Performance
CREATE INDEX idx_news_slug ON news(slug);
CREATE INDEX idx_news_category ON news(category);
CREATE INDEX idx_news_status ON news(status);
CREATE INDEX idx_news_published ON news(published_at);
CREATE INDEX idx_news_author ON news(author_id);
CREATE INDEX idx_news_views ON news(views);

-- Insert Sample News Data
INSERT INTO news (title, slug, content, excerpt, featured_image, category, tags, author_id, status, published_at, views) VALUES

('Sri Lankan Cricket Team Wins Asia Cup 2024', 
 'sri-lankan-cricket-team-wins-asia-cup-2024',
 'In a thrilling final at the Dubai International Cricket Stadium, the Sri Lankan cricket team defeated India by 23 runs to claim the Asia Cup 2024 title. The victory comes after a spectacular batting performance led by captain Dasun Shanaka, who scored an unbeaten 89 runs in the final.\n\nThe match began with Sri Lanka winning the toss and electing to bat first. The opening partnership between Pathum Nissanka and Kusal Mendis laid a solid foundation, adding 76 runs for the first wicket. Nissanka played a composed innings of 45 runs before being dismissed by Jasprit Bumrah.\n\nThe middle order, anchored by Shanaka and supported by Charith Asalanka (67 not out), propelled Sri Lanka to a commanding total of 314/6 in their allotted 50 overs. The innings was marked by aggressive stroke play and intelligent running between the wickets.\n\nIndia''s chase got off to a shaky start, losing both openers within the first 10 overs. Despite a valiant effort by Virat Kohli (78) and KL Rahul (65), the Indian team fell short of the target, finishing at 291/8.\n\nThe Sri Lankan bowling attack, led by Wanindu Hasaranga (3/54) and Maheesh Theekshana (2/48), maintained consistent pressure throughout India''s innings. The victory marks Sri Lanka''s first Asia Cup title since 2014.',
 'Sri Lankan cricket team defeats India by 23 runs to claim Asia Cup 2024 title in a thrilling final at Dubai.',
 '/public/assets/images/news/cricket-asia-cup.jpg',
 'Cricket',
 '["Asia Cup", "Sri Lanka", "Cricket", "Final", "Victory"]',
 11, 'published', '2024-09-07 14:30:00', 1250),

('Premier League Football Season Kicks Off with Spectacular Matches',
 'premier-league-football-season-kicks-off',
 'The 2024-25 Premier League season has begun with a bang, featuring some of the most exciting opening weekend matches in recent memory. Manchester City started their title defense with a convincing 3-0 victory over Chelsea at Stamford Bridge, while Arsenal showcased their attacking prowess with a 4-1 win against Newcastle United.\n\nCity''s dominance was evident from the first whistle, with Erling Haaland opening the scoring in the 15th minute with a trademark finish. The Norwegian striker''s partnership with Kevin De Bruyne continues to flourish, as evidenced by the Belgian''s assist for the opening goal.\n\nPhil Foden doubled City''s lead just before halftime with a stunning curled effort from outside the box. The young midfielder''s development under Pep Guardiola has been remarkable, and he looks set for another outstanding season.\n\nArsenal''s victory over Newcastle was equally impressive, with new signing Kai Havertz scoring twice on his debut. The German forward''s movement in the box caused constant problems for the Newcastle defense, and his clinical finishing proved the difference.\n\nOther notable results from the opening weekend include Liverpool''s narrow 2-1 victory over Brighton, Tottenham''s dramatic 3-2 win against Brentford, and Manchester United''s solid 1-0 triumph over Wolves.\n\nThe early evidence suggests this could be one of the most competitive Premier League seasons in years, with several teams making significant improvements to their squads during the summer transfer window.',
 'The 2024-25 Premier League season opens with thrilling matches as Manchester City and Arsenal secure convincing victories.',
 '/public/assets/images/news/premier-league.jpg',
 'Football',
 '["Premier League", "Football", "Manchester City", "Arsenal", "Opening Weekend"]',
 11, 'published', '2024-09-06 16:45:00', 890),

('Tennis Grand Slam Update: US Open Semifinals Set',
 'tennis-us-open-semifinals-set',
 'The US Open tennis tournament has reached the semifinal stage with some surprising results and outstanding performances from both established stars and rising talents. The men''s semifinals will feature defending champion Novak Djokovic against young sensation Carlos Alcaraz, while Daniil Medvedev faces off against Stefanos Tsitsipas.\n\nDjokovic''s path to the semifinals hasn''t been without challenges. The Serbian star had to dig deep in his quarterfinal match against Alexander Zverev, winning in four sets after dropping the second set. His experience and mental fortitude were crucial in the decisive moments.\n\nAlcaraz, the 20-year-old Spanish phenom, continued his impressive form with a straight-sets victory over Jannik Sinner in the quarterfinals. The young player''s combination of power, speed, and court craft has been mesmerizing fans throughout the tournament.\n\nOn the women''s side, the semifinals will showcase defending champion Coco Gauff against former world number one Aryna Sabalenka, while Iga Swiatek takes on Madison Keys in what promises to be an exciting American showdown.\n\nGauff''s journey to the semifinals has been particularly impressive, as she hasn''t dropped a set throughout the tournament. The young American''s serving has improved significantly since last year, and her confidence on home soil is evident.\n\nSwiatek, the current world number one, faced a tough challenge in her quarterfinal against Jessica Pegula but managed to secure victory in three sets. The Polish player''s consistency and mental toughness continue to set her apart from the field.\n\nThe semifinals promise to deliver high-quality tennis, with each match potentially going the distance. The tournament has already provided numerous memorable moments and the final weekend looks set to add to that legacy.',
 'US Open tennis semifinals are set with exciting matchups featuring Djokovic, Alcaraz, Gauff, and Swiatek.',
 '/public/assets/images/news/us-open-tennis.jpg',
 'Tennis',
 '["Tennis", "US Open", "Djokovic", "Alcaraz", "Gauff", "Semifinals"]',
 12, 'published', '2024-09-05 12:20:00', 756),

('Basketball World Championship Finals Preview',
 'basketball-world-championship-finals-preview',
 'The FIBA Basketball World Championship reaches its climax this weekend as the United States faces Serbia in what promises to be an epic final. Both teams have shown exceptional form throughout the tournament, setting up a mouth-watering conclusion to three weeks of outstanding basketball.\n\nTeam USA, led by Anthony Edwards and Paolo Banchero, has been dominant throughout the competition. Their victory over Germany in the semifinals showcased the depth and talent in their roster, with contributions coming from multiple players at crucial moments.\n\nEdwards, in particular, has been a revelation in this tournament. The Minnesota Timberwolves guard has averaged 24.5 points per game while shooting an impressive 47% from three-point range. His explosive athleticism and improved decision-making have made him the standout performer for the Americans.\n\nSerbia''s path to the final has been equally impressive. The team, anchored by Denver Nuggets superstar Nikola Jokic, defeated Spain 95-86 in their semifinal. Jokic''s unique skill set and basketball IQ have been instrumental in Serbia''s success, averaging a near triple-double throughout the tournament.\n\nThe supporting cast around Jokic has been exceptional, with Bogdan Bogdanovic providing crucial scoring from the perimeter and Aleksa Avramovic orchestrating the offense with precision. The team''s chemistry and understanding of international basketball rules have given them an edge against more talented opponents.\n\nThe final promises to be a fascinating tactical battle between two contrasting styles. USA''s athleticism and individual talent will be pitted against Serbia''s team cohesion and tactical discipline.\n\nHistory suggests this could be one of the closest World Championship finals in recent memory, with both teams possessing the firepower to claim the title.',
 'USA and Serbia set for epic FIBA Basketball World Championship final showdown this weekend.',
 '/public/assets/images/news/basketball-world-championship.jpg',
 'Basketball',
 '["Basketball", "World Championship", "USA", "Serbia", "Final", "FIBA"]',
 13, 'published', '2024-09-04 18:15:00', 623),

('Swimming World Records Broken at Commonwealth Games',
 'swimming-world-records-commonwealth-games',
 'The Commonwealth Games swimming competition has witnessed history in the making, with three world records broken on a single extraordinary evening at the Sandwell Aquatics Centre. The performances have elevated the profile of the Games and showcased the incredible depth of swimming talent across the Commonwealth nations.\n\nThe evening began with Australia''s Emma McKeon breaking the women''s 100m freestyle world record, clocking an incredible 51.71 seconds. McKeon''s performance was a masterclass in sprint swimming, with perfect technique and an explosive finish that left the crowd in stunned silence before erupting in celebration.\n\nCanada''s Summer McIntosh followed with another world record in the women''s 400m individual medley, touching the wall in 4:24.38. The 17-year-old''s performance was remarkable for its consistency across all four strokes, demonstrating the complete skillset that has made her one of swimming''s brightest prospects.\n\nThe third world record came in the men''s 4x100m freestyle relay, where the Australian team of Kyle Chalmers, Zac Stubblety-Cook, Matthew Temple, and Elijah Winnington combined for a time of 3:08.24. The relay was a showcase of Australian swimming depth, with each swimmer posting split times that would have been competitive in individual events.\n\nEngland''s Adam Peaty, competing in his home nation, delivered one of the most emotionally charged performances of the Games. The breaststroke specialist won gold in the 100m breaststroke, his first major title since returning from mental health struggles. His victory lap was met with thunderous applause from the packed venue.\n\nThe swimming competition has already exceeded expectations for both performance levels and attendance, with many sessions sold out. The Commonwealth Games continue to prove their importance as a stepping stone for swimmers preparing for the Paris Olympics next year.',
 'Three world records fall on extraordinary night at Commonwealth Games swimming competition.',
 '/public/assets/images/news/swimming-commonwealth.jpg',
 'Swimming',
 '["Swimming", "Commonwealth Games", "World Records", "McKeon", "McIntosh"]',
 14, 'published', '2024-09-03 20:45:00', 445);

COMMIT;