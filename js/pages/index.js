// Fixed Home Page JavaScript
class HomePage {
    constructor() {
        this.venuesGrid = document.getElementById('venuesGrid');
        this.popularVenues = [];
        this.init();
    }

    async init() {
        await this.loadVenuesData();
        this.renderPopularVenues();
        this.setupAnimations();
        this.setupIntersectionObserver();
    }

    async loadVenuesData() {
        // Sample popular venues data as fallback
        this.popularVenues = [
            {
                id: 1,
                name: 'City Sports Complex',
                type: 'Multi-Sport',
                rating: 4.9,
                price: '$25/hour',
                image: 'assets/images/placeholder.jpg', // Fixed to use placeholder
                availability: 'Available Now'
            },
            {
                id: 2,
                name: 'Tennis Center Pro',
                type: 'Tennis Courts',
                rating: 4.8,
                price: '$30/hour',
                image: 'assets/images/placeholder.jpg', // Fixed to use placeholder
                availability: 'Available Now'
            },
            {
                id: 3,
                name: 'Basketball Arena',
                type: 'Basketball',
                rating: 4.7,
                price: '$20/hour',
                image: 'assets/images/placeholder.jpg', // Fixed to use placeholder
                availability: 'Available Now'
            }
        ];

        // Try to load from data manager if available
        try {
            if (window.dataManager) {
                const venues = await window.dataManager.getVenues();
                if (venues && venues.length > 0) {
                    // Take top 3 highest rated venues
                    this.popularVenues = venues
                        .sort((a, b) => (b.rating || 0) - (a.rating || 0))
                        .slice(0, 3)
                        .map(venue => ({
                            id: venue.id,
                            name: venue.name || 'Unnamed Venue',
                            type: venue.sport || venue.type || 'Sports Venue',
                            rating: venue.rating || 4.0,
                            price: venue.pricePerHour ? `$${venue.pricePerHour}/hour` : '$25/hour',
                            image: this.getVenueImage(venue),
                            availability: venue.availability || 'Available Now'
                        }));
                }
            }
        } catch (error) {
            console.warn('Could not load venues from data manager, using default data:', error);
        }

        // Also try to load from grounds.json as fallback
        try {
            const response = await fetch('data/grounds.json');
            if (response.ok) {
                const data = await response.json();
                if (data && data.length > 0) {
                    // Take top 3 highest rated venues
                    this.popularVenues = data
                        .sort((a, b) => (b.rating || 0) - (a.rating || 0))
                        .slice(0, 3)
                        .map(venue => ({
                            id: venue.id,
                            name: venue.name || 'Unnamed Venue',
                            type: venue.sport || venue.type || 'Sports Venue',
                            rating: venue.rating || 4.0,
                            price: venue.pricePerHour ? `${venue.pricePerHour}/hour` : '$25/hour',
                            image: this.getVenueImage(venue),
                            availability: venue.availability || 'Available Now'
                        }));
                }
            }
        } catch (error) {
            console.warn('Could not load grounds.json, using default venues data:', error);
        }
    }

    // Helper function to get venue image with proper fallback
    getVenueImage(venue) {
        if (venue.images && venue.images.length > 0) {
            return venue.images[0];
        }
        if (venue.image) {
            return venue.image;
        }
        // Return placeholder image that actually exists
        return 'assets/images/placeholder.jpg';
    }

    renderPopularVenues() {
        if (!this.venuesGrid) return;

        this.venuesGrid.innerHTML = '';
        
        this.popularVenues.forEach((venue, index) => {
            const venueCard = this.createVenueCard(venue, index);
            this.venuesGrid.appendChild(venueCard);
        });
    }

    createVenueCard(venue, index) {
        const venueCard = document.createElement('div');
        venueCard.className = 'venue-card';
        venueCard.dataset.index = index;

        venueCard.innerHTML = `
            <div class="venue-image">
                <img src="${venue.image}" alt="${venue.name}" onerror="this.src='assets/images/placeholder.jpg'">
                <div class="venue-overlay"></div>
                <div class="venue-rating">
                    <i class="fas fa-star"></i>
                    ${venue.rating}
                </div>
                <div class="venue-info">
                    <h3 class="venue-name">${venue.name}</h3>
                    <p class="venue-type">${venue.type}</p>
                </div>
            </div>
            <div class="venue-details">
                <div class="venue-pricing">
                    <div class="venue-price">
                        <i class="fas fa-clock"></i>
                        <span>${venue.price}</span>
                    </div>
                    <div class="venue-availability badge">${venue.availability}</div>
                </div>
                <a href="pages/book-ground.html?id=${venue.id}" class="venue-book-btn">
                    <i class="fas fa-map-pin"></i>
                    Book Now
                </a>
            </div>
        `;

        // Add click event for venue details
        const venueImage = venueCard.querySelector('.venue-image');
        venueImage.addEventListener('click', () => {
            this.openVenueDetails(venue.id);
        });

        return venueCard;
    }

    setupAnimations() {
        // Add staggered animation to feature cards
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.2}s`;
            card.classList.add('animate-fade-in');
        });

        // Add animation to venue cards
        const venueCards = document.querySelectorAll('.venue-card');
        venueCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.3}s`;
            card.classList.add('animate-fade-in');
        });

        // Add hover effects to hero buttons
        const heroButtons = document.querySelectorAll('.hero-btn');
        heroButtons.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'translateY(-2px)';
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translateY(0)';
            });
        });
    }

    setupIntersectionObserver() {
        // Create intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                    
                    // Add staggered animation to children
                    const children = entry.target.querySelectorAll('.feature-card, .venue-card, .news-item');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.style.opacity = '1';
                            child.style.transform = 'translateY(0)';
                        }, index * 100);
                    });
                }
            });
        }, observerOptions);

        // Observe sections
        const sections = document.querySelectorAll('.features-section, .news-section, .venues-section');
        sections.forEach(section => {
            observer.observe(section);
        });

        // Setup parallax effect for hero section
        this.setupParallaxEffect();
    }

    setupParallaxEffect() {
        const heroBackground = document.querySelector('.hero-background');
        if (!heroBackground) return;

        let ticking = false;

        const updateParallax = () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            if (scrolled < window.innerHeight) {
                heroBackground.style.transform = `translateY(${rate}px)`;
            }
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });
    }

    openVenueDetails(venueId) {
        window.location.href = `pages/ground-details.html?id=${venueId}`;
    }

    // Smooth scroll to sections
    scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Public method to handle CTA button clicks
    handleCTAClick(action) {
        switch (action) {
            case 'book-ground':
                window.location.href = 'pages/book-ground.html';
                break;
            case 'book-coach':
                window.location.href = 'pages/book-coach.html';
                break;
            case 'shop':
                window.location.href = 'pages/shop.html';
                break;
            case 'explore-sports':
                this.scrollToSection('features');
                break;
            default:
                console.log('Unknown action:', action);
        }
    }

    // Method to refresh venues data
    async refreshVenues() {
        await this.loadVenuesData();
        this.renderPopularVenues();
    }

    // Method to handle search functionality
    handleSearch(query) {
        if (!query || query.trim().length < 2) return;
        
        const searchUrl = `pages/search.html?q=${encodeURIComponent(query.trim())}`;
        window.location.href = searchUrl;
    }
}

// Utility functions for the home page
class HomePageUtils {
    static formatPrice(price) {
        return `${price}/hour`;
    }

    static formatRating(rating) {
        return rating.toFixed(1);
    }

    static truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + '...';
    }

    static animateCounter(element, target, duration = 2000) {
        if (!element) return;
        
        let start = 0;
        const increment = target / (duration / 16);
        
        const timer = setInterval(() => {
            start += increment;
            element.textContent = Math.floor(start);
            
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            }
        }, 16);
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Utility to handle image loading errors
    static handleImageError(img, fallbackSrc = 'assets/images/placeholder.jpg') {
        img.onerror = function() {
            this.onerror = null; // Prevent infinite loop
            this.src = fallbackSrc;
        };
    }

    // Utility to create loading skeleton
    static createLoadingSkeleton(container) {
        if (!container) return;
        
        container.innerHTML = `
            <div class="loading-skeleton">
                <div class="skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-text"></div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Initialize home page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Wait for data manager to be ready if it exists
    const initHomePage = () => {
        window.homePage = new HomePage();
        
        // Setup global click handlers
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action]')) {
                const action = e.target.dataset.action;
                window.homePage.handleCTAClick(action);
            }
        });
        
        // Setup scroll-based animations with debouncing
        window.addEventListener('scroll', HomePageUtils.debounce(() => {
            // Add any additional scroll-based animations here
        }, 16));

        // Setup search functionality if search input exists
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    window.homePage.handleSearch(searchInput.value);
                }
            });
        }

        console.log('Home page initialized successfully!');
    };

    // Check if data manager is available, if not initialize immediately
    if (window.dataManager) {
        // Wait a bit for data manager to finish initialization
        setTimeout(initHomePage, 100);
    } else {
        initHomePage();
    }
});

// Handle window resize with debouncing
window.addEventListener('resize', HomePageUtils.debounce(() => {
    if (window.homePage) {
        // Handle responsive adjustments
        const venuesGrid = document.getElementById('venuesGrid');
        if (venuesGrid) {
            // Trigger re-layout if needed
            window.homePage.renderPopularVenues();
        }
    }
}, 250));

// Handle page visibility changes
document.addEventListener('visibilitychange', () => {
    if (!document.hidden && window.homePage) {
        // Refresh data when page becomes visible
        window.homePage.refreshVenues();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { HomePage, HomePageUtils };
}