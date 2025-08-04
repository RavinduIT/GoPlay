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
// Your actual data from data.json
// Global variables
// Your actual data from data.json
// Sample grounds data (in real app, this would come from your grounds.json file)

async function loadGroundsData() {
    try {
        const response = await fetch('../../data/grounds.json');
        const data = await response.json();
        allGrounds = data.grounds;
        filteredGrounds = [...allGrounds];
        displayGrounds();
        updateResultsCount();
    } catch (error) {
        console.error('Error loading grounds data:', error);
        displayError('Fuckeeeedddddd.');
    }
}
const groundsData = [
    {
        id: 1,
        name: "City Sports Complex",
        location: "Downtown",
        sports: ["Tennis", "Basketball", "Volleyball"],
        pricePerHour: 50,
        rating: 4.5,
        amenities: ["Parking", "Changing Rooms", "Water Fountain", "Lighting"],
        availableSlots: ["9:00 AM", "11:00 AM", "2:00 PM", "4:00 PM", "6:00 PM"],
        image: "https://via.placeholder.com/400x200?text=City+Sports+Complex"
    },
    {
        id: 2,
        name: "Tennis Center Pro",
        location: "North Side",
        sports: ["Tennis"],
        pricePerHour: 60,
        rating: 4.8,
        amenities: ["Parking", "Pro Shop", "Coaching", "Equipment Rental"],
        availableSlots: ["8:00 AM", "10:00 AM", "1:00 PM", "3:00 PM", "5:00 PM"],
        image: "https://via.placeholder.com/400x200?text=Tennis+Center+Pro"
    },
    {
        id: 3,
        name: "Olympic Arena",
        location: "East District",
        sports: ["Basketball", "Badminton", "Squash"],
        pricePerHour: 75,
        rating: 4.7,
        amenities: ["Parking", "Cafeteria", "Air Conditioning", "Sound System"],
        availableSlots: ["7:00 AM", "12:00 PM", "3:00 PM", "7:00 PM"],
        image: "https://via.placeholder.com/400x200?text=Olympic+Arena"
    },
    {
        id: 4,
        name: "Green Field Stadium",
        location: "South End",
        sports: ["Football", "Cricket", "Rugby"],
        pricePerHour: 100,
        rating: 4.6,
        amenities: ["Parking", "Dressing Rooms", "Medical Room", "Scoreboard"],
        availableSlots: ["6:00 AM", "9:00 AM", "2:00 PM", "5:00 PM"],
        image: "https://via.placeholder.com/400x200?text=Green+Field+Stadium"
    },
    {
        id: 5,
        name: "Aqua Sports Center",
        location: "West Side",
        sports: ["Swimming", "Water Polo", "Diving"],
        pricePerHour: 80,
        rating: 4.4,
        amenities: ["Parking", "Lockers", "Showers", "Pool Equipment"],
        availableSlots: ["6:00 AM", "8:00 AM", "11:00 AM", "4:00 PM", "7:00 PM"],
        image: "https://via.placeholder.com/400x200?text=Aqua+Sports+Center"
    }
];


// Global variables
let allGrounds = [];
let filteredGrounds = [];

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadGroundsData();
    populateFilters();
    setupEventListeners();
});

// Load grounds data (in real app, fetch from grounds.json)
async function loadGroundsData() {
    try {
        // In a real application, you would fetch from your JSON file like this:
        // const response = await fetch('grounds.json');
        // const data = await response.json();
        // allGrounds = data.grounds;
        
        // For now, using the sample data
        allGrounds = groundsData;
        filteredGrounds = [...allGrounds];
        displayGrounds();
        updateResultsCount();
    } catch (error) {
        console.error('Error loading grounds data:', error);
        displayError('Failed to load sports grounds. Please try again later.');
    }
}

// Display grounds in the UI
function displayGrounds() {
    const container = document.getElementById('grounds-container');
    
    if (filteredGrounds.length === 0) {
        container.innerHTML = '<div class="error">No sports grounds found matching your criteria.</div>';
        return;
    }
    
    container.innerHTML = filteredGrounds.map(ground => createGroundCard(ground)).join('');
}

// Create individual ground card HTML
function createGroundCard(ground) {
    const stars = generateStars(ground.rating);
    const sportsHtml = ground.sports.map(sport => 
        `<span class="sport-tag">${sport}</span>`
    ).join('');
    
    const amenitiesHtml = ground.amenities.slice(0, 4).map(amenity => 
        `<span class="amenity-tag">${amenity}</span>`
    ).join('');
    
    const timeSlotsHtml = ground.availableSlots.slice(0, 5).map(slot => 
        `<span class="time-slot">${slot}</span>`
    ).join('');
    
    return `
        <div class="ground-card" data-ground-id="${ground.id}">
            <div class="card-header">
                <div class="ground-info">
                    <h3>${ground.name}</h3>
                    <div class="ground-type">${ground.sports[0]} Ground</div>
                    <div class="location">📍 ${ground.location}</div>
                </div>
                <div class="rating-price">
                    <div class="rating">
                        ${stars}
                        <span>${ground.rating}</span>
                    </div>
                    <div class="price">
                        $${ground.pricePerHour}
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
                    ${ground.amenities.length > 4 ? `<span class="amenity-tag">+${ground.amenities.length - 4} more</span>` : ''}
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
    
    // Get unique locations
    const locations = [...new Set(allGrounds.map(ground => ground.location))];
    locations.forEach(location => {
        const option = document.createElement('option');
        option.value = location;
        option.textContent = location;
        locationSelect.appendChild(option);
    });
    
    // Get unique sports
    const sports = [...new Set(allGrounds.flatMap(ground => ground.sports))];
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
    
    // Real-time search
    searchInput.addEventListener('input', function() {
        applyFilters();
    });
    
    locationSelect.addEventListener('change', function() {
        applyFilters();
    });
    
    sportTypeSelect.addEventListener('change', function() {
        applyFilters();
    });
}

// Apply filters and search
function applyFilters() {
    const searchTerm = document.getElementById('search-grounds').value.toLowerCase();
    const selectedLocation = document.getElementById('location').value;
    const selectedSport = document.getElementById('sport-type').value;
    
    filteredGrounds = allGrounds.filter(ground => {
        const matchesSearch = ground.name.toLowerCase().includes(searchTerm);
        const matchesLocation = !selectedLocation || ground.location === selectedLocation;
        const matchesSport = !selectedSport || ground.sports.includes(selectedSport);
        
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
    const count = filteredGrounds.length;
    countElement.textContent = `${count} sports ground${count !== 1 ? 's' : ''} found`;
}

// Display error message
function displayError(message) {
    const container = document.getElementById('grounds-container');
    container.innerHTML = `<div class="error">${message}</div>`;
}

// View ground details (placeholder function)
function viewGroundDetails(groundId) {
    const ground = allGrounds.find(g => g.id === groundId);
    if (ground) {
        // In a real app, you might navigate to a details page
        alert(`Viewing details for: ${ground.name}\nLocation: ${ground.location}\nPrice: $${ground.pricePerHour}/hour`);
        console.log('Ground details:', ground);
    }
}

// Book ground (placeholder function)
function bookGround(groundId) {
    const ground = allGrounds.find(g => g.id === groundId);
    if (ground) {
        // Check if user is logged in
        const currentUser = localStorage.getItem('currentUser');
        if (!currentUser) {
            alert('Please log in to book a ground.');
            return;
        }
        
        // In a real app, you might navigate to a booking page
        alert(`Booking ${ground.name}...\nPrice: $${ground.pricePerHour}/hour`);
        console.log('Booking ground:', ground);
        
        // You could redirect to a booking form page:
        // window.location.href = `booking.html?groundId=${groundId}`;
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
    // Convert 24-hour format to 12-hour format if needed
    return timeString;
}

// Export functions for external use (if needed)
window.searchGrounds = searchGrounds;
window.viewGroundDetails = viewGroundDetails;
window.bookGround = bookGround;