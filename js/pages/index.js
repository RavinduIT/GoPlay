// Home Page JavaScript
class HomePage {
    constructor() {
        this.currentNewsIndex = 0;
        this.newsInterval = null;
        this.init();
    }

    init() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Load navbar first, then initialize other components
        this.loadNavbar();

        // Set up images
        this.setupImages();

        // Initialize news carousel
        this.initNewsCarousel();

        // Add event listeners
        this.addEventListeners();
    }

    async loadNavbar() {
        console.log('Starting navbar load...');
        const navbarContainer = document.getElementById('navbar-container');
        
        if (!navbarContainer) {
            console.error('Navbar container not found!');
            return;
        }

        console.log('Navbar container found, fetching navbar...');

        try {
            const response = await fetch('../pages/components/navbar.html');
            console.log('Fetch response:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            console.log('HTML fetched, length:', html.length);
            
            // Extract just the navbar content from the full HTML document
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const navElement = doc.querySelector('nav');
            
            if (navElement) {
                navbarContainer.innerHTML = navElement.outerHTML;
                console.log('Navbar HTML inserted successfully');
            } else {
                console.error('No nav element found in the fetched HTML');
                navbarContainer.innerHTML = html; // Fallback to full HTML
            }
            
            // Re-run Lucide icons after navbar loads
            if (window.lucide) {
                console.log('Reinitializing Lucide icons...');
                lucide.createIcons();
            }
            
            // Load and execute navbar JavaScript
            this.loadNavbarScript();
            
        } catch (error) {
            console.error('Error loading navbar:', error);
            // Create a simple fallback navbar
            this.createFallbackNavbar(navbarContainer);
        }
    }

    loadNavbarScript() {
        // Check if NavbarManager is available
        if (typeof NavbarManager !== 'undefined') {
            console.log('NavbarManager found, initializing...');
            if (!window.navbarManager) {
                window.navbarManager = new NavbarManager();
            }
        } else {
            console.log('NavbarManager not found, loading script...');
            // Dynamically load navbar script if not already loaded
            const script = document.createElement('script');
            script.src = '../components/navbar.js';
            script.onload = () => {
                console.log('Navbar script loaded');
                if (typeof NavbarManager !== 'undefined') {
                    window.navbarManager = new NavbarManager();
                }
            };
            script.onerror = () => {
                console.error('Failed to load navbar script');
            };
            document.head.appendChild(script);
        }
    }

    createFallbackNavbar(container) {
        console.log('Creating fallback navbar...');
        container.innerHTML = `
            <nav class="navbar">
                <div class="container">
                    <div class="nav-content">
                        <a href="/" class="logo">
                            <div class="logo-icon">
                                <i data-lucide="trophy"></i>
                            </div>
                            <span class="logo-text">SportHub</span>
                        </a>
                        <div class="nav-items desktop-nav">
                            <a href="/" class="nav-item active">
                                <i data-lucide="trophy"></i>
                                <span>Home</span>
                            </a>
                            <a href="/book-ground" class="nav-item">
                                <i data-lucide="calendar"></i>
                                <span>Book Ground</span>
                            </a>
                            <a href="/book-coach" class="nav-item">
                                <i data-lucide="user"></i>
                                <span>Book Coach</span>
                            </a>
                            <a href="/shop" class="nav-item">
                                <i data-lucide="shopping-cart"></i>
                                <span>Sports Shop</span>
                            </a>
                        </div>
                        <div class="auth-section desktop-nav">
                            <a href="/login" class="btn btn-outline">Login</a>
                            <a href="/signup" class="btn btn-primary">Sign Up</a>
                        </div>
                    </div>
                </div>
            </nav>
        `;
        
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    setupImages() {
        // Set up image sources - you can modify these paths as needed
        const imageMap = {
            heroBackground: 'assets/hero-background.jpg',
            featureBookingImage: 'assets/feature-booking.jpg',
            featureCoachingImage: 'assets/feature-coaching.jpg',
            featureEquipmentImage: 'assets/feature-equipment.jpg',
            sportsComplexImage: 'assets/sports-complex.jpg',
            tennisCourtImage: 'assets/tennis-court.jpg',
            basketballCourtImage: 'assets/basketball-court.jpg'
        };

        // Set hero background
        const heroBackground = document.querySelector('.hero-background');
        if (heroBackground) {
            heroBackground.style.backgroundImage = `url('${imageMap.heroBackground}')`;
        }

        // Set feature images
        document.querySelectorAll('[data-image]').forEach(img => {
            const imageKey = img.getAttribute('data-image');
            if (imageMap[imageKey]) {
                img.src = imageMap[imageKey];
            }
        });
    }

    initNewsCarousel() {
        const newsCarousel = document.getElementById('newsCarousel');
        const newsIndicators = document.getElementById('newsIndicators');

        if (!newsCarousel || !newsIndicators) return;

        // Create news items
        this.createNewsItems(newsCarousel);

        // Create indicators
        this.createNewsIndicators(newsIndicators);

        // Start auto-scroll
        this.startNewsAutoScroll();
    }

    createNewsItems(container) {
        // Mock news data if newsData is not available
        const newsItems = window.newsData?.newsItems || [
            {
                id: 1,
                title: "Sports Complex Opens New Facilities",
                summary: "State-of-the-art equipment and modern courts available",
                date: "2024-08-01",
                image: "heroSportsImage"
            },
            {
                id: 2,
                title: "Tennis Championship Registration Open",
                summary: "Join the annual tennis tournament this summer",
                date: "2024-07-28",
                image: "tennisCourtImage"
            },
            {
                id: 3,
                title: "Basketball League Season Starts",
                summary: "Professional coaches and competitive leagues",
                date: "2024-07-25",
                image: "basketballCourtImage"
            }
        ];

        // Create items twice for seamless loop
        const allItems = [...newsItems, ...newsItems];

        allItems.forEach((news, index) => {
            const newsItem = document.createElement('div');
            newsItem.className = 'news-item';

            newsItem.innerHTML = `
                <div class="news-card" onclick="navigateToNews(${news.id})">
                    <div class="news-image-container">
                        <img src="${this.getNewsImage(news.image)}" alt="${news.title}" class="news-image">
                        <div class="news-image-overlay"></div>
                        <div class="news-content">
                            <h3 class="news-title">${news.title}</h3>
                            <p class="news-summary">${news.summary}</p>
                            <div class="news-meta">
                                <p class="news-date">${this.formatDate(news.date)}</p>
                                <div class="news-indicator"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(newsItem);
        });

        // Set initial width and position
        container.style.width = `${(newsItems.length * 2 * 100) / 3}%`;
        this.updateNewsCarouselPosition();
    }

    createNewsIndicators(container) {
        const newsItems = window.newsData?.newsItems || [1, 2, 3]; // Mock data

        newsItems.forEach((_, index) => {
            const indicator = document.createElement('button');
            indicator.className = `indicator ${index === 0 ? 'active' : ''}`;
            indicator.onclick = () => this.goToNewsSlide(index);
            container.appendChild(indicator);
        });
    }

    getNewsImage(imageName) {
        const imageMap = {
            'heroSportsImage': 'assets/hero-sports.jpg',
            'sportsComplexImage': 'assets/sports-complex.jpg',
            'tennisCourtImage': 'assets/tennis-court.jpg',
            'basketballCourtImage': 'assets/basketball-court.jpg'
        };
        return imageMap[imageName] || 'assets/hero-sports.jpg';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    updateNewsCarouselPosition() {
        const carousel = document.getElementById('newsCarousel');
        if (carousel) {
            const translateX = -(this.currentNewsIndex * (100 / 3));
            carousel.style.transform = `translateX(${translateX}%)`;
        }

        // Update indicators
        this.updateNewsIndicators();
    }

    updateNewsIndicators() {
        const indicators = document.querySelectorAll('.indicator');
        const newsItems = window.newsData?.newsItems || [1, 2, 3];

        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === (this.currentNewsIndex % newsItems.length));
        });
    }

    startNewsAutoScroll() {
        this.newsInterval = setInterval(() => {
            this.nextNewsSlide();
        }, 5000); // Change every 5 seconds
    }

    stopNewsAutoScroll() {
        if (this.newsInterval) {
            clearInterval(this.newsInterval);
            this.newsInterval = null;
        }
    }

    nextNewsSlide() {
        const newsItems = window.newsData?.newsItems || [1, 2, 3];
        this.currentNewsIndex = (this.currentNewsIndex + 1) % newsItems.length;
        this.updateNewsCarouselPosition();
    }

    goToNewsSlide(index) {
        this.currentNewsIndex = index;
        this.updateNewsCarouselPosition();

        // Restart auto-scroll
        this.stopNewsAutoScroll();
        this.startNewsAutoScroll();
    }

    addEventListeners() {
        // Pause carousel on hover
        const newsContainer = document.querySelector('.news-container');
        if (newsContainer) {
            newsContainer.addEventListener('mouseenter', () => {
                this.stopNewsAutoScroll();
            });

            newsContainer.addEventListener('mouseleave', () => {
                this.startNewsAutoScroll();
            });
        }

        // Add smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for animations
        this.setupScrollAnimations();
    }

    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.feature-card, .venue-card, .section-header').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    }
}

// Global functions
function navigateToNews(newsId) {
    window.location.href = `/news-details?id=${newsId}`;
}

function navigateToBookGround() {
    window.location.href = '/book-ground';
}

function navigateToBookCoach() {
    window.location.href = '/book-coach';
}

function navigateToShop() {
    window.location.href = '/shop';
}

// Initialize page when DOM is loaded
let homePage;
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded, initializing home page...');
    homePage = new HomePage();
});