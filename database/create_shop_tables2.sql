-- Fix FK + insert products without FK errors (MySQL 5.7/8.0)

USE goplay_sports_platform;

-- 1) Ensure real categories table exists & seeded
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  icon VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, description, icon) VALUES
('Football','football','Football equipment and accessories','⚽'),
('Tennis','tennis','Tennis rackets, balls, and accessories','🎾'),
('Basketball','basketball','Basketball equipment and gear','🏀'),
('Cricket','cricket','Cricket bats, balls, and accessories','🏏'),
('Badminton','badminton','Badminton rackets and shuttlecocks','🏸'),
('Swimming','swimming','Swimming gear and accessories','🏊'),
('Fitness','fitness','Fitness equipment and accessories','💪'),
('Running','running','Running shoes and accessories','🏃')
ON DUPLICATE KEY UPDATE description=VALUES(description), icon=VALUES(icon);

-- 2) Re-point products.category_id -> categories(id)
-- (The error shows FK name = products_ibfk_1)
ALTER TABLE products DROP FOREIGN KEY products_ibfk_1;

ALTER TABLE products
  ADD CONSTRAINT fk_products_category
  FOREIGN KEY (category_id) REFERENCES categories(id)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- 3) Upsert products resolving category by slug
--    Also set safe defaults for extra NOT NULL columns if your products table has them
INSERT INTO products (
  name, sku, description, category_id, brand,
  price, compare_price, cost_price,
  stock_quantity, min_stock_level,
  weight, dimensions, specifications, features,
  images, tags,
  status, rating, total_reviews, total_sales
) VALUES

-- Football
('Professional FIFA Football','FB001',
 'FIFA approved professional football for matches and training. Made with high-quality synthetic leather for durability and optimal performance.',
 (SELECT id FROM categories WHERE slug='football'),'Nike',
 3500.00, 4000.00, 0.00,
 25, 5,
 0, NULL,
 JSON_OBJECT('material','Synthetic Leather','size','Official Size 5','weight','410g'),
 JSON_ARRAY('FIFA Approved','Synthetic Leather','Hand Stitched','All Weather'),
 NULL, NULL,
 'active', 4.8, 124, 0),

('Football Training Cones Set','FB002',
 'Complete set of 20 training cones for football practice. Bright colors for visibility and durable plastic construction.',
 (SELECT id FROM categories WHERE slug='football'),'Adidas',
 1200.00, NULL, 0.00,
 50, 10,
 0, NULL,
 JSON_OBJECT('quantity','20 pieces','material','Plastic','height','23cm'),
 JSON_ARRAY('20 Piece Set','Bright Colors','Stackable','Weather Resistant'),
 NULL, NULL,
 'active', 4.5, 89, 0),

-- Tennis
('Carbon Fiber Tennis Racket','TN001',
 'Professional carbon fiber tennis racket with advanced string technology. Perfect for intermediate to advanced players.',
 (SELECT id FROM categories WHERE slug='tennis'),'Wilson',
 15000.00, 18000.00, 0.00,
 15, 3,
 0, NULL,
 JSON_OBJECT('head_size','100 sq in','weight','315g','string_pattern','16x19'),
 JSON_ARRAY('Carbon Fiber Frame','100 sq in Head','315g Weight','16x19 String Pattern'),
 NULL, NULL,
 'active', 4.9, 67, 0),

('Tennis Ball Pack (3 balls)','TN002',
 'Professional tennis balls approved for tournament play. Pack of 3 balls with excellent bounce and durability.',
 (SELECT id FROM categories WHERE slug='tennis'),'Wilson',
 800.00, NULL, 0.00,
 100, 20,
 0, NULL,
 JSON_OBJECT('quantity','3 balls','type','Tournament Grade','surface','All Court'),
 JSON_ARRAY('ITF Approved','Tournament Grade','Premium Felt','Consistent Bounce'),
 NULL, NULL,
 'active', 4.6, 234, 0),

-- Basketball
('Professional Basketball','BB001',
 'Official size and weight basketball with superior grip and bounce. Perfect for indoor and outdoor play.',
 (SELECT id FROM categories WHERE slug='basketball'),'Spalding',
 2500.00, 3000.00, 0.00,
 30, 5,
 0, NULL,
 JSON_OBJECT('size','Size 7','material','Composite Leather','use','Indoor/Outdoor'),
 JSON_ARRAY('Official Size 7','Composite Leather','Deep Channel Design','Indoor/Outdoor'),
 NULL, NULL,
 'active', 4.7, 156, 0),

('Basketball Shoes','BB002',
 'High-performance basketball shoes with superior ankle support and cushioning. Available in multiple sizes.',
 (SELECT id FROM categories WHERE slug='basketball'),'Nike',
 12000.00, 15000.00, 0.00,
 20, 3,
 0, NULL,
 JSON_OBJECT('technology','Air Cushioning','support','High-top','sole','Non-slip'),
 JSON_ARRAY('Air Cushioning','Ankle Support','Non-slip Sole','Breathable Upper'),
 NULL, NULL,
 'active', 4.8, 98, 0),

-- Cricket
('Professional Cricket Bat','CR001',
 'Premium English willow cricket bat with excellent balance and performance. Hand-crafted for professional players.',
 (SELECT id FROM categories WHERE slug='cricket'),'Gray Nicolls',
 8500.00, 10000.00, 0.00,
 12, 2,
 0, NULL,
 JSON_OBJECT('wood','English Willow','weight','1.2kg','handle','Cane Handle'),
 JSON_ARRAY('English Willow','Hand Crafted','Perfect Balance','Professional Grade'),
 NULL, NULL,
 'active', 4.6, 73, 0),

('Cricket Ball Set (6 balls)','CR002',
 'Professional cricket balls with authentic leather construction. Set of 6 balls perfect for practice and matches.',
 (SELECT id FROM categories WHERE slug='cricket'),'Kookaburra',
 2400.00, NULL, 0.00,
 40, 10,
 0, NULL,
 JSON_OBJECT('quantity','6 balls','material','Leather','stitching','Traditional'),
 JSON_ARRAY('Leather Construction','Traditional Stitching','Match Quality','Excellent Durability'),
 NULL, NULL,
 'active', 4.5, 45, 0),

-- Badminton
('Badminton Racket Set','BD001',
 'Complete badminton set with 2 rackets and shuttlecocks. Perfect for beginners and recreational players.',
 (SELECT id FROM categories WHERE slug='badminton'),'Yonex',
 5500.00, 6500.00, 0.00,
 25, 5,
 0, NULL,
 JSON_OBJECT('rackets','2 included','weight','Lightweight','grip','Comfortable'),
 JSON_ARRAY('2 Rackets Included','Shuttlecocks Included','Lightweight Frame','Carrying Case'),
 NULL, NULL,
 'active', 4.5, 92, 0),

('Professional Shuttlecocks (12 pack)','BD002',
 'Professional grade feather shuttlecocks for tournament play. Pack of 12 for extended play sessions.',
 (SELECT id FROM categories WHERE slug='badminton'),'Victor',
 1800.00, NULL, 0.00,
 60, 15,
 0, NULL,
 JSON_OBJECT('quantity','12 pieces','material','Feather','grade','Tournament'),
 JSON_ARRAY('Feather Construction','Tournament Grade','Consistent Flight','Professional Quality'),
 NULL, NULL,
 'active', 4.7, 67, 0),

-- Swimming
('Swimming Goggles','SW001',
 'Anti-fog swimming goggles with UV protection and adjustable straps. Perfect for pool and open water swimming.',
 (SELECT id FROM categories WHERE slug='swimming'),'Speedo',
 2000.00, 2500.00, 0.00,
 50, 10,
 0, NULL,
 JSON_OBJECT('protection','UV Protection','feature','Anti-fog','strap','Adjustable'),
 JSON_ARRAY('Anti-fog Coating','UV Protection','Adjustable Straps','Comfortable Seal'),
 NULL, NULL,
 'active', 4.8, 234, 0),

('Swimming Cap','SW002',
 'Silicone swimming cap for competitive swimming. Reduces drag and protects hair from chlorine.',
 (SELECT id FROM categories WHERE slug='swimming'),'TYR',
 800.00, NULL, 0.00,
 80, 20,
 0, NULL,
 JSON_OBJECT('material','100% Silicone','design','Hydrodynamic','resistance','Chlorine Resistant'),
 JSON_ARRAY('100% Silicone','Hydrodynamic Design','Chlorine Resistant','Easy to Put On'),
 NULL, NULL,
 'active', 4.4, 123, 0),

-- Fitness
('Adjustable Dumbbells Set','FT001',
 'Complete adjustable dumbbells set for home fitness training. Adjustable weights from 2kg to 24kg per dumbbell.',
 (SELECT id FROM categories WHERE slug='fitness'),'Bowflex',
 7500.00, 9000.00, 0.00,
 15, 2,
 0, NULL,
 JSON_OBJECT('weight_range','2kg-24kg','adjustment','Quick Change','space','Space Saving'),
 JSON_ARRAY('Adjustable Weight','Space Saving','Quick Change','Durable Construction'),
 NULL, NULL,
 'active', 4.7, 167, 0),

('Yoga Mat Premium','FT002',
 'Premium non-slip yoga mat for all types of exercise. Eco-friendly materials with superior grip and cushioning.',
 (SELECT id FROM categories WHERE slug='fitness'),'Manduka',
 2500.00, NULL, 0.00,
 40, 8,
 0, NULL,
 JSON_OBJECT('thickness','6mm','material','Eco-friendly','surface','Non-slip'),
 JSON_ARRAY('Non-slip Surface','Eco-friendly','6mm Thick','Lightweight'),
 NULL, NULL,
 'active', 4.9, 298, 0),

-- Running
('Running Shoes','RN001',
 'Professional running shoes with advanced cushioning and breathable mesh upper. Perfect for long-distance running.',
 (SELECT id FROM categories WHERE slug='running'),'Asics',
 8500.00, 10000.00, 0.00,
 35, 5,
 0, NULL,
 JSON_OBJECT('technology','Gel Cushioning','upper','Breathable Mesh','support','Stability'),
 JSON_ARRAY('Gel Cushioning','Breathable Mesh','Stability Support','Durable Outsole'),
 NULL, NULL,
 'active', 4.8, 189, 0),

('Fitness Tracker','RN002',
 'Advanced fitness tracker with heart rate monitoring, GPS, and smartphone connectivity. Perfect for athletes.',
 (SELECT id FROM categories WHERE slug='running'),'Garmin',
 12000.00, 15000.00, 0.00,
 25, 3,
 0, NULL,
 JSON_OBJECT('features','Heart Rate + GPS','connectivity','Smartphone Sync','resistance','Water Resistant'),
 JSON_ARRAY('Heart Rate Monitor','GPS Tracking','Smartphone Sync','Water Resistant'),
 NULL, NULL,
 'active', 4.6, 145, 0)
ON DUPLICATE KEY UPDATE
  description    = VALUES(description),
  price          = VALUES(price),
  compare_price  = VALUES(compare_price),
  cost_price     = VALUES(cost_price),
  stock_quantity = VALUES(stock_quantity),
  min_stock_level= VALUES(min_stock_level),
  weight         = VALUES(weight),
  dimensions     = VALUES(dimensions),
  specifications = VALUES(specifications),
  features       = VALUES(features),
  images         = VALUES(images),
  tags           = VALUES(tags),
  status         = VALUES(status),
  rating         = VALUES(rating),
  total_reviews  = VALUES(total_reviews),
  total_sales    = VALUES(total_sales);

-- 4) Create product_reviews table
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL,
    title VARCHAR(200),
    review_text TEXT,
    images JSON,
    is_verified_purchase TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_user (user_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB;

-- 5) Create product_images table
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_primary (is_primary)
) ENGINE=InnoDB;

-- 6) Insert sample product reviews (using actual user IDs from database)
INSERT INTO product_reviews (product_id, user_id, rating, title, review_text, is_verified_purchase) VALUES
-- Football reviews (user IDs 21-25 are regular users)
((SELECT id FROM products WHERE sku='FB001'), 21, 5, 'Excellent Quality Football', 'Great quality football, perfect for our local matches. Highly recommended! Using it for 3 months now.', 1),
((SELECT id FROM products WHERE sku='FB001'), 22, 4, 'Good but pricey', 'Nice football but a bit expensive. Overall satisfied with the purchase. Good for practice sessions.', 1),
((SELECT id FROM products WHERE sku='FB002'), 23, 4, 'Great training equipment', 'These cones are very useful for football training. Bright colors make them easy to spot.', 1),

-- Tennis reviews
((SELECT id FROM products WHERE sku='TN001'), 21, 5, 'Best racket ever!', 'This racket has improved my game significantly. Perfect weight and balance. Worth every rupee!', 1),
((SELECT id FROM products WHERE sku='TN002'), 24, 5, 'Professional quality balls', 'Excellent bounce and durability. Perfect for tournament play. Highly recommend for serious players.', 1),

-- Basketball reviews
((SELECT id FROM products WHERE sku='BB001'), 23, 5, 'Perfect basketball', 'Great bounce and grip. Using it for 6 months now, still in excellent condition. Perfect for outdoor courts.', 1),
((SELECT id FROM products WHERE sku='BB002'), 25, 4, 'Comfortable shoes', 'Good basketball shoes with excellent grip. A bit expensive but worth the investment for serious players.', 1),

-- Cricket reviews
((SELECT id FROM products WHERE sku='CR001'), 24, 4, 'Great cricket bat', 'Nice willow bat with good balance. Perfect for club level cricket. Recommended for intermediate players.', 1),
((SELECT id FROM products WHERE sku='CR002'), 21, 5, 'Quality cricket balls', 'Excellent leather balls with traditional stitching. Perfect for practice and match play.', 1),

-- Badminton reviews
((SELECT id FROM products WHERE sku='BD001'), 22, 4, 'Good starter set', 'Perfect set for beginners. Rackets are lightweight and shuttlecocks included. Great value for money.', 1),
((SELECT id FROM products WHERE sku='BD002'), 25, 5, 'Tournament quality', 'Professional grade shuttlecocks with consistent flight. Perfect for competitive play.', 1)

ON DUPLICATE KEY UPDATE
    rating = VALUES(rating),
    title = VALUES(title),
    review_text = VALUES(review_text),
    is_verified_purchase = VALUES(is_verified_purchase);

-- 7) Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_status ON products(status);
CREATE INDEX IF NOT EXISTS idx_products_price ON products(price);
CREATE INDEX IF NOT EXISTS idx_products_rating ON products(rating);
CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories(slug);

-- 8) Quick sanity check (optional)
SELECT p.sku, c.slug AS category_slug
FROM products p JOIN categories c ON c.id=p.category_id
ORDER BY p.sku;

-- Show success message
SELECT 'Shop tables and data created successfully! All tables including reviews and images are ready.' as message;