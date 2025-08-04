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
let sportsData = null;
let currentGrounds = [];
let allLocations = new Set();
let allSports = new Set();

// Load data from JSON file
async function loadSportsData() {
    try {
        const data = await window.fs.readFile('data.json', { encoding: 'utf8' });
        sportsData = JSON.parse(data);
        
        // Extract unique locations and sports for filters
        sportsData.popularVenues.forEach(venue => {
            // Extract location from description or create from venue name
            const location = extractLocation(venue);
            if (location) allLocations.add(location);
            
            // Add all sports from this venue
            venue.sports.forEach(sport => allSports.add(sport));
        });

        // Populate filter dropdowns
        populateFilters();
        
        // Initialize with all venues
        currentGrounds = sportsData.popularVenues;
        renderGrounds(currentGrounds);
        
    } catch (error) {
        console.error('Error loading data:', error);
        document.getElementById('grounds-container').innerHTML = 
            '<div class="error">Error loading data. Please make sure data.json file is available.</div>';
    }
}

// Extract location information from venue data
function extractLocation(venue) {
    // Try to extract location from description or create meaningful location names
    if (venue.name.includes('City')) return 'Downtown Area';
    if (venue.name.includes('Tennis')) return 'Sports District';  
    if (venue.name.includes('Basketball')) return 'North Side';
    if (venue.name.includes('Arena')) return 'Arena District';
    return 'Central Area'; // Default location
}

// Generate sample time slots for venues
function generateTimeSlots(venueId) {
    const timeSlots = [
        ['08:00', '09:00', '10:00', '14:00', '15:00', '16:00'],
        ['09:00', '10:00', '11:00', '15:00', '16:00', '17:00'],
        ['08:00', '10:00', '14:00', '18:00', '19:00'],
        ['08:00', '09:00', '15:00', '16:00', '17:00', '18:00']
    ];
    return timeSlots[venueId % timeSlots.length] || timeSlots[0];
}

// Populate filter dropdowns with data from JSON
function populateFilters() {
    const locationSelect = document.getElementById('location');
    const sportSelect = document.getElementById('sport-type');
    
    // Clear existing options (except "All" options)
    locationSelect.innerHTML = '<option value="">All locations</option>';
    sportSelect.innerHTML = '<option value="">All sports</option>';
    
    // Add location options
    Array.from(allLocations).sort().forEach(location => {
        const option = document.createElement('option');
        option.value = location.toLowerCase().replace(/\s+/g, '-');
        option.textContent = location;
        locationSelect.appendChild(option);
    });
    
    // Add sport options
    Array.from(allSports).sort().forEach(sport => {
        const option = document.createElement('option');
        option.value = sport.toLowerCase();
        option.textContent = sport;
        sportSelect.appendChild(option);
    });
}

// Render grounds to the page
function renderGrounds(grounds) {
    const container = document.getElementById('grounds-container');
    const resultsCount = document.getElementById('results-count');
    
    resultsCount.textContent = `Available Grounds (${grounds.length})`;
    
    if (grounds.length === 0) {
        container.innerHTML = '<div class="error">No sports grounds found matching your criteria.</div>';
        return;
    }
    
    container.innerHTML = grounds.map(ground => {
        const location = extractLocation(ground);
        const timeSlots = generateTimeSlots(ground.id);
        
        return `
            <div class="ground-card">
                <div class="card-header">
                    <div class="ground-info">
                        <h3>${ground.name}</h3>
                        <div class="ground-type">${ground.type}</div>
                        <div class="location">📍 ${location}</div>
                    </div>
                    <div class="rating-price">
                        <div class="rating">
                            <span class="star">⭐</span>
                            <span>${ground.rating}</span>
                        </div>
                        <div class="price">
                            ${ground.price.split('/')[0]}
                            <span class="price-unit">/${ground.price.split('/')[1]}</span>
                        </div>
                    </div>
                </div>

                <div class="sports-available">
                    <h4>Sports Available:</h4>
                    <div class="sports-tags">
                        ${ground.sports.map(sport => `<span class="sport-tag">${sport}</span>`).join('')}
                    </div>
                </div>

                <div class="amenities">
                    <h4>Amenities:</h4>
                    <div class="amenities-tags">
                        ${ground.amenities.map(amenity => `<span class="amenity-tag">${amenity}</span>`).join('')}
                    </div>
                </div>

                <div class="availability">
                    <h4>Available Times Today:</h4>
                    <div class="time-slots">
                        ${timeSlots.map(time => `<span class="time-slot">${time}</span>`).join('')}
                    </div>
                </div>

                <div class="card-actions">
                    <button class="view-details-btn" onclick="viewDetails(${ground.id})">
                        📋 View Details
                    </button>
                    <button class="book-now-btn" onclick="bookNow(${ground.id})">
                        Book Now
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Search and filter grounds
function searchGrounds() {
    if (!sportsData) return;
    
    const searchTerm = document.getElementById('search-grounds').value.toLowerCase();
    const locationFilter = document.getElementById('location').value;
    const sportFilter = document.getElementById('sport-type').value;

    let filteredGrounds = sportsData.popularVenues.filter(ground => {
        // Search by name
        const matchesSearch = !searchTerm || ground.name.toLowerCase().includes(searchTerm);
        
        // Filter by location
        const groundLocation = extractLocation(ground).toLowerCase().replace(/\s+/g, '-');
        const matchesLocation = !locationFilter || groundLocation === locationFilter;
        
        // Filter by sport
        const matchesSport = !sportFilter || ground.sports.some(sport => sport.toLowerCase() === sportFilter);

        return matchesSearch && matchesLocation && matchesSport;
    });

    currentGrounds = filteredGrounds;
    renderGrounds(filteredGrounds);
}

// View ground details
function viewDetails(groundId) {
    const ground = sportsData.popularVenues.find(g => g.id === groundId);
    if (!ground) return;
    
    const location = extractLocation(ground);
    const bookings = getGroundBookings(groundId);
    
    alert(`${ground.name}
    
Type: ${ground.type}
Location: ${location}
Rating: ${ground.rating}/5 stars
Price: ${ground.price}

Description: ${ground.description}

Sports Available: ${ground.sports.join(', ')}
Amenities: ${ground.amenities.join(', ')}
Availability: ${ground.availability}

${bookings.length > 0 ? `\nCurrent Bookings: ${bookings.length}` : ''}`);
}

// Book ground
function bookNow(groundId) {
    const ground = sportsData.popularVenues.find(g => g.id === groundId);
    if (!ground) return;
    
    const timeSlots = generateTimeSlots(groundId);
    
    alert(`Booking ${ground.name}

Available Time Slots:
${timeSlots.join(' | ')}

Price: ${ground.price}
Sports: ${ground.sports.join(', ')}

This would redirect to the booking form where you can:
• Select date and time
• Choose duration  
• Select sport type
• Complete payment

Proceed with booking?`);
}

// Get bookings for a specific ground
function getGroundBookings(groundId) {
    if (!sportsData.groundBookings) return [];
    return sportsData.groundBookings.filter(booking => booking.venueId === groundId);
}

// Initialize event listeners
function initializeEventListeners() {
    document.getElementById('search-grounds').addEventListener('input', searchGrounds);
    document.getElementById('location').addEventListener('change', searchGrounds);
    document.getElementById('sport-type').addEventListener('change', searchGrounds);
}

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    loadSportsData();
});

// Make functions available globally for onclick handlers
window.searchGrounds = searchGrounds;
window.viewDetails = viewDetails;
window.bookNow = bookNow;