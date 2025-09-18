<?php 
$title = 'Ground Details - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];

// Get ground ID from URL parameter
$groundId = $_GET['id'] ?? 1;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* CSS Custom Properties */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #64748b;
            --primary-light: #f1f5f9;
            --secondary-color: #0891b2;
            --accent-color: #ffc107;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-light: #adb5bd;
            --text-white: #ffffff;
            --background-white: #ffffff;
            --background-light: #f8f9fa;
            --background-dark: #343a40;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-heavy: 0 8px 32px rgba(0,0,0,0.16);
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --border-radius-xl: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--background-white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Loading State */
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            flex-direction: column;
            gap: 1rem;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 1rem 0;
            background: var(--background-light);
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--primary-color);
        }

        .breadcrumb .current {
            color: var(--text-primary);
            font-weight: 600;
        }

        /* Ground Details Container */
        .ground-details {
            padding: 2rem 0;
        }

        /* Ground Header */
        .ground-header {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .ground-gallery {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 400px;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-medium);
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .main-image:hover img {
            transform: scale(1.05);
        }

        /* Ground Info */
        .ground-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .ground-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .ground-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .star {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .rating-text {
            font-weight: 600;
            color: var(--text-primary);
        }

        .reviews {
            color: var(--text-secondary);
        }

        .price-section {
            background: var(--primary-light);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }

        .price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-unit {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .book-button {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-white);
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .book-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* Ground Content */
        .ground-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .content-section {
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--background-light);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .feature-item:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: var(--text-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .feature-text {
            font-weight: 500;
            color: var(--text-primary);
        }

        /* Availability Section */
        .availability-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .day-slot {
            text-align: center;
            padding: 1rem 0.5rem;
            background: var(--background-light);
            border-radius: var(--border-radius);
            border: 2px solid transparent;
            transition: var(--transition);
            cursor: pointer;
        }

        .day-slot:hover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .day-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .day-hours {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-section {
            background: var(--background-white);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
        }

        .map-container {
            height: 300px;
            background: var(--background-light);
            border-radius: var(--border-radius);
            position: relative;
            overflow: hidden;
        }

        .map-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: var(--border-radius);
        }

        .map-loading-content {
            text-align: center;
            padding: 2rem;
        }

        .map-loading-spinner {
            margin-bottom: 1rem;
        }

        .spinner-ring {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        .map-loading-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .contact-icon {
            width: 35px;
            height: 35px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .contact-text {
            color: var(--text-secondary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .ground-header,
            .ground-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .ground-title {
                font-size: 2rem;
            }

            .main-image {
                height: 300px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .availability-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .ground-title {
                font-size: 1.5rem;
            }

            .price {
                font-size: 1.5rem;
            }

            .content-section,
            .sidebar-section {
                padding: 1.5rem;
            }
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .back-button:hover {
            color: var(--primary-color);
            gap: 0.75rem;
        }

        .error-container {
            text-align: center;
            padding: 3rem 0;
        }

        .error-title {
            font-size: 2rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .error-message {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
    </style>
<!-- Ground Details Content -->

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="/">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="/book-ground">Book Ground</a>
                <i class="fas fa-chevron-right"></i>
                <span class="current" id="breadcrumb-current">Ground Details</span>
            </nav>
        </div>
    </div>

    <!-- Back Button -->
    <div class="container">
        <a href="/book-ground" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Search
        </a>
    </div>

    <!-- Loading State -->
    <div id="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Loading ground details...</p>
    </div>

    <!-- Error State -->
    <div id="error-state" class="container" style="display: none;">
        <div class="error-container">
            <h2 class="error-title">Ground Not Found</h2>
            <p class="error-message">The requested ground could not be found.</p>
            <a href="/book-ground" class="book-button" style="width: auto;">
                <i class="fas fa-search"></i>
                Browse All Grounds
            </a>
        </div>
    </div>

    <!-- Ground Details Content -->
    <div id="ground-content" class="container ground-details" style="display: none;">
        <!-- Ground Header -->
        <div class="ground-header">
            <div class="ground-gallery">
                <div class="main-image">
                    <img id="ground-image" src="" alt="Ground Image">
                </div>
            </div>
            
            <div class="ground-info">
                <h1 id="ground-title" class="ground-title"></h1>
                <div class="ground-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="ground-location"></span>
                </div>
                
                <div class="rating-section">
                    <div class="rating">
                        <div id="ground-stars" class="stars"></div>
                        <span id="ground-rating" class="rating-text"></span>
                    </div>
                    <span id="ground-reviews" class="reviews"></span>
                </div>
                
                <div class="price-section">
                    <div class="price">
                        LKR <span id="ground-price"></span>
                        <span class="price-unit">per hour</span>
                    </div>
                </div>
                
                <button class="book-button" onclick="bookGround()">
                    <i class="fas fa-calendar-check"></i>
                    Book Now
                </button>
            </div>
        </div>

        <!-- Ground Content -->
        <div class="ground-content">
            <div class="main-content">
                <!-- Description -->
                <div class="content-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Description
                    </h3>
                    <p id="ground-description"></p>
                </div>

                <!-- Features -->
                <div class="content-section">
                    <h3 class="section-title">
                        <i class="fas fa-star"></i>
                        Features & Amenities
                    </h3>
                    <div id="ground-features" class="features-grid"></div>
                </div>

                <!-- Availability -->
                <div class="content-section">
                    <h3 class="section-title">
                        <i class="fas fa-clock"></i>
                        Availability
                    </h3>
                    <div id="ground-availability" class="availability-grid"></div>
                </div>
            </div>

            <div class="sidebar">
                <!-- Map -->
                <div class="sidebar-section">
                    <h3 class="section-title">
                        <i class="fas fa-map"></i>
                        Location
                    </h3>
                    <div class="map-container">
                        <div id="ground-map" style="width: 100%; height: 100%; border-radius: 8px;"></div>
                        <div id="map-loading-overlay" class="map-loading-overlay">
                            <div class="map-loading-content">
                                <div class="map-loading-spinner">
                                    <div class="spinner-ring"></div>
                                </div>
                                <p class="map-loading-text">Loading map...</p>
                            </div>
                        </div>
                    </div>
                    <div class="location-info">
                        <div class="ground-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="sidebar-location">Loading location...</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="sidebar-section">
                    <h3 class="section-title">
                        <i class="fas fa-phone"></i>
                        Contact
                    </h3>
                    <div id="ground-contact" class="contact-info">
                        <!-- Contact info will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ground data will be loaded here
        let currentGround = null;

        // Feature icons mapping
        const featureIcons = {
            'Floodlights': 'fas fa-lightbulb',
            'Parking': 'fas fa-parking',
            'Changing Rooms': 'fas fa-door-open',
            'Equipment Rental': 'fas fa-tools',
            'Clay Court': 'fas fa-circle',
            'Coaching Available': 'fas fa-user-graduate',
            'Air Conditioning': 'fas fa-snowflake',
            'Indoor': 'fas fa-home',
            'Spectator Seating': 'fas fa-chair',
            'Sound System': 'fas fa-volume-up',
            'Professional Pitch': 'fas fa-futbol',
            'Pavilion': 'fas fa-building',
            'Equipment Storage': 'fas fa-archive',
            'Scoreboard': 'fas fa-chart-line',
            'Multiple Courts': 'fas fa-th-large',
            'Olympic Pool': 'fas fa-swimming-pool',
            'Diving Board': 'fas fa-swimming-pool',
            'Locker Rooms': 'fas fa-lock',
            'Poolside Cafe': 'fas fa-coffee'
        };

        // Load ground data from database API
        async function loadGroundData() {
            try {
                // Get ground ID from URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const groundIdParam = urlParams.get('id');
                const groundId = groundIdParam ? parseInt(groundIdParam) : 1;
                
                if (!groundId || groundId <= 0) {
                    console.log('Invalid ground ID:', groundId);
                    showError();
                    return;
                }

                console.log('Loading ground with ID:', groundId);
                
                // Fetch ground details from database API
                const response = await fetch(`/api/ground/${groundId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('API Response:', result);
                
                if (result.success && result.data) {
                    currentGround = result.data;
                    console.log('Found ground:', currentGround.name);
                    displayGroundDetails(currentGround);
                } else {
                    console.log('Ground not found:', result.message);
                    showError();
                }
            } catch (error) {
                console.error('Error loading ground data:', error);
                showError();
            }
        }

        // Display ground details
        function displayGroundDetails(ground) {
            // Hide loading, show content
            document.getElementById('loading').style.display = 'none';
            document.getElementById('ground-content').style.display = 'block';

            // Update page title and breadcrumb
            document.title = `${ground.name} - GoPlay Sports Platform`;
            document.getElementById('breadcrumb-current').textContent = ground.name;

            // Basic info - handle database structure
            document.getElementById('ground-title').textContent = ground.name || 'Ground';
            
            // Location - combine address and city
            const location = ground.address ? `${ground.address}, ${ground.city}` : ground.city || ground.location || 'Location TBD';
            document.getElementById('ground-location').textContent = location;
            
            // Also update sidebar location
            const sidebarLocation = document.getElementById('sidebar-location');
            if (sidebarLocation) {
                sidebarLocation.textContent = location;
            }
            
            // Image - use first image from array or default
            const imageUrl = (ground.images && ground.images.length > 0) ? ground.images[0] : '/public/assets/images/ground.jpeg';
            document.getElementById('ground-image').src = imageUrl;
            document.getElementById('ground-image').alt = ground.name || 'Ground Image';
            
            // Description
            document.getElementById('ground-description').textContent = ground.description || 'A great facility for sports activities.';
            
            // Price - use hourly_rate from database
            const price = ground.hourly_rate || ground.price || 0;
            document.getElementById('ground-price').textContent = price.toLocaleString();

            // Rating - use database rating and review count
            const rating = ground.rating || 4.5;
            const reviewCount = ground.reviews || ground.total_reviews || 0;
            displayRating(rating, reviewCount);

            // Features - use amenities from database
            const features = ground.amenities || ground.features || ['Parking', 'Changing Rooms'];
            displayFeatures(features);

            // Availability - use availability from API response
            displayAvailability(ground.availability || getDefaultAvailability());

            // Contact information
            displayContactInfo(ground);

            // Update map location
            updateGroundLocation(ground);
        }

        // Default availability if not provided
        function getDefaultAvailability() {
            return {
                'monday': ['6:00 AM - 10:00 PM'],
                'tuesday': ['6:00 AM - 10:00 PM'],
                'wednesday': ['6:00 AM - 10:00 PM'],
                'thursday': ['6:00 AM - 10:00 PM'],
                'friday': ['6:00 AM - 10:00 PM'],
                'saturday': ['7:00 AM - 9:00 PM'],
                'sunday': ['8:00 AM - 8:00 PM']
            };
        }

        // Display rating stars
        function displayRating(rating, reviewCount) {
            const starsContainer = document.getElementById('ground-stars');
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;

            let starsHTML = '';
            
            // Full stars
            for (let i = 0; i < fullStars; i++) {
                starsHTML += '<i class="fas fa-star star"></i>';
            }
            
            // Half star
            if (hasHalfStar) {
                starsHTML += '<i class="fas fa-star-half-alt star"></i>';
            }
            
            // Empty stars
            const remainingStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            for (let i = 0; i < remainingStars; i++) {
                starsHTML += '<i class="far fa-star star"></i>';
            }

            starsContainer.innerHTML = starsHTML;
            document.getElementById('ground-rating').textContent = rating.toFixed(1);
            document.getElementById('ground-reviews').textContent = `(${reviewCount} reviews)`;
        }

        // Display features
        function displayFeatures(features) {
            const featuresContainer = document.getElementById('ground-features');
            
            const featuresHTML = features.map(feature => {
                const icon = featureIcons[feature] || 'fas fa-check';
                return `
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="${icon}"></i>
                        </div>
                        <span class="feature-text">${feature}</span>
                    </div>
                `;
            }).join('');

            featuresContainer.innerHTML = featuresHTML;
        }

        // Display availability
        function displayAvailability(availability) {
            const availabilityContainer = document.getElementById('ground-availability');
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            const availabilityHTML = days.map((day, index) => {
                const hours = availability[day] ? availability[day][0] : 'Closed';
                return `
                    <div class="day-slot">
                        <div class="day-name">${dayNames[index]}</div>
                        <div class="day-hours">${hours}</div>
                    </div>
                `;
            }).join('');

            availabilityContainer.innerHTML = availabilityHTML;
        }

        // Display contact information
        function displayContactInfo(ground) {
            const contactContainer = document.getElementById('ground-contact');
            
            let contactHTML = '';
            
            // Phone
            const phone = ground.phone || ground.contact_phone || '+94 11 234 5678';
            contactHTML += `
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <span class="contact-text">${phone}</span>
                </div>
            `;
            
            // Email
            const email = ground.email || ground.contact_email || 'info@goplay.lk';
            contactHTML += `
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span class="contact-text">${email}</span>
                </div>
            `;
            
            // Owner name (if available)
            if (ground.first_name && ground.last_name) {
                contactHTML += `
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="contact-text">${ground.first_name} ${ground.last_name}</span>
                    </div>
                `;
            }
            
            // Operating hours
            contactHTML += `
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="contact-text">Daily: 6AM - 10PM</span>
                </div>
            `;

            contactContainer.innerHTML = contactHTML;
        }

        // Show error state
        function showError() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error-state').style.display = 'block';
        }

        // Book ground function
        function bookGround() {
            if (currentGround) {
                // Redirect to booking page with ground ID
                window.location.href = `/payment?ground_id=${currentGround.id}&ground_name=${encodeURIComponent(currentGround.name)}`;
            }
        }

        // Google Maps for Ground Details
        let groundMap;
        let groundMarker;

        function initGroundMap() {
            console.log('Initializing ground map...');
            
            // Show loading overlay
            showMapLoading(true);
            
            // Default location (will be updated when ground data loads)
            const defaultLocation = { lat: 6.9271, lng: 79.8612 };
            
            groundMap = new google.maps.Map(document.getElementById('ground-map'), {
                zoom: 15,
                center: defaultLocation,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            });

            // Create marker (will be updated with sport-specific icon)
            groundMarker = new google.maps.Marker({
                position: defaultLocation,
                map: groundMap,
                title: 'Ground Location'
            });

            // Hide loading after map is ready
            google.maps.event.addListenerOnce(groundMap, 'idle', function() {
                showMapLoading(false);
            });

            console.log('Ground map initialized');
        }
        
        // Show/hide map loading overlay
        function showMapLoading(show) {
            const overlay = document.getElementById('map-loading-overlay');
            if (overlay) {
                overlay.style.display = show ? 'flex' : 'none';
            }
        }
        
        // Get sport-specific marker icon (same as book-ground page)
        function getSportMarkerIcon(sport) {
            const colors = {
                cricket: '#ff6b6b',
                tennis: '#4ecdc4', 
                football: '#45b7d1',
                basketball: '#f9ca24',
                badminton: '#6c5ce7',
                swimming: '#00cec9'
            };
            
            const sportKey = sport ? sport.toLowerCase() : 'general';
            
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: colors[sportKey] || '#2563eb',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 3
            };
        }

        function updateGroundLocation(ground) {
            if (!groundMap || !groundMarker) return;

            // Use actual coordinates from database
            if (ground.latitude && ground.longitude) {
                const position = { 
                    lat: parseFloat(ground.latitude), 
                    lng: parseFloat(ground.longitude) 
                };

                // Update map center and marker with database coordinates
                groundMap.setCenter(position);
                groundMarker.setPosition(position);
                groundMarker.setTitle(ground.name);
                
                // Update marker icon based on sport category
                const sportIcon = getSportMarkerIcon(ground.category_name);
                groundMarker.setIcon(sportIcon);
                
                console.log(`Updated map for ${ground.name} at coordinates:`, position);
            } else {
                console.log(`No coordinates available for ${ground.name}, using default location`);
                // Fallback to default location if no coordinates in database
                const defaultPosition = { lat: 6.9271, lng: 79.8612 };
                groundMap.setCenter(defaultPosition);
                groundMarker.setPosition(defaultPosition);
                groundMarker.setTitle(ground.name || 'Sports Facility');
                
                // Set default icon
                const defaultIcon = getSportMarkerIcon('general');
                groundMarker.setIcon(defaultIcon);
            }

            // Add info window with actual ground data
            const locationText = ground.address ? `${ground.address}, ${ground.city}` : ground.city || 'Sports Facility';
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h4 style="margin: 0 0 8px 0; color: #2563eb;">${ground.name}</h4>
                        <p style="margin: 0 0 6px 0; color: #666; font-size: 14px;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                            ${locationText}
                        </p>
                        <p style="margin: 0 0 6px 0; color: #666; font-size: 14px;">
                            <i class="fas fa-tag" style="margin-right: 5px;"></i>
                            ${ground.category_name || 'Sports Facility'}
                        </p>
                        <p style="margin: 0; color: #2563eb; font-weight: 600; font-size: 14px;">
                            LKR ${ground.hourly_rate ? ground.hourly_rate.toLocaleString() : 'N/A'}/hour
                        </p>
                    </div>
                `
            });

            groundMarker.addListener('click', () => {
                infoWindow.open(groundMap, groundMarker);
            });
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadGroundData();
        });
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3qKhJG9ulG0vgu9KxaG0NPXADLGxMr7k&callback=initGroundMap">
    </script>