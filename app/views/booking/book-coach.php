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
                    <button class="btn-secondary">
                        <i class="fas fa-video"></i>
                        Online Coaching
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
                            <option value="football">Football</option>
                            <option value="tennis">Tennis</option>
                            <option value="basketball">Basketball</option>
                            <option value="cricket">Cricket</option>
                            <option value="badminton">Badminton</option>
                            <option value="swimming">Swimming</option>
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
        // Sample coach data
        const coaches = [
            {
                id: 1,
                name: "Sanath Fernando",
                sport: "Cricket",
                experience: "8 years",
                rating: 4.9,
                reviews: 156,
                price: 3500,
                location: "Colombo",
                bio: "Former national team player with extensive coaching experience in cricket fundamentals and advanced techniques.",
                specialties: ["Batting", "Bowling", "Fielding", "Mental Training"],
                certifications: ["Level 3 Cricket Coach", "Sports Psychology"]
            },
            {
                id: 2,
                name: "Priya Wijesinghe",
                sport: "Tennis",
                experience: "6 years",
                rating: 4.8,
                reviews: 89,
                price: 4000,
                location: "Kandy",
                bio: "Professional tennis coach specializing in junior development and competitive training programs.",
                specialties: ["Technique", "Strategy", "Match Play", "Fitness"],
                certifications: ["ITF Certified", "Youth Development"]
            },
            {
                id: 3,
                name: "Kamal Silva",
                sport: "Football",
                experience: "10 years",
                rating: 4.7,
                reviews: 203,
                price: 2800,
                location: "Galle",
                bio: "Former professional footballer now dedicated to developing the next generation of players.",
                specialties: ["Technical Skills", "Tactical Awareness", "Physical Conditioning"],
                certifications: ["UEFA B License", "Strength & Conditioning"]
            },
            {
                id: 4,
                name: "Niluka Perera",
                sport: "Swimming",
                experience: "5 years",
                rating: 4.9,
                reviews: 92,
                price: 3200,
                location: "Colombo",
                bio: "Olympic swimmer turned coach, specializing in competitive swimming and stroke technique.",
                specialties: ["Stroke Technique", "Endurance", "Competition Prep"],
                certifications: ["Swim Coach Level 2", "Water Safety"]
            },
            {
                id: 5,
                name: "Ravi Mendis",
                sport: "Basketball",
                experience: "7 years",
                rating: 4.6,
                reviews: 134,
                price: 3000,
                location: "Negombo",
                bio: "Basketball coach with focus on fundamental skills and team play development.",
                specialties: ["Shooting", "Defense", "Team Strategy", "Youth Development"],
                certifications: ["FIBA Certified", "Youth Basketball"]
            },
            {
                id: 6,
                name: "Chamini De Silva",
                sport: "Badminton",
                experience: "4 years",
                rating: 4.8,
                reviews: 67,
                price: 2500,
                location: "Kandy",
                bio: "Former national badminton player with expertise in competitive training and technique refinement.",
                specialties: ["Technique", "Footwork", "Game Strategy", "Mental Preparation"],
                certifications: ["BWF Certified", "Sports Nutrition"]
            }
        ];


        // Render coaches
        function renderCoaches() {
            const grid = document.getElementById('coaches-grid');
            
            const coachCards = coaches.map(coach => {
                const stars = generateStars(coach.rating);
                const specialtyTags = coach.specialties.map(specialty => 
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
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="coach-name">${coach.name}</h3>
                            <span class="coach-specialization">${coach.sport} Coach</span>
                        </div>
                        <div class="coach-content">
                            <div class="coach-info">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${coach.location}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>${coach.experience}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span>${coach.reviews} students</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-star"></i>
                                    <span>${stars}</span>
                                </div>
                            </div>
                            <p class="coach-bio">${coach.bio}</p>
                            <div class="coach-specialties">
                                <div class="specialties-title">Specializations:</div>
                                <div class="specialty-tags">
                                    ${specialtyTags}
                                </div>
                            </div>
                            <div class="coach-footer">
                                <div class="coach-price">
                                    <div class="price-amount">LKR ${coach.price.toLocaleString()}</div>
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

        // Coach action functions
        function viewProfile(coachId) {
            console.log('View profile for coach:', coachId);
            // Implement profile view
        }

        function bookSession(coachId) {
            console.log('Book session with coach:', coachId);
            // Redirect to booking page
            window.location.href = `/payment?coach_id=${coachId}`;
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(renderCoaches, 1000); // Simulate loading
        });
    </script>