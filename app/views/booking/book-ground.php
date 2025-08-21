<?php 
$title = 'Book Ground - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/book-ground.css'];
$additionalJS = ['/public/js/pages/book-ground.js'];
?>
<link rel="stylesheet" href="/public/css/pages/book-ground.css">
    <div class="book-ground-container">
        <!-- Navbar Component -->
        <div id="navbar-container"></div>

        <!-- Professional Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1>Book Sports Facilities</h1>
                    <p>Find and reserve premium sports facilities across Colombo</p>
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

        <!--  Search Bar -->
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
                            <option value="colombo-3">Colombo 3</option>
                            <option value="colombo-4">Colombo 4</option>
                            <option value="colombo-5">Colombo 5</option>
                            <option value="colombo-6">Colombo 6</option>
                            <option value="colombo-7">Colombo 7</option>
                        </select>
                        <select id="sport-type" class="filter-select">
                            <option value="">All Sports</option>
                            <option value="football">Football</option>
                            <option value="cricket">Cricket</option>
                            <option value="tennis">Tennis</option>
                            <option value="basketball">Basketball</option>
                            <option value="badminton">Badminton</option>
                        </select>
                        <select id="price-range" class="filter-select">
                            <option value="">Any Price</option>
                            <option value="0-30">$0 - $30/hour</option>
                            <option value="30-50">$30 - $50/hour</option>
                            <option value="50+">$50+/hour</option>
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
                    <div id="facilities-map"></div>
                </div>
                <div class="map-controls">
                    <button class="map-control-btn" onclick="fitMapToMarkers()">
                        <i class="fas fa-expand-arrows-alt"></i>
                        Fit All
                    </button>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="content-area">
                <!-- Results Header -->
                <div class="results-header">
                    <div class="results-info">
                        <h2 id="results-count">8 Premium Facilities Available</h2>
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
                        <div class="loading-state">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <p>Loading facilities...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Component -->
        <div id="footer-container"></div>
    </div>

    <!-- Load Google Maps configuration and components -->
    <script src="../js/config/maps-config.js"></script>
    <script src="../js/components/google-map.js"></script>
    <script src="../js/components/navbar.js"></script>
    <script src="../js/data-loader.js"></script>

    <script src="/public/js/pages/book-ground.js"></script>

