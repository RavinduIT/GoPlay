<?php 
$title = 'Book Ground - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/book-ground.css'];
$additionalJS = ['/public/js/pages/book-ground.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Professional Book Ground Page Styles */
        :root {
            --primary-color: #28a745;
            --primary-dark: #218838;
            --primary-light: #d4edda;
            --secondary-color: #20c997;
            --accent-color: #007bff;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-light: #adb5bd;
            --background-light: #f8f9fa;
            --background-white: #ffffff;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-heavy: 0 8px 32px rgba(0,0,0,0.16);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
        }

        .book-ground-container {
            min-height: 100vh;
            background: var(--background-light);
        }

        /* Professional Header Section */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><path d="M0,10 Q25,0 50,10 T100,10 V20 H0 Z" fill="rgba(255,255,255,0.1)"/></svg>') repeat-x;
            background-size: 200px 20px;
            animation: wave 20s linear infinite;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-200px); }
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-text h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .header-text p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .header-stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-primary, .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--background-white);
            color: var(--primary-color);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Search Section */
        .search-section {
            background: var(--background-white);
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .search-bar {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 1rem;
            align-items: center;
        }

        .search-input-group {
            position: relative;
        }

        .search-input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .filter-group {
            display: flex;
            gap: 1rem;
        }

        .filter-select {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .search-btn {
            padding: 1rem 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Main Layout */
        .main-layout {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .content-area {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }

        .results-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .results-subtitle {
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .sort-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
            cursor: pointer;
        }

        /* Facilities Grid */
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .facility-card {
            background: var(--background-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
        }

        .facility-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .facility-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            overflow: hidden;
        }

        .facility-icon {
            font-size: 4rem;
            opacity: 0.8;
        }

        .facility-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.95);
            color: var(--warning-color);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .facility-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .facility-card:hover .facility-overlay {
            opacity: 1;
        }

        .view-on-map-btn {
            background: var(--background-white);
            color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .view-on-map-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .facility-content {
            padding: 2rem;
        }

        .facility-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .facility-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .facility-price {
            text-align: right;
        }

        .price-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-period {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .facility-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .facility-sports {
            margin-bottom: 1rem;
        }

        .sport-tag {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .facility-features {
            margin-bottom: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .feature-item i {
            color: var(--primary-color);
            width: 16px;
        }

        .more-features {
            color: var(--text-light);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .facility-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .facility-rating {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .stars i {
            color: var(--warning-color);
            font-size: 0.9rem;
        }

        .rating-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .facility-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-outline {
            padding: 0.5rem 1rem;
            border: 2px solid var(--primary-color);
            background: transparent;
            color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .facility-actions .btn-primary {
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .facility-actions .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Loading and Empty States */
        .loading-state, .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .loading-spinner {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        /* Map Sidebar */
        .map-sidebar {
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            overflow: hidden;
            transition: var(--transition);
        }

        .map-sidebar.hidden {
            display: none;
        }

        .sidebar-header {
            padding: 1.5rem;
            background: var(--primary-color);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-map-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition);
        }

        .close-map-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .map-container {
            height: 400px;
            background: var(--background-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }

            .header-stats {
                justify-content: center;
            }

            .search-bar {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .filter-group {
                flex-direction: column;
            }

            .facilities-grid {
                grid-template-columns: 1fr;
            }

            .results-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .main-layout {
                padding: 1rem;
            }

            .search-container {
                padding: 1rem;
            }

            .facility-header {
                flex-direction: column;
                gap: 1rem;
            }

            .facility-actions {
                flex-direction: column;
            }
        }
    </style>
<div class="book-ground-container">

        <!-- Professional Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1>Book Sports Facilities</h1>
                    <p>Find and reserve premium sports facilities across Sri Lanka</p>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Facilities</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Booking</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">5★</span>
                            <span class="stat-label">Rated</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn-primary" onclick="getCurrentLocation()">
                        <i class="fas fa-location-arrow"></i>
                        Find Near Me
                    </button>
                    <button class="btn-secondary" onclick="toggleMapView()">
                        <i class="fas fa-map"></i>
                        <span id="map-toggle-text">Show Map</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-bar">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-grounds" placeholder="Search facilities by name, sport, or location..." class="search-input">
                    </div>
                    <div class="filter-group">
                        <select id="location" class="filter-select">
                            <option value="">All Locations</option>
                            <?php if (isset($cities) && is_array($cities)): ?>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?php echo htmlspecialchars($city); ?>"
                                        <?php echo (isset($currentFilters['city']) && $currentFilters['city'] === $city) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($city); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <select id="sport-type" class="filter-select">
                            <option value="">All Sports</option>
                            <?php if (isset($categories) && is_array($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                        <?php echo (isset($currentFilters['sport_category']) && $currentFilters['sport_category'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <select id="price-range" class="filter-select">
                            <option value="">Any Price</option>
                            <option value="0-1000">LKR 0 - 1,000</option>
                            <option value="1000-2000">LKR 1,000 - 2,000</option>
                            <option value="2000+">LKR 2,000+</option>
                        </select>
                    </div>
                    <button class="search-btn" onclick="searchFacilities()">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="main-layout">
            <!-- Sidebar with Map (Initially Hidden) -->
            <div id="map-sidebar" class="map-sidebar hidden">
                <div class="sidebar-header">
                    <h3>Facility Locations</h3>
                    <button class="close-map-btn" onclick="toggleMapView()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="map-container">
                    <div>
                        <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                        <p>Interactive map coming soon</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="content-area">
                <!-- Results Header -->
                <div class="results-header">
                    <div class="results-info">
                        <h2 id="results-count">
                            <?php 
                            if (isset($facilities) && is_array($facilities)) {
                                $count = count($facilities);
                                echo "{$count} Premium Facilit" . ($count === 1 ? 'y' : 'ies') . " Available";
                            } else {
                                echo "Loading Facilities...";
                            }
                            ?>
                        </h2>
                        <p class="results-subtitle">Choose from our top-rated sports facilities</p>
                    </div>
                    <div class="sort-controls">
                        <select id="sort-by" class="sort-select" onchange="sortFacilities()">
                            <option value="rating">Sort by Rating</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <!-- Facilities Grid -->
                <div class="facilities-container">
                    <div class="facilities-grid" id="facilities-grid">
                        <?php if (isset($error)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <h3>Error Loading Facilities</h3>
                                <p><?php echo htmlspecialchars($error); ?></p>
                                <button class="btn-primary" onclick="location.reload()">Retry</button>
                            </div>
                        <?php elseif (empty($facilities)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h3>No facilities found</h3>
                                <p>No sports facilities are currently available. Please check back later.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($facilities as $facility): ?>
                                <div class="facility-card" data-ground-id="<?php echo $facility['id']; ?>">
                                    <div class="facility-image">
                                        <div class="facility-icon">
                                            <i class="<?php echo $facility['category_icon'] ?? 'fas fa-sports'; ?>"></i>
                                        </div>
                                        <div class="facility-badge">
                                            <i class="fas fa-star"></i>
                                            <?php echo number_format($facility['rating'] ?? 0, 1); ?>
                                        </div>
                                        <div class="facility-overlay">
                                            <button class="view-on-map-btn" onclick="viewOnMap(<?php echo $facility['id']; ?>)">
                                                <i class="fas fa-map-marker-alt"></i>
                                                View on Map
                                            </button>
                                        </div>
                                    </div>
                                    <div class="facility-content">
                                        <div class="facility-header">
                                            <h3 class="facility-name"><?php echo htmlspecialchars($facility['name']); ?></h3>
                                            <div class="facility-price">
                                                <div class="price-amount">LKR <?php echo number_format($facility['hourly_rate']); ?></div>
                                                <div class="price-period">per hour</div>
                                            </div>
                                        </div>
                                        
                                        <div class="facility-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($facility['city']); ?>
                                        </div>
                                        
                                        <div class="facility-sports">
                                            <span class="sport-tag"><?php echo htmlspecialchars($facility['category_name'] ?? 'Sports'); ?></span>
                                        </div>
                                        
                                        <div class="facility-features">
                                            <?php if (!empty($facility['amenities'])): ?>
                                                <?php 
                                                $amenities = is_string($facility['amenities']) ? json_decode($facility['amenities'], true) : $facility['amenities'];
                                                $amenities = $amenities ?? [];
                                                $displayAmenities = array_slice($amenities, 0, 3);
                                                ?>
                                                <?php foreach ($displayAmenities as $amenity): ?>
                                                    <div class="feature-item">
                                                        <i class="fas fa-check"></i>
                                                        <?php echo ucfirst(str_replace('_', ' ', $amenity)); ?>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($amenities) > 3): ?>
                                                    <div class="more-features">+<?php echo count($amenities) - 3; ?> more features</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="facility-footer">
                                            <div class="facility-rating">
                                                <div class="stars">
                                                    <?php 
                                                    $rating = $facility['rating'] ?? 0;
                                                    $fullStars = floor($rating);
                                                    $hasHalfStar = $rating - $fullStars >= 0.5;
                                                    
                                                    for ($i = 0; $i < $fullStars; $i++) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    }
                                                    if ($hasHalfStar) {
                                                        echo '<i class="fas fa-star-half-alt"></i>';
                                                    }
                                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                    for ($i = 0; $i < $emptyStars; $i++) {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <span class="rating-text">(<?php echo $facility['total_reviews'] ?? 0; ?> reviews)</span>
                                            </div>
                                            
                                            <div class="facility-actions">
                                                <button class="btn-outline" onclick="viewDetails(<?php echo $facility['id']; ?>)">
                                                    View Details
                                                </button>
                                                <button class="btn-primary" onclick="bookNow(<?php echo $facility['id']; ?>)">
                                                    Book Now
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

</div>

<script src="/public/js/pages/book-ground.js"></script>