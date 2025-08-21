<?php 
$title = 'GoPlay - Premier Sports Booking Platform';
$description = 'Book sports facilities, hire professional coaches, and shop for equipment at GoPlay';
$additionalCSS = ['/public/css/pages/index.css'];
$additionalJS = ['/public/js/pages/index.js'];
?>

<link rel="stylesheet" href="/public/css/pages/index.css">

    <style>
        /* CSS Custom Properties */
        :root {
            --primary-color: #28a745;
            --primary-dark: #218838;
            --primary-light: #d4edda;
            --secondary-color: #20c997;
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
            --font-primary: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-primary);
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--background-white);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Main Content */
        .main-content {
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
            z-index: 1;
        }

        .hero-content {
            text-align: center;
            color: var(--text-white);
            z-index: 2;
            position: relative;
            max-width: 800px;
            padding: 0 2rem;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: fadeInUp 1s ease-out;
        }

        .hero-highlight {
            color: var(--accent-color);
            text-shadow: 0 0 20px rgba(255, 193, 7, 0.5);
        }

        .hero-description {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            border-radius: var(--border-radius-lg);
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow-medium);
            min-width: 160px;
            justify-content: center;
        }

        .hero-btn.primary {
            background: var(--text-white);
            color: var(--primary-color);
        }

        .hero-btn.primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
            background: var(--background-light);
        }

        .hero-btn.secondary {
            background: transparent;
            color: var(--text-white);
            border: 2px solid var(--text-white);
        }

        .hero-btn.secondary:hover {
            background: var(--text-white);
            color: var(--primary-color);
            transform: translateY(-3px);
        }

        /* Section Styling */
        section {
            padding: 5rem 0;
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .title-highlight {
            color: var(--primary-color);
            position: relative;
        }

        .title-highlight::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
            opacity: 0.3;
        }

        .section-description {
            font-size: 1.1rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Features Section */
        .features-section {
            background: var(--background-light);
            overflow: hidden;
        }

        .features-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 0%, rgba(40, 167, 69, 0.02) 50%, transparent 100%);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            position: relative;
            z-index: 2;
        }

        .feature-card {
            background: var(--background-white);
            border-radius: var(--border-radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
            position: relative;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .feature-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .feature-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .feature-card:hover .feature-image img {
            transform: scale(1.05);
        }

        .feature-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(32, 201, 151, 0.8));
            opacity: 0;
            transition: var(--transition);
        }

        .feature-card:hover .feature-overlay {
            opacity: 1;
        }

        .feature-stats {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }

        .feature-header {
            padding: 1.5rem 2rem 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .feature-icon.accent {
            background: rgba(255, 193, 7, 0.2);
            color: var(--accent-color);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .feature-content {
            padding: 0 2rem 2rem;
        }

        .feature-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .feature-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .feature-btn:hover {
            gap: 1rem;
            color: var(--primary-dark);
        }

        .feature-btn::after {
            content: '→';
            transition: var(--transition);
        }

        /* News Section */
        .news-section {
            background: var(--background-white);
        }

        .news-container {
            position: relative;
        }

        .news-carousel {
            display: flex;
            gap: 2rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 1rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .news-carousel::-webkit-scrollbar {
            display: none;
        }

        .news-card {
            flex: 0 0 300px;
            background: var(--background-white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .news-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-medium);
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .carousel-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: var(--primary-color);
            color: var(--text-white);
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        /* Venues Section */
        .venues-section {
            background: var(--background-light);
        }

        .venues-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .venue-card {
            background: var(--background-white);
            border-radius: var(--border-radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .venue-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-medium);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-description {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .hero-btn {
                width: 100%;
                max-width: 300px;
            }

            .section-title {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            section {
                padding: 3rem 0;
            }

            .section-header {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .section-title {
                font-size: 1.75rem;
            }

            .feature-header {
                padding: 1rem 1.5rem 0.5rem;
            }

            .feature-content {
                padding: 0 1.5rem 1.5rem;
            }

            .news-card {
                flex: 0 0 250px;
            }
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .mb-4 {
            margin-bottom: 2rem;
        }

        .mt-4 {
            margin-top: 2rem;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Focus styles for accessibility */
        .hero-btn:focus,
        .feature-btn:focus,
        .carousel-btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>

    <!-- Navbar Component -->
    <div id="navbar-container"></div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-background"></div>
            <div class="hero-content">
                <h1 class="hero-title">
                    Your Ultimate GOPLAY
                </h1>
                <p class="hero-description">
                    Book grounds, hire coaches, buy equipment, and stay updated with the latest sports news
                </p>
                <div class="hero-buttons">
                    <a href="/book-ground" class="hero-btn primary">
                        <i class="fas fa-calendar"></i>
                        Book Now
                    </a>
                    <a href="/shop" class="hero-btn secondary">
                        <i class="fas fa-shopping-cart"></i>
                        Shop Equipment
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="features-background"></div>
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Everything You Need for <span class="title-highlight">Sports</span>
                    </h2>
                    <p class="section-description">
                        From booking world-class facilities to finding expert coaches and premium equipment
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card" data-feature="booking">
                        <div class="feature-image">
                            <img src="assets/images/feature-booking.jpg" alt="Book Sports Grounds" />
                            <div class="feature-overlay"></div>
                            <div class="feature-stats">500+ Venues</div>
                        </div>
                        <div class="feature-header">
                            <div class="feature-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h3 class="feature-title">Book Sports Grounds</h3>
                        </div>
                        <div class="feature-content">
                            <p class="feature-description">
                                Reserve courts, fields, and complexes for your games
                            </p>
                            <a href="/book-ground" class="feature-btn">
                                Get Started
                            </a>
                        </div>
                    </div>

                    <div class="feature-card" data-feature="coaching">
                        <div class="feature-image">
                            <img src="assets/images/feature-coaching.jpg" alt="Hire Professional Coaches" />
                            <div class="feature-overlay"></div>
                            <div class="feature-stats">200+ Coaches</div>
                        </div>
                        <div class="feature-header">
                            <div class="feature-icon accent">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3 class="feature-title">Hire Professional Coaches</h3>
                        </div>
                        <div class="feature-content">
                            <p class="feature-description">
                                Train with certified coaches for all sports
                            </p>
                            <a href="/book-coach" class="feature-btn">
                                Get Started
                            </a>
                        </div>
                    </div>

                    <div class="feature-card" data-feature="equipment">
                        <div class="feature-image">
                            <img src="assets/images/feature-equipment.jpg" alt="Sports Equipment Shop" />
                            <div class="feature-overlay"></div>
                            <div class="feature-stats">1000+ Products</div>
                        </div>
                        <div class="feature-header">
                            <div class="feature-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="feature-title">Sports Equipment Shop</h3>
                        </div>
                        <div class="feature-content">
                            <p class="feature-description">
                                Buy quality gear from trusted brands
                            </p>
                            <a href="/shop" class="feature-btn">
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section class="news-section">
            <div class="news-background"></div>
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Latest <span class="title-highlight">Sports News</span>
                    </h2>
                    <p class="section-description">
                        Stay updated with the latest happenings in the sports world
                    </p>
                </div>
                <div class="news-container">
                    <div class="news-carousel" id="newsCarousel">
                        <!-- News items will be loaded here -->
                    </div>
                    <div class="carousel-controls">
                        <button class="carousel-btn prev" id="prevBtn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="carousel-btn next" id="nextBtn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Venues -->
        <section class="venues-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Popular <span class="title-highlight">Venues</span>
                    </h2>
                    <p class="section-description">
                        Book your favorite sports facilities at premium locations
                    </p>
                </div>

                <div class="venues-grid" id="venuesGrid">
                    <!-- Venues will be loaded here -->
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Component -->
    <div id="footer-container"></div>

    <script src="js/components/news-carousel.js"></script>
    <script src="js/pages/index.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/components/navbar.js"></script>
    <script>
        // Load the shared navbar dynamically
        fetch('/app/views/components/navbar.php')
            .then(res => res.text())
            .then(data => {
                document.getElementById('navbar-container').innerHTML = data;
                if (typeof Navbar !== 'undefined') {
                    new Navbar(); // Initialize navbar behavior
                }
            });

        // Load the shared footer dynamically
        fetch('/app/views/components/footer.php')
            .then(res => res.text())
            .then(data => {
                document.getElementById('footer-container').innerHTML = data;
            })
            .catch(() => {
                document.getElementById('footer-container').innerHTML = `
                    <footer style="background: var(--text-primary); color: white; padding: 2rem 0; text-align: center; margin-top: 3rem;">
                        <p>&copy; 2024 GoPlay Sports Platform. All rights reserved.</p>
                    </footer>
                `;
            });

        // Global logout function
        function logout() {
            localStorage.removeItem('currentUser');
            window.location.href = '/';
        }
    </script>
