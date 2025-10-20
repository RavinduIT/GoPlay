<?php 
$title = 'Book Coach - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Unified Design System */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #64748b;
            --primary-light: #f1f5f9;
            --secondary-color: #0891b2;
            --accent-color: #0891b2;
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
            background: var(--background-light);
        }

        .book-coach-container {
            min-height: 100vh;
        }

        /* Header Section */
        .page-header {
            background: #1e3a8a;
            color: white;
            position: relative;
            overflow: hidden;
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

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--background-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
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

        /* Coaches Grid */
        .coaches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .coach-card {
            background: var(--background-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
        }

        .coach-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .coach-header {
            position: relative;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
            padding: 2rem;
            text-align: center;
        }

        .coach-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--background-white);
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
            box-shadow: var(--shadow-medium);
        }

        .coach-badge {
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
            font-size: 0.9rem;
        }

        .coach-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .coach-specialization {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .coach-content {
            padding: 2rem;
        }

        .coach-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .info-item i {
            width: 16px;
            color: var(--primary-color);
        }

        .coach-bio {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .coach-specialties {
            margin-bottom: 1.5rem;
        }

        .specialties-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .specialty-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .specialty-tag {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .coach-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .coach-price {
            text-align: left;
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

        .coach-actions {
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
            font-size: 0.9rem;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .coach-actions .btn-primary {
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .coach-actions .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .loading-spinner {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
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

            .coaches-grid {
                grid-template-columns: 1fr;
            }

            .results-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .coach-info {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 1rem;
            }

            .search-container {
                padding: 1rem;
            }

            .coach-actions {
                flex-direction: column;
            }
        }
    </style>
<div class="book-coach-container">

        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1>Book Professional Coaches</h1>
                    <p>Train with certified coaches and improve your sports skills</p>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number">100+</span>
                            <span class="stat-label">Coaches</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">15+</span>
                            <span class="stat-label">Sports</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">4.9★</span>
                            <span class="stat-label">Rating</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn-primary">
                        <i class="fas fa-calendar"></i>
                        Schedule Session
                    </button>
                   
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-bar">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-coaches" placeholder="Search coaches by name, sport, or expertise..." class="search-input">
                    </div>
                    <div class="filter-group">
                        <select id="sport-filter" class="filter-select">
                            <option value="">All Sports</option>
                            <!-- Sports options will be loaded dynamically -->
                        </select>
                        <select id="experience-filter" class="filter-select">
                            <option value="">Any Experience</option>
                            <option value="1-3">1-3 Years</option>
                            <option value="3-5">3-5 Years</option>
                            <option value="5+">5+ Years</option>
                        </select>
                        <select id="price-filter" class="filter-select">
                            <option value="">Any Price</option>
                            <option value="0-2000">LKR 0 - 2,000</option>
                            <option value="2000-4000">LKR 2,000 - 4,000</option>
                            <option value="4000+">LKR 4,000+</option>
                        </select>
                    </div>
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="results-header">
                <div class="results-info">
                    <h2>Professional Sports Coaches</h2>
                    <p class="results-subtitle">Choose from our certified and experienced coaches</p>
                </div>
                <div class="sort-controls">
                    <select class="sort-select">
                        <option value="rating">Sort by Rating</option>
                        <option value="experience">Sort by Experience</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div class="coaches-grid" id="coaches-grid">
                <!-- Coach cards will be dynamically loaded here -->
                <div class="loading-state">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Loading professional coaches...</p>
                </div>
            </div>
        </div>

</div>

    <script>
        // Global variables
        let allCoaches = [];
        let filteredCoaches = [];
        let sportsCategories = [];

        
        // Load sports categories for filter dropdown
        async function loadSportsCategories() {
            try {
                const response = await fetch('/api/sports-categories');
                const data = await response.json();
                
                if (data.success) {
                    sportsCategories = data.categories;
                    const sportFilter = document.getElementById('sport-filter');
                    
                    // Clear existing options (keep "All Sports")
                    sportFilter.innerHTML = '<option value="">All Sports</option>';
                    
                    // Add categories to dropdown
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.name;
                        option.textContent = category.name;
                        sportFilter.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading sports categories:', error);
            }
        }
        
        // Load coaches from database
        async function loadCoaches(params = {}) {
            try {
                showLoading();
                
                // Build query string
                const queryParams = new URLSearchParams();
                if (params.search) queryParams.set('search', params.search);
                if (params.sport) queryParams.set('sport', params.sport);
                if (params.experience) queryParams.set('experience', params.experience);
                if (params.price) queryParams.set('price', params.price);
                if (params.sort) queryParams.set('sort', params.sort);
                
                const response = await fetch(`/api/coaches?${queryParams.toString()}`);
                const data = await response.json();
                
                if (data.success) {
                    allCoaches = data.coaches;
                    filteredCoaches = data.coaches;
                    renderCoaches(filteredCoaches);
                    updateResultsCount(data.total);
                } else {
                    showError('Failed to load coaches: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error loading coaches:', error);
                showError('Failed to load coaches. Please try again.');
            }
        }
        
        // Show loading state
        function showLoading() {
            const grid = document.getElementById('coaches-grid');
            grid.innerHTML = `
                <div class="loading-state">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Loading professional coaches...</p>
                </div>
            `;
        }
        
        // Show error state
        function showError(message) {
            const grid = document.getElementById('coaches-grid');
            grid.innerHTML = `
                <div class="loading-state">
                    <div style="color: var(--danger-color); margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p>${message}</p>
                    <button onclick="loadCoaches()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer;">
                        Try Again
                    </button>
                </div>
            `;
        }
        
        // Update results count
        function updateResultsCount(count) {
            const subtitle = document.querySelector('.results-subtitle');
            if (subtitle) {
                subtitle.textContent = `${count} coaches available`;
            }
        }

        // Render coaches
        function renderCoaches(coaches) {
            const grid = document.getElementById('coaches-grid');
            
            if (!coaches || coaches.length === 0) {
                grid.innerHTML = `
                    <div class="loading-state">
                        <div style="color: var(--text-secondary); margin-bottom: 1rem;">
                            <i class="fas fa-search"></i>
                        </div>
                        <p>No coaches found matching your criteria.</p>
                        <button onclick="clearFilters()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer;">
                            Clear Filters
                        </button>
                    </div>
                `;
                return;
            }
            
            const coachCards = coaches.map(coach => {
                const stars = generateStars(coach.rating);
                const specialtyTags = (coach.specialties || []).map(specialty => 
                    `<span class="specialty-tag">${specialty}</span>`
                ).join('');

                return `
                    <div class="coach-card">
                        <div class="coach-header">
                            <div class="coach-badge">
                                <i class="fas fa-star"></i>
                                ${coach.rating}
                            </div>
                            <div class="coach-avatar">
                                ${getCoachImage(coach)}
                            </div>
                            <h3 class="coach-name">${coach.name}</h3>
                            <span class="coach-specialization">${coach.sport} Coach</span>
                        </div>
                        <div class="coach-content">
                            <div class="coach-info">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${coach.location || 'Location not specified'}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>${coach.experience}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span>${coach.reviews} reviews</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-star"></i>
                                    <span>${stars}</span>
                                </div>
                            </div>
                            <p class="coach-bio">${coach.bio || 'No bio available.'}</p>
                            ${specialtyTags ? `
                            <div class="coach-specialties">
                                <div class="specialties-title">Specializations:</div>
                                <div class="specialty-tags">
                                    ${specialtyTags}
                                </div>
                            </div>
                            ` : ''}
                            <div class="coach-footer">
                                <div class="coach-price">
                                    <div class="price-amount">LKR ${parseFloat(coach.price).toLocaleString()}</div>
                                    <div class="price-period">per session</div>
                                </div>
                                <div class="coach-actions">
                                    <button class="btn-outline" onclick="viewProfile(${coach.id})">
                                        View Profile
                                    </button>
                                    <button class="btn-primary" onclick="bookSession(${coach.id})">
                                        Book Session
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            grid.innerHTML = coachCards;
        }

        // Get coach image based on sport category
        function getCoachImage(coach) {
            // If coach has profile picture, use it
            if (coach.profile_picture) {
                return `<img src="${coach.profile_picture}" alt="${coach.name}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
            }

            // Use sport-specific coach images with existing assets
            const sportName = coach.sport ? coach.sport.toLowerCase() : 'general';
            const coachImages = {
                'football': '/public/assets/images/coaches/coach1.png',
                'cricket': '/public/assets/images/coaches/coach2.png',
                'tennis': '/public/assets/images/coaches/coach3.png',
                'basketball': '/public/assets/images/coaches/coach1.png',
                'badminton': '/public/assets/images/coaches/coach2.png',
                'volleyball': '/public/assets/images/coaches/coach3.png',
                'swimming': '/public/assets/images/coaches/coach1.png',
                'fitness': '/public/assets/images/coaches/coach2.png',
                'gym': '/public/assets/images/coaches/coach3.png'
            };

            const imageUrl = coachImages[sportName] || '/public/assets/images/coaches/coach1.png';
            return `<img src="${imageUrl}" alt="${coach.name}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
        }

        // Generate star rating HTML
        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;

            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fas fa-star" style="color: var(--warning-color);"></i>';
            }

            if (hasHalfStar) {
                stars += '<i class="fas fa-star-half-alt" style="color: var(--warning-color);"></i>';
            }

            const remainingStars = 5 - Math.ceil(rating);
            for (let i = 0; i < remainingStars; i++) {
                stars += '<i class="far fa-star" style="color: var(--warning-color);"></i>';
            }

            return stars;
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('search-coaches').value = '';
            document.getElementById('sport-filter').value = '';
            document.getElementById('experience-filter').value = '';
            document.getElementById('price-filter').value = '';
            document.querySelector('.sort-select').value = 'rating';
            loadCoaches();
        }
        
        // Search and filter coaches
        function searchCoaches() {
            const searchQuery = document.getElementById('search-coaches').value;
            const sport = document.getElementById('sport-filter').value;
            const experience = document.getElementById('experience-filter').value;
            const price = document.getElementById('price-filter').value;
            const sort = document.querySelector('.sort-select').value;
            
            const params = {};
            if (searchQuery) params.search = searchQuery;
            if (sport) params.sport = sport;
            if (experience) params.experience = experience;
            if (price) params.price = price;
            if (sort) params.sort = sort;
            
            loadCoaches(params);
        }

        // Coach action functions
        function viewProfile(coachId) {
            console.log('View profile for coach:', coachId);
            // Redirect to public coach profile page
            window.location.href = `/coach/view`;
        }

        function bookSession(coachId) {
            console.log('Book session with coach:', coachId);
            // Redirect to session booking page
            window.location.href = `/app/views/booking/book-session.php?coach_id=${coachId}`;
        }
        
    
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data
            loadSportsCategories();
            loadCoaches();
            
            // Set up event listeners
            const searchBtn = document.querySelector('.search-btn');
            const searchInput = document.getElementById('search-coaches');
            const sportFilter = document.getElementById('sport-filter');
            const experienceFilter = document.getElementById('experience-filter');
            const priceFilter = document.getElementById('price-filter');
            const sortSelect = document.querySelector('.sort-select');
            
            // Search button click
            if (searchBtn) {
                searchBtn.addEventListener('click', searchCoaches);
            }
            
            // Enter key in search input
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchCoaches();
                    }
                });
            }
            
            // Filter changes
            [sportFilter, experienceFilter, priceFilter, sortSelect].forEach(element => {
                if (element) {
                    element.addEventListener('change', searchCoaches);
                }
            });
        });
    </script>