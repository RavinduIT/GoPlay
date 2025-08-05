/* Ground booking functionality
function loadBookGroundPage() {
    // Load ground booking page
}

function initializeGroundSelection() {
    // Initialize ground selection
}

function handleDateTimeSelection() {
    // Handle date/time selection
}

function handleGroundBooking() {
    // Handle booking submission
}

// Export ground booking functions
window.bookGroundPage = {
    load: loadBookGroundPage,
    init: initializeBookGroundPage
};

function initializeBookGroundPage() {
    // Initialize ground booking page
}*/


// Global variables
let allGrounds = [];
let filteredGrounds = [];

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadGroundsData();
    populateFilters();
    setupEventListeners();
});

// Load grounds data from JSON file
async function loadGroundsData() {
    try {
        // UPDATE THIS PATH based on your file structure
        const response = await fetch('../../data/grounds.json'); // Relative path to your JSON file
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        allGrounds = data.grounds;
        filteredGrounds = [...allGrounds]; // Initialize filtered grounds
        
        displayGrounds();
        updateResultsCount();
        
        console.log('Loaded grounds data:', allGrounds);
    } catch (error) {
        console.error('Error loading grounds data:', error);
        displayError('Failed to load sports grounds. Please try again later.');
        
        // Fallback: load sample data for testing
        loadSampleData();
    }
}


function loadSampleData() {
    console.log('Loading sample data as fallback...');
    allGrounds = [
        {
            id: 1,
            name: "City Sports Complex",
            location: "Downtown",
            sports: ["Tennis", "Basketball"],
            rating: 4.5,
            pricePerHour: 25,
            amenities: ["Parking", "Restrooms", "Lighting", "Equipment Rental"],
            availableSlots: ["9:00 AM", "11:00 AM", "2:00 PM", "4:00 PM", "6:00 PM"]
        },
        {
            id: 2,
            name: "Tennis Center Pro",
            location: "Uptown",
            sports: ["Tennis"],
            rating: 4.8,
            pricePerHour: 30,
            amenities: ["Pro Shop", "Coaching", "Parking", "Restrooms", "Air Conditioning"],
            availableSlots: ["8:00 AM", "10:00 AM", "1:00 PM", "3:00 PM", "5:00 PM"]
        }
    ];
    
    filteredGrounds = [...allGrounds];
    displayGrounds();
    updateResultsCount();
}

// Display grounds in the UI
function displayGrounds() {
    const container = document.getElementById('grounds-container');
    
    if (!container) {
        console.error('Grounds container not found');
        return;
    }
    
    if (filteredGrounds.length === 0) {
        container.innerHTML = '<div class="error">No sports grounds found matching your criteria.</div>';
        return;
    }
    
    container.innerHTML = filteredGrounds.map(ground => createGroundCard(ground)).join('');
}

// Create individual ground card HTML
function createGroundCard(ground) {
    const stars = generateStars(ground.rating);
    const sportsHtml = ground.sports?.map(sport => 
        `<span class="sport-tag">${sport}</span>`
    ).join('') || '';
    
    const amenitiesHtml = ground.amenities?.slice(0, 4).map(amenity => 
        `<span class="amenity-tag">${amenity}</span>`
    ).join('') || '';
    
    const timeSlotsHtml = ground.availableSlots?.slice(0, 5).map(slot => 
        `<span class="time-slot">${slot}</span>`
    ).join('') || '';
    
    return `
        <div class="ground-card" data-ground-id="${ground.id}">
            <div class="card-header">
                <div class="ground-info">
                    <h3>${ground.name || 'Unnamed Ground'}</h3>
                    <div class="ground-type">${ground.sports?.[0] || 'Sports'} Ground</div>
                    <div class="location">📍 ${ground.location || 'Location not specified'}</div>
                </div>
                <div class="rating-price">
                    <div class="rating">
                        ${stars}
                        <span>${ground.rating || 'N/A'}</span>
                    </div>
                    <div class="price">
                        Rs ${ground.pricePerHour || 0}
                        <span class="price-unit">/hour</span>
                    </div>
                </div>
            </div>
            
            <div class="sports-available">
                <h4>Available Sports</h4>
                <div class="sports-tags">
                    ${sportsHtml}
                </div>
            </div>
            
            <div class="amenities">
                <h4>Amenities</h4>
                <div class="amenities-tags">
                    ${amenitiesHtml}
                    ${ground.amenities && ground.amenities.length > 4 ? `<span class="amenity-tag">+${ground.amenities.length - 4} more</span>` : ''}
                </div>
            </div>
            
            <div class="availability">
                <h4>Available Today</h4>
                <div class="time-slots">
                    ${timeSlotsHtml}
                </div>
            </div>
            
            <div class="card-actions">
                <button class="view-details-btn" onclick="viewGroundDetails(${ground.id})">
                    View Details
                </button>
                <button class="book-now-btn" onclick="bookGround(${ground.id})">
                    Book Now
                </button>
            </div>
        </div>
    `;
}

// Generate star rating HTML
function generateStars(rating) {
    if (!rating) return '<span class="no-rating">No rating</span>';
    
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let starsHtml = '';
    
    for (let i = 0; i < fullStars; i++) {
        starsHtml += '<span class="star">★</span>';
    }
    
    if (hasHalfStar) {
        starsHtml += '<span class="star">☆</span>';
    }
    
    return starsHtml;
}

// Populate filter dropdowns
function populateFilters() {
    const locationSelect = document.getElementById('location');
    const sportTypeSelect = document.getElementById('sport-type');
    
    if (!locationSelect || !sportTypeSelect) {
        console.error('Filter elements not found');
        return;
    }
    
    // Get unique locations
    const locations = [...new Set(allGrounds.map(ground => ground.location).filter(Boolean))];
    locations.forEach(location => {
        const option = document.createElement('option');
        option.value = location;
        option.textContent = location;
        locationSelect.appendChild(option);
    });
    
    // Get unique sports
    const sports = [...new Set(allGrounds.flatMap(ground => ground.sports || []))];
    sports.forEach(sport => {
        const option = document.createElement('option');
        option.value = sport;
        option.textContent = sport;
        sportTypeSelect.appendChild(option);
    });
}

// Setup event listeners
function setupEventListeners() {
    const searchInput = document.getElementById('search-grounds');
    const locationSelect = document.getElementById('location');
    const sportTypeSelect = document.getElementById('sport-type');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applyFilters();
        });
    }
    
    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    if (sportTypeSelect) {
        sportTypeSelect.addEventListener('change', function() {
            applyFilters();
        });
    }
}

// Apply filters and search
function applyFilters() {
    const searchInput = document.getElementById('search-grounds');
    const locationSelect = document.getElementById('location');
    const sportTypeSelect = document.getElementById('sport-type');
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const selectedLocation = locationSelect ? locationSelect.value : '';
    const selectedSport = sportTypeSelect ? sportTypeSelect.value : '';
    
    filteredGrounds = allGrounds.filter(ground => {
        const matchesSearch = !searchTerm || (ground.name && ground.name.toLowerCase().includes(searchTerm));
        const matchesLocation = !selectedLocation || ground.location === selectedLocation;
        const matchesSport = !selectedSport || (ground.sports && ground.sports.includes(selectedSport));
        
        return matchesSearch && matchesLocation && matchesSport;
    });
    
    displayGrounds();
    updateResultsCount();
}

// Search function (called by search button)
function searchGrounds() {
    applyFilters();
}

// Update results count
function updateResultsCount() {
    const countElement = document.getElementById('results-count');
    if (countElement) {
        const count = filteredGrounds.length;
        countElement.textContent = `${count} sports ground${count !== 1 ? 's' : ''} found`;
    }
}

// Display error message
function displayError(message) {
    const container = document.getElementById('grounds-container');
    if (container) {
        container.innerHTML = `<div class="error">${message}</div>`;
    }
}

function viewGroundDetails(groundId) {
    const ground = allGrounds.find(g => g.id === groundId);
    if (ground) {
        // Store ground data in localStorage for the details page to access
        localStorage.setItem('selectedGround', JSON.stringify(ground));
        
        // Navigate to details page with ground ID
        window.location.href = `../../pages/ground-details.html?id=${groundId}`;
    }
}

// Book ground
function bookGround(groundId) {
    const ground = allGrounds.find(g => g.id === groundId);
    if (ground) {
        // Check if user is logged in
        const currentUser = localStorage.getItem('currentUser');
        if (!currentUser) {
            //alert('Please log in to book a ground.');
            window.location.href = `../../pages/login.html`;
            return;
        }
        
        // Navigate to booking page
        window.location.href = `booking.html?groundId=${groundId}`;
    }
}

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Utility function to format time
function formatTime(timeString) {
    return timeString;
}

// Export functions for external use
window.searchGrounds = searchGrounds;
window.viewGroundDetails = viewGroundDetails;
window.bookGround = bookGround;