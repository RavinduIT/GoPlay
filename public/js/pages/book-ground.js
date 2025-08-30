document.addEventListener('DOMContentLoaded', function() {
            loadComponents();
            
        });
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
/* Book Ground JavaScript - Connects to JSON data
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
        await this.loadGroundsData();
        this.setupEventListeners();
        this.renderGrounds();
    }

    // Load JSON data
    async loadGroundsData() {
        try {
            const response = await fetch('/public/data/grounds.json');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            this.grounds = data.grounds;
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
                const locationValue = this.currentFilters.location.replace('-', ' ');
                if (!ground.location.toLowerCase().includes(locationValue)) {
                    return false;
                }
            }

            // Sport filter
            if (this.currentFilters.sport) {
                if (ground.sport !== this.currentFilters.sport) {
                    return false;
                }
            }

            // Price filter
            if (this.currentFilters.priceRange) {
                const price = ground.price;
                switch (this.currentFilters.priceRange) {
                    case '0-30':
                        if (price > 30) return false;
                        break;
                    case '30-50':
                        if (price < 30 || price > 50) return false;
                        break;
                    case '50+':
                        if (price < 50) return false;
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

        return `
            <div class="facility-card" data-ground-id="${ground.id}">
                <div class="facility-image">
                    <div class="facility-icon">
                        ${this.getSportIcon(ground.sport)}
                    </div>
                    <div class="facility-badge">
                        <i class="fas fa-star"></i>
                        ${ground.rating}
                    </div>
                    <div class="facility-overlay">
                        <button class="view-on-map-btn" onclick="viewOnMap(${ground.id})">
                            <i class="fas fa-map-marker-alt"></i>
                            View on Map
                        </button>
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

    // Get sport icon
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
*/
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
    // Implement booking functionality
    // Could redirect to booking page or show booking modal
    window.location.href = `/book/${groundId}`;
}

function viewOnMap(groundId) {
    console.log('View on map for ground:', groundId);
    // Implement map view functionality
    toggleMapView();
    // Focus on specific ground marker
}

function toggleMapView() {
    const mapSidebar = document.getElementById('map-sidebar');
    const mapToggleText = document.getElementById('map-toggle-text');
    
    if (mapSidebar) {
        mapSidebar.classList.toggle('hidden');
        if (mapToggleText) {
            mapToggleText.textContent = mapSidebar.classList.contains('hidden') ? 'Show Map' : 'Hide Map';
        }
    }
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
    console.log('Fit map to markers');
    // Implement map fitting functionality
}

// Initialize the app when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.groundsApp = new GroundBookingApp();
});

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GroundBookingApp;
}