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
            height: 250px;
            background: var(--background-light);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
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
                        <i class="fas fa-map-marker-alt" style="font-size: 2rem;"></i>
                        <p>Interactive map coming soon</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="sidebar-section">
                    <h3 class="section-title">
                        <i class="fas fa-phone"></i>
                        Contact
                    </h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <span class="contact-text">+94 11 234 5678</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="contact-text">info@goplay.lk</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="contact-text">24/7 Support</span>
                        </div>
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

        // Load ground data
        async function loadGroundData() {
            try {
                const response = await fetch('/public/data/grounds.json');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                
                // Get ground ID from URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const groundIdParam = urlParams.get('id');
                const groundId = groundIdParam ? parseInt(groundIdParam) : 1;
                
                console.log('Looking for ground with ID:', groundId);
                console.log('Available grounds:', data.grounds.map(g => ({id: g.id, name: g.name})));
                
                currentGround = data.grounds.find(ground => ground.id === groundId);
                
                if (currentGround) {
                    console.log('Found ground:', currentGround.name);
                    displayGroundDetails(currentGround);
                } else {
                    console.log('Ground not found with ID:', groundId);
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

            // Basic info
            document.getElementById('ground-title').textContent = ground.name;
            document.getElementById('ground-location').textContent = ground.location;
            document.getElementById('ground-image').src = ground.image;
            document.getElementById('ground-image').alt = ground.name;
            document.getElementById('ground-description').textContent = ground.description;
            document.getElementById('ground-price').textContent = ground.price.toLocaleString();

            // Rating
            displayRating(ground.rating, ground.reviews);

            // Features
            displayFeatures(ground.features);

            // Availability
            displayAvailability(ground.availability);
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

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadGroundData();
        });
    </script>