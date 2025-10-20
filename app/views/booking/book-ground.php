<?php
$title = 'Book Ground - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/book-ground.css'];
$additionalJS = ['/public/js/pages/book-ground.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
   
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
                    <div id="map" style="width: 100%; height: 400px; border-radius: 8px;"></div>
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
                                        <?php
                                        // Get appropriate ground image based on sport category
                                        $categoryName = strtolower($facility['category_name'] ?? 'general');
                                        $groundImages = [
                                            'football' => '/public/assets/images/grounds/football-ground.jpg',
                                            'cricket' => '/public/assets/images/grounds/cricket-ground.jpg',
                                            'tennis' => '/public/assets/images/grounds/tennis-court.jpg',
                                            'basketball' => '/public/assets/images/grounds/basketball-court.jpg',
                                            'badminton' => '/public/assets/images/grounds/badminton-court.jpg',
                                            'volleyball' => '/public/assets/images/grounds/football-ground.jpg',
                                            'swimming' => '/public/assets/images/grounds/football-ground.jpg'
                                        ];
                                        $imageUrl = $groundImages[$categoryName] ?? '/public/assets/images/ground.jpeg';
                                        ?>
                                        <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="facility-icon" style="display: none;">
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

        

<script async 
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3qKhJG9ulG0vgu9KxaG0NPXADLGxMr7k&callback=initMap&libraries=&v=weekly"
    onerror="handleMapLoadError()">
</script>

<script>
    // Fallback function if Google Maps fails to load
    function handleMapLoadError() {
        console.error('Failed to load Google Maps API');
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; color: #6c757d; text-align: center; padding: 2rem;">
                    <div>
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>Unable to load map. Please check your internet connection.</p>
                    </div>
                </div>
            `;
        }
    }
    
    // Ensure initMap exists even if not loaded
    window.initMap = window.initMap || function() {
        console.log('initMap fallback called');
    };

    

<script src="/public/js/pages/book-ground.js"></script>