-- GoPlay Shop Database Tables
-- Execute this script in your MySQL database

USE goplay_db;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    category_id INT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    discount_percentage INT DEFAULT 0,
    sku VARCHAR(100) UNIQUE,
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 5,
    weight DECIMAL(8,2),
    dimensions VARCHAR(100),
    brand VARCHAR(100),
    model VARCHAR(100),
    color VARCHAR(50),
    size VARCHAR(50),
    material VARCHAR(100),
    image_url VARCHAR(500),
    gallery JSON,
    features JSON,
    specifications JSON,
    rating DECIMAL(3,2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(255),
    meta_description VARCHAR(500),
    meta_keywords VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_price (price),
    INDEX idx_featured (is_featured),
    INDEX idx_active (is_active),
    INDEX idx_stock (stock_quantity),
    FULLTEXT idx_search (name, description, short_description)
);

-- Create product_reviews table
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    reviewer_name VARCHAR(100),
    reviewer_email VARCHAR(255),
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    review_text TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_rating (rating),
    INDEX idx_approved (is_approved)
);

-- Create product_images table
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
);

-- Insert sample categories
INSERT INTO categories (name, slug, description, icon) VALUES
('Football', 'football', 'Football equipment and accessories', '⚽'),
('Tennis', 'tennis', 'Tennis rackets, balls, and accessories', '🎾'),
('Basketball', 'basketball', 'Basketball equipment and gear', '🏀'),
('Cricket', 'cricket', 'Cricket bats, balls, and accessories', '🏏'),
('Badminton', 'badminton', 'Badminton rackets and shuttlecocks', '🏸'),
('Swimming', 'swimming', 'Swimming gear and accessories', '🏊'),
('Fitness', 'fitness', 'Fitness equipment and accessories', '💪'),
('Running', 'running', 'Running shoes and accessories', '🏃');

-- Insert sample products
INSERT INTO products (
    name, slug, description, short_description, category_id, price, original_price, 
    discount_percentage, sku, stock_quantity, brand, model, color, image_url, 
    features, rating, review_count, is_featured, is_active
) VALUES

-- Football Products
('Professional FIFA Football', 'professional-fifa-football', 
'FIFA approved professional football for matches and training. Made with high-quality synthetic leather for durability and optimal performance.',
'FIFA approved professional football for matches and training',
1, 3500.00, 4000.00, 13, 'FB001', 25, 'Nike', 'Strike Pro', 'White/Black',
'/public/assets/images/products/football-pro.jpg',
'["FIFA Approved", "Synthetic Leather", "Hand Stitched", "All Weather"]',
4.8, 124, TRUE, TRUE),

('Football Training Cones Set', 'football-training-cones-set',
'Complete set of 20 training cones for football practice. Bright colors for visibility and durable plastic construction.',
'Set of 20 training cones for football practice',
1, 1200.00, NULL, 0, 'FB002', 50, 'Adidas', 'Training Pro', 'Orange',
'/public/assets/images/products/training-cones.jpg',
'["20 Piece Set", "Bright Colors", "Stackable", "Weather Resistant"]',
4.5, 89, FALSE, TRUE),

-- Tennis Products
('Carbon Fiber Tennis Racket', 'carbon-fiber-tennis-racket',
'Professional carbon fiber tennis racket with advanced string technology. Perfect for intermediate to advanced players.',
'Professional carbon fiber tennis racket',
2, 15000.00, 18000.00, 17, 'TN001', 15, 'Wilson', 'Pro Staff 97', 'Black/Red',
'/public/assets/images/products/tennis-racket-pro.jpg',
'["Carbon Fiber Frame", "100 sq in Head", "315g Weight", "16x19 String Pattern"]',
4.9, 67, TRUE, TRUE),

('Tennis Ball Pack (3 balls)', 'tennis-ball-pack-3',
'Professional tennis balls approved for tournament play. Pack of 3 balls with excellent bounce and durability.',
'Professional tennis balls - pack of 3',
2, 800.00, NULL, 0, 'TN002', 100, 'Wilson', 'Championship', 'Yellow',
'/public/assets/images/products/tennis-balls.jpg',
'["ITF Approved", "Tournament Grade", "Premium Felt", "Consistent Bounce"]',
4.6, 234, FALSE, TRUE),

-- Basketball Products
('Professional Basketball', 'professional-basketball',
'Official size and weight basketball with superior grip and bounce. Perfect for indoor and outdoor play.',
'Official size basketball with superior grip',
3, 2500.00, 3000.00, 17, 'BB001', 30, 'Spalding', 'NBA Official', 'Orange',
'/public/assets/images/products/basketball-pro.jpg',
'["Official Size 7", "Composite Leather", "Deep Channel Design", "Indoor/Outdoor"]',
4.7, 156, TRUE, TRUE),

('Basketball Shoes', 'basketball-shoes',
'High-performance basketball shoes with superior ankle support and cushioning. Available in multiple sizes.',
'High-performance basketball shoes',
3, 12000.00, 15000.00, 20, 'BB002', 20, 'Nike', 'Air Jordan', 'Black/White',
'/public/assets/images/products/basketball-shoes.jpg',
'["Air Cushioning", "Ankle Support", "Non-slip Sole", "Breathable Upper"]',
4.8, 98, TRUE, TRUE),

-- Cricket Products
('Professional Cricket Bat', 'professional-cricket-bat',
'Premium English willow cricket bat with excellent balance and performance. Hand-crafted for professional players.',
'Premium English willow cricket bat',
4, 8500.00, 10000.00, 15, 'CR001', 12, 'Gray Nicolls', 'Legend', 'Natural',
'/public/assets/images/products/cricket-bat.jpg',
'["English Willow", "Hand Crafted", "Perfect Balance", "Professional Grade"]',
4.6, 73, TRUE, TRUE),

('Cricket Ball Set (6 balls)', 'cricket-ball-set-6',
'Professional cricket balls with authentic leather construction. Set of 6 balls perfect for practice and matches.',
'Professional cricket balls - set of 6',
4, 2400.00, NULL, 0, 'CR002', 40, 'Kookaburra', 'County', 'Red',
'/public/assets/images/products/cricket-balls.jpg',
'["Leather Construction", "Traditional Stitching", "Match Quality", "Excellent Durability"]',
4.5, 45, FALSE, TRUE),

-- Badminton Products
('Badminton Racket Set', 'badminton-racket-set',
'Complete badminton set with 2 rackets and shuttlecocks. Perfect for beginners and recreational players.',
'Complete badminton set with 2 rackets',
5, 5500.00, 6500.00, 15, 'BD001', 25, 'Yonex', 'Arcsaber', 'Blue/White',
'/public/assets/images/products/badminton-set.jpg',
'["2 Rackets Included", "Shuttlecocks Included", "Lightweight Frame", "Carrying Case"]',
4.5, 92, FALSE, TRUE),

('Professional Shuttlecocks (12 pack)', 'professional-shuttlecocks-12',
'Professional grade feather shuttlecocks for tournament play. Pack of 12 for extended play sessions.',
'Professional feather shuttlecocks - pack of 12',
5, 1800.00, NULL, 0, 'BD002', 60, 'Victor', 'Champion', 'White',
'/public/assets/images/products/shuttlecocks.jpg',
'["Feather Construction", "Tournament Grade", "Consistent Flight", "Professional Quality"]',
4.7, 67, FALSE, TRUE),

-- Swimming Products
('Swimming Goggles', 'swimming-goggles',
'Anti-fog swimming goggles with UV protection and adjustable straps. Perfect for pool and open water swimming.',
'Anti-fog swimming goggles with UV protection',
6, 2000.00, 2500.00, 20, 'SW001', 50, 'Speedo', 'Vanquisher', 'Blue',
'/public/assets/images/products/swimming-goggles.jpg',
'["Anti-fog Coating", "UV Protection", "Adjustable Straps", "Comfortable Seal"]',
4.8, 234, TRUE, TRUE),

('Swimming Cap', 'swimming-cap',
'Silicone swimming cap for competitive swimming. Reduces drag and protects hair from chlorine.',
'Silicone swimming cap for competitive swimming',
6, 800.00, NULL, 0, 'SW002', 80, 'TYR', 'Silicone', 'Black',
'/public/assets/images/products/swimming-cap.jpg',
'["100% Silicone", "Hydrodynamic Design", "Chlorine Resistant", "Easy to Put On"]',
4.4, 123, FALSE, TRUE),

-- Fitness Products
('Adjustable Dumbbells Set', 'adjustable-dumbbells-set',
'Complete adjustable dumbbells set for home fitness training. Adjustable weights from 2kg to 24kg per dumbbell.',
'Complete adjustable dumbbells set for home fitness',
7, 7500.00, 9000.00, 17, 'FT001', 15, 'Bowflex', 'SelectTech', 'Black/Red',
'/public/assets/images/products/dumbbells.jpg',
'["Adjustable Weight", "Space Saving", "Quick Change", "Durable Construction"]',
4.7, 167, TRUE, TRUE),

('Yoga Mat Premium', 'yoga-mat-premium',
'Premium non-slip yoga mat for all types of exercise. Eco-friendly materials with superior grip and cushioning.',
'Premium non-slip yoga mat for all exercises',
7, 2500.00, NULL, 0, 'FT002', 40, 'Manduka', 'Pro', 'Purple',
'/public/assets/images/products/yoga-mat.jpg',
'["Non-slip Surface", "Eco-friendly", "6mm Thick", "Lightweight"]',
4.9, 298, FALSE, TRUE),

-- Running Products
('Running Shoes', 'running-shoes',
'Professional running shoes with advanced cushioning and breathable mesh upper. Perfect for long-distance running.',
'Professional running shoes with advanced cushioning',
8, 8500.00, 10000.00, 15, 'RN001', 35, 'Asics', 'Gel-Kayano', 'Blue/White',
'/public/assets/images/products/running-shoes.jpg',
'["Gel Cushioning", "Breathable Mesh", "Stability Support", "Durable Outsole"]',
4.8, 189, TRUE, TRUE),

('Fitness Tracker', 'fitness-tracker',
'Advanced fitness tracker with heart rate monitoring, GPS, and smartphone connectivity. Perfect for athletes.',
'Advanced fitness tracker with heart rate monitoring',
8, 12000.00, 15000.00, 20, 'RN002', 25, 'Garmin', 'Forerunner', 'Black',
'/public/assets/images/products/fitness-tracker.jpg',
'["Heart Rate Monitor", "GPS Tracking", "Smartphone Sync", "Water Resistant"]',
4.6, 145, TRUE, TRUE);