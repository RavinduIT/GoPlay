// Load components
function loadComponents() {
    // Load navbar
    fetch('/app/views/components/navbar.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('navbar-container').innerHTML = data;
            if (typeof Navbar !== 'undefined') {
                new Navbar();
            }
        });

    // Load footer
    fetch('/app/views/components/footer.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });
}
// Book Ground JavaScript - Connects to database API
class GroundBookingApp {
    constructor() {
        this.grounds = [];
        this.filteredGrounds = [];
        this.currentFilters = {
            search: '',
            location: '',
            sport: '',
            priceRange: ''
        };
        
        this.init();
    }

    async init() {
        // Don't load data from API since PHP already provides it
        // await this.loadGroundsData();
        this.setupEventListeners();
        // Don't render grounds since PHP already rendered them
        // this.renderGrounds();
    }

    // Load data from database API
    async loadGroundsData() {
        try {
            const response = await fetch('/api/grounds');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success || !data.data) {
                throw new Error(data.message || 'Failed to load grounds');
            }

            // Convert API data to the format expected by the UI
            this.grounds = data.data.map(facility => ({
                id: facility.id,
                name: facility.name,
                location: facility.address && facility.city ? `${facility.address}, ${facility.city}` : facility.city || 'Location TBD',
                sport: facility.category_name || 'General',
                price: facility.hourly_rate || 0,
                rating: facility.rating || 4.5,
                reviews: facility.total_reviews || 0,
                description: facility.description || 'Sports facility',
                features: facility.amenities || ['Available for booking'],
                latitude: facility.latitude,
                longitude: facility.longitude
            }));

            this.filteredGrounds = [...this.grounds];

            console.log('Loaded grounds data:', this.grounds.length, 'facilities');
        } catch (error) {
            console.error('Error loading grounds data:', error);
            this.showErrorState();
        }
    }

    // Setup event listeners
    setupEventListeners() {
        // Search input
        const searchInput = document.getElementById('search-grounds');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.currentFilters.search = e.target.value;
                this.filterGrounds();
            });
        }

        // Filter selects
        const locationSelect = document.getElementById('location');
        const sportSelect = document.getElementById('sport-type');
        const priceSelect = document.getElementById('price-range');

        if (locationSelect) {
            locationSelect.addEventListener('change', (e) => {
                this.currentFilters.location = e.target.value;
                this.filterGrounds();
            });
        }

        if (sportSelect) {
            sportSelect.addEventListener('change', (e) => {
                this.currentFilters.sport = e.target.value;
                this.filterGrounds();
            });
        }

        if (priceSelect) {
            priceSelect.addEventListener('change', (e) => {
                this.currentFilters.priceRange = e.target.value;
                this.filterGrounds();
            });
        }

        // Sort select
        const sortSelect = document.getElementById('sort-by');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.sortGrounds(e.target.value);
            });
        }
    }

    // Filter grounds based on current filters
    filterGrounds() {
        this.filteredGrounds = this.grounds.filter(ground => {
            // Search filter
            if (this.currentFilters.search) {
                const searchTerm = this.currentFilters.search.toLowerCase();
                const matchesSearch =
                    ground.name.toLowerCase().includes(searchTerm) ||
                    ground.location.toLowerCase().includes(searchTerm) ||
                    ground.sport.toLowerCase().includes(searchTerm) ||
                    ground.description.toLowerCase().includes(searchTerm);

                if (!matchesSearch) return false;
            }

            // Location filter
            if (this.currentFilters.location) {
                const locationValue = this.currentFilters.location.toLowerCase();
                if (!ground.location.toLowerCase().includes(locationValue)) {
                    return false;
                }
            }

            // Sport filter - compare the sport name instead of ID
            if (this.currentFilters.sport) {
                // Get the selected option's text (sport name) not value (ID)
                const sportSelect = document.getElementById('sport-type');
                if (sportSelect) {
                    const selectedOption = sportSelect.options[sportSelect.selectedIndex];
                    const sportName = selectedOption.text.toLowerCase();
                    if (!ground.sport.toLowerCase().includes(sportName)) {
                        return false;
                    }
                }
            }

            // Price filter - convert from LKR ranges
            if (this.currentFilters.priceRange) {
                const price = ground.price;
                switch (this.currentFilters.priceRange) {
                    case '0-1000':
                        if (price > 1000) return false;
                        break;
                    case '1000-2000':
                        if (price < 1000 || price > 2000) return false;
                        break;
                    case '2000+':
                        if (price < 2000) return false;
                        break;
                }
            }

            return true;
        });

        this.updateResultsCount();
        this.renderGrounds();
    }

    // Sort grounds
    sortGrounds(sortBy) {
        switch (sortBy) {
            case 'rating':
                this.filteredGrounds.sort((a, b) => b.rating - a.rating);
                break;
            case 'price-low':
                this.filteredGrounds.sort((a, b) => a.price - b.price);
                break;
            case 'price-high':
                this.filteredGrounds.sort((a, b) => b.price - a.price);
                break;
            default:
                this.filteredGrounds.sort((a, b) => b.rating - a.rating);
        }
        this.renderGrounds();
    }

    // Update results count
    updateResultsCount() {
        const resultsCountElement = document.getElementById('results-count');
        if (resultsCountElement) {
            const count = this.filteredGrounds.length;
            resultsCountElement.textContent = `${count} Premium Facilit${count === 1 ? 'y' : 'ies'} Available`;
        }
    }

    // Render grounds to the page
    renderGrounds() {
        const container = document.getElementById('facilities-grid');
        if (!container) return;

        if (this.filteredGrounds.length === 0) {
            container.innerHTML = this.getEmptyStateHTML();
            return;
        }

        const groundsHTML = this.filteredGrounds.map(ground => this.createGroundCard(ground)).join('');
        container.innerHTML = groundsHTML;

        // Add click events to cards
        this.attachCardEvents();
    }

    // Create HTML for a single ground card
    createGroundCard(ground) {
        const stars = this.generateStarsHTML(ground.rating);
        const features = ground.features.slice(0, 3).map(feature =>
            `<div class="feature-item"><i class="fas fa-check"></i>${feature}</div>`
        ).join('');

        const moreFeatures = ground.features.length > 3 ?
            `<div class="more-features">+${ground.features.length - 3} more features</div>` : '';

        // Get image URL or fallback
        const imageUrl = ground.imageUrl || '/public/assets/images/ground.jpeg';
        const sportIcon = this.getSportIconClass(ground.sport);

        return `
            <div class="facility-card" data-ground-id="${ground.id}">
                <div class="facility-image">
                    <img src="${imageUrl}" alt="${ground.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="facility-icon" style="display: none;">
                        <i class="${sportIcon}"></i>
                    </div>
                    <div class="facility-badge">
                        <i class="fas fa-star"></i>
                        ${ground.rating}
                    </div>
                </div>
                <div class="facility-content">
                    <div class="facility-header">
                        <h3 class="facility-name">${ground.name}</h3>
                        <div class="facility-price">
                            <div class="price-amount">Rs. ${ground.price.toLocaleString()}</div>
                            <div class="price-period">per hour</div>
                        </div>
                    </div>
                    
                    <div class="facility-location">
                        <i class="fas fa-map-marker-alt"></i>
                        ${ground.location}
                    </div>
                    
                    <div class="facility-sports">
                        <span class="sport-tag">${ground.sport.charAt(0).toUpperCase() + ground.sport.slice(1)}</span>
                    </div>
                    
                    <div class="facility-features">
                        ${features}
                        ${moreFeatures}
                    </div>
                    
                    <div class="facility-footer">
                        <div class="facility-rating">
                            <div class="stars">${stars}</div>
                            <span class="rating-text">(${ground.reviews} reviews)</span>
                        </div>
                        
                        <div class="facility-actions">
                            <button class="btn-outline" onclick="viewDetails(${ground.id})">
                                View Details
                            </button>
                            <button class="btn-primary" onclick="bookNow(${ground.id})">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Generate stars HTML based on rating
    generateStarsHTML(rating) {
        let stars = '';
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;
        
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star filled"></i>';
        }
        
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt filled"></i>';
        }
        
        const remainingStars = 5 - Math.ceil(rating);
        for (let i = 0; i < remainingStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }
        
        return stars;
    }

    // Get sport icon (emoji - kept for backward compatibility)
    getSportIcon(sport) {
        const icons = {
            'football': '⚽',
            'tennis': '🎾',
            'basketball': '🏀',
            'cricket': '🏏',
            'badminton': '🏸',
            'swimming': '🏊'
        };
        return icons[sport] || '🏟️';
    }

    // Get sport icon class (Font Awesome icons)
    getSportIconClass(sport) {
        const sportLower = sport.toLowerCase();
        const icons = {
            'football': 'fas fa-futbol',
            'soccer': 'fas fa-futbol',
            'tennis': 'fas fa-table-tennis',
            'basketball': 'fas fa-basketball-ball',
            'cricket': 'fas fa-baseball-ball',
            'badminton': 'fas fa-shuttle-van',
            'swimming': 'fas fa-swimmer',
            'volleyball': 'fas fa-volleyball-ball'
        };
        return icons[sportLower] || 'fas fa-running';
    }

    // Get empty state HTML
    getEmptyStateHTML() {
        return `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No facilities found</h3>
                <p>Try adjusting your search criteria or filters to find more facilities.</p>
                <button class="btn-primary" onclick="clearAllFilters()">Clear All Filters</button>
            </div>
        `;
    }

    // Show error state
    showErrorState() {
        const container = document.getElementById('facilities-grid');
        if (container) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Error Loading Facilities</h3>
                    <p>We're having trouble loading the facility data. Please try again later.</p>
                    <button class="btn-primary" onclick="location.reload()">Retry</button>
                </div>
            `;
        }
    }

    // Attach events to cards
    attachCardEvents() {
        const cards = document.querySelectorAll('.facility-card');
        cards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    const groundId = card.dataset.groundId;
                    viewDetails(groundId);
                }
            });
        });
    }

    // Clear all filters
    clearAllFilters() {
        this.currentFilters = {
            search: '',
            location: '',
            sport: '',
            priceRange: ''
        };

        // Reset form elements
        const searchInput = document.getElementById('search-grounds');
        const locationSelect = document.getElementById('location');
        const sportSelect = document.getElementById('sport-type');
        const priceSelect = document.getElementById('price-range');

        if (searchInput) searchInput.value = '';
        if (locationSelect) locationSelect.value = '';
        if (sportSelect) sportSelect.value = '';
        if (priceSelect) priceSelect.value = '';

        this.filteredGrounds = [...this.grounds];
        this.updateResultsCount();
        this.renderGrounds();
    }

    // Get ground by ID
    getGroundById(id) {
        return this.grounds.find(ground => ground.id == id);
    }
}
// Global functions for button events
function searchFacilities() {
    // This is handled by the input event listener
    console.log('Search triggered');
}

function clearAllFilters() {
    if (window.groundsApp) {
        window.groundsApp.clearAllFilters();
    }
}

function viewDetails(groundId) {
    console.log('View details clicked for ground ID:', groundId);
    const url = `/ground-details?id=${groundId}`;
    console.log('Redirecting to:', url);
    // Redirect to ground details page
    window.location.href = url;
}

function bookNow(groundId) {
    console.log('Book now for ground:', groundId);

    // Check if user is logged in before proceeding
    fetch('/auth/check')
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                // User is not logged in, redirect to login
                window.location.href = `/login?redirect=${encodeURIComponent('/ground-details?id=' + groundId)}`;
                return;
            }

            // User is logged in, redirect to ground details
            window.location.href = `/ground-details?id=${groundId}`;
        })
        .catch(error => {
            console.error('Error checking login status:', error);
            // On error, redirect to login to be safe
            window.location.href = `/login?redirect=${encodeURIComponent('/ground-details?id=' + groundId)}`;
        });
}

function viewOnMap(groundId) {
    // Map view disabled
}

function toggleMapView() {
    // Map toggle disabled
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                console.log('User location:', position.coords.latitude, position.coords.longitude);
                // Implement location-based filtering
            },
            (error) => {
                console.error('Error getting location:', error);
                alert('Unable to get your location. Please enable location services.');
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

function sortFacilities() {
    // This is handled by the change event listener
    console.log('Sort triggered');
}

function fitMapToMarkers() {
    // Map functionality disabled
}

// Load facility data from the DOM (since PHP already rendered them)
function loadFacilitiesFromDOM() {
    if (!window.groundsApp) return;

    const facilityCards = document.querySelectorAll('.facility-card');
    const facilities = [];

    facilityCards.forEach(card => {
        const id = card.dataset.groundId;
        const name = card.querySelector('.facility-name')?.textContent || '';
        const location = card.querySelector('.facility-location')?.textContent.trim() || '';
        const sport = card.querySelector('.sport-tag')?.textContent || '';
        const priceText = card.querySelector('.price-amount')?.textContent || '0';
        const price = parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
        const ratingBadge = card.querySelector('.facility-badge')?.textContent.trim() || '0';
        const rating = parseFloat(ratingBadge.replace('★', '').trim());
        const reviewsText = card.querySelector('.rating-text')?.textContent || '(0 reviews)';
        const reviews = parseInt(reviewsText.match(/\d+/)?.[0] || '0');

        // Get image URL
        const imgElement = card.querySelector('.facility-image img');
        const imageUrl = imgElement?.src || '';

        // Get features
        const featureElements = card.querySelectorAll('.feature-item');
        const features = Array.from(featureElements).map(el => el.textContent.trim());

        facilities.push({
            id: id,
            name: name,
            location: location,
            sport: sport.toLowerCase(),
            price: price,
            rating: rating,
            reviews: reviews,
            description: sport,
            features: features,
            imageUrl: imageUrl,
            latitude: null,
            longitude: null
        });
    });

    console.log('Loaded facilities from DOM:', facilities.length);
    window.groundsApp.grounds = facilities;
    window.groundsApp.filteredGrounds = [...facilities];
}

// Initialize the app when the page loads
document.addEventListener('DOMContentLoaded', () => {
    // Load page components first
    loadComponents();

    // Initialize the booking app to enable search and filtering
    window.groundsApp = new GroundBookingApp();

    // Load facility data from the DOM since PHP already rendered them
    loadFacilitiesFromDOM();
});

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GroundBookingApp;
}